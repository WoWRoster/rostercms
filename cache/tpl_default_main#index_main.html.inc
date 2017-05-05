<?php global $roster; ?><div id="main" class="container">

    <div class="col-md-9">
		
		<div class="carousel slide article-slide" id="article-photo-carousel">
			<!-- Indicators -->
			<ol class="carousel-indicators">
				<?php $_slider_count = (isset($this->_tpldata['slider'])) ?  count($this->_tpldata['slider']) : 0;if ($_slider_count) {for ($this->_slider_i = 0; $this->_slider_i < $_slider_count; $this->_slider_i++){ ?>
				<li data-target="#article-photo-carousel" data-slide-to="<?php echo $this->_tpldata['slider'][$this->_slider_i]['NUM']; ?>"<?php if ($this->_tpldata['slider'][$this->_slider_i]['NUM'] == 0) {  ?> class="active"<?php } ?>>
					<img src="<?php echo $this->_tpldata['slider'][$this->_slider_i]['TIMAGE']; ?>" alt="<?php echo $this->_tpldata['slider'][$this->_slider_i]['TITLE']; ?>">
				</li>
				<?php }} ?>
			</ol>

			<!-- Wrapper for slides -->
			<div class="carousel-inner" role="listbox">
			
				<?php $_slider_count = (isset($this->_tpldata['slider'])) ?  count($this->_tpldata['slider']) : 0;if ($_slider_count) {for ($this->_slider_i = 0; $this->_slider_i < $_slider_count; $this->_slider_i++){ ?>
				<div class="item<?php if ($this->_tpldata['slider'][$this->_slider_i]['NUM'] == 0) {  ?> active<?php } ?>">
					<?php if ($this->_tpldata['slider'][$this->_slider_i]['URL']) {  ?><a href="<?php echo $this->_tpldata['slider'][$this->_slider_i]['URL']; ?>" target="_blank"><?php } ?>
					<img src="<?php echo $this->_tpldata['slider'][$this->_slider_i]['IMAGE']; ?>" alt="<?php echo $this->_tpldata['slider'][$this->_slider_i]['TITLE']; ?>">
					<div class="carousel-caption">
						<?php if ($this->_tpldata['slider'][$this->_slider_i]['TITLE']) {  ?>
							<h3><?php echo $this->_tpldata['slider'][$this->_slider_i]['TITLE']; ?></h3>
						<?php }if ($this->_tpldata['slider'][$this->_slider_i]['DESC']) {  ?>
							<p><?php echo $this->_tpldata['slider'][$this->_slider_i]['DESC']; ?></p>
						<?php } ?>
					</div>
					<?php if ($this->_tpldata['slider'][$this->_slider_i]['URL']) {  ?></a><?php } ?>
				</div>
				<?php }} ?>

			</div>
			<a class="left carousel-control" href="#article-photo-carousel" role="button" data-slide="prev">
				<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
				<span class="sr-only">Previous</span>
			</a>
			<a class="right carousel-control" href="#article-photo-carousel" role="button" data-slide="next">
				<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
				<span class="sr-only">Next</span>
			</a>
		</div>



        <?php if ($this->_tpldata['.'][0]['S_ADD_NEWS']) {  ?>
        <div class="right">
            <a class="btn btn-primary btn-xs" href="<?php echo $this->_tpldata['.'][0]['U_ADD_NEWS']; ?>"><span class="ui-icon ui-icon-plus"></span><?php if( isset($roster->locale->act['add_news']) ) { echo $roster->locale->act['add_news']; } else { echo '{ TRANSLATE add_news }'; trigger_error('Missing translation { add_news }', E_USER_NOTICE); } ?></a>
        </div>
        <?php }$_news_count = (isset($this->_tpldata['news'])) ?  count($this->_tpldata['news']) : 0;if ($_news_count) {for ($this->_news_i = 0; $this->_news_i < $_news_count; $this->_news_i++){ ?>

        <div class="news_block">
            <div class="title">
                <div class="icon">
                    <img src="<?php echo $this->_tpldata['.'][0]['INTERFACE_URL']; ?>Interface/Icons/inv_misc_note_02.<?php echo $this->_tpldata['.'][0]['IMG_SUFFIX']; ?>" alt="" />
                    <div class="mask"></div>
                </div>
                <?php echo $this->_tpldata['news'][$this->_news_i]['TITLE']; ?>
                <?php if ($this->_tpldata['.'][0]['S_EDIT_NEWS']) {  ?>
                <div class="right">
                    <a class="input" href="<?php echo $this->_tpldata['news'][$this->_news_i]['U_EDIT']; ?>"><span class="ui-icon ui-icon-pencil"></span><?php if( isset($roster->locale->act['edit']) ) { echo $roster->locale->act['edit']; } else { echo '{ TRANSLATE edit }'; trigger_error('Missing translation { edit }', E_USER_NOTICE); } ?></a>
                </div>
                <?php } ?>
            </div>
			<?php if ($this->_tpldata['news'][$this->_news_i]['IMG']) {  ?>
			<div class="header-image">
				<img src="<?php echo $this->_tpldata['news'][$this->_news_i]['IMG']; ?>"></a>
			</div>
			<?php } ?>
            <div class="article-summary">
                    <?php echo $this->_tpldata['news'][$this->_news_i]['TEXT']; ?>
            </div>
            <span class="clear"></span>
            <div class="tier-3-a">
                <div class="tier-3-b">
                    <div class="config">
                        <div class="config-name"><?php if( isset($roster->locale->act['posted_by']) ) { echo $roster->locale->act['posted_by']; } else { echo '{ TRANSLATE posted_by }'; trigger_error('Missing translation { posted_by }', E_USER_NOTICE); } ?>: <?php echo $this->_tpldata['news'][$this->_news_i]['POSTER']; ?> <span class="spacer"> - </span> <?php echo $this->_tpldata['news'][$this->_news_i]['DATE']; ?></div>
                        <div class="config-input comment_color"><a class="comment_color" href="<?php echo $this->_tpldata['news'][$this->_news_i]['U_COMMENT']; ?>"><?php echo $this->_tpldata['news'][$this->_news_i]['L_COMMENT']; ?></a></div>
                    </div>
                </div>
            </div>
        </div>
        <?php }} ?>

    </div>
    <div class="col-md-3">
        <?php $_right_count = (isset($this->_tpldata['right'])) ?  count($this->_tpldata['right']) : 0;if ($_right_count) {for ($this->_right_i = 0; $this->_right_i < $_right_count; $this->_right_i++){ ?>
		<div id="node1" class="side_pannel">
			<div class="side_title">
				<div class="icon">
					<img src="<?php echo $this->_tpldata['.'][0]['INTERFACE_URL']; ?>Interface/Icons/<?php echo $this->_tpldata['right'][$this->_right_i]['ICON']; ?>.<?php echo $this->_tpldata['.'][0]['IMG_SUFFIX']; ?>" alt="" />
					<div class="mask"></div>
				</div>
				<?php echo $this->_tpldata['right'][$this->_right_i]['BLOCKNAME']; ?>
			</div>
			<div class="content"><?php echo $this->_tpldata['right'][$this->_right_i]['BLOCK_DATA']; ?></div>
		</div>
        <?php }} ?>
    </div>
</div>