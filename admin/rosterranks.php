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


//user_desc
if( isset($_POST['process']) && $_POST['process'] == 'process' )
{
	
}


$c = 1;
$ranks = 12;
$r = 0;
for ($r=0;$r<=$ranks;$r++)
{
	$roster->tpl->assign_block_vars('user', array(
		'ROW_CLASS' => $roster->switch_row_class(),
		'ID'        => $r,
		'IDC'       => $c++,
		'IDX'       => $c++,
		'ACTIVE'    => (bool)$row['active'],
		'NAME'      => 'Rank '.$r,
		'TOOLTIP'   => '',
		'EMAIL'     => $r,
		'ACCESS'    => $roster->auth->rosterAccess(array('guild_id' => ''.$roster->data['guild_id'].'','name' => 'rank'.$r.'[access]', 'value' => $roster->config['rank'.$r.''])),
		'PERMISS'	=> ''
		)
	);
}

$roster->tpl->assign_vars(array(
  'L_USER_MANAGER' => $roster->locale->act['admin']['user_desc'],
	)
);

$roster->tpl->set_filenames(array('body' => 'admin/user_manager.html'));
$body = $roster->tpl->fetch('body');
