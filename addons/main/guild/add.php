<?php
/**
 * WoWRoster.net WoWRoster
 *
 * @copyright  2002-2011 WoWRoster.net
 * @license    http://www.gnu.org/licenses/gpl.html   Licensed under the GNU General Public License v3. * @package    News
 */

if( !defined('IN_ROSTER') )
{
	exit('Detected invalid access to this file!');
}

if( !$roster->auth->getAuthorized('news_can_post') )
{
	echo $roster->auth->getLoginForm();
	return; //To the addon framework
}

include_once($addon['inc_dir'] . 'functions.lib.php');
$func = New mainFunctions;

$js = '
	jQuery(document).on(\'click\', \'#ilibo\', function (e) {
		var $this = $(this);
		var frame = $this.data("member");
		console.log(frame);
		$(\'#ilib\').toggle(\'fast\');
	});
	
	jQuery(document).on(\'click\', \'[data-dismiss]\', function (e) {
		var $this = $(this);
		if ($this.data(\'dismiss\') == "modal" )
		{
			var frame = $this.data("member");
			$(\'#ilib\').toggle(\'fast\');
		}
	});

	//isel
	jQuery(document).on(\'click\', \'#isel\', function (e) {
		var $this = $(this);
		var image = $this.data("image");
		var image_src = $this.data("src");
		var target = $this.data("target");
		console.log(image);
		console.log(target);
		$(\'#\'+target+\'\').val(image);
		$(\'#\'+target+\'\').attr(\'value\',image);
		$(\'#h_imagel_p\').attr({
			src: image_src,
			width: \'260px\',
			height: \'160px\',
		});
		$(\'#ilib\').toggle(\'fast\');
	});
	
	jQuery(document).on(\'click\', \'#reload\', function (e) {
		$.ajax({
			type: "GET",
			url: "'. makelink('ajax-addon-main-load') .'",
			dataType: "html",
			async: false,
			success: function(r){
				jQuery(\'#gallery\').empty();
				jQuery(\'#gallery\').prepend(r);
			},
		});

	});
	
	jQuery(document).on(\'click\', \'input[type=button]#attachment\', function() {
		wrapText("news", "[attachment]", "[/attachment]");
	});
	jQuery(document).on(\'click\', \'input[type=button]#youtube\', function() {
		wrapText("news", "[youtube]", "[/youtube]");
	});
	jQuery(document).on(\'click\', \'input[type=button]#item\', function() {
		wrapText("news", "[item]", "[/item]");
	});
	jQuery(document).on(\'click\', \'input[type=button]#code\', function() {
		wrapText("news", "<pre><code>", "</code></pre>");
	});
	
function wrapText(elementID, openTag, closeTag) {
    var textArea = $(\'#\' + elementID);
    var len = textArea.val().length;
    var start = textArea[0].selectionStart;
    var end = textArea[0].selectionEnd;
    var selectedText = textArea.val().substring(start, end);
    var replacement = openTag + selectedText + closeTag;
    textArea.val(textArea.val().substring(0, start) + replacement + textArea.val().substring(end, len));
}		
';

$directory = $addon['dir'].'images/news/thumbs/';
$scanned_directory = array_diff(scandir($directory), array('..', '.'));

roster_add_js($js, 'inline', 'header', false, false);

roster_add_js('templates/' . $roster->tpl->tpl . '/js/jquery.uploadfile.min.js');
foreach ($scanned_directory as $ii => $img)
{
	$path_parts = pathinfo($img);
	//d($path_parts);
	$f = explode('-',$path_parts['filename']);
	$roster->tpl->assign_block_vars('images', array(
		'ID'		=> $ii,
		'IMAGE'		=> $addon['image_url'].'news/thumbs/'.$img,
		'FILE'		=> $f[0]
		)
	);
}
	


// Assign template vars
$roster->tpl->assign_vars(array(
	'S_ADD_NEWS'     => false,
	'U_BACK'   => makelink('guild-main'),
	'S_HTML_ENABLE'		=> false,
	'S_NEWS_HTML'		=> $addon['config']['news_html'],
	'S_POSTER'			=> $roster->auth->user['user_display'],
	'S_POSTER_ID'		=> $roster->auth->user['id'],
	'U_FORMACTION'		=> makelink('guild-main'),
	'IMG_AJAX_LOAD'		=> makelink('ajax-addon-main-load'),
	'IMG_AJAX_DELETE'	=> makelink('ajax-addon-main-delete'),
	'IMG_AJAX_UPLOAD'	=> makelink('ajax-addon-main-upload'),
	'TYPE_SELECT'		=> $func->_build_select($roster->locale->act['newstype'], 'news_type'),
	)
);

$roster->tpl->set_filenames(array(
	'head' => $addon['basename'] . '/news_head.html',
	'body' => $addon['basename'] . '/add.html',
	'foot' => $addon['basename'] . '/news_foot.html'
	)
);
$roster->tpl->display('head');
$roster->tpl->display('body');
$roster->tpl->display('foot');
