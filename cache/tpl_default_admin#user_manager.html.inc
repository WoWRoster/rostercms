<?php global $roster; ?><!-- $Id: user_manager.html 2496 2012-05-27 17:43:18Z c.treyce@gmail.com $ -->

<form action="" method="post" id="update">
	<input type="hidden" id="deletehide" name="action" value="" />
	<input type="hidden" name="process" value="process" />
	<div class="config-submit">
		<button type="submit" class="btn btn-default btn-xs" onclick="return confirm('update settings');"><?php if( isset($roster->locale->act['update']) ) { echo $roster->locale->act['update']; } else { echo '{ TRANSLATE update }'; trigger_error('Missing translation { update }', E_USER_NOTICE); } ?></button>
	</div>

	<div class="tier-2-a">
		<div class="tier-2-b">
			<div class="info-text-h">
				<?php echo ((isset($this->_tpldata['.'][0]['L_USER_MANAGER'])) ? $this->_tpldata['.'][0]['L_USER_MANAGER'] : '{ '.ucfirst(strtolower(str_replace('_', ' ', 'USER_MANAGER'))).' }'); ?>
			</div>
		</div>
	</div>

<?php $_user_count = (isset($this->_tpldata['user'])) ?  count($this->_tpldata['user']) : 0;if ($_user_count) {for ($this->_user_i = 0; $this->_user_i < $_user_count; $this->_user_i++){ ?>

	<div class="row cfg-row">
		<div class="col-md-3 col-sm-2"><i class="cfg-row-i icon-flag-1" style="cursor:help;" <?php echo $this->_tpldata['user'][$this->_user_i]['TOOLTIP']; ?>></i><?php echo $this->_tpldata['user'][$this->_user_i]['NAME']; ?></div>
		<div class="col-md-3 col-sm-2"><?php echo $this->_tpldata['user'][$this->_user_i]['ACCESS']; ?></div>
		<div class="col-md-2 col-sm-2">
			<div class="btn-group" data-toggle="buttons">
				<label class="btn btn-default btn-xs" id="rad_config_<?php echo $this->_tpldata['user'][$this->_user_i]['IDC']; ?>_xx">
					<input type="radio" id="rad_config_<?php echo $this->_tpldata['user'][$this->_user_i]['IDC']; ?>" name="config_<?php echo $this->_tpldata['user'][$this->_user_i]['ID']; ?>[active]" value="0" <?php if ($this->_tpldata['user'][$this->_user_i]['ACTIVE'] == 0) {  ?>checked="checked" <?php } ?>/>
					<?php if( isset($roster->locale->act['inactive']) ) { echo $roster->locale->act['inactive']; } else { echo '{ TRANSLATE inactive }'; trigger_error('Missing translation { inactive }', E_USER_NOTICE); } ?></label>
				<label class="btn btn-default btn-xs" id="rad_config_<?php echo $this->_tpldata['user'][$this->_user_i]['IDC']; ?>xxx">
					<input type="radio" id="rad_config_<?php echo $this->_tpldata['user'][$this->_user_i]['IDX']; ?>" name="config_<?php echo $this->_tpldata['user'][$this->_user_i]['ID']; ?>[active]" value="1" <?php if ($this->_tpldata['user'][$this->_user_i]['ACTIVE'] == 1) {  ?>checked="checked" <?php } ?>/>
					<?php if( isset($roster->locale->act['active']) ) { echo $roster->locale->act['active']; } else { echo '{ TRANSLATE active }'; trigger_error('Missing translation { active }', E_USER_NOTICE); } ?></label>
			</div>
		</div>
		<div class="col-md-2 col-sm-2">
			<label for="delete_<?php echo $this->_tpldata['user'][$this->_user_i]['ID']; ?>"> <?php if( isset($roster->locale->act['delete']) ) { echo $roster->locale->act['delete']; } else { echo '{ TRANSLATE delete }'; trigger_error('Missing translation { delete }', E_USER_NOTICE); } ?></label><input type="checkbox" name="delete[]" id="delete_<?php echo $this->_tpldata['user'][$this->_user_i]['ID']; ?>" value="<?php echo $this->_tpldata['user'][$this->_user_i]['ID']; ?>" />
		</div>

		<div class="col-md-2 col-sm-2"><a class="btn btn-default btn-xs" href="<?php echo $this->_tpldata['user'][$this->_user_i]['PERMISS']; ?>"><?php if( isset($roster->locale->act['permissions']) ) { echo $roster->locale->act['permissions']; } else { echo '{ TRANSLATE permissions }'; trigger_error('Missing translation { permissions }', E_USER_NOTICE); } ?></a></div>
	</div>

<?php }} ?>

</form>