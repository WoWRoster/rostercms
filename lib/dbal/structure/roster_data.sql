-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 04, 2017 at 07:19 PM
-- Server version: 5.6.21
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `rostercms`
--

-- --------------------------------------------------------

--
-- Table structure for table `roster_config`
--

CREATE TABLE `roster_config` (
  `id` int(11) UNSIGNED NOT NULL,
  `config_name` varchar(255) DEFAULT NULL,
  `config_value` tinytext,
  `form_type` mediumtext,
  `config_type` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `roster_config`
--

INSERT INTO `roster_config` (`id`, `config_name`, `config_value`, `form_type`, `config_type`) VALUES
(3, 'roster_dbver', '6', 'display', 'master'),
(4, 'version', '2.9.9.1122', 'display', 'master'),
(5, 'startpage', 'main_conf', 'display', 'master'),
(6, 'versioncache', 'a:4:{s:9:\"timestamp\";i:1493865235;s:10:\"ver_latest\";s:5:\"2.3.0\";s:8:\"ver_info\";s:13:\"2.3.0 Release\";s:8:\"ver_date\";s:10:\"1347206563\";}', 'hidden', 'master'),
(99, 'css_js_query_string', 'zas03jN', 'hidden', 'master'),
/* menu data */
(110, 'main_conf', NULL, 'blockframe', 'menu'),
(120, 'defaults_conf', NULL, 'blockframe', 'menu'),
(130, 'debug_conf', NULL, 'blockframe', 'menu'),
(140, 'display_conf', NULL, 'blockframe', 'menu'),
(150, 'realmstatus_conf', NULL, 'page{1', 'menu'),
(160, 'data_links', NULL, 'blockframe', 'menu'),
(170, 'update_access', NULL, 'blockframe', 'menu'),
(180, 'documentation', 'http://www.wowroster.net/MediaWiki', 'newlink', 'menu'),
(190, 'acc_session', 'NULL', 'blockframe', 'menu'),
/* main_conf */
(1010, 'minCPver', '1.0.0', 'text{10|10', 'main_conf'),
(1020, 'minGPver', '1.0.0', 'text{10|10', 'main_conf'),
(1040, 'locale', 'enUS', 'function{rosterLangValue', 'main_conf'),
(1050, 'default_page', 'guild-main', 'function{pageNames', 'main_conf'),
(1055, 'external_auth', 'roster', 'function{externalAuth', 'main_conf'),
(1056, 'default_group', '2', 'function{defaultgroup', 'main_conf'),
(1060, 'website_address', 'http://localhost', 'text{128|60', 'main_conf'),
(1085, 'interface_url', 'http://localhost/static/', 'text{128|60', 'main_conf'),
(1090, 'img_suffix', 'png', 'select{jpg^jpg|png^png|gif^gif', 'main_conf'),
(1190, 'enforce_rules', '1', 'select{Never^0|All LUA Updates^1|CP Updates^2|Guild Updates^3', 'main_conf'),
(1095, 'alt_img_suffix', 'png', 'select{jpg^jpg|png^png|gif^gif', 'main_conf'),
(1100, 'img_url', 'img/', 'text{128|60', 'main_conf'),
(1110, 'timezone', 'PST', 'text{10|10', 'main_conf'),
(1120, 'localtimeoffset', '0', 'select{-12^-12|-11^-11|-10^-10|-9^-9|-8^-8|-7^-7|-6^-6|-5^-5|-4^-4|-3.5^-3.5|-3^-3|-2^-2|-1^-1|0^0|+1^1|+2^2|+3^3|+3.5^3.5|+4^4|+4.5^4.5|+5^5|+5.5^5.5|+6^6|+6.5^6.5|+7^7|+8^8|+9^9|+9.5^9.5|+10^10|+11^11|+12^12|+13^13', 'main_conf'),
(1140, 'use_update_triggers', '1', 'radio{on^1|off^0', 'main_conf'),
(1150, 'check_updates', '24', 'select{Do Not check^0|Once a Day^24|Once a Week^168|Once a Month^720', 'main_conf'),
/* defaults_conf */
(2000, 'default_name', 'WoWRoster', 'text{50|50', 'defaults_conf'),
(2020, 'default_desc', 'THE original Roster for World of Warcraft', 'text{255|60', 'defaults_conf'),
(2040, 'alt_type', 'alt', 'text{30|30', 'defaults_conf'),
(2050, 'alt_location', 'note', 'select{Player Note^note|Officer Note^officer_note|Guild Rank Number^guild_rank|Guild Title^guild_title', 'defaults_conf'),
/* debug_conf */
(1001, 'debug_mode', '1', 'radio{extended^2|on^1|off^0', 'debug_conf'),
(1002, 'sql_window', '1', 'radio{extended^2|on^1|off^0', 'debug_conf'),
(5050, 'processtime', '1', 'radio{on^1|off^0', 'debug_conf'),
/* display_conf */
/* realmstatus_conf */
(8010, 'rs_top', NULL, 'blockframe', 'realmstatus_conf'),
(8020, 'rs_wide', NULL, 'page{3', 'realmstatus_conf'),
(8030, 'rs_left', NULL, 'blockframe', 'rs_wide'),
(8040, 'rs_middle', NULL, 'blockframe', 'rs_wide'),
(8050, 'rs_right', NULL, 'blockframe', 'rs_wide'),
(8100, 'rs_display', 'full', 'radio{off^0|image^image|text^text', 'rs_top'),
(8120, 'rs_timer', '10', 'text{5|5', 'rs_top'),
(8200, 'rs_font_server', 'GREY.TTF', 'function{fontFiles', 'rs_left'),
(8210, 'rs_size_server', '20', 'text{5|5', 'rs_left'),
(8220, 'rs_color_server', '#FFFFFF', 'color', 'rs_left'),
(8230, 'rs_color_shadow', '#000000', 'color', 'rs_left'),
(8300, 'rs_font_type', 'visitor.ttf', 'function{fontFiles', 'rs_middle'),
(8310, 'rs_size_type', '10', 'text{5|5', 'rs_middle'),
(8320, 'rs_color_rppvp', '#EBDBA2', 'color', 'rs_middle'),
(8330, 'rs_color_pve', '#EBDBA2', 'color', 'rs_middle'),
(8340, 'rs_color_pvp', '#CC3333', 'color', 'rs_middle'),
(8350, 'rs_color_rp', '#33CC33', 'color', 'rs_middle'),
(8360, 'rs_color_unknown', '#860D02', 'color', 'rs_middle'),
(8400, 'rs_font_pop', 'visitor.ttf', 'function{fontFiles', 'rs_right'),
(8410, 'rs_size_pop', '10', 'text{5|5', 'rs_right'),
(8420, 'rs_color_low', '#33CC33', 'color', 'rs_right'),
(8430, 'rs_color_medium', '#EBDBA2', 'color', 'rs_right'),
(8440, 'rs_color_high', '#CC3333', 'color', 'rs_right'),
(8450, 'rs_color_max', '#CC3333', 'color', 'rs_right'),
(8460, 'rs_color_error', '#646464', 'color', 'rs_right'),
(8465, 'rs_color_offline', '#646464', 'color', 'rs_right'),
(8470, 'rs_color_full', '#CC3333', 'color', 'rs_right'),
(8480, 'rs_color_recommended', '#33CC33', 'color', 'rs_right'),
/* data_links */
/* update_access */
(10000, 'authenticated_user', '1', 'radio{enable^1|disable^0', 'update_access'),
(10001, 'api_key_private', '3kMykjYN6qFrFEqkgyfgbMpjUX7phpjZ', 'text{64|40', 'update_access'),
(10002, 'api_key_public', 'hmtxw3wkj7h4hku972fd37ms299ne6vv', 'text{64|40', 'update_access'),
(10003, 'api_url_region', 'US', 'select{US^US|Europe^EU|Korea^KR|Taiwan^TW|China^CN', 'update_access'),
(10004, 'api_url_locale', 'en_US', 'select{Americas English (US)^en_US|Americas Espanol (AL)^es_MX|Americas Portugues (AL)^pt_BR|Europe Deutsch^de_DE|Europe English^en_GB|Europe Espanol^es_ES|Europe Francais^fr_FR|Europe Italiano^it_IT|Europe Portugues^pt_PT|Europe Pyccknn^ru_RU|Korea^ko_KR|Taiwan^zh_TW|China^zh_CN|Southeast Asia^en_US', 'update_access'),
(10006, 'use_api_onupdate', '0', 'select{Yes^1|No^0', 'update_access'),
(10005, 'update_inst', '1', 'radio{on^1|off^0', 'update_access'),
/* acc_session */
(1900, 'sess_time', '15', 'text{30|4', 'acc_session'),
(1910, 'save_login', '1', 'radio{on^1|off^0', 'acc_session'),
/* theme_conf */
(1160, 'seo_url', '0', 'radio{on^1|off^0', 'theme_conf'),
(1170, 'local_cache', '0', 'radio{on^1|off^0', 'main_conf'),
(1180, 'use_temp_tables', '0', 'radio{on^1|off^0', 'main_conf'),
(1181, 'preprocess_js', '0', 'radio{on^1|off^0', 'theme_conf'),
(1182, 'preprocess_css', '0', 'radio{on^1|off^0', 'theme_conf'),
(5000, 'theme', 'default', 'function{templateList', 'theme_conf'),
(5020, 'logo', '', 'text{128|60', 'theme_conf'),
(5025, 'roster_bg', '', 'text{128|60', 'theme_conf'),
(5031, 'header_locale', '1', 'radio{on^1|off^0', 'theme_conf'),
(5032, 'header_login', '1', 'radio{on^1|off^0', 'theme_conf'),
(5033, 'header_search', '1', 'radio{on^1|off^0', 'theme_conf'),
(5001, 'bootstrap', 'default', 'function{ThemeList', 'theme_conf');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `roster_config`
--
ALTER TABLE `roster_config`
  ADD PRIMARY KEY (`id`);
COMMIT;
