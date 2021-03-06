<?php
/**
 * WoWRoster.net WoWRoster
 *
 * Available pages for RosterCP
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

// The key in the $config_pages array is the pagename for the admincp file.
// The value is an array whose keys have these meanings:
//	"href"		The link this should refer to.
//	"title"		The localization key for the button title.
//	"file"		The file to include if this page is called. Missing means
//			invalid page.
//	"special"	Ignored unless it's one of the following:
//			'divider'	Prints a horizontal line and no button.
//			'hidden'	Hides the link, but allows access to the page

$config_pages['roster'] = array(
	'href'=>	$roster->pages[0],
	'title'=>	'pagebar_rosterconf',
	'file'=>	'roster_conf.php',
	);
$config_pages['theme'] = array(
	'href'=>	$roster->pages[0].'-theme',
	'title'=>	'pagebar_rostertheme',
	'file'=>	'theme_settings.php',
	);
	
$config_pages['addon'] = array(
	'special'=>	'hidden',
	'file'=>	'addon_conf.php',
	);
$config_pages['plugin'] = array(
	'special'=>	'hidden',
	'file'=>	'plugin_conf.php',
	);
$config_pages['user'] = array(
	'special'	=> 'group',
	'title'		=> 'USER_GROUP',
	'icon'		=> 'glyphicon-user',
	'pages' 	=> array (
		'userman' => array(
			'href'=>	'userman',
			'title'=>	'pagebar_userman',
			'file'=>	'user_manager.php',
		),
		'usergroups' => array(
			'href'=>	'usergroups',
			'title'=>	'pagebar_usergroups',
			'file'=>	'user_groups.php',
		),
		'permissions' => array(
			'href'=>	'permissions',
			'title'=>	'pagebar_permissions',
			'file'=>	'permissions.php',
		),
		'rosterranks' => array(
			'href'=>	'rosterranks',
			'title'=>	'pagebar_rosterranks',
			'file'=>	'rosterranks.php',
		),
	),
);	
	
$config_pages['upload'] = array(
	'href'=>	$roster->pages[0].'-upload',
	'title'=>	'pagebar_uploadrules',
	'file'=>	'upload_rules.php',
	);
$config_pages['media'] = array(
	'href'=>	$roster->pages[0].'-media',
	'title'=>	'pagebar_media',
	'file'=>	'media.php',
	);
$config_pages['dataman'] = array(
	'href'=>	$roster->pages[0].'-dataman',
	'title'=>	'pagebar_dataman',
	'file'=>	'data_manager.php',
	);
$config_pages['armory_data'] = array(
	'href'=>	$roster->pages[0].'-armory_data',
	'title'=>	'pagebar_armory_data',
	'file'=>	'armory_data.php',
	);
$config_pages['apidata'] = array(
	'href'=>	$roster->pages[0].'-apidata',
	'title'=>	'pagebar_api_data',
	'file'=>	'api_data.php',
	);
$config_pages['install'] = array(
	'href'=>	$roster->pages[0].'-install',
	'title'=>	'pagebar_addoninst',
	'file'=>	'addon_install.php',
	);
$config_pages['pinstall'] = array(
	'href'=>	$roster->pages[0].'-pinstall',
	'title'=>	'pagebar_plugin',
	'file'=>	'plugin_install.php',
	);
$config_pages['menu'] = array(
	'href'=>	$roster->pages[0].'-menu',
	'title'=>	'pagebar_menuconf',
	'file'=>	'menu_conf.php',
	);
/*
if( $roster->config['external_auth'] == 'roster' )
{
	$config_pages['change_pass'] = array(
		'href'=>	$roster->pages[0].'-change_pass',
		'title'=>	'pagebar_changepass',
		'file'=>	'change_pass.php',
		);
}
*/


$config_pages['userman'] = array(
	'special'=>	'hidden',
	'href'=>	$roster->pages[0].'-userman',
	'title'=>	'pagebar_userman',
	'file'=>	'user_manager.php',
	);
$config_pages['userchars'] = array(
	'special'=>	'hidden',
	'file'=>	'user_chars.php',
	);
$config_pages['usergroups'] = array(
	'special'=>	'hidden',
	'href'=>	$roster->pages[0].'-usergroups',
	'title'=>	'pagebar_usergroups',
	'file'=>	'user_groups.php',
	);
$config_pages['permissions'] = array(
	'special'=>	'hidden',
	'href'=>	$roster->pages[0].'-permissions',
	'title'=>	'pagebar_permissions',
	'file'=>	'permissions.php',
	);
$config_pages['rosterranks'] = array(
	'href'=>	$roster->pages[0].'-rosterranks',
	'title'=>	'pagebar_rosterranks',
	'file'=>	'rosterranks.php',
	);
$config_pages['config_reset'] = array(
	'href'=>	$roster->pages[0].'-config_reset',
	'title'=>	'pagebar_configreset',
	'file'=>	'config_reset.php',
	);
$config_pages['rosterdiag2'] = array(
	'href'=>	'rosterdiag2',
	'title'=>	'pagebar_rosterdiag',
	);
$config_pages['guide'] = array(
	'special'=>	'hidden',
	'title'=>   'setup_guide',
	'file'=>	'install_guide.php',
	);
$config_pages['cache'] = array(
	'href'=>	$roster->pages[0].'-cache',
	'title'=>	'cache',
	'file'=>	'roster_cache.php',
	);
