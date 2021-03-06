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
$roster->output['show_menu'] = false;
include( ROSTER_LIB . 'update.lib.php' );
$update = new update;

$start = (isset($_GET['start']) ? $_GET['start'] : 0);

$roster->output['title'] .= $roster->locale->act['pagebar_dataman'];

// Change scope to guild, and rerun detection to load default
$roster->scope = 'guild';
$roster->get_scope_data();



// Get the scope select data
$dm_query = "SELECT `guild_name`, CONCAT(`region`,'-',`server`), `guild_id`"
	. " FROM `" . $roster->db->table('guild') . "`"
	. " ORDER BY `region` ASC, `server` ASC, `guild_name` ASC;";

$dm_result = $roster->db->query($dm_query);

if( !$dm_result )
{
	die_quietly($roster->db->error(), 'Database error', __FILE__, __LINE__, $dm_query);
}

$guilds = 0;
while( $data = $roster->db->fetch($dm_result, SQL_NUM) )
{
	$dm_select[$data[1]][$data[2]] = $data[0];
	$guilds++;
}

$roster->db->free_result($dm_result);

$roster->tpl->assign_vars(array(
	'S_DM_SELECT' => ($guilds > 1 ? true : false),
	)
);

if( count($dm_select) > 0 )
{
	foreach( $dm_select as $realm => $guild )
	{
		$roster->tpl->assign_block_vars('dm_select_group', array(
			'U_VALUE' => $realm
		));

		foreach( $guild as $id => $name )
		{
			$roster->tpl->assign_block_vars('dm_select_group.dm_select_row', array(
				'TEXT'       => $name,
				'U_VALUE'    => makelink('&amp;a=g:' . $id, true),
				'S_SELECTED' => ($id == $roster->data['guild_id'] ? true : false)
			));
		}
	}
}


$roster->tpl->assign_vars(array(
	'U_ACTION'   => makelink('&amp;start=' . $start),
	'U_GUILD_ID' => isset($roster->data['guild_id']) ? $roster->data['guild_id'] : '',

	'L_CLEAN_TIP'      => makeOverlib($roster->locale->act['clean_help'], $roster->locale->act['clean'], '', 2, '', ',WRAP'),

	'S_DATA'           => false,
	'S_RESPONSE'       => false,
	'S_RESPONSE_ERROR' => false,
	)
);


/**
 * Process a new line
 */
if( isset($_POST['process']) && $_POST['process'] == 'process' )
{
	// We have a response
	$roster->tpl->assign_var('S_RESPONSE',true);

	if( substr($_POST['action'],0,9) == 'delguild_' )
	{
		$sel_guild = substr($_POST['action'],9);
		$update->deleteGuild( $sel_guild, time() );

		$roster->scope = 'none';
		$roster->anchor = '';
	}
	elseif( isset($_POST['massdel']) )
	{
		$member_ids = array();
		foreach( $_POST['massdel'] as $member_id => $checked )
		{
			$member_ids[] = $member_id;
		}
		$member_ids = implode(',', $member_ids);

		$update->setMessage('<li>Deleting members "' . $member_ids . '".</li>');
		$update->deleteMembers( $member_ids );
	}
	elseif( substr($_POST['action'],0,4) == 'del_' )
	{
		$member_id = substr($_POST['action'],4);

		$update->setMessage('<li>Deleting member "' . $member_id . '".</li>');
		$update->deleteMembers( $member_id );
	}
	elseif( $_POST['action'] == 'clean' )
	{
		$update->enforceRules( time() );
	}

	$messages = $update->getMessages();
	$errors = $update->getErrors();

	// print the error messages
	if( !empty($errors) )
	{
		// We have errors
		$roster->tpl->assign_vars(array(
			'S_RESPONSE_ERROR'   => true,
			'RESPONSE_ERROR'     => $errors,
			'RESPONSE_ERROR_LOG' => htmlspecialchars(stripAllHtml($errors)),
			)
		);
	}

	$roster->tpl->assign_vars(array(
		'RESPONSE'      => $messages,
		'RESPONSE_POST' => htmlspecialchars(stripAllHtml($messages)),
		)
	);
}


/**
 * Actual list
 */
$query = "SELECT "
	. " COUNT( `member_id` )"
	. " FROM `" . $roster->db->table('members') . "`"
	. " WHERE `guild_id` = " . ( isset($roster->data['guild_id']) ? $roster->data['guild_id'] : 0 ) . ";";

$num_members = $roster->db->query_first($query);

if( $num_members > 0 )
{
	$roster->tpl->assign_var('S_DATA',true);

	// Draw the header line
	if ($start > 0)
	{
		$prev = '<a href="' . makelink('&amp;start=0') . '">|&lt;&lt;</a>&nbsp;&nbsp;<a href="' . makelink('&amp;start=' . ($start - 30)) . '">&lt;</a> ';
	}
	else
	{
		$prev = '';
	}

	if (($start+30) < $num_members)
	{
		$listing = ' <small>[' . $start . ' - ' . ($start+30) . '] of ' . $num_members . '</small>';
		$next = ' <a href="' . makelink('&amp;start=' . ($start+30)) . '">&gt;</a>&nbsp;&nbsp;<a href="' . makelink('&amp;start=' . ( floor( $num_members / 30) * 30 )) . '">&gt;&gt;|</a>';
	}
	else
	{
		$listing = ' <small>[' . $start . ' - ' . ($num_members) . '] of ' . $num_members . '</small>';
		$next = '';
	}

	$roster->tpl->assign_vars(array(
		'PREV'    => $prev,
		'NEXT'    => $next,
		'LISTING' => $listing
		)
	);

	$i=0;

	$query = "SELECT `member_id`, `name`, `server`, `region`, `class`, `level`"
		. " FROM `" . $roster->db->table('members') . "`"
		. " WHERE `guild_id` = " . $roster->data['guild_id']
		. " ORDER BY `name` ASC"
		. " LIMIT " . ($start > 0 ? $start : 0) . ", 30;";

	$result = $roster->db->query($query);

	while( $row = $roster->db->fetch($result) )
	{
		$roster->tpl->assign_block_vars('data_list', array(
			'ROW_CLASS' => $roster->switch_row_class(),
			'ID'        => $row['member_id'],
			'NAME'      => $row['name'],
			'SERVER'    => $row['server'],
			'REGION'    => $row['region'],
			'CLASS'     => $row['class'],
			'LEVEL'     => $row['level'],
			)
		);

		$i++;
	}

	$roster->db->free_result($result);
}
$roster->output['show_menu'] = false;
$roster->tpl->set_filenames(array('body' => 'admin/data_manager.html'));
$body = $roster->tpl->fetch('body');
