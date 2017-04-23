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
	var $equip;
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

		// Get display column names
		$query = 'SELECT * FROM `' . $roster->db->table('default',$addon['basename']) . '`;';

		$result = $roster->db->query($query);
		$disp_defaults = $roster->db->fetch_all($result, SQL_ASSOC);
		$disp_defaults = $disp_defaults[0];

		// Get permission data for this member id
		$querystr = "SELECT * FROM `" . $roster->db->table('display',$addon['basename']) . "`"
			. " WHERE `member_id` = '" . $this->data['member_id'] . "';";

		$results = $roster->db->query($querystr);
		$row = $roster->db->fetch($results, SQL_ASSOC);

		foreach( $disp_defaults as $name => $value )
		{
			if( $addon['config'][$name] == -1)
			{
				$addon['config'][$name] = $row[$name];
			}
		}

		//echo '<pre>';		print_r($this->data);		echo '</pre>';
		//d($this->data);
		$ximg_url=null;
		if (file_exists($addon['dir'] .'chars/thumb-'. $this->data['member_id'].'.jpg') )
		{
			$ximg_url = $addon['url_path'] .'chars/thumb-'. $this->data['member_id'].'.jpg';
		}

		/**
		 * Assigning everything this file may need to the template
		 * The only tpl vars not here are ones that need to be generated in their respective methods
		 */

		$roster->tpl->assign_vars(array(
			'S_MAX_LEVEL' => ROSTER_MAXCHARLEVEL,

			'S_MODEL_TAB'   => $ximg_url != '' ? true : false,//false,//$roster->auth->getAuthorized($addon['config']['show_model']),
			/*'MODEL_URL'     => $model_url != '' ? $model_url : false,*/
			
			'S_CHAR_IMG'	=> $ximg_url != '' ? $ximg_url : false,
			'XIMG_URL'		=> $ximg_url,

			'S_PLAYED'      => $roster->auth->getAuthorized($addon['config']['show_played']),
			'S_MONEY'       => $roster->auth->getAuthorized($addon['config']['show_money']),
			'S_PET_TAB'     => $roster->auth->getAuthorized($addon['config']['show_pets']),
			'S_COMPAN_TAB'  => $roster->auth->getAuthorized($addon['config']['show_companions']),
			'S_REP_TAB'     => $roster->auth->getAuthorized($addon['config']['show_reputation']),
			'S_SKILL_TAB'   => $roster->auth->getAuthorized($addon['config']['show_skills']),
			'S_PVP_TAB'     => $roster->auth->getAuthorized($addon['config']['show_honor']),
			'S_TALENT_TAB'  => $roster->auth->getAuthorized($addon['config']['show_talents']),
			'S_GLYPH_TAB'   => $roster->auth->getAuthorized($addon['config']['show_glyphs']),
			'S_SPELL_TAB'   => $roster->auth->getAuthorized($addon['config']['show_spellbook']),
			'S_BONUS_TAB'   => $roster->auth->getAuthorized($addon['config']['show_item_bonuses']),
			'S_PET_TALENT_TAB' => $roster->auth->getAuthorized($addon['config']['show_pet_talents']),
			'S_PET_SPELL_TAB'  => $roster->auth->getAuthorized($addon['config']['show_pet_spells']),
			'S_CURRENCY_TAB'   => $roster->auth->getAuthorized($addon['config']['show_currency']),

			'S_PETS'        => false,
			//'S_MOUNTS'      => false,
			//'S_COMPANIONS'  => false,
			'CLASSID'		=> $this->data['classid'],

			'L_CHAR_POWER'    => $this->data['power'],
			'L_CHAR_POWER_ID' => strtolower($this->data['power']),

			'HEALTH'        => $this->data['health'],
			'POWER'         => $this->data['mana'],
			'TALENT_POINTS' => $this->data['talent_points'],

			'MONEY_G' => $this->data['money_g'],
			'MONEY_S' => $this->data['money_s'],
			'MONEY_C' => $this->data['money_c']
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
	

	function show_buffs()
	{
		global $roster;

		// Get char professions for quick links
		$query = "SELECT * FROM `" . $roster->db->table('buffs') . "` WHERE `member_id` = '" . $this->data['member_id'] . "';";
		$result = $roster->db->query($query);

		$return_string = '';
		if( $roster->db->num_rows($result) > 0 )
		{
			$return_string .= '<div class="buff_icons">';
			while( $row = $roster->db->fetch($result, SQL_ASSOC) )
			{
				$tooltip = makeOverlib($row['tooltip'], '', 'ffdd00', 1, '', ',RIGHT');

				$roster->tpl->assign_block_vars('buff', array(
					'NAME'    => $row['name'],
					'RANK'    => $row['rank'],
					'COUNT'   => $row['count'],
					'ICON'    => $row['icon'],
					'TOOLTIP' => $tooltip
					)
				);
			}
		}
	}


	/**
	 * Build quests
	 *
	 * @return string
	 */
	function show_quests()
	{
		global $roster, $addon;

		$quests = quest_get_many($this->data['member_id']);

		$roster->tpl->assign_vars(array(
			'S_QUESTS'    => count($quests),
			'S_MAXQUESTS' => ROSTER_MAXQUESTS,
			)
		);

		if( isset($quests[0]) )
		{
			$quests_arr = array();
			foreach( $quests as $object )
			{
				$zone = $object->data['zone'];
				$quest_name = $object->data['quest_name'];
				$quests_arr[$zone][$quest_name]['quest_id'] = $object->data['quest_id'];
				$quests_arr[$zone][$quest_name]['quest_index'] = $object->data['quest_index'];
				$quests_arr[$zone][$quest_name]['quest_level'] = $object->data['quest_level'];
				$quests_arr[$zone][$quest_name]['quest_tag'] = $object->data['quest_tag'];
				$quests_arr[$zone][$quest_name]['difficulty'] = $object->data['difficulty'];

				$description = str_replace('<class>',$this->data['class'],$object->data['description']);
				$description = str_replace('<name>',$this->data['name'],$description);
				$quests_arr[$zone][$quest_name]['description'] = nl2br($description);

				$objective = str_replace('<class>',$this->data['class'],$object->data['objective']);
				$objective = str_replace('<name>',$this->data['name'],$objective);
				$quests_arr[$zone][$quest_name]['objective'] = nl2br($objective);

				$quests_arr[$zone][$quest_name]['reward_money'] = $object->data['reward_money'];
				$quests_arr[$zone][$quest_name]['daily'] = $object->data['daily'];
				$quests_arr[$zone][$quest_name]['group'] = $object->data['group'];
				$quests_arr[$zone][$quest_name]['is_complete'] = $object->data['is_complete'];
			}

			foreach( $quests_arr as $zone => $quest )
			{
				$roster->tpl->assign_block_vars('zone',array(
					'NAME' => $zone,
					)
				);

				foreach( $quest as $quest_name => $data )
				{
					switch( $data['difficulty'] )
					{
						case 4:
							$color = 'red';
							break;

						case 3:
							$color = 'orange';
							break;

						case 2:
							$color = 'yellow';
							break;

						case 1:
							$color = 'green';
							break;

						case 0:
						default:
							$color = 'grey';
							break;
					}

					$reward_money_c = $reward_money_s = $reward_money_g = 0;
					if( $data['reward_money'] > 0 )
					{
						$money = $data['reward_money'];

						$reward_money_c = $money % 100;
						$money = floor( $money / 100 );

						if( !empty($money) )
						{
							$reward_money_s = $money % 100;
							$money = floor( $money / 100 );
						}
						if( !empty($money) )
						{
							$reward_money_g = $money;
						}
					}

					$roster->tpl->assign_block_vars('zone.quest',array(
						'ROW_CLASS'    => $roster->switch_row_class(),
						'NAME'         => $quest_name,
						'COLOR'        => $color,
						'ID'           => $data['quest_id'],
						'INDEX'        => $data['quest_index'],
						'LEVEL'        => $data['quest_level'],
						'DIFFICULTY'   => $data['difficulty'],
						'TAG'          => $data['quest_tag'],
						'COMPLETE'     => $data['is_complete'],
						'DESCRIPTION'  => $data['description'],
						'REWARD_MONEY_C' => $reward_money_c,
						'REWARD_MONEY_S' => $reward_money_s,
						'REWARD_MONEY_G' => $reward_money_g,
						'OBJECTIVE'    => $data['objective'],
						'DAILY'        => $data['daily'],
						'GROUP'        => $data['group'],
						)
					);

					foreach( $roster->locale->act['questlinks'] as $link )
					{
						$roster->tpl->assign_block_vars('zone.quest.links',array(
							'NAME' => $link['name'],
							'LINK' => sprintf($link['url'],$data['quest_id']),
							)
						);
					}
				}
			}
		}
		$roster->tpl->set_filenames(array('quests' => $addon['basename'] . '/quests.html'));
		return $roster->tpl->fetch('quests');
	}


	/**
	 * Build Recipes
	 *
	 * @return string
	 */
	function show_recipes()
	{
		global $roster, $addon;

		$roster->tpl->assign_vars(array(
			'S_RECIPE_HIDE' => !(bool)$addon['config']['recipe_disp'],

			'U_ITEM'       => makelink('char-info-recipes&amp;s=item'),
			'U_NAME'       => makelink('char-info-recipes&amp;s=name'),
			'U_DIFFICULTY' => makelink('char-info-recipes&amp;s=difficulty'),
			'U_TYPE'       => makelink('char-info-recipes&amp;s=type'),
			'U_LEVEL'      => makelink('char-info-recipes&amp;s=level'),
			'U_REAGENTS'   => makelink('char-info-recipes&amp;s=reagents'),
			)
		);

		// Get recipe sort mode
		$sort = (isset($_GET['s']) ? $_GET['s'] : '');

		$recipes = recipe_get_many($this->data['member_id'], '', $sort);
		$reagents = recipe_get_regents($this->data['member_id']);

		$reagent_arr = array();
		foreach ($reagents as $objects)
		{
			$skil = $objects->data['reagent_id'];
			$reagent_arr[$skil]['item_color'] = $objects->data['reagent_color'];
			$reagent_arr[$skil]['item_texture'] = $objects->data['reagent_texture'];
			$reagent_arr[$skil]['item_id'] = $objects->data['reagent_id'];
			$reagent_arr[$skil]['item_name'] = $objects->data['reagent_name'];
			$reagent_arr[$skil]['tooltip'] = $objects->data['reagent_tooltip'];
		}
		$recipexx = array();
		//$recipeee = new recipe();
		foreach ($recipes as $idx => $data)
		{
			if (isset($data['recipe_sub_type']) && !empty($data['recipe_sub_type']) )
			{
				$skill = $data['skill_name'];
				$type = $data['recipe_type'];
				$subtype = $data['recipe_sub_type'];
				$recipe = $data['recipe_name'];
				$recipeee = new recipe($data);
				$recipexx[$skill][$type][$subtype]["sub"] = true;
				$recipexx[$skill][$type][$subtype][$recipe]['recipe_type'] = $data['recipe_type'];
				$recipexx[$skill][$type][$subtype][$recipe]['difficulty'] = $data['difficulty'];
				$recipexx[$skill][$type][$subtype][$recipe]['item_color'] = $data['item_color'];
				$recipexx[$skill][$type][$subtype][$recipe]['reagents'] = $data['reagents'];
				$recipexx[$skill][$type][$subtype][$recipe]['recipe_texture'] = $data['recipe_texture'];
				$recipexx[$skill][$type][$subtype][$recipe]['level'] = $data['level'];
				$recipexx[$skill][$type][$subtype][$recipe]['item_id'] = $data['item_id'];
				$recipexx[$skill][$type][$subtype][$recipe]['recipe_id'] = $data['recipe_id'];
				$recipexx[$skill][$type][$subtype][$recipe]['icon'] = $roster->config['interface_url'] . 'Interface/Icons/' . $data['recipe_texture'] . '.' . $roster->config['img_suffix'];//$data->tpl_get_icon();
				$recipexx[$skill][$type][$subtype][$recipe]['tooltip'] = makeOverlib($data['recipe_tooltip'],'',$data['item_color'],0,$roster->config['locale']);
				$recipexx[$skill][$type][$subtype][$recipe]['itemlink'] = $recipeee->tpl_get_itemlink();
				$recipexx[$skill][$type][$subtype][$recipe]['quality'] = $recipeee->_setQuality($data['item_color']);
			}
			else
			{
				$skill = $data['skill_name'];
				$type = $data['recipe_type'];
				$recipe = $data['recipe_name'];
				$recipeee = new recipe($data);
				$recipexx[$skill][$type][$recipe]['recipe_type'] = $data['recipe_type'];
				$recipexx[$skill][$type][$recipe]['difficulty'] = $data['difficulty'];
				$recipexx[$skill][$type][$recipe]['item_color'] = $data['item_color'];
				$recipexx[$skill][$type][$recipe]['reagents'] = $data['reagents'];
				$recipexx[$skill][$type][$recipe]['recipe_texture'] = $data['recipe_texture'];
				$recipexx[$skill][$type][$recipe]['level'] = $data['level'];
				$recipexx[$skill][$type][$recipe]['item_id'] = $data['item_id'];
				$recipexx[$skill][$type][$recipe]['recipe_id'] = $data['recipe_id'];
				$recipexx[$skill][$type][$recipe]['icon'] = $roster->config['interface_url'] . 'Interface/Icons/' . $data['recipe_texture'] . '.' . $roster->config['img_suffix'];//$data->tpl_get_icon();
				$recipexx[$skill][$type][$recipe]['tooltip'] = makeOverlib($data['recipe_tooltip'],'',$data['item_color'],0,$roster->config['locale']);
				$recipexx[$skill][$type][$recipe]['itemlink'] = $recipeee->tpl_get_itemlink();
				$recipexx[$skill][$type][$recipe]['quality'] = $recipeee->_setQuality($data['item_color']);
			}
		}
		
		foreach ($recipexx as $skill_name => $header)
		{
			$roster->tpl->assign_block_vars('recipe',array(
				'ID'      => strtolower(str_replace(' ','',$skill_name)),
				'NAME'    => $skill_name,
				'ICON'    => $this->locale['ts_iconArray'][$skill_name],
				'TOOLTIP' => makeOverlib($skill_name,'','',1,'',',WRAP'),
				'LINK'    => makelink('#' . strtolower(str_replace(' ','',$skill_name))),
				)
			);
			foreach ($header as $hname => $recipe)
			{
				$roster->tpl->assign_block_vars('recipe.header',array(
						'NAME'         => $hname,
						)
					);
				foreach ($recipe as $name => $data)
				{
					if (isset($data['sub']))
					{
						$roster->tpl->assign_block_vars('recipe.header.subheader',array(
							'NAME'         => $name,
							)
						);
						foreach($data as $s => $dat)
						{
							if ($s != 'sub')
							{
								$roster->tpl->assign_block_vars('recipe.header.subheader.rows',array(
									'ROW_CLASS'    => $roster->switch_row_class(),
									'DIFFICULTY'   => $dat['difficulty'],
									'L_DIFFICULTY' => $roster->locale->act['recipe_' . $dat['difficulty']],
									'ITEM_COLOR'   => $dat['item_color'],
									'NAME'         => $s,
									'DIFFICULTY_COLOR' => $this->diff($dat['difficulty']),
									'TYPE'         => $dat['recipe_type'],
									'LEVEL'        => $dat['level'],
									'ICON'         => $dat['icon'],
									'TOOLTIP'      => $dat['tooltip'],
									'ITEMLINK'     => $dat['itemlink'],
									'QUALITY'      => $dat['quality'],
									)
								);
								
								$reagents = explode('|',$dat['reagents']);

								if ( is_array($reagents) )
								{

									foreach ($reagents as $reagent)
									{
										$dtr = explode(':', $reagent);
										if (empty($dtr[0]))
										{
											$roster->tpl->assign_block_vars('recipe.header.subheader.rows.reagents',array(
												'DATA' 		 => $reagent,
												'ID' 		 => '000',
												'NAME' 		 => 'Missing',
												'ITEM_COLOR' => '000000',
												'QUALITY'    => recipe::getQualityName('ffffff'),
												'COUNT' 	 => '0',
												'ICON' 		 => 'inv_misc_questionmark',
												'TOOLTIP' 	 => makeOverlib('Missing data','','',0,$this->data['clientLocale'],',RIGHT'),
												)
											);
										}
										else
										{
											$roster->tpl->assign_block_vars('recipe.header.subheader.rows.reagents',array(
												'DATA' 		 => $reagent,
												'ID' 		 => $reagent_arr[$dtr[0]]['item_id'],
												'NAME' 		 => $reagent_arr[$dtr[0]]['item_name'],
												'ITEM_COLOR' => $reagent_arr[$dtr[0]]['item_color'],
												'QUALITY'    => recipe::getQualityName($reagent_arr[$dtr[0]]['item_color']),
												'COUNT' 	 => $dtr[1],
												'ICON' 		 => $reagent_arr[$dtr[0]]['item_texture'],
												'TOOLTIP' 	 => makeOverlib($reagent_arr[$dtr[0]]['tooltip'],'','',0,$this->data['clientLocale'],',RIGHT'),
												)
											);
										}
									}
								}
					
							}
						}
					}
					else
					{
					
					$roster->tpl->assign_block_vars('recipe.header.row',array(
						'ROW_CLASS'    => $roster->switch_row_class(),
						'DIFFICULTY'   => $this->diff($data['difficulty']),
						'L_DIFFICULTY' => $roster->locale->act['recipe_' . $data['difficulty']],
						'ITEM_COLOR'   => $data['item_color'],
						'NAME'         => $name,
						'DIFFICULTY_COLOR' => $this->diff($data['difficulty']),
						'TYPE'         => $data['recipe_type'],
						'LEVEL'        => $data['level'],
						'ICON'         => $data['icon'],
						'TOOLTIP'      => $data['tooltip'],
						'ITEMLINK'     => $data['itemlink'],
						'QUALITY'      => $data['quality'],
						)
					);

					$reagents = explode('|',$data['reagents']);

					//echo $name.'<br>';
					if ( is_array($reagents) )
					{
					//print_r($reagents);
					//echo '<pre><br>';
					foreach ($reagents as $reagent)
					{
						$dtr = explode(':', $reagent);
						if (empty($dtr[0]))
						{
							$roster->tpl->assign_block_vars('recipe.header.row.reagents',array(
								'DATA' 		 => $reagent,
								'ID' 		 => '000',
								'NAME' 		 => 'Missing',
								'ITEM_COLOR' => '000000',
								'QUALITY'    => recipe::getQualityName('ffffff'),
								'COUNT' 	 => '0',
								'ICON' 		 => 'inv_misc_questionmark',
								'TOOLTIP' 	 => makeOverlib('Missing data','','',0,$this->data['clientLocale'],',RIGHT'),
								)
							);
						}
						else
						{
							$roster->tpl->assign_block_vars('recipe.header.row.reagents',array(
								'DATA' 		 => $reagent,
								'ID' 		 => $reagent_arr[$dtr[0]]['item_id'],
								'NAME' 		 => $reagent_arr[$dtr[0]]['item_name'],
								'ITEM_COLOR' => $reagent_arr[$dtr[0]]['item_color'],
								'QUALITY'    => recipe::getQualityName($reagent_arr[$dtr[0]]['item_color']),
								'COUNT' 	 => $dtr[1],
								'ICON' 		 => $reagent_arr[$dtr[0]]['item_texture'],
								'TOOLTIP' 	 => makeOverlib($reagent_arr[$dtr[0]]['tooltip'],'','',0,$this->data['clientLocale'],',RIGHT'),
								)
							);
						}
						}
					}
					}
				}
			}
		}	
		
		/*
		if (isset($recipes[0]))
		{
			$recipe_arr = array();
			foreach ($recipes as $object)
			{
				$skill = $object->data['skill_name'];
				$recipe = $object->data['recipe_name'];
				$recipe_arr[$skill][$recipe]['recipe_type'] = $object->data['recipe_type'];
				$recipe_arr[$skill][$recipe]['difficulty'] = $object->data['difficulty'];
				$recipe_arr[$skill][$recipe]['item_color'] = $object->data['item_color'];
				$recipe_arr[$skill][$recipe]['reagents'] = $object->data['reagents'];
				$recipe_arr[$skill][$recipe]['recipe_texture'] = $object->data['recipe_texture'];
				$recipe_arr[$skill][$recipe]['level'] = $object->data['level'];
				$recipe_arr[$skill][$recipe]['item_id'] = $object->data['item_id'];
				$recipe_arr[$skill][$recipe]['recipe_id'] = $object->data['recipe_id'];
				$recipe_arr[$skill][$recipe]['icon'] = $object->tpl_get_icon();
				$recipe_arr[$skill][$recipe]['tooltip'] = $object->tpl_get_tooltip();
				$recipe_arr[$skill][$recipe]['itemlink'] = $object->tpl_get_itemlink();
				$recipe_arr[$skill][$recipe]['quality'] = $object->quality;
			}

//			echo '<pre>';
//			print_r($recipe_arr);

			foreach ($recipe_arr as $skill_name => $recipe)
			{
				$roster->tpl->assign_block_vars('recipe',array(
					'ID'      => strtolower(str_replace(' ','',$skill_name)),
					'NAME'    => $skill_name,
					'ICON'    => $this->locale['ts_iconArray'][$skill_name],
					'TOOLTIP' => makeOverlib($skill_name,'','',1,'',',WRAP'),
					'LINK'    => makelink('#' . strtolower(str_replace(' ','',$skill_name))),
					)
				);
				foreach ($recipe as $name => $data)
				{
					switch ($data['difficulty'])
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

					$roster->tpl->assign_block_vars('recipe.row',array(
						'ROW_CLASS'    => $roster->switch_row_class(),
						'DIFFICULTY'   => $data['difficulty'],
						'L_DIFFICULTY' => $roster->locale->act['recipe_' . $data['difficulty']],
						'ITEM_COLOR'   => $data['item_color'],
						'NAME'         => $name,
						'DIFFICULTY_COLOR' => $difficultycolor,
						'TYPE'         => $data['recipe_type'],
						'LEVEL'        => $data['level'],
						'ICON'         => $data['icon'],
						'TOOLTIP'      => $data['tooltip'],
						'ITEMLINK'     => $data['itemlink'],
						'QUALITY'      => $data['quality'],
						)
					);

					$reagents = explode('|',$data['reagents']);

					//echo $name.'<br>';
					if ( is_array($reagents) )
					{
					//print_r($reagents);
					//echo '<pre><br>';
					foreach ($reagents as $reagent)
					{
						$dtr = explode(':', $reagent);
						//print_r($reagent);
						//echo '<br>';
						//echo $dtr[0].' -|- '.
						//print_r($reagent_arr[$dtr[0]]);
						//echo '<br><hr><br>';
						if (empty($dtr[0]))
						{
							$roster->tpl->assign_block_vars('recipe.row.reagents',array(
								'DATA' 		 => $reagent,
								'ID' 		 => '000',
								'NAME' 		 => 'Missing',
								'ITEM_COLOR' => '000000',
								'QUALITY'    => recipe::getQualityName('ffffff'),
								'COUNT' 	 => '0',
								'ICON' 		 => 'inv_misc_questionmark',
								'TOOLTIP' 	 => makeOverlib('Missing data','','',0,$this->data['clientLocale'],',RIGHT'),
								)
							);
						}
						else
						{
							$roster->tpl->assign_block_vars('recipe.row.reagents',array(
								'DATA' 		 => $reagent,
								'ID' 		 => $reagent_arr[$dtr[0]]['item_id'],
								'NAME' 		 => $reagent_arr[$dtr[0]]['item_name'],
								'ITEM_COLOR' => $reagent_arr[$dtr[0]]['item_color'],
								'QUALITY'    => recipe::getQualityName($reagent_arr[$dtr[0]]['item_color']),
								'COUNT' 	 => $dtr[1],
								'ICON' 		 => $reagent_arr[$dtr[0]]['item_texture'],
								'TOOLTIP' 	 => makeOverlib($reagent_arr[$dtr[0]]['tooltip'],'','',0,$this->data['clientLocale'],',RIGHT'),
								)
							);
						}
						}
					}
				}
			}
		}
		*/
		//echo '<pre>';print_r($recipexx);echo '</pre>';
		$roster->tpl->set_filenames(array('recipes' => $addon['basename'] . '/recipes.html'));
		return $roster->tpl->fetch('recipes');
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
	 * Build Mail
	 *
	 * @return string
	 */
	function show_mailbox()
	{
		global $roster, $addon;

		$sqlquery = "SELECT * FROM `" . $roster->db->table('mailbox') . "` "
				  . "WHERE `member_id` = '" . $this->data['member_id'] . "' "
				  . "ORDER BY `mailbox_days`;";

		$result = $roster->db->query($sqlquery);

		$roster->tpl->assign_vars(array(
			'S_MAIL_DISP' => $addon['config']['mail_disp'],
			'S_MAIL' => false,
			)
		);

		if( $result && $roster->db->num_rows($result) > 0 )
		{
			$roster->tpl->assign_var('S_MAIL', true);

			while( $row = $roster->db->fetch($result, SQL_ASSOC) )
			{
				$maildateutc = strtotime($this->data['maildateutc']);

				// Get money in mail
				$money_included = '';
				if( $row['mailbox_coin'] > 0 && $roster->auth->getAuthorized($addon['config']['show_money']) )
				{
					$db_money = $row['mailbox_coin'];

					$mail_money['c'] = $db_money % 100;
					$db_money = floor( $db_money / 100 );
					$money_included = $mail_money['c'] . '<img src="' . $roster->config['img_url'] . 'coin_copper.png" alt="c" />';

					if( !empty($db_money) )
					{
						$mail_money['s'] = $db_money % 100;
						$db_money = floor( $db_money / 100 );
						$money_included = $mail_money['s'] . '<img src="' . $roster->config['img_url'] . 'coin_silver.png" alt="s" /> ' . $money_included;
					}
					if( !empty($db_money) )
					{
						$mail_money['g'] = $db_money;
						$money_included = $mail_money['g'] . '<img src="' . $roster->config['img_url'] . 'coin_gold.png" alt="g" /> ' . $money_included;
					}
				}

				// Start the tooltips
				$tooltip_h = $row['mailbox_subject'];

				// first line is sender
				$tooltip = $roster->locale->act['mail_sender'] . ': ' . $row['mailbox_sender'] . '<br />';

				$expires_line = date($roster->locale->act['phptimeformat'],($row['mailbox_days']*24*3600)+$maildateutc) . ' ' . $roster->config['timezone'];

				if( (($row['mailbox_days']*24*3600)+$maildateutc) - time() < (3*24*3600) )
				{
					$color = 'ff0000';
				}
				else
				{
					$color = 'ffffff';
				}

				$tooltip .= $roster->locale->act['mail_expires'] . ": <span style=\"color:#$color;\">$expires_line</span><br />";

				// Join money with main tooltip
				if( !empty($money_included) )
				{
					$tooltip .= $roster->locale->act['mail_money'] . ': ' . $money_included;
				}

				$tooltipcode = makeOverlib($tooltip,$tooltip_h,'',2,$this->data['clientLocale']);

				if( $addon['config']['mail_disp'] > 0 )
				{
					// Set up box display
					$row['item_slot'] = 'Mail ' . $row['mailbox_slot'];
					$row['item_id'] = '0:0:0:0:0';
					$row['item_name'] = $row['mailbox_subject'];
					$row['item_level'] = 0;
					$row['item_texture'] = $row['mailbox_icon'];
					$row['item_parent'] = 'Mail';
					$row['item_tooltip'] = $tooltip;
					$row['item_color'] = '';
					$row['item_quantity'] = 0;
					$row['locale'] = $this->data['clientLocale'];

					$attach = new bag($row);
					$attach->out();
				}

				$roster->tpl->assign_block_vars('mail',array(
					'ROW_CLASS' => $roster->switch_row_class(),
					'TOOLTIP'   => $tooltipcode,
					'ITEM_ICON' => $row['mailbox_icon'],
					'SENDER'    => $row['mailbox_sender'],
					'SUBJECT'   => $row['mailbox_subject'],
					'EXPIRES'   => $expires_line,
					)
				);
			}
		}

		$roster->tpl->set_filenames(array('mailbox' => $addon['basename'] . '/mailbox.html'));
		return $roster->tpl->fetch('mailbox');
	}


	/**
	 * Build Spellbook
	 *
	 * @return bool
	 */
	function show_spellbook()
	{
		global $roster, $addon;

		// Initialize $spellbook array
		$spellbook[$this->data['name']] = array();

		$query = "SELECT `spelltree`.*, `talenttree`.`order`"
			. " FROM `" . $roster->db->table('spellbooktree') . "` AS spelltree"
			. " LEFT JOIN `" . $roster->db->table('talenttree') . "` AS talenttree"
				. " ON `spelltree`.`member_id` = `talenttree`.`member_id`"
				. " AND `spelltree`.`spell_type` = `talenttree`.`tree`"
			. " WHERE `spelltree`.`member_id` = " . $this->data['member_id']
			. " ORDER BY `talenttree`.`order` ASC;";

		$result = $roster->db->query($query);

		if( !$result )
		{
			return false;
		}

		$num_trees = $roster->db->num_rows($result);

		if( $num_trees == 0 )
		{
			return false;
		}

		for( $t=0; $t < $num_trees; $t++)
		{
			$row = $roster->db->fetch($result,SQL_ASSOC);

			$spell_type = $row['spell_type'];
			$spellbook[$this->data['name']][$spell_type]['order'] = $t;
			$spellbook[$this->data['name']][$spell_type]['icon'] = $row['spell_texture'];
			$spellbook[$this->data['name']][$spell_type]['isOffspec'] = $row['spell_build'];
			$spellbook[$this->data['name']][$spell_type]['tooltip'] = makeOverlib($spell_type,'','',2,'',',WRAP,RIGHT');

			// Get the spell data
			$query2 = "SELECT * FROM `" . $roster->db->table('spellbook') . "`"
				. " WHERE `member_id` = '" . $this->data['member_id'] . "'"
					. " AND `spell_type` = '" . $roster->db->escape($spell_type) . "'"
				. " ORDER BY `spell_name`;";

			$result2 = $roster->db->query($query2);

			$s = $p = 0;
			while( $row2 = $roster->db->fetch($result2, SQL_ASSOC) )
			{
				if( ($s / 14) == 1 )
				{
					$s = 0;
					++$p;
				}

				// Prepare tooltip for rank insertion
				$tooltip = str_replace("\n\n", "\n", $row2['spell_tooltip']);
				$tooltip = str_replace('<br>',"\n",$tooltip);
				$tooltip = str_replace('<br />',"\n",$tooltip);
				$tooltip = explode("\n", $tooltip);
				$tooltip[0] .= "\t" . $row2['spell_rank'];
				$tooltip = implode("\n", $tooltip);

				$spell_name = $row2['spell_name'];
				$spellbook[$this->data['name']][$spell_type]['spells'][$p][$spell_name]['num'] = $s;
				$spellbook[$this->data['name']][$spell_type]['spells'][$p][$spell_name]['icon'] = $row2['spell_texture'];
				$spellbook[$this->data['name']][$spell_type]['spells'][$p][$spell_name]['rank'] = $row2['spell_rank'];
				$spellbook[$this->data['name']][$spell_type]['spells'][$p][$spell_name]['tooltip'] = makeOverlib($tooltip,'','',0,$this->data['clientLocale'],',RIGHT');
				++$s;
			}
			$roster->db->free_result($result2);
		}

		$roster->db->free_result($result);


		// Get the PET spell data
		$query = "SELECT `spell`.*, `pet`.`name`"
			. " FROM `" . $roster->db->table('pet_spellbook') . "` as spell"
			. " LEFT JOIN `" . $roster->db->table('pets') . "` AS pet"
				. " ON `spell`.`pet_id` = `pet`.`pet_id`"
			. " WHERE `spell`.`member_id` = '" . $this->data['member_id'] . "'"
			. " ORDER BY `spell`.`spell_name`;";

		$result = $roster->db->query($query);

		$pet_rows = $roster->db->num_rows($result);

		if( $pet_rows > 0 )
		{
			$s = $p = 0;
			while( $row = $roster->db->fetch($result, SQL_ASSOC) )
			{
				if( ($s / 14) == 1 )
				{
					$s = 0;
					++$p;
				}
				$petname = $row['name'];
				$spell_name = $row['spell_name'];

				$spellbook[$petname][0]['order'] = 0;
				$spellbook[$petname][0]['icon'] = 'ability_kick';
				$spellbook[$petname][0]['tooltip'] = '';

				$spellbook[$petname][0]['spells'][0][$spell_name]['num'] = $s;
				$spellbook[$petname][0]['spells'][0][$spell_name]['icon'] = $row['spell_texture'];
				$spellbook[$petname][0]['spells'][0][$spell_name]['rank'] = $row['spell_rank'];
				$spellbook[$petname][0]['spells'][0][$spell_name]['tooltip'] = makeOverlib($row['spell_tooltip'],'','',0,$this->data['clientLocale'],',RIGHT');
				++$s;
			}
		}
		$roster->db->free_result($result);

		foreach( $spellbook as $name => $spell_tree )
		{
			$roster->tpl->assign_block_vars('spell_book',array(
				'NAME'    => $name,
				'ID'      => strtolower(str_replace("'",'',$name)),
				'S_TREES' => !isset($spell_tree[0])
				)
			);
			foreach( $spell_tree as $spell_type => $spell_tree )
			{
				$roster->tpl->assign_block_vars('spell_book.tree',array(
					'NAME'  => $spell_type,
					'ORDER' => $spell_tree['order'],
					'ID'    => strtolower(str_replace(' ','',$spell_type)),
					'ICON'  => $spell_tree['icon'],
					'ISOFFSPEC' => $spell_tree['isOffspec'],
					'TOOLTIP' => $spell_tree['tooltip'],
					)
				);
				foreach( $spell_tree['spells'] as $page => $spell )
				{
					$roster->tpl->assign_block_vars('spell_book.tree.page',array(
						'ID'   => $page,
						'NUM'  => $page+1,
						'PREV' => ( isset($spell_tree['spells'][$page-1]) ? $page-1 : false ),
						'NEXT' => ( isset($spell_tree['spells'][$page+1]) ? $page+1 : false ),
						)
					);
					foreach( $spell as $spell_name => $spell_data )
					{
						$roster->tpl->assign_block_vars('spell_book.tree.page.spell',array(
							'NUM'  => $spell_data['num'],
							'NAME' => $spell_name,
							'ICON' => $spell_data['icon'],
							'RANK' => $spell_data['rank'],
							'TOOLTIP' => $spell_data['tooltip'],
							)
						);
					}
				}
			}
		}

		return true;
	}

	function show_currency( )
	{
		global $roster;

		$query = "SELECT * FROM `" . $roster->db->table('currency') . "`"
			. " WHERE `member_id` = '" . $this->data['member_id'] . "'"
			. " ORDER BY `category` ASC, `order` ASC;";

		$result = $roster->db->query($query);

		if( !$result )
		{
			return false;
		}

		$num_currency = $roster->db->num_rows($result);

		if( $num_currency == 0 )
		{
			return false;
		}

		$currency_data = array();
		for( $t = 0; $t < $num_currency; $t++ )
		{
			$row = $roster->db->fetch($result, SQL_ASSOC);

			$category = $row['category'];
			$currency_name = $row['name'];
			$currency_data[$category][$currency_name]['order'] = $row['order'];
			$currency_data[$category][$currency_name]['count'] = $row['count'];
			$currency_data[$category][$currency_name]['type'] = $row['type'];
			$currency_data[$category][$currency_name]['icon'] = $row['icon'];
			$currency_data[$category][$currency_name]['tooltip'] = makeOverlib($row['tooltip'], '', '', 0, $this->data['clientLocale']);
		}

		$roster->db->free_result($result);

		foreach( $currency_data as $category => $currency )
		{
			$roster->tpl->assign_block_vars('currency', array(
				'ID' => strtolower(str_replace(' ', '', $category)),
				'CATEGORY' => $category
				)
			);

			foreach( $currency as $name => $data )
			{
				$roster->tpl->assign_block_vars('currency.item', array(
					'ID' => strtolower(str_replace(' ', '', $name)),
					'NAME' => $name,
					'COUNT' => $data['count'],
					'TYPE' => $data['type'],
					'ORDER' => $data['order'],
					'ICON' => $data['icon'],
					'TOOLTIP' => $data['tooltip']
					)
				);
			}
		}

		return true;
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

	/**
		got pissed off with tryen to fix old talent system .. so now im just gona build a better faster one...
		NumTalents == 18 ALLWAYS
		3 colums 
		6 rows
		1..1 talent tree per class
		here we go
	*/
	
	function show_talents( )
	{
		global $roster, $addon;

		$sqlquery = "SELECT "
			. " `builds`.`tree`,"
			. " `builds`.`build`,"
			. " `builds`.`spec`,"
			. " `specs`.`order`,"
			. " `specs`.`pointsspent` "
			. "FROM `" . $roster->db->table('talent_builds') . "` as builds "
			. "LEFT JOIN `".$roster->db->table('talenttree')."` AS specs ON `builds`.`member_id` = `specs`.`member_id`  "
			. " WHERE `builds`.`member_id` = '" . $this->data['member_id'] . "' and `specs`.`build` = `builds`.`build`"
			. " ORDER BY `specs`.`order` ASC,`builds`.`build` ASC;";

		$result = $roster->db->query($sqlquery);

		$spec = array(); 
		
		while( $t = $roster->db->fetch($result, SQL_ASSOC) )
		{
			//d($t);
			$spec[$t['build']]=array();
			$spec[$t['build']]['build'] = $t['build'];
			$spec[$t['build']]['spec'] = $t['spec'];
			$spec[$t['build']]['order'] = $t['order'];
			//$spec[$t['build']]['spent'] = $t['pointsspent'];
		}

		$tree_rows = $roster->db->num_rows($result);
		
		$specs = $this->build_spec_data($this->data['classid']);// build the possable spec trees
		$talents = $this->build_talent_data($this->data['classid']);// build the list of talents
		
		// time to build some damn talents
		// Talent data and build spec data
		$talentdata = $specdata = array();

		// Temp var for talent spec detection
		$spec_points_temp = array();
		//d($specs);
		//echo '<pre>';		print_r($spec);		echo '</pre>';
		foreach($spec as $build => $build_data)
		{
			$spc = $build;

			$order = $build_data['order'];
			$treeindex = $build.'t';
			//echo $build_data['spec'].'<br>';
			$specdata[$build]['order'] = $order;
			$specdata[$build]['name'] = $build_data['spec'];
			$specdata[$build]['role'] = $specs[$build_data['spec']]['roles'];
			$specdata[$build]['icon'] = $specs[$build_data['spec']]['icon'];
			// Store our talent points for later use

			// Set talent tree data
			$talentdata[$build]['name'] = $build_data['spec'];
			$talentdata[$build]['image'] = $specs[$build_data['spec']]['background'];
			//$talentdata[$build]['points'] = $build_data['spent'];
			$talentdata[$build]['role'] =  strtolower($specs[$build_data['spec']]['roles']);
			$talentdata[$build]['talents'] = $talents;
			
			$roster->tpl->assign_block_vars('spec', array(
				'TALENT_EXPORT' => sprintf($roster->locale->act['export_url2'], strtolower($roster->locale->act['id_to_class'][$this->data['classid']]), $build_data['build']),
				// old code keeping for now  sprintf($roster->locale->act['export_url'], $this->data['classid'], $builddata),
				'ID'    => $build,
				'TID'	=> $treeindex,
				'NAME'  => $specdata[$build]['name'],
				'ROLE'  => strtolower($specdata[$build]['role']),
				'TYPE'  => $roster->locale->act['talent_build_' . ($build_data['order'] == 1 ? 0 : 1)],
				//'BUILD' => $build_data['spent'],
				'ICON'  => $specs[$build_data['spec']]['icon'],
				'SELECTED' => ($build_data['order'] == 1 ? true : false)
				)
			);
			$talentArray = preg_split('//', $build_data['build'], -1, PREG_SPLIT_NO_EMPTY);
			
			$roster->tpl->assign_block_vars('spec.build', array(
					'L_POINTS_SPENT' => sprintf($roster->locale->act['pointsspent'], $build_data['spec']),
					'NAME' => $specdata[$build]['name'],
					'ROLE'  => $specdata[$build]['role'],
					'ID'    => $build,
					'TID'	=> $treeindex,
					'ICON' => $specs[$build_data['spec']]['background'],
					'HSELECT' => true,
					'SELECTED' => ($build_data['order'] == 1 ? true : false)
					)
				);
				$i=0;
				$unl = 15;
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
			$talents[$row['build']][$row['row']]['tooltip'] = makeOverlib($row['talent_id'], $row['name'], '', 2,'','','talent');//$row['tooltip'];
			$talents[$row['build']][$row['row']]['icon'] = $row['texture'];
			//$talents[$row['build']][$row['row']]['isspell'] = $row['isspell'];
			//$talents[$row['build']][$row['row']]['spec'] = $row['tree_order'];
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
	 * Build Gyphs
	 *
	 * @return bool
	 */
	function show_glyphs()
	{
		global $roster, $addon;

		$query = "SELECT * FROM `" . $roster->db->table('glyphs') . "`"
			. " WHERE `member_id` = '" . $this->data['member_id'] . "'"
			. " ORDER BY `glyph_build`, `glyph_type` ASC;";

		$result = $roster->db->query($query);

		if( !$result )
		{
			return false;
		}

		$num_glyphs = $roster->db->num_rows($result);

		if( $num_glyphs == 0 )
		{
			return false;
		}

		$glyph_data = array();
		while($row = $roster->db->fetch($result))
		{
			$glyph_data = array('0'=>array(),'1' => array());
			$glyph_build = $row['glyph_build'];
			$glyph_order = $row['glyph_type'];
			$glyph_data[$glyph_build][$glyph_order][] = array(
				'type' => $row['glyph_type'],
				'name' => $row['glyph_name'],
				'icon' => $row['glyph_icon'],
				'tooltip' => makeOverlib($row['glyph_tooltip'], '', '', 0, $this->data['clientLocale']),
			);
		}

		// Figure out build names
		$sqlquery = "SELECT `build`, `tree`, `background`, `pointsspent`"
			. " FROM `" . $roster->db->table('talenttree') . "`"
			. " WHERE `member_id` = '" . $this->data['member_id'] . "'"
			. " ORDER BY `build`, `order`;";

		$trees = $roster->db->query($sqlquery);

		$tree_rows = $roster->db->num_rows($trees);

		if( $tree_rows > 0 )
		{
			// Talent data and build spec data
			$specdata = array();

			// Temp var for talent spec detection
			$spec_points_temp = array();

			// Loop each mysql row and build arrays
			for( $j=0; $j < $tree_rows; $j++)
			{
				$treedata = $roster->db->fetch($trees, SQL_ASSOC);

				// Get the order and the build numbers
				$talent_build = $treedata['build'];

				// Checking for build spec

				// Sets initial value if it doesnt exist
				if( !isset($spec_points_temp[$talent_build]) )
				{
					$spec_points_temp[$talent_build] = $treedata['pointsspent'];
					$specdata[$talent_build]['name'] = $treedata['tree'];
					$specdata[$talent_build]['icon'] = $treedata['background'];
				}
				elseif( $treedata['pointsspent'] > $spec_points_temp[$talent_build] )
				{
					$specdata[$talent_build]['name'] = $treedata['tree'];
					$specdata[$talent_build]['icon'] = $treedata['background'];

					// Store highest tree points to temp var
					$spec_points_temp[$talent_build] = $treedata['pointsspent'];
				}
			}
		}

		$roster->db->free_result($result);

		foreach( $glyph_data as $build => $glyph_order )
		{
			$roster->tpl->assign_block_vars('glyphs',array(
				'ID'   => $build,
				'NAME' => $specdata[$build]['name'],
				'ICON' => $specdata[$build]['icon'],
				)
			);
			foreach( $glyph_order as $order => $gl )
			{
				foreach( $gl as $or => $glyph )
				{
					if( $glyph['name'] != '' )
					{
						$roster->tpl->assign_block_vars('glyphs.glyph',array(
							'TYPE'    => $glyph['type'],
							'NAME'    => $glyph['name'],
							'ORDER'   => $order,
							'ID'      => strtolower(str_replace(' ','',$glyph['name'])),
							'ICON'    => $glyph['icon'],
							'TOOLTIP' => $glyph['tooltip'],
							)
						);
					}
				}
			}
		}
		return true;
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
					'NAME_ID' => $this->locale['skill_to_id'][$skill['name']]
					)
				);

				foreach( $skill['bars'] as $skillbar )
				{
					$roster->tpl->assign_block_vars('skill.bar',array(
						'NAME'     => $skillbar['name'],
						'WIDTH'    => $skillbar['barwidth'],
						'VALUE'    => $skillbar['value'],
						'MAXVALUE' => $skillbar['maxvalue'],
						'ICON'     => $this->locale['ts_iconArray'][$skillbar['name']]
						)
					);

					if( $skill['name'] == $this->locale['professions'] )
					{
						$roster->tpl->assign_block_vars('professions',array(
							'NAME'     => $skillbar['name'],
							'WIDTH'    => $skillbar['barwidth'],
							'VALUE'    => $skillbar['value'],
							'MAXVALUE' => $skillbar['maxvalue'],
							'ICON'     => $this->locale['ts_iconArray'][$skillbar['name']]
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
	 * Build character reputation
	 *
	 * @return mixed Array on success, false on fail
	 */
	function show_reputation()
	{
		global $roster, $addon;

		$repData = $this->_rep_tab_values();

		//echo '<pre>';print_r($repData);echo '</pre>';

		if( is_array($repData) )
		{
			foreach( $repData as $findex => $faction )
			{
//				echo $findex.'<br>';
//				aprint($faction);

				$roster->tpl->assign_block_vars('rep',array(
					'ID'      => $findex,
					'NAME'    => $findex,
					'NAME_ID' => $this->locale['faction_to_id'][$findex]
					)
				);

				foreach( $faction as $rep => $bar )
				{
//					echo $rep.' - '.$bar .'<br>';

					if (isset($bar['value']))
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
							'SUB'			=> (isset($bar['sub']) ? '1' : '0' ),
							)
						);
					}
					else
					{
						$roster->tpl->assign_block_vars('rep.bar.rep2',array(
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
							if ($fact != 'sub')
							{
//								echo $fact . '<br/>';
								$roster->tpl->assign_block_vars('rep.bar.rep2.bar2',array(
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
				//$repInfo[$data['faction']] =$i; // ]['name'] = $data['faction'];

				if( $data['name'] != $data['parent'] && $data['parent']=='')
				{
					$i++;
					$j=0;
					$l++;
					$factions = $data['faction'];
					$repInfo[$factions][$data['name']] = $i++;
					$repInfo[$factions][$data['name']] = $this->_rep_bar_values($data);

				}
				//curr_rep'] != '' && $data['max_rep'] != ''
				if (isset($data['parent']) && $data['curr_rep'] != '' && $data['max_rep'] != '')//&& $data['parent']!= $data['name'])
				{
					$p=$data['name'];
					$repInfo[$factions][$data['parent']]['sub'] = 'Y';
					$repInfo[$factions][$data['parent']][$data['name']] = $this->_rep_bar_values($data);
					$k++;
				}
				else
				{
					$p='';
				}

				$j++;
			}
			//echo '<pre>';print_r($repInfo);echo '</pre>';
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

		$img = array(
			$this->locale['exalted'] => 'exalted',
			$this->locale['revered'] => 'revered',
			$this->locale['honored'] => 'honored',
			$this->locale['friendly'] => 'friendly',
			$this->locale['neutral'] => 'neutral',
			$this->locale['unfriendly'] => 'unfriendly',
			$this->locale['hostile'] => 'hostile',
			$this->locale['hated'] => 'hated'
		);
		$bimg = array(
			$this->locale['exalted'] => 'rank-7',// .faction-fill { background-position: 0 -88px; }
			$this->locale['revered'] => 'rank-6',// .faction-fill { background-position: 0 -66px; }
			$this->locale['honored'] => 'rank-5',// .faction-fill { background-position: 0 -66px; }
			$this->locale['friendly'] => 'rank-4',// .faction-fill { background-position: 0 -66px; }
			$this->locale['neutral'] => 'rank-3',// .faction-fill { background-position: 0 -44px; }
			$this->locale['unfriendly'] => 'rank-2',// .faction-fill { background-position: 0 -22px; }
			$this->locale['hostile'] => 'rank-1',// .faction-fill { background-position: 0 -22px; }
			$this->locale['hated'] => 'rank-0',// .faction-fill { background-position: 0 -22px; }
		);


		$returnData['name'] = $repdata['name'];
		$returnData['barwidth'] = ceil($level / $max * 100);
		$returnData['image'] = $img[$repdata['Standing']];
		$returnData['fill'] = $bimg[$repdata['Standing']];
		$returnData['barid'] = $repnum;
		$returnData['standing'] = $repdata['Standing'];
		$returnData['description'] = $repdata['Description'];
		$returnData['value'] = $level;
		$returnData['maxvalue'] = $max;
		$returnData['atwar'] = $repdata['AtWar'];

		$repnum++;

		return $returnData;
	}


	/**
	 * Build pvp stats
	 *
	 * @return string
	 */
	function show_pvp()
	{
		global $roster;

		$roster->tpl->assign_vars(array(
			'HONOR_POINTS' => $this->data['honorpoints'],
			'ARENA_POINTS' => $this->data['arenapoints'],
			'SESSION_HK'   => $this->data['sessionHK'],
			'YEST_HK'      => $this->data['yesterdayHK'],
			'LIFE_HK'      => $this->data['lifetimeHK'],
			'SESSION_CP'   => $this->data['sessionCP'],
			'YEST_CP'      => $this->data['yesterdayContribution']
			)
		);
	}


	/**
	 * Build Pet
	 *
	 * @return bool
	 */
	function show_pets()
	{
		global $roster, $addon;

		$query = "SELECT * FROM `" . $roster->db->table('pets') . "` WHERE `member_id` = '" . $this->data['member_id'] . "';";
		$result = $roster->db->query( $query );

		$petNum = 0;
		if( $roster->db->num_rows($result) > 0 )
		{
			$roster->tpl->assign_var('S_PETS',true);

			while ($row = $roster->db->fetch($result, SQL_ASSOC))
			{
				$expbar_show = true;
				$expbar_amount = $expbar_max = '';

				if( $row['level'] == ROSTER_MAXCHARLEVEL )
				{
					$exp_percent = 100;
					$expbar_text = $roster->locale->act['max_exp'];
				}
				else
				{
					$xp = explode(':',$row['xp']);
					if( isset($xp[1]) && $xp[1] != '0' && $xp[1] != '' )
					{
						$exp_percent = ( $xp[1] > 0 ? floor($xp[0] / $xp[1] * 100) : 0);

						$expbar_amount = $xp[0];
						$expbar_max = $xp[1];
					}
					else
					{
						$expbarshow = false;
						$exp_percent = 0;

					}
				}


				// Start Warlock Pet Icon Fix
				if( $row['type'] == $this->locale['Imp'] )
				{
					$row['icon'] = 'spell_shadow_summonimp';
				}
				elseif( $row['type'] == $this->locale['Voidwalker'] )
				{
					$row['icon'] = 'spell_shadow_summonvoidwalker';
				}
				elseif( $row['type'] == $this->locale['Succubus'] )
				{
					$row['icon'] = 'spell_shadow_summonsuccubus';
				}
				elseif( $row['type'] == $this->locale['Felhunter'] )
				{
					$row['icon'] = 'spell_shadow_summonfelhunter';
				}
				elseif( $row['type'] == $this->locale['Felguard'] )
				{
					$row['icon'] = 'spell_shadow_summonfelguard';
				}
				elseif( $row['type'] == $this->locale['Infernal'] )
				{
					$row['icon'] = 'spell_shadow_summoninfernal';
				}
				// End Warlock Pet Icon Fix

				if( $row['icon'] == '' || !isset($row['icon']) )
				{
					$row['icon'] = 'inv_misc_questionmark';
				}

				$roster->tpl->assign_block_vars('pet',array(
					'ID'        => $petNum,
					'NAME'      => stripslashes($row['name']),
					'LEVEL'     => $row['level'],
					'TYPE'      => stripslashes($row['type']),
					'HEALTH'    => (isset($row['health']) ? $row['health'] : '0'),
					'POWER'     => (isset($row['mana']) ? $row['mana'] : '0'),
					'ICON'      => $row['icon'],
					'TOTAL_TP'  => $row['totaltp'],

					'TOOLTIP' => makeOverlib($row['name'],$row['type'],'',2,'',',WRAP'),

					'L_POWER' => $row['power'],

					'S_EXP'      => $expbar_show,
					'EXP_AMOUNT' => $expbar_amount,
					'EXP_MAX'    => $expbar_max,
					'EXP_PERC'   => $exp_percent,
					)
				);

				// Print Resistance
				$this->pet_resist('arcane',$row);
				$this->pet_resist('fire',$row);
				$this->pet_resist('nature',$row);
				$this->pet_resist('frost',$row);
				$this->pet_resist('shadow',$row);

				// Print stats boxes
				$roster->tpl->assign_block_vars('pet.box_stats',array());
				$this->pet_stat('stat_str',$row);
				$this->pet_stat('stat_agl',$row);
				$this->pet_stat('stat_sta',$row);
				$this->pet_stat('stat_int',$row);
				$this->pet_stat('stat_spr',$row);
				$this->pet_stat('stat_armor',$row);

				$roster->tpl->assign_block_vars('pet.box_stats',array());
				$this->pet_wskill($row);
				$this->pet_wdamage($row);
				$this->pet_stat('melee_power',$row);
				$this->pet_stat('melee_hit',$row);
				$this->pet_stat('melee_crit',$row);
				$this->pet_resilience($row);

				$petNum++;
			}
		}
		return (bool)$petNum;
	}


	/**
	 * Build Pet stats
	 *
	 * @param string $statname
	 * @param array $data
	 * @return string
	 */
	function pet_stat( $statname , $data )
	{
		global $roster;

		switch( $statname )
		{
			case 'stat_str':
				$name = $roster->locale->act['strength'];
				$tooltip = $roster->locale->act['strength_tooltip'];
				break;

			case 'stat_int':
				$name = $roster->locale->act['intellect'];
				$tooltip = $roster->locale->act['intellect_tooltip'];
				break;

			case 'stat_sta':
				$name = $roster->locale->act['stamina'];
				$tooltip = $roster->locale->act['stamina_tooltip'];
				break;

			case 'stat_spr':
				$name = $roster->locale->act['spirit'];
				$tooltip = $roster->locale->act['spirit_tooltip'];
				break;

			case 'stat_agl':
				$name = $roster->locale->act['agility'];
				$tooltip = $roster->locale->act['agility_tooltip'];
				break;

			case 'stat_armor':
				$name = $roster->locale->act['armor'];
				$tooltip = sprintf($roster->locale->act['armor_tooltip'],$this->data['mitigation']);
				break;
			
			

			case 'melee_power':
				$lname = $roster->locale->act['melee_att_power'];
				$name = $roster->locale->act['power'];
				$tooltip = sprintf($roster->locale->act['melee_att_power_tooltip'], $data['melee_power_dps']);
				break;

			case 'melee_hit':
				$name = $roster->locale->act['weapon_hit_rating'];
				$tooltip = $roster->locale->act['weapon_hit_rating_tooltip'];
				break;

			case 'melee_crit':
				$name = $roster->locale->act['weapon_crit_rating'];
				$tooltip = sprintf($roster->locale->act['weapon_crit_rating_tooltip'], $data['melee_crit_chance']);
				break;
		}

		if( isset($lname) )
		{
			$tooltipheader = $lname . ' ' . $this->rating_long($statname,$data);
		}
		else
		{
			$tooltipheader = $name . ' ' . $this->rating_long($statname,$data);
		}

		$line = '<span style="color:#DFB801;">' . $tooltip . '</span>';

		$this->pet_stat_line($name, $this->rating_short($statname,$data), $line, $tooltipheader);
	}


	/**
	 * Build Pet weapon skill
	 *
	 * @param array $data
	 * @return string
	 */
	function pet_wskill( $data )
	{
		global $roster;

		$value = '<strong class="white">' . $data['melee_mhand_skill'] . '</strong>';
		$name = $roster->locale->act['weapon_skill'];
		$tooltipheader = $roster->locale->act['mainhand'];
		$tooltip = sprintf($roster->locale->act['weapon_skill_tooltip'], $data['melee_mhand_skill'], $data['melee_mhand_rating']);

		$line = '<span style="color:#DFB801;">' . $tooltip . '</span>';

		$this->pet_stat_line($name, $value, $line, $tooltipheader);
	}


	/**
	 * Build Pet weapon damage
	 *
	 * @param array $data
	 * @return string
	 */
	function pet_wdamage( $data )
	{
		global $roster;

		$value = '<strong class="white">' . $data['melee_mhand_mindam'] . '</strong>' . '-' . '<strong class="white">' . $data['melee_mhand_maxdam'] . '</strong>';
		$name = $roster->locale->act['damage'];
		$tooltipheader = $roster->locale->act['mainhand'];
		$tooltip = sprintf($roster->locale->act['damage_tooltip'], $data['melee_mhand_speed'], $data['melee_mhand_mindam'], $data['melee_mhand_maxdam'], $data['melee_mhand_dps']);

		$line = '<span style="color:#DFB801;">' . $tooltip . '</span>';

		$this->pet_stat_line($name, $value, $line, $tooltipheader);
	}


	/**
	 * Build Pet resists
	 *
	 * @param string $resname
	 * @param array $data
	 * @return string
	 */
	function pet_resist( $resname , $data )
	{
		global $roster;

		switch( $resname )
		{
			case 'fire':
				$name = $roster->locale->act['res_fire'];
				$tooltip = $roster->locale->act['res_fire_tooltip'];
				$color = 'red';
				break;

			case 'nature':
				$name = $roster->locale->act['res_nature'];
				$tooltip = $roster->locale->act['res_nature_tooltip'];
				$color = 'green';
				break;

			case 'arcane':
				$name = $roster->locale->act['res_arcane'];
				$tooltip = $roster->locale->act['res_arcane_tooltip'];
				$color = 'yellow';
				break;

			case 'frost':
				$name = $roster->locale->act['res_frost'];
				$tooltip = $roster->locale->act['res_frost_tooltip'];
				$color = 'blue';
				break;

			case 'shadow':
				$name = $roster->locale->act['res_shadow'];
				$tooltip = $roster->locale->act['res_shadow_tooltip'];
				$color = 'purple';
				break;
		}

		$tooltip = '<span style="color:' . $color . ';font-size:11px;font-weight:bold;">' . $name . '</span> ' . $this->rating_long('res_' . $resname,$data) . '<br />'
				 . '<span style="color:#DFB801;text-align:left;">' . $tooltip . '</span>';

		$roster->tpl->assign_block_vars('pet.resist',array(
			'NAME'  => $name,
			'CLASS' => $resname,
			'COLOR' => $color,
			'VALUE' => $data['res_' . $resname . '_c'],
			'TOOLTIP' => makeOverlib($tooltip,'','',2,'',''),
			)
		);
	}


	/**
	 * Build Pet resilience
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	function pet_resilience( $data )
	{
		global $roster;

		$name = $roster->locale->act['resilience'];
		$value = min($data['stat_res_melee'],$data['stat_res_ranged'],$data['stat_res_spell']);

		$tooltipheader = $name;
		$tooltip  = '<div><span style="float:right;">' . $data['stat_res_melee'] . '</span>' . $roster->locale->act['melee'] . '</div>';
		$tooltip .= '<div><span style="float:right;">' . $data['stat_res_ranged'] . '</span>' . $roster->locale->act['ranged'] . '</div>';
		$tooltip .= '<div><span style="float:right;">' . $data['stat_res_spell'] . '</span>' . $roster->locale->act['spell'] . '</div>';


		$line = '<span style="color:#DFB801;">' . $tooltip . '</span>';

		$this->pet_stat_line($name, '<strong class="white">' . $value . '</strong>', $line, $tooltipheader);
	}


	/**
	 * Build stat line
	 *
	 * @param string $label
	 * @param string $value
	 * @param string $tooltip
	 * @param string $caption
	 * @return string
	 */
	function pet_stat_line( $label , $value , $tooltip, $caption )
	{
		global $roster;

		$roster->tpl->assign_block_vars('pet.box_stats.statline',array(
			'NAME'  => $label,
			'VALUE' => $value,
			'TOOLTIP' => makeOverlib($tooltip,$caption,'',2,'','')
			)
		);
	}


	/**
	 * Build stat line
	 *
	 * @param string $label
	 * @param string $value
	 * @param string $tooltip
	 * @param string $caption
	 * @return string
	 */
	function stat_line( $label , $value , $tooltip, $caption )
	{
		global $roster;

		$roster->tpl->assign_block_vars('box_stats.statline',array(
			'NAME'  => $label,
			'VALUE' => $value,
			//'TOOLTIP' => 'data-tooltip="text-' .htmlspecialchars(htmlentities($tooltip)).'" data-caption="'.$caption.'"',//
			'TOOLTIP' => makeOverlib($tooltip,$caption,'',2,'','')
			)
		);
	}


	/**
	 * Build short rating value
	 *
	 * @param string $statname
	 * @param array $data_or Alternative data to use
	 * @return string
	 */
	function rating_short( $statname , $data_or=false )
	{
		if( $data_or == false )
		{
			$data = $this->data;
		}
		else
		{
			$data = $data_or;
		}

		$base = $data[$statname];
		$current = $data[$statname . '_c'];
		$buff = $data[$statname . '_b'];
		$debuff = -$data[$statname . '_d'];

		if( $buff > 0 && $debuff > 0 )
		{
			$color = 'purple';
		}
		elseif( $buff > 0 )
		{
			$color = 'green';
		}
		elseif( $debuff > 0 )
		{
			$color = 'red';
		}
		else
		{
			$color = 'white';
		}

		return '<strong class="' . $color . '">' . $current . '</strong>';
	}


	/**
	 * Build long rating value
	 *
	 * @param string $statname
	 * @param array $data_or Alternative data to use
	 * @return string
	 */
	function rating_long( $statname , $data_or=false )
	{
		if( $data_or == false )
		{
			$data = $this->data;
		}
		else
		{
			$data = $data_or;
		}

		$base = $data[$statname];
		$current = $data[$statname];
		$buff = $data[$statname];
		$debuff = -$data[$statname];

		$tooltipheader = $current;

		if( $base != $current)
		{
			$tooltipheader .= " ($base";
			if( $buff > 0 )
			{
				$tooltipheader .= ' <span class=\'green\'>+ ' . $buff . '</span>';
			}
			if( $debuff > 0 )
			{
				$tooltipheader .= ' <span class=\'red\'>- ' . $debuff . '</span>';
			}
			$tooltipheader .= ')';
		}

		return $tooltipheader;
	}


	/**
	 * Build a status box
	 *
	 * @param string $cat
	 * @param string $side
	 * @param bool $visible
	 */
	function status_box( $cat , $side , $visible=false )
	{
		global $roster;

		$roster->tpl->assign_block_vars('box_stats',array(
			'ID'   => $cat . $side,
			'SHOW' => $visible
			)
		);

		switch( $cat )
		{
			case 'stats':
				$this->box_stat_line('stat_str');
				$this->box_stat_line('stat_agl');
				$this->box_stat_line('stat_sta');
				$this->box_stat_line('stat_int');
				$this->box_stat_line('stat_spr');
				$this->box_stat_line('stat_armor');
				$this->status_value('mastery');
				break;

			case 'melee':
				$this->wdamage('melee');
				$this->wspeed('melee');
				$this->box_stat_line('melee_power');
				$this->box_stat_line('melee_hit');
				$this->box_stat_line('melee_crit');
				$this->box_stat_line('melee_expertise');
				break;

			case 'ranged':
				//$this->wskill('ranged');
				$this->wdamage('ranged');
				$this->wspeed('ranged');
				$this->box_stat_line('ranged_power');
				$this->box_stat_line('ranged_hit');
				$this->box_stat_line('ranged_crit');
				break;

			case 'spell':
				//$this->spell_damage();
				$this->status_value('spell_damage');
				$this->box_stat_line('spell_hit');
				$this->spell_crit();
				$this->status_value('spell_penetration');
				$this->status_value('mana_regen');
				break;

			case 'defense':
				$this->box_stat_line('stat_armor');
				$this->defense_rating();
				$this->defense_line('dodge');
				$this->defense_line('parry');
				$this->defense_line('block');
				$this->resilience();
				break;
		}
	}


	/**
	 * Build a status line
	 *
	 * @param string $statname
	 * @return string
	 */
	function box_stat_line( $statname )
	{
		global $roster;

		switch( $statname )
		{
			case 'stat_str':
				$name = $roster->locale->act['strength'];
				$tooltip = $roster->locale->act['strength_tooltip'];
				break;

			case 'stat_int':
				$name = $roster->locale->act['intellect'];
				$tooltip = $roster->locale->act['intellect_tooltip'];
				break;

			case 'stat_sta':
				$name = $roster->locale->act['stamina'];
				$tooltip = $roster->locale->act['stamina_tooltip'];
				break;

			case 'stat_spr':
				$name = $roster->locale->act['spirit'];
				$tooltip = $roster->locale->act['spirit_tooltip'];
				break;

			case 'stat_agl':
				$name = $roster->locale->act['agility'];
				$tooltip = $roster->locale->act['agility_tooltip'];
				break;

			case 'stat_armor':
				$name = $roster->locale->act['armor'];
				$tooltip = sprintf($roster->locale->act['armor_tooltip'],$this->data['mitigation']);
				break;
			
			case 'melee_power':
				$lname = $roster->locale->act['melee_att_power'];
				$name = $roster->locale->act['power'];
				$tooltip = sprintf($roster->locale->act['melee_att_power_tooltip'], $this->data['melee_power_dps']);
				break;

			case 'melee_hit':
				$name = $roster->locale->act['weapon_hit_rating'];
				$tooltip = $roster->locale->act['weapon_hit_rating_tooltip'];
				break;

			case 'melee_expertise':
				$name = $roster->locale->act['weapon_expertise'];
				$tooltip = $roster->locale->act['weapon_expertise_tooltip'];
				break;

			case 'melee_crit':
				$name = $roster->locale->act['weapon_crit_rating'];
				$tooltip = sprintf($roster->locale->act['weapon_crit_rating_tooltip'], $this->data['melee_crit_chance']);
				break;

			case 'ranged_power':
				$lname = $roster->locale->act['ranged_att_power'];
				$name = $roster->locale->act['power'];
				$tooltip = sprintf($roster->locale->act['ranged_att_power_tooltip'], $this->data['ranged_power_dps']);
				break;

			case 'ranged_hit':
				$name = $roster->locale->act['weapon_hit_rating'];
				$tooltip = $roster->locale->act['weapon_hit_rating_tooltip'];
				break;

			case 'ranged_crit':
				$name = $roster->locale->act['weapon_crit_rating'];
				$tooltip = sprintf($roster->locale->act['weapon_crit_rating_tooltip'], $this->data['ranged_crit_chance']);
				break;

			case 'spell_hit':
				$name = $roster->locale->act['spell_hit_rating'];
				$tooltip = $roster->locale->act['spell_hit_rating_tooltip'];
				break;
		}

		if( isset($lname) )
		{
			$tooltipheader = $lname . ' ' . $this->rating_long($statname);
		}
		else
		{
			$tooltipheader = $name . ' ' . $this->rating_long($statname);
		}

		$line = '<span style="color:#DFB801;">' . $tooltip . '</span>';

		$this->stat_line($name, $this->rating_short($statname), $line, $tooltipheader);
	}


	/**
	 * Build a special status line
	 *
	 * @param string $statname
	 * @return unknown
	 */
	function status_value( $statname )
	{
		global $roster;

		$value = $this->data[$statname];
		switch( $statname )
		{
			case 'spell_penetration':
				$name = $roster->locale->act['spell_penetration'];
				$tooltip = $roster->locale->act['spell_penetration_tooltip'];
				break;

			case 'mana_regen':
				$name = $roster->locale->act['mana_regen'];
				$tooltip = sprintf($roster->locale->act['mana_regen_tooltip'],$this->data['mana_regen'],$this->data['mana_regen_cast']);
				break;

			case 'spell_healing':
				$name = $roster->locale->act['spell_healing'];
				$tooltip = sprintf($roster->locale->act['spell_healing_tooltip'],$this->data['spell_healing']);
				break;
			case 'spell_damage':
				$name = $roster->locale->act['spell_damage'];
				$tooltip = sprintf($roster->locale->act['spell_damage_tooltip'],$this->data['spell_damage']);
				break;
			case 'mastery':
				$name = $roster->locale->act['mastery'];
				$tooltip = $this->data['mastery_tooltip'];
				break;
		}

		$tooltipheader = (isset($name) ? $name : '');

		$line = '<span style="color:#DFB801;">' . $tooltip . '</span>';

		$this->stat_line($name, '<strong class="white">' . $value . '</strong>', $line, $tooltipheader);
	}


	/**
	 * Build weapon skill
	 *
	 * @param string $location
	 * @return string
	 */
	function wskill( $location )
	{
		global $roster;

		if( $location == 'ranged' )
		{
			$value = '<strong class="white">' . $this->data['ranged_skill'] . '</strong>';
			$name = $roster->locale->act['weapon_skill'];
			$tooltipheader = $roster->locale->act['ranged'];
			$tooltip = sprintf($roster->locale->act['weapon_skill_tooltip'], $this->data['ranged_skill'], $this->data['ranged_rating']);

			$line = '<div style="color:#DFB801;">' . $tooltip . '</div>';
		}
		else
		{
			$value = '<strong class="white">' . $this->data['melee_mhand_skill'] . '</strong>';
			$name = $roster->locale->act['weapon_skill'];
			$tooltipheader = $roster->locale->act['mainhand'];
			$tooltip = sprintf($roster->locale->act['weapon_skill_tooltip'], $this->data['melee_mhand_skill'], $this->data['melee_mhand_rating']);

			$line = '<span style="color:#DFB801;">' . $tooltip . '</span>';

			if( $this->data['melee_ohand_dps'] > 0 )
			{
				$value .= '/<strong class="white">' . $this->data['melee_ohand_skill'] . '</strong>';
				$ohandheader = $roster->locale->act['offhand'];
				$tooltip = sprintf($roster->locale->act['weapon_skill_tooltip'], $this->data['melee_ohand_skill'], $this->data['melee_ohand_rating']);

				$line .= '<br /><span style="color:#ffffff;font-size:11px;font-weight:bold;">' . $ohandheader . '</span><br />'
					   . '<div style="color:#DFB801;">' . $tooltip . '</div>';
			}
		}

		$this->stat_line($name, $value, $line, $tooltipheader);
	}


	/**
	 * Build weapon damage
	 *
	 * @param string $location
	 * @return string
	 */
	function wdamage( $location )
	{
		global $roster;

		if( $location == 'ranged' )
		{
			$value = '<strong class="white">' . $this->data['ranged_mindam'] . '</strong>' . '-' . '<strong class="white">' . $this->data['ranged_maxdam'] . '</strong>';
			$name = $roster->locale->act['damage'];
			$tooltipheader = $roster->locale->act['ranged'];
			$tooltip = sprintf($roster->locale->act['damage_tooltip'], $this->data['ranged_speed'], $this->data['ranged_mindam'], $this->data['ranged_maxdam'], $this->data['ranged_dps']);

			$line = '<div style="color:#DFB801;">' . $tooltip . '</div>';
		}
		else
		{
			$value = '<strong class="white">' . $this->data['melee_mhand_mindam'] . '</strong>-<strong class="white">' . $this->data['melee_mhand_maxdam'] . '</strong>';
			$name = $roster->locale->act['damage'];
			$tooltipheader = $roster->locale->act['mainhand'];
			$tooltip = sprintf($roster->locale->act['damage_tooltip'], $this->data['melee_mhand_speed'], $this->data['melee_mhand_mindam'], $this->data['melee_mhand_maxdam'], $this->data['melee_mhand_dps']);

			$line = '<div style="color:#DFB801;">' . $tooltip . '</div>';

			if( $this->data['melee_ohand_dps'] > 0 )
			{
				// This will only print then there is no main hand data because printing both stats is too long for the box
				if( empty($this->data['melee_mhand_mindam']) )
				{
					$value .= '<strong class="white">' . $this->data['melee_ohand_mindam'] . '</strong>-<strong class="white">' . $this->data['melee_ohand_maxdam'] . '</strong>';
				}
				$mindam_header = $roster->locale->act['offhand'];
				$tooltip = sprintf($roster->locale->act['damage_tooltip'], $this->data['melee_ohand_speed'], $this->data['melee_ohand_mindam'], $this->data['melee_ohand_maxdam'], $this->data['melee_ohand_dps']);

				$line .= '<br /><span style="color:#ffffff;font-size:11px;font-weight:bold;">' . $mindam_header . '</span><br />'
					   . '<div style="color:#DFB801;">' . $tooltip . '</div>';
			}
		}

		$this->stat_line($name, $value, $line, $tooltipheader);
	}


	/**
	 * Build weapon speed
	 *
	 * @param string $location
	 * @return string
	 */
	function wspeed( $location )
	{
		global $roster;

		if( $location == 'ranged' )
		{
			$value = '<strong class="white">' . $this->data['ranged_speed'] . '</strong>';
			$name = $roster->locale->act['speed'];
			$tooltipheader = $roster->locale->act['atk_speed'] . ' ' . $this->data['ranged_speed'];
			$tooltip = $roster->locale->act['haste_tooltip'] . $this->rating_long('ranged_haste');

			$line = '<span style="color:#DFB801;">' . $tooltip . '</span>';
		}
		else
		{
			$value = '<strong class="white">' . $this->data['melee_mhand_speed'] . '</strong>';
			$name = $roster->locale->act['speed'];

			if( $this->data['melee_ohand_dps'] > 0 )
			{
				$value .= '/<strong class="white">' . $this->data['melee_ohand_speed'] . '</strong>';
			}

			$tooltipheader = $roster->locale->act['atk_speed'] . ' ' . $this->data['melee_mhand_speed'];
			$tooltip = $roster->locale->act['haste_tooltip'] . $this->rating_long('melee_haste');

			$line = '<span style="color:#DFB801;">' . $tooltip . '</span>';
		}

		$this->stat_line($name, $value, $line, $tooltipheader);
	}


	/**
	 * Build spell damage
	 *
	 * @return string
	 */
	function spell_damage()
	{
		global $roster, $addon;

		$name = $roster->locale->act['spell_damage'];
		$value = '<strong class="white">' . $this->data['spell_damage'] . '</strong>';

		$tooltipheader = $name . ' ' . $this->data['spell_damage'];

		$tooltip  = '<div><span style="float:right;">' . $this->data['spell_damage_holy'] . '</span><img src="' . $addon['tpl_image_path'] . 'resist/icon-holy.gif" alt="" />' . $roster->locale->act['holy'] . '</div>';
		$tooltip .= '<div><span style="float:right;">' . $this->data['spell_damage_fire'] . '</span><img src="' . $addon['tpl_image_path'] . 'resist/icon-fire.gif" alt="" />' . $roster->locale->act['fire'] . '</div>';
		$tooltip .= '<div><span style="float:right;">' . $this->data['spell_damage_nature'] . '</span><img src="' . $addon['tpl_image_path'] . 'resist/icon-nature.gif" alt="" />' . $roster->locale->act['nature'] . '</div>';
		$tooltip .= '<div><span style="float:right;">' . $this->data['spell_damage_frost'] . '</span><img src="' . $addon['tpl_image_path'] . 'resist/icon-frost.gif" alt="" />' . $roster->locale->act['frost'] . '</div>';
		$tooltip .= '<div><span style="float:right;">' . $this->data['spell_damage_shadow'] . '</span><img src="' . $addon['tpl_image_path'] . 'resist/icon-shadow.gif" alt="" />' . $roster->locale->act['shadow'] . '</div>';
		$tooltip .= '<div><span style="float:right;">' . $this->data['spell_damage_arcane'] . '</span><img src="' . $addon['tpl_image_path'] . 'resist/icon-arcane.gif" alt="" />' . $roster->locale->act['arcane'] . '</div>';

		$line = '<span style="color:#DFB801;">' . $tooltip . '</span>';

		$this->stat_line($name, $value, $line, $tooltipheader);
	}


	/**
	 * Build spell crit chance
	 *
	 * @return string
	 */
	function spell_crit()
	{
		global $roster, $addon;

		$name = $roster->locale->act['spell_crit_chance'];
		$value = '<strong class="white">' . $this->data['spell_crit_chance'] . '</strong>';

		$tooltipheader = $roster->locale->act['spell_crit_rating'] . ' ' . $this->rating_long('spell_crit');

		$tooltip = '<div><span style="float:right;">' . sprintf('%.2f%%',$this->data['spell_crit_chance_holy']) . '</span><img src="' . $addon['tpl_image_path'] . 'resist/icon-holy.gif" alt="" />' . $roster->locale->act['holy'] . '</div>';
		$tooltip .= '<div><span style="float:right;">' . sprintf('%.2f%%',$this->data['spell_crit_chance_fire']) . '</span><img src="' . $addon['tpl_image_path'] . 'resist/icon-fire.gif" alt="" />' . $roster->locale->act['fire'] . '</div>';
		$tooltip .= '<div><span style="float:right;">' . sprintf('%.2f%%',$this->data['spell_crit_chance_nature']) . '</span><img src="' . $addon['tpl_image_path'] . 'resist/icon-nature.gif" alt="" />' . $roster->locale->act['nature'] . '</div>';
		$tooltip .= '<div><span style="float:right;">' . sprintf('%.2f%%',$this->data['spell_crit_chance_frost']) . '</span><img src="' . $addon['tpl_image_path'] . 'resist/icon-frost.gif" alt="" />' . $roster->locale->act['frost'] . '</div>';
		$tooltip .= '<div><span style="float:right;">' . sprintf('%.2f%%',$this->data['spell_crit_chance_shadow']) . '</span><img src="' . $addon['tpl_image_path'] . 'resist/icon-shadow.gif" alt="" />' . $roster->locale->act['shadow'] . '</div>';
		$tooltip .= '<div><span style="float:right;">' . sprintf('%.2f%%',$this->data['spell_crit_chance_arcane']) . '</span><img src="' . $addon['tpl_image_path'] . 'resist/icon-arcane.gif" alt="" />' . $roster->locale->act['arcane'] . '</div>';

		$line = '<span style="color:#DFB801;">' . $tooltip . '</span>';

		$this->stat_line($name, $value, $line, $tooltipheader);
	}


	/**
	 * Build defense rating value
	 *
	 * @return string
	 */
	function defense_rating()
	{
		global $roster;

		$qry = "SELECT `skill_level` FROM `" . $roster->db->table('skills') . "` WHERE `member_id` = " . $this->data['member_id'] . " AND `skill_name` = '" . $this->locale['defense'] . "';";
		$result = $roster->db->query($qry);

		if( !$result )
		{
			$value = 'N/A';
		}
		else
		{
			$row = $roster->db->fetch($result,SQL_NUM);
			$value = explode(':',$row[0]);
			$value = $value[0];
			$roster->db->free_result($result);
			unset($row);
		}

		$name = $roster->locale->act['defense'];
		$tooltipheader = $name . ' ' . $value;

		$tooltip = $roster->locale->act['defense_rating'] . $this->rating_long('stat_defr');

		$line = '<span style="color:#DFB801;">' . $tooltip . '</span>';

		$this->stat_line($name, '<strong class="white">' . $value . '</strong>', $line, $tooltipheader);
	}


	/**
	 * Build a defense value
	 *
	 * @param string $statname
	 *
	 * @return string
	 */
	function defense_line( $statname )
	{
		global $roster;

		$name = $roster->locale->act[$statname];
		$value = $this->data[$statname];

		$tooltipheader = $name . ' ' . $this->rating_long('stat_' . $statname);
		$tooltip = sprintf($roster->locale->act['def_tooltip'],$name);

		$line = '<span style="color:#DFB801;">' . $tooltip . '</span>';

		$this->stat_line($name, '<strong class="white">' . $value . '%</strong>', $line, $tooltipheader);
	}


	/**
	 * Build resiliance value
	 *
	 * @return string
	 */
	function resilience()
	{
		global $roster;

		$name = $roster->locale->act['resilience'];
		$value = min($this->data['stat_res_melee'],$this->data['stat_res_ranged'],$this->data['stat_res_spell']);

		$tooltipheader = $name;
		$tooltip = '<div><span style="float:right;">' . $this->data['stat_res_melee'] . '</span>' . $roster->locale->act['melee'] . '</div>'
				 . '<div><span style="float:right;">' . $this->data['stat_res_ranged'] . '</span>' . $roster->locale->act['ranged'] . '</div>'
				 . '<div><span style="float:right;">' . $this->data['stat_res_spell'] . '</span>' . $roster->locale->act['spell'] . '</div>';

		$line = '<span style="color:#DFB801;">' . $tooltip . '</span>';

		$this->stat_line($name, '<strong class="white">' . $value . '</strong>', $line, $tooltipheader);
	}


	/**
	 * Build a resistance value
	 *
	 * @param string $resname
	 * @return string
	 */
	function resist_value( $resname )
	{
		global $roster;

		switch( $resname )
		{
			case 'fire':
				$name = $roster->locale->act['res_fire'];
				$tooltip = $roster->locale->act['res_fire_tooltip'];
				$color = 'red';
				break;

			case 'nature':
				$name = $roster->locale->act['res_nature'];
				$tooltip = $roster->locale->act['res_nature_tooltip'];
				$color = 'green';
				break;

			case 'arcane':
				$name = $roster->locale->act['res_arcane'];
				$tooltip = $roster->locale->act['res_arcane_tooltip'];
				$color = 'yellow';
				break;

			case 'frost':
				$name = $roster->locale->act['res_frost'];
				$tooltip = $roster->locale->act['res_frost_tooltip'];
				$color = 'blue';
				break;

			case 'shadow':
				$name = $roster->locale->act['res_shadow'];
				$tooltip = $roster->locale->act['res_shadow_tooltip'];
				$color = 'purple';
				break;
		}

		$tooltip = '<span style="color:' . $color . ';font-size:11px;font-weight:bold;">' . $name . '</span> ' . $this->rating_long('res_'.$resname) . '<br />'
		 . '<span style="color:#DFB801;text-align:left;">' . $tooltip . '</span>';

		$data = $this->data;
		$current = $data['res_'.$resname . '_c'];
		
		$roster->tpl->assign_block_vars('resist',array(
			'NAME'  => $name,
			'CLASS' => $resname,
			'COLOR' => $color,
			'VALUE' => $this->data['res_' . $resname . '_c'],
			'TOOLTIP' => 'data-tooltip="text-' .htmlspecialchars(htmlentities($tooltip)).'"',//makeOverlib($tooltip,'','',2,'',''),
			)
		);
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
			
			//echo $this->slotID[$idata['item_slot']].'<br>';
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
				//'GEMS'		=> $gems,
				'EMPTY'		=> false,
				'ICON'     => $roster->config['interface_url'] . 'Interface/Icons/' . $idata['item_texture'] . '.' . $roster->config['img_suffix'],//$this->equip[$slot]->tpl_get_icon(),
				'TOOLTIP'	=> 'data-tooltip="item-'.$idata['item_id'].'|'.$roster->data['member_id'].'"',
				//'ITEMLINK' => $this->equip[$slot]->tpl_get_itemlink(),
				'QUALITY'  => $idata['item_rarity'],
				);
				//echo '<pre>';print_r($b);echo '</pre>';
			$roster->tpl->assign_block_vars('equipment', $b );
		}
		else
		{
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
				//'GEMS'		=> $gems,
				'EMPTY'		=> true,
				//'SLOT'     => $this->slotID[$idata['item_slot']],
				'ICON'     => $roster->config['img_url'] . 'pixel.gif',
				'TOOLTIP'  => '',//makeOverlib($roster->locale->act['empty_equip'],$roster->locale->act[$slot],'',2,'',',WRAP'),
				'ITEMLINK' => '',
				'QUALITY'  => 'none',
				'QTY'      => 0,
				'S_AMMO'   => $slot == 'Ammo'
				)
			);
		}
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
		//$this->grouping['attack']['mainHandDmgMin']['title1'] = $s['mainHandDmgMin'];
		//$this->grouping['attack']['mainHandDmgMin']['title2'] = $s['mainHandDmgMax'];
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
			//echo $s.' - '.$v.'<br>';
			if ( method_exists($this, $s) ) //function_exists($this->{$s}))
			{
				if ($v > 0)
				{
					//$callable_name($row, $data);
					call_user_func_array(array($this, $s),array($row,$data));
				}
			}
		}
		//echo '<pre>';
		//print_r($this->grouping);
		//print_r($this->data);
		//echo '</pre>';
		foreach ($this->grouping as $cat => $vals)
		{
			//echo $cat.'<br>';
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
		$this->equip_slot('Head');
		$this->equip_slot('Neck');
		$this->equip_slot('Shoulder');
		$this->equip_slot('Back');
		$this->equip_slot('Chest');
		$this->equip_slot('Shirt');
		$this->equip_slot('Tabard');
		$this->equip_slot('Wrist');

		$this->equip_slot('MainHand');
		if( isset($this->equip['SecondaryHand']) )
		{
			$this->equip_slot('SecondaryHand');
		}
		if( isset($this->equip['Ranged']) )
		{
			$this->equip_slot('Ranged');
		}
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

		// Resists
		//$this->resist_value('arcane');
		//$this->resist_value('fire');
		//$this->resist_value('nature');
		//$this->resist_value('frost');
		//$this->resist_value('shadow');

		if( $roster->auth->getAuthorized($addon['config']['show_played']) )
		{
			$TimeLevelPlayedConverted = seconds_to_time($this->data['timelevelplayed']);
			$TimePlayedConverted = seconds_to_time($this->data['timeplayed']);

			$roster->tpl->assign_block_vars('info_stats',array(
				'NAME'  => $roster->locale->act['timeplayed'],
				'VALUE' => $TimePlayedConverted['days'] . $TimePlayedConverted['hours'] . $TimePlayedConverted['minutes'] . $TimePlayedConverted['seconds']
				)
			);

			$roster->tpl->assign_block_vars('info_stats',array(
				'NAME'  => $roster->locale->act['timelevelplayed'],
				'VALUE' => $TimeLevelPlayedConverted['days'] . $TimeLevelPlayedConverted['hours'] . $TimeLevelPlayedConverted['minutes'] . $TimeLevelPlayedConverted['seconds']
				)
			);
		}

		if( $roster->auth->getAuthorized($addon['config']['show_talents']) && $this->data['talent_points'] )
		{
			$roster->tpl->assign_block_vars('info_stats',array(
				'NAME'  => $roster->locale->act['unusedtalentpoints'],
				'VALUE' => $this->data['talent_points']
				)
			);
		}

		// Code to write a "Max Exp bar" just like in SigGen
		$expbar_amount = $expbar_max = $expbar_rest = '';
		if( $this->data['level'] == ROSTER_MAXCHARLEVEL )
		{
			$exp_percent = 100;
			$expbar_amount = $roster->locale->act['max_exp'];
			$expbar_type = 'max';
		}
		elseif( $this->data['exp'] == '0' )
		{
			$exp_percent = 0;
			$expbar_type = 'normal';
		}
		else
		{
			$xp = explode(':',$this->data['exp']);
			if( isset($xp) && $xp[1] != '0' && $xp[1] != '' )
			{
				$exp_percent = ( $xp[1] > 0 ? floor($xp[0] / $xp[1] * 100) : 0);

				$expbar_amount = $xp[0];
				$expbar_max = $xp[1];

				$expbar_rest = ( $xp[2] > 0 ? $xp[2] : '' );
				$expbar_type = ( $xp[2] > 0 ? 'rested' : 'normal' );
			}
		}

		$roster->tpl->assign_vars(array(
			'EXP_AMOUNT' => $expbar_amount,
			'EXP_MAX'    => $expbar_max,
			'EXP_REST'   => $expbar_rest,
			'EXP_PERC'   => $exp_percent,
			'EXP_TYPE'   => $expbar_type,
			)
		);

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
/*
		// Print stat boxes
		$this->status_box('stats','left',true);
		$this->status_box('melee','left');
		$this->status_box('ranged','left');
		$this->status_box('spell','left');
		$this->status_box('defense','left');
		$this->status_box('stats','right');
		$this->status_box('melee','right',$rightbox=='melee');
		$this->status_box('ranged','right',$rightbox=='ranged');
		$this->status_box('spell','right',$rightbox=='spell');
		$this->status_box('defense','right');

		// Buffs
		$this->show_buffs();
		*/

		// PvP
		$this->show_pvp();
		$this->show_talents();
		// Item bonuses
		/*
		if( $roster->auth->getAuthorized($addon['config']['show_item_bonuses']) )
		{
			require_once($addon['inc_dir'] . 'charbonus.lib.php');
			$char_bonus = new CharBonus($this);
			$char_bonus->dumpBonus();
			unset($char_bonus);
		}
		*/


		// Selected default tab
		$select_tab = (isset($_GET['t']) ? $_GET['t'] : 'profile');

		// Print tabs
		$roster->tpl->assign_block_vars('tabs',array(
			'NAME'     => $roster->locale->act['profile'],
			'VALUE'    => 'profile',
			'SELECTED' => $select_tab == 'profile' ? true : false
			)
		);

		// Pet Tab
		if( $roster->auth->getAuthorized($addon['config']['show_pets']) && $this->show_pets() )
		{
			$roster->tpl->assign_block_vars('tabs',array(
				'NAME'     => $roster->locale->act['pets'],
				'VALUE'    => 'pets',
				'SELECTED' => $select_tab == 'pets' ? true : false
				)
			);
		}
		else
		{
			$roster->tpl->assign_var('S_PET_TAB',false);
		}

		// Companion tab
		if( $roster->auth->getAuthorized($addon['config']['show_companions']) && $this->show_companions() )
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
		if( $roster->auth->getAuthorized($addon['config']['show_reputation']) && $this->show_reputation() )
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
		if( $roster->auth->getAuthorized($addon['config']['show_skills']) && $this->show_skills() )
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
/*
		// Talents Tab
		if( $roster->auth->getAuthorized($addon['config']['show_talents']) && $this->show_talents() )
		{
			$roster->tpl->assign_block_vars('tabs',array(
				'NAME'     => $roster->locale->act['talents'],
				'VALUE'    => 'talents',
				'SELECTED' => $select_tab == 'talents' ? true : false
				)
			);
		}
		else
		{
			$roster->tpl->assign_var('S_TALENT_TAB',false);
		}

		// Glyphs Tab
		if( !$roster->auth->getAuthorized($addon['config']['show_glyphs']) || !$this->show_glyphs() )
		{
			$roster->tpl->assign_var('S_GLYPH_TAB',false);
		}
*/
		// Spell Book Tab
		if( $roster->auth->getAuthorized($addon['config']['show_spellbook']) && $this->show_spellbook() )
		{
			$roster->tpl->assign_block_vars('tabs',array(
				'NAME'     => $roster->locale->act['spellbook'],
				'VALUE'    => 'spellbook',
				'SELECTED' => $select_tab == 'spellbook' ? true : false
				)
			);
		}
		else
		{
			$roster->tpl->assign_var('S_SPELL_TAB',false);
		}

		// Currency Tab
		if( $roster->auth->getAuthorized($addon['config']['show_currency']) && $this->show_currency() )
		{
			$roster->tpl->assign_block_vars('tabs',array(
				'NAME'     => $roster->locale->act['currency'],
				'VALUE'    => 'currency',
				'SELECTED' => $select_tab == 'currency' ? true : false
				)
			);
		}
		else
		{
			$roster->tpl->assign_var('S_CURRENCY_TAB',false);
		}

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
