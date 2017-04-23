<?php
/*
	About this api file
	
	This api file will take standard api items and user equipment and make full html tooltips for items
	for storage and use later
	
	this is ment as a 1 time rendering for items and should not be used to generate items on the fly as
	the only output is an item tooltip
	
*/

class api_item{
	
	var $user = array();
	var $dapi = array();
	var $dgems = array();
	
	var $enchants = array();
	var $de = '{"5324":{"bonus":"+50 Critical Strike"},"5325":{"bonus":"+50 Haste"},"5326":{"bonus":"+50 Mastery"},"5327":{"bonus":"+50 Multistrike"},
	"5328":{"bonus":"+50 Versatility"},"5317":{"bonus":"+75 Critical Strike"},"5318":{"bonus":"+75 Haste"},"5319":{"bonus":"+75 Mastery"},"5320":{"bonus":"+75 Multistrike"},"5321":{"bonus":"+75 Versatility"},"5310":{"bonus":"+100 Critical Strike & +10% Speed"},"5311":{"bonus":"+100 Haste & +10% Speed"},"5312":{"bonus":"+100 Mastery & +10% Speed"},"5313":{"bonus":"+100 Multistrike & +10% Speed"},"5314":{"bonus":"+100 Versatility & +10% Speed"},"5330":{"bonus":"Mark of the Thunderlord"},"5331":{"bonus":"Mark of the Shattered Hand"},"5335":{"bonus":"Mark of Shadowmoon"},"5336":{"bonus":"Mark of Blackrock"},"5337":{"bonus":"Mark of Warsong"},"5334":{"bonus":"Mark of the Frostwolf"},"5384":{"bonus":"Mark of Bleeding Hollow"},"5284":{"bonus":"+30 Critical Strike"},"5297":{"bonus":"+30 Haste"},"5299":{"bonus":"+30 Mastery"},"5301":{"bonus":"+30 Multistrike"},"5303":{"bonus":"+30 Versatility"},"5285":{"bonus":"+40 Critical Strike"},"5292":{"bonus":"+40 Haste"},"5293":{"bonus":"+40 Mastery"},"5294":{"bonus":"+40 Multistrike"},"5295":{"bonus":"+40 Versatility"},"5281":{"bonus":"+100 Critical Strike"},"5298":{"bonus":"+100 Haste"},"5300":{"bonus":"+100 Mastery"},"5302":{"bonus":"+100 Multistrike"},"5304":{"bonus":"+100 Versatility"},"4441":{"bonus":"Windsong"},"4442":{"bonus":"Jade Spirit"},"4443":{"bonus":"Elemental Force"},"4444":{"bonus":"Dancing Steel"},"4445":{"bonus":"Colossus"},"4446":{"bonus":"River\'s Song"},"4411":{"bonus":"+170 Mastery"},"4412":{"bonus":"+170 Dodge"},"4414":{"bonus":"+180 Intellect"},"4415":{"bonus":"+180 Strength"},"4416":{"bonus":"+180 Agility"},"4417":{"bonus":"+200 PvP Resilience"},"4418":{"bonus":"+200 Spirit"},"4419":{"bonus":"+80 All Stats"},"4420":{"bonus":"+300 Stamina"},"4421":{"bonus":"+180 Critical Strike"},"4422":{"bonus":"+200 Stamina"},"4423":{"bonus":"+180 Intellect"},"4424":{"bonus":"+180 Critical Strike"},"4426":{"bonus":"+175 Haste"},"4427":{"bonus":"+175 Critical Strike"},"4428":{"bonus":"+140 Agility & Minor Speed Increase"},"4429":{"bonus":"+140 Mastery & Minor Speed Increase"},"4430":{"bonus":"+170 Haste"},"4431":{"bonus":"+170 Haste"},"4432":{"bonus":"+170 Strength"},"4433":{"bonus":"+170 Mastery"},"4434":{"bonus":"+165 Intellect"},"4993":{"bonus":"+170 Parry"},"4066":{"bonus":"Mending"},"4258":{"bonus":"+50 Agility"},"4256":{"bonus":"+50 Strength"},"4257":{"bonus":"+50 Intellect"},"4061":{"bonus":"+50 Mastery"},"4062":{"bonus":"+30 Stamina and Minor Movement Speed"},"4063":{"bonus":"+15 All Stats"},"4064":{"bonus":"+56 PvP Power"},"4065":{"bonus":"+50 Haste"},"4067":{"bonus":"Avalanche"},"4068":{"bonus":"+50 Haste"},"4069":{"bonus":"+50 Haste"},"4070":{"bonus":"+55 Stamina"},"4071":{"bonus":"+50 Critical Strike"},"4072":{"bonus":"+30 Intellect"},"4073":{"bonus":"+16 Stamina"},"4074":{"bonus":"Elemental Slayer"},"4075":{"bonus":"+35 Strength"},"4076":{"bonus":"+35 Agility"},"4077":{"bonus":"+40 PvP Resilience"},"4082":{"bonus":"+50 Haste"},"4083":{"bonus":"Hurricane"},"4084":{"bonus":"Heartsong"},"4085":{"bonus":"+50 Mastery"},"4086":{"bonus":"+50 Dodge"},"4087":{"bonus":"+50 Critical Strike"},"4088":{"bonus":"+40 Spirit"},"4089":{"bonus":"+50 Critical Strike"},"4090":{"bonus":"+30 Stamina"},"4091":{"bonus":"+40 Intellect"},"4092":{"bonus":"+50 Critical Strike"},"4093":{"bonus":"+50 Spirit"},"4094":{"bonus":"+50 Mastery"},"4095":{"bonus":"+50 Haste"},"4096":{"bonus":"+50 Intellect"},"4097":{"bonus":"Power Torrent"},"4098":{"bonus":"Windwalk"},"4099":{"bonus":"Landslide"},"4100":{"bonus":"+65 Critical Strike"},"4101":{"bonus":"+65 Critical Strike"},"4102":{"bonus":"+20 All Stats"},"4103":{"bonus":"+75 Stamina"},"4105":{"bonus":"+25 Agility and Minor Movement Speed"},"4104":{"bonus":"+35 Mastery and Minor Movement Speed"},"4106":{"bonus":"+50 Strength"},"4107":{"bonus":"+65 Mastery"},"4108":{"bonus":"+65 Haste"},"4227":{"bonus":"+130 Agility"},"3225":{"bonus":"Executioner"},"3844":{"bonus":"+45 Spirit"},"3239":{"bonus":"Icebreaker Weapon"},"3241":{"bonus":"Lifeward"},"3247":{"bonus":"+70 Attack Power versus Undead"},"3251":{"bonus":"Giantslaying"},"3830":{"bonus":"+50 Spell Power"},"3828":{"bonus":"+42 Attack Power"},"1103":{"bonus":"+26 Agility"},"3273":{"bonus":"Deathfrost"},"3790":{"bonus":"Black Magic"},"1606":{"bonus":"+25 Attack Power"},"3827":{"bonus":"+55 Attack Power"},"3833":{"bonus":"+32 Attack Power"},"3834":{"bonus":"+63 Spell Power"},"3789":{"bonus":"Berserking"},"3788":{"bonus":"+50 Critical Strike"},"3854":{"bonus":"+81 Spell Power"},"3855":{"bonus":"+69 Spell Power"},"3233":{"bonus":"+250 Mana"},"3231":{"bonus":"+15 Haste"},"3234":{"bonus":"+20 Critical Strike"},"1952":{"bonus":"+20 Dodge"},"3236":{"bonus":"+200 Health"},"4747":{"bonus":"+16 Agility"},"1147":{"bonus":"+18 Spirit"},"2381":{"bonus":"+20 Spirit"},"3829":{"bonus":"+17 Attack Power"},"1075":{"bonus":"+22 Stamina"},"5259":{"bonus":"+20 Agility"},"1119":{"bonus":"+16 Intellect"},"1600":{"bonus":"+19 Attack Power"},"3243":{"bonus":"+28 PvP Power"},"3244":{"bonus":"+14 Spirit and +14 Stamina"},"3245":{"bonus":"+20 PvP Resilience"},"4748":{"bonus":"+16 Agility"},"1951":{"bonus":"+18 Dodge"},"3246":{"bonus":"+28 Spell Power"},"3826":{"bonus":"+24 Critical Strike"},"2661":{"bonus":"+3 All Stats"},"3252":{"bonus":"+8 All Stats"},"3253":{"bonus":"+2% Threat and +10 Parry"},"3256":{"bonus":"+10 Agility and +40 Armor"},"2326":{"bonus":"+23 Spell Power"},"3294":{"bonus":"+25 Stamina"},"1953":{"bonus":"+22 Dodge"},"3831":{"bonus":"+23 Haste"},"3296":{"bonus":"+10 Spirit and 2% Reduced Threat"},"3297":{"bonus":"+275 Health"},"3232":{"bonus":"+15 Stamina and Minor Speed Increase"},"3824":{"bonus":"+12 Attack Power"},"1128":{"bonus":"+25 Intellect"},"3825":{"bonus":"+15 Haste"},"1099":{"bonus":"+22 Agility"},"1603":{"bonus":"+22 Attack Power"},"3832":{"bonus":"+10 All Stats"},"1597":{"bonus":"+16 Attack Power"},"2332":{"bonus":"+30 Spell Power"},"3845":{"bonus":"+25 Attack Power"},"3850":{"bonus":"+40 Stamina"},"4746":{"bonus":"+7 Weapon Damage"},"2666":{"bonus":"+30 Intellect"},"2667":{"bonus":"+35 Attack Power"},"2668":{"bonus":"+20 Strength"},"2669":{"bonus":"+40 Spell Power"},"2670":{"bonus":"+35 Agility"},"2671":{"bonus":"+50 Arcane and Fire Spell Power"},"2672":{"bonus":"+54 Shadow and Frost Spell Power"},"2673":{"bonus":"Mongoose"},"2674":{"bonus":"Spellsurge"},"2675":{"bonus":"Battlemaster"},"3846":{"bonus":"+40 Spell Power"},"3222":{"bonus":"+20 Agility"},"2657":{"bonus":"+12 Agility"},"2622":{"bonus":"+12 Dodge"},"2647":{"bonus":"+12 Strength"},"1891":{"bonus":"+4 All Stats"},"2648":{"bonus":"+14 Dodge"},"5183":{"bonus":"+15 Spell Power"},"2679":{"bonus":"+12 Spirit"},"2649":{"bonus":"+12 Stamina"},"5184":{"bonus":"+15 Spell Power"},"2653":{"bonus":"+12 Dodge"},"2654":{"bonus":"+12 Intellect"},"2655":{"bonus":"+15 Parry"},"2656":{"bonus":"+10 Spirit and +10 Stamina"},"2658":{"bonus":"+10 Haste and +10 Critical Strike"},"2659":{"bonus":"+150 Health"},"2662":{"bonus":"+120 Armor"},"5237":{"bonus":"+15 Spirit"},"3150":{"bonus":"+14 Spirit"},"2933":{"bonus":"+15 PvP Resilience"},"2934":{"bonus":"+10 Critical Strike"},"2935":{"bonus":"+15 Critical Strike"},"5250":{"bonus":"+15 Strength"},"5255":{"bonus":"+13 Attack Power"},"2937":{"bonus":"+20 Spell Power"},"2322":{"bonus":"+19 Spell Power"},"369":{"bonus":"+12 Intellect"},"5257":{"bonus":"+12 Attack Power"},"2938":{"bonus":"+16 PvP Power"},"5258":{"bonus":"+12 Agility"},"2939":{"bonus":"Minor Speed and +6 Agility"},"2940":{"bonus":"Minor Speed and +9 Stamina"},"1071":{"bonus":"+18 Stamina"},"3229":{"bonus":"+12 PvP Resilience"},"5260":{"bonus":"+18 Dodge"},"4723":{"bonus":"+2 Weapon Damage"},"249":{"bonus":"+2 Beastslaying"},"250":{"bonus":"+1  Weapon Damage"},"723":{"bonus":"+3 Intellect"},"255":{"bonus":"+3 Spirit"},"241":{"bonus":"+2 Weapon Damage"},"943":{"bonus":"+3 Weapon Damage"},"853":{"bonus":"+6 Beastslaying"},"854":{"bonus":"+6 Elemental Slayer"},"4745":{"bonus":"+3 Weapon Damage"},"1897":{"bonus":"+5 Weapon Damage"},"803":{"bonus":"Fiery Weapon"},"912":{"bonus":"Demonslaying"},"963":{"bonus":"+7 Weapon Damage"},"805":{"bonus":"+4 Weapon Damage"},"1894":{"bonus":"Icy Chill"},"1896":{"bonus":"+9 Weapon Damage"},"1898":{"bonus":"Lifestealing"},"1899":{"bonus":"Unholy Weapon"},"1900":{"bonus":"Crusader"},"1903":{"bonus":"+9 Spirit"},"1904":{"bonus":"+9 Intellect"},"2443":{"bonus":"+7 Frost Spell Damage"},"2504":{"bonus":"+30 Spell Power"},"2505":{"bonus":"+29 Spell Power"},"2563":{"bonus":"+15 Strength"},"2564":{"bonus":"+15 Agility"},"2567":{"bonus":"+20 Spirit"},"2568":{"bonus":"+22 Intellect"},"2646":{"bonus":"+25 Agility"},"3869":{"bonus":"Blade Ward"},"3870":{"bonus":"Blood Draining"},"4720":{"bonus":"+5 Health"},"41":{"bonus":"+5 Health"},"44":{"bonus":"Absorption (10)"},"924":{"bonus":"+2 Dodge"},"24":{"bonus":"+5 Mana"},"4721":{"bonus":"+1 Stamina"},"242":{"bonus":"+15 Health"},"243":{"bonus":"+1 Spirit"},"783":{"bonus":"+10 Armor"},"246":{"bonus":"+20 Mana"},"4725":{"bonus":"+1 Agility"},"248":{"bonus":"+1 Strength"},"254":{"bonus":"+25 Health"},"4727":{"bonus":"+3 Spirit"},"66":{"bonus":"+1 Stamina"},"247":{"bonus":"+1 Agility"},"4722":{"bonus":"+1 Stamina"},"4724":{"bonus":"+1 Agility"},"744":{"bonus":"+20 Armor"},"4733":{"bonus":"+30 Armor"},"4728":{"bonus":"+3 Spirit"},"4730":{"bonus":"+3 Stamina"},"823":{"bonus":"+3 Strength"},"63":{"bonus":"Absorption (25)"},"843":{"bonus":"+30 Mana"},"844":{"bonus":"+2 Mining"},"845":{"bonus":"+2 Herbalism"},"2603":{"bonus":"+2 Fishing"},"4729":{"bonus":"+3 Intellect"},"847":{"bonus":"+1 All Stats"},"4731":{"bonus":"+3 Stamina"},"848":{"bonus":"+30 Armor"},"849":{"bonus":"+3 Agility"},"850":{"bonus":"+35 Health"},"4735":{"bonus":"+5 Spirit"},"724":{"bonus":"+3 Stamina"},"925":{"bonus":"+3 Dodge"},"4737":{"bonus":"+5 Stamina"},"4736":{"bonus":"+5 Spirit"},"856":{"bonus":"+5 Strength"},"857":{"bonus":"+50 Mana"},"4726":{"bonus":"+3 Spirit"},"863":{"bonus":"+10 Parry"},"865":{"bonus":"+5 Skinning"},"866":{"bonus":"+2 All Stats"},"884":{"bonus":"+50 Armor"},"4740":{"bonus":"+5 Agility"},"4738":{"bonus":"+5 Stamina"},"905":{"bonus":"+5 Intellect"},"852":{"bonus":"+5 Stamina"},"906":{"bonus":"+5 Mining"},"907":{"bonus":"+7 Spirit"},"908":{"bonus":"+50 Health"},"909":{"bonus":"+5 Herbalism"},"4734":{"bonus":"+3 Agility"},"4739":{"bonus":"+5 Strength"},"911":{"bonus":"Minor Speed Increase"},"4741":{"bonus":"+7 Spirit"},"913":{"bonus":"+65 Mana"},"923":{"bonus":"+5 Dodge"},"904":{"bonus":"+5 Agility"},"927":{"bonus":"+7 Strength"},"928":{"bonus":"+3 All Stats"},"4743":{"bonus":"+7 Stamina"},"930":{"bonus":"+2% Mount Speed"},"931":{"bonus":"+10 Haste"},"1883":{"bonus":"+7 Intellect"},"1884":{"bonus":"+9 Spirit"},"1885":{"bonus":"+9 Strength"},"1886":{"bonus":"+9 Stamina"},"1887":{"bonus":"+7 Agility"},"4742":{"bonus":"+7 Strength"},"1889":{"bonus":"+70 Armor"},"1890":{"bonus":"+10 Spirit and +10 Stamina"},"4744":{"bonus":"+7 Stamina"},"929":{"bonus":"+7 Stamina"},"851":{"bonus":"+5 Spirit"},"1892":{"bonus":"+100 Health"},"1893":{"bonus":"+100 Mana"},"2565":{"bonus":"+9 Spirit"},"2650":{"bonus":"+15 Spell Power"},"2613":{"bonus":"+2% Threat"},"2614":{"bonus":"+20 Shadow Spell Power"},"2615":{"bonus":"+20 Frost Spell Power"},"2616":{"bonus":"+20 Fire Spell Power"},"2617":{"bonus":"+16 Spell Power"},"910":{"bonus":"+8 Agility and +8 Dodge"},"2621":{"bonus":"2% Reduced Threat"},"3238":{"bonus":"Gatherer"},"3858":{"bonus":"+10 Critical Strike"},"4732":{"bonus":"+5 Fishing"}}';
	
	var $output = '';
	
	var $isBag = false, $isSetPiece = false, $isSocketable = false, $isEnchant = false, $isArmor = false;
	var $isWeapon = false, $isParseError = false, $isParseMode = false, $isSocketBonus = false;
	
	/*
	
	*/
	function item($id, $api_data, $userdata=null, $gems=false)
	{
		global $api, $roster;
		
		$this->user = $userdata;
		$this->dapi = $api_data;
		$this->dgems = $gems;
		$this->enchants = json_decode($this->de,true);
		
		//d($roster->locale->act['apiitem']);
		$this->build_tooltip();
		
		return $this->output;
		
	}
	
	function build_tooltip()
	{
		$this->output = '';
		$this->output .= '<div class="wiki-tooltip">';
		
		$this->_getCaption();
		
		$this->output .= '<ul class="item-specs">';
		
			$this->_getnameDescription();
			$this->_getItemLevel();
			$this->_getBindType();
			$this->_getItemClass();
			$this->_getStats();
		
		$this->output .= '</ul><ul class="item-specs">';
		
			$this->_getGems();
		
		$this->output .= '</ul><ul class="item-specs">';
		
			$this->_getPassiveBonus();
			$this->_getDescription();
		
		$this->output .= '</ul><ul class="item-specs">';
		
		if( isset($this->dapi['allowableClasses']) )
		{
			$this->_getRequiredClasses();
		}
		if ($this->dapi['requiredLevel'] > 0)
		{
			$this->_getRequiredLevel();
		}
		
		$this->output .= '</ul>';
		
		$this->output .= '</div>';
	}
	
	function _getCaption()
	{
		$this->output .= '<h3 class="color-q'.(isset($this->user['quality']) ? $this->user['quality'] : $this->dapi['quality']).'">'.$this->dapi['name'].'</h3>';
	}
	
	function _getnameDescription()
	{
		if ( !empty($this->user['nameDescription']) OR !empty($this->dapi['nameDescription']))
		{
			$this->output .= '<li style="color:#'.$this->dapi['nameDescriptionColor'].'">'.(isset($this->user['nameDescription']) ? $this->user['nameDescription'] : $this->dapi['nameDescription']).'</li>';
		}
	}
	
	function _getItemLevel()
	{
		global $roster;
		$this->output .= '<li class="color-tooltip-yellow">Item Level '.(isset($this->user['itemLevel']) ? $this->user['itemLevel'] : $this->dapi['itemLevel']).'</li>';
	}
	
	function _getBindType()
	{
		global $roster;
		if ( !empty($this->dapi['itemBind']) )
		{
			$this->output .= '<li>'.$roster->locale->act['apiitem']['bind'][$this->dapi['itemBind']].'</li>';
		}
	}
	
	function _getItemClass()
	{
		global $roster;
		if ($this->dapi['itemClass'] == '4')
		{
			$this->output .= '<li><span class="float-right">'.$roster->locale->act['apiitem']['itemSubClass'][$this->dapi['itemClass']][$this->dapi['itemSubClass']].'</span>';
			$this->output .= ''.$roster->locale->act['apiitem']['slotType'][$this->dapi['inventoryType']].'</li>';
			//<li><span class="float-right">Miscellaneous</span>Finger</li>
			if ($this->dapi['baseArmor'] > 0)
			{
				$this->output .= '<li>'.(isset($this->user['armor']) ? $this->user['armor'] : $this->dapi['baseArmor']) .' Armor</li>';
			}
			$this->isArmor = true;
		}
		if ($this->dapi['itemClass'] == '2' )
		{
			if(isset($this->user['weaponInfo']))
			{
				$this->output .= '<li><span class="float-right">'.$roster->locale->act['apiitem']['itemSubClass'][$this->dapi['itemClass']][$this->dapi['itemSubClass']].'</span>';
				$this->output .= $roster->locale->act['apiitem']['slotType'][$this->dapi['inventoryType']].''.'</li>';
				$this->output .= '<li><span class="float-right">'.sprintf( $roster->locale->act['apiitem']['speed'], $this->dapi['weaponInfo']['weaponSpeed']).'</span>';
				$this->output .= sprintf( $roster->locale->act['apiitem']['damage'], $this->user['weaponInfo']['damage']['min'].' - '.$this->user['weaponInfo']['damage']['max']).'</li>';
				$this->output .= '<li>'.sprintf( $roster->locale->act['apiitem']['dps'], number_format($this->user['weaponInfo']['dps'], 1, '.', '')).'</li>';
			}
			else
			{
				$this->output .= '<li><span class="float-right">'.$roster->locale->act['apiitem']['itemSubClass'][$this->dapi['itemClass']][$this->dapi['itemSubClass']].'</span>';
				$this->output .= $roster->locale->act['apiitem']['slotType'][$this->dapi['inventoryType']].''.'</li>';
				$this->output .= '<li><span class="float-right">'.sprintf( $roster->locale->act['apiitem']['speed'], $this->dapi['weaponInfo']['weaponSpeed']).'</span>';
				$this->output .= sprintf( $roster->locale->act['apiitem']['damage'], $this->dapi['weaponInfo']['damage']['min'].' - '.$this->dapi['weaponInfo']['damage']['max']).'</li>';
				$this->output .= '<li>'.sprintf( $roster->locale->act['apiitem']['dps'], number_format($this->dapi['weaponInfo']['dps'], 1, '.', '')).'</li>';
			}
			$this->isWeapon = true;
			
		}
		if ($this->dapi['itemClass'] == '1' )
		{
			$this->output .= '<li>'.$roster->locale->act['apiitem']['itemSubClass'][$this->dapi['itemClass']][$this->dapi['itemSubClass']].'</li>';
			$this->output .= '<li>'.$roster->locale->act['apiitem']['slotType'][$this->dapi['inventoryType']].''.'</li>';
			$this->output .= '<li>'.$this->dapi['containerSlots'].'</li>';
			$this->output .= '<li>'.$this->dapi['containerSlots'].' Slot '.$roster->locale->act['apiitem']['itemSubClass'][$this->dapi['itemClass']][$this->dapi['itemSubClass']].'</li>';
			$this->isBag = true;
		}
	}
	
	function _getStats()
	{
		global $roster;
		if (isset($this->user['stats']))
		{
			foreach ($this->user['stats'] as $s => $stat)
			{
				$this->output .= '<li id="stat-'.$stat['stat'].'" class="color-tooltip-green">'.sprintf( $roster->locale->act['apiitem']['itemstat'][$stat['stat']], $stat['amount']).'<li>';
			}
		}
		else
		{
			if ( !empty($this->dapi['bonusStats']) )
			{
				foreach ($this->dapi['bonusStats'] as $s => $stat)
				{
					$this->output .= '<li id="stat-'.$stat['stat'].'" class="color-tooltip-green">'.sprintf( $roster->locale->act['apiitem']['itemstat'][$stat['stat']], $stat['amount']).'<li>';
				}
			}
		}
		
	}
	
	function _getGems()
	{
		global $roster;
		
		$sockets = array(
			'Meta' => 1,
			'Red' => 2,
			'Yellow' => 3,
			'Blue' => 4,
			'Hydraulic' => 5,
			'Cogwheel' => 6,
			'Prismatic' => 7,
			'Iron' => 8,
			'Blood' => 9,
			'Shadow' => 10,
			'Fel' => 11,
			'Arcane' => 12,
			'Frost' => 13,
			'Fire' => 14,
			'Water' => 15,
			'Life' => 16,
			'Wind' => 17,
			'Holy' => 18,
		);
		if ( isset($this->dapi['socketInfo']) )
		{
			$num_s = count($this->dapi['socketInfo']['sockets']);
			$num_g = 0;
			$gm = array();
			$fgm = $this->dapi['socketInfo']['sockets'];
			for($e=0;$e<=4;$e++)
			{
				if (isset($this->user['tooltipParams']['gem'.$e.'']))
				{
					$num_g++;
					$fgm[$e]['id'] = $this->user['tooltipParams']['gem'.$e.''];
				}
			}
			//echo $num_g.' - '.$num_s.'<br>';
			if ($num_g == $num_s)
			{
				foreach ($fgm as $idx => $gem)
				{
					$g = $roster->api2->fetch('item',array('id'=>$gem['id']));
					$gname = '';
					if ($g['itemSubClass'] < 8)
					{
						$gname = $g['gemInfo']['bonus']['name'];
					}
					else
					{
						$gname = $g['name'];
					}
					$this->output .= '<li>
						<span class="icon-socket socket-type-'.$sockets[ucfirst(strtolower($gem['type']))].'">
							<a href="#" class="gem">
								<img src="'. $roster->config['interface_url'].'Interface/Icons/'.$g['icon'].'.'.$roster->config['img_suffix'].'" alt="" />
								<span class="frame" />
							</a>
						</span>
						'.$gname.'
						<span class="clear"><!-- --></span>
					</li>';
				}
			}
			else
			{
				$egem = ($num_s-1);
				if ($num_g > 0)
				{
					foreach ($fgm as $idx => $gem)
					{
						$g = $roster->api2->fetch('item',array('id'=>$gem['id']));
						$gname = '';
						if ($g['itemSubClass'] < 8)
						{
							$gname = $g['gemInfo']['bonus']['name'];
						}
						else
						{
							$gname = $g['name'];
						}
						
						$this->output .= '<li>
							<span class="icon-socket socket-type-'.$sockets[ucfirst(strtolower($gem['type']))].'">
								<a href="#" class="gem">
									<img src="'. $roster->config['interface_url'].'Interface/Icons/'.$g['icon'].'.'.$roster->config['img_suffix'].'" alt="" />
									<span class="frame" />
								</a>
							</span>
							'.$gname.'
							<span class="clear"><!-- --></span>
						</li>';
						$egem-1;
					}
				}
				for($i=0;$i<=$egem;$i++)
				{
					$this->output .= '<li>
						<span class="icon-socket socket-type-'.$sockets[ucfirst(strtolower($this->dapi['socketInfo']['sockets'][$i]['type']))].'">
								<span class="empty"></span>
								<span class="frame"></span>
						</span>
							'.$this->dapi['socketInfo']['sockets'][$i]['type'].' Socket
						<span class="clear"><!-- --></span>
					</li>';
				}
			}
		}
	}
	
	function _getPassiveBonus()
	{
		if (!empty($this->dapi['itemSpells']))
		{
			foreach($this->dapi['itemSpells'] as $i => $spell)
			{
				if (!empty($spell['spell']['description']))
				{
					$this->output .= '<li class="color-q2 item-spec-group">'.$spell['spell']['description'].'</li>';
				}
			}
		}
	}
	
	function _getDescription()
	{
		if (isset($this->dapi['description']) && !empty($this->dapi['description']))
		{
			$this->output .= '<li class="color-tooltip-yellow">"'.$this->dapi['description'].'"</li>';
		}
	}
	
	function _getRequiredClasses()
	{
		global $roster;
		$c = array();
		foreach($this->dapi['allowableClasses'] as $id => $classes)
		{
			$c[] = '<span class="color-c'.$classes.'">'.$roster->locale->act['id_to_class'][$classes].'</span>';
		}
		$cl = implode(', ', $c);
		$this->output .= '<li>'.$roster->locale->act['apiitem']['classes'].' '.$cl.'</li>';
	}
	
	function _getRequiredLevel()
	{
		global $roster;
		$this->output .= '<li>'.sprintf($roster->locale->act['apiitem']['reqlevel'], $this->dapi['requiredLevel']).'</li>';
	}
	
	
}



