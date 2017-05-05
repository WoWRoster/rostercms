<?php global $roster; ?><!-- $Id: footer.html 2484 2012-05-26 22:09:21Z c.treyce@gmail.com $ -->

	</div>

	<div class="roster-footer">

		<div class="version">Blood God v<?php echo $this->_tpldata['.'][0]['ROSTER_VERSION']; ?></div>
<div class="bliz">
<?php if( isset($roster->locale->act['bliz_notice']) ) { echo $roster->locale->act['bliz_notice']; } else { echo '{ TRANSLATE bliz_notice }'; trigger_error('Missing translation { bliz_notice }', E_USER_NOTICE); } ?>
		</div>
		<?php if ($this->_tpldata['.'][0]['S_PROCESSTIME']) {  ?>
		<div class="process"><?php echo $this->_tpldata['.'][0]['PROCESSTIME']; ?> | <?php echo $this->_tpldata['.'][0]['QUERYCOUNT']; ?></div>
		<?php } ?>
	</div>
</div>

<?php if ($this->_tpldata['.'][0]['S_DEBUG_MODE']) {  ?>
<br />

<table class="border_frame" cellpadding="0" cellspacing="1">
	<tr>
		<td class="border_color sredborder">
			<div class="header_text sredborder">PHP Errors</div>

			<table cellpadding="0" cellspacing="0" width="100%">
<?php $_php_debug_count = (isset($this->_tpldata['php_debug'])) ?  count($this->_tpldata['php_debug']) : 0;if ($_php_debug_count) {for ($this->_php_debug_i = 0; $this->_php_debug_i < $_php_debug_count; $this->_php_debug_i++){ ?>
				<tr>
					<th class="membersHeaderRight"><?php echo $this->_tpldata['php_debug'][$this->_php_debug_i]['FILE']; ?></th>
				</tr>
<?php $_row_count = (isset($this->_tpldata['php_debug'][$this->_php_debug_i]['row'])) ? count($this->_tpldata['php_debug'][$this->_php_debug_i]['row']) : 0;if ($_row_count) {for ($this->_row_i = 0; $this->_row_i < $_row_count; $this->_row_i++){ ?>
				<tr>
					<td class="membersRowRight<?php echo $this->_tpldata['php_debug'][$this->_php_debug_i]['row'][$this->_row_i]['ROW_CLASS']; ?>" style="font-size:10px;">&nbsp;&nbsp;<?php echo $this->_tpldata['php_debug'][$this->_php_debug_i]['row'][$this->_row_i]['ERROR']; ?></td>
				</tr>
<?php }}}} ?>
			</table>

		</td>
	</tr>
</table>

<br />
<?php }if ($this->_tpldata['.'][0]['S_DEBUG']) {  ?>
			<br />
			<div class="tier-1-a">
				<div class="tier-1-b">
					<div class="tier-1-c">
						<div class="tier-1-title">Roster Debugging</div>
						<div class="tier-2-a">
							<div class="tier-2-b">
								<div class="tier-2-title">
									<table width=100%>
										<td width="10%">Line</td>
										<td width="10%">Time</td>
										<td width="10%">Class</td>
										<td width="20%">Function</td>
										<td width="30%">Info</td>
										<td width="10%">Status</td>
									</table>
								</div>
			<?php $_d_row_count = (isset($this->_tpldata['d_row'])) ?  count($this->_tpldata['d_row']) : 0;if ($_d_row_count) {for ($this->_d_row_i = 0; $this->_d_row_i < $_d_row_count; $this->_d_row_i++){ ?>
								<div class="tier-6-a">
									<div class="tier-6-b">
										<table width="100%">
											<tr>
												<td width="10%"><?php echo $this->_tpldata['d_row'][$this->_d_row_i]['LINE']; ?></td>
												<td width="10%"><?php echo $this->_tpldata['d_row'][$this->_d_row_i]['TIME']; ?></td>
												<td width="10%"><?php echo $this->_tpldata['d_row'][$this->_d_row_i]['CLASS']; ?></td>
												<td width="20%"><?php echo $this->_tpldata['d_row'][$this->_d_row_i]['FUNC']; ?></td>
												<td width="30%"><?php echo $this->_tpldata['d_row'][$this->_d_row_i]['INFO']; ?></td>
												<td width="10%"><?php echo $this->_tpldata['d_row'][$this->_d_row_i]['STATUS']; ?></td>
											</tr>
										</table>
									</div>
								</div>
			<?php if ($this->_tpldata['.'][0]['DEBUG_DATA']) {  ?>
								<div class="tier-1-a">
									<div class="tier-1-b">
										<div class="tier-1-c">
											<div class="tier-1-title">Arguments</div>
											<?php echo $this->_tpldata['d_row'][$this->_d_row_i]['ARGS']; ?>
										</div>
										<div class="tier-1-title">Returns</div>
											<?php echo $this->_tpldata['d_row'][$this->_d_row_i]['RET']; ?>
									</div>
								</div>


			<?php }}} ?>
							</div>
						</div>
					</div>
				</div>
			</div>

			<?php }if ($this->_tpldata['.'][0]['S_SQL_WIN']) {  ?>
<br />

<table class="border_frame" cellpadding="0" cellspacing="1">
	<tr>
		<td class="border_color sgreenborder">
			<div class="header_text sgreenborder"><?php if( isset($roster->locale->act['sql_queries']) ) { echo $roster->locale->act['sql_queries']; } else { echo '{ TRANSLATE sql_queries }'; trigger_error('Missing translation { sql_queries }', E_USER_NOTICE); } ?></div>

			<div class="sqlwindow">
				<table class="table table-striped">
<?php $_sql_debug_count = (isset($this->_tpldata['sql_debug'])) ?  count($this->_tpldata['sql_debug']) : 0;if ($_sql_debug_count) {for ($this->_sql_debug_i = 0; $this->_sql_debug_i < $_sql_debug_count; $this->_sql_debug_i++){ ?>
					<thead>
						<tr><th colspan="3"></th></tr>
						<tr>
							<th colspan="3"><?php echo $this->_tpldata['sql_debug'][$this->_sql_debug_i]['FILE']; ?></th>
						</tr>
						<tr>
							<th>Line</th>
							<th>Time</th>
							<th>Query</th>
						</tr>
					</thead>
<?php $_row_count = (isset($this->_tpldata['sql_debug'][$this->_sql_debug_i]['row'])) ? count($this->_tpldata['sql_debug'][$this->_sql_debug_i]['row']) : 0;if ($_row_count) {for ($this->_row_i = 0; $this->_row_i < $_row_count; $this->_row_i++){ ?>
					<tr>
						<td>&nbsp;&nbsp;<?php echo $this->_tpldata['sql_debug'][$this->_sql_debug_i]['row'][$this->_row_i]['LINE']; ?></td>
						<td><?php echo $this->_tpldata['sql_debug'][$this->_sql_debug_i]['row'][$this->_row_i]['TIME']; ?></td>
						<td style="white-space:normal;"><?php echo $this->_tpldata['sql_debug'][$this->_sql_debug_i]['row'][$this->_row_i]['QUERY']; ?></td>
					</tr>
<?php if ($this->_tpldata['.'][0]['S_DESCRIBE']) {  ?>
					<tr>
						<td colspan="3" class="membersRowRight<?php echo $this->_tpldata['sql_debug'][$this->_sql_debug_i]['row'][$this->_row_i]['ROW_CLASS']; ?>"><?php echo $this->_tpldata['sql_debug'][$this->_sql_debug_i]['row'][$this->_row_i]['DESCRIBE']; ?></td>
					</tr>
<?php }}}}} ?>
				</table>
			</div>

		</td>
	</tr>
</table>
<?php } ?>

</div>

<div id="overDiv"></div>

<?php echo $this->_tpldata['.'][0]['ROSTER_FOOT_JS']; ?>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-64984999-1', 'auto');
  ga('send', 'pageview');

</script>
</body>
</html>