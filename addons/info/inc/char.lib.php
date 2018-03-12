<?php
/**
 * WoWRoster.net WoWRoster
 *
 * Character class
 *
 *
 * @copyright  2002-2011 WoWRoster.net
 * @license    http://www.gnu.org/licenses/gpl.html   Licensed under the GNU General Public License v3. * @package    CharacterInfo
 * @subpackage CharacterLib
 */

if( !defined('IN_ROSTER') )
{
	exit('Detected invalid access to this file!');
}

require_once (ROSTER_LIB . 'item.php');
require_once ($addon['inc_dir'] . 'bag.php');
require_once (ROSTER_LIB . 'quest.php');
require_once (ROSTER_LIB . 'recipes.php');

/**
 * Character Information Class
 * @package    CharacterInfo
 * @subpackage CharacterLib
 *
 */
class char
{
	var $data;
	var $equip = array( //);/*
		'Head'			=> array(),
		'Neck'			=> array(),
		'Shoulder'		=> array(),
		'Back'			=> array(),
		'Chest'			=> array(),
		'Shirt'			=> array(),
		'Tabard'		=> array(),
		'Wrist'			=> array(),
		'MainHand'		=> array(),
		'Ranged'		=> array(),
		
		'Hands'			=> array(),
		'Waist'			=> array(),
		'Legs'			=> array(),
		'Feet'			=> array(),
		'Finger'		=> array(),
		'Finger0'		=> array(),
		'Finger1'		=> array(),
		'Trinket'		=> array(),
		'Trinket0'		=> array(),
		'Trinket1'		=> array(),
		'OffHand'		=> array(),
		'SecondaryHand'	=> array(),
		'Relic'			=> array(),
	);//*/
	var $talent_build_url = array();
	var $locale;
	
	var $slotTYPE = array(
		'Ammo' => 0,
		'Head' => 1,
		'Neck' => 2,
		'Shoulder' => 3,
		'Shirt' => 4,
		'Chest' => 5,
		'Waist' => 6,
		'Belt' => 6,
		'Legs' => 7,
		'Feet' => 8,
		'Wrist' => 9,
		'Hands' => 10,
		'Finger0' => 11,
		'Finger1' => 12,
		'Trinket0' => 13,
		'Trinket1' => 14,
		'Back' => 15,
		'MainHand' => 16,
		'OffHand' => 17,
		'Ranged' => 18,
		'Tabard' => 19
	);
	var $slotID = array(
		'Head' => 1,
		'Neck' => 2,
		'Shoulder' => 3,
		'Back' => 16,
		'Chest' => 5,
		'Tabard' => 19,
		'Shirt' => 4,
		'Wrist' => 9,
		'Hands' => 10,
		'Waist' => 6,
		'Legs' => 7,
		'Feet' => 8,
		'Finger' => 11,
		'Finger0' => 11,
		'Finger1' => 12,
		'Trinket' => 13,
		'Trinket0' => 13,
		'Trinket1' => 14,
		'MainHand' => 21,
		'OffHand' => 22,
		'SecondaryHand' => 22,
		'Ranged' => 15,
		'Relic' => 28,
	);
	var $slotOrder = array(
		'Head'			=> 1,
		'Neck'			=> 2,
		'Shoulder'		=> 3,
		'Back'			=> 4,
		'Chest'			=> 5,
		'Shirt'			=> 6,
		'Tabard'		=> 7,
		'Wrist'			=> 8,
		'MainHand'		=> 9,
		'Ranged'		=> 9,
		
		'Hands'			=> 10,
		'Waist'			=> 11,
		'Legs'			=> 12,
		'Feet'			=> 13,
		'Finger'		=> 14,
		'Finger0'		=> 14,
		'Finger1'		=> 15,
		'Trinket'		=> 16,
		'Trinket0'		=> 16,
		'Trinket1'		=> 17,
		'OffHand'		=> 18,
		'SecondaryHand'	=> 18,
		'Relic'			=> 18,
	);
	var $eqside = array(
		'Head' => 'left',
		'Neck' => 'left',
		'Shoulder' => 'left',
		'Back' => 'left',
		'Chest' => 'left',
		'Shirt' => 'left',
		'Tabard' => 'left',
		'Wrist' => 'left',
		
		'MainHand' => 'left_bottom',
		'Ranged' => 'left_bottom',
		
		'Hands' => 'right',
		'Waist' => 'right',
		'Legs' => 'right',
		'Feet' => 'right',
		'Finger' => 'right',
		'Finger0' => 'right',
		'Finger1' => 'right',
		'Trinket' => 'right',
		'Trinket0' => 'right',
		'Trinket1' => 'right',
		
		'OffHand' => 'right_bottom',
		'SecondaryHand' => 'right_bottom',
		'Relic' => 'right_bottom',
	);

	/**
	 * Constructor
	 *
	 * @param array $data
	 * @return char
	 */
	function char( $data )
	{
		global $roster, $addon;

		if( empty($data) )
		{
			roster_die('The member is not in the database', $roster->locale->act['roster_error']);
		}

		$this->data = $data;
		$this->locale = $roster->locale->wordings[$this->data['clientLocale']];

		$ximg_url=null;
		if (file_exists($addon['dir'] .'chars/thumb-'. $this->data['member_id'].'.jpg') )
		{
			$ximg_url = $addon['url_path'] .'chars/thumb-'. $this->data['member_id'].'.jpg';
		}

		/**
		 * Assigning everything this file may need to the template
		 * The only tpl vars not here are ones that need to be generated in their respective methods
		 */

		$templates = '';
		$roster->tpl->assign_vars(array(
			'S_MAX_LEVEL' => ROSTER_MAXCHARLEVEL,

			'S_CHAR_IMG'	=> (!empty($this->data['background']) ? 'https://render-us.worldofwarcraft.com/character/'.$this->data['background'] : $addon['tpl_image_url'].'classbg/'.strtolower(str_replace(' ', '', $this->data['class'])).'.jpg' ),
			'S_CHAR_BG'	=> (!empty($this->data['profile_main']) ? 'https://render-us.worldofwarcraft.com/character/'.$this->data['profile_main'] : $addon['tpl_image_url'].'classbg/'.strtolower(str_replace(' ', '', $this->data['class'])).'.jpg' ),

			'S_PET_TAB'     => $roster->auth->getAuthorized($addon['config']['show_pets']),
			'S_COMPAN_TAB'  => $roster->auth->getAuthorized($addon['config']['show_companions']),
			'S_REP_TAB'     => true,//$roster->auth->getAuthorized($addon['config']['show_reputation']),
			'S_SKILL_TAB'   => true,//$roster->auth->getAuthorized($addon['config']['show_skills']),
			'S_TALENT_TAB'  => $roster->auth->getAuthorized($addon['config']['show_talents']),

			'CLASSID'		=> $this->data['classid'],
			'CLASS_EN'		=> strtolower( str_replace(' ', '', $this->data['class']) ),

			'L_CHAR_POWER'    => $this->data['power'],
			'L_CHAR_POWER_ID' => strtolower($this->data['power']),

			'HEALTH'        => $this->data['health'],
			'POWER'         => $this->data['mana'],
			'POWERTYPE'     => ucfirst($this->data['powerType']),
			'TALENT_POINTS' => $this->data['talent_points'],
			'ADD_TEMPLATES'	=> apply_filters('info_char_template', $templates, $this->data) //$templates
			)
		);
	}


	/**
	 * Gets a value from the character data
	 *
	 * @param string $field
	 * @return mixed
	 */
	function get( $field )
	{
		return $this->data[$field];
	}


	/**
	 * Gathers all of the characters equiped items into an array
	 * Array $this->equip
	 *
	 */
	function fetchEquip()
	{
		$this->equip = $this->fetchManyItems($this->data['member_id'], 'equip');
	}

	function fetchManyItems( $member_id, $parent)
	{
		global $roster;

		$parent = $roster->db->escape($parent);
		$items = array();

		$query  = " SELECT *"
				. " FROM `" . $roster->db->table('items') . "`"
				. " WHERE `member_id` = '$member_id'"
				. " AND `item_parent` = '$parent'";

		$result = $roster->db->query( $query );

		while( $data = $roster->db->fetch( $result ) )
		{
			$items[$data['item_slot']] = $data;
		}
		return $items;
	}
	

	function diff($value)
	{
		switch ($value)
		{
			case 5 :
				$difficultycolor = 'red'; //difficult
				break;

			case 4 :
				$difficultycolor = 'orange'; //optimal
				break;

			case 3 :
				$difficultycolor = 'yellow'; //medium
				break;

			case 2 :
				$difficultycolor = 'green'; //easy
				break;

			case 1 :
			default :
				$difficultycolor = 'grey'; //trivial
				break;
		}
		return $difficultycolor;
	}

	/**
	 * Build Companions
	 *
	 * @return bool
	 */
	function show_companions()
	{
		global $roster, $addon;

		$query = "SELECT * FROM `" . $roster->db->table('companions') . "`"
			. " WHERE `member_id` = '" . $this->data['member_id'] . "'"
			. " ORDER BY `type` ASC, `slot` ASC;";

		$result = $roster->db->query($query);

		$mount_num = $comp_num = 0;
		if( $roster->db->num_rows($result) > 0 )
		{
			while( $row = $roster->db->fetch($result, SQL_ASSOC) )
			{
				if( $row['icon'] == '' || !isset($row['icon']) )
				{
					$row['icon'] = 'inv_misc_questionmark';
				}

				if( $roster->auth->getAuthorized($addon['config']['show_mounts']) && $row['type'] == 'Mount' )
				{
					$roster->tpl->assign_block_vars('mounts',array(
						'ID'        => $row['slot'],
						'NAME'      => $row['name'],
						'ICON'      => $row['icon'],
						'TOOLTIP' => makeOverlib($row['tooltip']),
						)
					);
					$mount_num++;
				}

				if( $roster->auth->getAuthorized($addon['config']['show_companions']) && $row['type'] == 'Critter' )
				{
					$roster->tpl->assign_block_vars('companions',array(
						'ID'        => $row['slot'],
						'NAME'      => $row['name'],
						'ICON'      => $row['icon'],
						'TOOLTIP' => makeOverlib($row['tooltip']),
						)
					);
					$comp_num++;
				}
			}

			$roster->tpl->assign_vars(array(
				'S_MOUNTS'     => (bool)$mount_num,
				'S_COMPANIONS' => (bool)$comp_num,
				)
			);
			return true;
		}
		return false;
	}

	function show_talents( )
	{
		global $roster, $addon;

		$sqlquery = "SELECT * FROM `" . $roster->db->table('talenttree')."` WHERE `member_id` = '" . $this->data['member_id'] . "' ORDER BY `order` ASC;";

		$result = $roster->db->query($sqlquery);

		$spec = array(); 
		
		while( $t = $roster->db->fetch($result, SQL_ASSOC) )
		{
			$spec[$t['build']]=array();
			$spec[$t['build']]=$t;
		}

		$tree_rows = $roster->db->num_rows($result);
		
		$specs = $this->build_spec_data($this->data['classid']);// build the possable spec trees
		$talents = $this->build_talent_data($this->data['classid']);// build the list of talents
		
		// time to build some damn talents
		// Talent data and build spec data
		$talentdata = $specdata = array();

		// Temp var for talent spec detection
		$spec_points_temp = array();

		foreach($spec as $build => $build_data)
		{
			$spc = $build;

			$order = $build_data['order'];
			$treeindex = $build.'t';
			//echo $build_data['spec'].'<br>';
			$specdata[$build]['order'] = $order;
			$specdata[$build]['name'] = $build_data['tree'];
			$specdata[$build]['role'] = $specs[$build_data['tree']]['roles'];
			$specdata[$build]['desc'] = $specs[$build_data['tree']]['desc'];
			$specdata[$build]['icon'] = $specs[$build_data['tree']]['icon'];
			$specdata[$build]['image'] = $build_data['background'];

			$roster->tpl->assign_block_vars('spec', array(
				'TALENT_EXPORT' => sprintf($roster->locale->act['export_url2'], strtolower($roster->locale->act['id_to_class'][$this->data['classid']]), $build_data['build']),
				'ID'    => $build,
				'TID'	=> $treeindex,
				'NAME'  => $build_data['tree'],
				'DESC'  => $specdata[$build]['desc'],
				'ROLE'  => strtolower($specdata[$build]['role']),
				'TYPE'  => $roster->locale->act['talent_build_' . ($build_data['order'] == 1 ? 0 : 1)],
				'ICON'  => $specdata[$build]['icon'],
				'BGIMG'  => $specdata[$build]['image'],
				'SELECTED' => ($build_data['selected'] == 1 ? true : false)
				)
			);
			if ($build_data['selected'] == 1)
			{
				$roster->tpl->assign_vars(array(
					'SPEC' => $build_data['tree'],
					)
				);
			}
			
			$roster->tpl->assign_block_vars('spec.build', array(
				'L_POINTS_SPENT' => sprintf($roster->locale->act['pointsspent'], $build_data['pointsspent']),
				'NAME' => $specdata[$build]['name'],
				'ROLE'  => $specdata[$build]['role'],
				'ID'    => $build,
				'TID'	=> $treeindex,
				//'ICON' => $specs[$build_data['spec']]['background'],
				'HSELECT' => true,
				'SELECTED' => ($build_data['selected'] == 1 ? true : false)
				)
			);

			foreach( $talents[$build] as $row )
			{
				$roster->tpl->assign_block_vars('spec.build.talent', array(
					'NAME'		=> $row['name'],
					'ID'		=> $row['id'],
					'TOOLTIP'   => (isset($row['tooltip']) ? $row['tooltip'] : ''),
					'ICON'      => (isset($row['icon']) ? $row['icon'] : '')
				));
			}
		}
		return true;
	}
	
	function build_spec_data($class)
	{
		global $roster, $addon;
		
		$sql = "SELECT * FROM `" . $roster->db->table('talenttree_data') . "` WHERE `class_id` = '" . $class . "' ;";
		$results = $roster->db->query($sql);
		$talents = array();

		while( $row = $roster->db->fetch($results) )
		{
			$talents[$row['tree']]['name'] = $row['tree'];
			$talents[$row['tree']]['background'] = $row['background'];
			$talents[$row['tree']]['icon'] = strtolower($row['icon']);
			$talents[$row['tree']]['roles'] = $row['roles'];
			$talents[$row['tree']]['desc'] = $row['desc'];
			$talents[$row['tree']]['tree_num'] = $row['tree_num'];
		}

		return $talents;
	
	}
	function build_talent_data( $class )
	{
		global $roster, $addon;
		
		$sql = "SELECT * FROM `" . $roster->db->table('talents') . "`"
			. " WHERE `member_id` = '" . $this->data['member_id'] . "'"
			. " ORDER BY `row` ASC , `column` ASC;";

		$results = $roster->db->query($sql);

		$talents = array();

		while( $row = $roster->db->fetch($results) )
		{
			$talents[$row['build']][$row['row']]['name'] = $row['name'];
			$talents[$row['build']][$row['row']]['id'] = $row['talent_id'];
			$talents[$row['build']][$row['row']]['tooltip'] = makeOverlib($row['talent_id'], $row['name'], '', 2,'','','talent');
			$talents[$row['build']][$row['row']]['icon'] = $row['texture'];
		}

		return $talents;
	}
	
	/**
	 * Build a talent tree
	 *
	 * @param string $treename
	 * @return array
	 */
	function _talent_layer( $treename , $build )
	{
		global $roster;

		$sqlquery = "SELECT * FROM `" . $roster->db->table('talents') . "`"
			. " WHERE `member_id` = '" . $this->data['member_id'] . "'"
				. " AND `build` = '" . $build . "'"
				. " AND `tree` = '" . $treename . "'"
			. " ORDER BY `row` ASC , `column` ASC;";

		$result = $roster->db->query($sqlquery);

		$returndata = array();
		if( $roster->db->num_rows($result) > 0 )
		{
			// initialize the rows and cells
			for( $r = 1; $r < ROSTER_TALENT_ROWS + 1; $r++ )
			{
				for( $c = 1; $c < ROSTER_TALENT_COLS + 1; $c++ )
				{
					$returndata[$r][$c]['name'] = '';
				}
			}

			while( $talentdata = $roster->db->fetch($result, SQL_ASSOC) )
			{
				$r = $talentdata['row'];
				$c = $talentdata['column'];

				if( isset($this->talent_build_url[$build]) )
				{
					$this->talent_build_url[$build] .= $talentdata['rank'];
				}
				else
				{
					$this->talent_build_url[$build] = $talentdata['rank'];
				}

				$returndata[$r][$c]['name'] = $talentdata['name'];
				$returndata[$r][$c]['rank'] = $talentdata['rank'];
				$returndata[$r][$c]['maxrank'] = $talentdata['maxrank'];
				$returndata[$r][$c]['row'] = $r;
				$returndata[$r][$c]['column'] = $c;
				$returndata[$r][$c]['image'] = $talentdata['texture'] . '.' . $roster->config['img_suffix'];
				$returndata[$r][$c]['tooltip'] = makeOverlib($talentdata['tooltip'], '', '', 0, $this->data['clientLocale']);
			}
		}
		return $returndata;
	}


	/**
	 * Build character skills
	 *
	 * @return string
	 */
	function show_skills()
	{
		global $roster, $addon;

		$skillData = $this->_skill_tab_values();

		if( $skillData !== false && count($skillData) > 0 )
		{
			foreach( $skillData as $sindex => $skill )
			{
				$roster->tpl->assign_block_vars('skill',array(
					'ID'      => $sindex,
					'NAME'    => $skill['name'],
					'NAME_ID' => $roster->locale->act['skill_to_id'][$skill['name']]
					)
				);

				foreach( $skill['bars'] as $skillbar )
				{
					$roster->tpl->assign_block_vars('skill.bar',array(
						'NAME'     => $skillbar['name'],
						'WIDTH'    => $skillbar['barwidth'],
						'VALUE'    => $skillbar['value'],
						'MAXVALUE' => $skillbar['maxvalue'],
						'ICON'     => $roster->locale->act['ts_iconArray'][$skillbar['name']]
						)
					);

					if( $skill['name'] == $this->locale['professions'] )
					{
						$roster->tpl->assign_block_vars('professions',array(
							'NAME'     => $skillbar['name'],
							'WIDTH'    => $skillbar['barwidth'],
							'VALUE'    => $skillbar['value'],
							'MAXVALUE' => $skillbar['maxvalue'],
							'ICON'     => $roster->locale->act['ts_iconArray'][$skillbar['name']]
							)
						);
					}

				}
			}
			return true;
		}
		else
		{
			return false;
		}
	}


	/**
	 * Build a skill bars data
	 *
	 * @param array $skilldata
	 * @return array
	 */
	function _skill_bar_values( $skilldata )
	{
		list($level, $max) = explode( ':', $skilldata['skill_level'] );

		$returnData['maxvalue'] = $max;
		$returnData['value'] = $level;
		$returnData['name'] = $skilldata['skill_name'];
		if ($level != '0' && $max != '0')
		{
			$returnData['barwidth'] = ceil($level/$max*100);
		}
		else
		{
			$returnData['barwidth'] = 100;
		}

		return $returnData;
	}


	/**
	 * Build skill values
	 *
	 * @return mixed Array on success, false on fail
	 */
	function _skill_tab_values()
	{
		global $roster;

		$query = "SELECT * FROM `" . $roster->db->table('skills') . "`"
			. " WHERE `member_id` = '" . $this->data['member_id'] . "'"
			. " ORDER BY `skill_type`, `skill_order` ASC, `skill_name` ASC;";

		$result = $roster->db->query( $query );

		$skill_rows = $roster->db->num_rows($result);

		$i=0;
		$j=0;
		if ( $skill_rows > 0 )
		{
			$skillInfo = array();

			$data = $roster->db->fetch($result,SQL_ASSOC);
			$skillInfo[$i]['name'] = $data['skill_type'];

			for( $r=0; $r < $skill_rows; $r++ )
			{
				if( $skillInfo[$i]['name'] != $data['skill_type'] )
				{
					$i++;
					$j=0;
					$skillInfo[$i]['name'] = $data['skill_type'];
				}
				$skillInfo[$i]['bars'][$j] = $this->_skill_bar_values($data);
				$j++;
				$data = $roster->db->fetch($result,SQL_ASSOC);
			}
			return $skillInfo;
		}
		else
		{
			return false;
		}
	}

	/**
	*
	*	rep 2 functions fixing this bitch
	*
	*/
	function show_reputation()
	{
		global $roster, $addon;

		$repData = $this->_rep_array();
		
		foreach ($repData as $exp => $f)
		{
			$roster->tpl->assign_block_vars('expantion',array(
				'ID'			=> $exp,
				'NAME'			=> $exp,
				'NAME_ID'		=> $this->locale['faction_to_id'][$exp]
			));
			foreach ($f as $faction => $val)
			{
				if ( !isset($val['sub']) )
				{
					$level = $val['value'];
					$max = $val['max'];
					$standing = $this->_standing_to_image($val['standing']);
					if ($level == 0 && $max == 0)
					{
						$level = 2;
						$max = 2;
					}
		
					$width = ceil($level / $max * 100);
					$fill = $this->_standing_to_image($val['standing']);
					$roster->tpl->assign_block_vars('expantion.faction',array(
						'NAME'			=> $faction,
						'WIDTH'			=> $width,
						'FILL'      	=> $fill,
						'STANDING'		=> $this->_standing_to_local($val['standing']),
						'VALUE'			=> $level,
						'MAXVALUE'		=> $max
						)
					);
				}
				else
				{
					if ( !isset($val['standing']) )
					{
						$roster->tpl->assign_block_vars('expantion.group',array(
							'NAME'			=> $faction,
							'ISBAR'			=> false,
							)
						);
					}
					else
					{
						$level = $val['value'];
						$max = $val['max'];
						$standing = $this->_standing_to_image($val['standing']);
						if ($level == 0 && $max == 0)
						{
							$level = 2;
							$max = 2;
						}
			
						$width = ceil($level / $max * 100);
						$fill = $this->_standing_to_image($val['standing']);
						$roster->tpl->assign_block_vars('expantion.group',array(
							'NAME'			=> $faction,
							'WIDTH'			=> $width,
							'FILL'      	=> $fill,
							'STANDING'		=> $this->_standing_to_local($val['standing']),
							'VALUE'			=> $level,
							'MAXVALUE'		=> $max,
							'ISBAR'			=> true,
							)
						);
					}
					if ( isset($val['sub']) && count($val['sub']) > 0)
					{
						foreach($val['sub'] as $sub => $s)
						{
							$level = $s['value'];
							$max = $s['max'];
							$standing = $this->_standing_to_image($s['standing']);
							if ($level == 0 && $max == 0)
							{
								$level = 2;
								$max = 2;
							}
				
							$width = ceil($level / $max * 100);
							$fill = $this->_standing_to_image($s['standing']);
							$roster->tpl->assign_block_vars('expantion.group.sub',array(
								'NAME'			=> $sub,
								'WIDTH'			=> $width,
								'FILL'      	=> $fill,
								'STANDING'		=> $this->_standing_to_local($s['standing']),
								'VALUE'			=> $level,
								'MAXVALUE'		=> $max,
								'ISBAR'			=> false,
								)
							);
						}
					}
				}
			}
		}
		return true;
		
		
	}

	function _rep_array()
	{
		global $roster;
		$rid = json_decode('{"1740":"Aeda Brightdawn","2099":"Akule Riverhorn","1037":"Alliance Vanguard","1515":"Arakkoa Outcasts","1862":"Arcane Thirst (Oculeth)","1861":"Arcane Thirst (Silgryn) DEPRECATED","1860":"Arcane Thirst (Thalyssra)","1919":"Arcane Thirst (Valtrois)","1106":"Argent Crusade","529":"Argent Dawn","2045":"Armies of Legionfall","2091":"Armies of Legionfall (Paragon)","2062":"Arne Test - Paragon Reputation","2063":"Arne Test - Paragon Reputation Stormwind","1012":"Ashtongue Deathsworn","1204":"Avengers of Hyjal","1177":"Baradin\'s Wardens","1735":"Barracks Bodyguards","1133":"Bilgewater Cartel","2011":"Bizmo\'s Brawlpub","1419":"Bizmo\'s Brawlpub (Season 1)","1691":"Bizmo\'s Brawlpub (Season 2)","87":"Bloodsail Buccaneers","21":"Booty Bay","2010":"Brawl\'gar Arena","1374":"Brawl\'gar Arena (Season 1)","1690":"Brawl\'gar Arena (Season 2)","910":"Brood of Nozdormu","609":"Cenarion Circle","942":"Cenarion Expedition","1277":"Chee Chee","1975":"Conjurer Margoss","2100":"Corbyn","1731":"Council of Exarchs","1900":"Court of Farondis","2087":"Court of Farondis (Paragon)","909":"Darkmoon Faire","1440":"Darkspear Rebellion","530":"Darkspear Trolls","69":"Darnassus","1738":"Defender Illona","1733":"Delvar Ironfist","1375":"Dominance Offensive","1172":"Dragonmaw Clan","1883":"Dreamweavers","2088":"Dreamweavers (Paragon)","1275":"Ella","1492":"Emperor Shaohao","577":"Everlook","930":"Exodar","1068":"Explorers\' League","1283":"Farmer Fung","1282":"Fish Fellreed","1228":"Forest Hozen","1104":"Frenzyheart Tribe","729":"Frostwolf Clan","1445":"Frostwolf Orcs","369":"Gadgetzan","92":"Gelkis Clan Centaur","1815":"Gilnean Survivors","1134":"Gilneas","1281":"Gina Mudclaw","54":"Gnomeregan","1269":"Golden Lotus","1158":"Guardians of Hyjal","1168":"Guild","1847":"Hand of the Prophet","1279":"Haohan Mudclaw","1178":"Hellscream\'s Reach","1828":"Highmountain Tribe","2085":"Highmountain Tribe (Paragon)","946":"Honor Hold","1052":"Horde Expedition","1352":"Huojin Pandaren","749":"Hydraxian Waterlords","1947":"Illidari","2097":"Ilyssia of the Waters","2102":"Impus","47":"Ironforge","1888":"Jandvik Vrykul","1273":"Jogu the Drunk","2098":"Keeper Raynae","989":"Keepers of Time","1090":"Kirin Tor","1387":"Kirin Tor Offensive","1098":"Knights of the Ebon Blade","978":"Kurenai","1708":"Laughing Skull Orcs","1741":"Leorajh","1011":"Lower City","93":"Magram Clan Centaur","1989":"Moon Guard","1899":"Moonguard","1358":"Nat Pagle","1015":"Netherwing","1357":"Nomi","1038":"Ogri\'la","1276":"Old Hillpaw","1376":"Operation: Shieldwall","1849":"Order of the Awakened","1271":"Order of the Cloud Serpent","76":"Orgrimmar","1242":"Pearlfin Jinyu","1173":"Ramkahen","470":"Ratchet","349":"Ravenholdt","2101":"Sha\'leth","1710":"Sha\'tari Defense","1031":"Sha\'tari Skyguard","1270":"Shado-Pan","1435":"Shado-Pan Assault","1520":"Shadowmoon Exiles","1216":"Shang Xi\'s Academy","1077":"Shattered Sun Offensive","809":"Shen\'dralar","1278":"Sho","911":"Silvermoon City","890":"Silverwing Sentinels","970":"Sporeggar","1732":"Steamwheedle Draenor Expedition","1711":"Steamwheedle Preservation Society","730":"Stormpike Guard","72":"Stormwind","1388":"Sunreaver Onslaught","70":"Syndicate","2018":"Talon\'s Vengeance","1737":"Talonpriest Ishaal","932":"The Aldor","1302":"The Anglers","1156":"The Ashen Verdict","1341":"The August Celestials","1359":"The Black Prince","1351":"The Brewmasters","933":"The Consortium","510":"The Defilers","1135":"The Earthen Ring","1984":"The First Responders","1126":"The Frostborn","1067":"The Hand of Vengeance","1073":"The Kalu\'ak","1337":"The Klaxxi","509":"The League of Arathor","1345":"The Lorewalkers","941":"The Mag\'har","1859":"The Nightfallen","2089":"The Nightfallen (Paragon)","1105":"The Oracles","1850":"The Saberstalkers","990":"The Scale of the Sands","934":"The Scryers","935":"The Sha\'tar","1094":"The Silver Covenant","1119":"The Sons of Hodir","1124":"The Sunreavers","1064":"The Taunka","1272":"The Tillers","967":"The Violet Eye","1894":"The Wardens","2090":"The Wardens (Paragon)","1091":"The Wyrmrest Accord","1171":"Therazane","59":"Thorium Brotherhood","947":"Thrallmar","81":"Thunder Bluff","576":"Timbermaw Hold","1280":"Tina Mudclaw","1736":"Tormmok","922":"Tranquillien","1353":"Tushui Pandaren","68":"Undercity","1948":"Valarjar","2086":"Valarjar (Paragon)","1050":"Valiance Expedition","1739":"Vivianne","1848":"Vol\'jin\'s Headhunters","1681":"Vol\'jin\'s Spear","1085":"Warsong Offensive","889":"Warsong Outriders","1174":"Wildhammer Clan","589":"Wintersaber Trainers","1682":"Wrynn\'s Vanguard","270":"Zandalar Tribe","1117":"Sholazar Basin","936":"Shattrath City","169":"Steamwheedle Cartel","1118":"Classic","1162":"Cataclysm","1169":"Guild","1834":"Legion","1245":"Mists of Pandaria","0":"Other","980":"The Burning Crusade","1444":"Warlords of Draenor","1097":"Wrath of the Lich King","891":"Alliance Forces","892":"Horde Forces","469":"Alliance","67":"Horde"}',true);
		$query= "SELECT * FROM `".$roster->db->table('reputation')."` WHERE `member_id` = '".$this->data['member_id']."' ORDER BY `expansion` DESC";
		$result = $roster->db->query( $query );

		$exp = array(
			0 => 'Classic',
			1 => 'The Burning Crusade',
			2 => 'Wrath of the Lich King',
			3 => 'Cataclism',
			4 => 'Mists of Pandaria',
			5 => 'Warlords of Draenor',
			6 => 'Legion',
		);
		$exp_en = array(
			0 => 'Classic',
			1 => 'The Burning Crusade',
			2 => 'Wrath of the Lich King',
			3 => 'Cataclism',
			4 => 'Mists of Pandaria',
			5 => 'Warlords of Draenor',
			6 => 'Legion',
		);

		$rep_rows = $roster->db->num_rows($result);

		if ( $rep_rows > 0 )
		{
			$repInfo = array();
			
			while($data = $roster->db->fetch($result))
			{
				$repInfo[$data['id']] = $data;
			}
			
			$repTree = array();
			foreach ($repInfo as $i => $f)
			{
				if ($f['category'] == 0)
				{
					if ($f['category2'] != 0)
					{
						$repTree[$rid[$f['category2']]][$f['name']]=$f;
					}
					else if ( isset($f['expansion']) )
					{
						$repTree[$exp[$f['expansion']]][$f['name']]=$f;
					}
					if ($f['category2'] == 0)
					{
						$repTree[$exp['0']][$f['name']]=$f;
					}
				}
			}
			foreach ($repInfo as $i => $f)
			{	
				if ($f['category'] != 0)
				{
					if ($f['category2'] != 0)
					{
						$a = $rid[$f['category2']];
						$b = $rid[$f['category']];
						$repTree[$a][$b]['sub'][$f['name']] = $f;
					}
				}
			}
			//+d($repTree);
		}
		return $repTree;
	}
	/**
	 * Build character reputation
	 *
	 * @return mixed Array on success, false on fail
	 */
	function show_reputation2()
	{
		global $roster, $addon;

		$repData = $this->_rep_tab_values();

		//+d($repData);
		if( is_array($repData) )
		{
			foreach( $repData as $findex => $faction )
			{
				$roster->tpl->assign_block_vars('rep',array(
					'ID'      => $findex,
					'NAME'    => $findex,
					'NAME_ID' => $this->locale['faction_to_id'][$findex]
					)
				);

				foreach( $faction as $rep => $bar )
				{
					if ($rep !='')
					{
						if (isset($bar['value']) && !$bar['sub'])
						{
							$roster->tpl->assign_block_vars('rep.bar',array(
								'ID'			=> $bar['barid'],
								'NAME'			=> $rep,
								'WIDTH'			=> $bar['barwidth'],
								'IMAGE'			=> $bar['image'],
								'FILL'      	=> $bar['fill'],
								'STANDING'		=> $bar['standing'],
								'DESCRIPTION'	=> $bar['description'],
								'TOOLTIP'		=> makeOverlib($bar['description'], $rep, '' , 2, '', ', WIDTH, 325'),
								'VALUE'			=> $bar['value'],
								'MAXVALUE'		=> $bar['maxvalue'],
								'ATWAR'			=> $bar['atwar'],
								'SUB'			=> $bar['sub'],
								)
							);
						}
						else
						{
							$roster->tpl->assign_block_vars('rep.rep2',array(
								'ID'      		=> $rep,
								'NAME'    		=> $rep,
								'NAME_ID' 		=> $this->locale['faction_to_id'][$rep],
								
								'WIDTH'			=> '',
								'STANDING'		=> '',
								'VALUE'			=> '',
								'MAXVALUE'		=> '',
								'ATWAR'			=> '',
								)
							);
						}
						if (isset($bar['sub']))
						{
							foreach($bar as $fact => $sta)
							{
								if ($fact != 'sub' && $fact != 'barwidth' && $fact != 'image' && $fact != 'fill' && $fact != 'barid' && $fact != 'standing' && $fact != 'description' && $fact != 'value' && $fact != 'maxvalue' && $fact != 'atwar')
								{
									$roster->tpl->assign_block_vars('rep.rep2.bar2',array(
										'ID'          => $sta['barid'],
										'NAME'        => $fact,
										'WIDTH'       => $sta['barwidth'],
										'IMAGE'       => $sta['image'],
										'FILL'       => $sta['fill'],
										'STANDING'    => $sta['standing'],
										'DESCRIPTION' => $sta['description'],
										'TOOLTIP'     => makeOverlib($sta['description'], $fact, '' , 2, '', ', WIDTH, 325'),
										'VALUE'       => $sta['value'],
										'MAXVALUE'    => $sta['maxvalue'],
										'ATWAR'       => $sta['atwar']
										)
									);
								}
							}
						}
					}
				}
			}
			return true;
		}
		else
		{
			return false;
		}
	}


	/**
	 * Build reputation bars data
	 *
	 * @return array
	 */
	function _rep_tab_values()
	{
		global $roster;

		$query= "SELECT * FROM `".$roster->db->table('reputation')."` WHERE `member_id` = '".$this->data['member_id']."' ORDER BY `sort` DESC, `faction` ASC, `parent` ASC, `name` ASC;";
		$result = $roster->db->query( $query );

		$rep_rows = $roster->db->num_rows($result);

		$i=0;
		$j=0;
		$k=0;
		$l=0;
		$p = '';

		if ( $rep_rows > 0 )
		{
			$repInfo = array();

			$factions='';
			$sub_faction='';
			$name='';

			while($data = $roster->db->fetch($result,SQL_ASSOC))
			{
				if( $data['name'] != $data['parent'] && $data['parent']=='' && $data['name']!='')
				{
					$i++;
					$j=0;
					$l++;
					$factions = $data['faction'];
					$repInfo[$factions][$data['name']] = $i++;
					$repInfo[$factions][$data['name']] = $this->_rep_bar_values($data);
					$repInfo[$factions][$data['name']]['sub'] = false;

				}

				if (isset($data['parent']) && $data['curr_rep'] != '' && $data['max_rep'] != '' && $data['name']!='')//&& $data['parent']!= $data['name'])
				{
					$p=$data['name'];
					$repInfo[$factions][$data['parent']][$data['name']] = $this->_rep_bar_values($data);
					$repInfo[$factions][$data['parent']]['sub'] = true;
					$k++;
				}
				else
				{
					$p='';
				}

				$j++;
			}

			return $repInfo;
		}
		else
		{
			return false;
		}
	}


	/**
	 * Build reputation values
	 *
	 * @param array $repdata
	 * @return array
	 */
	function _rep_bar_values( $repdata )
	{
		static $repnum = 0;

		global $roster, $addon;

		$level = $repdata['curr_rep'];
		$max = $repdata['max_rep'];

		$rank = $this->_rep_to_rank($repdata['Standing']);
		
		$img = array(
			'7' => 'exalted',
			'6' => 'revered',
			'5' => 'honored',
			'4' => 'friendly',
			'3' => 'neutral',
			'2' => 'unfriendly',
			'1' => 'hostile',
			'0' => 'hated'
		);
		$bimg = array(
			'7' => 'rank-7',// .faction-fill { background-position: 0 -88px; }
			'6' => 'rank-6',// .faction-fill { background-position: 0 -66px; }
			'5' => 'rank-5',// .faction-fill { background-position: 0 -66px; }
			'4' => 'rank-4',// .faction-fill { background-position: 0 -66px; }
			'3' => 'rank-3',// .faction-fill { background-position: 0 -44px; }
			'2' => 'rank-2',// .faction-fill { background-position: 0 -22px; }
			'1' => 'rank-1',// .faction-fill { background-position: 0 -22px; }
			'0' => 'rank-0',// .faction-fill { background-position: 0 -22px; }
		);

		if ($level == 0 && $max == 0)
		{
			$level = 2;
			$max = 2;
			$repdata['Standing'] = $this->locale['exalted'];
		}
		//d($repdata);
		$returnData['name'] = $repdata['name'];
		$returnData['barwidth'] = ceil($level / $max * 100);
		$returnData['image'] = $img[$repdata['standing_id']];
		$returnData['fill'] = $bimg[$repdata['standing_id']];
		$returnData['barid'] = $repnum;
		$returnData['standing'] = $repdata['Standing'];
		$returnData['description'] = $repdata['Description'];
		$returnData['value'] = $level;
		$returnData['maxvalue'] = $max;
		$returnData['atwar'] = $repdata['AtWar'];

		$repnum++;

		return $returnData;
	}

	function _standing_to_local($standing)
	{
		global $roster, $addon;
		
		return $roster->locale->act['standing_to_local'][$standing];
	}
	
	function _standing_to_image($standing)
	{
		$bimg = array(
			'7' => 'rank-7',// .faction-fill { background-position: 0 -88px; }
			'6' => 'rank-6',// .faction-fill { background-position: 0 -66px; }
			'5' => 'rank-5',// .faction-fill { background-position: 0 -66px; }
			'4' => 'rank-4',// .faction-fill { background-position: 0 -66px; }
			'3' => 'rank-3',// .faction-fill { background-position: 0 -44px; }
			'2' => 'rank-2',// .faction-fill { background-position: 0 -22px; }
			'1' => 'rank-1',// .faction-fill { background-position: 0 -22px; }
			'0' => 'rank-0',// .faction-fill { background-position: 0 -22px; }
		);
		return $bimg[$standing];
	}
	function _rep_to_rank($standing)
	{
		global $roster, $addon;
		
		switch ($standing) {
			default: $ret = "0";
			break;
			
			case $this->locale['exalted']:
				$ret = 7;
			break;
			
			case $this->locale['revered']:
				$ret = 6;
			break;
			
			case $this->locale['honored']:
				$ret = 5;
			break;
			
			case $this->locale['friendly']:
				$ret = 4;
			break;
			
			case $this->locale['neutral']:
				$ret = 3;
			break;
			
			case $this->locale['unfriendly']:
				$ret = 2;
			break;
			
			case $this->locale['hostile']:
				$ret = 1;
			break;
			
			case $this->locale['hated']:
				$ret = 0;
			break;

		}
		return $ret;
	}

	/**
	 * Build a equiped item slot
	 *
	 * @param string $slot
	 * @return string
	 */
	function equip_slot( $slot )
	{
		global $roster;

		
		$r = array('8', '11', '12', '10', '7', '21', '13', '14', '6');
		if( isset($this->equip[$slot]) )
		{
			$idata = $this->equip[$slot];
			$jdata = '';
			if (isset($this->equip[$slot]['json']))
			{
				$jdata = json_decode($this->equip[$slot]['json'],true);
			}
			/*
				new equipment wrapper
			*/
			$e = array(
				'SLOT'		=> $this->slotID[$idata['item_slot']],
				'SLOTID'    => $this->slotID[$idata['item_slot']],
				'SLOTTYPE'  => $this->slotID[$idata['item_slot']],
				'NAME'		=> $idata['item_name'],
				'ILVL'		=> $idata['item_level'],
				'ITEM_ID'	=> $idata['item_id'],
				'QUALITY2'	=> $idata['item_rarity'],
				'RARITY'	=> $this->_getItemQuality($idata['item_rarity']),
				'EMPTY'		=> false,
				'ICON'     => $roster->config['interface_url'] . 'Interface/Icons/' . $idata['item_texture'] . '.' . $roster->config['img_suffix'],
				'TOOLTIP'	=> 'data-tooltip="item-'.$idata['item_id'].'|'.$roster->data['member_id'].'"',
				'QUALITY'  => $idata['item_rarity'],
			);
			$roster->tpl->assign_block_vars('equip.'.$this->eqside[$slot], $e );
			if (isset($jdata['gems']))
			{
				if (count($jdata['gems']) > 0)
				{
					foreach($jdata['gems'] as $i => $g)
					{
						$roster->tpl->assign_block_vars('equip.'.$this->eqside[$slot].'.gems', array(
							'ICON' => $roster->config['interface_url'] . 'Interface/Icons/' . $g['icon'] . '.' . $roster->config['img_suffix']
						));
					}
				}
			}
			$b = array(
			//new stuff 
				'SLOT'		=> $this->slotID[$idata['item_slot']],
				'SLOTID'    => $this->slotID[$idata['item_slot']],
				'SLOTTYPE'  => $this->slotID[$idata['item_slot']],
				'NAME'		=> $idata['item_name'],
				'ILVL'		=> $idata['item_level'],
				'RIGHT'		=> (in_array($this->slotID[$idata ['item_slot']], $r) ? true : false),
				'ITEM_ID'	=> $idata['item_id'],
				'QUALITY2'	=> $idata['item_rarity'],
				'EMPTY'		=> false,
				'ICON'     => $roster->config['interface_url'] . 'Interface/Icons/' . $idata['item_texture'] . '.' . $roster->config['img_suffix'],//$this->equip[$slot]->tpl_get_icon(),
				'TOOLTIP'	=> 'data-tooltip="item-'.$idata['item_id'].'|'.$roster->data['member_id'].'"',
				'QUALITY'  => $idata['item_rarity'],
				);
			$roster->tpl->assign_block_vars('equipment', $b );
		}
		else
		{
			$e = array(
				//new stuff 
				'SLOT'		=> $this->slotID[$slot],
				'SLOTID'    => $this->slotID[$slot],
				'SLOTTYPE'  => $this->slotID[$slot],
				'NAME'		=> '',
				'ILVL'		=> '',
				'RARITY'	=> $slot,
				'ITEM_ID'	=> $slot,
				'QUALITY2'	=> '',
				'EMPTY'		=> true,
				'ICON'     => '',
				'TOOLTIP'  => '',
				'ITEMLINK' => '',
				'QUALITY'  => 'none',
				'QTY'      => 0,
				'S_AMMO'   => $slot == 'Ammo'
			);
			$roster->tpl->assign_block_vars('equip.'.$this->eqside[$slot], $e );
			$roster->tpl->assign_block_vars('equipment',array(
				//new stuff 
				'SLOT'		=> $this->slotID[$slot],
				'SLOTID'    => $this->slotID[$slot],
				'SLOTTYPE'  => $this->slotID[$slot],
				'NAME'		=> '',
				'ILVL'		=> '',
				'RIGHT'		=> (in_array($this->slotID[$slot], $r) ? true : false),
				'ITEM_ID'	=> $slot,
				'QUALITY2'	=> '',
				'EMPTY'		=> true,
				'ICON'     => $roster->config['img_url'] . 'pixel.gif',
				'TOOLTIP'  => '',
				'ITEMLINK' => '',
				'QUALITY'  => 'none',
				'QTY'      => 0,
				'S_AMMO'   => $slot == 'Ammo'
				)
			);
		}
	}

	function _getItemQuality($value)
	{
		$ret = '';
		switch ($value) {
			default: $ret = "POOR"; //Grey
				break;
			case 0: $ret = "POOR"; /* poor (gray) */
			break;
			case 1: $ret = "COMMON"; /* common (white) */
			break;
			case 2: $ret = "UNCOMMON"; /* uncommon (green) */
			break;
			case 3: $ret = "RARE"; /* #0070dd rare (blue) */
			break;
			case 4: $ret = "EPIC"; /* #a335ee epic (purple) */
			break;
			case 5: $ret = "LEGENDARY"; /* lengendary (orange) */
			break;
			case 6: $ret = "ARTIFACT"; /* artifact (gold) */
			break;
			case 7: $ret = "HEIRLOOM"; /* heirloom (lt blue) */
			break;

		}
		return $ret;
	}
	/*
				NEW stat block!!!!!
	*/
	var $dd = '{"increases": {"attackPower": "Increases Attack Power by %1$s.","critChance": "Increases Crit chance by %1$s&#37;.","spellCritChance": "Increases Spell Crit chance by %1$s&#37;.","spellPower": "Increases Spell Power by %1$s.","health": "Increases Health by %1$s.","mana": "Increases Mana by %1$s.","manaRegen": "Increases mana regeneration while in combat by %1$s per 5 Seconds.","meleeDps": "Increases damage with melee weapons by %1$s damage per second.","rangedDps": "Increases damage with ranged weapons by %1$s damage per second.","petArmor": "Increases your pet\'s Armor by %1$s.","petAttackPower": "Increases your pet\'s Attack Power by %1$s.","petSpellDamage": "Increases your pet\'s Spell Damage by %1$s.","petAttackPowerSpellDamage": "Increases your pet\'s Attack Power by %1$s and Spell Damage by %2$s."},"decreases": {"damageTaken": "Reduces Physical Damage taken by %1$s&#37;.","enemyRes": "Reduces enemy resistances by %1$s.","dodgeParry": "Reduces chance to be dodged or parried by %1$s&#37;."},"noBenefits": "Provides no benefit for your class.","beforeReturns": "(Before diminishing returns)","damage": {"speed": "Attack speed (seconds):","damage": "Damage:","dps": "Damage per second:"},"averageItemLevel": {"title": "Item Level","description": "The average item level of your best equipment. Increasing this will allow you to enter more difficult dungeons using Dungeon Finder."},"health": {"title": "Health","description": "Your maximum amount of health. If your health reaches zero, you will die."},"mana": {"title": "Mana","description": "Your maximum mana. Mana allows you to cast spells."},"rage": {"title": "Rage","description": "Your maximum rage. Rage is consumed when using abilities and is restored by attacking enemies or being damaged in combat."},"focus": {"title": "Focus","description": "Your maximum focus. Focus is consumed when using abilities and is restored automatically over time."},"energy": {"title": "Energy","description": "Your maximum energy. Energy is consumed when using abilities and is restored automatically over time."},"runic": {"title": "Runic","description": "Your maximum Runic Power."},"strength": {"title": "Strength"},"agility": {"title": "Agility"},"stamina": {"title": "Stamina"},"intellect": {"title": "Intellect"},"spirit": {"title": "Spirit"},"mastery": {"title": "Mastery","description": "Mastery %1$s (+%2$s&#37; mastery)","unknown": "You must learn Mastery from your trainer before this will have an effect.","unspecced": "You must select a talent specialization in order to activate Mastery."},"crit": {"title": "Critical Strike","description": "Chance for extra effectiveness on attack and heals.","description2": "Critical Strike: %1$s [%2$s&#37;]"},"haste": {"title": "Haste","description": "Increases attack speed and spell casting speed.","description2": "Haste: %1$s [%2$s&#37;]"},"meleeDps": {"title": "Damage per Second"},"meleeAttackPower": {"title": "Attack Power"},"meleeSpeedm": {"title": "Attack Speed"},"meleeSpeedmo": {"title": "Attack Speed"},"meleeHaste": {"title": "Melee Haste","description": "Haste rating of %1$s adds %2$s&#37; Haste.","description2": "Increases melee attack speed."},"meleeHit": {"title": "Melee Hit Chance","description": "Hit rating of %1$s adds %2$s&#37; Hit chance."},"meleeCrit": {"title": "Melee Crit Chance","description": "Crit rating of %1$s adds %2$s&#37; Crit chance.","description2": "Chance of melee attacks doing extra damage."},"expertise": {"title": "Expertise","description": "Expertise rating of %1$s adds %2$s Expertise."},"rangedDps": {"title": "Damage per Second"},"rangedAttackPower": {"title": "Ranged Attack Power"},"rangedSpeed": {"title": "Ranged Attack Speed"},"rangedHaste": {"title": "Ranged Haste","description": "Haste rating of %1$s adds %2$s&#37; Haste.","description2": "Increases ranged attack speed."},"rangedHit": {"title": "Ranged Hit Chance","description": "Hit rating of %1$s adds %2$s&#37; Hit chance."},"rangedCrit": {"title": "Ranged Crit Chance","description": "Crit rating of %1$s adds %2$s&#37; Crit chance.","description2": "Chance of ranged attacks doing extra damage."},"spellPower": {"title": "Spell Power","description": "Increases the damage and healing of spells."},"spellHaste": {"title": "Spell Haste","description": "Haste rating of %1$s adds %2$s&#37; Haste.","description2": "Increases spell casting speed."},"spellHit": {"title": "Spell Hit Chance","description": "Hit rating of %1$s adds %2$s&#37; Hit chance."},"spellCrit": {"title": "Spell Crit Chance","description": "Crit rating of %1$s adds %2$s&#37; Crit chance.","description2": "Chance of spells doing extra damage or healing."},"mana5": {"title": "Mana Regen","description": "%1$s mana regenerated every 5 seconds while not in combat."},"mana5Combat": {"title": "Combat Regen","description": "%1$s mana regenerated every 5 seconds while in combat."},"armor": {"title": "Armor"},"dodge": {"title": "Dodge Chance","description": "Dodge rating of %1$s adds %2$s&#37; Dodge chance."},"parry": {"title": "Parry Chance","description": "Parry rating of %1$s adds %2$s&#37; Parry chance."},"block": {"title": "Block Chance","description": "Block rating of %1$s adds %2$s&#37; Block chance.","description2": "Your block stops %1$s&#37; of incoming damage."},"resilience": {"title": "PvP Resilience","description": "Provides damage reduction against all damage done by players and their pets or minions.","description2": "Resilience %1$s (+%2$s&#37; Resilience)"},"pvppower": {"title": "PvP Power","description": "Increases damage done to players and their pets or minions, and increases healing done in PvP zones and outdoors.","description2": "Power %1$s","description3": "+%1$s&#37; Healing","description4": "+%1$s&#37; Damage"},"arcaneRes": {"title": "Arcane Resistance","description": "Reduces Arcane damage taken by an average of %1$s&#37;."},"fireRes": {"title": "Fire Resistance","description": "Reduces Fire damage taken by an average of %1$s&#37;."},"frostRes": {"title": "Frost Resistance","description": "Reduces Frost damage taken by an average of %1$s&#37;."},"natureRes": {"title": "Nature Resistance","description": "Reduces Nature damage taken by an average of %1$s&#37;."},"shadowRes": {"title": "Shadow Resistance","description": "Reduces Shadow damage taken by an average of %1$s&#37;."},"bonusArmor": {"title": "Bonus Armor","description": "Total Armor Physical Damage Reduction: %1$s&#37;","description2": "Increases Attack Power by %1$s."},"multistrike": {"title": "Multistrike","description": "Grants two %1$s&#37; chances to deliver extra attacks or heals for %2$s&#37; of normal value.","description2": "Multistrike: %1$s %2$s&#37;"},"leech": {"title": "Leech","description": "Returns a portion of your damage and healing as healing to you.","description2": "Leech: %1$s +%2$s&#37;"},"versatility": {"title": "Versatility","description": "Increases damage and healing done by %1$s&#37; and decreases damage taken by %2$s&#37;.","description2": "Versatility: %1$s %2$s&#37;/%3$s&#37;"},"avoidance": {"title": "Avoidance","description": "Reduces damage taken from area of effect attacks.","description2": "Avoidance: %1$s&#37; +%2$s&#37;"},"attack": {"title": "Attack","description": "Reduces damage taken from area of effect attacks."}}';	
	var $grouping = array(
		'attributes' => array (),
		'attack' => array (),
		'spell' => array (),
		'defense' => array (),
		'enhancements' => array (),
	);
	
	function xhealth ( $s, $data ) {

		$this->grouping['attributes']['health'] = array ( 'title' => $data['health']['title'], 'value' => $s['health']);
	}

	function xpowerType( $s, $data )
	{
		global $grouping, $a;
		$x = $s['powerType'];
		//echo $x.'<br>';
		$this->grouping['attributes'][$x]['title'] = $d[$x]['title'];//$this->$x($s,$data);
		$this->grouping['attributes'][$x]['value'] = $s['power'];
	}
	// Power
	function mana( $s, $data ) { // Mana

		return sprintf($data['mana']['title'], $s['power']);
	}
	function rage( $s, $data ) { // Rage

		return sprintf($data['rage']['title'], $s['power']);
	}
	function focus( $s, $data ) { // Focus

		return sprintf($data['focus']['title'], $s['power']);
	}
	function energy( $s, $data ) { // Energy

		return sprintf($data['energy']['title'], $s['power']);
	}
	function runic( $s, $data ) { // Runic

		return sprintf($data['runic']['title'], $s['power']);
	}

	// Attributes
	function str ( $s, $data ) {
		$this->grouping['attributes']['strength'] = array('title' => $data['strength']['title'], 'value' =>$s['str']);
	}

	function agi( $s, $data ) {
		$this->grouping['attributes']['agility'] = array ( 'title' => $data['agility']['title'], 'value' =>$s['agi']);
	}

	function sta ( $s, $data ) {
		$this->grouping['attributes']['stamina'] = array ( 'title' => $data['stamina']['title'], 'value' => $s['sta']);
	}	

	function int ( $s, $data ) {
		$this->grouping['attributes']['intellect'] = array ( 'title' => $data['intellect']['title'], 'value' => $s['int']);
	}

	// spell

	function spellPower ( $s, $data ) {

		$this->grouping['spell']['spellPower'] = array ( 'title' => $data['spellPower']['title'], 'value' => number_format($s['spellPower'], 2, '.', '') );
		$this->grouping['spell']['spellPower']['description'] = $data['spellPower']['description'].'\r\n'.sprintf($data['spellPower']['description2'], $s['critRating'], number_format($s['crit'], 2, '.', '') );
	}
	/*function spellPen ( $s, $data ) {

		$this->grouping['spell']['spellPen']['title'] = sprintf($data['spellPen']['title'], number_format($s['spellPen'], 2, '.', '') );
		$this->grouping['spell']['spellPen']['description'] = $data['spellPen']['description'].'\r\n'.sprintf($data['spellPen']['description2'], $s['spellPenRating'], number_format($s['spellPen'], 2, '.', '') );
	}*/
	function spellCrit ( $s, $data ) {

		$this->grouping['spell']['spellCrit'] = array ( 'title' => $data['spellCrit']['title'], 'value' => number_format($s['spellCrit'], 2, '.', '') );
		$this->grouping['spell']['spellCrit']['description'] = $data['spellCrit']['description2'].'\r\n'.sprintf($data['spellCrit']['description'], $s['spellCritRating'], number_format($s['spellCrit'], 2, '.', '') );
	}
	function mana5 ( $s, $data ) {

		$this->grouping['spell']['manaRegen'] = array ( 'title' => $data['manaRegen']['title'], 'value' => number_format($s['mana5'], 2, '.', '') );
		$this->grouping['spell']['manaRegen']['description'] = sprintf($data['manaRegen']['description'], number_format($s['mana5'], 2, '.', '') );
	}
	function mana5Combat ( $s, $data ) {

		$this->grouping['spell']['manaCombat'] = array ( 'title' => $data['combatRegen']['title'], 'value' => number_format($s['mana5Combat'], 2, '.', '') );
		$this->grouping['spell']['manaCombat']['description'] = sprintf($data['combatRegen']['description'], number_format($s['mana5Combat'], 2, '.', '') );
	}
	//Enhancements
	#		number_format($number, 2, '.', '')
	// $this->grouping['enhancements']['title'] = 

	function crit ( $s, $data ) {

		$this->grouping['enhancements']['crit'] = array ( 'title' => $data['crit']['title'], 'value' => number_format($s['crit'], 2, '.', '') );
		$this->grouping['enhancements']['crit']['description'] = $data['crit']['description'].'\r\n'.sprintf($data['crit']['description2'], $s['critRating'], number_format($s['crit'], 2, '.', '') );
	}
	function haste ( $s, $data ) {

		$this->grouping['enhancements']['haste'] = array ( 'title' => $data['haste']['title'], 'value' => number_format($s['haste'], 2, '.', '') );
		$this->grouping['enhancements']['haste']['description'] = $data['haste']['description'].'\r\n'.sprintf($data['haste']['description2'], $s['hasteRating'], number_format($s['hasteRatingPercent'], 2, '.', '') );
	}
	function mastery ( $s, $data ) {

		$this->grouping['enhancements']['mastery'] = array ( 'title' => $data['mastery']['title'], 'value' => number_format($s['mastery'], 2, '.', '') );
		$this->grouping['enhancements']['mastery']['description'] = sprintf($data['mastery']['description'], $s['masteryRating'], number_format($s['mastery'], 2, '.', '') );
	}

	function spirit ( $s, $data ) {

		$this->grouping['enhancements']['spirit'] = array ( 'title' => $data['spirit']['title'], 'value' => $s['spr']);
	}

	function bonusarmor ( $s, $data ) {

		$this->grouping['enhancements']['bonusArmor'] = array ( 'title' => $data['bonusArmor']['title'], 'value' => $s['bonusArmor']);
	}

	function multistrike ( $s, $data ) {

		$this->grouping['enhancements']['multistrike'] = array ( 'title' => $data['multistrike']['title'], 'value' => number_format($s['multistrike'], 2, '.', '') );
		$this->grouping['enhancements']['multistrike']['description'] = sprintf($data['multistrike']['description'], number_format($s['multistrike'], 2, '.', ''), number_format($s['multistrikeRatingBonus'], 2, '.', '')).'\r\n'.sprintf($data['multistrike']['description2'], $s['multistrikeRating'], number_format($s['multistrikeRatingBonus'], 2, '.', '') );
	}

	function leech ( $s, $data ) {

		$this->grouping['enhancements']['leech'] = array ( 'title' => $data['leech']['title'], 'value' => number_format($s['leech'], 2, '.', '') );
		$this->grouping['enhancements']['leech']['description'] = $data['leech']['description'].'\r\n'.sprintf($data['leech']['description2'], $s['leechRating'], number_format($s['leechRatingBonus'], 2, '.', '') );
	}

	function versatilityDamageDoneBonus ( $s, $data ) {


		$this->grouping['enhancements']['versatility'] = array ( 'title' => $data['versatility']['title'], 'value' => number_format($s['versatilityHealingDoneBonus'], 2, '.', ''), number_format($s['versatilityDamageTakenBonus'], 2, '.', '') );
		$this->grouping['enhancements']['versatility']['description'] = sprintf($data['versatility']['description'], number_format($s['versatilityHealingDoneBonus'], 2, '.', ''), number_format($s['versatilityDamageTakenBonus'], 2, '.', '') );
	}

	function avoidanceRatingBonus ( $s, $data ) {

		$this->grouping['enhancements']['avoidance'] = array ( 'title' => $data['avoidance']['title'], 'value' => number_format($s['avoidanceRating'], 2, '.', '') );
		$this->grouping['enhancements']['avoidance']['description'] = $data['avoidance']['description'].'\r\n'.sprintf($data['avoidance']['description2'], number_format($s['avoidanceRating'], 2, '.', ''), number_format($s['avoidanceRatingBonus'], 2, '.', '') );
	}

	// attack
	function mainHandDmgMin ( $s, $data ) {
		if ($s['rangedDmgMin'] > 1)
		{
			return $this->rangedDmgMin ( $s, $data );
			break;
		}
		$this->grouping['attack']['mainHandDmgMin'] = array ( 'title' => $data['attack']['title'], 'value' => $s['mainHandDmgMin'].'-'.$s['mainHandDmgMax']);
	}

	function attackPower ( $s, $data ) {
		$this->grouping['attack']['meleeAttackPower'] = array ( 'title' => $data['meleeAttackPower']['title'], 'value' => $s['attackPower'] );
	}

	function mainHandSpeed ( $s, $data ) {
		if ($s['rangedSpeed'] > 1)
		{
			return $this->rangedSpeed ( $s, $data );
			break;
		}

		if ($s['offHandSpeed'] < 0)
		{
			$this->grouping['attack']['meleeSpeed'] = array ( 'title' => $data['meleeSpeedm']['title'], 'value' => $s['mainHandSpeed']);
		}
		if ($s['offHandSpeed'] > 0)
		{
			$this->grouping['attack']['meleeSpeed'] = array ( 'title' => $data['meleeSpeedmo']['title'], 'value' => $s['mainHandSpeed'].'-'.$s['offHandSpeed']);
		}
	}

	function rangedDmgMin ( $s, $data ) {
		$this->grouping['attack']['mainHandDmgMin'] = array ( 'title' => $data['attack']['title'], 'value' => $s['rangedDmgMin'].'-'.$s['rangedDmgMax']);
	}

	function rangedSpeed ( $s, $data ) {
		$this->grouping['attack']['meleeSpeed'] = array ( 'title' => $data['meleeSpeedm']['title'], 'value' => $s['rangedSpeed']);
		
	}

	// Defense
	function armor ( $s, $data ) {
		$this->grouping['defense']['armor'] = array ( 'title' => $data['armor']['title'], 'value' => $s['armor']);
	}

	function dodge ( $s, $data ) {

		$this->grouping['defense']['dodge'] = array ( 'title' => $data['dodge']['title'], 'value' => number_format($s['dodge'], 2, '.', ''));
	}

	function parry ( $s, $data ) {

		$this->grouping['defense']['parry'] = array ( 'title' => $data['parry']['title'], 'value' => number_format($s['parry'], 2, '.', ''));
	}

	function block ( $s, $data ) {

		$this->grouping['defense']['block'] = array ( 'title' => $data['block']['title'], 'value' => number_format($s['block'], 2, '.', ''));
	}
		
	function stats()
	{
		global $roster;
		$sqlquery2 = "SELECT * FROM `" . $roster->db->table('players') . "` WHERE `member_id` = '" . $this->data['member_id'] . "'";
		$result2 = $roster->db->query($sqlquery2);
		$row = $roster->db->fetch($result2);
		
		$data = json_decode($this->dd,true);
		
		foreach ($row as $s => $v)
		{
			if ( method_exists($this, $s) )
			{
				if ($v > 0)
				{
					call_user_func_array(array($this, $s),array($row,$data));
				}
			}
		}

		foreach ($this->grouping as $cat => $vals)
		{
			$roster->tpl->assign_block_vars('stats', array(
				'SCAT' => ucfirst($cat),
				)
			);
			foreach ($vals as $t => $d)
			{
				$roster->tpl->assign_block_vars('stats.line', array(
					'LINE'	=> $d['title'],
					'VALUE'	=> $d['value'],
					)
				);
			}
		}
		
	}
	
	/**
	 * Main output function
	 */
	function out()
	{
		global $roster, $addon;

		$this->fetchEquip();

		$this->stats();
		// Equipment
		$roster->tpl->assign_block_vars('equip', array('SHOW_EMPTY'=>TRUE) );
		$this->equip_slot('Head');
		$this->equip_slot('Neck');
		$this->equip_slot('Shoulder');
		$this->equip_slot('Back');
		$this->equip_slot('Chest');
		$this->equip_slot('Shirt');
		$this->equip_slot('Tabard');
		$this->equip_slot('Wrist');

		$this->equip_slot('MainHand');
		$this->equip_slot('SecondaryHand');
		//no longer used
		//$this->equip_slot('Ammo');

		$this->equip_slot('Hands');
		$this->equip_slot('Waist');
		$this->equip_slot('Legs');
		$this->equip_slot('Feet');
		$this->equip_slot('Finger0');
		$this->equip_slot('Finger1');
		$this->equip_slot('Trinket0');
		$this->equip_slot('Trinket1');

		switch( $this->data['classid'] )
		{
			case ROSTER_CLASS_WARRIOR:
			case ROSTER_CLASS_PALADIN:
			case ROSTER_CLASS_ROGUE:
			case ROSTER_CLASS_MONK:
			case ROSTER_CLASS_DEATHKNIGHT:
				$rightbox = 'melee';
				break;

			case ROSTER_CLASS_HUNTER:
				$rightbox = 'ranged';
				break;

			case ROSTER_CLASS_SHAMAN:
			case ROSTER_CLASS_DRUID:
			case ROSTER_CLASS_MAGE:
			case ROSTER_CLASS_WARLOCK:
			case ROSTER_CLASS_PRIEST:
				$rightbox = 'spell';
				break;
		}

		$roster->tpl->assign_var('RIGHTBOX', $rightbox);

		// PvP
		//$this->show_pvp();
		$this->show_talents();

		// Selected default tab
		$select_tab = (isset($_GET['t']) ? $_GET['t'] : 'profile');

		// Print tabs
		$roster->tpl->assign_block_vars('tabs',array(
			'NAME'     => $roster->locale->act['profile'],
			'VALUE'    => 'profile',
			'SELECTED' => $select_tab == 'profile' ? true : false
			)
		);

		$roster->tpl->assign_var('S_PET_TAB',false);

		// Companion tab
		if( $roster->auth->getAuthorized('show_companions') && $this->show_companions() )
		{
			$roster->tpl->assign_block_vars('tabs',array(
				'NAME'     => $roster->locale->act['companions'],
				'VALUE'    => 'companions',
				'SELECTED' => $select_tab == 'companions' ? true : false
				)
			);
		}
		else
		{
			$roster->tpl->assign_var('S_COMPAN_TAB',false);
		}

		// Reputation Tab
		if( $roster->auth->getAuthorized('show_reputation') && $this->show_reputation() )
		{
			$roster->tpl->assign_block_vars('tabs',array(
				'NAME'     => $roster->locale->act['reputation'],
				'VALUE'    => 'reputation',
				'SELECTED' => $select_tab == 'reputation' ? true : false
				)
			);
		}
		else
		{
			$roster->tpl->assign_var('S_REP_TAB',false);
		}

		// Skills Tab
		if( $roster->auth->getAuthorized('show_skills') && $this->show_skills() )
		{
			$roster->tpl->assign_block_vars('tabs',array(
				'NAME'     => $roster->locale->act['skills'],
				'VALUE'    => 'skills',
				'SELECTED' => $select_tab == 'skills' ? true : false
				)
			);
		}
		else
		{
			$roster->tpl->assign_var('S_SKILL_TAB',false);
		}

		do_action('info_menu_after');
		
		$roster->tpl->set_filenames(array('char' => $addon['basename'] . '/char.html'));
		return $roster->tpl->fetch('char');
	}
}


/**
 * Gets one characters data using a member id
 *
 * @param int $member_id
 * @return mixed False on failure
 */
function char_get_one_by_id( $member_id )
{
	global $roster;

	$query = "SELECT a.*, b.*, `c`.`guild_name`, DATE_FORMAT(  DATE_ADD(`a`.`dateupdatedutc`, INTERVAL " . $roster->config['localtimeoffset'] . " HOUR ), '" . $roster->locale->act['timeformat'] . "' ) AS 'update_format'"
		. " FROM `" . $roster->db->table('players') . "` a, `" . $roster->db->table('members') . "` b, `" . $roster->db->table('guild') . "` c"
		. " WHERE `a`.`member_id` = `b`.`member_id`"
			. " AND `a`.`member_id` = '$member_id'"
			. " AND `a`.`guild_id` = `c`.`guild_id`;";
	$result = $roster->db->query($query);
	if( $roster->db->num_rows($result) > 0 )
	{
		$data = $roster->db->fetch($result);
		return new char($data);
	}
	else
	{
		return false;
	}
}


/**
 * Gets one characters data using name, server
 *
 * @param string $name
 * @param string $server
 * @return mixed False on failure
 */
function char_get_one( $name, $server )
{
	global $roster;

	$name = $roster->db->escape( $name );
	$server = $roster->db->escape( $server );
	$query = "SELECT `a`.*, `b`.*, `c`.`guild_name`, DATE_FORMAT(  DATE_ADD(`a`.`dateupdatedutc`, INTERVAL " . $roster->config['localtimeoffset'] . " HOUR ), '" . $roster->locale->act['timeformat'] . "' ) AS 'update_format'"
		. " FROM `" . $roster->db->table('players') . "` a, `" . $roster->db->table('members') . "` b, `" . $roster->db->table('guild') . "` c"
		. " WHERE `a`.`member_id` = `b`.`member_id`"
			. " AND `a`.`name` = '$name'"
			. " AND `a`.`server` = '$server'"
			. " AND `a`.`guild_id` = `c`.`guild_id`;";
	$result = $roster->db->query($query);
	if( $roster->db->num_rows($result) > 0 )
	{
		$data = $roster->db->fetch($result);
		return new char($data);
	}
	else
	{
		return false;
	}
}
