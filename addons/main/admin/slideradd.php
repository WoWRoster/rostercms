<?php

if ( !defined('IN_ROSTER') )
{
	exit('Detected invalid access to this file!');
}

/**
 * Make our menu from the config api
 */
// ----[ Set the tablename and create the config class ]----
include(ROSTER_LIB . 'config.lib.php');
$config = new roster_config( $roster->db->table('addon_config'), '`addon_id` = "' . $addon['addon_id'] . '"','config_',false );
// ----[ Get configuration data ]---------------------------
$config->getConfigData();

// ----[ Build the page items using lib functions ]---------
$menu .= $config->buildConfigMenu('rostercp-addon-' . $addon['basename']);


include_once($addon['inc_dir'] . 'functions.lib.php');
$func = New mainFunctions;


if (isset($_POST['op']) && $_POST['op'] == 'upload')
{
	$func->upload_slide($_FILES);
	/*
	$ext = substr($_FILES['b_image']['name'], strrpos($_FILES['b_image']['name'], '.')+1);
	$filename = hash_file('md5', $_FILES['b_image']['tmp_name']).'.'.$ext;
	$target_path = $addon['dir'] .'images'. DIR_SEP . $filename;
	$path = $addon['dir'] .'images'. DIR_SEP . 'slider'. DIR_SEP;
	$slider = $path .'slider-'. $filename;
	$thumb = $path .'thumb-'. $filename;

	if(move_uploaded_file($_FILES['b_image']['tmp_name'], $target_path))
	{
		
		$image = new ImageResize($target_file);
		$image->resizeToHeight(420);//resize(275, 200);
		$image->crop(850, 420);
		$image->save($slider);
		
		$image1 = new ImageResize($target_file);
		$image1->resizeToHeight(47);//resize(275, 200);
		$image1->crop(100, 47);
		$image1->save($thumb);

		$query = "INSERT INTO `" . $roster->db->table('slider', $addon['basename']) . "` SET "
			. "`b_title` = '" . $_POST['b_title'] . "', "
			. "`b_desc` = '" . $_POST['b_desc'] . "', "
			. "`b_url` = '" . $_POST['b_url'] . "', "
			. "`b_video` = '" . $_POST['b_video'] . "', "
			. "`b_image` = '" . $filename . "';";

		if( $roster->db->query($query) )
		{
			$roster->set_message(sprintf($roster->locale->act['slider_add_success'], $_FILES['b_image']['name']));
		}
		else
		{
			unlink($target_path);
			unlink($slider);
			unlink($thumb);
			$roster->set_message($roster->locale->act['slider_error_db'], '', 'error');
			$roster->set_message('<pre>' . $roster->db->error() . '</pre>', 'MySQL Said', 'error');
		}
	}
	else
	{
		$roster->set_message(sprintf($roster->locale->act['slider_file_error'], $target_path), $roster->locale->act['b_add'], 'error');
	}
	*/
}

$roster->tpl->set_handle('slider',$addon['basename'] . '/admin/slideradd.html');

$body .= $roster->tpl->fetch('slider');


