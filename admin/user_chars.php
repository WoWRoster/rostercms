<?php

if( ! $roster->auth->getAuthorized( 'roster_cp' ) )
{
	echo $roster->auth->getLoginForm();
	return;
}
$uid = null;
if ( isset( $_GET['id'] ) )
{
	$uid = $_GET['id'];
}

	$dm_query1 = "SELECT * FROM `" . $roster->db->table('user_link','user') . "` WHERE `uid` = '".$uid."' ORDER BY `guild`";
	$dm_result = $roster->db->query($dm_query1);
	
	while( $row = $roster->db->fetch($dm_result) )
	{
		$gr = '';
		if ( $row['member_id'] != 0 )
		{
			$dm_queryx = "SELECT * FROM `" . $roster->db->table('members') . "` WHERE `member_id` = '".$row['member_id']."'";
			$dm_resultx = $roster->db->query($dm_queryx);
			$rowx = $roster->db->fetch($dm_resultx);
			$gr = $rowx['guild_rank'];
		}

		$roster->tpl->assign_block_vars('chars', array(
				'THUMB'		=> 'http://us.battle.net/static-render/us/'.$row['thumbnail'],
				'NAME'		=> $row['name'],
				'ID'		=> $row['link_id'],
				'LEVEL'		=> $row['level'],
				'RACE'		=> $row['race'],
				'GENDER'	=> $row['gender'],
				'SERVER'	=> $row['realm'],
				'GUILD'		=> $row['guild'],
				'GUILD_R'	=> $gr,
				'IS_MAIN'	=> (bool)$row['is_main'],
				'CLASS'		=> $roster->locale->act['id_to_class'][$row['class']],
				'PROFILE'	=> (( active_addon('info') && $row['realm'] && $row['member_id'] != 0 ) ? makelink('char-info&amp;a=c:' . $row['member_id']) : '' )
			)
		);
	}
	$roster->tpl->set_filenames(array('body' => 'admin/user_chars.html'));
	$body = $roster->tpl->fetch('body');
?>