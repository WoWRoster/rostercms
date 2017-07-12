<?php
/**
 * WoWRoster.net WoWRoster
 *
 * Roster upload rule config
 *
 *
 * @copyright  2002-2011 WoWRoster.net
 * @license    http://www.gnu.org/licenses/gpl.html   Licensed under the GNU General Public License v3.
 * @package    WoWRoster
 * @subpackage RosterCP
*/

if( !defined('IN_ROSTER') || !defined('IN_ROSTER_ADMIN') )
{
	exit('Detected invalid access to this file!');
}

$start = (isset($_GET['start']) ? $_GET['start'] : 0);

$roster->output['title'] .= $roster->locale->act['pagebar_rosterrank'];
include(ROSTER_LIB . 'config.lib.php');
$config = new roster_config( $roster->db->table('config') );

//user_desc
if( isset($_POST['process']) && $_POST['process'] == 'process' )
{
	foreach ($_POST['config_rank'] as $slug => $d)
	{
		$query = "UPDATE `" . $roster->db->table('guild_rank') . "` SET `title` = '" . $d['name'] . "', `access` = '" . implode(':', $d['access']) . "' WHERE `slug` = '" . $slug . "';";
		$roster->db->query($query);
	}
}

$query = "SELECT * FROM `" . $roster->db->table('guild_rank') . "`;";
$result = $roster->db->query($query);

if (!$result)
{
	die_quietly('Could not fetch menu configuration from database. MySQL said: <br />' . $roster->db->error(),'Roster',__FILE__,__LINE__,$query);
}

while($r = $roster->db->fetch($result))
{
	$roster->tpl->assign_block_vars('rank', array(
		'ROW_CLASS' => $roster->switch_row_class(),
		'ID'        => $r['id'],
		'IDC'       => $c++,
		'RANK_NUM'	=> $r['slug'],
		'RANK'      => 'Rank '.$r['rank'],
		'TITLE'		=> $r['title'],
		'ACCESS'    => $roster->auth->rosterAccess(array('guild_id' => ''.$roster->data['guild_id'].'','name' => 'rank['.$r['slug'].'][access]', 'value' => $r['access']))
		)
	);
}

$roster->tpl->assign_vars(array(
  'L_USER_MANAGER' => $roster->locale->act['admin']['user_desc'],
	)
);

$roster->tpl->set_filenames(array('body' => 'admin/rosterranks.html'));
$body = $roster->tpl->fetch('body');
