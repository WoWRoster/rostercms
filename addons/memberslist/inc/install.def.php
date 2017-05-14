<?php
/**
 * WoWRoster.net WoWRoster
 *
 *
 * @copyright  2002-2011 WoWRoster.net
 * @license    http://www.gnu.org/licenses/gpl.html   Licensed under the GNU General Public License v3. * @package    MembersList
 * @subpackage Installer
 */

if ( !defined('IN_ROSTER') )
{
	exit('Detected invalid access to this file!');
}

/**
 * Installer for MembersList Addon
 * @package    MembersList
 * @subpackage Installer
 */
class memberslistInstall
{
	var $active = true;
	var $icon = 'inv_letter_06';

	var $version = '2.9.9.0000';
	var $wrnet_id = '0';

	var $fullname = 'memberslist';
	var $description = 'memberslist_desc';
	var $credits = array(
		array(	"name"=>	"PleegWat",
				"info"=>	"Original Author"),
		array(	"name"=>	"WoWRoster Dev Team",
				"info"=>	"Contributor")
	);


	/**
	 * Install Function
	 *
	 * @return bool
	 */
	function install()
	{
		global $installer;

		# Master data for the config file
		$installer->add_config("1,'startpage','display','display','master'");

		# Config menu entries
		$installer->add_config("110,'display',NULL,'blockframe','menu'");
		$installer->add_config("120,'members',NULL,'blockframe','menu'");
		$installer->add_config("130,'stats',NULL,'blockframe','menu'");
		$installer->add_config("150,'log',NULL,'blockframe','menu'");
		//$installer->add_config("160,'build',NULL,'blockframe','menu'");
		$installer->add_config("170,'ml_wiki','http://www.wowroster.net/MediaWiki/MembersList','newlink','menu'");

		# Generic display settings
		$installer->add_config("1000,'openfilter','0','radio{Show^1|Hide^0','display'");
		$installer->add_config("1010,'nojs','0','radio{Server^1|Client^0','display'");
		$installer->add_config("1020,'def_sort','','select{Default Sort^|Name^name|Class^class|Level^level|Guild Title^guild_title|Highest Rank^lifetimeHighestRank','display'");
		$installer->add_config("1030,'member_tooltip','1','radio{On^1|Off^0','display'");
		$installer->add_config("1040,'group_alts','1','radio{Open^2|Closed^1|Ungrouped^0','display'");
		$installer->add_config("1060,'class_icon','2','radio{Full^2|On^1|Off^0','display'");
		$installer->add_config("1070,'class_text','0','radio{Color^2|On^1|Off^0','display'");
		$installer->add_config("1080,'talent_text','0','radio{On^1|Off^0','display'");
		$installer->add_config("1090,'level_bar','1','radio{On^1|Off^0','display'");
		$installer->add_config("1120,'page_size','0','text{4|30','display'");

		# Per page settings: Memberlist
		$installer->add_config("2010,'member_motd','1','radio{Off^0|On^1','members'");
		$installer->add_config("2020,'member_hslist','1','radio{Off^0|On^1','members'");
		$installer->add_config("2030,'member_pvplist','1','radio{Off^0|On^1','members'");
		$installer->add_config("2040,'member_class','2','radio{Force Hidden^0|Default Hidden^1|Default Shown^2|Force Shown^3','members'");
		$installer->add_config("2050,'member_level','2','radio{Force Hidden^0|Default Hidden^1|Default Shown^2|Force Shown^3','members'");
		$installer->add_config("2060,'member_gtitle','2','radio{Force Hidden^0|Default Hidden^1|Default Shown^2|Force Shown^3','members'");
		$installer->add_config("2070,'member_hrank','0','radio{Force Hidden^0|Default Hidden^1|Default Shown^2|Force Shown^3','members'");
		$installer->add_config("2080,'member_prof','2','radio{Force Hidden^0|Default Hidden^1|Default Shown^2|Force Shown^3','members'");

		# Per page settings: Statslist
		$installer->add_config("3010,'stats_motd','0','radio{Off^0|On^1','stats'");
		$installer->add_config("3020,'stats_hslist','0','radio{Off^0|On^1','stats'");
		$installer->add_config("3030,'stats_pvplist','0','radio{Off^0|On^1','stats'");
		$installer->add_config("3040,'stats_class','2','radio{Force Hidden^0|Default Hidden^1|Default Shown^2|Force Shown^3','stats'");
		$installer->add_config("3050,'stats_level','2','radio{Force Hidden^0|Default Hidden^1|Default Shown^2|Force Shown^3','stats'");
		$installer->add_config("3060,'stats_str','2','radio{Force Hidden^0|Default Hidden^1|Default Shown^2|Force Shown^3','stats'");
		$installer->add_config("3070,'stats_agi','2','radio{Force Hidden^0|Default Hidden^1|Default Shown^2|Force Shown^3','stats'");
		$installer->add_config("3080,'stats_sta','2','radio{Force Hidden^0|Default Hidden^1|Default Shown^2|Force Shown^3','stats'");
		$installer->add_config("3090,'stats_int','2','radio{Force Hidden^0|Default Hidden^1|Default Shown^2|Force Shown^3','stats'");
		$installer->add_config("3100,'stats_spi','2','radio{Force Hidden^0|Default Hidden^1|Default Shown^2|Force Shown^3','stats'");
		$installer->add_config("3110,'stats_sum','2','radio{Force Hidden^0|Default Hidden^1|Default Shown^2|Force Shown^3','stats'");
		$installer->add_config("3120,'stats_health','2','radio{Force Hidden^0|Default Hidden^1|Default Shown^2|Force Shown^3','stats'");
		$installer->add_config("3130,'stats_mana','2','radio{Force Hidden^0|Default Hidden^1|Default Shown^2|Force Shown^3','stats'");
		$installer->add_config("3140,'stats_armor','2','radio{Force Hidden^0|Default Hidden^1|Default Shown^2|Force Shown^3','stats'");
		$installer->add_config("3150,'stats_dodge','2','radio{Force Hidden^0|Default Hidden^1|Default Shown^2|Force Shown^3','stats'");
		$installer->add_config("3160,'stats_parry','2','radio{Force Hidden^0|Default Hidden^1|Default Shown^2|Force Shown^3','stats'");
		$installer->add_config("3170,'stats_block','2','radio{Force Hidden^0|Default Hidden^1|Default Shown^2|Force Shown^3','stats'");
		$installer->add_config("3180,'stats_crit','2','radio{Force Hidden^0|Default Hidden^1|Default Shown^2|Force Shown^3','stats'");


		# Per page settings: Member Log
		$installer->add_config("5010,'log_motd','0','radio{Off^0|On^1','log'");
		$installer->add_config("5020,'log_hslist','0','radio{Off^0|On^1','log'");
		$installer->add_config("5030,'log_pvplist','0','radio{Off^0|On^1','log'");
		$installer->add_config("5040,'log_class','2','radio{Force Hidden^0|Default Hidden^1|Default Shown^2|Force Shown^3','log'");
		$installer->add_config("5050,'log_level','2','radio{Force Hidden^0|Default Hidden^1|Default Shown^2|Force Shown^3','log'");
		$installer->add_config("5060,'log_gtitle','2','radio{Force Hidden^0|Default Hidden^1|Default Shown^2|Force Shown^3','log'");
		$installer->add_config("5070,'log_type','2','radio{Force Hidden^0|Default Hidden^1|Default Shown^2|Force Shown^3','log'");

		$installer->create_table($installer->table('alts'),"
			`member_id` int(11)    unsigned NOT NULL default '0',
			`main_id`   int(11)    unsigned NOT NULL default '0',
			`alt_type`  tinyint(3) unsigned NOT NULL default '0',
			PRIMARY KEY (`member_id`)");

		// Filled from the normal config table by admin/guild.php
		$installer->create_table($installer->table('config_guild'),"
			`guild_id` int(11) NOT NULL default '0',
			`id` int(11) unsigned NOT NULL,
			`config_name` varchar(255) default NULL,
			`config_value` tinytext,
			`form_type` mediumtext,
			`config_type` varchar(255) default NULL,
			PRIMARY KEY  (`guild_id`,`id`)");

		$installer->add_query("INSERT INTO `" . $installer->table('config_guild') . "` VALUES
			(0, 5590, 'use_global', '1', 'radio{on^1|off^0', 'build');");

		# Roster menu entry
		$installer->add_menu_button('memberslist_Members','guild','','spell_holy_prayerofspirit');
		$installer->add_menu_button('memberslist_Stats','guild','statslist','inv_misc_book_09');
		$installer->add_menu_button('memberslist_Skills','guild','skilllist','inv_scroll_12',false);

		return true;
	}

	/**
	 * Upgrade Function
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
	 * Un-Install Function
	 *
	 * @return bool
	 */
	function uninstall()
	{
		global $installer;

		$installer->remove_all_config();

		$installer->drop_table($installer->table('alts'));
		$installer->drop_table($installer->table('config_guild'));
		$installer->remove_all_menu_button();
		$installer->remove_all_permissions();

		return true;
	}
}
