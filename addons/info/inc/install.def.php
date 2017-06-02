<?php
/**
 * WoWRoster.net WoWRoster
 *
 *
 * @copyright  2002-2011 WoWRoster.net
 * @license    http://www.gnu.org/licenses/gpl.html   Licensed under the GNU General Public License v3. * @package    CharacterInfo
 * @subpackage Installer
*/

if ( !defined('IN_ROSTER') )
{
	exit('Detected invalid access to this file!');
}

/**
 * Installer for Character Info Addon
 * @package    CharacterInfo
 * @subpackage Installer
 */
class infoInstall
{
	var $active = true;
	var $icon = 'spell_holy_divinespirit';

	var $version = '2.9.9.0000';
	var $wrnet_id = '0';

	var $fullname = 'char_info';
	var $description = 'char_info_desc';
	var $credits = array(
		array(	"name"=>	"WoWRoster Dev Team",
				"info"=>	"Original Author")
	);


	/**
	 * Install Function
	 *
	 * @return bool
	 */
	function install()
	{
		global $installer, $roster;

		// Master and menu entries
		$installer->add_config("'1','startpage','char_conf','display','master'");
		$installer->add_config("'110','char_conf',NULL,'blockframe','menu'");
		$installer->add_config("'120','char_pref','rostercp-addon-info-display','makelink','menu'");

		$installer->add_config("'1000', 'recipe_disp', '0', 'radio{show^1|collapse^0', 'char_conf'");
		$installer->add_config("'1010', 'mail_disp', '1', 'radio{Table^0|Bag^1|Both^2', 'char_conf'");
		$installer->add_config("'1020', 'api_image', '0', 'radio{No^0|Yes^1', 'char_conf'");
		$installer->add_config("'1030', 'show_pets', '0', 'radio{No^0|Yes^1', 'char_conf'");
		$installer->add_config("'1040', 'show_reputation', '0', 'radio{No^0|Yes^1', 'char_conf'");
		$installer->add_config("'1050', 'show_skills', '0', 'radio{No^0|Yes^1', 'char_conf'");
		$installer->add_config("'1060', 'show_talents', '0', 'radio{No^0|Yes^1', 'char_conf'");
		$installer->add_config("'1070', 'show_glyphs', '0', 'radio{No^0|Yes^1', 'char_conf'");
		$installer->add_config("'1080', 'show_pet_talents', '0', 'radio{No^0|Yes^1', 'char_conf'");
		$installer->add_config("'1090', 'show_companions', '0', 'radio{No^0|Yes^1', 'char_conf'");
		$installer->add_config("'1091', 'show_mounts', '0', 'radio{No^0|Yes^1', 'char_conf'");

		$installer->create_table($installer->table('display'),"
		  `member_id` int(11) NOT NULL default '0',
		  `show_pets` varchar(30) NOT NULL default '0',
		  `show_reputation` varchar(30) NOT NULL default '0',
		  `show_honor` varchar(30) NOT NULL default '0',
		  `show_talents` varchar(30) NOT NULL default '0',
		  `show_glyphs` varchar(30) NOT NULL default '0',
		  `show_pet_talents` varchar(30) NOT NULL default '0',
		  `show_companions` varchar(30) NOT NULL default '0',
		  `show_mounts` varchar(30) NOT NULL default '0',
		  PRIMARY KEY  (`member_id`)");


		$installer->create_table($installer->table('default'),"
		  `show_pets` varchar(30) NOT NULL default '0',
		  `show_reputation` varchar(30) NOT NULL default '0',
		  `show_honor` varchar(30) NOT NULL default '0',
		  `show_talents` varchar(30) NOT NULL default '0',
		  `show_glyphs` varchar(30) NOT NULL default '0',
		  `show_pet_talents` varchar(30) NOT NULL default '0',
		  `show_companions` varchar(30) NOT NULL default '0',
		  `show_mounts` varchar(30) NOT NULL default '0'");

		$build_query = array(
			'show_pets' => '0',
			'show_reputation' => '0',
			'show_honor' => '0',
			'show_talents' => '0',
			'show_glyphs' => '0',
			'show_pet_talents' => '0',
			'show_companions' => '0',
			'show_mounts' => '0'
		);

		$installer->add_query('INSERT INTO `' . $installer->table('default') . '` ' . $roster->db->build_query('INSERT', $build_query) . ';');
		$installer->add_query('INSERT INTO `' . $installer->table('display') . '` SELECT `p`.`member_id` , `d` . * FROM `' . $roster->db->table('players') . '` p, `' . $installer->table('default') . '` d ');

		$installer->add_menu_button('cb_character', 'char', '', 'spell_holy_divinespirit');
		
		$permissions = array(
			array('catagory'	=> 'info', 'name' => 'per_show_pets',		'info' => 'per_show_pets_info',			'cfg_name' => 'show_pets'),
			array('catagory'	=> 'info', 'name' => 'per_show_reputation',	'info' => 'per_show_reputation_info',	'cfg_name' => 'show_reputation'),
			array('catagory'	=> 'info', 'name' => 'per_show_skills',		'info' => 'per_show_skills_info',		'cfg_name' => 'show_skills'),
			array('catagory'	=> 'info', 'name' => 'per_show_talents',	'info' => 'per_show_talents_info',		'cfg_name' => 'show_talents'),
			array('catagory'	=> 'info', 'name' => 'per_show_pet_talents','info' => 'per_show_pet_talents_info',	'cfg_name' => 'show_pet_talents'),
			array('catagory'	=> 'info', 'name' => 'per_show_companions',	'info' => 'per_show_companions_info',	'cfg_name' => 'show_companions'),
			array('catagory'	=> 'info', 'name' => 'per_show_mounts',		'info' => 'per_show_mounts_info',		'cfg_name' => 'show_mounts')
		);
		$installer->add_permissions($permissions);
		

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
		global $installer, $roster;

		return true;
	}

	/**
	 * Un-Install Function
	 *
	 * @return bool
	 */
	function uninstall()
	{
		global $installer, $roster;

		$installer->remove_all_config();
		$installer->remove_all_menu_button();
		$installer->remove_all_permissions();
		$installer->drop_table($installer->table('display'));
		$installer->drop_table($installer->table('default'));

		return true;
	}
}
