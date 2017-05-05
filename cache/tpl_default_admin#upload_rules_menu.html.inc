<?php global $roster; ?><!-- $Id: upload_rules_menu.html 2228 2010-12-08 09:45:57Z c.treyce@gmail.com $ -->

<ul class="tab_menu">
<?php $_upload_rules_menu_count = (isset($this->_tpldata['upload_rules_menu'])) ?  count($this->_tpldata['upload_rules_menu']) : 0;if ($_upload_rules_menu_count) {for ($this->_upload_rules_menu_i = 0; $this->_upload_rules_menu_i < $_upload_rules_menu_count; $this->_upload_rules_menu_i++){ ?>
	<li<?php if ($this->_tpldata['upload_rules_menu'][$this->_upload_rules_menu_i]['SELECTED']) {  ?> class="selected"<?php } ?>><a href="<?php echo $this->_tpldata['upload_rules_menu'][$this->_upload_rules_menu_i]['LINK']; ?>"><?php echo $this->_tpldata['upload_rules_menu'][$this->_upload_rules_menu_i]['NAME']; ?></a></li>
<?php }} ?>
</ul>