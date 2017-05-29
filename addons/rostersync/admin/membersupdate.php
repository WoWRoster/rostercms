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

$js = '
var members={};
var count = 0;
var mcount = 0;
var processed = 0;
var nextRequest = 2000;
var t;

	jQuery(document).on(\'click\', \'#getlist\', function (e) {

		jQuery.ajax({
			type: "GET",
			url: "'. makelink('ajax-addon-rostersync-makelist').'",
			beforeSend: function() {
				setTimeout(function() {
				}, 500);
			},
			success: function(data)
			{
				members = jQuery.parseJSON( data );
				count = members.length - 1;
				mcount = members.length;
				jQuery(\'#mcurrent\').html(members[count].name);
				jQuery(\'#mnext\').html(members[count-1].name);
				jQuery(\'#mtotal\').html(mcount);
			},
			error: function(xhr) {
				if (xhr.status != 200){
					jQuery(\'#results\').empty().append(\'Error...\');
				}
			}
		});
	});
	
	jQuery(document).on(\'click\', \'#process\', function (e) {
		nextRequest = 2000;
		process_many();
	});
	
	function process_many()
	{
		if ( members.length > 0 )
		{
			next = members.length - 1;
			$.ajax({
				type: "GET",
				url: "'. makelink('ajax-addon-rostersync-character').'",
				data: members[next],
				dataType: "html",
				async: false,
				success: function(r){
					jQuery(\'#results\').prepend(r);
					processed = processed+1;
					_getper();
					members.splice(next, 1);
					if ( members.length > 0 )
					{
						_updateNext();
						setTimeout(function() {
						}, 500);
					}
					else
					{
						clearTimeout(t);
						return;
					}
				},
			});
			t = setTimeout( process_many, nextRequest );
		}
		else
		{
			clearTimeout(t);
		}
	}
	
	jQuery(document).on(\'click\', \'#STOP\', function (e) {
		myStopFunction();
	});
	
	function myStopFunction() {
		clearTimeout(t);
	}

	jQuery(document).on(\'click\', \'#OneProcess\', function (e) {
		next = members.length - 1;
		$.ajax({
			type: "GET",
			url: "'. makelink('ajax-addon-rostersync-character').'",
			data: members[next],
			dataType: "html",
			async: false,
			success: function(r){
				jQuery(\'#results\').prepend(r);
				processed = processed+1;
				_getper();
				members.splice(next, 1);
				_updateNext();
			},
		});

	});
	
	function _updateNext()
	{
		count = members.length - 1;
		jQuery(\'#mcurrent\').html(members[count].name);
		if ( members.length > 1 )
		{
			jQuery(\'#mnext\').html(members[count-1].name);
		}
		jQuery(\'#mtotal\').html(mcount);
		jQuery(\'#mcomplete\').html(processed);
	}

	function _getper()
	{
		per = (processed / mcount * 100);
		per.toFixed(2);
		jQuery(\'#bar_per\').html(per.toFixed(2));
		jQuery(\'#pro_bar\').css( "width", per.toFixed(2)+"%" );
	}

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

';

roster_add_js($js, 'inline', 'header', false, false);

echo '<div class="page-header">
			<h1>Member update</h1>
		</div>
<div class="row">
	<div class="col-md-3"><button type="submit" id="getlist" class="btn btn-primary btn-block">Get Members</button></div>
	<div class="col-md-3"><button type="submit" id="process" class="btn btn-primary btn-block">process Members</button></div>
	<div class="col-md-3"><button type="submit" id="OneProcess" class="btn btn-primary btn-block">process 1 Member</button></div>
	<div class="col-md-3"><button type="submit" id="STOP" class="btn btn-primary btn-block">Stop</button></div>
</div>';

echo '
<div class="row cfg-row">
	<div class="col-md-12">
		<div class="progress">
		  <div id="pro_bar" class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar"
		  aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:0%">
			<span id="bar_per">0</span>% Complete (success)
		  </div>
		</div>
	</div>
</div>';
echo '
<div class="row cfg-row">
	<div class="col-md-3">Next Player: <div id="mnext"></div></div>
	<div class="col-md-3">Current Player: <div id="mcurrent"></div></div>
	<div class="col-md-3">Total: <div id="mtotal"></div></div>
	<div class="col-md-3">Complete: <div id="mcomplete"></div></div>
</div>';

echo '<div id="results"></div>';

