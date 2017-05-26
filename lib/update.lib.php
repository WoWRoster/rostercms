<?php
/**
 * WoWRoster.net WoWRoster
 *
 * LUA updating library
 *
 *
 * @copyright  2002-2011 WoWRoster.net
 * @license	http://www.gnu.org/licenses/gpl.html   Licensed under the GNU General Public License v3.
 * @package	WoWRoster
 * @subpackage LuaUpdate
 */

if ( !defined('IN_ROSTER') )
{
	exit('Detected invalid access to this file!');
}

/**
 * Lua Update handler
 *
 * @package	WoWRoster
 * @subpackage LuaUpdate
 */
class update
{
	var $textmode = false;
	var $uploadData;
	var $addons = array();
	var $files = array();
	var $locale;
	var $blinds = array();

	var $processTime;			// time() starting timestamp for enforceRules

	var $messages = array();
	var $errors = array();
	var $assignstr = '';
	var $assigngem = '';		// 2nd tracking property since we build a gem list while building an items list

	var $membersadded = 0;
	var $membersupdated = 0;
	var $membersremoved = 0;

	var $current_region = '';
	var $current_realm = '';
	var $current_guild = '';
	var $current_member = '';
	var $talent_build_urls = array();

	/**
	 * Collect info on what files are used
	 */
	function fetchAddonData()
	{
		global $roster;

		// Add roster-used tables
		$this->files[] = 'wowrcp';

		if( !$roster->config['use_update_triggers'] )
		{
			return;
		}

		if( !empty($roster->addon_data) )
		{
			foreach( $roster->addon_data as $row )
			{
				$hookfile = ROSTER_ADDONS . $row['basename'] . DIR_SEP . 'inc' . DIR_SEP . 'update_hook.php';

				if( file_exists($hookfile) )
				{
					// Check if this addon is in the process of an upgrade and deny access if it hasn't yet been upgraded
					$installfile = ROSTER_ADDONS . $row['basename'] . DIR_SEP . 'inc' . DIR_SEP . 'install.def.php';
					$install_class = $row['basename'] . 'Install';

					if( file_exists($installfile) )
					{
						include_once($installfile);

						if( class_exists($install_class) )
						{
							$addonstuff = new $install_class;

							// -1 = overwrote newer version
							//  0 = same version
							//  1 = upgrade available
							if( version_compare($addonstuff->version,$row['version']) )
							{
								$this->setError(sprintf($roster->locale->act['addon_upgrade_notice'],$row['basename']),$roster->locale->act['addon_error']);
								continue;
							}
							unset($addonstuff);
						}
					}

					$addon = getaddon($row['basename']);

					include_once($hookfile);

					$updateclass = $row['basename'] . 'Update';

					// Save current locale array
					// Since we add all locales for localization, we save the current locale array
					// This is in case one addon has the same locale strings as another, and keeps them from overwritting one another
					$localetemp = $roster->locale->wordings;

					foreach( $roster->multilanguages as $lang )
					{
						$roster->locale->add_locale_file(ROSTER_ADDONS . $addon['basename'] . DIR_SEP . 'locale' . DIR_SEP . $lang . '.php',$lang);
					}

					$addon['fullname'] = ( isset($roster->locale->act[$addon['fullname']]) ? $roster->locale->act[$addon['fullname']] : $addon['fullname'] );

					if( class_exists($updateclass) )
					{
						$this->addons[$row['basename']] = new $updateclass($addon);
						$this->files = array_merge($this->files,$this->addons[$row['basename']]->files);
					}
					else
					{
						$this->setError('Failed to load update trigger for ' . $row['basename'] . ': Update class did not exist',$roster->locale->act['addon_error']);
					}
					// Restore our locale array
					$roster->locale->wordings = $localetemp;
					unset($localetemp);
				}
			}
		}

		// Remove duplicates
		$this->files = array_unique($this->files);

		// Make all the file names requested lower case
		$this->files = array_flip($this->files);
		$this->files = array_change_key_case($this->files);
		$this->files = array_flip($this->files);
	}

	/**
	*
	*	file error upload handler
	*	returns true/false | sets error message with file name
	*/
	
	function upload_error_check($file)
	{
		global $roster;

		switch($file['error'])
		{
			case UPLOAD_ERR_OK:		  // Value: 0; There is no error, the file uploaded with success.
				return true;
			break;
 
			case UPLOAD_ERR_INI_SIZE:	// Value: 1; The uploaded file exceeds the upload_max_filesize directive in php.ini.
				$this->setError('The uploaded file exceeds the upload_max_filesize directive in php.ini.','File Error ['.$file['name'].']');
				
			case UPLOAD_ERR_FORM_SIZE:   // Value: 2; The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.
				$this->setError('The uploaded file exceeds the server maximum filesize allowed.','File Error ['.$file['name'].']');
				return false;
			break;
 
			case UPLOAD_ERR_PARTIAL:	 // Value: 3; The uploaded file was only partially uploaded.
				$this->setError('The uploaded file was only partially uploaded.','File Error ['.$file['name'].']');
				return false;
				break;
 
			case UPLOAD_ERR_NO_FILE:	 // Value: 4; No file was uploaded.
				$this->setError('No file was uploaded.','File Error ['.$file['name'].']');
				return false;
				break;
 
			case UPLOAD_ERR_NO_TMP_DIR:  // Value: 6; Missing a temporary folder.
				$output .= '<li>Missing a temporary folder. Please contact the admin.</li>';
				return false;
				break;
 
			case UPLOAD_ERR_CANT_WRITE:  // Value: 7; Failed to write file to disk.
				$output .= '<li>Failed to write file to disk. Please contact the admin.</li>';
				return false;
				break;
		}
	}
	
	
	/**
	 * Parses the files and put it in $uploadData
	 *
	 * @return string $output | Output messages
	 */
	function parseFiles( )
	{
		global $roster;

		if( !is_array($_FILES) )
		{
			return '<span class="red">Upload failed: No files present</span>' . "<br />\n";
		}

		require_once(ROSTER_LIB . 'luaparser.php');
		$output = $roster->locale->act['parsing_files'] . "<br />\n<ul>";
		foreach( $_FILES as $file )
		{
			if( !empty($file['name']) && $this->upload_error_check($file))
			{
				$filename = explode('.',$file['name']);
				$filebase = strtolower($filename[0]);				
				
				if( in_array($filebase,$this->files))
				{
					// Get start of parse time
					$parse_starttime = format_microtime();
					$luahandler = new lua();
					$data = $luahandler->luatophp( $file['tmp_name'], isset($this->blinds[$filebase]) ? $this->blinds[$filebase] : array() );

					// Calculate parse time
					$parse_totaltime = round((format_microtime() - $parse_starttime), 2);

					if( $data )
					{
						$output .= '<li>' . sprintf($roster->locale->act['parsed_time'],$filename[0],$parse_totaltime) . "</li>\n";
						$this->uploadData[$filebase] = $data;
					}
					else
					{
						$output .= '<li>' . sprintf($roster->locale->act['error_parsed_time'],$filebase,$parse_totaltime) . "</li>\n";
						$output .= ($luahandler->error() != '' ? '<li>' . $luahandler->error() . "</li>\n" : '');
					}
					unset($luahandler);
				}
				else
				{
					$output .= '<li>' . sprintf($roster->locale->act['upload_not_accept'],$file['name']) . "</li>\n";
				}
			}
			else
			{
				$output .= '<li>' . sprintf($roster->locale->act['error_parsed_time'],$file['name'],'0') . "</li>\n";
			}
		}
		$output .= "</ul><br />\n";
		return $output;
	}

	/**
	 * Process the files
	 *
	 * @return string $output | Output messages
	 */
	function processFiles()
	{
		global $roster;
		$this->processTime = time();

		if( !is_array($this->uploadData) )
		{
			return '';
		}
		$output = $roster->locale->act['processing_files'] . "<br />\n";

		$gotfiles = array_keys($this->uploadData);

		if( $roster->auth->getAuthorized('lua_update') )
		{
			if( is_array($this->addons) && count($this->addons) > 0 )
			{
				foreach( array_keys($this->addons) as $addon )
				{
					if( count(array_intersect($gotfiles, $this->addons[$addon]->files)) > 0 )
					{
						if( file_exists($this->addons[$addon]->data['trigger_file']) )
						{
							$this->addons[$addon]->reset_messages();
							if( method_exists($this->addons[$addon], 'update') )
							{
								$result = $this->addons[$addon]->update();

								if( $result )
								{
									$output .= $this->addons[$addon]->messages;
								}
								else
								{
									$output .= sprintf($roster->locale->act['error_addon'],$this->addons[$addon]->data['fullname'],'update') . "<br />\n"
											 . $roster->locale->act['addon_messages'] . "<br />\n" . $this->addons[$addon]->messages;
								}
							}
						}
					}
				}
			}

			if( $roster->config['enforce_rules'] == '1' )
			{
				$this->enforceRules($this->processTime);
			}
		}

		return $output;
	}

	/**
	 * Run trigger
	 */
	function addon_hook( $mode , $data , $memberid = '0' )
	{
		global $roster;

		$output = '';
		foreach( array_keys($this->addons) as $addon )
		{
			if( file_exists($this->addons[$addon]->data['trigger_file']) )
			{
				$this->addons[$addon]->reset_messages();
				if( method_exists($this->addons[$addon], $mode) )
				{
					$result = $this->addons[$addon]->{$mode}($data , $memberid);

					if( $result )
					{
						if( $mode == 'guild' )
						{
							$output .= '<li>' . $this->addons[$addon]->messages . "</li>\n";
						}
						else
						{
							$output .= $this->addons[$addon]->messages . "<br />\n";
						}
					}
					else
					{
						if( $mode == 'guild' )
						{
							$output .= '<li>' . sprintf($roster->locale->act['error_addon'],$this->addons[$addon]->data['fullname'],$mode) . "<br />\n"
									 . $roster->locale->act['addon_messages'] . "<br />\n" . $this->addons[$addon]->messages . "</li>\n";
						}
						else
						{
							$output .= sprintf($roster->locale->act['error_addon'],$this->addons[$addon]->data['fullname'],$mode) . "<br />\n"
									 . $roster->locale->act['addon_messages'] . "<br />\n" . $this->addons[$addon]->messages . "<br />\n";
						}
					}
				}
			}
		}

		return $output;
	}

	/**
	 * Returns the file input fields for all addon files we need.
	 *
	 * @return string $filefields | The HTML, without border
	 */
	function makeFileFields($blockname='file_fields')
	{
		global $roster;

		if( !is_array($this->files) || (count($this->files) == 0) ) // Just in case
		{
			$roster->tpl->assign_block_vars($blockname, array(
				'TOOLTIP' => '',
				'FILE' => 'No files accepted!'
			));
		}

		$account_dir = '<i>*WOWDIR*</i>\\\\WTF\\\\Account\\\\<i>*ACCOUNT_NAME*</i>\\\\SavedVariables\\\\';

		foreach( $this->files as $file )
		{
			$roster->tpl->assign_block_vars($blockname, array(
				'TOOLTIP' => makeOverlib($account_dir . $file . '.lua', $file . '.lua Location', '', 2, '', ',WRAP'),
				'FILE' => $file
			));
		}
	}

	/**
	 * Adds a message to the $messages array
	 *
	 * @param string $message
	 */
	function setMessage($message)
	{
		$this->messages[] = $message;
	}


	/**
	 * Returns all messages
	 *
	 * @return string
	 */
	function getMessages()
	{
		return implode("\n",$this->messages) . "\n";
	}


	/**
	 * Resets the stored messages
	 *
	 */
	function resetMessages()
	{
		$this->messages = array();
	}


	/**
	 * Adds an error to the $errors array
	 *
	 * @param string $message
	 */
	function setError( $message , $error )
	{
		$this->errors[] = array($message=>$error);
	}


	/**
	 * Gets the errors in wowdb
	 * Return is based on $mode
	 *
	 * @param string $mode
	 * @return mixed
	 */
	function getErrors( $mode='' )
	{
		if( $mode == 'a' )
		{
			return $this->errors;
		}

		$output = '';

		$errors = $this->errors;
		if( !empty($errors) )
		{
			$output = '<table width="100%" cellspacing="0">';
			$steps = 0;
			foreach( $errors as $errorArray )
			{
				foreach( $errorArray as $message => $error )
				{
					if( $steps == 1 )
					{
						$steps = 2;
					}
					else
					{
						$steps = 1;
					}

					$output .= "<tr><td class=\"membersRowRight$steps\">$error<br />\n"
							 . "$message</td></tr>\n";
				}
			}
			$output .= '</table>';
		}
		return $output;
	}

	/**
	 * DB insert code (former WoWDB)
	 */

	/**
	 * Resets the SQL insert/update string holder
	 */
	function reset_values()
	{
		$this->assignstr = '';
	}


	/**
	 * Add a value to an INSERT or UPDATE SQL string
	 *
	 * @param string $row_name
	 * @param string $row_data
	 */
	function add_value( $row_name , $row_data )
	{
		global $roster;

		if( $this->assignstr != '' )
		{
			$this->assignstr .= ',';
		}

		// str_replace added to get rid of non breaking spaces in cp.lua tooltips
		$row_data = str_replace('\n\n','<br>',$row_data);
		$row_data = str_replace(chr(194) . chr(160), ' ', $row_data);
		$row_data = stripslashes($row_data);
		$row_data = $roster->db->escape($row_data);

		$this->assignstr .= " `$row_name` = '$row_data'";
	}


	/**
	 * Verifies existance of variable before attempting add_value
	 *
	 * @param array $array
	 * @param string $key
	 * @param string $field
	 * @param string $default
	 * @return boolean
	 */
	function add_ifvalue( $array , $key , $field=false , $default=false )
	{
		if( $field === false )
		{
			$field = $key;
		}

		if( isset($array[$key]) )
		{
			$this->add_value($field, $array[$key]);
			return true;
		}
		else
		{
			if( $default !== false )
			{
				$this->add_value($field, $default);
			}
			return false;
		}
	}

	/**
	 * Add a gem to an INSERT or UPDATE SQL string
	 * (clone of add_value method--this functions as a 2nd SQL insert placeholder)
	 *
	 * @param string $row_name
	 * @param string $row_data
	 */
	function add_gem( $row_name , $row_data )
	{
		global $roster;

		if( $this->assigngem != '' )
		{
			$this->assigngem .= ',';
		}

		$row_data = "'" . $roster->db->escape($row_data) . "'";

		$this->assigngem .= " `$row_name` = $row_data";
	}


	/**
	 * Add a time value to an INSERT or UPDATE SQL string
	 *
	 * @param string $row_name
	 * @param array $date
	 */
	function add_time( $row_name , $date )
	{
		// 2000-01-01 23:00:00.000
		$row_data = $date['year'] . '-' . $date['mon'] . '-' . $date['mday'] . ' ' . $date['hours'] . ':' . $date['minutes'] . ':' . $date['seconds'];
		$this->add_value($row_name,$row_data);
	}


	/**
	 * Add a time value to an INSERT or UPDATE SQL string
	 *
	 * @param string $row_name
	 * @param string $date | UNIX TIMESTAMP
	 */
	function add_timestamp( $row_name , $date )
	{
		$date = date('Y-m-d H:i:s',$date);
		$this->add_value($row_name,$date);
	}

	/**
	 * Turn the WoW internal icon format into the one used by us
	 * All lower case and spaces converted into _
	 *
	 * @param string $icon_name
	 * @return string
	 */
	function fix_icon( $icon_name )
	{
		$icon_name = basename($icon_name);
		return strtolower(str_replace(' ','_',$icon_name));
	}

	/**
	 * Format tooltips for insertion to the db
	 *
	 * @param mixed $tipdata
	 * @return string
	 */
	function tooltip( $tipdata )
	{
		$tooltip = '';
		//$tipdata = preg_replace('/\|c[a-f0-9]{8}(.+?)\|r/i','$1',$tipdata);
		$tipdata = preg_replace('/\|c([0-9a-f]{2})([0-9a-f]{6})([^\|]+)/','<span style="color:#$2;">$3</span>',$tipdata);
		$tipdata = str_replace('|r', '', $tipdata);

		
		if( is_array($tipdata) )
		{
			$tooltip = implode("<br>",$tipdata);
		}
		else
		{
			$tooltip = $tipdata;//str_replace('<br>',"\n",$tipdata);
		}
		return $tooltip;
	}

	/**
	 * Delete Members in database not matching the upload rules
	 */
	function enforceRules( $timestamp )
	{
		global $roster;

		$messages = '';
		// Select and delete all non-matching guilds
		$query = "SELECT *"
			. " FROM `" . $roster->db->table('guild') . "` guild"
			. " WHERE `guild_name` NOT LIKE 'guildless-_';";
		$result = $roster->db->query($query);
		while( $row = $roster->db->fetch($result) )
		{
			$query = "SELECT `type`, COUNT(`rule_id`)"
				   . " FROM `" . $roster->db->table('upload') . "`"
				   . " WHERE (`type` = 0 OR `type` = 1)"
				   . " AND '" . $roster->db->escape($row['guild_name']) . "' LIKE `name` "
				   . " AND '" . $roster->db->escape($row['server']) . "' LIKE `server` "
				   . " AND '" . $roster->db->escape($row['region']) . "' LIKE `region` "
				   . " GROUP BY `type` "
				   . " ORDER BY `type` DESC;";
			if( $roster->db->query_first($query) !== '0' )
			{
				$messages .= '<ul><li>Deleting guild "' . $row['guild_name'] . '" and setting its members guildless.</li>';
				// Does not match rules
				$this->deleteGuild($row['guild_id'], $timestamp);
				$messages .= '</ul>';
			}
		}

		// Select and delete all non-matching guildless members
		$messages .= '<ul>';
		$inClause=array();

		$query = "SELECT *"
			. " FROM `" . $roster->db->table('members') . "` members"
			. " INNER JOIN `" . $roster->db->table('guild') . "` guild"
				. " USING (`guild_id`)"
			. " WHERE `guild_name` LIKE 'guildless-_';";
		$result = $roster->db->query($query);

		while( $row = $roster->db->fetch($result) )
		{
			$query = "SELECT `type`, COUNT(`rule_id`)"
				   . " FROM `" . $roster->db->table('upload') . "`"
				   . " WHERE (`type` = 2 OR `type` = 3)"
				   . " AND '" . $roster->db->escape($row['name']) . "' LIKE `name` "
				   . " AND '" . $roster->db->escape($row['server']) . "' LIKE `server` "
				   . " AND '" . $roster->db->escape($row['region']) . "' LIKE `region` "
				   . " GROUP BY `type` "
				   . " ORDER BY `type` DESC;";
			if( $roster->db->query_first($query) !== '2' )
			{
				$messages .= '<li>Deleting member "' . $row['name'] . '".</li>';
				// Does not match rules
				$inClause[] = $row['member_id'];
			}
		}

		if( count($inClause) == 0 )
		{
			$messages .= '<li>No members deleted.</li>';
		}
		else
		{
			$this->deleteMembers(implode(',', $inClause));
		}
		$this->setMessage($messages . '</ul>');
	}


	/**
	 * Update Memberlog function
	 *
	 */
	function updateMemberlog( $data , $type , $timestamp )
	{
		global $roster;

		$this->reset_values();
		$this->add_ifvalue($data, 'member_id');
		$this->add_ifvalue($data, 'name');
		$this->add_ifvalue($data, 'server');
		$this->add_ifvalue($data, 'region');
		$this->add_ifvalue($data, 'guild_id');
		$this->add_ifvalue($data, 'class');
		$this->add_ifvalue($data, 'classid');
		$this->add_ifvalue($data, 'level');
		$this->add_ifvalue($data, 'note');
		$this->add_ifvalue($data, 'guild_rank');
		$this->add_ifvalue($data, 'guild_title');
		$this->add_ifvalue($data, 'officer_note');
		$this->add_time('update_time', getDate($timestamp));
		$this->add_value('type', $type);

		$querystr = "INSERT INTO `" . $roster->db->table('memberlog') . "` SET " . $this->assignstr . ";";
		$result = $roster->db->query($querystr);
		if( !$result )
		{
			$this->setError('Member Log [' . $data['name'] . '] could not be inserted',$roster->db->error());
		}
	}


	/**
	 * Delete Guild from database. Doesn't directly delete members, because some of them may have individual upload permission (char based)
	 *
	 * @param int $guild_id
	 * @param string $timestamp
	 */
	function deleteGuild( $guild_id , $timestamp )
	{
		global $roster;

		$query = "SELECT (`guild_name` LIKE 'Guildless-%') FROM `" . $roster->db->table('guild') . "` WHERE `guild_id` = '" . $guild_id . "';";

		if( $roster->db->query_first($query) )
		{
			$this->setError('Guildless- guilds have a special meaning internally. You cannot explicitly delete them, they will be deleted automatically once the last member is deleted. To delete the guildless guild, delete all its members');
		}

		// Set all members as left
		$query = "UPDATE `" . $roster->db->table('members') . "` SET `active` = 0 WHERE `guild_id` = '" . $guild_id . "';";
		$roster->db->query($query);

		// Set those members guildless. After that the guild will be empty, and remove_guild_members will call deleteEmptyGuilds to clean that up.
		$this->remove_guild_members($guild_id, $timestamp);
	}

	/**
	 * Clean up empty guilds.
	 */
	function deleteEmptyGuilds()
	{
		global $roster;

		$query = "DELETE FROM `" . $roster->db->table('guild') . "` WHERE `guild_id` NOT IN (SELECT DISTINCT `guild_id` FROM `" . $roster->db->table('members') . "`);";
		$roster->db->query($query);

	}

	/**
	 * Delete Members in database using inClause
	 * (comma separated list of member_id's to delete)
	 *
	 * @param string $inClause
	 */
	function deleteMembers( $inClause )
	{
		global $roster;

		$messages = '<li>';

		$messages .= 'Character Data..';

		$messages .= 'Skills..';
		$querystr = "DELETE FROM `" . $roster->db->table('skills') . "` WHERE `member_id` IN ($inClause)";
		if( !$roster->db->query($querystr) )
		{
			$this->setError('Skill Data could not be deleted',$roster->db->error());
		}

		$messages .= 'Inventory..';
		$querystr = "DELETE FROM `" . $roster->db->table('items') . "` WHERE `member_id` IN ($inClause)";
		if( !$roster->db->query($querystr) )
		{
			$this->setError('Inventory Data could not be deleted',$roster->db->error());
		}

		$messages .= 'Professions..';
		$querystr = "DELETE FROM `" . $roster->db->table('recipes') . "` WHERE `member_id` IN ($inClause)";
		if( !$roster->db->query($querystr) )
		{
			$this->setError('Recipe Data could not be deleted',$roster->db->error());
		}


		$messages .= 'Talents..';
		$querystr = "DELETE FROM `" . $roster->db->table('talents') . "` WHERE `member_id` IN ($inClause)";
		if( !$roster->db->query($querystr) )
		{
			$this->setError('Talent Data could not be deleted',$roster->db->error());
		}

		$querystr = "DELETE FROM `" . $roster->db->table('talenttree') . "` WHERE `member_id` IN ($inClause)";
		if( !$roster->db->query($querystr) )
		{
			$this->setError('Talent Tree Data could not be deleted',$roster->db->error());
		}

		$messages .= 'Reputation..';
		$querystr = "DELETE FROM `" . $roster->db->table('reputation') . "` WHERE `member_id` IN ($inClause)";
		if( !$roster->db->query($querystr) )
		{
			$this->setError('Reputation Data could not be deleted',$roster->db->error());
		}

		$messages .= 'Membership..';
		$querystr = "DELETE FROM `" . $roster->db->table('members') . "` WHERE `member_id` IN ($inClause)";
		if( !$roster->db->query($querystr) )
		{
			$this->setError('Member Data could not be deleted',$roster->db->error());
		}

		$messages .= 'Final Character Cleanup..';
		$querystr = "DELETE FROM `" . $roster->db->table('players') . "` WHERE `member_id` IN ($inClause)";
		if( !$roster->db->query($querystr) )
		{
			$this->setError('Player Data could not be deleted',$roster->db->error());
		}

		if( $roster->config['use_update_triggers'] )
		{
			$messages .= $this->addon_hook('char_delete', $inClause);
		}

		$this->deleteEmptyGuilds();

		$this->setMessage($messages . '</li>');
	}

	/**
	 * Removes guild members with `active` = 0
	 *
	 * @param int $guild_id
	 * @param string $timestamp
	 */
	function remove_guild_members( $guild_id , $timestamp )
	{
		global $roster;

		$querystr = "SELECT * FROM `" . $roster->db->table('members') . "` WHERE `guild_id` = '$guild_id' AND `active` = '0';";

		$result = $roster->db->query($querystr);
		if( !$result )
		{
			$this->setError('Members could not be selected for deletion',$roster->db->error());
			return;
		}

		$num = $roster->db->num_rows($result);
		if( $num > 0 )
		{
			// Get guildless guild for this realm
			$query = "SELECT * FROM `" . $roster->db->table('guild') . "` WHERE `guild_id` = '$guild_id';";
			$result2 = $roster->db->query($query);
			$row = $roster->db->fetch($result2);
			$roster->db->free_result($result2);

			$query = "SELECT `guild_id` FROM `" . $roster->db->table('guild') . "` WHERE `server` = '" . $roster->db->escape($row['server']) . "' AND `region` = '" . $roster->db->escape($row['region']) . "' AND `factionEn` = '" . $roster->db->escape($row['factionEn']) . "' AND `guild_name` LIKE 'guildless-%';";
			$guild_id = $roster->db->query_first($query);

			if( !$guild_id )
			{
				$guilddata['Faction'] = $row['factionEn'];
				$guilddata['FactionEn'] = $row['factionEn'];
				$guilddata['Locale'] = $row['Locale'];
				$guilddata['Info'] = '';
				$guild_id = $this->update_guild($row['server'],'GuildLess-' . substr($row['factionEn'],0,1),strtotime($timestamp),$guilddata,$row['region']);
				unset($guilddata);
			}

			$inClause = array();
			while( $row = $roster->db->fetch($result) )
			{
				$this->setMessage('<li><span class="red">[</span> ' . $row[1] . ' <span class="red">] - Removed</span></li>');
				$this->setMemberLog($row,0,$timestamp);

				$inClause[] = $row[0];
			}
			$inClause = implode(',',$inClause);

			// now that we have our inclause, set them guildless
			$this->setMessage('<li><span class="red">Setting ' . $num . ' member' . ($num > 1 ? 's' : '') . ' to guildless</span></li>');

			$roster->db->free_result($result);

			$this->reset_values();
			$this->add_value('guild_id',$guild_id);
			$this->add_value('note','');
			$this->add_value('guild_rank',0);
			$this->add_value('guild_title','');
			$this->add_value('officer_note','');

			$querystr = "UPDATE `" . $roster->db->table('members') . "` SET " . $this->assignstr . " WHERE `member_id` IN ($inClause);";
			if( !$roster->db->query($querystr) )
			{
				$this->setError('Guild members could not be set guildless',$roster->db->error());
			}

			$this->reset_values();
			$this->add_value('guild_id',$guild_id);

			$querystr = "UPDATE `" . $roster->db->table('players') . "` SET " . $this->assignstr . " WHERE `member_id` IN ($inClause);";
			if( !$roster->db->query($querystr) )
			{
				$this->setError('Guild members could not be set guildless',$roster->db->error());
			}
		}

		$this->deleteEmptyGuilds();
	}

	/**
	 * Gets guild info from database
	 * Returns info as an array
	 *
	 * @param string $realmName
	 * @param string $guildName
	 * @return array
	 */
	function get_guild_info( $realmName , $guildName , $region='' )
	{
		global $roster;

		$guild_name_escape = $roster->db->escape($guildName);
		$server_escape = $roster->db->escape($realmName);

		if( !empty($region) )
		{
			$region = " AND `region` = '" . $roster->db->escape($region) . "'";
		}

		$querystr = "SELECT * FROM `" . $roster->db->table('guild') . "` WHERE `guild_name` = '$guild_name_escape' AND `server` = '$server_escape'$region;";
		$result = $roster->db->query($querystr) or die_quietly($roster->db->error(),'WowDB Error',__FILE__ . '<br />Function: ' . (__FUNCTION__),__LINE__,$querystr);

		if( $roster->db->num_rows() > 0 )
		{
			$retval = $roster->db->fetch($result);
			$roster->db->free_result($result);

			return $retval;
		}
		else
		{
			return false;
		}
	}

	function get_guild_rank( $guild_id )
	{
		global $roster;

		$querystr = "SELECT * FROM `" . $roster->db->table('guild_rank') . "` WHERE `guild_id` = '$guild_id';";
		$result = $roster->db->query($querystr) or die_quietly($roster->db->error(),'WowDB Error',__FILE__ . '<br />Function: ' . (__FUNCTION__),__LINE__,$querystr);

		if( $roster->db->num_rows() > 0 )
		{
			$retval = $roster->db->fetch_all($result);
			$roster->db->free_result($result);

			return $retval;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Function to prepare the memberlog data
	 *
	 * @param array $data | Member info array
	 * @param multiple $type | Action to update ( 'rem','del,0 | 'add','new',1 )
	 * @param string $timestamp | Time
	 */
	function setMemberLog( $data , $type , $timestamp )
	{
		if ( is_array($data) )
		{
			switch ($type)
			{
				case 'del':
				case 'rem':
				case 0:
					$this->membersremoved++;
					$this->updateMemberlog($data,0,$timestamp);
					break;

				case 'add':
				case 'new':
				case 1:
					$this->membersadded++;
					$this->updateMemberlog($data,1,$timestamp);
					break;
			}
		}
	}

	/**
	 * Updates or creates the guild rank database
	 *
	 * @param array $guild
	 */
	function update_guild_ranks($guild , $guild_id )
	{
		global $roster;
		
		$guild_ranks = $this->get_guild_rank($guild_id);
		$ranks = array();
		if (is_array($guild_ranks))
		{
			foreach($guild_ranks as $r => $rw)
			{
				$ranks[$rw['rank']] = array('title' => $rw['title'],'control' => $rw['control']);
			}
		}
		
		foreach($guild['Ranks'] as $id => $d)
		{
			$this->reset_values();
			$this->add_value('rank', $id);
			$this->add_value('guild_id', $guild_id);
			$this->add_ifvalue($d, 'Title', 'title');
			$this->add_ifvalue($d, 'Control', 'control');

			if( isset($ranks[$id]['title']) && $ranks[$id]['title'] == $d['Title'] )
			{
				$querystra = "UPDATE `" . $roster->db->table('guild_rank') . "` SET " . $this->assignstr . " WHERE `rank` = '" . $id . "' AND `guild_id` = '" . $guild_id . "';";
			}
			else
			{
				$querystra = "INSERT INTO `" . $roster->db->table('guild_rank') . "` SET " . $this->assignstr;
			}

			$roster->db->query($querystra) or die_quietly($roster->db->error(),'WowDB Error',__FILE__ . '<br />Function: ' . (__FUNCTION__),__LINE__,$querystra);
		}
	}
	
	/**
	 * Updates or creates an entry in the guild table in the database
	 * Then returns the guild ID
	 *
	 * @param string $realmName
	 * @param string $guildName
	 * @param array $currentTime
	 * @param array $guild
	 * @return string
	 */
	function update_guild( $realmName , $guildName , $currentTime , $guild , $region )
	{
		global $roster;
		$guildInfo = $this->get_guild_info($realmName,$guildName,$region);

		$this->locale = $guild['Locale'];

		$this->reset_values();

		$this->add_value('guild_name', $guildName);

		$this->add_value('server', $realmName);
		$this->add_value('region', $region);
		$this->add_ifvalue($guild, 'Faction', 'faction');
		$this->add_ifvalue($guild, 'FactionEn', 'factionEn');
		$this->add_ifvalue($guild, 'Motd', 'guild_motd');

		$this->add_ifvalue($guild, 'NumMembers', 'guild_num_members');
		$this->add_ifvalue($guild, 'NumAccounts', 'guild_num_accounts');

		$this->add_ifvalue($guild, 'GuildXP', 'guild_xp');
		$this->add_ifvalue($guild, 'GuildXPCap', 'guild_xpcap');
		$this->add_ifvalue($guild, 'GuildLevel', 'guild_level');

		$this->add_timestamp('update_time', $currentTime);

		$this->add_ifvalue($guild, 'DBversion');
		$this->add_ifvalue($guild, 'GPversion');
		if (is_array($guild['Info']))
		{
			$this->add_value('guild_info_text', str_replace('\n',"<br />",$guild['Info']));
		}

		if( is_array($guildInfo) )
		{
			$querystra = "UPDATE `" . $roster->db->table('guild') . "` SET " . $this->assignstr . " WHERE `guild_id` = '" . $guildInfo['guild_id'] . "';";
			$output = $guildInfo['guild_id'];
		}
		else
		{
			$querystra = "INSERT INTO `" . $roster->db->table('guild') . "` SET " . $this->assignstr;
		}

		$roster->db->query($querystra) or die_quietly($roster->db->error(),'WowDB Error',__FILE__ . '<br />Function: ' . (__FUNCTION__),__LINE__,$querystra);

		if( is_array($guildInfo) )
		{
			$querystr = "UPDATE `" . $roster->db->table('members') . "` SET `active` = '0' WHERE `guild_id` = '" . $guildInfo['guild_id'] . "';";
			$roster->db->query($querystr) or die_quietly($roster->db->error(),'WowDB Error',__FILE__ . '<br />Function: ' . (__FUNCTION__),__LINE__,$querystr);
		}

		if( !is_array($guildInfo) )
		{
			$guildInfo = $this->get_guild_info($realmName,$guildName);
			$output = $guildInfo['guild_id'];
		}

		return $output;
	}


	/**
	 * Updates or adds guild members
	 *
	 * @param int $guildId	| Character's guild id
	 * @param string $name	| Character's name
	 * @param array $char	| LUA data
	 * @param array $currentTimestamp
	 * @return mixed		| False on error, memberid on success
	 */
	function update_guild_member( $guildId , $name , $server , $region , $char , $currentTimestamp , $guilddata )
	{
		global $roster;

		$name_escape = $roster->db->escape($name);
		$server_escape = $roster->db->escape($server);
		$region_escape = $roster->db->escape($region);

		$querystr = "SELECT `member_id` "
			. "FROM `" . $roster->db->table('members') . "` "
			. "WHERE `name` = '$name_escape' "
			. "AND `server` = '$server_escape' "
			. "AND `region` = '$region_escape';";
		$result = $roster->db->query($querystr);
		if( !$result )
		{
			$this->setError('Member could not be selected for update',$roster->db->error());
			return false;
		}

		$memberInfo = $roster->db->fetch( $result );
		if( $memberInfo )
		{
			$memberId = $memberInfo['member_id'];
		}

		$roster->db->free_result($result);

		$this->reset_values();

		$this->add_value('name', $name);
		$this->add_value('server', $server);
		$this->add_value('region', $region);
		$this->add_value('guild_id', $guildId);
		$this->add_ifvalue($char, 'Class', 'class');
		$this->add_ifvalue($char, 'ClassId', 'classid');
		$this->add_ifvalue($char, 'Level', 'level');
		$this->add_ifvalue($char, 'Note', 'note', '');
		$this->add_ifvalue($char, 'Rank', 'guild_rank');

		if( isset($char['Rank']) && isset($guilddata['Ranks'][$char['Rank']]['Title']) )
		{
			$this->add_value('guild_title', $guilddata['Ranks'][$char['Rank']]['Title']);
		}
		else if( isset($char['RankEn']) )
		{
			$this->add_value('guild_title', $char['RankEn']);
		}

		if( isset($guilddata['ScanInfo']) && $guilddata['ScanInfo']['HasOfficerNote'] )
		{
			$this->add_ifvalue($char, 'OfficerNote', 'officer_note', '');
		}

		$this->add_ifvalue($char, 'Zone', 'zone', '');
		$this->add_ifvalue($char, 'Status', 'status', '');
		$this->add_value('active', '1');

		if( isset($char['Online']) && $char['Online'] == '1' )
		{
			$this->add_value('online', 1);
			$this->add_time('last_online', getDate($currentTimestamp));
		}
		else
		{
			$this->add_value('online', 0);
			list($lastOnlineYears,$lastOnlineMonths,$lastOnlineDays,$lastOnlineHours) = explode(':',$char['LastOnline']);

			# use strtotime instead
			#	  $lastOnlineTime = $currentTimestamp - 365 * 24* 60 * 60 * $lastOnlineYears
			#						- 30 * 24 * 60 * 60 * $lastOnlineMonths
			#						- 24 * 60 * 60 * $lastOnlineDays
			#						- 60 * 60 * $lastOnlineHours;
			$timeString = '-';
			if ($lastOnlineYears > 0)
			{
				$timeString .= $lastOnlineYears . ' Years ';
			}
			if ($lastOnlineMonths > 0)
			{
				$timeString .= $lastOnlineMonths . ' Months ';
			}
			if ($lastOnlineDays > 0)
			{
				$timeString .= $lastOnlineDays . ' Days ';
			}
			$timeString .= max($lastOnlineHours,1) . ' Hours';

			$lastOnlineTime = strtotime($timeString,$currentTimestamp);
			$this->add_time('last_online', getDate($lastOnlineTime));
		}

		if( isset($memberId) )
		{
			$querystr = "UPDATE `" . $roster->db->table('members') . "` SET " . $this->assignstr . " WHERE `member_id` = '$memberId';";
			$this->setMessage('<li>[ ' . $name . ' ]<ul>');
			$this->membersupdated++;

			$result = $roster->db->query($querystr);
			if( !$result )
			{
				$this->setError($name . ' could not be inserted',$roster->db->error());
				return false;
			}
		}
		else
		{
			$querystr = "INSERT INTO `" . $roster->db->table('members') . "` SET " . $this->assignstr . ';';
			//$this->setMessage('<li><span class="green">[</span> ' . $name . ' <span class="green">] - Added</span></li>');
			$this->setMessage('<li><span class="green">[</span> ' . $name . ' <span class="green">] - Added</span><ul>');

			$result = $roster->db->query($querystr);
			if( !$result )
			{
				$this->setError($name . ' could not be inserted',$roster->db->error());
				return false;
			}

			$memberId = $roster->db->insert_id();

			$querystr = "SELECT * FROM `" . $roster->db->table('members') . "` WHERE `member_id` = '$memberId';";
			$result = $roster->db->query($querystr);
			if( !$result )
			{
				$this->setError('Member could not be selected for MemberLog',$roster->db->error());
			}
			else
			{
				$row = $roster->db->fetch($result);
				$this->setMemberLog($row,1,$currentTimestamp);
			}
		}

		// We may have added the last member of the guildless guild to a real guild, so check for empty guilds
		$this->deleteEmptyGuilds();

		return $memberId;
	}

	/**
	 * Handles formatting an insertion of Character Data
	 *
	 * @param int $guildId
	 * @param string $region
	 * @param string $name
	 * @param array $data
	 * @return mixed False on failure | member_id on success
	 */
	function update_char( $guildId , $region , $server , $name , $data )
	{
		global $roster;

		$name_escape = $roster->db->escape($name);
		$server_escape = $roster->db->escape($server);
		$region_escape = $roster->db->escape($region);

		$querystr = "SELECT `member_id` "
			. "FROM `" . $roster->db->table('members') . "` "
			. "WHERE `name` = '$name_escape' "
			. "AND `server` = '$server_escape' "
			. "AND `region` = '$region_escape';";

		$result = $roster->db->query($querystr);
		if( !$result )
		{
			$this->setError('Cannot select member_id for Character Data',$roster->db->error());
			return false;
		}

		$memberInfo = $roster->db->fetch($result);
		$roster->db->free_result($result);

		if (isset($memberInfo) && is_array($memberInfo))
		{
			$memberId = $memberInfo['member_id'];
		}
		else
		{
			$this->setMessage('<li>Missing member id for ' . $name . '</li>');
			return false;
		}

		// update level in members table
		$querystr = "UPDATE `" . $roster->db->table('members') . "` SET `level` = '" . $data['Level'] . "' WHERE `member_id` = '$memberId' LIMIT 1;";
		$result = $roster->db->query($querystr);
		if( !$result )
		{
			$this->setError('Cannot update Level in Members Table',$roster->db->error());
		}


		$querystr = "SELECT `member_id` FROM `" . $roster->db->table('players') . "` WHERE `member_id` = '$memberId';";
		$result = $roster->db->query($querystr);
		if( !$result )
		{
			$this->setError('Cannot select member_id for Character Data',$roster->db->error());
			return false;
		}

		$update = $roster->db->num_rows($result) == 1;
		$roster->db->free_result($result);

		$this->reset_values();

		$this->add_value('name', $name);
		$this->add_value('guild_id', $guildId);
		$this->add_value('server', $server);
		$this->add_value('region', $region);

		$this->add_ifvalue($data, 'Level', 'level');

		if (!empty($data['Stats']))
			{
				foreach ($data['Stats'] as $s => $v)
				{
					$this->add_value( $s, $v );
				}
			}
			
		
		if ( isset($data['Attributes']['Melee']) && is_array($data['Attributes']['Melee']) )
		{
			$this->add_ifvalue($data['Attributes']['Melee'], 'CritChance', 'crit', 0);
		}
		
		// END STATS

		if( isset($data['Attributes']['ITEMLEVEL']))
		{
			$this->add_value('ilvl', $data['Attributes']['ITEMLEVEL']);
		}
		// BEGIN mastery
		if( isset($data['Attributes']['Mastery']) && is_array($data['Attributes']['Mastery']) )
		{
			$attack = $data['Attributes']['Mastery'];

			$this->add_ifvalue($attack, 'Percent', 'mastery');
			//$this->add_ifvalue($attack, 'Tooltip', 'mastery_tooltip');
			$this->add_value('mastery_tooltip', $this->tooltip($data['Attributes']['Mastery']['Tooltip']));

			unset($attack);
		}
		// END Mastery

		// BEGIN SPELL
		if( isset($data['Attributes']['Spell']) && is_array($data['Attributes']['Spell']) )
		{
			$spell = $data['Attributes']['Spell'];

			$this->add_rating('spell_hit', $spell['HitRating']);
			$this->add_rating('spell_crit', $spell['CritRating']);
			$this->add_rating('spell_haste', $spell['HasteRating']);

			$this->add_ifvalue($spell, 'CritChance', 'spell_crit_chance');

			list($not_cast, $cast) = explode(':',$spell['ManaRegen']);
			$this->add_value('mana_regen', $not_cast);
			$this->add_value('mana_regen_cast', $cast);
			unset($not_cast, $cast);

			$this->add_ifvalue($spell, 'Penetration', 'spell_penetration');
			$this->add_ifvalue($spell, 'BonusDamage', 'spell_damage');
			$this->add_ifvalue($spell, 'BonusHealing', 'spell_healing');

			if( isset($spell['SchoolCrit']) && is_array($spell['SchoolCrit']) )
			{
				$schoolcrit = $spell['SchoolCrit'];

				$this->add_ifvalue($schoolcrit, 'Holy', 'spell_crit_chance_holy');
				$this->add_ifvalue($schoolcrit, 'Frost', 'spell_crit_chance_frost');
				$this->add_ifvalue($schoolcrit, 'Arcane', 'spell_crit_chance_arcane');
				$this->add_ifvalue($schoolcrit, 'Fire', 'spell_crit_chance_fire');
				$this->add_ifvalue($schoolcrit, 'Shadow', 'spell_crit_chance_shadow');
				$this->add_ifvalue($schoolcrit, 'Nature', 'spell_crit_chance_nature');

				unset($schoolcrit);
			}

			if( isset($spell['School']) && is_array($spell['School']) )
			{
				$school = $spell['School'];

				$this->add_ifvalue($school, 'Holy', 'spell_damage_holy');
				$this->add_ifvalue($school, 'Frost', 'spell_damage_frost');
				$this->add_ifvalue($school, 'Arcane', 'spell_damage_arcane');
				$this->add_ifvalue($school, 'Fire', 'spell_damage_fire');
				$this->add_ifvalue($school, 'Shadow', 'spell_damage_shadow');
				$this->add_ifvalue($school, 'Nature', 'spell_damage_nature');

				unset($school);
			}

			unset($spell);
		}
		// END SPELL

		$this->add_ifvalue($data, 'TalentPoints', 'talent_points');

		//$this->add_ifvalue('money_c', $data['Money']['Copper']);
		//$this->add_ifvalue('money_s', $data['Money']['Silver']);
		//$this->add_ifvalue('money_g', $data['Money']['Gold']);
		
		$this->add_ifvalue($data, 'Race', 'race');
		$this->add_ifvalue($data, 'RaceId', 'raceid');
		$this->add_ifvalue($data, 'RaceEn', 'raceEn');
		$this->add_ifvalue($data, 'Class', 'class');
		$this->add_ifvalue($data, 'ClassId', 'classid');
		$this->add_ifvalue($data, 'ClassEn', 'classEn');
		//$this->add_ifvalue($data, 'Health', 'health');
		$this->add_ifvalue($data, 'Mana', 'mana');
		//$this->add_ifvalue($data, 'Power', 'power');
		$this->add_ifvalue($data, 'Sex', 'sex');
		$this->add_ifvalue($data, 'SexId', 'sexid');
		$this->add_ifvalue($data, 'Hearth', 'hearth');

		$this->add_ifvalue($data['timestamp']['init'], 'DateUTC','dateupdatedutc');

		$this->add_ifvalue($data, 'DBversion');
		$this->add_ifvalue($data, 'CPversion');
		
		// Capture client language
		//$this->add_ifvalue($data, 'Locale', 'clientLocale');

		$this->setMessage('<li>About to update player</li>');

		if( $update )
		{
			$querystr = "UPDATE `" . $roster->db->table('players') . "` SET " . $this->assignstr . " WHERE `member_id` = '$memberId';";
		}
		else
		{
			$this->add_value('member_id', $memberId);
			$querystr = "INSERT INTO `" . $roster->db->table('players') . "` SET " . $this->assignstr . ";";
		}

		$result = $roster->db->query($querystr);
		if( !$result )
		{
			$this->setError('Cannot update Character Data',$roster->db->error());
			return false;
		}

		$this->locale = $data['Locale'];

		if ( isset($data['Equipment']) && is_array($data['Equipment']) )
		{
			$this->do_equip($data, $memberId);
		}
		if ( isset($data['Inventory']) && is_array($data['Inventory']) )
		{
			$this->do_inventory($data, $memberId);
		}
		$this->do_skills($data, $memberId);
		$this->do_talents($data, $memberId);
		$this->do_reputation($data, $memberId);
		$this->do_companions($data, $memberId);

		return $memberId;

	} //-END function update_char()
	
	
	
}
