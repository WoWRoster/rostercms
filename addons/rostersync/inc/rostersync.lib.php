<?php

class rostersync {

	var $fields = array('guild'=>array(),'character'=>array());
	var $colums = array('guild'=>array(),'character'=>array());
	var $uselmh = false;
	var $preprocessitems = false;
	var $type;
	
	var $status = array(
		'guild' => array(
			'name'			=> '',
			'server'		=> '',
			'updated'	=> 0,
			'added'		=> 0,
			'removed'	=> 0,
			'start'		=> null,
			'stop'		=> null,
		),
		'character' => array(
			'name'			=> '',
			'server'		=> '',
			'member_id'		=> 0,
			'profile'		=> 0,
			'reputation'	=> 0,
			'equipment'		=> 0,
			'talents'		=> 0,
			'skills'		=> 0,
			'message'		=> '',
			'start'			=> null,
			'stop'			=> null,
			'log' 			=> '',
		),
		'log' => '',
	);
	/*
		this is a global var used all over the lib
	*/
	var $data = array();
	var $lua = array();
	var $messages = array();
	
	var $factionEN = array(0 => 'Alliance',
	1 => 'Horde');
	
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
	
	function rostersync($type = 'character')
	{
		global $roster, $addon;
		
		// build the class config
		$this->fields['guild'] = $this->_guild_fields();
		$this->fields['character'] = $this->_char_fields();
		$this->type = $type;
		
		//d($this->fields, $this->colums);
	}
	/*
		character update stuff
	*/
	function _sync_member($server, $memberId, $memberName, $region, $guildId)
	{
		global $roster, $addon;
		if (empty($memberId))
		{
			return 'error no member id';
		}
		$roster->api2->ignore_cache = false;
		$this->status[$this->type]['name'] = $memberName;
		$this->status[$this->type]['server'] = $server;
		$this->status[$this->type]['member_id'] = $memberId;
		if ($addon['config']['rsync_skip_cache'])
		{
			$this->setMessage('Sending Skip cache command');
			$roster->api2->ignore_cache = true;
			$this->data = $roster->api2->fetch(
				'character',
				array('name'=>$memberName,'server'=>$server,'fields'=>$this->fields['character'] )
				);		
		}
		else
		{
			$this->setMessage('Sending IF-Mod date');
			
			$udctime = ($addon['config']['rsync_synchcutofftime'] * 86400);
			$this->data = $roster->api2->fetch(
				'character',
				array('name'=>$memberName,'server'=>$server,'fields'=>$this->fields['character'] ),
				array('If-Modified-Since'=> gmdate('D, d M Y H:i:s \G\M\T',(time()-$udctime)))
			);
			
			if ($this->data['http_code'] == 304)
			{
				$this->status[$this->type]['message'] = 'Not Modified since '.gmdate('D, d M Y H:i:s \G\M\T',(time()-$udctime)).' <br>';
				$this->setMessage($this->_processheader($this->data['header']));
				$this->status[$this->type]['log'] = $this->getMessages();
				return false;
			}
			else if ($this->data['http_code'] != 200)
			{
				$this->status[$this->type]['message'] = $this->data['header']['http_code'].'<br>'.$this->data['status'] .'<br>'.$this->data['reason'];
				$this->setMessage($this->_processheader($this->data['header']));
				$this->status[$this->type]['log'] = $this->getMessages();
				return false;
			}
			/*
			else
			{
				$this->setMessage('unknown error halting');
				$this->status[$this->type]['log'] = $this->getMessages();
				print_r($this->status[$this->type]['log']);
				d($this->data);
				
				return false;
				exit();
			}
			*/
		}
		
		$this->status[$this->type]['start'] = time();
		$this->setMessage('<ul><li>Updating '.$memberName.'@'.$server.'</li>');
		//d($this->data);
		$pre = do_action('char_pre',$this->data);
		$this->setMessage($pre);
		
		$p = $this->_sync_member_profile($server, $memberId, $memberName, $region, $guildId);
		if($p)
		{
			$e = $this->_sync_member_equipment($memberId);
			$r = $this->_sync_member_reputation($memberId);
			$t = $this->_sync_member_talents($memberId);
			$s = $this->_sync_member_skills($memberId);
		}
		do_action('char_post',$this->data,$memberId);
		$value = '';
		$xpost = apply_filters( 'char_post_msg', $value );
		$this->setMessage($xpost);
		$this->setMessage('</ul><br>');
		$this->setMessage($this->_processheader($this->data['header']));
		$this->status[$this->type]['stop'] = time();
		$this->status[$this->type]['log'] = $this->getMessages();
		//
		return true;
	}
	
	function _processheader($header)
	{
		$head = '';
		foreach ($header as $n => $v)
		{
			$head.= "".$n.": ".$v."<br>\r\n";
		}
		return $head;
	}
	function _sync_member_profile($server, $memberId, $memberName, $region, $guildId)
	{
		global $roster, $addon;
		
		$querystr = "SELECT `member_id` FROM `" . $roster->db->table('players') . "` WHERE `member_id` = '$memberId';";
		$result = $roster->db->query($querystr);
		if( !$result )
		{
			$this->status[$this->type]['message'] = $roster->db->this_error;
			$this->setMessage($roster->db->this_error.'<br>');
			return false;
		}

		$update = $roster->db->num_rows($result) == 1;
		$roster->db->free_result($result);
		
		$this->setMessage('<li>Updating Profile:');
		
		$race = $roster->locale->act['id_to_race'][$this->data['race']];
		$raceEn = $roster->locale->act['race_to_en'][$race];
		$class = $roster->locale->act['id_to_class'][$this->data['class']];
		$classEn = $roster->locale->act['class_to_en'][$class];
		$bg = str_replace("avatar", "main", $this->data['thumbnail']);
		$player = array(
			'member_id'						=> $memberId,
			'name'							=> $memberName,
			'guild_id'						=> $guildId,
			'dateupdatedutc'				=> time(),
			'api_udt'						=> ( isset($this->data['lastModified']) ? ($this->data['lastModified']/1000) : time()),
			'CPversion'						=> '3.0',
			'DBversion'						=> '3.0',
			'clientLocale'					=> $roster->api2->locale,
			'race'							=> $roster->locale->act['id_to_race'][$this->data['race']],
			'raceid'						=> $this->data['race'],
			'raceEn'						=> $raceEn,
			'faction_id'					=> $this->data['faction'],
			'sex'							=> $this->return_gender($this->data['gender']),
			'sexid'							=> $this->data['gender'],
			'level'							=> $this->data['level'],
			'server'						=> $server,
			'region'						=> $region,
			'ilvl'							=> $this->data['items']['averageItemLevelEquipped'],
			'talent_points'					=> '',
			'class'							=> $class,
			'classid'						=> $this->data['class'],
			'classEn'						=> $classEn,
			'achievementPoints'				=> $this->data['achievementPoints'],
			'thumbnail'						=> $this->data['thumbnail'],
			'background'					=> $bg,
			'mana'							=> $this->data['stats']['power'],
			// start stats dump
			'health'						=> '',
			'power'							=> '',
			'powerType'						=> '',
			'str'							=> '',
			'agi'							=> '',
			'int'							=> '',
			'sta'							=> '',
			'speedRating'					=> '',
			'speedRatingBonus'				=> '',
			'critRating'					=> '',
			'haste'							=> '',
			'hasteRating'					=> '',
			'hasteRatingPercent'			=> '',
			'masteryRating'					=> '',
			'leech'							=> '',
			'leechRating'					=> '',
			'leechRatingBonus'				=> '',
			'versatility'					=> '',
			'versatilityDamageDoneBonus'	=> '',
			'versatilityHealingDoneBonus'	=> '',
			'versatilityDamageTakenBonus'	=> '',
			'avoidanceRating'				=> '',
			'avoidanceRatingBonus'			=> '',
			'spellPen'						=> '',
			'spellCrit'						=> '',
			'spellCritRating'				=> '',
			'mana5'							=> '',
			'mana5Combat'					=> '',
			'armor'							=> '',
			'dodgeRating'					=> '',
			'parryRating'					=> '',
			'blockRating'					=> '',
			'mainHandDmgMin'				=> '',
			'mainHandDmgMax'				=> '',
			'mainHandSpeed'					=> '',
			'mainHandDps'					=> '',
			'offHandDmgMin'					=> '',
			'offHandDmgMax'					=> '',
			'offHandSpeed'					=> '',
			'offHandDps'					=> '',
			'rangedDmgMin'					=> '',
			'rangedDmgMax'					=> '',
			'rangedSpeed'					=> '',
			'rangedDps'						=> ''
		);
		foreach ($this->data['stats'] as $n => $v)
		{
			$player[$n] = $v;
		}
		//$data = $player + $this->data['stats'];
		//d($player);
		
		if( $update )
		{
			$querystr = "UPDATE `" . $roster->db->table('players') . "` SET ". $roster->db->build_query('UPDATE', $player) . " WHERE `member_id` = '$memberId';";
			$this->status[$this->type]['profile'] = 2;
			$this->setMessage('Updateing <a href="' . makelink('char-info&amp;a=c:' . $memberId) . '"> '.$memberName.' </a> -');
		}
		else
		{
			$querystr = "INSERT INTO `" . $roster->db->table('players') . "` ". $roster->db->build_query('INSERT', $player) . ";";
			$this->status[$this->type]['profile'] = 1;
			$this->setMessage('inserting -');
		}

		$result = $roster->db->query($querystr);
		$this->setMessage(($result ? 'OK' : 'Failed').'</li>');
		if( !$result )
		{
			$this->status[$this->type]['message'] = $roster->db->this_error;
			$this->setMessage($querystr);
			$this->setMessage($this->_processheader($this->data['header']));
			$this->status[$this->type]['profile'] = 0;
			return false;
		}

		return true;
		
	}
	
	function _sync_member_equipment($memberId)
	{
		global $roster, $addon;
		
		$equip = $this->data['items'];
		$querystr = "DELETE FROM `" . $roster->db->table('items') . "` WHERE `member_id` = '$memberId' AND `item_parent` = 'equip';";
		if( !$roster->db->query($querystr) )
		{
			return;
		}
		$this->setMessage('<li>Equipment:');
		foreach($equip as $slot => $item) 
		{
			if ($slot != 'averageItemLevel' && $slot != 'averageItemLevelEquipped')
			{
				$params = array();
				$params['id'] = $item['id'];

				/*
					note add option for extended calls 
				*/
				$base_item = $roster->api2->fetch('item',$params);
				if (isset($item['bonusLists']) && !empty($item['bonusLists']))
				{
					$params['bl'] = implode(',',$item['bonusLists']);
				}
				if (isset($item['context']) && !empty($item['context']) && in_array($item['context'], $base_item['availableContexts']) )//$item['context'] != 'quest-reward' && $item['context'] != 'vendor')
				{
					$params['context'] = $item['context'];
				}
				$gam=array();
				$enchant =  $gem0 =  $gem1 =  $gem2 = $gem3 = $es = $set = $reforge = $suffex = $seed = $upgrade = null;
				$gam = array('enchant' => '','reforge' => '','suffix' => '','seed' => '','set' => '','gem0' => null,'gem1' => null,'gem2' => null,'gem3' => null,'upgrade' => array(),'extraSocket' => '',);
				$tip = '';//str_replace($string, '', $x);
				$gam = null;
				$this->gemx = array();
				$gam = array();
				foreach ($item['tooltipParams'] as $ge => $id)
				{
					$gam[$ge] = $id;
				}
				if (isset($gam['enchant']))
				{
					$enchant = $gam['enchant'] ? $gam['enchant'] : null;
				}
				if (isset($gam['reforge']))
				{
					$reforge = $gam['reforge'] ? $gam['reforge'] : null;
				}
				if (isset($gam['suffix']))
				{
					$suffix = $gam['suffix'] ? $gam['suffix'] : null;
				}
				if (isset($gam['seed']))
				{
					$seed = $gam['seed'] ? $gam['seed'] : null;
				}
				if (isset($gam['set']))
				{
					$e='';
					foreach ($gam['set'] as $id => $inf)
					{
						$e .= $inf.',';
					}
					$set = preg_replace('/,$/', '', $e);
				}
				
				if (isset($gam['upgrade']))
				{
					$upgrade = $gam['upgrade']['current'].','.$gam['upgrade']['total'];
				}
				$slot = ucfirst($slot);
				if ($slot == 'Finger1')
				{
				$slot = 'Finger0';
				}
				if ($slot == 'Finger2')
				{
				$slot = 'Finger1';
				}
				
				if ($slot == 'Trinket1')
				{
				$slot = 'Trinket0';
				}
				if ($slot == 'Trinket2')
				{
				$slot = 'Trinket1';
				}
				if ($slot == 'OffHand')
				{
					$slot = 'SecondaryHand';
				}
				
				$gem_json = array();
				if ( isset( $gam['gem0'] ) ){$gem0 = $gam['gem0']; $gem_json['gems'][0]=$roster->api2->fetch('item',array('id'=>$gem0));}
				if ( isset( $gam['gem1'] ) ){$gem1 = $gam['gem1']; $gem_json['gems'][1]=$roster->api2->fetch('item',array('id'=>$gem1));}
				if ( isset( $gam['gem2'] ) ){$gem2 = $gam['gem2']; $gem_json['gems'][2]=$roster->api2->fetch('item',array('id'=>$gem2));}
				if ( isset( $gam['gem3'] ) ){$gem3 = $gam['gem3']; $gem_json['gems'][3]=$roster->api2->fetch('item',array('id'=>$gem3));}
				//$enchant = 0;
				$item_api = $roster->api2->fetch('item',$params);

				
				$sockets = (isset($item_api['socketInfo']) ? count($item_api['socketInfo']) : 0 );
				if ($gem0 == null && $sockets >= 1) { $gem0 = 'empty'; }
				if ($gem1 == null && $sockets >= 2) { $gem1 = 'empty'; }
				if ($gem2 == null && $sockets >= 3) { $gem2 = 'empty'; }
				if ($gem3 == null && $sockets >= 4) { $gem3 = 'empty'; }
			
				$output = $roster->api2->item->item($item['id'],$item_api,$item,null);
				
				$items[] = array(
					'member_id'			=> $memberId,
					'item_name'			=> $item['name'],
					'item_parent'		=> 'equip',
					'item_slot'			=> $slot,
					'item_color'		=> $this->_getItemColor($item['quality']),
					'item_id'			=> $item['id'].":".$enchant. ":". $gem0. ":". $gem1. ":". $gem2. ":". $gem3. ":0:0". ":". (isset($item['requiredLevel']) ? $item['requiredLevel'] :'0'). ":". $reforge. ":". $upgrade. ":".(isset($item['bonusLists'][0]) ? $item['bonusLists'][0] : ''). ":".(isset($item['bonusLists'][1]) ? $item['bonusLists'][1] : ''). ":".(isset($item['bonusLists'][2]) ? $item['bonusLists'][2] : ''). ":".(isset($item['bonusLists'][3]) ? $item['bonusLists'][3] : ''),
					'item_texture'		=> $item['icon'],
					'item_quantity'		=> 1,
					'item_tooltip'		=> $output,
					'level'				=> (isset($item_api['requiredLevel']) ? $item_api['requiredLevel'] :'0'),
					'item_level'		=> $item['itemLevel'],
					'item_type'			=> (isset( $roster->api->Item->itemclass[$item_api['itemClass']] ) ? $roster->api->Item->itemclass[$item_api['itemClass']] : '' ),
					'item_subtype'		=> (isset( $roster->api->Item->itemSubClass[$item_api['itemClass']][$item_api['itemSubClass']] ) ? $roster->api->Item->itemSubClass[$item_api['itemClass']][$item_api['itemSubClass']] : '' ),
					'item_rarity'		=> $item['quality'],
					'locale'			=> '',
					'json'				=> json_encode( array_merge( $item, $gem_json ), true ),
				);
				
				$this->status[$this->type]['equipment'] += 1;
				$this->setMessage('.');
			}
		}
		$querystr = "INSERT INTO `" . $roster->db->table('items') . "` ". $roster->db->build_query('INSERT_ARRAY', $items) . ";";
		$result = $roster->db->query($querystr);//d($items);
		$this->setMessage('</li>');
	
		if( !$result )
		{
			$this->status[$this->type]['message'] = $roster->db->this_error;
			$this->setMessage($querystr);
			$this->setMessage($this->_processheader($this->data['header']));
			$this->status[$this->type]['skills'] = 0;
			return false;
		}

		return true;
	}
	
	function _sync_member_reputation($memberId)
	{
		global $roster, $addon;
		
		$querystrd = "DELETE FROM `" . $roster->db->table('reputation') . "` WHERE `member_id` = '$memberId';";
		if( !$roster->db->query($querystrd) )
		{
			return;
		}
		
		$e = '[{"category":1735,"category2":1444,"expansion":5,"friendshiprep":163,"id":1740,"name":"Aeda Brightdawn","side":2},{"category":0,"category2":1834,"expansion":6,"friendshiprep":200,"id":2099,"name":"Akule Riverhorn"},{"category":0,"category2":1097,"expansion":2,"id":1037,"name":"Alliance Vanguard","side":1},{"category":0,"category2":1444,"expansion":5,"id":1515,"name":"Arakkoa Outcasts"},{"category":1859,"category2":1834,"expansion":6,"friendshiprep":183,"id":1862,"name":"Arcane Thirst (Oculeth)"},{"category":1859,"category2":1834,"expansion":6,"friendshiprep":182,"id":1861,"name":"Arcane Thirst (Silgryn) DEPRECATED"},{"category":1859,"category2":1834,"expansion":6,"friendshiprep":181,"id":1860,"name":"Arcane Thirst (Thalyssra)"},{"category":1859,"category2":1834,"expansion":6,"friendshiprep":185,"id":1919,"name":"Arcane Thirst (Valtrois)"},{"category":0,"category2":1097,"expansion":2,"id":1106,"name":"Argent Crusade"},{"category":0,"category2":1118,"id":529,"name":"Argent Dawn"},{"category":0,"category2":1834,"expansion":6,"id":2170,"name":"Argussian Reach"},{"category":0,"category2":0,"expansion":6,"id":2167,"name":"Argussian Reach (Paragon)"},{"category":0,"category2":1834,"expansion":6,"id":2045,"name":"Armies of Legionfall"},{"category":0,"category2":1834,"expansion":6,"id":2091,"name":"Armies of Legionfall (Paragon)"},{"category":0,"category2":1834,"expansion":6,"id":2165,"name":"Army of the Light"},{"category":0,"category2":1834,"expansion":6,"id":2166,"name":"Army of the Light (Paragon)"},{"category":0,"category2":0,"expansion":6,"id":2063,"name":"Arne Test - Paragon Reputation Stormwind"},{"category":0,"category2":980,"expansion":1,"id":1012,"name":"Ashtongue Deathsworn"},{"category":0,"category2":1162,"expansion":3,"id":1204,"name":"Avengers of Hyjal"},{"category":0,"category2":1162,"expansion":3,"id":1177,"name":"Baradin\'s Wardens","side":1},{"category":0,"category2":1444,"expansion":5,"id":1735,"name":"Barracks Bodyguards"},{"category":67,"category2":1118,"expansion":3,"id":1133,"name":"Bilgewater Cartel","side":2},{"category":891,"category2":1118,"expansion":6,"friendshiprep":43,"id":2011,"name":"Bizmo\'s Brawlpub","side":1},{"category":891,"category2":1118,"expansion":4,"friendshiprep":172,"id":1419,"name":"Bizmo\'s Brawlpub (Season 1)","side":1},{"category":891,"category2":1118,"expansion":5,"id":1691,"name":"Bizmo\'s Brawlpub (Season 2)","side":1},{"category":0,"category2":1118,"id":87,"name":"Bloodsail Buccaneers"},{"category":169,"category2":1118,"id":21,"name":"Booty Bay"},{"category":892,"category2":1118,"expansion":6,"friendshiprep":28,"id":2010,"name":"Brawl\'gar Arena","side":2},{"category":892,"category2":1118,"expansion":4,"friendshiprep":171,"id":1374,"name":"Brawl\'gar Arena (Season 1)","side":2},{"category":892,"category2":1118,"expansion":5,"id":1690,"name":"Brawl\'gar Arena (Season 2)","side":2},{"category":0,"category2":1118,"id":910,"name":"Brood of Nozdormu"},{"category":0,"category2":1118,"id":609,"name":"Cenarion Circle"},{"category":0,"category2":980,"expansion":1,"id":942,"name":"Cenarion Expedition"},{"category":1272,"category2":1245,"expansion":4,"friendshiprep":16,"id":1277,"name":"Chee Chee"},{"category":0,"category2":1834,"expansion":6,"friendshiprep":209,"id":2135,"name":"Chromie"},{"category":0,"category2":1834,"expansion":6,"friendshiprep":193,"id":1975,"name":"Conjurer Margoss"},{"category":0,"category2":1834,"expansion":6,"friendshiprep":208,"id":2100,"name":"Corbyn"},{"category":0,"category2":1444,"expansion":5,"id":1731,"name":"Council of Exarchs","side":1},{"category":0,"category2":1834,"expansion":6,"id":1900,"name":"Court of Farondis"},{"category":0,"category2":1834,"expansion":6,"id":2087,"name":"Court of Farondis (Paragon)"},{"category":0,"category2":1118,"id":909,"name":"Darkmoon Faire"},{"category":0,"category2":1245,"expansion":4,"friendshiprep":72,"id":1440,"name":"Darkspear Rebellion"},{"category":67,"category2":1118,"id":530,"name":"Darkspear Trolls","side":2},{"category":469,"category2":1118,"id":69,"name":"Darnassus","side":1},{"category":1735,"category2":1444,"expansion":5,"friendshiprep":162,"id":1738,"name":"Defender Illona","side":1},{"category":1735,"category2":1444,"expansion":5,"friendshiprep":164,"id":1733,"name":"Delvar Ironfist","side":1},{"category":0,"category2":1245,"expansion":4,"id":1375,"name":"Dominance Offensive","side":2},{"category":0,"category2":1162,"expansion":3,"id":1172,"name":"Dragonmaw Clan","side":2},{"category":0,"category2":1834,"expansion":6,"id":1883,"name":"Dreamweavers"},{"category":0,"category2":1834,"expansion":6,"id":2088,"name":"Dreamweavers (Paragon)"},{"category":1272,"category2":1245,"expansion":4,"friendshiprep":17,"id":1275,"name":"Ella"},{"category":0,"category2":1245,"expansion":4,"id":1492,"name":"Emperor Shaohao"},{"category":169,"category2":1118,"id":577,"name":"Everlook"},{"category":469,"category2":1118,"expansion":1,"id":930,"name":"Exodar","side":1},{"category":1037,"category2":1097,"expansion":2,"id":1068,"name":"Explorers\' League","side":1},{"category":1272,"category2":1245,"expansion":4,"friendshiprep":2,"id":1283,"name":"Farmer Fung"},{"category":1272,"category2":1245,"expansion":4,"friendshiprep":18,"id":1282,"name":"Fish Fellreed"},{"category":0,"category2":1245,"expansion":4,"id":1228,"name":"Forest Hozen","side":2},{"category":1117,"category2":1097,"expansion":2,"id":1104,"name":"Frenzyheart Tribe"},{"category":892,"category2":1118,"id":729,"name":"Frostwolf Clan","side":2},{"category":0,"category2":1444,"expansion":5,"id":1445,"name":"Frostwolf Orcs","side":2},{"category":169,"category2":1118,"id":369,"name":"Gadgetzan"},{"category":0,"category2":1118,"id":92,"name":"Gelkis Clan Centaur"},{"category":0,"category2":1834,"expansion":6,"id":1815,"name":"Gilnean Survivors"},{"category":469,"category2":1118,"expansion":3,"id":1134,"name":"Gilneas","side":1},{"category":1272,"category2":1245,"expansion":4,"friendshiprep":19,"id":1281,"name":"Gina Mudclaw"},{"category":469,"category2":1118,"id":54,"name":"Gnomeregan","side":1},{"category":0,"category2":1245,"expansion":4,"id":1269,"name":"Golden Lotus"},{"category":0,"category2":1162,"expansion":3,"id":1158,"name":"Guardians of Hyjal"},{"category":0,"category2":1169,"expansion":3,"id":1168,"name":"Guild"},{"category":0,"category2":1444,"expansion":5,"id":1847,"name":"Hand of the Prophet","side":1},{"category":1272,"category2":1245,"expansion":4,"friendshiprep":20,"id":1279,"name":"Haohan Mudclaw"},{"category":0,"category2":1162,"expansion":3,"id":1178,"name":"Hellscream\'s Reach","side":2},{"category":0,"category2":1834,"expansion":6,"id":1828,"name":"Highmountain Tribe"},{"category":0,"category2":1834,"expansion":6,"id":2085,"name":"Highmountain Tribe (Paragon)"},{"category":0,"category2":980,"expansion":1,"id":946,"name":"Honor Hold","side":1},{"category":0,"category2":1097,"expansion":2,"id":1052,"name":"Horde Expedition","side":2},{"category":67,"category2":1118,"expansion":4,"id":1352,"name":"Huojin Pandaren","side":2},{"category":0,"category2":1118,"id":749,"name":"Hydraxian Waterlords"},{"category":0,"category2":1834,"expansion":6,"id":1947,"name":"Illidari"},{"category":0,"category2":1834,"expansion":6,"friendshiprep":199,"id":2097,"name":"Ilyssia of the Waters"},{"category":0,"category2":1834,"expansion":6,"friendshiprep":202,"id":2102,"name":"Impus"},{"category":469,"category2":1118,"id":47,"name":"Ironforge","side":1},{"category":0,"category2":1834,"expansion":6,"id":1888,"name":"Jandvik Vrykul"},{"category":1272,"category2":1245,"expansion":4,"friendshiprep":21,"id":1273,"name":"Jogu the Drunk"},{"category":0,"category2":1834,"expansion":6,"friendshiprep":204,"id":2098,"name":"Keeper Raynae"},{"category":0,"category2":980,"expansion":1,"id":989,"name":"Keepers of Time"},{"category":0,"category2":1097,"expansion":2,"id":1090,"name":"Kirin Tor"},{"category":0,"category2":1245,"expansion":4,"id":1387,"name":"Kirin Tor Offensive","side":1},{"category":0,"category2":1097,"expansion":2,"id":1098,"name":"Knights of the Ebon Blade"},{"category":0,"category2":980,"expansion":1,"id":978,"name":"Kurenai","side":1},{"category":0,"category2":1444,"expansion":5,"id":1708,"name":"Laughing Skull Orcs","side":2},{"category":1735,"category2":1444,"expansion":5,"friendshiprep":167,"id":1741,"name":"Leorajh"},{"category":936,"category2":980,"expansion":1,"id":1011,"name":"Lower City"},{"category":0,"category2":1118,"id":93,"name":"Magram Clan Centaur"},{"category":0,"category2":1834,"expansion":6,"id":1989,"name":"Moon Guard"},{"category":0,"category2":1834,"expansion":6,"id":1899,"name":"Moonguard"},{"category":1302,"category2":1245,"expansion":4,"friendshiprep":26,"id":1358,"name":"Nat Pagle"},{"category":0,"category2":980,"expansion":1,"id":1015,"name":"Netherwing"},{"category":0,"category2":0,"expansion":4,"friendshiprep":25,"id":1357,"name":"Nomi"},{"category":0,"category2":980,"expansion":1,"id":1038,"name":"Ogri\'la"},{"category":1272,"category2":1245,"expansion":4,"friendshiprep":22,"id":1276,"name":"Old Hillpaw"},{"category":0,"category2":1245,"expansion":4,"id":1376,"name":"Operation: Shieldwall","side":1},{"category":0,"category2":1444,"expansion":5,"id":1849,"name":"Order of the Awakened"},{"category":0,"category2":1245,"expansion":4,"id":1271,"name":"Order of the Cloud Serpent"},{"category":67,"category2":1118,"id":76,"name":"Orgrimmar","side":2},{"category":0,"category2":1245,"expansion":4,"id":1242,"name":"Pearlfin Jinyu","side":1},{"category":0,"category2":1162,"expansion":3,"id":1173,"name":"Ramkahen"},{"category":169,"category2":1118,"id":470,"name":"Ratchet"},{"category":0,"category2":1118,"id":349,"name":"Ravenholdt"},{"category":0,"category2":1834,"expansion":6,"friendshiprep":206,"id":2101,"name":"Sha\'leth"},{"category":0,"category2":1444,"expansion":5,"id":1710,"name":"Sha\'tari Defense","side":1},{"category":936,"category2":980,"expansion":1,"id":1031,"name":"Sha\'tari Skyguard"},{"category":0,"category2":1245,"expansion":4,"id":1270,"name":"Shado-Pan"},{"category":0,"category2":1245,"expansion":4,"id":1435,"name":"Shado-Pan Assault"},{"category":0,"category2":1444,"expansion":5,"id":1520,"name":"Shadowmoon Exiles"},{"category":0,"category2":1245,"expansion":4,"id":1216,"name":"Shang Xi\'s Academy"},{"category":936,"category2":980,"expansion":1,"id":1077,"name":"Shattered Sun Offensive"},{"category":0,"category2":1118,"id":809,"name":"Shen\'dralar"},{"category":1272,"category2":1245,"expansion":4,"friendshiprep":23,"id":1278,"name":"Sho"},{"category":67,"category2":1118,"expansion":1,"id":911,"name":"Silvermoon City","side":2},{"category":891,"category2":1118,"id":890,"name":"Silverwing Sentinels","side":1},{"category":0,"category2":980,"expansion":1,"id":970,"name":"Sporeggar"},{"category":0,"category2":1444,"expansion":5,"id":1732,"name":"Steamwheedle Draenor Expedition"},{"category":0,"category2":1444,"expansion":5,"id":1711,"name":"Steamwheedle Preservation Society"},{"category":891,"category2":1118,"id":730,"name":"Stormpike Guard","side":1},{"category":469,"category2":1118,"id":72,"name":"Stormwind","side":1},{"category":0,"category2":1245,"expansion":4,"id":1388,"name":"Sunreaver Onslaught","side":2},{"category":0,"category2":0,"id":70,"name":"Syndicate"},{"category":0,"category2":1834,"expansion":6,"id":2018,"name":"Talon\'s Vengeance"},{"category":1735,"category2":1444,"expansion":5,"friendshiprep":166,"id":1737,"name":"Talonpriest Ishaal"},{"category":936,"category2":980,"expansion":1,"id":932,"name":"The Aldor"},{"category":0,"category2":1245,"expansion":4,"id":1302,"name":"The Anglers"},{"category":0,"category2":1097,"expansion":2,"id":1156,"name":"The Ashen Verdict"},{"category":0,"category2":1245,"expansion":4,"id":1341,"name":"The August Celestials"},{"category":0,"category2":1245,"expansion":4,"id":1359,"name":"The Black Prince"},{"category":0,"category2":1245,"expansion":4,"id":1351,"name":"The Brewmasters"},{"category":0,"category2":980,"expansion":1,"id":933,"name":"The Consortium"},{"category":892,"category2":1118,"id":510,"name":"The Defilers","side":2},{"category":0,"category2":1162,"expansion":3,"id":1135,"name":"The Earthen Ring"},{"category":0,"category2":1834,"expansion":6,"friendshiprep":195,"id":1984,"name":"The First Responders"},{"category":1037,"category2":1097,"expansion":2,"id":1126,"name":"The Frostborn","side":1},{"category":1052,"category2":1097,"expansion":2,"id":1067,"name":"The Hand of Vengeance","side":2},{"category":0,"category2":1097,"expansion":2,"id":1073,"name":"The Kalu\'ak"},{"category":0,"category2":1245,"expansion":4,"id":1337,"name":"The Klaxxi"},{"category":891,"category2":1118,"id":509,"name":"The League of Arathor","side":1},{"category":0,"category2":1245,"expansion":4,"id":1345,"name":"The Lorewalkers"},{"category":0,"category2":980,"expansion":1,"id":941,"name":"The Mag\'har","side":2},{"category":0,"category2":1834,"expansion":6,"id":1859,"name":"The Nightfallen"},{"category":0,"category2":1834,"expansion":6,"id":2089,"name":"The Nightfallen (Paragon)"},{"category":1117,"category2":1097,"expansion":2,"id":1105,"name":"The Oracles"},{"category":0,"category2":1444,"expansion":5,"id":1850,"name":"The Saberstalkers"},{"category":0,"category2":980,"expansion":1,"id":990,"name":"The Scale of the Sands"},{"category":936,"category2":980,"expansion":1,"id":934,"name":"The Scryers"},{"category":936,"category2":980,"expansion":1,"id":935,"name":"The Sha\'tar"},{"category":1037,"category2":1097,"expansion":2,"id":1094,"name":"The Silver Covenant","side":1},{"category":0,"category2":1097,"expansion":2,"id":1119,"name":"The Sons of Hodir"},{"category":1052,"category2":1097,"expansion":2,"id":1124,"name":"The Sunreavers","side":2},{"category":1052,"category2":1097,"expansion":2,"id":1064,"name":"The Taunka","side":2},{"category":0,"category2":1245,"expansion":4,"id":1272,"name":"The Tillers"},{"category":0,"category2":980,"expansion":1,"id":967,"name":"The Violet Eye"},{"category":0,"category2":1834,"expansion":6,"id":1894,"name":"The Wardens"},{"category":0,"category2":1834,"expansion":6,"id":2090,"name":"The Wardens (Paragon)"},{"category":0,"category2":1097,"expansion":2,"id":1091,"name":"The Wyrmrest Accord"},{"category":0,"category2":1162,"expansion":3,"id":1171,"name":"Therazane"},{"category":0,"category2":1118,"id":59,"name":"Thorium Brotherhood"},{"category":0,"category2":980,"expansion":1,"id":947,"name":"Thrallmar","side":2},{"category":67,"category2":1118,"id":81,"name":"Thunder Bluff","side":2},{"category":0,"category2":1118,"id":576,"name":"Timbermaw Hold"},{"category":1272,"category2":1245,"expansion":4,"friendshiprep":24,"id":1280,"name":"Tina Mudclaw"},{"category":1735,"category2":1444,"expansion":5,"friendshiprep":160,"id":1736,"name":"Tormmok"},{"category":0,"category2":980,"expansion":1,"id":922,"name":"Tranquillien","side":2},{"category":469,"category2":1118,"expansion":4,"id":1353,"name":"Tushui Pandaren","side":1},{"category":67,"category2":1118,"id":68,"name":"Undercity","side":2},{"category":0,"category2":1834,"expansion":6,"id":1948,"name":"Valarjar"},{"category":0,"category2":1834,"expansion":6,"id":2086,"name":"Valarjar (Paragon)"},{"category":1037,"category2":1097,"expansion":2,"id":1050,"name":"Valiance Expedition","side":1},{"category":1735,"category2":1444,"expansion":5,"friendshiprep":165,"id":1739,"name":"Vivianne","side":2},{"category":0,"category2":1444,"expansion":5,"id":1848,"name":"Vol\'jin\'s Headhunters","side":2},{"category":0,"category2":1444,"expansion":5,"id":1681,"name":"Vol\'jin\'s Spear","side":2},{"category":1052,"category2":1097,"expansion":2,"id":1085,"name":"Warsong Offensive","side":2},{"category":892,"category2":1118,"id":889,"name":"Warsong Outriders","side":2},{"category":0,"category2":1162,"expansion":3,"id":1174,"name":"Wildhammer Clan","side":1},{"category":0,"category2":0,"id":589,"name":"Wintersaber Trainers","side":1},{"category":0,"category2":1444,"expansion":5,"id":1682,"name":"Wrynn\'s Vanguard","side":1},{"category":0,"category2":1118,"id":270,"name":"Zandalar Tribe"},{"category":0,"category2":0,"expansion":6,"id":2180,"name":"_JKL - test rep","side":1}]';
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
		$x = json_decode($e,true);

		$r = array();
		$rid = array();
		$sd = array();
		foreach ($x as $i => $f)
		{
			$rid[$f['id']] = $f;
		}
		$rid['1117'] = 'Sholazar Basin';
		$rid['936'] = 'Shattrath City';
		$rid['169'] = 'Steamwheedle Cartel';
		$rid['1118'] = 'Classic';
		$rid['1162'] = 'Cataclysm';
		$rid['1169'] = 'Guild';
		$rid['1834'] = 'Legion';
		$rid['1245'] = 'Mists of Pandaria';
		$rid['0'] = 'Other';
		$rid['980'] = 'The Burning Crusade';
		$rid['1444'] = 'Warlords of Draenor';
		$rid['1097'] = 'Wrath of the Lich King';
		$rid['891'] = 'Alliance Forces';
		$rid['892'] = 'Horde Forces';
		$rid['469'] = 'Alliance';
		$rid['67'] = 'Horde';

		$rep = array();
		foreach ($this->data['reputation'] as $i => $r)
		{
			$rep[] = array_merge ( $r, $rid[$r['id']]);
		}

		$this->setMessage('<li>Updating Reputation:');

		$rept = array();
		foreach($rep as $id => $f)
		{
			$rept[] = array(
				'member_id'		=> $memberId,
				'id' 			=> (isset($f['id']) ? $f['id'] : ''),
				'name' 			=> (isset($f['name']) ? $f['name'] : ''),
				'standing' 		=> (isset($f['standing']) ? $f['standing'] : ''),
				'value' 		=> (isset($f['value']) ? $f['value'] : ''),
				'max' 			=> (isset($f['max']) ? $f['max'] : ''),
				'category' 		=> (isset($f['category']) ? $f['category'] : ''),
				'category2' 	=> (isset($f['category2']) ? $f['category2'] : ''),
				'expansion' 	=> (isset($f['expansion']) ? $f['expansion'] : ''),
				'friendshiprep' => (isset($f['friendshiprep']) ? $f['friendshiprep'] : ''),
				'side' 			=> (isset($f['side']) ? $f['side'] : ''),
			);
			$this->setMessage('.');
			$this->status[$this->type]['reputation'] += 1;
		}

		$this->setMessage('</li>');
		$querystr = "INSERT INTO `" . $roster->db->table('reputation') . "` ". $roster->db->build_query('INSERT_ARRAY', $rept) . ";";
		$result = $roster->db->query($querystr);
		
		if( !$result )
		{
			$this->status[$this->type]['message'] = $roster->db->this_error;
			$this->setMessage($querystr);
			$this->setMessage($this->_processheader($this->data['header']));
			$this->status[$this->type]['skills'] = 0;
			return false;
		}
		return true;
	}
	
	function _sync_member_talents($memberId)
	{
		global $roster, $addon;
		
		$this->setMessage('<li>Updating Talents: <ul>');

		// first delete the stale data
		$querystr = "DELETE FROM `" . $roster->db->table('talents') . "` WHERE `member_id` = '$memberId';";
		if( !$roster->db->query($querystr) )
		{
			return;
		}

		$querystr = "DELETE FROM `" . $roster->db->table('talenttree') . "` WHERE `member_id` = '$memberId';";
		if( !$roster->db->query($querystr) )
		{
			return;
		}
			
		$talent = array();
		$trees = array();
		foreach ($this->data['talents'] as $key => $spec)
		{
			if(!empty($spec['talents']))
			{
				$this->setMessage('<li>'.$spec['spec']['order'].'-'.$spec['spec']['name'].': ');
				$ts = 0;
				foreach($spec['talents'] as $s => $spell)
				{
					$talent[] = array(
						'member_id'	=> $memberId,
						'build'		=> $spec['spec']['order'],
						'name'		=> $spell['spell']['name'],
						'tree'		=> $spec['spec']['name'],
						'row'		=> $spell['tier'],
						'column'	=> $spell['column'],
						'rank'		=> '1',
						'maxrank'	=> '1',
						'tooltip'	=> '',
						'texture'	=> $spell['spell']['icon'],
						'talent_id'	=> $spell['spell']['id'],
					);
					$this->setMessage('.');
					$ts++;
				}
				$trees[] = array(
					'member_id'		=> $memberId,
					'build'			=> $spec['spec']['order'],
					'tree'			=> $spec['spec']['name'],
					'background'	=> $spec['spec']['backgroundImage'],
					'order'			=> $key,
					'pointsspent'	=> $ts,
					'selected'		=> ($spec['selected'] ? true : false),
				);
				$this->setMessage('</li>');
			}
		}
		$this->setMessage('</ul></li>');
		
		$querystr1 = "INSERT INTO `" . $roster->db->table('talents') . "` ". $roster->db->build_query('INSERT_ARRAY', $talent) . ";";
		$result1 = $roster->db->query($querystr1);
		
		if( !$result1 )
		{
			$this->status[$this->type]['message'] = $roster->db->this_error;
			$this->setMessage($querystr);
			$this->setMessage($this->_processheader($this->data['header']));
			$this->status[$this->type]['skills'] = 0;
			return false;
		}
		$querystr2 = "INSERT INTO `" . $roster->db->table('talenttree') . "` ". $roster->db->build_query('INSERT_ARRAY', $trees) . ";";
		$result2 = $roster->db->query($querystr2);
		
		if( !$result2 )
		{
			$this->status[$this->type]['message'] = $roster->db->this_error;
			$this->setMessage($querystr);
			$this->setMessage($this->_processheader($this->data['header']));
			$this->status[$this->type]['skills'] = 0;
			return false;
		}
		
		return true;

	}
	
	function _sync_member_skills($memberId)
	{
		global $roster, $addon;

		$querystr = "DELETE FROM `" . $roster->db->table('skills') . "` WHERE `member_id` = '$memberId';";
		if( !$roster->db->query($querystr) )
		{
			return;
		}
		$this->setMessage('<li>Updating Professions: <ul>');
		$skills = $this->data['professions'];
		$o = 0;
		$s = array();
		foreach ($skills['primary'] as $id =>$skil) 
		{

			if ( isset($skil['name']) )
			{ 			
				$this->data["Skills"][''.$roster->locale->act['professions'].''][''.$skil['name'].''] = "".$skil['rank'].":".$skil['max']."";
				$this->data["Skills"][''.$roster->locale->act['professions'].'']["Order"] = $o;
				
				$s[] = array(
					'member_id'		=> $memberId,
					'skill_type'	=> $roster->locale->act['professions'],
					'skill_name'	=> $skil['name'],
					'skill_order'	=> $o,
					'skill_level'	=> $skil['rank'].":".$skil['max'],
				);
				
				$this->setMessage('<li>'.$skil['name'].' '.$skil['rank'].':'.$skil['max'].'</li>');

				$this->status[$this->type]['skillInfo'] += 1;
				$o++;
			}
			else
			{
				$this->status[$this->type]['skillInfo'] = '0';
			}
		}

		foreach ($skills['secondary'] as $id =>$skil) 
		{
			if ( isset($skil['name']) )
			{ 			
				$s[] = array(
					'member_id'		=> $memberId,
					'skill_type'	=> $roster->locale->act['professions'],
					'skill_name'	=> $skil['name'],
					'skill_order'	=> $o,
					'skill_level'	=> $skil['rank'].":".$skil['max'],
				);
				
				$this->setMessage('<li>'.$skil['name'].' '.$skil['rank'].':'.$skil['max'].'</li>');
				$this->status[$this->type]['skillInfo'] += 1;
				$o++;
			}
			else
			{
				$this->status[$this->type]['skillInfo'] = $this->status[$this->type]['skillInfo'];
			}
		}
		$this->setMessage('</ul></li>');
		$querystr1 = "INSERT INTO `" . $roster->db->table('skills') . "` ". $roster->db->build_query('INSERT_ARRAY', $s) . ";";
		$result1 = $roster->db->query($querystr1);
	
		if( !$result1 )
		{
			$this->status[$this->type]['message'] = $roster->db->this_error;
			$this->setMessage($querystr);
			$this->setMessage($this->_processheader($this->data['header']));
			$this->status[$this->type]['skills'] = 0;
			return false;
		}

		return true;
	}

	function build_update_table($type)
	{
		global $roster, $addon;
		
		$row = '';
		switch ($type)
		{
			case 'guild':
			$row = '<div class="row cfg-row">
				<div class="col-md-2">'.$roster->locale->act['name'].'</div>
				<div class="col-md-2">'.$roster->locale->act['server'].'</div>
				<div class="col-md-1">'.$roster->locale->act['members'].'</div>
				<div class="col-md-1">'.$roster->locale->act['update_log'].'</div>
			</div>';
			
			break;
			
			case 'guild_members':
			$row = '<div class="row cfg-row">
				<div class="col-md-2">'.$roster->locale->act['name'].'</div>
				<div class="col-md-2">'.$roster->locale->act['guild'].'</div>
				<div class="col-md-2">'.$roster->locale->act['server'].'</div>
				<div class="col-md-1">'.$roster->locale->act['character_short'].'</div>
				<div class="col-md-1">'.$roster->locale->act['reputation_short'].'</div>
				<div class="col-md-1">'.$roster->locale->act['equipment_short'].'</div>
				<div class="col-md-1">'.$roster->locale->act['talents_short'].'</div>
				<div class="col-md-1">'.$roster->locale->act['skill_short'].'</div>
				<div class="col-md-1">'.$roster->locale->act['update_log'].'</div>
			</div>';
			break;
			
			case 'character':
			
			$row = '<div class="row cfg-row">
				<div class="col-md-2">'.$roster->locale->act['name'].'</div>
				<div class="col-md-2">'.$roster->locale->act['server'].'</div>
				<div class="col-md-1">'.$roster->locale->act['character_short'].'</div>
				<div class="col-md-1">'.$roster->locale->act['reputation_short'].'</div>
				<div class="col-md-1">'.$roster->locale->act['equipment_short'].'</div>
				<div class="col-md-1">'.$roster->locale->act['talents_short'].'</div>
				<div class="col-md-1">'.$roster->locale->act['skill_short'].'</div>
				<div class="col-md-1">'.$roster->locale->act['time'].'</div>
				<div class="col-md-2">'.$roster->locale->act['update_log'].'</div>
			</div>';

			break;
			
			case 'guild-success':
				$row .= $this->status[$this->type]['log'];
			break;
			
			case 'character-info-success':
			
			$stop = time();
			$row = '<div class="row cfg-row">
					<div class="col-md-2"><a href="' . makelink('char-info&amp;a=c:' . $this->status[$this->type]['member_id']) . '">'.$this->status[$this->type]['name'].'</a></div>
					<div class="col-md-2">'.$this->status[$this->type]['server'].'</div>';
					
			$row .= '
					<div class="col-md-1">'.$this->status[$this->type]['profile'].'</div>
					<div class="col-md-1">'.$this->status[$this->type]['reputation'].'</div>
					<div class="col-md-1">'.$this->status[$this->type]['equipment'].'</div>
					<div class="col-md-1">'.$this->status[$this->type]['talents'].'</div>
					<div class="col-md-1">'.$this->status[$this->type]['skills'].'</div>
					<div class="col-md-3">'.$this->status[$this->type]['start'].'-'.$stop.'</div>
					</div>
					<div class="row">
						<div class="col-md-12">
						<h4>'.$roster->locale->act['update_log'].'</h4><br>
						  '.$this->status[$this->type]['log'].'
						</div>
					</div>';
			
			break;
			
			case 'character-success':
			
			$stop = time();
			$row = '<div class="row cfg-row">
					<div class="col-md-2"><a href="' . makelink('char-info&amp;a=c:' . $this->status[$this->type]['member_id']) . '">'.$this->status[$this->type]['name'].'</a></div>
					<div class="col-md-2">'.$this->status[$this->type]['server'].'</div>';
					
			if ( isset($this->status[$this->type]['message']) && !empty($this->status[$this->type]['message']) )
			{
				ob_start();
				+d($this->data);
			$content = ob_get_clean();
				$row .= '
					<div class="col-md-3">'.$this->status[$this->type]['message'].'</div>
					<div class="col-md-5">
					
						<button type="button" class="btn btn-primary btn-md" id="logbutton" data-member="log-'.$this->status[$this->type]['member_id'].'" data-target="log-'.$this->status[$this->type]['member_id'].'">
							'.$roster->locale->act['error_log'].'
						</button>
					<!-- Modal -->
					<div class="modal" id="log-'.$this->status[$this->type]['member_id'].'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
					  <div class="modal-dialog" role="document">
						<div class="modal-content">
						  <div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close" data-member="log-'.$this->status[$this->type]['member_id'].'" data-target="log-'.$this->status[$this->type]['member_id'].'"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="myModalLabel">'.$roster->locale->act['error_log'].'</h4>
						  </div>
						  <div class="modal-body" style="color: #000 !important;">'.$this->status[$this->type]['log'].'<br>'.$this->status[$this->type]['message'].'<br>'.$content.'</div>
						  <div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal" data-member="log-'.$this->status[$this->type]['member_id'].'" data-target="log-'.$this->status[$this->type]['member_id'].'">Close</button>
						  </div>
						</div>
					  </div>
					</div>
					
					</div>
					</div>';
			}
			else
			{
				$row .= '
					<div class="col-md-1">'.$this->status[$this->type]['profile'].'</div>
					<div class="col-md-1">'.$this->status[$this->type]['reputation'].'</div>
					<div class="col-md-1">'.$this->status[$this->type]['equipment'].'</div>
					<div class="col-md-1">'.$this->status[$this->type]['talents'].'</div>
					<div class="col-md-1">'.$this->status[$this->type]['skills'].'</div>
					<div class="col-md-1">'.$this->status[$this->type]['start'].'-'.$stop.'</div>
					<div class="col-md-2">
						<button type="button" class="btn btn-primary btn-md" id="logbutton" data-member="log-'.$this->status[$this->type]['member_id'].'" data-target="log-'.$this->status[$this->type]['member_id'].'">
							'.$roster->locale->act['update_log'].'
						</button>
					<!-- Modal -->
					<div class="modal" id="log-'.$this->status[$this->type]['member_id'].'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
					  <div class="modal-dialog" role="document">
						<div class="modal-content">
						  <div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close" data-member="log-'.$this->status[$this->type]['member_id'].'" data-target="log-'.$this->status[$this->type]['member_id'].'"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="myModalLabel">'.$roster->locale->act['update_log'].'</h4>
						  </div>
						  <div class="modal-body" style="color: #000 !important;">'.$this->status[$this->type]['log'].'</div>
						  <div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal" data-member="log-'.$this->status[$this->type]['member_id'].'" data-target="log-'.$this->status[$this->type]['member_id'].'">Close</button>
						  </div>
						</div>
					  </div>
					</div>
					</div>
				</div>';
			}
			
			break;
		}
		
		return $row;
	}
	
	/*
		Add Guild
	*/
	function _sync_guild_add($server, $guildName, $region, $guildId)
	{
		global $roster, $addon;
		
		$this->status[$this->type]['name'] = $guildName;
		$this->status[$this->type]['server'] = $server;
		
		$this->data = $roster->api2->fetch('guild',array('name'=>$guildName,'server'=>$server,'fields'=>$this->fields['guild'] ));
		
		$this->setMessage('<ul><li>Adding '.$guildName.'@'.$server.'<ul>');
			
		$guild = array(
			'guild_name'			=> $guildName,
			'server'				=> $server,
			'region'				=> $region,
			'faction'				=> $this->factionEN[$this->data['side']],
			'factionEn'				=> $this->factionEN[$this->data['side']],
			'guild_motd'			=> '',
			'guild_num_members'		=> count($this->data['members']),
			'guild_num_accounts'	=> count($this->data['members']),
			'guild_xp'				=> '',
			'guild_xpcap'			=> '',
			'guild_level'			=> $this->data['level'],
			'achievementPoints'		=> $this->data['achievementPoints'],
			'battlegroup'			=> $this->data['battlegroup'],
			'update_time'			=> date('Y-m-d H:i:s'),
			'GPversion'				=> '',
			'DBversion'				=> '',
			'guild_info_text'		=> '',
		);
		$querystr = "INSERT `" . $roster->db->table('guild') . "` SET ". $roster->db->build_query('UPDATE', $guild) . ";";
		$result = $roster->db->query($querystr);
		if( !$result )
		{
			$this->status[$this->type]['message'] = $roster->db->this_error;
			$this->setMessage($roster->db->this_error.'<br>');
		}
		
		$this->status[$this->type]['updated'] = 2;
		$this->setMessage('</ul></li></ul>');
		
		return true;
		
	}
	/*
		Update Guild
	*/
	function _sync_guild_update($server, $guildName, $region, $guildId)
	{
		global $roster, $addon;
		
		$this->status[$this->type]['name'] = $guildName;
		$this->status[$this->type]['server'] = $server;
		
		//$this->data = $roster->api2->fetch('guild',array('name'=>$guildName,'server'=>$server,'fields'=>$this->fields['guild'] ));
		
		$this->setMessage('<ul><li>Updating '.$guildName.'@'.$server.'<ul>');
			
		$guild = array(
			'guild_name'			=> $guildName,
			'server'				=> $server,
			'region'				=> $region,
			'faction'				=> $this->factionEN[$this->data['side']],
			'factionEn'				=> $this->factionEN[$this->data['side']],
			'guild_motd'			=> '',
			'guild_num_members'		=> count($this->data['members']),
			'guild_num_accounts'	=> count($this->data['members']),
			'guild_xp'				=> '',
			'guild_xpcap'			=> '',
			'guild_level'			=> $this->data['level'],
			'achievementPoints'		=> $this->data['achievementPoints'],
			'battlegroup'			=> $this->data['battlegroup'],
			'update_time'			=> date('Y-m-d H:i:s'),
			'GPversion'				=> '',
			'DBversion'				=> '',
			'guild_info_text'		=> '',
		);
		$querystr = "UPDATE `" . $roster->db->table('guild') . "` SET ". $roster->db->build_query('UPDATE', $guild) . " WHERE `guild_id` = '".$guildId."';";
		$result = $roster->db->query($querystr);
		if( !$result )
		{
			$this->status[$this->type]['message'] = $roster->db->this_error;
			$this->setMessage($roster->db->this_error.'<br>');
			return false;
		}
		
		$this->status[$this->type]['updated'] = 2;
		$this->setMessage('</ul></li></ul>');
		
	}
	/*
		begin guild update and memberlist update
	*/
	function _sync_guild_members($server, $guildName, $region, $guildId)
	{
		global $roster, $addon;
		$this->data = $roster->api2->fetch('guild',array('name'=>$guildName,'server'=>$server,'fields'=>$this->fields['guild'] ));
		
		$this->_sync_guild_update($server, $guildName, $region, $guildId);

		$querystrx = "UPDATE `" . $roster->db->table('members') . "` SET `active` = '0' WHERE `guild_id` = '".$guildId."';";
		$resultx = $roster->db->query($querystrx);
				
				
		$members = 0;
		$this->setMessage('<ul><li>Updating '.$guildName.'@'.$server.' Members<ul>');
		foreach ($this->data['members'] as $i => $m)
		{
			if (isset($m['character']['name']) && isset($m['character']['realm']))
			{
				$queryst = "SELECT `member_id` FROM `" . $roster->db->table('members') . "` WHERE `name` = '".$m['character']['name']."' AND `server` = '".$m['character']['realm']."';";
				$result = $roster->db->query($queryst);
				if( !$result )
				{
					return false;
				}

				$update = ($roster->db->num_rows($result) == 1 ? true : false );
				
				$member_id = $roster->db->fetch($result);
				$roster->db->free_result($result);
				
				$name = $m['character']['name'];
				$member = array(
					'name'				=> $m['character']['name'],
					'server'			=> $m['character']['realm'],
					'region'			=> $region,
					'guild_id'			=> $guildId,
					'class'				=> $roster->locale->act['id_to_class'][$m['character']['class']],
					'classid'			=> $m['character']['class'],
					'level'				=> $m['character']['level'],
					'note'				=> '',
					'guild_rank'		=> $m['rank'],
					'guild_title'		=> $roster->data['ranks'][$m['rank']]['title'],//'Rank'.$m['rank'],
					'zone'				=> '',
					'status'			=> '',
					'active'			=> '1',
					'online'			=> '0',
					'last_online'		=>	time(),
					'achievementPoints'	=> $m['character']['achievementPoints'],
				);
				
				if( $update )
				{
					$querystr = "UPDATE `" . $roster->db->table('members') . "` SET ". $roster->db->build_query('UPDATE', $member) . " WHERE `member_id` = '".$member_id['member_id']."';";
					$this->status[$this->type]['member'] = 2;
					$this->setMessage('<li>[ ' . $name . ' ] - updated</li>');
				}
				else
				{
					$querystr = "INSERT INTO `" . $roster->db->table('members') . "` ". $roster->db->build_query('INSERT', $member) . ";";
					$this->status[$this->type]['member'] = 1;
					$this->setMessage('<li><span class="color-green-medium">[ ' . $name . ' ] - Added</span></li>');
				}

				$result = $roster->db->query($querystr);
			}
			else
			{
				$this->setMessage('<li><span class="color-red-medium">'.$m['character']['name'].' data missing</span></li>');
			}
		}
		$querystrx = "DELETE FROM `" . $roster->db->table('members') . "` WHERE `guild_id` = '".$guildId."' AND `active` = '0' ;";
		$resultx = $roster->db->query($querystrx);
		
		$this->setMessage('<li><span class="color-red-medium">'.$roster->db->affected_rows( ).' members Removed</span></li>');
		$this->setMessage('</ul></li></ul>');
		$this->status[$this->type]['log'] = $this->getMessages();
	}	
	/*
		guild member list generage
	*/
	function _getMembersToUpdate( $where = false )
	{
		global $roster, $addon;
		
		$w = array();
		if (isset($addon['config']['rsync_MinLvl']) && !empty($addon['config']['rsync_MinLvl']))
		{
			$w[] = "members.level >= '" . $addon['config']['rsync_MinLvl'] . "'";
		}
		if (isset($addon['config']['rsync_MaxLvl']) && !empty($addon['config']['rsync_MaxLvl']))
		{
			$w[] = "members.level <= '" . $addon['config']['rsync_MaxLvl'] . "'";
		}
		if (isset($addon['config']['rsync_Rank']) && !empty($addon['config']['rsync_Rank']))
		{
			$w[] = "members.guild_rank = '" . $addon['config']['rsync_Rank'] . "'";
		}
		if (isset($addon['config']['rsync_Class']) && !empty($addon['config']['rsync_Class']))
		{
			$w[] = "members.classid = '" . $addon['config']['rsync_Class'] . "'";
		}
		if (count($w) > 1)
		{
			$where .= implode($w,' AND ');
		}
		if (count($w) == 1)
		{
			$where .= implode($w,' ');
		}
		$query =	"SELECT members.member_id, members.name, guild.guild_id, guild.guild_name, members.server, guild.region, updates.api_udt ".
		"FROM `".$roster->db->table('members')."` members LEFT JOIN `".$roster->db->table('guild')."` guild ON members.guild_id = guild.guild_id " .
		"LEFT JOIN `". $roster->db->table('players'). "` updates ON members.member_id = updates.member_id ".
		"WHERE ". $where. "ORDER BY members.member_id;";	

		$result = $roster->db->query($query);
		
		if( $roster->db->num_rows($result) > 0 )
		{
			$ret = $roster->db->fetch_all();
		}
		else
		{
			$ret = array();
		}
		
		$roster->db->free_result( $result );
		return $ret;
	}
	
	function _guild_fields()
	{
		global $addon;
		
		$x=array();
		if ($addon['config']['rsync_guild_members']) { 		$x[] = 'members';}
		if ($addon['config']['rsync_guild_achievements']) { $x[] = 'achievements';}
		if ($addon['config']['rsync_guild_news']) { 		$x[] = 'news';}
		if ($addon['config']['rsync_guild_challenge']) { 	$x[] = 'challenge';}
		
		$this->colums['guild'] = $x;
		
		return implode(',',$x);
	}
	function _char_fields()
	{
		global $addon;
		
		$x=array();
		if ($addon['config']['rsync_char_achievements']) {	$x[] = 'achievements';	}
		if ($addon['config']['rsync_char_appearance']) {	$x[] = 'appearance';	}
		if ($addon['config']['rsync_char_feed']) {			$x[] = 'feed';			}
		if ($addon['config']['rsync_char_guild']) {			$x[] = 'guild';			}
		if ($addon['config']['rsync_char_hunterPets']) {	$x[] = 'hunterPets';	}
		if ($addon['config']['rsync_char_items']) {			$x[] = 'items';			}
		if ($addon['config']['rsync_char_mounts']) {		$x[] = 'mounts';		}
		if ($addon['config']['rsync_char_pets']) {			$x[] = 'pets';			}
		if ($addon['config']['rsync_char_petSlots']) { 		$x[] = 'petSlots';		}
		if ($addon['config']['rsync_char_professions']) { 	$x[] = 'professions';	}
		if ($addon['config']['rsync_char_progression']) { 	$x[] = 'progression';	}
		if ($addon['config']['rsync_char_pvp']) { 			$x[] = 'pvp';			}
		if ($addon['config']['rsync_char_quests']) { 		$x[] = 'quests';		}
		if ($addon['config']['rsync_char_reputation']) { 	$x[] = 'reputation';	}
		if ($addon['config']['rsync_char_stats']) {			$x[] = 'stats';			}
		if ($addon['config']['rsync_char_talents']) { 		$x[] = 'talents';		}
		if ($addon['config']['rsync_char_titles']) { 		$x[] = 'titles';		}
		if ($addon['config']['rsync_char_audit']) { 		$x[] = 'audit';			}
		if ($addon['config']['rsync_char_companions']) { 	$x[] = 'companions';	}
		if ($addon['config']['rsync_char_statistics']) { 	$x[] = 'statistics';	}
		if ($addon['config']['rsync_char_hunterPets']) { 	$x[] = 'hunterPets';	}
		
		$this->colums['character'] = $x;
		
		return implode(',',$x);
	}
	function return_gender($genderid)
	{
		if ($genderid == "0") {$gender = "Male";}
		if ($genderid == "1") {$gender = "Female";}
		return $gender;
	}
	
	function _getItemColor($value)
	{
		$ret = '';
		switch ($value) {
			default: $ret = "9d9d9d"; //Grey
				break;
			case 0: $ret = "9d9d9d"; /* poor (gray) */
			break;
			case 1: $ret = "ffffff"; /* common (white) */
			break;
			case 2: $ret = "1eff00"; /* uncommon (green) */
			break;
			case 3: $ret = "0081ff"; /* #0070dd rare (blue) */
			break;
			case 4: $ret = "c600ff"; /* #a335ee epic (purple) */
			break;
			case 5: $ret = "ff8000"; /* lengendary (orange) */
			break;
			case 6: $ret = "e5cc80"; /* artifact (gold) */
			break;
			case 7: $ret = "00ccff"; /* heirloom (lt blue) */
			break;

		}
		return $ret;
	}
	
	/**
 	* helper function to get relative value for reputation
 	*
 	* @param int $value
 	* @return int RepValue
 	*/
	function _getRepValue($value)
	{
		global $roster, $addon;
		
		$value = abs($value);

		if ($value >= 42000 && $value < 43000) { $value -= 42000; }
		elseif ($value >= 21000 && $value < 42000) { $value -= 21000; }
		elseif ($value >= 9000 && $value < 21000) { $value -= 9000;  }
		elseif ($value >= 3000 && $value < 9000) { $value -= 3000; }
		elseif ($value >= -3000 && $value < 3000) { $value -= 0;  }
		elseif ($value >= -6000 && $value < -3000) { $value -= 3000; }
		elseif ($value >= -42000 && $value < -6000) { $value -= 6000; }

		$roster->debug->_debug( 2, $value, 'Determined reputation value', 'OK' );
		return $value;
	}

	/**
 	* helper function to get cap value for reputation
 	*
 	* @param int $value
 	* @return int RepCap
 	*/
	function _getRepCap($value)
	{
		global $roster, $addon;
		
		$ret = 0;
		if ($value >= 42000 && $value < 43000) { $ret = 1000; }
		if ($value >= 21000 && $value < 42000) { $ret = 21000; }
		if ($value >= 9000 && $value < 21000) { $ret = 12000; }
		if ($value >= 3000 && $value < 9000) { $ret = 6000; }
		if ($value >= -6000 && $value < 3000) { $ret = 3000; }
		if ($value >= -42000 && $value < -6000) { $ret = 36000; }

		$roster->debug->_debug( 2, $ret, 'Determined reputation cap', 'OK' );
		return $ret;
	}

	/**
 	* helper function to get war status for reputation
 	*
 	* @param int $value
 	* @return bool
 	*/
	function _getRepAtWar($value)
	{
		global $roster, $addon;
		
		if ($value >= -3000) { $ret = 0; }
		else { $ret = 1; }
		$roster->debug->_debug( 2, $ret, 'Determined reputation at war', 'OK' );
		return $ret;
	}

	/**
 	* helper function to get localized string for reputation
 	*
 	* @param int $value
 	* @return string RepStanding
 	*/
	function _getRepStanding($value)
	{
		global $roster, $addon;
		
		$ret = '';
		if ($value == '7') { $ret = $roster->locale->act['exalted']; }
		if ($value == '6') { $ret = $roster->locale->act['revered']; }
		if ($value == '5') { $ret = $roster->locale->act['honored']; }
		if ($value == '4') { $ret = $roster->locale->act['friendly']; }
		if ($value == '3') { $ret = $roster->locale->act['neutral']; }
		if ($value == '2') { $ret = $roster->locale->act['unfriendly']; }
		if ($value == '1') { $ret = $roster->locale->act['hostile']; }
		if ($value == '0') { $ret = $roster->locale->act['hated']; }

		$roster->debug->_debug( 2, $ret, 'Determined reputation standing', 'OK' );
		return $ret;
	}

}