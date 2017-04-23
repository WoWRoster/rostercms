<?php

$roster->output['show_header'] = false;
$roster->output['show_footer'] = false;
/*
guild_id:"3"
guild_name:"Blood God"
member_id:"1"
name:"Malauren"
region:"US"
server:"Hellscream"
*/
$server 	= $_GET['server'];
$memberId 	= $_GET['member_id'];
$memberName = $_GET['name'];
$region 	= $_GET['region'];
$guildId 	= $_GET['guild_id'];

	//*
	require_once ($addon['dir'] . 'inc/rsync_core.class.php');

	$rsync = new rsync();
	$rsync->is_cron = true;
	$rsync->synchMemberByID( $server, $memberId, $memberName, $region, $guildId);
	//*/
	$d = $rsync->status;
	/*
	array(	
		'guildInfo' => 0,
		'characterInfo' => 0,
		'skillInfo' => 0,
		'reputationInfo' => 0,
		'equipmentInfo' => 0,
		'talentInfo' => 0,
	);
	*/
	$r ='
	<div class="row cfg-row">
		<div class="col-md-2">'.$memberName.'</div>
		<div class="col-md-2">'.$server.'</div>
		<div class="col-md-2">'.$d['characterInfo'].'</div>
		<div class="col-md-2">'.$d['reputationInfo'].'</div>
		<div class="col-md-2">'.$d['equipmentInfo'].'</div>
		<div class="col-md-2">'.$d['talentInfo'].'</div>
	</div>
	';
	echo $r;