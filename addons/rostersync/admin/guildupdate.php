<?php
if ( !defined('IN_ROSTER') )
{
    exit('Detected invalid access to this file!');
}
/**
 * Make our menu from the config api
 */
// ----[ Set the tablename and create the config class ]----
include(ROSTER_LIB . 'config.lib.php');
$config = new roster_config( $roster->db->table('addon_config'), '`addon_id` = "' . $addon['addon_id'] . '"','config_',false );
// ----[ Get configuration data ]---------------------------
$config->getConfigData();

// ----[ Build the page items using lib functions ]---------
$menu .= $config->buildConfigMenu('rostercp-addon-' . $addon['basename']);

function add_title($title)
{
	$title .=' - Guild Update';
	return $title;
}
add_filter('roster_cp_title', 'add_title',20,1);
$js = '
var members={};
var count = 0;
var mcount = 0;
var processed = 0;
var nextRequest = 2000;
var t;

	jQuery(document).on(\'click\', \'#updateguild\', function (e) {
		
		$.ajax({
			type: "GET",
			url: "'. makelink('ajax-addon-rostersync-guildupdate').'",
			data: {
				server: "'.$roster->data['server'].'",
				guildname: "'.$roster->data['guild_name'].'",
				region: "'.$roster->data['region'].'",
				guild_id: "'.$roster->data['guild_id'].'",
			},
			dataType: "html",
			async: false,
			success: function(r){
				jQuery(\'#update_log\').prepend(r);
			},
		});

	});


';

roster_add_js($js, 'inline', 'header', false, false);


echo '<div class="row">
	<div class="col-md-12"><button type="submit" id="updateguild" class="btn btn-primary btn-block">update guild</button></div>
</div>';

echo '<div class="row">
	<div class="col-md-12">
	<div class="border_color syellowborder" id="update_log" style="background:black;height:500px;width:100%;overflow:auto;text-align:left;font-size:12px;margin: 5px;border-color: #c77e19;color:#fff;">
	</div>
	
	</div>
</div>';