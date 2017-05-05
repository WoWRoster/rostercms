<?php global $roster;if ($this->_tpldata['.'][0]['GPTYPE'] == "") { $_groups_count = (isset($this->_tpldata['groups'])) ?  count($this->_tpldata['groups']) : 0;if ($_groups_count) {for ($this->_groups_i = 0; $this->_groups_i < $_groups_count; $this->_groups_i++){ ?>
	<div class="row cfg-row">
		<div class="col-md-3 col-sm-2"><strong><?php echo $this->_tpldata['groups'][$this->_groups_i]['NAME']; ?></strong></div>
		<div class="col-md-3 col-sm-2"><?php echo $this->_tpldata['groups'][$this->_groups_i]['MEM']; ?></div>
		<div class="col-md-3 col-sm-2"><a href="<?php echo $this->_tpldata['groups'][$this->_groups_i]['DELURL']; ?>">Delete</a> <a class="btn btn-default btn-xs" href="<?php echo $this->_tpldata['groups'][$this->_groups_i]['PERMISS']; ?>"><?php if( isset($roster->locale->act['permissions']) ) { echo $roster->locale->act['permissions']; } else { echo '{ TRANSLATE permissions }'; trigger_error('Missing translation { permissions }', E_USER_NOTICE); } ?></a></div>
	</div>
	<?php }} ?>
	<br>
	<a href="<?php echo $this->_tpldata['.'][0]['ADDGROUP']; ?>">Add Group</a>
<?php }if ($this->_tpldata['.'][0]['GPTYPE'] == "add") {  ?>
<form name="add" method="post" enctype="multipart/form-data" action="<?php echo $this->_tpldata['.'][0]['ADDGROUPF']; ?>">
	<div class="row cfg-row">
		<div class="col-md-3 col-sm-2">
			<span class="ui-icon ui-icon-help" style="cursor:help;"></span>
			<label for="name">Group Name</label>
		</div>
		<div class="col-md-3 col-sm-2">
			<input type="text" name="name" value="" id="name" size="45"/>
		</div>
	</div>
	<div class="row cfg-row">
		<div class="col-md-3 col-sm-2">
			<span class="ui-icon ui-icon-help" style="cursor:help;"></span>
			<label for="desc">Group Description</label>
			</div>
		<div class="col-md-3 col-sm-2">
			<input type="text" name="desc" value="" id="desc" size="45"/>
		</div>
	</div>
	<div class="row cfg-row">
		<input type="submit" value="Add Group" class="btn btn-default btn-xs" >
		<input type="hidden" name="process" value="addgroup">
	</div>
</form>
<?php } ?>