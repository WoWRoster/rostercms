<?php
//no hooks
/*
add_action('info_menu_after','_my_char_menu',10);
function _my_char_menu()
{
	global $roster;
	$roster->tpl->assign_block_vars('tabs',array(
				'NAME'     => 'raid',
				'VALUE'    => 'raid',
				'SELECTED' => false
				)
			);
}

add_filter('info_char_template','_my_char_template',10,1);
function _my_char_template()
{
	$template = '<div id="raid" style="display:none;">
			<div class="row">
				<div class="col-md-12"><h1>Raid Progress</h1></div>
			</div>
			</div>';
	
	return $template;
}
*/
?>