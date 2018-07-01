<?php
/**
 * WoWRoster.net WoWRoster
 *
 * ApiSync ajax function for Status update
 *
 * LICENSE: Licensed under the Creative Commons
 *          "Attribution-NonCommercial-ShareAlike 2.5" license
 *
 * @copyright  2002-2007 WoWRoster.net
 * @license    http://creativecommons.org/licenses/by-nc-sa/2.5   Creative Commons "Attribution-NonCommercial-ShareAlike 2.5"
 * @version    SVN: $Id: ApiSync.php 533 2011-09-15 08:33:29Z ulminia@gmail.com $
 * @link       http://www.wowroster.net
 * @since      File available since Release 2.6.0
 * @package    ApiSync
 * @subpackage Ajax
*/

if( !defined('IN_ROSTER') )
{
    //exit('Detected invalid access to this file!');
}
if (isset($_GET['adebug']) && $_GET['adebug'] == true)
{
	$roster->api2->ignore_cache = true;
}
require_once ($addon['dir'] . 'inc/rostersync.lib.php');
$rsync = new rostersync;
switch ($method)
{
	case 'character':
		$rsync = new rostersync('character');
		$b = $_GET['value'];
		list($id, $name, $server) = explode(':',$b);
		$rsync->_sync_member($server, $id, $name, $_GET['region'], $_GET['guild_id']);

		echo $rsync->build_update_table('character-success');

	break;
	
	case 'members':
		$rsync = new rostersync('character');
		$rsync->_sync_member($_GET['server'], $_GET['member_id'], $_GET['name'], $_GET['region'], $_GET['guild_id']);
		echo $rsync->build_update_table('character-success');

	break;
	
	case 'makelist':
		$e = $rsync->_getMembersToUpdate();
		echo json_encode($e);
	break;
	
    case 'guildupdate':
        $rsync = new rostersync('guild');
		$rsync->_sync_guild_members($_GET['server'], $_GET['guildname'], $_GET['region'], $_GET['guild_id']);
		
		echo $rsync->build_update_table('guild-success');

	break;
	
	case 'guildsync':
        $rsync = new rostersync('guild');
		$rsync->_sync_guild_update($_GET['server'], $_GET['guildname'], $_GET['region'], $_GET['guild_id']);
		
		echo $rsync->build_update_table('guild-success');

	break;
	
	case 'guildadd':
	echo 'im add a guild';
        $rsync = new rostersync('guild');
		$rsync->_sync_guild_add($_GET['server'], $_GET['guildname'], $_GET['region']);
		
		echo $rsync->build_update_table('guild-success');

	break;
}
//
if (isset($_GET['adebug']) && $_GET['adebug'] == true)
{
	include_once (ROSTER_BASE . 'footer.php');
}