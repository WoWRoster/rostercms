<?php

//$addon = getaddon('forum');
 
include( $addon['dir'] . 'inc/function.lib.php' );
$functions = new forum;

//roster_add_css(ROSTER_BASE . 'pages/forum/style.css','module');

if( isset( $_POST['type'] ) && !empty($_POST['type']) )
{
//	echo '<pre>';print_r($_POST);echo '</pre>';
$op = ( isset($_POST['op']) ? $_POST['op'] : '' );
$id = ( isset($_POST['tid']) ? $_POST['tid'] : $_GET['tid'] );

	switch( $_POST['type'] )
	{
		case 'newTopic':
			postReply();
		break;

		case 'locked':
			$functions->processLock($id,$_POST['value']);
			break;

		case 'sticky':
			$functions->processSticky($id,$_POST['value']);
			break;

		case 'anounc':
			$functions->processAnounc($id,$_POST['value']);
			break;
			
		default:
			break;
	}
}

//require_once (ROSTER_LIB . 'bbcode.php' );
//$bbcode = new bbcode();

$info = $functions->getInfo('topic',$_GET['tid']);
//echo '<pre>';print_r($roster->auth->getAuthorized( 'post_start' ));echo '</pre>';
$forums = $functions->getPosts($_GET['tid']);
$x = $functions->getCrumbsb($_GET['tid']);
$roster->tpl->assign_vars(array(
			'CRUMB'				=> $x,
			'M_REPLYPOST'		=> makelink('guild-'.$addon['basename'].'-topic_reply&amp;tid=' . $_GET['tid']),
			'M_POSTDELETE'		=> makelink('guild-'.$addon['basename'].'-topic&amp;tid=' . $_GET['tid']),
			'LOCKED'			=> ($info['locked'] == 1 ? true : false),
			'IMAGE'    			=> '<div class="icon"><img src="'.$addon['url_path'] .'images/topic_unread_locked.gif"></a></div>',
			'CANLOCK'			=> $roster->auth->getAuthorized( 'topic_lock' ),
			'CANPOST'			=> $roster->auth->getAuthorized( 'post_start' ),
			'CANSTICKY'			=> $roster->auth->getAuthorized( 'topic_sticky' ),
			'CANANOUNC'			=> $roster->auth->getAuthorized( 'topic_anounc' ),
			'CANDELA'			=> $roster->auth->getAuthorized( 'post_deleteany' ),
			'CANDELO'			=> $roster->auth->getAuthorized( 'post_deleteown' ),
			'PAB_DEL'			=> $roster->locale->act['delete'],
			'PAB_REP'			=> $roster->locale->act['report'],
			'L_ACTIVE'			=> ( $info['locked'] == 1 ? $roster->locale->act['lock'] : $roster->locale->act['unlock']),
			'L_STICKY' 			=> ( $info['sticky'] == 1 ? $roster->locale->act['t_u_sticky'] : $roster->locale->act['t_sticky']),
			'L_ANOUNC'			=> ( $info['anounc'] == 1 ? $roster->locale->act['t_u_announ'] : $roster->locale->act['t_announ']),
			'L_ACTIVEP' 		=> 'locked',
			'L_STICKYP' 		=> 'sticky',
			'L_ANOUNCP'			=> 'anounc',
			'L_ACTIVEOP'		=> ( $info['locked'] == 1 ? 0 : 1),
			'L_STICKYOP' 		=> ( $info['sticky'] == 1 ? 0 : 1),
			'L_ANOUNCOP'		=> ( $info['anounc'] == 1 ? 0 : 1),
			'TOPIC_ID'			=> $info['topic_id'],
			'TOPIC_TITLE'		=> $info['title'],
			
		));
	foreach($forums as $id =>$forum)
	{
		$f=null;
		$u=null;
		$message ='';
			
		$u = $roster->auth->GetUserInfo($forum['user_id']);

		$bg ='';
		if ($roster->auth->_ingroup( '1', $u['access'] ))
		{
			$bg = ' xadmin';
		}
		else if ($roster->auth->_ingroup( '2', $u['access'] ))
		{
			$bg = ' xmod';
		}
		$message = $forum['post_text'];
		//$message = $bbcode->bbcodeParser($message);
		//$message = bbcode_nl2br($message);
		/*
		postmod
		<li class="edit-icon"><a href="" title="Edit post"><span>Edit post</span></a></li>
						<li class="delete-icon"><a href="" title="Delete post"><span>Delete post</span></a></li>
						<li class="report-icon"><a href="" title="Report this post"><span>Report this post</span></a></li>
						<li class="info-icon"><a href="" title="Information"><span>Information</span></a></li>
						<li class="quote-icon"><a href="" title="Reply with quote"><span>Reply with quote</span></a></li>
						
		*/
		$roster->tpl->assign_block_vars('forums', array(

			'POST_SUBJECT'		=> $forum['post_subject'],
			'POST_TIME'			=> date("F j, Y, g:i a", $forum['post_time']),//$forum['post_time'],
			'POST_USERNAME'		=> $forum['post_username'],
			//'POST_AVATAR'		=> $d,// poster_id
			//'POST_SIG'			=> $u['signature'],// poster_id
			//'POST_ISAV'			=> $f,
			'POST_MOD'			=> '',
			'POST_BG'			=> $roster->switch_row_class(),
			'POST_ADMIN'		=> $bg,
			'POST_TEXT'			=> $message,
			'POST_ID'			=> $forum['post_id'],
			'TOPIC_ID'			=> $forum['topic_id'],
			'FORUM_ID'			=> $forum['forum_id']
			));
			$f_id = $forum['forum_id'];
	}		
	
	$functions->trackTopics('', $f_id, $_GET['tid'], $post_time = 0, $user_id = 0);		
	
	$roster->tpl->set_filenames(array(
		'posts_main' => $addon['basename'] . '/forum_posts.html',
		));

	$roster->tpl->display('posts_main');
	
	if(!$roster->auth->allow_login)
	{
		$roster->auth->message = 'Sorry you must be logged in to post a reply';
		echo $roster->auth->getLoginForm();
		$roster->auth->message ='';
	}
	else
	{
		if ($roster->auth->getAuthorized( 'post_start' ) && $info['locked'] != 1 OR $roster->auth->getAuthorized( 'posts_postin_lock' ))
		{
			$roster->tpl->assign_vars(array(
				'S_ADD_TOPIC'		=> false,
				'S_HTML_ENABLE'		=> false,
				'S_TOPIC_HTML'		=> $addon['config']['forum_html_posts'],
				'S_POSTER'			=> $roster->auth->user['usr'],
				'S_POSTER_ID'		=> $roster->auth->user['id'],
				'U_ADD_FORMACTION'	=> '',
				'U_FORM_NAME'		=>'add_comment',
				'U_TEXT_NAME'		=>'text',
				'U_TYPE_ID'			=> $id,
				)
			);

			$roster->tpl->set_filenames(array('comments_add' => $addon['basename'] . '/forum_reply.html'));
			$roster->tpl->display('comments_add');
		}
	}
	
	
	

	function postReply()
	{
		global $roster, $addon;

		if( isset($_POST['html']) && $_POST['html'] == 1 && $roster->config['forum_html_posts'] >= 0 )
		{
			$html = 1;
		}
		else
		{
			$html = 0;
		}
		$querya = "SELECT * FROM `" . $roster->db->table('posts',$addon['basename']) . "` WHERE `topic_id` = '".$_GET['tid']."';";
		$resulta = $roster->db->query($querya);
		$rowa = $roster->db->fetch($resulta);
		$q = "INSERT INTO `" . $roster->db->table('posts',$addon['basename']) . "` 
		(`topic_id`, `forum_id`, `user_id`, `post_time`, `user`, `enable_html`, `post_edit_time`, `post_edit_count`, `post_subject`, `post_text`)
		VALUES
		('".$rowa['topic_id']."', '".$rowa['forum_id']."', '".$_POST['author_id']."', '".time()."', '".$_POST['author']."', '".$html."', 0, 0, 'RE:".$rowa['post_subject']."', '".$_POST['comment']."');";
		$r = $roster->db->query($q);
		if ($r)
		{
			$query = "UPDATE `" . $roster->db->table('topics',$addon['basename']) . "` SET `last_user` = '".$_POST['author']."',`date_update`='".time()."' WHERE `topic_id` = '".$rowa['topic_id']."';";
			$result = $roster->db->query($query);
		}

	
	}