<?php global $roster; ?><!-- $Id: armory_data.html 2616 2012-09-27 16:51:19Z ulminia@gmail.com $ -->


<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title"><?php if( isset($roster->locale->act['adata_update_talents']) ) { echo $roster->locale->act['adata_update_talents']; } else { echo '{ TRANSLATE adata_update_talents }'; trigger_error('Missing translation { adata_update_talents }', E_USER_NOTICE); } ?></h3>
			<form action="<?php echo $this->_tpldata['classes'][$this->_classes_i]['UPDATELINK']; ?>" method="post" id="update">
				<input type="hidden" name="parse" value="ALL" />
				<input type="hidden" name="process" value="process" />
				<button type="submit" class="input btn-danger"><?php if( isset($roster->locale->act['update']) ) { echo $roster->locale->act['update']; } else { echo '{ TRANSLATE update }'; trigger_error('Missing translation { update }', E_USER_NOTICE); } ?></button>
			</form>
	</div>
	<div class="panel-body">
		<div class="container">
		<?php $_classes_count = (isset($this->_tpldata['classes'])) ?  count($this->_tpldata['classes']) : 0;if ($_classes_count) {for ($this->_classes_i = 0; $this->_classes_i < $_classes_count; $this->_classes_i++){ ?>
			<div class="row">
				<div class="col-sm-6"><?php echo $this->_tpldata['classes'][$this->_classes_i]['NAME']; ?></div>
				<div class="col-sm-6"><?php echo $this->_tpldata['classes'][$this->_classes_i]['ROWS']; ?></div>
			</div>
		<?php }} ?>
		</div>
	</div>
</div>

<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title"><?php if( isset($roster->locale->act['items']) ) { echo $roster->locale->act['items']; } else { echo '{ TRANSLATE items }'; trigger_error('Missing translation { items }', E_USER_NOTICE); } ?></h3>
			<form action="" method="post" id="update">
				<input type="hidden" name="truncate" value="true" />
				<input type="hidden" name="process" value="process" />
				<button type="submit" class="input btn-danger"><?php if( isset($roster->locale->act['installer_purge']) ) { echo $roster->locale->act['installer_purge']; } else { echo '{ TRANSLATE installer_purge }'; trigger_error('Missing translation { installer_purge }', E_USER_NOTICE); } ?></button>
			</form>
	</div>
	<div class="panel-body">
		<div class="container">
		<?php $_cache_count = (isset($this->_tpldata['cache'])) ?  count($this->_tpldata['cache']) : 0;if ($_cache_count) {for ($this->_cache_i = 0; $this->_cache_i < $_cache_count; $this->_cache_i++){ ?>
			<div class="row">
				<div class="col-sm-6"><?php echo $this->_tpldata['cache'][$this->_cache_i]['NAME']; ?></div>
				<div class="col-sm-6"><?php echo $this->_tpldata['cache'][$this->_cache_i]['ROWS']; ?></div>
			</div>
		<?php }} ?>
		</div>
	</div>
</div>

<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title">Api Usage</h3>
			<form action="" method="post" id="update">
				<input type="hidden" name="clear" value="true" />
				<input type="hidden" name="process" value="process" />
				<button type="submit" class="input btn-danger"><?php if( isset($roster->locale->act['clear']) ) { echo $roster->locale->act['clear']; } else { echo '{ TRANSLATE clear }'; trigger_error('Missing translation { clear }', E_USER_NOTICE); } ?></button>
			</form>
	</div>
	<div class="panel-body">
		<table class="table table-inverse">
			<thead>
				<tr>
					<th>Type</th>
					<th>Request</th>
					<th>Percente</th>
				</tr>
			</thead>
			<tbody>
			<?php $_apiusage_count = (isset($this->_tpldata['apiusage'])) ?  count($this->_tpldata['apiusage']) : 0;if ($_apiusage_count) {for ($this->_apiusage_i = 0; $this->_apiusage_i < $_apiusage_count; $this->_apiusage_i++){ ?>
				<tr>
					<th colspan="3" ><?php echo $this->_tpldata['apiusage'][$this->_apiusage_i]['DATE']; ?></th>
				</tr>
				<?php $_type_count = (isset($this->_tpldata['apiusage'][$this->_apiusage_i]['type'])) ? count($this->_tpldata['apiusage'][$this->_apiusage_i]['type']) : 0;if ($_type_count) {for ($this->_type_i = 0; $this->_type_i < $_type_count; $this->_type_i++){ ?>
				<tr>
					<td >&nbsp;&nbsp;<?php echo $this->_tpldata['apiusage'][$this->_apiusage_i]['type'][$this->_type_i]['TYPE']; ?></td>
					<td ><?php echo $this->_tpldata['apiusage'][$this->_apiusage_i]['type'][$this->_type_i]['REQ']; ?></td>
					<td ><?php echo $this->_tpldata['apiusage'][$this->_apiusage_i]['type'][$this->_type_i]['PERCENT']; ?></td>
				</tr>
				<?php }}}} ?>
			</tbody>
		</table>
	</div>
</div>