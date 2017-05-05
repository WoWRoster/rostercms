<?php global $roster; ?><div class="header">
		<nav class="navbar navbar-inverse navigation-clean-button">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="#"><?php echo $this->_tpldata['.'][0]['ROSTERCP_TITLE']; ?></a>
				</div>
				<div id="navbar" class="navbar-collapse collapse">
					<ul class="nav navbar-nav navbar-right">
						<li class="right"><a href="<?php echo $this->_tpldata['.'][0]['ROSTER_URL']; ?>" class="menu-icon"><i class="fa fa-sign-out"></i> Back To Roster</a></li>
					</ul>
				</div>
			</div>
		</nav>
	</div>

    <div class="page-content">
    	<div class="row">
		  <div class="col-md-2">
		  	<div class="sidebar content-box" style="display: block;">
                <ul class="nav">
					<?php $_pagebar_count = (isset($this->_tpldata['pagebar'])) ?  count($this->_tpldata['pagebar']) : 0;if ($_pagebar_count) {for ($this->_pagebar_i = 0; $this->_pagebar_i < $_pagebar_count; $this->_pagebar_i++){ ?>
					<li<?php if ($this->_tpldata['pagebar'][$this->_pagebar_i]['SELECTED']) {  ?> class="current"<?php } ?>>
						<a href="<?php echo $this->_tpldata['pagebar'][$this->_pagebar_i]['LINK']; ?>"><i class="fa fa-fw fa-bar-chart-o"></i><?php echo $this->_tpldata['pagebar'][$this->_pagebar_i]['NAME']; ?></a>
					</li>
					<?php }}if ($this->_tpldata['.'][0]['ADDON_PAGEBAR']) {  ?>
					<li class="submenu<?php if ($this->_tpldata['.'][0]['ADDON_PAGE_S']) {  ?> open<?php } ?>">
						<a href="javascript:;">
							<i class="glyphicon glyphicon-list"></i> Addons
                            <span class="caret pull-right"></span>
						</a>
						<ul>
						<?php $_addon_pagebar_count = (isset($this->_tpldata['addon_pagebar'])) ?  count($this->_tpldata['addon_pagebar']) : 0;if ($_addon_pagebar_count) {for ($this->_addon_pagebar_i = 0; $this->_addon_pagebar_i < $_addon_pagebar_count; $this->_addon_pagebar_i++){ ?>
							<li<?php if ($this->_tpldata['addon_pagebar'][$this->_addon_pagebar_i]['SELECTED']) {  ?> class="current"<?php } ?>>
								<a href="<?php echo $this->_tpldata['addon_pagebar'][$this->_addon_pagebar_i]['LINK']; ?>"><?php echo $this->_tpldata['addon_pagebar'][$this->_addon_pagebar_i]['NAME']; ?></a>
							</li>
						<?php }} ?>
						</ul>
					</li>
					<?php }if ($this->_tpldata['.'][0]['PLUGIN_PAGEBAR']) {  ?>
                    <li class="submenu<?php if ($this->_tpldata['.'][0]['PLUGIN_PAGE_S']) {  ?> open<?php } ?>">
                         <a href="javascript:;">
							<i class="glyphicon glyphicon-list"></i> Plugins
                            <span class="caret pull-right"></span>
						</a>
                        
                        <ul>
						<?php $_plugin_pagebar_count = (isset($this->_tpldata['plugin_pagebar'])) ?  count($this->_tpldata['plugin_pagebar']) : 0;if ($_plugin_pagebar_count) {for ($this->_plugin_pagebar_i = 0; $this->_plugin_pagebar_i < $_plugin_pagebar_count; $this->_plugin_pagebar_i++){ ?>
                            <li<?php if ($this->_tpldata['plugin_pagebar'][$this->_plugin_pagebar_i]['SELECTED']) {  ?> class="current"<?php } ?>>
								<a href="<?php echo $this->_tpldata['plugin_pagebar'][$this->_plugin_pagebar_i]['LINK']; ?>"><?php echo $this->_tpldata['plugin_pagebar'][$this->_plugin_pagebar_i]['NAME']; ?></a>
							</li>
						<?php }} ?>
                        </ul>
                    </li>
					<?php } ?>				
                </ul>
             </div>
		  </div>
		  <div class="col-md-10">
		  	<div class="row">
				<?php if ($this->_tpldata['.'][0]['MENU'] != '') {  ?>
		  		<div class="col-md-2">
		  			<div class="content-box-large">
		  				<?php echo $this->_tpldata['.'][0]['MENU']; ?>
		  			</div>
		  		</div>

		  		<div class="col-md-10">
				<?php } else { ?>
				<div class="col-md-12">
				<?php } ?>
		  			<div class="row">
		  				<div class="col-md-12">
						<?php echo $this->_tpldata['.'][0]['BEGIN_FORM']; ?>
		  					<div class="content-box-header">
			  					<div class="panel-title"><?php echo $this->_tpldata['.'][0]['ROSTERCP_TITLE']; ?></div>
								<?php if ($this->_tpldata['.'][0]['BOXOPTIONS']) {  ?>
								<div class="panel-options">
									<?php echo $this->_tpldata['.'][0]['BOXOPTIONS']; ?>
								</div>
								<?php } ?>
				  			</div>
				  			<div class="content-box-large box-with-header"><?php echo $this->_tpldata['.'][0]['BODY']; ?></div>
						<?php echo $this->_tpldata['.'][0]['END_FORM']; ?>
		  				</div>
		  			</div>
		  		</div>
		  	</div>
		  </div>
		</div>
    </div>