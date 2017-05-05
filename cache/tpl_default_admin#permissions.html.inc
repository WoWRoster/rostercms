<?php global $roster; ?><div class="row cfg-row">
	<div class="col-md-6 col-sm-6">
		<a class="input" href="<?php echo $this->_tpldata['.'][0]['RETURN']; ?>"><span class="ui-icon ui-icon-circle-arrow-w"></span> Return</a>
	</div>
	<div class="col-md-6 col-sm-6">
		<form action="" method="post" id="update">
		<input type="hidden" id="action" name="action" value="<?php echo $this->_tpldata['.'][0]['TYPE']; ?>" />
		<input type="hidden" name="process" value="process" />
		<input type="hidden" name="id" value="<?php echo $this->_tpldata['.'][0]['ID']; ?>" />
		<button type="submit" class="btn btn-default btn-xs"><?php if( isset($roster->locale->act['update']) ) { echo $roster->locale->act['update']; } else { echo '{ TRANSLATE update }'; trigger_error('Missing translation { update }', E_USER_NOTICE); } ?></button>
	</div>
	<div class="col-md-12 col-sm-12 center">
		<div class="tab-navigation" style="height: 30px;display:block;">
			<ul id="perm_page">
				<?php $_type_count = (isset($this->_tpldata['type'])) ?  count($this->_tpldata['type']) : 0;if ($_type_count) {for ($this->_type_i = 0; $this->_type_i < $_type_count; $this->_type_i++){ ?>
					<li class="" id="<?php echo $this->_tpldata['type'][$this->_type_i]['SLUG']; ?>"><?php echo $this->_tpldata['type'][$this->_type_i]['TYPE']; ?></li>
				<?php }} ?>
			</ul>
		</div>
	</div>
</div>
	<?php $_type_count = (isset($this->_tpldata['type'])) ?  count($this->_tpldata['type']) : 0;if ($_type_count) {for ($this->_type_i = 0; $this->_type_i < $_type_count; $this->_type_i++){ ?>
<div class="panel panel-default perm_page" id="<?php echo $this->_tpldata['type'][$this->_type_i]['SLUG']; ?>">
	<div class="panel-heading"><?php echo $this->_tpldata['type'][$this->_type_i]['TYPE']; ?></div>
		<div class="panel-body">
		<?php $_catagory_count = (isset($this->_tpldata['type'][$this->_type_i]['catagory'])) ? count($this->_tpldata['type'][$this->_type_i]['catagory']) : 0;if ($_catagory_count) {for ($this->_catagory_i = 0; $this->_catagory_i < $_catagory_count; $this->_catagory_i++){ ?>
			<div class="panel panel-default">
				<div class="panel-heading"><?php echo $this->_tpldata['type'][$this->_type_i]['catagory'][$this->_catagory_i]['NAME']; ?></div>
					<div class="panel-body">
					<?php $_info_count = (isset($this->_tpldata['type'][$this->_type_i]['catagory'][$this->_catagory_i]['info'])) ? count($this->_tpldata['type'][$this->_type_i]['catagory'][$this->_catagory_i]['info']) : 0;if ($_info_count) {for ($this->_info_i = 0; $this->_info_i < $_info_count; $this->_info_i++){ ?>
					<div class="row cfg-row">
						<div class="col-md-6 col-sm-4"><?php echo $this->_tpldata['type'][$this->_type_i]['catagory'][$this->_catagory_i]['info'][$this->_info_i]['NAME']; ?> - <?php echo $this->_tpldata['type'][$this->_type_i]['catagory'][$this->_catagory_i]['info'][$this->_info_i]['INFO']; ?></div>
						<div class="col-md-6 col-sm-4">						
							<div class="btn-group" data-toggle="buttons">
								<label class="btn btn-default btn-xs" id="rad_config_<?php echo $this->_tpldata['type'][$this->_type_i]['catagory'][$this->_catagory_i]['info'][$this->_info_i]['CFG']; ?>0">
									<input type="radio" id="rad_config_<?php echo $this->_tpldata['type'][$this->_type_i]['catagory'][$this->_catagory_i]['info'][$this->_info_i]['CFG']; ?>0" name="<?php echo $this->_tpldata['type'][$this->_type_i]['catagory'][$this->_catagory_i]['info'][$this->_info_i]['CFG']; ?>" value="0" <?php if ($this->_tpldata['type'][$this->_type_i]['catagory'][$this->_catagory_i]['info'][$this->_info_i]['CFGVALUE'] == 0) {  ?>checked="checked" <?php } ?>/>
									<?php if( isset($roster->locale->act['no']) ) { echo $roster->locale->act['no']; } else { echo '{ TRANSLATE no }'; trigger_error('Missing translation { no }', E_USER_NOTICE); } ?></label>
								<label class="btn btn-default btn-xs" id="rad_config_<?php echo $this->_tpldata['type'][$this->_type_i]['catagory'][$this->_catagory_i]['info'][$this->_info_i]['CFG']; ?>1">
									<input type="radio" id="rad_config_<?php echo $this->_tpldata['type'][$this->_type_i]['catagory'][$this->_catagory_i]['info'][$this->_info_i]['CFG']; ?>1" name="<?php echo $this->_tpldata['type'][$this->_type_i]['catagory'][$this->_catagory_i]['info'][$this->_info_i]['CFG']; ?>" value="1" <?php if ($this->_tpldata['type'][$this->_type_i]['catagory'][$this->_catagory_i]['info'][$this->_info_i]['CFGVALUE'] == 1) {  ?>checked="checked" <?php } ?>/>
									<?php if( isset($roster->locale->act['yes']) ) { echo $roster->locale->act['yes']; } else { echo '{ TRANSLATE yes }'; trigger_error('Missing translation { yes }', E_USER_NOTICE); } ?></label>
							</div>
						</div>
					</div>
					<?php }} ?>
				</div>
			</div>
		<?php }} ?>
		
		</div>
	</div>
	<?php }} ?>
</form>