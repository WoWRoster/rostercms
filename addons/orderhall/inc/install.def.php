<?php
/**
 * WoWRoster.net WoWRoster
 *
 * @copyright  2002-2011 WoWRoster.net
 * @license    http://www.gnu.org/licenses/gpl.html   Licensed under the GNU General Public License v3. * @package    News
 * @subpackage Forum
*/

if ( !defined('IN_ROSTER') )
{
	exit('Detected invalid access to this file!');
}

/**
 * Contest Addon Installer
 * @package Contest
 */
class orderhallInstall
{
	var $active = true;
	var $icon = 'achievement_dungeon_classicraider';

	var $version = '0.0.1';
	var $wrnet_id = '0';

	var $fullname = 'Order Hall Tracker';
	var $description = 'A contest system for guild websites';
	var $credits = array(
		array(	"name"=>	"Ulminia",
				"info"=>	"Original author")
	);


	/**
	 * Install function
	 *
	 * @return bool
	 */
	function install()
	{
		global $installer;

		// no config data needed

		/*
			contests table 
			a listing of all contest past and present but need pages
		*/
		$installer->add_config("'2200','startpage','contest_admin','display','master'");
		
		$permissions = array(
			array('catagory'=> 'orderhall','name'=> 'can_view','info'=> 'can_view_desc','cfg_name'=> 'can_view'),
		);
		$installer->add_permissions($permissions);
		
		$installer->add_menu_button('orderhall_button','guild');
		return true;
	}

	/**
	 * Upgrade functoin
	 *
	 * @param string $oldversion
	 * @return bool
	 */
	function upgrade($oldversion)
	{
		global $installer;

		
		return true;
	}

	/**
	 * Un-Install function
	 *
	 * @return bool
	 */
	function uninstall()
	{
		global $installer;

		$installer->remove_all_config();
		$installer->remove_all_menu_button();
		$installer->remove_all_permissions();
		
		return true;
	}
}
