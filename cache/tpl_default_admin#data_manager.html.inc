<?php global $roster; ?><!-- $Id: data_manager.html 2493 2012-05-27 06:01:28Z c.treyce@gmail.com $ --><?php if ($this->_tpldata['.'][0]['S_DM_SELECT']) {  ?>
<div class="config-submit">
	<form class="slim" action="<?php echo $this->_tpldata['.'][0]['U_MAKELINK']; ?>" name="data_select" method="post">
		<select name="guild" onchange="window.location.href=this.options[this.selectedIndex].value;">
<?php $_dm_select_group_count = (isset($this->_tpldata['dm_select_group'])) ?  count($this->_tpldata['dm_select_group']) : 0;if ($_dm_select_group_count) {for ($this->_dm_select_group_i = 0; $this->_dm_select_group_i < $_dm_select_group_count; $this->_dm_select_group_i++){ ?>
			<optgroup label="<?php echo $this->_tpldata['dm_select_group'][$this->_dm_select_group_i]['U_VALUE']; ?>">
<?php $_dm_select_row_count = (isset($this->_tpldata['dm_select_group'][$this->_dm_select_group_i]['dm_select_row'])) ? count($this->_tpldata['dm_select_group'][$this->_dm_select_group_i]['dm_select_row']) : 0;if ($_dm_select_row_count) {for ($this->_dm_select_row_i = 0; $this->_dm_select_row_i < $_dm_select_row_count; $this->_dm_select_row_i++){ ?>
				<option value="<?php echo $this->_tpldata['dm_select_group'][$this->_dm_select_group_i]['dm_select_row'][$this->_dm_select_row_i]['U_VALUE']; ?>"<?php if ($this->_tpldata['dm_select_group'][$this->_dm_select_group_i]['dm_select_row'][$this->_dm_select_row_i]['S_SELECTED']) {  ?> selected="selected"<?php } ?>><?php echo $this->_tpldata['dm_select_group'][$this->_dm_select_group_i]['dm_select_row'][$this->_dm_select_row_i]['TEXT']; ?></option>
<?php }} ?>
			</optgroup>
<?php }} ?>
		</select>
	</form>
</div>
<?php }if ($this->_tpldata['.'][0]['S_RESPONSE']) { if ($this->_tpldata['.'][0]['S_RESPONSE_ERROR']) {  ?>

		<div class="tier-2-title" style="cursor:pointer;" onclick="showHide('msgbox_data_error','msgboximg_data_error','<?php echo $this->_tpldata['.'][0]['THEME_PATH']; ?>/images/button_open.png','<?php echo $this->_tpldata['.'][0]['THEME_PATH']; ?>/images/button_close.png');">
			<div class="right">
				<form method="post" action="<?php echo $this->_tpldata['.'][0]['U_MAKELINK']; ?>" name="post">
					<input type="hidden" name="data" value="<?php echo $this->_tpldata['.'][0]['RESPONSE_ERROR_LOG']; ?>" />
					<input type="hidden" name="send_file" value="error_log" />
					<input type="submit" name="download" value="<?php if( isset($roster->locale->act['save_error_log']) ) { echo $roster->locale->act['save_error_log']; } else { echo '{ TRANSLATE save_error_log }'; trigger_error('Missing translation { save_error_log }', E_USER_NOTICE); } ?>" />
				</form>
			</div>
			<?php if( isset($roster->locale->act['update_errors']) ) { echo $roster->locale->act['update_errors']; } else { echo '{ TRANSLATE update_errors }'; trigger_error('Missing translation { update_errors }', E_USER_NOTICE); } ?>
			<div class="toggle">
				<img id="msgboximg_data_error" src="<?php echo $this->_tpldata['.'][0]['THEME_PATH']; ?>/images/button_open.png" alt="" />
			</div>
		</div>

		<div class="border_color sredborder" style="background:black;height:300px;width:100%;overflow:auto;display:none;" id="msgbox_data_error">
			<?php echo $this->_tpldata['.'][0]['RESPONSE_ERROR']; ?>
		</div>

<?php } ?>


			<div class="right">
				<form method="post" action="<?php echo $this->_tpldata['.'][0]['U_MAKELINK']; ?>" name="post">
					<input type="hidden" name="data" value="<?php echo $this->_tpldata['.'][0]['RESPONSE_POST']; ?>" />
					<input type="hidden" name="send_file" value="update_log" />
					<input type="submit" name="download" value="<?php if( isset($roster->locale->act['save_update_log']) ) { echo $roster->locale->act['save_update_log']; } else { echo '{ TRANSLATE save_update_log }'; trigger_error('Missing translation { save_update_log }', E_USER_NOTICE); } ?>" />
				</form>
			</div>
			<?php if( isset($roster->locale->act['update_log']) ) { echo $roster->locale->act['update_log']; } else { echo '{ TRANSLATE update_log }'; trigger_error('Missing translation { update_log }', E_USER_NOTICE); } ?>


		<div class="border_color syellowborder"  style="background:black;height:300px;width:100%;overflow:auto;text-align:left;font-size:10px;">
			<?php echo $this->_tpldata['.'][0]['RESPONSE']; ?>
		</div>


<?php } ?>
		<div class="text">
			<span class="ui-icon ui-icon-help" style="cursor:help;" <?php echo ((isset($this->_tpldata['.'][0]['L_CLEAN_TIP'])) ? $this->_tpldata['.'][0]['L_CLEAN_TIP'] : '{ '.ucfirst(strtolower(str_replace('_', ' ', 'CLEAN_TIP'))).' }'); ?>></span>
			<?php if( isset($roster->locale->act['clean']) ) { echo $roster->locale->act['clean']; } else { echo '{ TRANSLATE clean }'; trigger_error('Missing translation { clean }', E_USER_NOTICE); } ?>

			<form action="<?php echo $this->_tpldata['.'][0]['U_ACTION']; ?>" method="post" id="clean" style="display:inline;">
				<input type="hidden" name="action" value="clean" />
				<input type="hidden" name="process" value="process" />
				<input type="submit" value="Go" />
			</form>
		</div>

<br />

<form action="<?php echo $this->_tpldata['.'][0]['U_ACTION']; ?>" method="post" id="delete">
	<input type="hidden" id="deletehide" name="action" value="" />
	<input type="hidden" name="process" value="process" />
<?php if ($this->_tpldata['.'][0]['S_DATA']) {  ?>
<div class="tier-2-a">
	<div class="tier-2-b">
		<div class="tier-2-title">
			<div class="right">
				<?php echo $this->_tpldata['.'][0]['PREV'];echo $this->_tpldata['.'][0]['LISTING'];echo $this->_tpldata['.'][0]['NEXT']; ?>
			</div>
			<?php if( isset($roster->locale->act['delete']) ) { echo $roster->locale->act['delete']; } else { echo '{ TRANSLATE delete }'; trigger_error('Missing translation { delete }', E_USER_NOTICE); } ?>
		</div>

		<table cellspacing="0" class="table-striped" style="width: 100%;">
			<thead>
				<tr>
					<th class="membersHeader"><?php if( isset($roster->locale->act['name']) ) { echo $roster->locale->act['name']; } else { echo '{ TRANSLATE name }'; trigger_error('Missing translation { name }', E_USER_NOTICE); } ?></th>
					<th class="membersHeader"><?php if( isset($roster->locale->act['server']) ) { echo $roster->locale->act['server']; } else { echo '{ TRANSLATE server }'; trigger_error('Missing translation { server }', E_USER_NOTICE); } ?></th>
					<th class="membersHeader"><?php if( isset($roster->locale->act['region']) ) { echo $roster->locale->act['region']; } else { echo '{ TRANSLATE region }'; trigger_error('Missing translation { region }', E_USER_NOTICE); } ?></th>
					<th class="membersHeader"><?php if( isset($roster->locale->act['class']) ) { echo $roster->locale->act['class']; } else { echo '{ TRANSLATE class }'; trigger_error('Missing translation { class }', E_USER_NOTICE); } ?></th>
					<th class="membersHeader"><?php if( isset($roster->locale->act['level']) ) { echo $roster->locale->act['level']; } else { echo '{ TRANSLATE level }'; trigger_error('Missing translation { level }', E_USER_NOTICE); } ?></th>
					<th class="membersHeader">&nbsp;</th>
					<th class="membersHeader"><label for="massdel">&nbsp;</label><input type="checkbox" class="checkall" rel="massdel" /></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="7" class="membersRow" style="text-align: center;">
						<button type="submit" class="input" style="float: right;"><?php if( isset($roster->locale->act['delete_checked']) ) { echo $roster->locale->act['delete_checked']; } else { echo '{ TRANSLATE delete_checked }'; trigger_error('Missing translation { delete_checked }', E_USER_NOTICE); } ?></button>
						<button type="submit" class="input" style="float: left;" onclick="return confirm('<?php if( isset($roster->locale->act['delete_guild_confirm']) ) { echo $roster->locale->act['delete_guild_confirm']; } else { echo '{ TRANSLATE delete_guild_confirm }'; trigger_error('Missing translation { delete_guild_confirm }', E_USER_NOTICE); } ?>') &amp;&amp; setvalue('deletehide','delguild_<?php echo $this->_tpldata['.'][0]['U_GUILD_ID']; ?>');"><?php if( isset($roster->locale->act['delete_guild']) ) { echo $roster->locale->act['delete_guild']; } else { echo '{ TRANSLATE delete_guild }'; trigger_error('Missing translation { delete_guild }', E_USER_NOTICE); } ?></button>
					</td>
				</tr>
			</tfoot>
			<tbody>
	<?php $_data_list_count = (isset($this->_tpldata['data_list'])) ?  count($this->_tpldata['data_list']) : 0;if ($_data_list_count) {for ($this->_data_list_i = 0; $this->_data_list_i < $_data_list_count; $this->_data_list_i++){ ?>
				<tr>
					<td class="membersRow<?php echo $this->_tpldata['data_list'][$this->_data_list_i]['ROW_CLASS']; ?>"><?php echo $this->_tpldata['data_list'][$this->_data_list_i]['NAME']; ?></td>
					<td class="membersRow<?php echo $this->_tpldata['data_list'][$this->_data_list_i]['ROW_CLASS']; ?>"><?php echo $this->_tpldata['data_list'][$this->_data_list_i]['SERVER']; ?></td>
					<td class="membersRow<?php echo $this->_tpldata['data_list'][$this->_data_list_i]['ROW_CLASS']; ?>"><?php echo $this->_tpldata['data_list'][$this->_data_list_i]['REGION']; ?></td>
					<td class="membersRow<?php echo $this->_tpldata['data_list'][$this->_data_list_i]['ROW_CLASS']; ?>"><?php echo $this->_tpldata['data_list'][$this->_data_list_i]['CLASS']; ?></td>
					<td class="membersRow<?php echo $this->_tpldata['data_list'][$this->_data_list_i]['ROW_CLASS']; ?>"><?php echo $this->_tpldata['data_list'][$this->_data_list_i]['LEVEL']; ?></td>
					<td class="membersRow<?php echo $this->_tpldata['data_list'][$this->_data_list_i]['ROW_CLASS']; ?>"><button type="submit" class="input" onclick="setvalue('deletehide','del_<?php echo $this->_tpldata['data_list'][$this->_data_list_i]['ID']; ?>');"><?php if( isset($roster->locale->act['delete']) ) { echo $roster->locale->act['delete']; } else { echo '{ TRANSLATE delete }'; trigger_error('Missing translation { delete }', E_USER_NOTICE); } ?></button></td>
					<td class="membersRow<?php echo $this->_tpldata['data_list'][$this->_data_list_i]['ROW_CLASS']; ?>"><label for="massdel_<?php echo $this->_tpldata['data_list'][$this->_data_list_i]['ID']; ?>">&nbsp;</label><input type="checkbox" name="massdel[<?php echo $this->_tpldata['data_list'][$this->_data_list_i]['ID']; ?>]" id="massdel_<?php echo $this->_tpldata['data_list'][$this->_data_list_i]['ID']; ?>" value="1" /></td>
				</tr>
	<?php }} ?>
			</tbody>
		</table>

		<div class="tier-2-title">
			<?php echo $this->_tpldata['.'][0]['PREV'];echo $this->_tpldata['.'][0]['LISTING'];echo $this->_tpldata['.'][0]['NEXT']; ?>
		</div>
	</div>
</div>

</form>

<?php } else { ?>
	<h1>No Data</h1>
<?php if ($this->_tpldata['.'][0]['U_GUILD_ID'] != '') {  ?>
	<button type="submit" class="input" onclick="return confirm('<?php if( isset($roster->locale->act['delete_guild_confirm']) ) { echo $roster->locale->act['delete_guild_confirm']; } else { echo '{ TRANSLATE delete_guild_confirm }'; trigger_error('Missing translation { delete_guild_confirm }', E_USER_NOTICE); } ?>') &amp;&amp; setvalue('deletehide','delguild_<?php echo $this->_tpldata['.'][0]['U_GUILD_ID']; ?>');"><?php if( isset($roster->locale->act['delete_guild']) ) { echo $roster->locale->act['delete_guild']; } else { echo '{ TRANSLATE delete_guild }'; trigger_error('Missing translation { delete_guild }', E_USER_NOTICE); } ?></button>
	<?php } ?>
</form>
<?php } ?>