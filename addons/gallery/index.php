<?php
/******************************
 * $Id: index.php,v 1.7.0 2006/06/14 09:16:24 Ulminia Exp $
 ******************************/
include( $addon['dir'] . 'inc/functions.php' );
include( $addon['dir'] . 'inc/functions2.php' );
include( $addon['dir'] . 'inc/conf.php' );
//$roster->output['show_menu'] = false;
$functions = new ssconfig;
$functions2 = new ssconfig2;
define('ROSTER_SCREENTABLE',$roster->db->table('ss',$addon['basename']));
$basename = basename(dirname(dirname(__FILE__)));
if ( !defined('ROSTER_INSTALLED') )
{
    exit('Detected invalid access to this file!');
}
$imgdirx = $addon['url_path'].'screenshots/';

$imagex = '';//$roster->config['website_address'];
$imgdir = $imagex.$addon['url'].'screenshots/';
$imgdirthumb = $imagex.$addon['url'].'screenshots/thumbs/';
//<body onload="MM_preloadImages(‘/images/menu_on.gif’)…;initLightbox()">
$roster->output['body_onload'] = 'onload="MM_preloadImages(‘'.$addon['dir'].'images/menu_on.gif’)…;initLightbox()"';
$roster->output['html_head'] = '

<script type="text/javascript" src="' . ROSTER_PATH . 'addons/gallery/js/lightbox.js"></script>

<link rel="stylesheet" href="' . ROSTER_PATH . 'addons/gallery/lightbox.css" type="text/css" media="screen" />
<script language="javascript" type="text/javascript">
<!--
function popitup(url) {
	newwindow=window.open(url,\'name\',\'height='.($addon['config']['rg_height']+150*1.5).',width='.($addon['config']['rg_width']*1.5+70).'\');
	if (window.focus) {newwindow.focus()}
	return false;
}

// -->
</script>

';


echo '

<!-- Begin Java Link -->
<script type="text/javascript" language="JavaScript">
<!--
	var id=new tabcontent(\'rg_menu2\');
      id.init();
//-->
</script>
<!-- Begin Java Link -->
<script type="text/javascript" language="JavaScript">
<!--
	var id=new tabcontent(\'rg_menu3\');
      id.init();
//-->
</script>
<!-- Begin Java Link -->
<script type="text/javascript" language="JavaScript">
<!--
	var id=new tabcontent(\'rg_menu4\');
      id.init();
//-->
</script>

<!-- Begin Java Link -->
<script type="text/javascript" language="JavaScript">
<!--
	var id=new tabcontent(\tab_menu3\');
      id.init();
//-->
</script>
';
// ----[ Decide what to do next ]---------------------------
if( isset($_POST['ss_op']) && $_POST['ss_op'] != '' )
{
	switch ( $_POST['ss_op'] )
	{
		case 'upload_image';
			$functions2->upload_image($_POST['desc'], $_POST['caption'], $_POST['catagories'], '');
		break;
	}
}

	$c=null;
	if (isset($_GET['c']) != '')
	{
		$c = $functions->getvars( 'c' );
	}
	if ($c!=null)
	{
		  $query = "SELECT * FROM `".ROSTER_SCREENTABLE."` WHERE `approve` = 'yes' AND `catagory` = '".$c."' ORDER BY `id` DESC";
	}
	else
	{
		  $query = "SELECT * FROM `".ROSTER_SCREENTABLE."` WHERE `approve` = 'yes' ORDER BY `id` DESC";
	}

	$result = $roster->db->query($query) or die_quietly($roster->db->error(),'Database Error',basename(__FILE__),__LINE__,$query);
	$total = $roster->db->num_rows($result);

	$roster->tpl->assign_vars(array(
			'TOTAL'			=> $total,
			'CATS'			=> $functions2->build_cat_dropdown(),
			'CAN_POST'		=> $roster->auth->getAuthorized( 'gallery_canpost' ),
		));

	$cats = $functions2->get_cat_full();
	foreach($cats as $id => $cat)
	{
		if( $roster->auth->getAuthorized( $cat['cat_access'] ) )
		{
			$roster->tpl->assign_block_vars('cats', array(
				'NAME'		=> $cat['cat_name'],
				'ID'		=> $id,
				'URL'		=> makelink('&amp;c='.$id.''),
			));
		}
	}
	$cat_listings = array();
	$ipp = 12;
	$ipc = 1;

	$cf = $functions2->get_cat_full();

	while( $row = $roster->db->fetch( $result ) )
	{
		if( $roster->auth->getAuthorized( $cf[$row['catagory']]['cat_access'] ) )
		{
			$roster->tpl->assign_block_vars('images', array(
				'FILE'		=> $row['file'],
				'CAPTION'	=> $row['caption'],
				'DISC'		=> $row['disc'],
				'EXT'		=> $row['ext'],
				'CATAGORY'	=> $row['catagory'],
				'APPROVED'	=> $row['approve'],
				'VOTES'		=> $row['votes'],
				'RATING'	=> $row['rateing'],
				'ID'		=> $row['id'],
				'LOC'		=> $imgdirthumb,
				'FPATH'		=> $imgdir,
			));
		}
		
		$cat_listings[$row['catagory']][$row['id']] = $row;
	}
	
	foreach ($cat_listings as $id => $ca)
	{
		if( $roster->auth->getAuthorized( $cf[$id]['cat_access'] ) )
		{
			$roster->tpl->assign_block_vars('cp', array(
				'ID'		=> $id,
			));
			$ic = count($ca);
			foreach ($ca as $i => $img)
			{
				$roster->tpl->assign_block_vars('cp.img', array(
					'FILE'		=> $img['file'],
					'CAPTION'	=> $img['caption'],
					'DISC'		=> $img['disc'],
					'EXT'		=> $img['ext'],
					'CATAGORY'	=> $img['catagory'],
					'APPROVED'	=> $img['approve'],
					'VOTES'		=> $img['votes'],
					'RATING'	=> $img['rateing'],
					'ID'		=> $img['id'],
					'LOC'		=> $imgdirthumb,
				));
			}
		}
	}

	$roster->tpl->set_handle('gallery', $addon['basename'] . '/gallery.html');
	$roster->tpl->display('gallery');
//echo '<pre>';print_r($cat_listings);echo '</pre>';
?>