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
	
$roster->tpl->set_handle('event_add', $addon['basename'] . '/admin_add.html');

$body .= $roster->tpl->fetch('event_add');