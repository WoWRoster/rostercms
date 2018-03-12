<?php
/**
 * WoWRoster.net WoWRoster
 *
 * LICENSE: Licensed under the Creative Commons
 *          "Attribution-NonCommercial-ShareAlike 2.5" license
 *
 * @copyright  2002-2007 WoWRoster.net
 * @license    http://creativecommons.org/licenses/by-nc-sa/2.5   Creative Commons "Attribution-NonCommercial-ShareAlike 2.5"
 * @link       http://www.wowroster.net
 * @package    Roster Gallery
 * @subpackage Installer
*/

if ( !defined('ROSTER_INSTALLED') )
{
    exit('Detected invalid access to this file!');
}

/**
 * Installer for Roster Gallery Addon
 *
 * @package    Roster Gallery
 * @subpackage Installer
 *
 */
class galleryinstall
{
	var $active = true;
	var $icon = 'inv_misc_ selfiecamera_01';

	var $version = '3.5.0';
	var $oldversion = '';
	var $wrnet_id = '62';

	var $fullname = 'Gallery';
	var $description = 'Guild Screenshot database.';
	var $credits = array(
		array(	"name"=>	"Ulminia <Ulminia@gmail.com>",
				"info"=>	"Guild Screenshot database (Alpha Release)"),
	);

	/**
	 * Install Function
	 *
	 * @return bool
	 */
	function install()
	{
		global $installer, $addon;

		# Master data for the config file
		$installer->add_config("'62101','startpage','gallery_conf','display','master'");
		$installer->add_config("'62102','gallery_conf',NULL,'blockframe','menu'");
		$installer->add_config("'62103','gallery_uap','rostercp-addon-gallery-uap','makelink','menu'");
		$installer->add_config("'62104','gallery_ap','rostercp-addon-gallery-ap','makelink','menu'");
		$installer->add_config("'62116','gallery_cat','rostercp-addon-gallery-catagory','makelink','menu'");
		$installer->add_config("'62105', 'rg_lpp', '3','radio{enabled^1|disabled^0', 'gallery_conf'");
		$installer->add_config("'62106', 'rg_dct', '1','radio{enabled^1|disabled^0', 'gallery_conf'");
		$installer->add_config("'62107', 'rg_dspc', '1','radio{enabled^1|disabled^0', 'gallery_conf'");
		$installer->add_config("'62108', 'rg_dscp', '1','radio{enabled^1|disabled^0', 'gallery_conf'");
		$installer->add_config("'62109', 'rg_u_ovlb', '1','radio{enabled^1|disabled^0', 'gallery_conf'");
		$installer->add_config("'62110', 'rg_u_lb', '0','radio{enabled^1|disabled^0', 'gallery_conf'");
		$installer->add_config("'62111', 'rg_rating_align', '0','radio{top^1|bottom^0', 'gallery_conf'");
		$installer->add_config("'62112', 'rg_caption_align', '1','radio{top^1|bottom^0', 'gallery_conf'");
		$installer->add_config("'62113', 'rg_dul', '0','radio{enabled^1|disabled^0', 'gallery_conf'");
		$installer->add_config("'62114', 'rg_upload_size', '650','text{10', 'gallery_conf'");
		$installer->add_config("'62115', 'rg_upload_win', 'true', 'radio{enabled^1|disabled^0', 'gallery_conf'");

		$installer->add_query("DROP TABLE IF EXISTS `" . $installer->table('ss') . "`;");
		
		$installer->add_query("
		CREATE TABLE `" . $installer->table('ss') . "` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `file` varchar(255) NOT NULL default '',
            `caption` varchar(255) NOT NULL default '',
            `disc` text NOT NULL,
            `ext` varchar(255) NOT NULL default '',
            `catagory` varchar(255) NOT NULL default '',
            `approve` varchar(3) NOT NULL default '',
            `votes` varchar(10) NOT NULL default '',
            `rateing` varchar(10) NOT NULL default '',
            PRIMARY KEY  (`id`)
            ) ;");
			
		$installer->add_query("
		CREATE TABLE `" . $installer->table('catagories') . "` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`cat_name` varchar(40) NOT NULL DEFAULT '',
			`cat_desc` varchar(200) NOT NULL DEFAULT '',
			`cat_stat` varchar(10) NOT NULL DEFAULT '',
			`cat_access` varchar(30) NOT NULL DEFAULT '0:',
			`cat_lock` int(10) NOT NULL DEFAULT '0',
            PRIMARY KEY  (`id`)
            ) ;");

		#permissions
		$permissions = array(
			array('catagory'=> 'gallery_per', 'name'=> 'gallery_canpost', 'info'=> 'gallery_canpost_i', 'cfg_name'=> 'gallery_canpost'),
			array('catagory'=> 'gallery_per', 'name'=> 'gallery_autoapprove', 'info'=> 'gallery_autoapprove_i', 'cfg_name'=> 'gallery_autoapprove'),
			array('catagory'=> 'gallery_per', 'name'=> 'gallery_candelete', 'info'=> 'gallery_candelete_i', 'cfg_name'=> 'gallery_candelete'),
		);
		$installer->add_permissions($permissions);
		
		# Roster menu entry
		$installer->add_menu_button('rg_button','util','','inv_misc_selfiecamera_01');
      	$ssfolder = array('screenshots','screenshots/thumbs/'); 
      	$addondir = 'addons/gallery/';
		$struct = $addondir;
		
		chmod( $addondir ,0777 );
		foreach($ssfolder as $dir){
			if (chmod( $struct.$dir,0777 ))
			{
				$installer->setmessages(' '.$dir.' set to <font color=green>777</font> successful');
			}
			else
			{
				$installer->setmessages(' '.$dir.' <font color=red>Not set to 777</font> successful chmod manualy on your server');
			}
		}
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
	    global $installer, $addon,$roster;
        return true;

	}

	/**
	 * Un-Install Function
	 *
	 * @return bool
	 */
	function uninstall()
	{
		global $installer, $addon;

		$installer->remove_all_config();
            
		# remove the screenshot dir
		$dir = 'addons/gallery/screenshots/';
		$this->deleteDir($dir);

		$installer->remove_all_menu_button();
		$installer->remove_all_permissions();;
		//$installer->add_query("DROP TABLE IF EXISTS `" . $installer->table('ss') . "`;");
		//$installer->add_query("DROP TABLE IF EXISTS `" . $installer->table('catagories') . "`;");
		$installer->drop_table($installer->table('ss'));
		$installer->drop_table($installer->table('catagories'));
		return true;
	}
	
	function deleteDir($dir)
      {
            global $installer, $addon;

            if (substr($dir,-1) != "/") $dir .= "/";
                  if (!is_dir($dir)) return false;

                        if (($dh = opendir($dir)) !== false) {
                              while (($entry = readdir($dh)) !== false) {
                                    if ($entry != "." && $entry != ".." && $entry != "index.html") {
                                          if (is_file($dir . $entry) || is_link($dir . $entry)) unlink($dir . $entry);
                                    else if (is_dir($dir . $entry)) $this->deleteDir($dir . $entry);
                                    }
                              }
                        closedir($dh);
                        //rmdir($dir);

                        return true;
                        $installer->setmessages('<br>All images removed');
                  }
            return false;
            $installer->setmessages('<br>Images Could not br removed<br>delete manualy');
      }
}
