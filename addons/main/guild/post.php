<?php

/*
	dev cache purge
*/
include_once (ROSTER_LIB . 'cache.php');
		$cache = new RosterCache();
		$cache->cleanCache();
		$update_sql = "UPDATE `roster_config` SET `config_value` = '" . generateRandomString(7) . "' WHERE `config_name` = 'css_js_query_string';";
		$result = $roster->db->query($update_sql);



function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


$id = $_GET['id'];

$roster->tpl->assign_vars(array(
	'S_COMMENTS'		=> false,
	'S_USER'			=> $roster->auth->user['user_display'],
	'POST_TITLE'		=> $news['title'],
	'S_HTML_ENABLE'		=> false,
	'S_COMMENT_HTML'	=> $addon['config']['comm_html'],
	'S_ADD_NEWS'		=> $roster->auth->getAuthorized('news_can_post'),
	'S_EDIT_NEWS'		=> $roster->auth->getAuthorized('news_can_edit_post'),
	'S_ADD_COMMENT'		=> $roster->auth->getAuthorized('news_can_post_comment'),
	'S_EDIT_COMMENT'	=> $roster->auth->getAuthorized('news_can_edit_comment'),

	'U_ADD_FORMACTION'	=> makelink('guild-main-post&id=' . $_GET['id']),
	'U_BACK'			=> makelink('guild-main'),
	'U_NEWS_ID'			=> $id,

	)
);

$query = "SELECT `news`.*, "
	. "DATE_FORMAT(  DATE_ADD(`news`.`date`, INTERVAL " . $roster->config['localtimeoffset'] . " HOUR ), '" . $roster->locale->act['timeformat'] . "' ) AS 'date_format', "
	. "COUNT(`comments`.`comment_id`) comm_count "
	. "FROM `" . $roster->db->table('news',$addon['basename']) . "` news "
	. "LEFT JOIN `" . $roster->db->table('comments',$addon['basename']) . "` comments USING (`news_id`) "
	. "Where `news_id` = '".$id."';";

$results = $roster->db->query($query);
$numn = 1;
$totaln = $roster->db->num_rows($results);

require_once (ROSTER_LIB . 'bbcode.php' );
$bbcode = new bbcode();

$row = $roster->db->fetch($results);

$message = $row['text'];
$message = $bbcode->bbcodeParser($message);
$roster->tpl->assign_block_vars('news', array(
	'POSTER'    => $row['poster'],
	'NUM'       => $numn,
	'U_EDIT'	=> makelink('guild-'. $addon['basename'] .'-edit&amp;id='. $row['news_id']),
	'TEXT'      => $message,
	'IMG'		=> (!empty($row['img']) ? $addon['image_url'].'news/'.$row['img'].'-image.jpg' : false),
	'TITLE'     => $row['title'],
	'DATE'      => $row['date_format'],
	'L_COMMENT' => ($row['comm_count'] != 1 ? sprintf($roster->locale->act['n_comments'], $row['comm_count']) : sprintf($roster->locale->act['n_comment'], $row['comm_count'])),
	'NEWS_TYPE' => $row['news_type']
));


$query = "SELECT `comments`.*, "
		. "DATE_FORMAT(  DATE_ADD(`comments`.`date`, INTERVAL " . $roster->config['localtimeoffset'] . " HOUR ), '" . $roster->locale->act['timeformat'] . "' ) AS 'date_format' "
		. "FROM `" . $roster->db->table('comments',$addon['basename']) . "` comments "
		. "WHERE `comments`.`news_id` = '" . $id . "' "
		. "ORDER BY `comments`.`date` ASC;";

$result = $roster->db->query($query);

if( $roster->db->num_rows() > 0 )
{
	$roster->tpl->assign_var('S_COMMENTS', true);

	while( $comment = $roster->db->fetch($result) )
	{
		$roster->tpl->assign_block_vars('comment_row', array(
			'CONTENT'       => $comment['content'],
			'AUTHOR'        => $comment['author'],
			'DATE'          => $comment['date_format'],
			'U_COMMENT_ID'  => $comment['comment_id'],
			'U_EDIT'     => makelink('guild-main-comment_edit&amp;id=' . $comment['comment_id']),
			)
		);
	}
}

$roster->tpl->set_filenames(array(
	'post'		=> $addon['basename'] . '/post.html',
	'comments'	=> $addon['basename'] . '/comments.html'
	)
);

$roster->tpl->display('post');
$roster->tpl->display('comments');
