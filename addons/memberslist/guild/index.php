<?php
/**
 * WoWRoster.net WoWRoster
 *
 *
 * @copyright  2002-2011 WoWRoster.net
 * @license    http://www.gnu.org/licenses/gpl.html   Licensed under the GNU General Public License v3. * @package    MembersList
*/

if ( !defined('IN_ROSTER') )
{
	exit('Detected invalid access to this file!');
}

if (!isset($roster->data['guild_id'])) {
	return;
}

include_once ($addon['inc_dir'] . 'memberslist.php');

$memberlist = new memberslist;

$members_list_select = $memberlist->members_list_select;
$members_list_table = $memberlist->members_list_table;
$members_list_where = $memberlist->members_list_where;
$members_list_fields = $memberlist->members_list_fields;

$mainQuery =
	'SELECT '.
	'`members`.`member_id`, '.
	'`members`.`name`, '.
	'`members`.`class`, '.
	'`members`.`classid`, '.
	'`members`.`level`, '.
	"UNIX_TIMESTAMP(`members`.`last_online`) AS 'last_online_stamp', ".
	"DATE_FORMAT(  DATE_ADD(`members`.`last_online`, INTERVAL ".$roster->config['localtimeoffset']." HOUR ), '".$roster->locale->act['timeformat']."' ) AS 'last_online_format', ".
	'`members`.`note`, '.
	'`members`.`guild_title`, '.

	'`guild`.`update_time`, '.
	'`guild`.`factionEn`, '.

	"IF( `members`.`note` IS NULL OR `members`.`note` = '', 1, 0 ) AS 'nisnull', ".
	'`members`.`officer_note`, '.
	"IF( `members`.`officer_note` IS NULL OR `members`.`officer_note` = '', 1, 0 ) AS 'onisnull', ".
	"$members_list_select".
	'`members`.`guild_rank`, '.
	'`players`.`clientLocale`, '.
	'`players`.`server`, '.
	'`players`.`race`, '.
	'`players`.`sex`, '.
	"UNIX_TIMESTAMP( `players`.`dateupdatedutc`) AS 'last_update_stamp', ".
	"DATE_FORMAT(  DATE_ADD(`players`.`dateupdatedutc`, INTERVAL ".$roster->config['localtimeoffset']." HOUR ), '".$roster->locale->act['timeformat']."' ) AS 'last_update_format', ".
	"IF( `players`.`dateupdatedutc` IS NULL OR `players`.`dateupdatedutc` = '', 1, 0 ) AS 'luisnull', ".

	"GROUP_CONCAT( DISTINCT CONCAT( `proftable`.`skill_name` , '|', `proftable`.`skill_level` ) ORDER BY `proftable`.`skill_order`) as professions, ".
	"GROUP_CONCAT( DISTINCT CONCAT( `talenttable`.`build`, '|', `talenttable`.`tree` , '|', `talenttable`.`pointsspent` , '|', `talenttable`.`background`,'|', `talenttable`.`order` ) ORDER BY `talenttable`.`order`, `talenttable`.`build`) AS 'talents', ".
	"GROUP_CONCAT( DISTINCT CONCAT( `talenttre`.`tree` , '|', `talenttre`.`roles` , '|', `talenttre`.`icon` ) ORDER BY `talenttre`.`tree`) AS 'talents2' ".

	'FROM `'.$roster->db->table('members').'` AS members '.
	'LEFT JOIN `'.$roster->db->table('players').'` AS players ON `members`.`member_id` = `players`.`member_id` '.
	'LEFT JOIN `'.$roster->db->table('skills').'` AS proftable ON `members`.`member_id` = `proftable`.`member_id` '.
	'LEFT JOIN `'.$roster->db->table('talenttree').'` AS talenttable ON `members`.`member_id` = `talenttable`.`member_id` '.
	'LEFT JOIN `'.$roster->db->table('talenttree_data').'` AS talenttre ON `members`.`classid` = `talenttre`.`class_id` '.
	'LEFT JOIN `'.$roster->db->table('guild').'` AS guild ON `members`.`guild_id` = `guild`.`guild_id` '.$members_list_table;

$where[] = '`members`.`guild_id` = "'.$roster->data['guild_id'].'" ';

	if (!empty($members_list_where))
	{
		foreach ($members_list_where as $piwhere => $str)
		{
			foreach ($str as $e)
			{
				$where[] = $e;
			}
		}
	}
$group[] = '`members`.`member_id`';
$order_first[] = '`members`.`level` DESC';
$order_last[] = '`members`.`name` ASC';//, `talenttable`.`order` ASC ';

$FIELD['name'] = array (
	'lang_field' => 'name',
	'filt_field' => '`members`.`name`',
	'order'      => array( '`members`.`name` ASC' ),
	'order_d'    => array( '`members`.`name` DESC' ),
	'value'      => array($memberlist,'name_value'),
	'float'		=> 'left',
	'display'    => 3,
);

$FIELD['class'] = array (
	'lang_field' => 'class',
	'filt_field' => '`members`.`class`',
	'order'      => array( '`members`.`class` ASC' ),
	'order_d'    => array( '`members`.`class` DESC' ),
	'value'      => array($memberlist,'class_value'),
	'float'		=> 'left',
	'display'    => $addon['config']['member_class'],
);

$FIELD['level'] = array (
	'lang_field' => 'level',
	'filt_field' => '`members`.`level`',
	'order'      => array( '`members`.`level` DESC' ),
	'order_d'    => array( '`members`.`level` ASC' ),
	'value'      => array($memberlist,'level_value'),
	'float'		=> 'center',
	'display'    => $addon['config']['member_level'],
);

$FIELD['guild_title'] = array (
	'lang_field' => 'title',
	'order'      => array( '`members`.`guild_rank` ASC' ),
	'order_d'    => array( '`members`.`guild_rank` DESC' ),
	'float'		=> 'center',
	'display'    => $addon['config']['member_gtitle'],
);

$FIELD['lifetimeRankName'] = array (
	'lang_field' => 'currenthonor',
	'order'      => array( 'risnull', '`players`.`lifetimeHighestRank` DESC' ),
	'order_d'    => array( 'risnull', '`players`.`lifetimeHighestRank` ASC' ),
	'value'      => array($memberlist,'honor_value'),
	'float'		=> 'center',
	'display'    => $addon['config']['member_hrank'],
);

$FIELD['professions'] = array (
	'lang_field' => 'professions',
	'value'      => 'tradeskill_icons',
	'filter'     => false,
	'float'		=> 'center',
	'display'    => $addon['config']['member_prof'],
);

foreach ($members_list_fields as $pifield => $data)
{
	foreach ($data as $name => $d)
	{
		$FIELD[$name] = $d;
	}
}

$memberlist->prepareData($mainQuery, $where, $group, $order_first, $order_last, $FIELD, 'memberslist');

// Start output

echo $memberlist->makeMembersList('syellow');

/**
 * Controls Output of the Tradeskill Icons Column
 *
 * @param array $row - of character data
 * @return string - Formatted output
 */
function tradeskill_icons ( $row )
{
	global $roster, $addon;

	$cell_value ='';

	// Don't proceed for characters without data
	if ($row['clientLocale'] == '')
	{
		return '<div>&nbsp;</div>';
	}

	$profs = explode(',',$row['professions']);
	foreach ( $profs as $prof )
	{
		$tip=$toolTip=$toolTip=$pname=$vals=$min=$maxnull=null;
		list($pname,$vals) = explode("|",$prof);
		list($min,$max) = explode (":",$vals);
		$tip = $pname."<br>".$min.":".$max;
		$icon = isset($roster->locale->act['ts_iconArray'][$pname])?$roster->locale->act['ts_iconArray'][$pname]:'';
		if ($icon != '')
		{
			$toolTip = makeOverlib($tip,'','',2,'',',WRAP');
			$icons = '<div class="item-sm" '.$toolTip.'><img src="'.$roster->config['interface_url'].'Interface/Icons/'.$icon.'.'.$roster->config['img_suffix'].'" alt="" /><div class="mask"></div></div>';
			$cell_value .= $icons;
		}
	}
	return $cell_value;
}
/*
echo'<pre>';
print_r($roster);
echo '</pre>';
*/
