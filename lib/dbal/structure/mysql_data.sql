#
# MySQL Roster Data File
#
# * $Id: mysql_data.sql 2602 2012-09-01 05:15:30Z ulminia@gmail.com $
#
# --------------------------------------------------------
### Data

# --------------------------------------------------------
### Master Values

INSERT INTO `renprefix_config` VALUES (3, 'roster_dbver', '7', 'display', 'master');
INSERT INTO `renprefix_config` VALUES (4, 'version', '', 'display', 'master');
INSERT INTO `renprefix_config` VALUES (5, 'startpage', 'main_conf', 'display', 'master');
INSERT INTO `renprefix_config` VALUES (6, 'versioncache', '', 'hidden', 'master');
INSERT INTO `renprefix_config` VALUES (99, 'css_js_query_string', 'lod68q', 'hidden', 'master');

# --------------------------------------------------------
### Permission settings
INSERT INTO `renprefix_permissions` VALUES ('', 'roster', '00', 'core', 'roster_cp', 'roster_cp_desc' , 'roster_cp');
INSERT INTO `renprefix_permissions` VALUES ('', 'roster', '00', 'core', 'gp_update', 'gp_update_desc' , 'gp_update');
INSERT INTO `renprefix_permissions` VALUES ('', 'roster', '00', 'core', 'cp_update', 'cp_update_desc' , 'cp_update');
INSERT INTO `renprefix_permissions` VALUES ('', 'roster', '00', 'core', 'lua_update', 'lua_update_desc' , 'lua_update');
INSERT INTO `renprefix_permissions` VALUES ('', 'roster', '00', 'core', 'debug_window', 'debug_window_desc' , 'debug_window');

# --------------------------------------------------------
### User groups
INSERT INTO `renprefix_user_groups` VALUES ('1', '1', 'Admin', 'Admin user group', '{"roster_cp": "1","gp_update": "1","cp_update": "1","lua_update": "1","debug_window":"1"}', '0');
INSERT INTO `renprefix_user_groups` VALUES ('2', '1', 'Public', 'general public access group auth use only', NULL, '0');

# --------------------------------------------------------
### menu data 
INSERT INTO `renprefix_config` VALUES (110, 'main_conf', NULL, 'blockframe', 'menu');
INSERT INTO `renprefix_config` VALUES (120, 'defaults_conf', NULL, 'blockframe', 'menu');
INSERT INTO `renprefix_config` VALUES (130, 'debug_conf', NULL, 'blockframe', 'menu');
INSERT INTO `renprefix_config` VALUES (150, 'realmstatus_conf', NULL, 'page{1', 'menu');
INSERT INTO `renprefix_config` VALUES (160, 'data_links', NULL, 'blockframe', 'menu');
INSERT INTO `renprefix_config` VALUES (170, 'update_access', NULL, 'blockframe', 'menu');
INSERT INTO `renprefix_config` VALUES (180, 'documentation', 'http://www.wowroster.net/MediaWiki', 'newlink', 'menu');
INSERT INTO `renprefix_config` VALUES (190, 'acc_session', 'NULL', 'blockframe', 'menu');

# --------------------------------------------------------
### main_conf 
INSERT INTO `renprefix_config` VALUES (1010, 'minCPver', '1.0.0', 'text{10|10', 'main_conf');
INSERT INTO `renprefix_config` VALUES (1020, 'minGPver', '1.0.0', 'text{10|10', 'main_conf');
INSERT INTO `renprefix_config` VALUES (1040, 'locale', 'enUS', 'function{rosterLangValue', 'main_conf');
INSERT INTO `renprefix_config` VALUES (1050, 'default_page', 'guild-main', 'function{pageNames', 'main_conf');
INSERT INTO `renprefix_config` VALUES (1055, 'external_auth', 'roster', 'function{externalAuth', 'main_conf');
INSERT INTO `renprefix_config` VALUES (1056, 'default_group', '2', 'function{defaultgroup', 'main_conf');
INSERT INTO `renprefix_config` VALUES (1060, 'website_address', 'http://localhost', 'text{128|60', 'main_conf');
INSERT INTO `renprefix_config` VALUES (1085, 'interface_url', 'http://localhost/static/', 'text{128|60', 'main_conf');
INSERT INTO `renprefix_config` VALUES (1090, 'img_suffix', 'png', 'select{jpg^jpg|png^png|gif^gif', 'main_conf');
INSERT INTO `renprefix_config` VALUES (1190, 'enforce_rules', '1', 'select{Never^0|All LUA Updates^1|CP Updates^2|Guild Updates^3', 'main_conf');
INSERT INTO `renprefix_config` VALUES (1095, 'alt_img_suffix', 'png', 'select{jpg^jpg|png^png|gif^gif', 'main_conf');
INSERT INTO `renprefix_config` VALUES (1100, 'img_url', 'img/', 'text{128|60', 'main_conf');
INSERT INTO `renprefix_config` VALUES (1110, 'timezone', 'PST', 'text{10|10', 'main_conf');
INSERT INTO `renprefix_config` VALUES (1120, 'localtimeoffset', '0', 'select{-12^-12|-11^-11|-10^-10|-9^-9|-8^-8|-7^-7|-6^-6|-5^-5|-4^-4|-3.5^-3.5|-3^-3|-2^-2|-1^-1|0^0|+1^1|+2^2|+3^3|+3.5^3.5|+4^4|+4.5^4.5|+5^5|+5.5^5.5|+6^6|+6.5^6.5|+7^7|+8^8|+9^9|+9.5^9.5|+10^10|+11^11|+12^12|+13^13', 'main_conf');
INSERT INTO `renprefix_config` VALUES (1140, 'use_update_triggers', '1', 'radio{on^1|off^0', 'main_conf');
INSERT INTO `renprefix_config` VALUES (1150, 'check_updates', '24', 'select{Do Not check^0|Once a Day^24|Once a Week^168|Once a Month^720', 'main_conf');

# --------------------------------------------------------
### defaults_conf 
INSERT INTO `renprefix_config` VALUES (2000, 'default_name', 'WoWRoster', 'text{50|50', 'defaults_conf');
INSERT INTO `renprefix_config` VALUES (2010, 'default_desc', 'THE original Roster for World of Warcraft', 'text{255|60', 'defaults_conf');
INSERT INTO `renprefix_config` VALUES (2020, 'alt_type', 'alt', 'text{30|30', 'defaults_conf');
INSERT INTO `renprefix_config` VALUES (2030, 'alt_location', 'note', 'select{Player Note^note|Officer Note^officer_note|Guild Rank Number^guild_rank|Guild Title^guild_title', 'defaults_conf');
INSERT INTO `renprefix_config` VALUES (2040, 'menu_title', 'WoWRoster', 'text{30|30', 'defaults_conf');

# --------------------------------------------------------
### debug_conf  
INSERT INTO `renprefix_config` VALUES (3010, 'debug_mode', '1', 'radio{extended^2|on^1|off^0', 'debug_conf');
INSERT INTO `renprefix_config` VALUES (3020, 'debug_window', '1', 'radio{on^1|off^0', 'debug_conf');
INSERT INTO `renprefix_config` VALUES (3030, 'sql_window', '1', 'radio{extended^2|on^1|off^0', 'debug_conf');
INSERT INTO `renprefix_config` VALUES (3040, 'processtime', '1', 'radio{on^1|off^0', 'debug_conf');

# --------------------------------------------------------
### realmstatus_conf 
INSERT INTO `renprefix_config` VALUES (8010, 'rs_top', NULL, 'blockframe', 'realmstatus_conf');
INSERT INTO `renprefix_config` VALUES (8020, 'rs_wide', NULL, 'page{3', 'realmstatus_conf');
INSERT INTO `renprefix_config` VALUES (8030, 'rs_left', NULL, 'blockframe', 'rs_wide');
INSERT INTO `renprefix_config` VALUES (8040, 'rs_middle', NULL, 'blockframe', 'rs_wide');
INSERT INTO `renprefix_config` VALUES (8050, 'rs_right', NULL, 'blockframe', 'rs_wide');
INSERT INTO `renprefix_config` VALUES (8060, 'rs_display', 'full', 'radio{off^0|image^image|text^text', 'rs_top');
INSERT INTO `renprefix_config` VALUES (8070, 'rs_timer', '10', 'text{5|5', 'rs_top');
INSERT INTO `renprefix_config` VALUES (8080, 'rs_font_server', 'GREY.TTF', 'function{fontFiles', 'rs_left');
INSERT INTO `renprefix_config` VALUES (8090, 'rs_size_server', '20', 'text{5|5', 'rs_left');
INSERT INTO `renprefix_config` VALUES (8100, 'rs_color_server', '#FFFFFF', 'color', 'rs_left');
INSERT INTO `renprefix_config` VALUES (8110, 'rs_color_shadow', '#000000', 'color', 'rs_left');
INSERT INTO `renprefix_config` VALUES (8120, 'rs_font_type', 'visitor.ttf', 'function{fontFiles', 'rs_middle');
INSERT INTO `renprefix_config` VALUES (8130, 'rs_size_type', '10', 'text{5|5', 'rs_middle');
INSERT INTO `renprefix_config` VALUES (8140, 'rs_color_rppvp', '#EBDBA2', 'color', 'rs_middle');
INSERT INTO `renprefix_config` VALUES (8150, 'rs_color_pve', '#EBDBA2', 'color', 'rs_middle');
INSERT INTO `renprefix_config` VALUES (8160, 'rs_color_pvp', '#CC3333', 'color', 'rs_middle');
INSERT INTO `renprefix_config` VALUES (8170, 'rs_color_rp', '#33CC33', 'color', 'rs_middle');
INSERT INTO `renprefix_config` VALUES (8180, 'rs_color_unknown', '#860D02', 'color', 'rs_middle');
INSERT INTO `renprefix_config` VALUES (8190, 'rs_font_pop', 'visitor.ttf', 'function{fontFiles', 'rs_right');
INSERT INTO `renprefix_config` VALUES (8200, 'rs_size_pop', '10', 'text{5|5', 'rs_right');
INSERT INTO `renprefix_config` VALUES (8210, 'rs_color_low', '#33CC33', 'color', 'rs_right');
INSERT INTO `renprefix_config` VALUES (8220, 'rs_color_medium', '#EBDBA2', 'color', 'rs_right');
INSERT INTO `renprefix_config` VALUES (8230, 'rs_color_high', '#CC3333', 'color', 'rs_right');
INSERT INTO `renprefix_config` VALUES (8240, 'rs_color_max', '#CC3333', 'color', 'rs_right');
INSERT INTO `renprefix_config` VALUES (8250, 'rs_color_error', '#646464', 'color', 'rs_right');
INSERT INTO `renprefix_config` VALUES (8265, 'rs_color_offline', '#646464', 'color', 'rs_right');
INSERT INTO `renprefix_config` VALUES (8270, 'rs_color_full', '#CC3333', 'color', 'rs_right');
INSERT INTO `renprefix_config` VALUES (8280, 'rs_color_recommended', '#33CC33', 'color', 'rs_right');

# --------------------------------------------------------
### update_access
INSERT INTO `renprefix_config` VALUES (9000, 'authenticated_user', '1', 'radio{enable^1|disable^0', 'update_access');
INSERT INTO `renprefix_config` VALUES (9001, 'api_key_private', '3kMykjYN6qFrFEqkgyfgbMpjUX7phpjZ', 'text{64|40', 'update_access');
INSERT INTO `renprefix_config` VALUES (9002, 'api_key_public', 'hmtxw3wkj7h4hku972fd37ms299ne6vv', 'text{64|40', 'update_access');
INSERT INTO `renprefix_config` VALUES (9003, 'api_url_region', 'US', 'select{US^US|Europe^EU|Korea^KR|Taiwan^TW|China^CN', 'update_access');
INSERT INTO `renprefix_config` VALUES (9004, 'api_url_locale', 'en_US', 'select{Americas English (US)^en_US|Americas Espanol (AL)^es_MX|Americas Portugues (AL)^pt_BR|Europe Deutsch^de_DE|Europe English^en_GB|Europe Espanol^es_ES|Europe Francais^fr_FR|Europe Italiano^it_IT|Europe Portugues^pt_PT|Europe Pyccknn^ru_RU|Korea^ko_KR|Taiwan^zh_TW|China^zh_CN|Southeast Asia^en_US', 'update_access');
INSERT INTO `renprefix_config` VALUES (9006, 'use_api_onupdate', '0', 'select{Yes^1|No^0', 'update_access');
INSERT INTO `renprefix_config` VALUES (9005, 'update_inst', '1', 'radio{on^1|off^0', 'update_access');

# --------------------------------------------------------
### acc_session
INSERT INTO `renprefix_config` VALUES (9100, 'sess_time', '15', 'text{30|4', 'acc_session');
INSERT INTO `renprefix_config` VALUES (9101, 'save_login', '1', 'radio{on^1|off^0', 'acc_session');

# --------------------------------------------------------
### theme_conf
INSERT INTO `renprefix_config` VALUES (4000, 'seo_url', '0', 'radio{on^1|off^0', 'theme_conf');
INSERT INTO `renprefix_config` VALUES (4010, 'local_cache', '0', 'radio{on^1|off^0', 'main_conf');
INSERT INTO `renprefix_config` VALUES (4020, 'use_temp_tables', '0', 'radio{on^1|off^0', 'main_conf');
INSERT INTO `renprefix_config` VALUES (4030, 'preprocess_js', '0', 'radio{on^1|off^0', 'theme_conf');
INSERT INTO `renprefix_config` VALUES (4040, 'preprocess_css', '0', 'radio{on^1|off^0', 'theme_conf');
INSERT INTO `renprefix_config` VALUES (4050, 'minifyjs', '0', 'radio{on^1|off^0', 'theme_conf');
INSERT INTO `renprefix_config` VALUES (4060, 'theme', 'default', 'function{templateList', 'theme_conf');
INSERT INTO `renprefix_config` VALUES (4070, 'logo', '', 'text{128|60', 'theme_conf');
INSERT INTO `renprefix_config` VALUES (4080, 'roster_bg', '', 'text{128|60', 'theme_conf');
INSERT INTO `renprefix_config` VALUES (4090, 'header_locale', '1', 'radio{on^1|off^0', 'theme_conf');
INSERT INTO `renprefix_config` VALUES (4100, 'header_login', '1', 'radio{on^1|off^0', 'theme_conf');
INSERT INTO `renprefix_config` VALUES (4110, 'header_search', '1', 'radio{on^1|off^0', 'theme_conf');
INSERT INTO `renprefix_config` VALUES (4120, 'bootstrap', 'custom', 'function{ThemeList', 'theme_conf');

# --------------------------------------------------------
### Menu table entries
INSERT INTO `renprefix_menu` VALUES (1, 'util', 'b1:b2:b3');
INSERT INTO `renprefix_menu` VALUES (2, 'realm', '');
INSERT INTO `renprefix_menu` VALUES (3, 'guild', '');
INSERT INTO `renprefix_menu` VALUES (4, 'char', '');
INSERT INTO `renprefix_menu` VALUES (5, 'user', '');

# --------------------------------------------------------
### Menu Button entries
INSERT INTO `renprefix_menu_button` VALUES (1, 0, 'menu_search', 'util', 'search', 'inv_misc_spyglass_02');
INSERT INTO `renprefix_menu_button` VALUES (2, 0, 'menu_roster_cp', 'util', 'rostercp', 'inv_misc_gear_02');
INSERT INTO `renprefix_menu_button` VALUES (3, 0, 'menu_roster_ucp', 'util', 'ucp', 'inv_misc_gear_07');

# --------------------------------------------------------
### Users
