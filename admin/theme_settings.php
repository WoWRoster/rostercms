<?php
/**
 * WoWRoster.net WoWRoster
 *
 * Main Roster configuration
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

$roster->output['title'] .= $roster->locale->act['pagebar_themeconf'];

// ----[ Set the tablename and create the config class ]----
include(ROSTER_LIB . 'config.lib.php');
$config = new roster_config( $roster->db->table('config') );

// ----[ Include special functions file ]-------------------
include(ROSTER_ADMIN . 'roster_config_functions.php');

// ----[ Get configuration data ]---------------------------
$config->getConfigData();

// ----[ Process data if available ]------------------------
$config->processData($roster->config);

$page = $config->buildBlock('theme_conf');

$body .= $config->formpages
		. $page
	   . $config->form_end
	   . $config->nonformpages;
//d($roster);