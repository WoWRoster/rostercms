#
# MySQL Roster Structure File
#
# * $Id: mysql_structure.sql 2632 2014-08-21 20:28:28Z ulminia@gmail.com $
#
# --------------------------------------------------------
### Account

CREATE TABLE IF NOT EXISTS `renprefix_account` (
  `account_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '',
  `hash` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_addon`
##

CREATE TABLE IF NOT EXISTS `renprefix_addon` (
  `addon_id` int(11) NOT NULL AUTO_INCREMENT,
  `basename` varchar(16) NOT NULL DEFAULT '',
  `version` varchar(16) NOT NULL DEFAULT '0',
  `active` int(1) NOT NULL DEFAULT '1',
  `access` varchar(30) NOT NULL DEFAULT '0',
  `fullname` tinytext NOT NULL,
  `description` mediumtext NOT NULL,
  `credits` mediumtext NOT NULL,
  `icon` varchar(64) NOT NULL DEFAULT '',
  `wrnet_id` int(4) NOT NULL DEFAULT '0',
  `versioncache` tinytext,
  PRIMARY KEY (`addon_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_addon_config`
##

CREATE TABLE IF NOT EXISTS `renprefix_addon_config` (
  `addon_id` int(11) NOT NULL DEFAULT '0',
  `id` int(11) UNSIGNED NOT NULL,
  `config_name` varchar(255) DEFAULT NULL,
  `config_value` tinytext,
  `form_type` mediumtext,
  `config_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`,`addon_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_all_gems`
##

CREATE TABLE IF NOT EXISTS `renprefix_all_gems` (
  `gem_id` int(11) NOT NULL,
  `gem_name` varchar(96) NOT NULL,
  `gem_color` varchar(16) NOT NULL,
  `gem_tooltip` mediumtext NOT NULL,
  `gem_texture` varchar(64) NOT NULL,
  `gem_bonus` varchar(255) NOT NULL,
  `gem_bonus_stat1` varchar(255) NOT NULL,
  `gem_bonus_stat2` varchar(255) NOT NULL,
  `locale` varchar(16) NOT NULL,
  `timestamp` int(10) NOT NULL,
  `json` longtext,
  PRIMARY KEY (`gem_id`,`locale`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_api_cache`
##

CREATE TABLE IF NOT EXISTS `renprefix_api_cache` (
  `id` int(11) NOT NULL,
  `type` varchar(96) NOT NULL,
  `lastmodified` varchar(16) NOT NULL,
  `name` varchar(64) NOT NULL,
  `locale` varchar(16) DEFAULT NULL,
  `json` longtext,
  `timestamp` int(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_api_enchant`
##

CREATE TABLE IF NOT EXISTS `renprefix_api_enchant` (
  `id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `name` varchar(200) NOT NULL,
  `bonus` mediumtext,
  `slot` varchar(30) NOT NULL,
  `icon` varchar(64) NOT NULL,
  `description` mediumtext NOT NULL,
  `castTime` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_api_error`
##

CREATE TABLE IF NOT EXISTS `renprefix_api_error` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) DEFAULT NULL,
  `error` varchar(250) DEFAULT NULL,
  `error_info` text,
  `content_type` varchar(100) DEFAULT NULL,
  `responce_code` varchar(100) DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  `total` int(10) NOT NULL DEFAULT '0',
  `date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_api_gems`
##

CREATE TABLE IF NOT EXISTS `renprefix_api_gems` (
  `gem_id` int(11) NOT NULL,
  `gem_name` varchar(96) NOT NULL DEFAULT '',
  `gem_color` varchar(16) NOT NULL DEFAULT '',
  `gem_tooltip` mediumtext NOT NULL,
  `gem_texture` varchar(64) NOT NULL DEFAULT '',
  `gem_bonus` varchar(255) NOT NULL DEFAULT '',
  `locale` varchar(16) NOT NULL DEFAULT '',
  `timestamp` int(10) NOT NULL,
  `json` longtext,
  PRIMARY KEY (`gem_id`,`locale`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_api_items`
##

CREATE TABLE IF NOT EXISTS `renprefix_api_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `context` varchar(100) DEFAULT NULL,
  `bl` varchar(100) DEFAULT NULL,
  `item_name` varchar(96) NOT NULL,
  `item_color` varchar(16) NOT NULL,
  `item_texture` varchar(64) NOT NULL,
  `item_tooltip` mediumtext NOT NULL,
  `level` int(11) DEFAULT NULL,
  `item_level` int(11) DEFAULT NULL,
  `item_type` varchar(64) DEFAULT NULL,
  `item_subtype` varchar(64) DEFAULT NULL,
  `item_rarity` int(4) NOT NULL DEFAULT '0',
  `locale` varchar(16) DEFAULT NULL,
  `timestamp` int(10) NOT NULL,
  `json` longtext,
  PRIMARY KEY (`id`),
  UNIQUE KEY `item_id` (`item_id`,`context`,`bl`,`locale`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_api_lmh`
##

CREATE TABLE IF NOT EXISTS `renprefix_api_lmh` (
  `idx` int(10) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) DEFAULT NULL,
  `battletag` varchar(25) DEFAULT '',
  `id` varchar(40) DEFAULT '',
  `data` varchar(255) NOT NULL DEFAULT '',
  `follower` varchar(50) DEFAULT '',
  `artisan` varchar(50) DEFAULT '',
  `leaderboard` varchar(10) DEFAULT '',
  `name` varchar(100) DEFAULT '',
  `server` varchar(100) DEFAULT '',
  `region` varchar(10) DEFAULT '',
  `size` varchar(10) DEFAULT '',
  `fields` varchar(125) DEFAULT '',
  `context` varchar(50) DEFAULT '',
  `bl` varchar(50) DEFAULT NULL,
  `Last-Modified` varchar(25) NOT NULL DEFAULT '',
  PRIMARY KEY (`idx`),
  UNIQUE KEY `battletag` (`battletag`,`id`,`follower`,`artisan`,`leaderboard`,`name`,`server`,`region`,`size`,`fields`,`context`,`bl`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

## --------------------------------------------------------

##
## Table structure for table `renprefix_api_usage`
##

CREATE TABLE IF NOT EXISTS `renprefix_api_usage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) DEFAULT NULL,
  `url` mediumtext,
  `responce_code` varchar(20) DEFAULT NULL,
  `content_type` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `total` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

## --------------------------------------------------------

##
## Table structure for table `renprefix_companions`
##

CREATE TABLE IF NOT EXISTS `renprefix_companions` (
  `comp_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `name` varchar(96) NOT NULL,
  `type` varchar(96) NOT NULL,
  `slot` int(11) NOT NULL,
  `spellid` int(11) NOT NULL DEFAULT '0',
  `icon` varchar(64) NOT NULL DEFAULT '',
  `creatureid` int(11) NOT NULL DEFAULT '0',
  `tooltip` mediumtext NOT NULL,
  PRIMARY KEY (`comp_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_config`
##

CREATE TABLE IF NOT EXISTS `renprefix_config` (
  `id` int(11) UNSIGNED NOT NULL,
  `config_name` varchar(255) DEFAULT NULL,
  `config_value` tinytext,
  `form_type` mediumtext,
  `config_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_error_msg`
##

CREATE TABLE IF NOT EXISTS `renprefix_error_msg` (
  `id` int(11) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `error` varchar(250) DEFAULT NULL,
  `error_info` text,
  `content_type` varchar(100) DEFAULT NULL,
  `responce_code` varchar(100) DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  `total` int(10) NOT NULL DEFAULT '0',
  `date` date DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_gems`
##

CREATE TABLE IF NOT EXISTS `renprefix_gems` (
  `gem_id` int(11) NOT NULL DEFAULT '0',
  `gem_name` varchar(96) NOT NULL DEFAULT '',
  `gem_color` varchar(16) NOT NULL DEFAULT '',
  `gem_tooltip` mediumtext NOT NULL,
  `gem_texture` varchar(64) NOT NULL DEFAULT '',
  `gem_bonus` varchar(255) NOT NULL DEFAULT '',
  `gem_socketid` int(11) NOT NULL DEFAULT '0',
  `locale` varchar(16) NOT NULL DEFAULT '',
  PRIMARY KEY (`gem_id`,`locale`),
  KEY `gem_socketid` (`gem_socketid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_glyphs`
##

CREATE TABLE IF NOT EXISTS `renprefix_glyphs` (
  `member_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `glyph_build` tinyint(2) NOT NULL DEFAULT '0',
  `glyph_order` tinyint(4) NOT NULL DEFAULT '0',
  `glyph_type` tinyint(4) NOT NULL DEFAULT '0',
  `glyph_name` varchar(96) NOT NULL DEFAULT '',
  `glyph_icon` varchar(64) NOT NULL DEFAULT '',
  `glyph_tooltip` mediumtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_guild`
##

CREATE TABLE IF NOT EXISTS `renprefix_guild` (
  `guild_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `guild_name` varchar(64) NOT NULL DEFAULT '',
  `server` varchar(32) NOT NULL DEFAULT '',
  `region` char(2) NOT NULL DEFAULT '',
  `faction` varchar(32) NOT NULL DEFAULT '',
  `factionEn` varchar(32) NOT NULL DEFAULT '',
  `guild_motd` varchar(255) NOT NULL DEFAULT '',
  `guild_num_members` int(11) NOT NULL DEFAULT '0',
  `guild_num_accounts` int(11) NOT NULL DEFAULT '0',
  `guild_xp` varchar(32) DEFAULT '',
  `guild_xpcap` varchar(32) DEFAULT '',
  `guild_level` varchar(32) DEFAULT '',
  `update_time` datetime DEFAULT NULL,
  `GPversion` varchar(6) NOT NULL DEFAULT '0.0.0',
  `DBversion` varchar(6) NOT NULL DEFAULT '0.0.0',
  `guild_info_text` mediumtext,
  PRIMARY KEY (`guild_id`),
  KEY `guild` (`guild_name`,`server`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_guild_rank`
##

CREATE TABLE IF NOT EXISTS `renprefix_guild_rank` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rank` tinyint(4) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `control` varchar(255) NOT NULL DEFAULT '0',
  `guild_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_items`
##

CREATE TABLE IF NOT EXISTS `renprefix_items` (
  `member_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `item_name` varchar(96) NOT NULL DEFAULT '',
  `item_parent` varchar(64) NOT NULL DEFAULT '',
  `item_slot` varchar(32) NOT NULL DEFAULT '',
  `item_color` varchar(16) NOT NULL DEFAULT '',
  `item_id` varchar(64) DEFAULT NULL,
  `item_texture` varchar(64) NOT NULL DEFAULT '',
  `item_quantity` int(11) DEFAULT NULL,
  `item_tooltip` mediumtext NOT NULL,
  `level` int(11) DEFAULT NULL,
  `item_level` int(11) DEFAULT NULL,
  `item_type` varchar(64) DEFAULT NULL,
  `item_subtype` varchar(64) DEFAULT NULL,
  `item_rarity` int(4) NOT NULL DEFAULT '-1',
  `locale` varchar(4) DEFAULT NULL,
  `json` longtext,
  PRIMARY KEY (`member_id`,`item_parent`,`item_slot`),
  KEY `parent` (`item_parent`),
  KEY `slot` (`item_slot`),
  KEY `name` (`item_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_memberlog`
##

CREATE TABLE IF NOT EXISTS `renprefix_memberlog` (
  `log_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `server` varchar(32) NOT NULL DEFAULT '',
  `region` char(2) NOT NULL DEFAULT '',
  `guild_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `class` varchar(32) NOT NULL DEFAULT '',
  `classid` int(11) NOT NULL DEFAULT '0',
  `level` int(11) NOT NULL DEFAULT '0',
  `note` varchar(255) NOT NULL DEFAULT '',
  `guild_rank` int(11) DEFAULT '0',
  `guild_title` varchar(64) DEFAULT NULL,
  `officer_note` varchar(255) NOT NULL DEFAULT '',
  `update_time` datetime DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_members`
##

CREATE TABLE IF NOT EXISTS `renprefix_members` (
  `member_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `server` varchar(32) NOT NULL DEFAULT '',
  `region` char(2) NOT NULL DEFAULT '',
  `guild_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `class` varchar(32) NOT NULL DEFAULT '',
  `classid` int(11) NOT NULL DEFAULT '0',
  `level` int(11) NOT NULL DEFAULT '0',
  `note` varchar(255) NOT NULL DEFAULT '',
  `guild_rank` int(11) DEFAULT '0',
  `guild_title` varchar(64) DEFAULT NULL,
  `officer_note` varchar(255) NOT NULL DEFAULT '',
  `zone` varchar(64) NOT NULL DEFAULT '',
  `status` varchar(16) NOT NULL DEFAULT '',
  `online` int(1) DEFAULT '0',
  `last_online` datetime DEFAULT NULL,
  `account_id` smallint(6) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`member_id`),
  KEY `member` (`guild_id`,`name`),
  KEY `name` (`name`),
  KEY `char` (`region`,`server`,`name`),
  KEY `class` (`class`),
  KEY `level` (`level`),
  KEY `guild_rank` (`guild_rank`),
  KEY `last_online` (`last_online`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_menu`
##

CREATE TABLE IF NOT EXISTS `renprefix_menu` (
  `config_id` int(11) NOT NULL AUTO_INCREMENT,
  `section` varchar(64) DEFAULT NULL,
  `config` mediumtext,
  PRIMARY KEY (`config_id`),
  UNIQUE KEY `section` (`section`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_menu_button`
##

CREATE TABLE IF NOT EXISTS `renprefix_menu_button` (
  `button_id` int(11) NOT NULL AUTO_INCREMENT,
  `addon_id` int(11) NOT NULL COMMENT '0 for main roster',
  `title` varchar(32) DEFAULT NULL,
  `scope` varchar(16) DEFAULT NULL,
  `url` varchar(128) DEFAULT NULL,
  `icon` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`button_id`),
  KEY `idtitle` (`addon_id`,`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_permissions`
##

CREATE TABLE IF NOT EXISTS `renprefix_permissions` (
  `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL DEFAULT '',
  `type_id` int(5) DEFAULT NULL,
  `catagory` varchar(30) DEFAULT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `info` varchar(255) NOT NULL DEFAULT '',
  `cfg_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_pets`
##

CREATE TABLE IF NOT EXISTS `renprefix_pets` (
  `pet_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `slot` int(11) NOT NULL DEFAULT '0',
  `level` int(11) NOT NULL DEFAULT '0',
  `health` int(11) NOT NULL DEFAULT '0',
  `mana` int(11) NOT NULL DEFAULT '0',
  `power` varchar(32) NOT NULL DEFAULT '',
  `xp` varchar(32) NOT NULL DEFAULT '0',
  `totaltp` int(11) NOT NULL DEFAULT '0',
  `type` varchar(32) NOT NULL DEFAULT '',
  `icon` varchar(64) NOT NULL DEFAULT '',
  `melee_power` int(11) NOT NULL DEFAULT '0',
  `melee_power_c` int(11) NOT NULL DEFAULT '0',
  `melee_power_b` int(11) NOT NULL DEFAULT '0',
  `melee_power_d` int(11) NOT NULL DEFAULT '0',
  `melee_hit` int(11) NOT NULL DEFAULT '0',
  `melee_hit_c` int(11) NOT NULL DEFAULT '0',
  `melee_hit_b` int(11) NOT NULL DEFAULT '0',
  `melee_hit_d` int(11) NOT NULL DEFAULT '0',
  `melee_crit` int(11) NOT NULL DEFAULT '0',
  `melee_crit_c` int(11) NOT NULL DEFAULT '0',
  `melee_crit_b` int(11) NOT NULL DEFAULT '0',
  `melee_crit_d` int(11) NOT NULL DEFAULT '0',
  `melee_haste` int(11) NOT NULL DEFAULT '0',
  `melee_haste_c` int(11) NOT NULL DEFAULT '0',
  `melee_haste_b` int(11) NOT NULL DEFAULT '0',
  `melee_haste_d` int(11) NOT NULL DEFAULT '0',
  `melee_expertise` int(11) NOT NULL DEFAULT '0',
  `melee_expertise_c` int(11) NOT NULL DEFAULT '0',
  `melee_expertise_b` int(11) NOT NULL DEFAULT '0',
  `melee_expertise_d` int(11) NOT NULL DEFAULT '0',
  `melee_crit_chance` float NOT NULL DEFAULT '0',
  `melee_power_dps` float NOT NULL DEFAULT '0',
  `melee_mhand_speed` float NOT NULL DEFAULT '0',
  `melee_mhand_dps` float NOT NULL DEFAULT '0',
  `melee_mhand_skill` int(11) NOT NULL DEFAULT '0',
  `melee_mhand_mindam` int(11) NOT NULL DEFAULT '0',
  `melee_mhand_maxdam` int(11) NOT NULL DEFAULT '0',
  `melee_mhand_rating` int(11) NOT NULL DEFAULT '0',
  `melee_mhand_rating_c` int(11) NOT NULL DEFAULT '0',
  `melee_mhand_rating_b` int(11) NOT NULL DEFAULT '0',
  `melee_mhand_rating_d` int(11) NOT NULL DEFAULT '0',
  `melee_range_tooltip` tinytext,
  `melee_power_tooltip` tinytext,
  `stat_int` int(11) NOT NULL DEFAULT '0',
  `stat_int_c` int(11) NOT NULL DEFAULT '0',
  `stat_int_b` int(11) NOT NULL DEFAULT '0',
  `stat_int_d` int(11) NOT NULL DEFAULT '0',
  `stat_agl` int(11) NOT NULL DEFAULT '0',
  `stat_agl_c` int(11) NOT NULL DEFAULT '0',
  `stat_agl_b` int(11) NOT NULL DEFAULT '0',
  `stat_agl_d` int(11) NOT NULL DEFAULT '0',
  `stat_sta` int(11) NOT NULL DEFAULT '0',
  `stat_sta_c` int(11) NOT NULL DEFAULT '0',
  `stat_sta_b` int(11) NOT NULL DEFAULT '0',
  `stat_sta_d` int(11) NOT NULL DEFAULT '0',
  `stat_str` int(11) NOT NULL DEFAULT '0',
  `stat_str_c` int(11) NOT NULL DEFAULT '0',
  `stat_str_b` int(11) NOT NULL DEFAULT '0',
  `stat_str_d` int(11) NOT NULL DEFAULT '0',
  `stat_spr` int(11) NOT NULL DEFAULT '0',
  `stat_spr_c` int(11) NOT NULL DEFAULT '0',
  `stat_spr_b` int(11) NOT NULL DEFAULT '0',
  `stat_spr_d` int(11) NOT NULL DEFAULT '0',
  `stat_def` int(11) NOT NULL DEFAULT '0',
  `stat_def_c` int(11) NOT NULL DEFAULT '0',
  `stat_def_b` int(11) NOT NULL DEFAULT '0',
  `stat_def_d` int(11) NOT NULL DEFAULT '0',
  `stat_armor` int(11) NOT NULL DEFAULT '0',
  `stat_armor_c` int(11) NOT NULL DEFAULT '0',
  `stat_armor_b` int(11) NOT NULL DEFAULT '0',
  `stat_armor_d` int(11) NOT NULL DEFAULT '0',
  `stat_block` int(11) NOT NULL DEFAULT '0',
  `stat_block_c` int(11) NOT NULL DEFAULT '0',
  `stat_block_b` int(11) NOT NULL DEFAULT '0',
  `stat_block_d` int(11) NOT NULL DEFAULT '0',
  `stat_parry` int(11) NOT NULL DEFAULT '0',
  `stat_parry_c` int(11) NOT NULL DEFAULT '0',
  `stat_parry_b` int(11) NOT NULL DEFAULT '0',
  `stat_parry_d` int(11) NOT NULL DEFAULT '0',
  `stat_defr` int(11) NOT NULL DEFAULT '0',
  `stat_defr_c` int(11) NOT NULL DEFAULT '0',
  `stat_defr_b` int(11) NOT NULL DEFAULT '0',
  `stat_defr_d` int(11) NOT NULL DEFAULT '0',
  `stat_dodge` int(11) NOT NULL DEFAULT '0',
  `stat_dodge_c` int(11) NOT NULL DEFAULT '0',
  `stat_dodge_b` int(11) NOT NULL DEFAULT '0',
  `stat_dodge_d` int(11) NOT NULL DEFAULT '0',
  `stat_res_ranged` int(11) NOT NULL DEFAULT '0',
  `stat_res_spell` int(11) NOT NULL DEFAULT '0',
  `stat_res_melee` int(11) NOT NULL DEFAULT '0',
  `res_holy` int(11) NOT NULL DEFAULT '0',
  `res_holy_c` int(11) NOT NULL DEFAULT '0',
  `res_holy_b` int(11) NOT NULL DEFAULT '0',
  `res_holy_d` int(11) NOT NULL DEFAULT '0',
  `res_frost` int(11) NOT NULL DEFAULT '0',
  `res_frost_c` int(11) NOT NULL DEFAULT '0',
  `res_frost_b` int(11) NOT NULL DEFAULT '0',
  `res_frost_d` int(11) NOT NULL DEFAULT '0',
  `res_arcane` int(11) NOT NULL DEFAULT '0',
  `res_arcane_c` int(11) NOT NULL DEFAULT '0',
  `res_arcane_b` int(11) NOT NULL DEFAULT '0',
  `res_arcane_d` int(11) NOT NULL DEFAULT '0',
  `res_fire` int(11) NOT NULL DEFAULT '0',
  `res_fire_c` int(11) NOT NULL DEFAULT '0',
  `res_fire_b` int(11) NOT NULL DEFAULT '0',
  `res_fire_d` int(11) NOT NULL DEFAULT '0',
  `res_shadow` int(11) NOT NULL DEFAULT '0',
  `res_shadow_c` int(11) NOT NULL DEFAULT '0',
  `res_shadow_b` int(11) NOT NULL DEFAULT '0',
  `res_shadow_d` int(11) NOT NULL DEFAULT '0',
  `res_nature` int(11) NOT NULL DEFAULT '0',
  `res_nature_c` int(11) NOT NULL DEFAULT '0',
  `res_nature_b` int(11) NOT NULL DEFAULT '0',
  `res_nature_d` int(11) NOT NULL DEFAULT '0',
  `dodge` float NOT NULL DEFAULT '0',
  `parry` float NOT NULL DEFAULT '0',
  `block` float NOT NULL DEFAULT '0',
  `mitigation` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`pet_id`,`member_id`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_pet_talents`
##

CREATE TABLE IF NOT EXISTS `renprefix_pet_talents` (
  `member_id` int(11) NOT NULL DEFAULT '0',
  `pet_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `name` varchar(64) NOT NULL DEFAULT '',
  `tree` varchar(64) NOT NULL DEFAULT '',
  `row` tinyint(4) NOT NULL DEFAULT '0',
  `column` tinyint(4) NOT NULL DEFAULT '0',
  `rank` tinyint(4) NOT NULL DEFAULT '0',
  `maxrank` tinyint(4) NOT NULL DEFAULT '0',
  `tooltip` mediumtext NOT NULL,
  `icon` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`member_id`,`pet_id`,`tree`,`row`,`column`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_pet_talenttree`
##

CREATE TABLE IF NOT EXISTS `renprefix_pet_talenttree` (
  `member_id` int(11) NOT NULL DEFAULT '0',
  `pet_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `tree` varchar(64) NOT NULL DEFAULT '',
  `background` varchar(64) NOT NULL DEFAULT '',
  `order` tinyint(4) NOT NULL DEFAULT '0',
  `pointsspent` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`member_id`,`pet_id`,`tree`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_players`
##

CREATE TABLE IF NOT EXISTS `renprefix_players` (

  `member_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `guild_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `dateupdatedutc` datetime DEFAULT NULL,
  `api_udt` int(10) NOT NULL DEFAULT '0',
  `CPversion` varchar(6) NOT NULL DEFAULT '0.0.0',
  `DBversion` varchar(6) NOT NULL DEFAULT '0.0.0',
  `race` varchar(32) NOT NULL DEFAULT '',
  `raceid` tinyint(1) NOT NULL DEFAULT '0',
  `raceEn` varchar(32) NOT NULL DEFAULT '',
  `sex` varchar(10) NOT NULL DEFAULT '',
  `sexid` tinyint(1) NOT NULL DEFAULT '0',
  `hearth` varchar(32) NOT NULL DEFAULT '',
  `level` int(11) NOT NULL DEFAULT '0',
  `server` varchar(32) NOT NULL DEFAULT '',
  `region` char(2) NOT NULL DEFAULT '',
  `ilvl` int(10) DEFAULT NULL,
  `talent_points` int(11) NOT NULL DEFAULT '0',
  `class` varchar(32) NOT NULL DEFAULT '',
  `classid` tinyint(1) NOT NULL DEFAULT '0',
  `classEn` varchar(32) NOT NULL DEFAULT '',
  `health` int(11) NOT NULL DEFAULT '0',
  `mana` int(11) NOT NULL DEFAULT '0',
  `power` varchar(32) NOT NULL DEFAULT '',
  `power_type` varchar(32) NOT NULL DEFAULT '',
  `powerType` int(10) NOT NULL DEFAULT '0',
  `str` int(10) NOT NULL DEFAULT '0',
  `agi` int(10) NOT NULL DEFAULT '0',
  `int` int(10) NOT NULL DEFAULT '0',
  `sta` int(10) NOT NULL DEFAULT '0',
  `speedRating` int(10) NOT NULL DEFAULT '0',
  `speedRatingBonus` int(10) NOT NULL DEFAULT '0',
  `critRating` int(10) NOT NULL DEFAULT '0',
  `haste` int(10) NOT NULL DEFAULT '0',
  `hasteRating` int(10) NOT NULL DEFAULT '0',
  `hasteRatingPercent` int(10) NOT NULL DEFAULT '0',
  `masteryRating` int(10) NOT NULL DEFAULT '0',
  `leech` int(10) NOT NULL DEFAULT '0',
  `leechRating` int(10) NOT NULL DEFAULT '0',
  `leechRatingBonus` int(10) NOT NULL DEFAULT '0',
  `versatility` int(10) NOT NULL DEFAULT '0',
  `versatilityDamageDoneBonus` int(10) NOT NULL DEFAULT '0',
  `versatilityHealingDoneBonus` int(10) NOT NULL DEFAULT '0',
  `versatilityDamageTakenBonus` int(10) NOT NULL DEFAULT '0',
  `avoidanceRating` int(10) NOT NULL DEFAULT '0',
  `avoidanceRatingBonus` int(10) NOT NULL DEFAULT '0',
  `spellPen` int(10) NOT NULL DEFAULT '0',
  `spellCrit` int(10) NOT NULL DEFAULT '0',
  `spellCritRating` int(10) NOT NULL DEFAULT '0',
  `mana5` int(10) NOT NULL DEFAULT '0',
  `mana5Combat` int(10) NOT NULL DEFAULT '0',
  `armor` int(10) NOT NULL DEFAULT '0',
  `dodgeRating` int(10) NOT NULL DEFAULT '0',
  `parryRating` int(10) NOT NULL DEFAULT '0',
  `blockRating` int(10) NOT NULL DEFAULT '0',
  `mainHandDmgMin` int(10) NOT NULL DEFAULT '0',
  `mainHandDmgMax` int(10) NOT NULL DEFAULT '0',
  `mainHandSpeed` int(10) NOT NULL DEFAULT '0',
  `mainHandDps` int(10) NOT NULL DEFAULT '0',
  `offHandDmgMin` int(10) NOT NULL DEFAULT '0',
  `offHandDmgMax` int(10) NOT NULL DEFAULT '0',
  `offHandSpeed` int(10) NOT NULL DEFAULT '0',
  `offHandDps` int(10) NOT NULL DEFAULT '0',
  `rangedDmgMin` int(10) NOT NULL DEFAULT '0',
  `rangedDmgMax` int(10) NOT NULL DEFAULT '0',
  `rangedSpeed` int(10) NOT NULL DEFAULT '0',
  `rangedDps` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`member_id`),
  KEY `name` (`name`,`server`),
  KEY `char` (`region`,`server`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_plugin`
##

CREATE TABLE IF NOT EXISTS `renprefix_plugin` (
  `addon_id` int(11) NOT NULL AUTO_INCREMENT,
  `basename` varchar(16) NOT NULL DEFAULT '',
  `parent` varchar(100) DEFAULT NULL,
  `scope` varchar(20) DEFAULT NULL,
  `version` varchar(16) NOT NULL DEFAULT '0',
  `active` int(1) NOT NULL DEFAULT '1',
  `access` int(1) NOT NULL DEFAULT '0',
  `fullname` tinytext NOT NULL,
  `description` mediumtext NOT NULL,
  `credits` mediumtext NOT NULL,
  `icon` varchar(64) NOT NULL DEFAULT '',
  `wrnet_id` int(4) NOT NULL DEFAULT '0',
  `versioncache` tinytext,
  PRIMARY KEY (`addon_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_plugin_config`
##

CREATE TABLE IF NOT EXISTS `renprefix_plugin_config` (
  `addon_id` int(11) NOT NULL DEFAULT '0',
  `id` int(11) UNSIGNED NOT NULL,
  `config_name` varchar(255) DEFAULT NULL,
  `config_value` tinytext,
  `form_type` mediumtext,
  `config_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`,`addon_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_realmstatus`
##

CREATE TABLE IF NOT EXISTS `renprefix_realmstatus` (
  `server_name` varchar(20) NOT NULL DEFAULT '',
  `server_region` varchar(2) NOT NULL DEFAULT '',
  `servertype` varchar(20) NOT NULL DEFAULT '',
  `serverstatus` varchar(20) NOT NULL DEFAULT '',
  `serverpop` varchar(20) NOT NULL DEFAULT '',
  `timestamp` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `server_name` (`server_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_reputation`
##

CREATE TABLE IF NOT EXISTS `renprefix_reputation` (
  `member_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `faction` varchar(32) NOT NULL DEFAULT '',
  `parent` varchar(32) DEFAULT NULL,
  `sort` int(3) DEFAULT NULL,
  `name` varchar(32) NOT NULL DEFAULT '',
  `curr_rep` int(8) DEFAULT NULL,
  `max_rep` int(8) DEFAULT NULL,
  `AtWar` int(11) NOT NULL DEFAULT '0',
  `Standing` varchar(32) DEFAULT '',
  `Description` mediumtext
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_sessions`
##

CREATE TABLE IF NOT EXISTS `renprefix_sessions` (
  `sess_id` varchar(35) DEFAULT NULL,
  `session_id` char(32) NOT NULL DEFAULT '',
  `session_user_id` varchar(5) DEFAULT NULL,
  `session_last_visit` int(11) NOT NULL DEFAULT '0',
  `session_start` int(11) NOT NULL DEFAULT '0',
  `session_time` int(11) NOT NULL DEFAULT '0',
  `session_ip` varchar(40) NOT NULL DEFAULT '',
  `session_browser` varchar(150) NOT NULL DEFAULT '',
  `session_forwarded_for` varchar(255) NOT NULL DEFAULT '',
  `session_page` varchar(255) NOT NULL DEFAULT '',
  `session_viewonline` tinyint(1) NOT NULL DEFAULT '1',
  `session_autologin` tinyint(1) NOT NULL DEFAULT '0',
  `session_admin` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `sess_id` (`sess_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_sessions_keys`
##

CREATE TABLE IF NOT EXISTS `renprefix_sessions_keys` (
  `key_id` char(32) NOT NULL,
  `user_id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `last_ip` varchar(40) NOT NULL,
  `last_login` int(11) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`key_id`,`user_id`),
  KEY `last_login` (`last_login`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_skills`
##

CREATE TABLE IF NOT EXISTS `renprefix_skills` (
  `member_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `skill_type` varchar(32) NOT NULL DEFAULT '',
  `skill_name` varchar(32) NOT NULL DEFAULT '',
  `skill_order` int(11) NOT NULL DEFAULT '0',
  `skill_level` varchar(16) NOT NULL DEFAULT '',
  PRIMARY KEY (`member_id`,`skill_name`),
  KEY `skill_type` (`skill_type`),
  KEY `skill_name` (`skill_name`),
  KEY `skill_order` (`skill_order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_talents`
##

CREATE TABLE IF NOT EXISTS `renprefix_talents` (
  `member_id` int(11) NOT NULL DEFAULT '0',
  `build` tinyint(2) NOT NULL DEFAULT '0',
  `name` varchar(64) NOT NULL DEFAULT '',
  `tree` varchar(64) NOT NULL DEFAULT '',
  `row` tinyint(4) NOT NULL DEFAULT '0',
  `column` tinyint(4) NOT NULL DEFAULT '0',
  `rank` tinyint(4) NOT NULL DEFAULT '0',
  `maxrank` tinyint(4) NOT NULL DEFAULT '0',
  `tooltip` mediumtext NOT NULL,
  `texture` varchar(64) NOT NULL DEFAULT '',
  `talent_id` int(10) NOT NULL,
  PRIMARY KEY (`member_id`,`build`,`tree`,`row`,`column`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_talents_data`
##

CREATE TABLE IF NOT EXISTS `renprefix_talents_data` (
  `talent_id` int(11) NOT NULL DEFAULT '0',
  `talent_num` int(11) NOT NULL DEFAULT '0',
  `tree_order` int(11) NOT NULL DEFAULT '0',
  `class_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(64) NOT NULL DEFAULT '',
  `tree` varchar(1) NOT NULL DEFAULT '',
  `row` tinyint(4) NOT NULL DEFAULT '0',
  `column` tinyint(4) NOT NULL DEFAULT '0',
  `rank` tinyint(4) NOT NULL DEFAULT '0',
  `tooltip` mediumtext NOT NULL,
  `texture` varchar(64) NOT NULL DEFAULT '',
  `isspell` int(1) DEFAULT '0',
  PRIMARY KEY (`rank`,`tree`,`tree_order`,`row`,`column`,`class_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_talenttree`
##

CREATE TABLE IF NOT EXISTS `renprefix_talenttree` (
  `member_id` int(11) NOT NULL DEFAULT '0',
  `build` tinyint(2) NOT NULL DEFAULT '0',
  `tree` varchar(64) NOT NULL DEFAULT '',
  `background` varchar(64) NOT NULL DEFAULT '',
  `order` tinyint(4) NOT NULL DEFAULT '0',
  `pointsspent` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`member_id`,`build`,`tree`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_talenttree_data`
##

CREATE TABLE IF NOT EXISTS `renprefix_talenttree_data` (
  `class_id` int(11) NOT NULL DEFAULT '0',
  `build` tinyint(2) NOT NULL DEFAULT '0',
  `tree` varchar(64) NOT NULL DEFAULT '',
  `tree_num` varchar(64) NOT NULL DEFAULT '',
  `background` varchar(64) NOT NULL DEFAULT '',
  `order` tinyint(4) NOT NULL DEFAULT '0',
  `icon` varchar(64) NOT NULL DEFAULT '',
  `roles` varchar(10) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`class_id`,`build`,`tree`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_talent_builds`
##

CREATE TABLE IF NOT EXISTS `renprefix_talent_builds` (
  `member_id` int(11) NOT NULL DEFAULT '0',
  `build` tinyint(2) NOT NULL DEFAULT '0',
  `tree` varchar(200) NOT NULL DEFAULT '',
  `spec` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`member_id`,`build`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_talent_mastery`
##

CREATE TABLE IF NOT EXISTS `renprefix_talent_mastery` (
  `class_id` int(11) NOT NULL DEFAULT '0',
  `tree` varchar(64) NOT NULL DEFAULT '',
  `tree_num` varchar(64) NOT NULL DEFAULT '',
  `icon` varchar(64) NOT NULL DEFAULT '',
  `name` varchar(64) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `spell_id` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`class_id`,`spell_id`,`tree`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_upload`
##

CREATE TABLE IF NOT EXISTS `renprefix_upload` (
  `rule_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `server` varchar(32) NOT NULL DEFAULT '',
  `region` char(2) NOT NULL DEFAULT '',
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rule_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_user_groups`
##

CREATE TABLE IF NOT EXISTS `renprefix_user_groups` (
  `group_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_type` tinyint(4) NOT NULL DEFAULT '1',
  `group_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `group_desc` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `group_permissions` text,
  `group_rank` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_user_members`
##

CREATE TABLE IF NOT EXISTS `renprefix_user_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usr` varchar(32) DEFAULT NULL,
  `user_display` varchar(100) NOT NULL,
  `pass` varchar(32) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `bnet_id` int(100) DEFAULT NULL,
  `bnet_token` varchar(100) DEFAULT NULL,
  `regIP` varchar(15) DEFAULT NULL,
  `dt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` varchar(25) DEFAULT NULL,
  `user_permissions` text,
  `fname` varchar(30) DEFAULT NULL,
  `lname` varchar(30) DEFAULT NULL,
  `age` varchar(32) DEFAULT NULL,
  `city` varchar(32) DEFAULT NULL,
  `state` varchar(32) DEFAULT NULL,
  `country` varchar(32) DEFAULT NULL,
  `zone` varchar(32) DEFAULT NULL,
  `homepage` varchar(64) DEFAULT NULL,
  `other_guilds` varchar(64) DEFAULT NULL,
  `why` varchar(64) DEFAULT NULL,
  `about` varchar(64) DEFAULT NULL,
  `notes` varchar(64) DEFAULT NULL,
  `last_login` varchar(64) DEFAULT NULL,
  `date_joined` varchar(64) DEFAULT NULL,
  `tmp_mail` varchar(32) DEFAULT NULL,
  `group_id` smallint(6) NOT NULL DEFAULT '1',
  `is_member` int(11) NOT NULL DEFAULT '0',
  `active` int(11) NOT NULL DEFAULT '0',
  `online` int(11) NOT NULL DEFAULT '0',
  `user_lastvisit` int(15) DEFAULT NULL,
  `last_sid` varchar(80) DEFAULT NULL,
  `hash` varchar(32) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `usr` (`usr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## --------------------------------------------------------

##
## Table structure for table `renprefix_webdb_cache`
##

CREATE TABLE IF NOT EXISTS `renprefix_webdb_cache` (
  `item_id` int(11) NOT NULL,
  `texture` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `item_level` int(11) NOT NULL,
  `required_level` int(11) NOT NULL,
  `locale` varchar(4) NOT NULL,
  `timestamp` int(8) NOT NULL,
  `tooltip_html` varchar(255) NOT NULL COMMENT 'passed from web db, not roster parsed tooltip_html',
  `basestats` mediumtext NOT NULL,
  `sockets` varchar(32) DEFAULT NULL,
  `source_id` smallint(8) NOT NULL COMMENT 'source data was collected from',
  PRIMARY KEY (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
