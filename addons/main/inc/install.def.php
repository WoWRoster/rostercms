<?php
/**
 * WoWRoster.net WoWRoster
 *
 * @copyright  2002-2011 WoWRoster.net
 * @license    http://www.gnu.org/licenses/gpl.html   Licensed under the GNU General Public License v3. * @package    News
 * @subpackage Installer
*/

if ( !defined('IN_ROSTER') )
{
	exit('Detected invalid access to this file!');
}

/**
 * News Addon Installer
 * @package News
 * @subpackage Installer
 */
class mainInstall
{
	var $active = true;
	var $icon = 'ability_warrior_rallyingcry';

	var $version = '2.9.9.0000';
	var $wrnet_id = '0';

	var $fullname = 'WoWRoster Portal';
	var $description = 'A \'front page\' for WoWRoster. Display user controls, post news and slideshow images.';
	var $credits = array(
		array("name"=>	"Ulminia",
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

		$installer->add_config("'1','startpage','cmsmain_conf','display','master'");
		$installer->add_config("'100','cmsmain_conf',NULL,'blockframe','menu'");
		$installer->add_config("'200','cmsmain_slider',NULL,'blockframe','menu'");
		$installer->add_config("'300','cmsmain_newsposts','rostercp-addon-main-news','makelink','menu'");
		$installer->add_config("'400','cmsmain_slider_images','rostercp-addon-main-sliderimages','makelink','menu'");
		$installer->add_config("'500','cmsmain_slider_add','rostercp-addon-main-slideradd','makelink','menu'");
		$installer->add_config("'600','cmsmain_plugins','rostercp-addon-main-plugins','makelink','menu'");

		$installer->add_config("'1000','news_add','11','access','cmsmain_conf'");
		$installer->add_config("'1010','news_edit','11','access','cmsmain_conf'");
		$installer->add_config("'1020','comm_add','0','access','cmsmain_conf'");
		$installer->add_config("'1030','comm_edit','11','access','cmsmain_conf'");

		$installer->add_config("'2000','slider_skin','camera_azure_skin','function{sliderSkin','cmsmain_slider'");
		$installer->add_config("'2010','slider_alignment','center','select{topLeft^topLeft|topCenter^topCenter|topRight^topRight|centerLeft^centerLeft|center^center|centerRight^centerRight|bottomLeft^bottomLeft|bottomCenter^bottomCenter|bottomRight^bottomRight','cmsmain_slider'");
		$installer->add_config("'2020','slider_autoAdvance','true','radio{yes^true|no^false','cmsmain_slider'");
		$installer->add_config("'2030','slider_mobileAutoAdvance','true','radio{yes^true|no^false','cmsmain_slider'");
		$installer->add_config("'2040','slider_barDirection','leftToRight','select{leftToRight^leftToRight|rightToLeft^rightToLeft|topToBottom^topToBottom|bottomToTop^bottomToTop','cmsmain_slider'");
		$installer->add_config("'2050','slider_barPosition','bottom','select{left^left|right^right|top^top|bottom^bottom','cmsmain_slider'");
		$installer->add_config("'2060','slider_easing','easeInOutExpo','function{sliderEasing','cmsmain_slider'");
		$installer->add_config("'2070','slider_mobileEasing','','function{sliderEasing','cmsmain_slider'");
		$installer->add_config("'2080','slider_fx','random','function{sliderFx','cmsmain_slider'");
		$installer->add_config("'2090','slider_mobileFx','','function{sliderFx','cmsmain_slider'");
		$installer->add_config("'2100','slider_gridDifference','250','text{10|10','cmsmain_slider'");
		$installer->add_config("'2110','slider_height','50%','text{10|10','cmsmain_slider'");
		$installer->add_config("'2120','slider_hover','true','radio{yes^true|no^false','cmsmain_slider'");
		$installer->add_config("'2130','slider_loader','pie','select{pie^pie|bar^bar|none^none','cmsmain_slider'");
		$installer->add_config("'2140','slider_loaderColor','#EEEEEE','color','cmsmain_slider'");
		$installer->add_config("'2150','slider_loaderBgColor','#222222','color','cmsmain_slider'");
		$installer->add_config("'2160','slider_loaderOpacity','0.8','select{0^0|0.1^0.1|0.2^0.2|0.3^0.3|0.4^0.4|0.5^0.5|0.6^0.6|0.7^0.7|0.8^0.8|0.9^0.9|1.0^1.0','cmsmain_slider'");
		$installer->add_config("'2170','slider_loaderPadding','2','text{10|10','cmsmain_slider'");
		$installer->add_config("'2180','slider_loaderStroke','7','text{10|10','cmsmain_slider'");
		$installer->add_config("'2190','slider_minHeight','200px','text{10|10','cmsmain_slider'");
		$installer->add_config("'2200','slider_navigation','true','radio{yes^true|no^false','cmsmain_slider'");
		$installer->add_config("'2210','slider_navigationHover','true','radio{yes^true|no^false','cmsmain_slider'");
		$installer->add_config("'2220','slider_mobileNavHover','true','radio{yes^true|no^false','cmsmain_slider'");
		$installer->add_config("'2230','slider_opacityOnGrid','false','radio{yes^true|no^false','cmsmain_slider'");
		$installer->add_config("'2240','slider_overlayer','false','radio{yes^true|no^false','cmsmain_slider'");
		$installer->add_config("'2250','slider_pagination','true','radio{yes^true|no^false','cmsmain_slider'");
		$installer->add_config("'2260','slider_playPause','true','radio{yes^true|no^false','cmsmain_slider'");
		$installer->add_config("'2270','slider_pauseOnClick','true','radio{yes^true|no^false','cmsmain_slider'");
		$installer->add_config("'2280','slider_pieDiameter','38','text{10|10','cmsmain_slider'");
		$installer->add_config("'2290','slider_piePosition','rightTop','select{rightTop^rightTop|leftTop^leftTop|leftBottom^leftBottom|rightBottom^rightBottom','cmsmain_slider'");
		$installer->add_config("'2300','slider_portrait','false','radio{yes^true|no^false','cmsmain_slider'");
		$installer->add_config("'2310','slider_cols','6','text{10|10','cmsmain_slider'");
		$installer->add_config("'2320','slider_rows','4','text{10|10','cmsmain_slider'");
		$installer->add_config("'2330','slider_slicedCols','12','text{10|10','cmsmain_slider'");
		$installer->add_config("'2340','slider_slicedRows','8','text{10|10','cmsmain_slider'");
		$installer->add_config("'2350','slider_slideOn','random','select{next^next|prev^prev|random^random','cmsmain_slider'");
		$installer->add_config("'2360','slider_thumbnails','false','radio{yes^true|no^false','cmsmain_slider'");
		$installer->add_config("'2370','slider_time','7000','text{10|10','cmsmain_slider'");
		$installer->add_config("'2380','slider_transPeriod','1500','text{10|10','cmsmain_slider'");


		$installer->create_table($installer->table('config'),"
			`guild_id` int(11) unsigned NOT NULL DEFAULT '0',
			`config_name` varchar(64) NOT NULL DEFAULT '',
			`config_value` varchar(225) NOT NULL DEFAULT '',
			PRIMARY KEY (`guild_id`,`config_name`)");

		$installer->create_table($installer->table('blocks'),"
			`guild_id` int(11) unsigned NOT NULL DEFAULT '0',
			`block_name` varchar(64) NOT NULL DEFAULT '',
			`block_id` INT( 10 ) NULL DEFAULT NULL,
			`block_location` varchar(10) NOT NULL DEFAULT '',
			PRIMARY KEY (`guild_id`,`block_name`)");

		$installer->create_table($installer->table('slider'),"
			`id` int(5) NOT NULL AUTO_INCREMENT,
			`b_id` varchar(10) DEFAULT NULL,
			`b_image` varchar(255) DEFAULT NULL,
			`b_desc` varchar(150) DEFAULT NULL,
			`b_url` varchar(255) NOT NULL DEFAULT '#',
			`b_title` varchar(255) DEFAULT NULL,
			`b_active` int(10) DEFAULT NULL,
			`b_video` int(10) DEFAULT NULL,
			PRIMARY KEY (`id`)");

		$installer->create_table($installer->table('images'),"
			`id` int(5) NOT NULL,
			`image` varchar(255) DEFAULT NULL,
			`basename` varchar(150) DEFAULT NULL,
			`ext` varchar(150) DEFAULT NULL,
			`url` varchar(255) NOT NULL DEFAULT '#',
			PRIMARY KEY (`id`)");

		$installer->create_table($installer->table('news'),"
			`news_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			`name` varchar(255) DEFAULT NULL,
			`title` varchar(200) DEFAULT NULL,
			`text` longtext,
			`img` varchar(255) DEFAULT NULL,
			`news_type` varchar(25) DEFAULT NULL,
			`comm_count` int(11) UNSIGNED NOT NULL,
			`poster` varchar(100) DEFAULT NULL,
			`poster_id` int(10) DEFAULT NULL,
			`poster_ip` varchar(30) NOT NULL,
			`poster_ipx` varchar(30) NOT NULL,
			`date` datetime DEFAULT NULL,
			`html` tinyint(1) DEFAULT NULL,
			PRIMARY KEY (`news_id`)");

		$installer->create_table($installer->table('comments'),"
			`comment_id` int(11) UNSIGNED NOT NULL,
			`news_id` int(11) UNSIGNED NOT NULL,
			`author` varchar(16) NOT NULL DEFAULT '',
			`author_id` int(10) NOT NULL,
			`date` datetime DEFAULT NULL,
			`content` longtext,
			`html` tinyint(1) DEFAULT NULL,
			PRIMARY KEY (`comment_id`)");

		$permissions = array(
			array('catagory'=> 'main_news','name'=> 'can_post','info'=> 'news_can_post_info','cfg_name'=> 'news_can_post'),
			array('catagory'=> 'main_news','name'=> 'can_edit_post','info'=> 'news_can_post_edit_info','cfg_name'=> 'news_can_edit_post'),
			array('catagory'=> 'main_news','name'=> 'can_post_comment','info'=> 'news_can_post_comment_info','cfg_name'=> 'news_can_post_comment'),
			array('catagory'=> 'main_news','name'=> 'can_edit_comment','info'=> 'news_can_edit_comment_info','cfg_name'=> 'news_can_edit_comment'),
			//array('catagory'=> 'forum_topics','name'=> 'forum_topics_move','info'=> 'forum_topics_move_desc','cfg_name'=> 'topic_move'),
		);
		$installer->add_permissions($permissions);
		
		$installer->add_menu_button('cms_button', 'guild');

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
		global $installer, $roster;

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
		$installer->drop_table($installer->table('config'));
		$installer->drop_table($installer->table('comments'));
		$installer->drop_table($installer->table('blocks'));
		$installer->drop_table($installer->table('slider'));
		$installer->drop_table($installer->table('images'));
		$installer->drop_table($installer->table('news'));
		$installer->remove_all_config();
		$installer->remove_all_permissions();
		$installer->remove_all_menu_button();
		return true;
	}
}
