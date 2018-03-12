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
 * forum Addon Installer
 * @package Forum
 */
class wow_eventsInstall
{
	var $active = true;
	var $icon = 'inv_helm_cloth_holiday_christmas_a_03';

	var $version = '0.0.1';
	var $wrnet_id = '0';

	var $fullname = 'WoW Events';
	var $description = 'Track events in wow or for your guild!';
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

		// Master and menu entries
		$installer->add_config("'12100','startpage','wowevent_conf','display','master'");
		$installer->add_config("'12101','wowevent_conf',NULL,'blockframe','menu'");	// forum
		$installer->add_config("'12102','wowevent_add','rostercp-addon-wow_events-add','makelink','menu'");
		$installer->add_config("'12103','wowevent_lst','rostercp-addon-wow_events-list','makelink','menu'");
		
		//wowevent_conf
		$installer->add_config("'12110','enable_menue','1','radio{enabled^1|disabled^0','wowevent_conf'");
		$installer->add_config("'12111','enable_block','1','radio{enabled^1|disabled^0', 'wowevent_conf'");

		
		$installer->create_table($installer->table('events'),"
			`event_id` int(10) NOT NULL AUTO_INCREMENT,
			`title` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
			`desc` varchar(200) CHARACTER SET latin1 DEFAULT NULL,
			`misc` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
			`start_date` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
			`end_date` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
			`active` int(2) DEFAULT NULL,
			`banner` int(1) NOT NULL DEFAULT '0',
			`icon` varchar(255) DEFAULT NULL,
			PRIMARY KEY (`event_id`)");

		/*
		$permissions = array(
			array('catagory'=> 'forum_forum','name'=> 'forum_lock','info'=> 'forum_lock_desc','cfg_name'=> 'forum_lock'),
			array('catagory'=> 'forum_topics', 'name'=> 'forum_topics_start', 'info'=> 'forum_topics_start_desc','cfg_name'=> 'topic_start'),
		);
		$installer->add_permissions($permissions);
		*/
		
		//$installer->add_menu_button('forum_button','guild');
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

		$installer->drop_table($installer->table('events'));
		$installer->remove_all_config();
		//$installer->remove_all_menu_button();
		//$installer->remove_all_permissions();
		
		return true;
	}
}
