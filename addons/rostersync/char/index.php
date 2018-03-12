<?php

require_once ($addon['dir'] . 'inc/rsync_core.class.php');
$job = new rsync();

$job->_showHeader();


require_once ($addon['dir'] . 'inc/rostersync.lib.php');
$rsync = new rostersync;

if($roster->pages[2] == 'profile')
{
	if ($addon['config']['rsync_skip_start'] == 0 && !isset($_POST['action']) && !( isset($_GET['job_id']) || isset($_POST['job_id']) ))
	{
		$job->_showStartPage('profile');
	}
	if ($addon['config']['rsync_skip_start'] == 0 && isset($_POST['action']) && $_POST['action'] == 'start')
	{
		//d($roster);
		//$job->_start_profile();
		$rsync->_sync_member($roster->data['server'], $roster->data['member_id'], $roster->data['name'], $roster->data['region'], $roster->data['guild_id']);

		echo $rsync->build_update_table('character-info-success');
	}
	
	if ($addon['config']['rsync_skip_start'] == 1 && ( !isset($_GET['job_id']) || !isset($_POST['job_id']) ))
	{
		$rsync->_sync_member($roster->data['server'], $roster->data['member_id'], $roster->data['name'], $roster->data['region'], $roster->data['guild_id']);

		echo $rsync->build_update_table('character-info-success');
	}
}

$job->_showFooter();