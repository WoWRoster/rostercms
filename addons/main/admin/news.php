<?php
/**
 * Project: SigGen - Signature and Avatar Generator for WoWRoster
 * File: /admin/index.php
 *

 * @license    http://www.gnu.org/licenses/gpl.html   Licensed under the GNU General Public License v3.
 * @author Joshua Clark

 * @copyright 2005-2011 Joshua Clark
 * @package SigGen
 * @filesource
 */

// Bad monkey! You can view this directly. And you are stupid for trying. HA HA, take that!
if ( !defined('IN_ROSTER') )
{
    exit('Detected invalid access to this file!');
}

//d($_POST);
	if( isset($_POST['op']) && $_POST['op'] == 'process' )
	{
		$type = ( isset($_POST['type']) ? $_POST['type'] : '' );
		
		switch( $type )
		{
			case 'delete':
				$query = 'DELETE FROM `' . $roster->db->table('news',$addon['basename']) . '` WHERE `news_id` = "' . $_POST['id'] . '";';
				$roster->db->query($query);
			break;
			
		}
	}
	
	
	$query = "SELECT `news`.*, "
		. "DATE_FORMAT(  DATE_ADD(`news`.`date`, INTERVAL " . $roster->config['localtimeoffset'] . " HOUR ), '" . $roster->locale->act['timeformat'] . "' ) AS 'date_format', "
		. "COUNT(`comments`.`comment_id`) comm_count "
		. "FROM `" . $roster->db->table('news',$addon['basename']) . "` news "
		. "LEFT JOIN `" . $roster->db->table('comments',$addon['basename']) . "` comments USING (`news_id`) "
		. "GROUP BY `news`.`news_id`"
		. "ORDER BY `news`.`date` DESC;";

	$results = $roster->db->query($query);
	$numn = 1;
	$totaln = $roster->db->num_rows($results);

	while( $row = $roster->db->fetch($results) )
	{
		//echo $row['title'].'-'.$row['poster'].'-'.$row['text'].'<br />';
		$roster->tpl->assign_block_vars('news_admin', array(
			'POSTER'    => $row['poster'],
			'NUM'       => $numn,
			'TEXT'      => $row['text'],
			'NEWS_ID'   => $row['news_id'],
			'TITLE'     => $row['title'],
			'DATE'      => $row['date_format'],
			'U_EDIT'    => makelink('guild-'. $addon['basename'] .'-edit&amp;id='. $row['news_id']),
			'U_COMMENT' => makelink('guild-'. $addon['basename'] .'-comment&amp;id='. $row['news_id']),
			'U_EDIT'    => makelink('guild-'. $addon['basename'] .'-edit&amp;id='. $row['news_id']),
			'L_COMMENT' => ($row['comm_count'] != 1 ? sprintf($roster->locale->act['n_comments'], $row['comm_count']) : sprintf($roster->locale->act['n_comment'], $row['comm_count'])),
			'NEWS_TYPE' => $row['news_type']
		));
		$numn++;
	}
	
	$roster->tpl->set_filenames(array(
		'amain' => $addon['basename'] . '/admin/admin_news.html'
	));
	$roster->tpl->display('amain');
