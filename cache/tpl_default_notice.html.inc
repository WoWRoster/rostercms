<?php global $roster;if ($this->_tpldata['.'][0]['S_MESSAGES']) {  ?>
<div id="messages">
	<div class="container">
		<?php $_messages_count = (isset($this->_tpldata['messages'])) ?  count($this->_tpldata['messages']) : 0;if ($_messages_count) {for ($this->_messages_i = 0; $this->_messages_i < $_messages_count; $this->_messages_i++){$_item_count = (isset($this->_tpldata['messages'][$this->_messages_i]['item'])) ? count($this->_tpldata['messages'][$this->_messages_i]['item']) : 0;if ($_item_count) {for ($this->_item_i = 0; $this->_item_i < $_item_count; $this->_item_i++){ ?>
		<div class="alert alert-{messages.item.type}">
			<strong><?php echo $this->_tpldata['messages'][$this->_messages_i]['item'][$this->_item_i]['TITLE']; ?></strong><?php echo $this->_tpldata['messages'][$this->_messages_i]['item'][$this->_item_i]['TEXT']; ?>
		</div>
		<?php }}}} ?>
	</div>
</div>
<?php } ?>