<?php
/**
 * WoWRoster.net WoWRoster
 *
 * Ajax interface file
 *
 *
 * @copyright  2002-2011 WoWRoster.net
 * @license    http://www.gnu.org/licenses/gpl.html   Licensed under the GNU General Public License v3.
 * @package    WoWRoster
 * @subpackage Ajax
*/

if( !defined('IN_ROSTER') )
{
	exit('Detected invalid access to this file!');
}
//d($roster);

$roster->api2->ignore_cache = true;
if ($_GET['state'] == 'login')
{
	$extra = array('state' => 'login2');
	$auth_url = $roster->api2->getAuthenticationUrl($roster->api2->baseurl[$roster->api2->region]['AUTHORIZATION_ENDPOINT'], $roster->api2->redirect_uri,$extra);
	header('Location: ' . $auth_url);
}

if ($_GET['state'] == 'claim')
{
	$params = array('code' => $_GET['code'], 'auth_flow' => 'auth_code', 'redirect_uri' => $roster->api2->redirect_uri, 'state' => 'login2');
	$response = $roster->api2->getAccessToken($roster->api2->baseurl[$roster->api2->region]['TOKEN_ENDPOINT'], 'authorization_code', $params);

	$roster->api2->setAccessToken($response['access_token']);
	$chars = $roster->api2->fetch('wowprofile');
	
	// ranks array for access lvls...
	$ranks = _getranks();
	$mranks = array();
	$update_sql = array();
	
	if (is_array($chars['characters']))
	{
		$query1 = 'DELETE FROM `' . $roster->db->table('user_link', 'user') . '` WHERE `uid` = '.$roster->auth->user['id'].'';
		$result1 = $roster->db->query($query1);
		foreach ($chars['characters'] as $id => $char)
		{
			if ($roster->data['guild_name'] == $char['guild'])
			{
				$idww = array();
				$idww = getcharid($char['name'],$char['realm']);
				$data = array(
					'uid'					=> $roster->auth->user['id'],
					'member_id'				=> (isset($idww['member_id']) ? $idww['member_id'] : '0'),
					'guild_id'				=> (isset($idww['guild_id']) ? $idww['guild_id'] : '0'),
					'group_id'				=> '',
					'is_main'				=> '0',
					'realm'					=> $char['realm'],
					'region'				=> $roster->api2->region,
					'name'					=> $char['name'],
					'battlegroup'			=> $char['battlegroup'],
					'class'					=> $char['class'],
					'race'					=> $char['race'],
					'gender'				=> $char['gender'],
					'level'					=> $char['level'],
					'achievementPoints'		=> $char['achievementPoints'],
					'thumbnail'				=> $char['thumbnail'],
					'guild'					=> (isset($char['guild']) ? $char['guild'] : ''),
					'guildRealm'			=> (isset($char['guildRealm']) ? $char['guildRealm'] : ''),
				);
				$query = 'INSERT INTO `' . $roster->db->table('user_link', 'user') . '` ' . $roster->db->build_query('INSERT', $data);
				$result = $roster->db->query($query);
				$update_sql[] = "UPDATE `" . $roster->db->table('members') . "`"
									  . " SET `account_id` = '" . $roster->auth->user['id'] . "'"
									  . " WHERE `name` = '".$roster->db->escape($char['name'])."' AND `server` = '".$roster->db->escape($char['realm'])."';";
				// guild rank update
				$r1 = "SELECT * FROM `roster_members` WHERE `name` = '".$char['name']."' AND `server` = '".$char['realm']."' AND `guild_id` = '".$idww['guild_id']."';";
				$r2 = $roster->db->query($r1);
				if ( $r2 )
				{
					$row = $roster->db->fetch($r2);
					if ( !empty($row['guild_rank']) )
					{
						if ( !in_array($row['guild_rank'], $mranks) )
						{
							$mranks = array_merge($mranks, array(''.$row['guild_rank'].''));
						}
					}
				}
			}
		}
		$rnk = min($mranks);
		if ($roster->auth->user['is_admin'] == 1)
		{
			$rnk = 0;
		}
		$update_sql[] = "UPDATE `" . $roster->db->table('user_members') . "`"
								  . " SET `access` = '" . $ranks[$rnk] . "', `bnet_token` = '".$response['access_token']."'"
								  . " WHERE `uid` = '".$roster->auth->user['id']."';";
		foreach( $update_sql as $sql )
		{
			$result = $roster->db->query($sql);
		}
		$jscript = "
			self.close();
			opener.location.href = '".makelink('user-user-chars')."';

		";
		roster_add_js($jscript, 'inline', 'header', false, false);
	}
}

if ($_GET['state'] == 'login2')
{
	$params = array('code' => $_GET['code'], 'auth_flow' => 'auth_code', 'redirect_uri' => $roster->api2->redirect_uri, 'state' => 'login2');
	d($params);
	$response = $roster->api2->getAccessToken($roster->api2->baseurl[$roster->api2->region]['TOKEN_ENDPOINT'], 'authorization_code', $params);
	d($response);
	$roster->api2->setAccessToken($response['access_token']);
	$res = $roster->api2->fetch('account');
	d($res);
	

	//bnetlogin($res)
	$re = $roster->auth->checkPass(md5($res['battletag'].'#'.$res['id']), $res['battletag'],'0');
	if ($re == true)
	{
		echo 'i have an account';
		updatecars($response);
		$jscript = '
		closepopup();
		';
		roster_add_js($jscript, 'inline', 'header', false, false);
		//exit();
	}
	else
	{
		$r = $roster->auth->bnetlogin($res,$response['access_token']);
		if ($r == true)
		{
			echo 'i dont have an account';
			updatecars($response);
			$jscript = '
			closepopup();
			';
			roster_add_js($jscript, 'inline', 'header', false, false);
			//exit();
		}
	}


}

function updatecars($response)
{
	global $roster, $addon;
	
	d($roster);
	
	$chars = $roster->api2->fetch('wowprofile');
	$ranks = _getranks();
	$orank = explode(':', $roster->auth->access);
	$mranks = $orank;

	$update_sql = array();
	if (is_array($chars['characters']))
	{
		$query1 = 'DELETE FROM `' . $roster->db->table('user_link', 'user') . '` WHERE `uid` = '.$roster->auth->user['id'].'';
		$result1 = $roster->db->query($query1);
		foreach ($chars['characters'] as $id => $char)
		{
			$idww = array();
			$idww = getcharid($char['name'],$char['realm']);
			$data = array(
				'uid'					=> $roster->auth->user['id'],
				'member_id'				=> (isset($idww['member_id']) ? $idww['member_id'] : '0'),
				'guild_id'				=> (isset($idww['guild_id']) ? $idww['guild_id'] : '0'),
				'group_id'				=> '',
				'is_main'				=> '0',
				'realm'					=> $char['realm'],
				'region'				=> $roster->api2->region,
				'name'					=> $char['name'],
				'battlegroup'			=> $char['battlegroup'],
				'class'					=> $char['class'],
				'race'					=> $char['race'],
				'gender'				=> $char['gender'],
				'level'					=> $char['level'],
				'achievementPoints'		=> $char['achievementPoints'],
				'thumbnail'				=> $char['thumbnail'],
				'guild'					=> (isset($char['guild']) ? $char['guild'] : ''),
				'guildRealm'			=> (isset($char['guildRealm']) ? $char['guildRealm'] : ''),
			);
			$query = 'INSERT INTO `' . $roster->db->table('user_link', 'user') . '` ' . $roster->db->build_query('INSERT', $data);
			$result = $roster->db->query($query);
			$update_sql[] = "UPDATE `" . $roster->db->table('members') . "`"
								  . " SET `account_id` = '" . $roster->auth->user['id'] . "'"
								  . " WHERE `name` = '".$roster->db->escape($char['name'])."' AND `server` = '".$roster->db->escape($char['realm'])."';";
			// guild rank update
			$r1 = "SELECT * FROM `roster_members` WHERE `name` = '".$char['name']."' AND `server` = '".$char['realm']."' AND `guild_id` = '".$idww['guild_id']."';";
			$r2 = $roster->db->query($r1);
			if ( $r2 && !empty($idww['guild_id']))
			{
				$row = $roster->db->fetch($r2);
				$mranks = array_merge($mranks, array(''.$row['guild_rank'].''));
				
			}
		}
		//d($mranks);
		$rnk = min($mranks);
		if ($roster->auth->user['is_admin'] == 1)
		{
			$rnk = 0;
		}
		$update_sql[] = "UPDATE `" . $roster->db->table('user_members') . "` SET `access` = '" . $ranks[$rnk] . "', `bnet_token` = '".$response['access_token']."' WHERE `id` = '".$roster->auth->user['id']."';";

		foreach( $update_sql as $sql )
		{
			$result = $roster->db->query($sql);
		}
		
	}
}

function _getranks()
{
	global $roster, $addon;
	
	$query = "SELECT * FROM `" . $roster->db->table('guild_rank') . "`;";
	$result = $roster->db->query($query);

	if (!$result)
	{
		die_quietly('Could not fetch menu configuration from database. MySQL said: <br />' . $roster->db->error(),'Roster',__FILE__,__LINE__,$query);
	}
	$ranks = array();
	while($r = $roster->db->fetch($result))
	{
		$ranks[$r['rank']] = $r['access'];
	}
	return $ranks;
}

function getcharid($name,$server)
{
	global $roster, $addon;
	$mid = array();
	$sql = 'SELECT `member_id`,`name`,`server`,`guild_id` FROM `' . $roster->db->table('members') . '` WHERE `name` = "' . $name . '" AND `server` = "'.$server.'"';
	$query = $roster->db->query($sql);
	while( $row = $roster->db->fetch($query) )
	{
		$mid = array(
		'member_id'	=> $row['member_id'],
		'name'		=> $row['name'],
		'server'	=> $row['server'],
		'guild_id'	=> $row['guild_id']
		);
	}
	return $mid;
}			