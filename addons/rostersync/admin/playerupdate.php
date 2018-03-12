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
	$title .=' - Player Update';
	return $title;
}

add_filter('roster_cp_title', 'add_title',20,1);

roster_add_js($addon['url'].'js/select2.js','module');
roster_add_css($addon['url'].'select2.css','module');

$js = '
	jQuery(document).on(\'click\', \'#updateplayer\', function (e) {
		
		//alert(jQuery(\'#members\').val());
		var member = jQuery(\'#members\').val();
		
		$.ajax({
			type: "GET",
			url: "'. makelink('ajax-addon-rostersync-character').'",
			data: {
				value: member,
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
	jQuery(document).on(\'click\', \'#logbutton\', function (e) {
		var $this = $(this);
		var member = $this.data("member");
		$(\'#\'+member+\'\').toggle(\'fast\');
	});
	
	jQuery(document).on(\'click\', \'[data-dismiss]\', function (e) {
		var $this = $(this);
		if ($this.data(\'dismiss\') == "modal" )
		{
			var member = $this.data("member");
			$(\'#\'+member+\'\').toggle(\'fast\');
		}
	});
	$(document).ready(function() {
            $("#members").select2();   
        });


';

roster_add_js($js, 'inline', 'header', false, false);

$query = "SELECT * FROM `" . $roster->db->table('members') . "` ORDER BY `member_id` DESC;";
$results = $roster->db->query($query);
$chars = array();
while( $row = $roster->db->fetch($results) )
{
	$chars[] = array(
		'value' => $row['member_id'].':'.$row['name'].':'.$row['server'],
		'name' => $row['name'].'-'.$row['server'],	
	);
}
//d($chars);
$input_field .= '<select id="members" name="player"  class="select2-offscreen" style="width:400px">' . "\n";
$select_one = 1;
foreach( $chars as $c => $v)
{
	$input_field .= '  <option value="' . $v['value'] . '" selected="selected">' . $v['name'] . '</option>' . "\n";
}
$input_field .= '</select>';

echo $input_field;
					
echo '<div class="row">
	<div class="col-md-12"><button type="submit" id="updateplayer" class="btn btn-primary btn-block">update guild</button></div>
</div>';

echo '<div class="row">
	<div class="col-md-12">
	<div class="border_color syellowborder" id="update_log" style="background:black;height:500px;width:100%;overflow:auto;text-align:left;font-size:12px;margin: 5px;border-color: #c77e19;color:#fff;">
	</div>
	
	</div>
</div>';
