<?php

if ( !defined('ROSTER_INSTALLED') ) 
{ 
    exit('Detected invalid access to this file!'); 
}

class usermsg
{
	
	function _GetLastMsg( $conversation_id )
	{
		global $roster, $addon;
		
		$con = "SELECT * FROM `" . $roster->db->table('messages',$addon['basename']) . "` WHERE `conversation_id` = '".$conversation_id."' ORDER BY `id` DESC LIMIT 1";

		$con_results = $roster->db->query($con);

		$row = $roster->db->fetch($con_results);
		
		return $row;
	}
}