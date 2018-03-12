<?php

include( $addon['dir'] . 'inc/functions.php' );
include( $addon['dir'] . 'inc/functions2.php' );
include( $addon['dir'] . 'inc/conf.php' );
//$roster->output['show_menu'] = false;
$functions = new ssconfig;
$functions2 = new ssconfig2;

if( isset($_POST['op']) && $_POST['op'] != '' )
{
	switch ( $_POST['type'] )
	{
		case 'create':
		d($_POST);
			$functions2->create_cat( $_POST );
		break;

		case 'delete';
			$functions2->delete_cat( $_POST );
		break;

		case 'lock';
			$functions2->lock_cat( $_POST );
		break;
		
		case 'unlock';
			$functions2->unlock_cat( $_POST );
		break;
		
		case 'deactivate';
			$functions2->da_cat( $_POST );
		break;
		
		case 'activate';
			$functions2->a_cat( $_POST );
		break;
		
		case 'access':
			processAccess();
		break;

		default:
		break;
	}
}

/**
	 * Make our menu from the config api
	 */
	// ----[ Set the tablename and create the config class ]----
	include(ROSTER_LIB . 'config.lib.php');
	$config = new roster_config( $roster->db->table('addon_config'), '`addon_id` = "' . $addon['addon_id'] . '"', 'config_', false );

	$config->form_builder = false;
	// ----[ Get configuration data ]---------------------------
	$config->getConfigData();

	$config->form_builder = false;
	// ----[ Build the page items using lib functions ]---------
	$menu .= $config->buildConfigMenu('rostercp-addon-' . $addon['basename']);

	
	$roster->tpl->assign_vars(array(
		'ACCESS'	=> $roster->auth->rosterAccess(array('name' => 'access', 'value' => '')),
	));


	$query = "SELECT * FROM `".$roster->db->table('catagories',$addon['basename'])."`;";
	$result = $roster->db->query($query) or die_quietly($roster->db->error(),'Database Error',basename(__FILE__),__LINE__,$query);
	

	while( $row = $roster->db->fetch( $result ) )
	{
		$roster->tpl->assign_block_vars('catagories', array(
			'ID'		=> $row['id'],
			'NAME'		=> $row['cat_name'],
			'DESC'		=> $row['cat_desc'],
			'STAT'		=> $row['cat_stat'],
			'L_ACTIVEU' => ( $row['cat_lock'] == 1 ? 'locked' : 'unlocked'),
			'L_ACTIVET'	=> ( $row['cat_lock'] == 1 ? $roster->locale->act['lock'] : $roster->locale->act['unlock']),
			'L_ACTIVEOP'=> ( $row['cat_lock'] == 1 ? 'unlock' : 'lock'),
			'B_ACTIVEI' => ( $row['cat_stat'] == 1 ? 'green' : 'red'),
			'B_ACTIVET'	=> ( $row['cat_stat'] == 1 ? 'Active' : 'Inactive'),
			'B_ACTIVEOP'=> ( $row['cat_stat'] == 1 ? 'deactivate' : 'activate'),
			'ACCESS'	=> ( isset($row['cat_access']) ? $roster->auth->rosterAccess(array('name' => 'access', 'value' => $row['cat_access'])) : false ),
		));
	}
	
$roster->tpl->set_handle('gallery_cat',$addon['basename'] . '/admin_cat.html');

$body .= $roster->tpl->fetch('gallery_cat');
	
	
	
	
	
	
function processAccess()
{
	global $addon, $roster;

	$access = implode(":",$_POST['config_access']);
	$id = (int)$_POST['id'];
	$query = "UPDATE `" . $roster->db->table('catagories',$addon['basename']) . "` SET `cat_access` = '$access' WHERE `id` = '$id';";

	if( !$roster->db->query($query) )
	{
		die_quietly($roster->db->error(),'Database Error',__FILE__,__LINE__,$query);
	}
}




?>
