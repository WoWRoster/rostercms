<?php global $roster; ?><!-- $Id: upload_rules.html 2282 2011-02-24 05:59:18Z c.treyce@gmail.com $ -->

<div class="panel panel-default">
  <div class="panel-body"><span class="ui-icon ui-icon-help" style="cursor:help;" <?php echo ((isset($this->_tpldata['.'][0]['L_ENFORCE_RULES_HELP'])) ? $this->_tpldata['.'][0]['L_ENFORCE_RULES_HELP'] : '{ '.ucfirst(strtolower(str_replace('_', ' ', 'ENFORCE_RULES_HELP'))).' }'); ?>></span>
		<?php if( isset($roster->locale->act['enforce_rules']) ) { echo $roster->locale->act['enforce_rules']; } else { echo '{ TRANSLATE enforce_rules }'; trigger_error('Missing translation { enforce_rules }', E_USER_NOTICE); } ?>
		<form action="<?php echo $this->_tpldata['.'][0]['U_MAKELINK']; ?>" method="post" id="enforce" style="display:inline;">
			<input type="hidden" name="action" value="enforce" />
			<input type="hidden" name="process" value="process" />
			<select name="enforce">
				<option value="0"<?php if ($this->_tpldata['.'][0]['S_ENFORCE_RULES'] == '0') {  ?> selected="selected"<?php } ?>><?php if( isset($roster->locale->act['enforce_rules_never']) ) { echo $roster->locale->act['enforce_rules_never']; } else { echo '{ TRANSLATE enforce_rules_never }'; trigger_error('Missing translation { enforce_rules_never }', E_USER_NOTICE); } ?></option>
				<option value="1"<?php if ($this->_tpldata['.'][0]['S_ENFORCE_RULES'] == '1') {  ?> selected="selected"<?php } ?>><?php if( isset($roster->locale->act['enforce_rules_all']) ) { echo $roster->locale->act['enforce_rules_all']; } else { echo '{ TRANSLATE enforce_rules_all }'; trigger_error('Missing translation { enforce_rules_all }', E_USER_NOTICE); } ?></option>
				<option value="2"<?php if ($this->_tpldata['.'][0]['S_ENFORCE_RULES'] == '2') {  ?> selected="selected"<?php } ?>><?php if( isset($roster->locale->act['enforce_rules_cp']) ) { echo $roster->locale->act['enforce_rules_cp']; } else { echo '{ TRANSLATE enforce_rules_cp }'; trigger_error('Missing translation { enforce_rules_cp }', E_USER_NOTICE); } ?></option>
				<option value="3"<?php if ($this->_tpldata['.'][0]['S_ENFORCE_RULES'] == '3') {  ?> selected="selected"<?php } ?>><?php if( isset($roster->locale->act['enforce_rules_gp']) ) { echo $roster->locale->act['enforce_rules_gp']; } else { echo '{ TRANSLATE enforce_rules_gp }'; trigger_error('Missing translation { enforce_rules_gp }', E_USER_NOTICE); } ?></option>
			</select>
			<input type="submit" value="Go" />
		</form>
	</div>
</div>

<div class="panel panel-default">
  <div class="panel-heading"><?php if( isset($roster->locale->act['disallow']) ) { echo $roster->locale->act['disallow']; } else { echo '{ TRANSLATE disallow }'; trigger_error('Missing translation { disallow }', E_USER_NOTICE); } ?></div>
  <div class="panel-body"><form action="<?php echo $this->_tpldata['.'][0]['U_MAKELINK']; ?>" method="post" id="deny">
			<input type="hidden" id="denyhide" name="action" value="" />
			<input type="hidden" name="process" value="process" />
			<input type="hidden" name="block" value="disallow" />

			<table class="border_frame" cellpadding="0" cellspacing="1" style="width:100%;">
				<thead>
					<tr>
						<th class="membersHeader" <?php echo ((isset($this->_tpldata['.'][0]['L_NAME_TIP'])) ? $this->_tpldata['.'][0]['L_NAME_TIP'] : '{ '.ucfirst(strtolower(str_replace('_', ' ', 'NAME_TIP'))).' }'); ?>><?php if( isset($roster->locale->act['name']) ) { echo $roster->locale->act['name']; } else { echo '{ TRANSLATE name }'; trigger_error('Missing translation { name }', E_USER_NOTICE); } ?></th>
						<th class="membersHeader" <?php echo ((isset($this->_tpldata['.'][0]['L_SERVER_TIP'])) ? $this->_tpldata['.'][0]['L_SERVER_TIP'] : '{ '.ucfirst(strtolower(str_replace('_', ' ', 'SERVER_TIP'))).' }'); ?>><?php if( isset($roster->locale->act['server']) ) { echo $roster->locale->act['server']; } else { echo '{ TRANSLATE server }'; trigger_error('Missing translation { server }', E_USER_NOTICE); } ?></th>
						<th class="membersHeader" <?php echo ((isset($this->_tpldata['.'][0]['L_REGION_TIP'])) ? $this->_tpldata['.'][0]['L_REGION_TIP'] : '{ '.ucfirst(strtolower(str_replace('_', ' ', 'REGION_TIP'))).' }'); ?>><?php if( isset($roster->locale->act['region']) ) { echo $roster->locale->act['region']; } else { echo '{ TRANSLATE region }'; trigger_error('Missing translation { region }', E_USER_NOTICE); } ?></th>
						<th class="membersHeaderRight">&nbsp;</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td class="membersRow2"><input type="text" name="value" value="" /></td>
						<td class="membersRow2"><input type="text" name="server" value="" /></td>
						<td class="membersRow2"><input type="text" name="region" value="" maxlength="2" /></td>
						<td class="membersRowRight2"><button type="submit" class="input" onclick="setvalue('denyhide','add');"><?php if( isset($roster->locale->act['add']) ) { echo $roster->locale->act['add']; } else { echo '{ TRANSLATE add }'; trigger_error('Missing translation { add }', E_USER_NOTICE); } ?></button></td>
					</tr>
				</tfoot>
				<tbody>
				<?php $_deny_list_count = (isset($this->_tpldata['deny_list'])) ?  count($this->_tpldata['deny_list']) : 0;if ($_deny_list_count) {for ($this->_deny_list_i = 0; $this->_deny_list_i < $_deny_list_count; $this->_deny_list_i++){ ?>
					<tr>
						<td class="membersRow1"><?php echo $this->_tpldata['deny_list'][$this->_deny_list_i]['NAME']; ?></td>
						<td class="membersRow1"><?php echo $this->_tpldata['deny_list'][$this->_deny_list_i]['SERVER']; ?></td>
						<td class="membersRow1"><?php echo $this->_tpldata['deny_list'][$this->_deny_list_i]['REGION']; ?></td>
						<td class="membersRowRight1"><button type="submit" class="input" onclick="setvalue('denyhide','del_<?php echo $this->_tpldata['deny_list'][$this->_deny_list_i]['ID']; ?>');"><?php if( isset($roster->locale->act['delete']) ) { echo $roster->locale->act['delete']; } else { echo '{ TRANSLATE delete }'; trigger_error('Missing translation { delete }', E_USER_NOTICE); } ?></button></td>
					</tr>
				<?php }} ?>
				</tbody>
			</table>

		</form>

	</div>
</div>


<div class="panel panel-default">
  <div class="panel-heading"><?php if( isset($roster->locale->act['allow']) ) { echo $roster->locale->act['allow']; } else { echo '{ TRANSLATE allow }'; trigger_error('Missing translation { allow }', E_USER_NOTICE); } ?></div>
  <div class="panel-body"><form action="<?php echo $this->_tpldata['.'][0]['U_MAKELINK']; ?>" method="post" id="allow">
			<input type="hidden" id="allowhide" name="action" value="" />
			<input type="hidden" name="process" value="process" />
			<input type="hidden" name="block" value="allow" />

			<table class="border_frame" cellpadding="0" cellspacing="1" style="width:100%;">
				<thead>
					<tr><?php if ($this->_tpldata['.'][0]['MODE'] == 'guild') {  ?>
						<th class="membersHeader"><?php if( isset($roster->locale->act['default']) ) { echo $roster->locale->act['default']; } else { echo '{ TRANSLATE default }'; trigger_error('Missing translation { default }', E_USER_NOTICE); } ?></th><?php } ?>
						<th class="membersHeader" <?php echo ((isset($this->_tpldata['.'][0]['L_NAME_TIP'])) ? $this->_tpldata['.'][0]['L_NAME_TIP'] : '{ '.ucfirst(strtolower(str_replace('_', ' ', 'NAME_TIP'))).' }'); ?>><?php if( isset($roster->locale->act['name']) ) { echo $roster->locale->act['name']; } else { echo '{ TRANSLATE name }'; trigger_error('Missing translation { name }', E_USER_NOTICE); } ?></th>
						<th class="membersHeader" <?php echo ((isset($this->_tpldata['.'][0]['L_SERVER_TIP'])) ? $this->_tpldata['.'][0]['L_SERVER_TIP'] : '{ '.ucfirst(strtolower(str_replace('_', ' ', 'SERVER_TIP'))).' }'); ?>><?php if( isset($roster->locale->act['server']) ) { echo $roster->locale->act['server']; } else { echo '{ TRANSLATE server }'; trigger_error('Missing translation { server }', E_USER_NOTICE); } ?></th>
						<th class="membersHeader" <?php echo ((isset($this->_tpldata['.'][0]['L_REGION_TIP'])) ? $this->_tpldata['.'][0]['L_REGION_TIP'] : '{ '.ucfirst(strtolower(str_replace('_', ' ', 'REGION_TIP'))).' }'); ?>><?php if( isset($roster->locale->act['region']) ) { echo $roster->locale->act['region']; } else { echo '{ TRANSLATE region }'; trigger_error('Missing translation { region }', E_USER_NOTICE); } ?></th>
						<th class="membersHeaderRight">&nbsp;</th>
					</tr>
				</thead>
				<tfoot>
					<tr><?php if ($this->_tpldata['.'][0]['MODE'] == 'guild') {  ?>
						<td class="membersRow2" style="text-align:center;"><label for="defaultchk">&nbsp;</label>
							<input type="checkbox" name="defaultchk" id="defaultchk" value="1" <?php if ($this->_tpldata['.'][0]['S_EXISTING_DATA'] != '1') {  ?> checked="checked"<?php } ?> /></td><?php } ?>
						<td class="membersRow2"><input type="text" name="value" value="" /></td>
						<td class="membersRow2"><input type="text" name="server" value="" /></td>
						<td class="membersRow2"><input type="text" name="region" value="" maxlength="2" /></td>
						<td class="membersRowRight2"><button type="submit" class="input" onclick="setvalue('allowhide','add');"><?php if( isset($roster->locale->act['add']) ) { echo $roster->locale->act['add']; } else { echo '{ TRANSLATE add }'; trigger_error('Missing translation { add }', E_USER_NOTICE); } ?></button></td>
					</tr>
				</tfoot>
				<tbody>
<?php $_allow_list_count = (isset($this->_tpldata['allow_list'])) ?  count($this->_tpldata['allow_list']) : 0;if ($_allow_list_count) {for ($this->_allow_list_i = 0; $this->_allow_list_i < $_allow_list_count; $this->_allow_list_i++){ ?>
					<tr>
						<?php if ($this->_tpldata['.'][0]['MODE'] == 'guild') {  ?>
						<td class="membersRow1" style="text-align:center;"><?php if ($this->_tpldata['allow_list'][$this->_allow_list_i]['DEFAULT']) {  ?><button disabled="disabled"><i class="fa fa-dot-circle-o" aria-hidden="true"></i></button>
							<?php } else { ?><button type="submit" class="button_hide" onclick="setvalue('allowhide','default_<?php echo $this->_tpldata['allow_list'][$this->_allow_list_i]['ID']; ?>');"><i class="fa fa-circle-o" aria-hidden="true"></i></button><?php } ?></td>
						<?php } ?>
						<td class="membersRow1"><?php echo $this->_tpldata['allow_list'][$this->_allow_list_i]['NAME']; ?></td>
						<td class="membersRow1"><?php echo $this->_tpldata['allow_list'][$this->_allow_list_i]['SERVER']; ?></td>
						<td class="membersRow1"><?php echo $this->_tpldata['allow_list'][$this->_allow_list_i]['REGION']; ?></td>
						<td class="membersRowRight1"><button type="submit" class="input" onclick="setvalue('allowhide','del_<?php echo $this->_tpldata['allow_list'][$this->_allow_list_i]['ID']; ?>');"><?php if( isset($roster->locale->act['delete']) ) { echo $roster->locale->act['delete']; } else { echo '{ TRANSLATE delete }'; trigger_error('Missing translation { delete }', E_USER_NOTICE); } ?></button></td>
					</tr>
<?php }} ?>
				</tbody>
			</table>

		</form>
	</div>
</div>


<div class="panel panel-default">
  <div class="panel-heading"><?php if( isset($roster->locale->act['pagebar_uploadrules']) ) { echo $roster->locale->act['pagebar_uploadrules']; } else { echo '{ TRANSLATE pagebar_uploadrules }'; trigger_error('Missing translation { pagebar_uploadrules }', E_USER_NOTICE); } ?></div>
  <div class="panel-body"><?php if( isset($roster->locale->act['upload_rules_help']) ) { echo $roster->locale->act['upload_rules_help']; } else { echo '{ TRANSLATE upload_rules_help }'; trigger_error('Missing translation { upload_rules_help }', E_USER_NOTICE); } ?>
	</div>
</div>