<?php global $roster; ?><!-- $Id: menu_login.html 2488 2012-05-27 02:43:48Z c.treyce@gmail.com $ -->

<div class="menu-login">
<?php if ($this->_tpldata['.'][0]['S_LOGIN_MESSAGE']) {  ?>
	<div class="menu-login-message">
		<?php echo ((isset($this->_tpldata['.'][0]['L_LOGIN_MESSAGE'])) ? $this->_tpldata['.'][0]['L_LOGIN_MESSAGE'] : '{ '.ucfirst(strtolower(str_replace('_', ' ', 'LOGIN_MESSAGE'))).' }'); ?>
<?php if ($this->_tpldata['.'][0]['U_LOGIN'] != 0) {  ?>
		<form class="inline slim" name="roster_logout" action="<?php echo $this->_tpldata['.'][0]['U_LOGIN_ACTION']; ?>" method="post" enctype="multipart/form-data">
			<input type="hidden" name="logout" value="1" />
			<button type="submit"><?php if( isset($roster->locale->act['logout']) ) { echo $roster->locale->act['logout']; } else { echo '{ TRANSLATE logout }'; trigger_error('Missing translation { logout }', E_USER_NOTICE); } ?></button>
	  </form>
<?php } ?>
	</div>
<?php }if ($this->_tpldata['.'][0]['U_LOGIN'] == 0) {  ?>
	<div class="control-group">
		<button type="submit" id="bnetlogins" class="btn btn-primary btn-block">
		Log In with Battle.net
		</button>
	</div>
<?php } ?>
</div>