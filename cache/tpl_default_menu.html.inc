<?php global $roster; ?><!-- $Id: menu.html 2631 2014-08-21 17:54:35Z ulminia@gmail.com $ --><?php  do_action('roster_before_menu'); if ($this->_tpldata['.'][0]['SHOW_MENU']) {  ?>
<nav class="navbar navbar-inverse navigation-clean-button">
    <div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="#"><?php echo $this->_tpldata['.'][0]['MENU_TITLE']; ?></a>
		</div>
		<div id="navbar" class="navbar-collapse collapse">
			<ul class="nav navbar-nav">

				<?php if ($this->_tpldata['.'][0]['T_BAR_USER_ILI']) {  ?>
				<li class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo $this->_tpldata['.'][0]['T_BAR_USER_MSG']; ?><span class="caret"></span></a>
					<ul class="dropdown-menu">
						<?php $_menu_button_section_count = (isset($this->_tpldata['menu_button_section'])) ?  count($this->_tpldata['menu_button_section']) : 0;if ($_menu_button_section_count) {for ($this->_menu_button_section_i = 0; $this->_menu_button_section_i < $_menu_button_section_count; $this->_menu_button_section_i++){$_menu_buttons_count = (isset($this->_tpldata['menu_button_section'][$this->_menu_button_section_i]['menu_buttons'])) ? count($this->_tpldata['menu_button_section'][$this->_menu_button_section_i]['menu_buttons']) : 0;if ($_menu_buttons_count) {for ($this->_menu_buttons_i = 0; $this->_menu_buttons_i < $_menu_buttons_count; $this->_menu_buttons_i++){if ($this->_tpldata['menu_button_section'][$this->_menu_button_section_i]['menu_buttons'][$this->_menu_buttons_i]['SCOPE'] == 'user') {  ?>
						<li class="smenuitem">
							<a href="<?php echo $this->_tpldata['menu_button_section'][$this->_menu_button_section_i]['menu_buttons'][$this->_menu_buttons_i]['U_LINK']; ?>" >
								<div>
									<div id="container">
									<img src="<?php echo $this->_tpldata['menu_button_section'][$this->_menu_button_section_i]['menu_buttons'][$this->_menu_buttons_i]['ICON']; ?>"  border=0 style="vertical-align:middle;" alt="" />
									<?php echo $this->_tpldata['menu_button_section'][$this->_menu_button_section_i]['menu_buttons'][$this->_menu_buttons_i]['NAME']; ?></div>
								</div>
							</a>
						</li>
						<?php }}}}} ?>
					</ul>
				</li>
				
				<li>
					<form name="roster_logout" action="<?php echo $this->_tpldata['.'][0]['U_LOGIN_ACTION']; ?>" style="padding: 12px;" method="post" enctype="multipart/form-data">
						<input type="hidden" name="logout" value="1" />
						<button class="btn btn-primary btn-xs" type="submit"><?php if( isset($roster->locale->act['logout']) ) { echo $roster->locale->act['logout']; } else { echo '{ TRANSLATE logout }'; trigger_error('Missing translation { logout }', E_USER_NOTICE); } ?></button>
					</form>
				</li>
				<?php } else { ?>
				<li>
					<div style="padding: 8px;">
						<button type="submit" onclick="openWin()" class="btn btn-primary btn-block">Log In with Battle.net</button>
					</div>
				</li>
				<?php }if ($this->_tpldata['.'][0]['ROSTER_SCOPE'] == "char") {  ?>
				<li class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php if( isset($roster->locale->act['member_profiles']) ) { echo $roster->locale->act['member_profiles']; } else { echo '{ TRANSLATE member_profiles }'; trigger_error('Missing translation { member_profiles }', E_USER_NOTICE); } ?><span class="caret"></span></a>
					<ul class="dropdown-menu">
					<?php $_menu_button_section_count = (isset($this->_tpldata['menu_button_section'])) ?  count($this->_tpldata['menu_button_section']) : 0;if ($_menu_button_section_count) {for ($this->_menu_button_section_i = 0; $this->_menu_button_section_i < $_menu_button_section_count; $this->_menu_button_section_i++){$_menu_buttons_count = (isset($this->_tpldata['menu_button_section'][$this->_menu_button_section_i]['menu_buttons'])) ? count($this->_tpldata['menu_button_section'][$this->_menu_button_section_i]['menu_buttons']) : 0;if ($_menu_buttons_count) {for ($this->_menu_buttons_i = 0; $this->_menu_buttons_i < $_menu_buttons_count; $this->_menu_buttons_i++){if ($this->_tpldata['menu_button_section'][$this->_menu_button_section_i]['menu_buttons'][$this->_menu_buttons_i]['SCOPE'] == 'char') {  ?>
						<li class="smenuitem">
							<a href="<?php echo $this->_tpldata['menu_button_section'][$this->_menu_button_section_i]['menu_buttons'][$this->_menu_buttons_i]['U_LINK']; ?>" >
								<div>
									<div id="container">
									<img src="<?php echo $this->_tpldata['menu_button_section'][$this->_menu_button_section_i]['menu_buttons'][$this->_menu_buttons_i]['ICON']; ?>"  border=0 style="vertical-align:middle;" alt="" />
									<?php echo $this->_tpldata['menu_button_section'][$this->_menu_button_section_i]['menu_buttons'][$this->_menu_buttons_i]['NAME']; ?></div>
								</div>
							</a>
						</li>
					<?php }}}}} ?>
					</ul>
				</li>
				<?php } ?>
					
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li class="right"><a href="<?php echo $this->_tpldata['.'][0]['ROSTER_URL']; ?>">Home</a></li>
				<li class="right"><a href="<?php echo $this->_tpldata['.'][0]['GALLERY']; ?>">Gallery</a></li>
				<li class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php if( isset($roster->locale->act['guild']) ) { echo $roster->locale->act['guild']; } else { echo '{ TRANSLATE guild }'; trigger_error('Missing translation { guild }', E_USER_NOTICE); } ?>
					<span class="caret"></span></a>
					<ul class="dropdown-menu">
					<?php $_menu_button_section_count = (isset($this->_tpldata['menu_button_section'])) ?  count($this->_tpldata['menu_button_section']) : 0;if ($_menu_button_section_count) {for ($this->_menu_button_section_i = 0; $this->_menu_button_section_i < $_menu_button_section_count; $this->_menu_button_section_i++){$_menu_buttons_count = (isset($this->_tpldata['menu_button_section'][$this->_menu_button_section_i]['menu_buttons'])) ? count($this->_tpldata['menu_button_section'][$this->_menu_button_section_i]['menu_buttons']) : 0;if ($_menu_buttons_count) {for ($this->_menu_buttons_i = 0; $this->_menu_buttons_i < $_menu_buttons_count; $this->_menu_buttons_i++){if ($this->_tpldata['menu_button_section'][$this->_menu_button_section_i]['menu_buttons'][$this->_menu_buttons_i]['SCOPE'] == 'guild') {  ?>
						<li class="smenuitem">
							<a href="<?php echo $this->_tpldata['menu_button_section'][$this->_menu_button_section_i]['menu_buttons'][$this->_menu_buttons_i]['U_LINK']; ?>" >
								<div>
									<div id="container">
									<img src="<?php echo $this->_tpldata['menu_button_section'][$this->_menu_button_section_i]['menu_buttons'][$this->_menu_buttons_i]['ICON']; ?>"  border=0 style="vertical-align:middle;" alt="" />
									<?php echo $this->_tpldata['menu_button_section'][$this->_menu_button_section_i]['menu_buttons'][$this->_menu_buttons_i]['NAME']; ?></div>
								</div>
							</a>
						</li>
					<?php }}}}} ?>
					</ul>
				</li>
				<li class="right"><a href="<?php echo $this->_tpldata['.'][0]['FORUM']; ?>">Forums</a></li>
				<?php if ($this->_tpldata['.'][0]['ISADMIN']) {  ?>
				<li class="right"><a class="mlink" href="<?php echo $this->_tpldata['.'][0]['RCP']; ?>">Admin CP</a></li>
				<?php }if ($this->_tpldata['.'][0]['T_BAR_USER_ILI']) { } else { ?>
				<li class="right" id="bnetlogin" ><a href="#">Log In with Battle.net<i class="spinner-battlenet"></i></a></li>
				<?php } ?>
				<li class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php if( isset($roster->locale->act['util']) ) { echo $roster->locale->act['util']; } else { echo '{ TRANSLATE util }'; trigger_error('Missing translation { util }', E_USER_NOTICE); } ?>
					<span class="caret"></span></a>
					<ul class="dropdown-menu">
					<?php $_menu_button_section_count = (isset($this->_tpldata['menu_button_section'])) ?  count($this->_tpldata['menu_button_section']) : 0;if ($_menu_button_section_count) {for ($this->_menu_button_section_i = 0; $this->_menu_button_section_i < $_menu_button_section_count; $this->_menu_button_section_i++){$_menu_buttons_count = (isset($this->_tpldata['menu_button_section'][$this->_menu_button_section_i]['menu_buttons'])) ? count($this->_tpldata['menu_button_section'][$this->_menu_button_section_i]['menu_buttons']) : 0;if ($_menu_buttons_count) {for ($this->_menu_buttons_i = 0; $this->_menu_buttons_i < $_menu_buttons_count; $this->_menu_buttons_i++){if ($this->_tpldata['menu_button_section'][$this->_menu_button_section_i]['menu_buttons'][$this->_menu_buttons_i]['SCOPE'] == 'util') {  ?>
						<li class="smenuitem">
							<a href="<?php echo $this->_tpldata['menu_button_section'][$this->_menu_button_section_i]['menu_buttons'][$this->_menu_buttons_i]['U_LINK']; ?>" >
								<div>
									<div id="container">
									<img src="<?php echo $this->_tpldata['menu_button_section'][$this->_menu_button_section_i]['menu_buttons'][$this->_menu_buttons_i]['ICON']; ?>"  border=0 style="vertical-align:middle;" alt="" />
									<?php echo $this->_tpldata['menu_button_section'][$this->_menu_button_section_i]['menu_buttons'][$this->_menu_buttons_i]['NAME']; ?></div>
								</div>
							</a>
						</li>
					<?php }}}}} ?>
					</ul>
				</li>
				
			</ul>
		</div>
	</div>
</nav>
<?php } do_action('roster_after_menu');  ?>