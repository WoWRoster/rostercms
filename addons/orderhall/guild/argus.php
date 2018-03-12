<?php

$d = array(

	'THE ASSAULT BEGINS' => array (
		'47221'=>'The Hand of Fate',
		'47222'=>'Two If By Sea',
		'47223'=>'Light\'s Exodus',
		'47224'=>'The Vindicaar',
		'48440'=>'Into the Night',
		'46938'=>'Alone in the Abyss',
		'47589'=>'Righteous Fury',
		'46297'=>'Overwhelming Power',
		'48483'=>'A Stranger\'s Plea',
		'47627'=>'Vengeance',
		'47641'=>'Sign of Resistance',
		'46732'=>'The Prophet\'s Gambit',
		'46816'=>'Rendezvous',
		'46839'=>'From Darkness',
		'46840'=>'Prisoners No More',
		'46841'=>'Threat Reduction',
		'46842'=>'A Strike at the Heart',
		'46843'=>'Return to the Vindicaar',
		'48500'=>'A Moment of Respite',
		'47431'=>'Gathering Light',
		'46213'=>'Crystals Not Included',
		'40238'=>'A Grim Equation',
		'47541'=>'The Best Prevention',
		'47508'=>'Fire at Will',
		'47771'=>'Locating the Longshot',
		'47526'=>'Bringing the Big Guns',
		'47754'=>'Lightly Roasted',
		'47653'=>'Light\'s Return',
		'47743'=>'The Child of Light and Shadow',
		'49143'=>'Essence of the Light Mother',
		'47287'=>'The Vindicaar Matrix Core',
		'48559'=>'An Offering of Light',
		'48199'=>'The Burning Heart',
		'48200'=>'Securing a Foothold',
		'48201'=>'Reinforce Light\'s Purchase',
		'48202'=>'Reinforce the Veiled Den',
		'47473'=>'Sizing Up The Opposition',
	),
	'DARK AWAKENINGS' => array (
		'47889'=>'The Speaker Calls',
		'47890'=>'Visions of Torment',
		'47891'=>'Dire News',
		'47892'=>'Storming the Citadel',
		'47986'=>'Scars of the Past',
		'47987'=>'Preventive Measures',
		'47988'=>'Chaos Theory',
		'47991'=>'Dark Machinations',
		'47990'=>'A Touch of Fel',
		'47989'=>'Heralds of Apocalypse',
		'47992'=>'Dawn of Justice',
		'47993'=>'Lord of the Spire',
		'47994'=>'Forming a Bond',
		'48081'=>'A Floating Ruin',
		'46815'=>'Mac\'Aree, Jewel of Argus',
		'46818'=>'Defenseless and Afraid',
		'46834'=>'Khazaduum, First of His Name',
		'47066'=>'Consecrating Ground',
		'46941'=>'The Path Forward',
		'47686'=>'Not-So-Humble Beginnings',
		'47882'=>'Conservation of Magic',
		'47688'=>'Invasive Species',
		'47883'=>'The Longest Vigil',
		'47689'=>'Gatekeeper\'s Challenge: Tenacity',
		'47685'=>'Gatekeeper\'s Challenge: Cunning',
		'47687'=>'Gatekeeper\'s Challenge: Mastery',
		'47690'=>'The Defiler\'s Legacy',
		'48107'=>'The Sigil of Awakening',
	),
		'WAR OF LIGHT AND SHADOW' => array(
		'48344'=>'We Have a Problem',
		'47691'=>'A Non-Prophet Organization',
		'47854'=>'Wrath of the Hight Exarch',
		'47995'=>'Overt Ops',
		'47853'=>'Flanking Maneuvers',
		'48345'=>'Talgath\'s Forces',
		'47855'=>'What Might Have Been',
		'47856'=>'Across the Universe',
		'47416'=>'Shadow of the Triumvirate',
		'47238'=>'The Seat of the Triumvirate',
		'40761'=>'Whispers from Oronaar',
		'47101'=>'Arkhaan\'s Prayers',
		'47180'=>'The Pulsing Madness',
		'47100'=>'Arkhaan\'s Pain',
		'47183'=>'Arkhaan\'s Plan',
		'47184'=>'Arkhaan\'s Peril',
		'47203'=>'Throwing Shade',
		'47217'=>'Sources of Darkness',
		'47218'=>'The Shadowguard Incursion',
		'47219'=>'A Vessel Made Ready',
		'47220'=>'A Beacon in the Dark',
		'48560'=>'An Offering of Shadow',
		'47654'=>'Seat of the Triumvirate: The Crest of Knowledge',
	),
);

if (!$roster->auth->allow_login)
{
	roster_useronly();
}

if ( !isset($_POST['process']) )
{

	$sql = 'SELECT * FROM `' . $roster->db->table('user_link', 'user') . '` WHERE `uid` = "' . $roster->auth->user['id'] . '"';
	$query = $roster->db->query($sql);
	while( $row = $roster->db->fetch($query) )
	{
		$roster->tpl->assign_block_vars('bpchars', array(
				'NAME'	=> $row['name'],
				'ID'	=> $row['member_id'],
			));
	}		
	$roster->tpl->set_handle('bpets', $addon['basename'] . '/usercharselect.html');
	$roster->tpl->display('bpets');
}
else
{
	roster_add_css($addon['css_url'],'module');
	
	$query = "SELECT * FROM `" . $roster->db->table('api_cache') . "` WHERE `id`='" . $_POST['member_id'] . "' AND `name`='quests' LIMIT 1;";
			$result = $roster->db->query($query);
			//echo (bool)$result;
			$row = $roster->db->fetch($result);
	$bppets = json_decode($row['json'],true);	

	$e = 0;
foreach ($d as $ach => $b)
{
	$e++;
	$total = $comp = 0;
	$roster->tpl->assign_block_vars('exp', array(
				'NAME'	=> $ach,
				'ID'	=> 'exp'.$e,
				'ACTIVE'	=> ($e == 1 ? ' active': ''),
				'ACTIVE2'	=> ($e == 1 ? ' in active': ''),
			));
	foreach ($b as $qid => $name)
	{
		$total++;
		if ( in_array($qid, $bppets) )
		{
			$comp++;
			$img = 'complete';
		}
		else
		{
			$img = 'incomplete';
		}
	
		$roster->tpl->assign_block_vars('exp.quest', array(
			'NAME'		=> $name,
			'IMG'		=> $img,
			'WOWHEAD'	=> 'http://www.wowhead.com/quest='.$qid.'',
		));
	}
	$roster->tpl->assign_block_vars('exp.per', array(
		'TOTAL'		=> $total,
		'COMP'		=> $comp,
		'BAR'		=> ceil($comp / $total * 100),
		'IMG_URL'	=> $addon['image_url'],
	));
}
	$roster->tpl->assign_vars(array(
			'IMG_URL'	=> $addon['image_url'],
		));
	$roster->tpl->set_handle('bquestlist', $addon['basename'] . '/argus.html');
	$roster->tpl->display('bquestlist');
}


























?>



