<?php

include( $addon['dir'] . 'inc/functions.php' );
include( $addon['dir'] . 'inc/functions2.php' );
include( $addon['dir'] . 'inc/conf.php' );
//$roster->output['show_menu'] = false;
$functions = new ssconfig;
$functions2 = new ssconfig2;

if( isset($_POST['ss_op']) && $_POST['ss_op'] != '' )
{
	switch ( $_POST['ss_op'] )
	{
		case 'settings':
		$functions->processData( $_POST );
		break;

		case 'incert';
		$functions->incertData( $_POST );
            break;

		case 'delete';
			if (is_array($_POST['deletess']))
			{
				foreach ($_POST['deletess'] as $delete)
				{
					$functions->deletesc( $delete );
				}
			}
			else
			{
				$functions->deletesc( $_POST['deletess'] );
			}
		break;
            
            case 'change_cat';
                  $functions->changecat($_POST['id'],$_POST['new_cat'],$_POST['old_cat'],$addon['config'],$_POST['file'],$_POST['path']);
            break;

            case 'approve';
                  if (isset($_POST['approve'])){
        	         foreach ($_POST['approve'] as $approve) {
				$functions->approvesc( $approve );
        	         }
                  }
                  if (isset($_POST['delete'])){
            	     foreach ($_POST['delete'] as $delete) {
                              $functions->deletesc( $delete );
			      }
                  }

            break;
            
            case 'upload_wm';
                  $functions->uploadwmImage( $addon['dir'].$addon['config']['rg_wm_dir'], $_FILES['wmfile']['name'] );
            break;

		default:
		break;
	}
}

	$addon_cfg = $addon['config'];
	$apimgdir = $addon['url_path'].'screenshots/thumbs/';
	roster_add_css($addon['dir'] . 'style.css','module');

	$query1 = "SELECT * FROM `".$roster->db->table('ss',$addon['basename'])."` WHERE `approve` = 'YES' ORDER BY 'order' DESC";
	$result1 = $roster->db->query($query1) or die_quietly($roster->db->error(),'Database Error',basename(__FILE__),__LINE__,$query1);
	$tapproved = $roster->db->num_rows($result1);

	$cats = $functions2->get_cat_full();
	//d($cats);
	if ($tapproved >= 12 ){
	$apages1  = ($tapproved / 12);
	} else {
	$apages1  = 1;
	}
	$apages = ceil($apages1);

	echo border('sgreen','start',$roster->locale->act['admin']['apconfig']).'';
	?>
	<!-- begin Display settings -->

	<div>
	<?php
	echo '<form action="'.makelink().'" method="post" enctype="multipart/form-data" id="configdel" onsubmit="submitonce(this)">
		<input type="hidden" name="ss_op" value="delete" />';

	echo '<div class="menue_adm_ss">
	<div class="btn-toolbar" id="menu">
			<ul class="btn-group" id="rcp_usage1">';
			for( $i=1; $i<=$apages; $i++){
				echo '<li id="ap_'.$i.'" class="btn btn-default btn-sm" >Page '.$i.'</li>';
			}
			echo '</ul>
		</div></div>';
		
	$rowex = 0;
	$rowe = 0;
	$g = 1;
	$ipp = 12;
	$ipl = 4;
	$h = 0;
	$pag = 1;
	$q = 0;
	if ( $tapproved == 0){
	echo 'No Images</td></tr>';
	}
	while ($row = $roster->db->fetch($result1))
	{
		$q++;
		$rowex++;
		$h++;
		if ($g == 1)
		{
			echo '<div id="ap_'.$pag.'" style="display:none;">
			';
		}
		$g++;
		if ($q == 1)
		{
			echo '<div class="card-deck">';
			$rowe++;
		}
		$c = 0;
	
echo '	
		<div class="card text-xs-center" style="background-color: #333; border-color: #333;">
			<img class="card-img-top" src="'.$apimgdir.$row['file'].'-thumb.'.$row['ext'].'" alt="Card image cap">
			<div class="card-block">
				<h4 class="card-title">'.$row['caption'].'</h4>
				<p class="card-text">'.$row['disc'].'</p>
			</div>
			<ul class="list-group list-group-flush">
				<li class="list-group-item">'.$roster->locale->act['admin']['delimage'].'?  <input name="deletess[]" type="checkbox" id="delete" value="'.$row['id'].'"></li>
				<li class="list-group-item"><a href="'.makelink('&amp;edit=cat&amp;ide='.$row['id'].'').'" onclick="return popitup(\''.makelink('&amp;edit=cat&amp;ide='.$row['id'].'').'\')">'.$roster->locale->act['admin']['edit'].'</a></li>
				<li class="list-group-item">Group: '.$cats[$row['catagory']]['cat_name'].'</li>
				<li class="list-group-item">';
				if ($row['approve']){
					echo '<font color=green>'.$roster->locale->act['admin']['imageapproved'].'</font>';
				}
				if (!$row['approve']){
					echo '<font color=red><div style="cursor:help;" onmouseover="overlib(\''.$roster->locale->act['admin']['na1'].''.$row['id'].''.$roster->locale->act['admin']['na2'].'\',CAPTION,\''.$roster->locale->act['admin']['imagenotapproved'].'\');" onmouseout="return nd();">'.$roster->locale->act['admin']['imagenotapproved'].'</font>  <input name="approve[]" type="checkbox" id="approve" value="'.$row['id'].'"></div>';
				}
				echo '</li>
			</ul>
		</div>';

	echo "\n\n\n";
	if ($q == 3){
		echo '</div>';
	$q = '0';
	}
	if ($h == $ipp){
		$g = 1;
		$pag++;
		$h = 0;
		echo '</div>';
	}
	}
	echo '</div></div>';

	?>
	<?php

	echo '<form action="'.makelink().'" method="post" enctype="multipart/form-data" id="configdel" onsubmit="submitonce(this)">
		<input type="hidden" name="ss_op" value="delete" />
		<input type="submit" value="'.$roster->locale->act['admin']['delsel'].'" />';
	echo  '</form>';

	?>			
	</div>

	<!-- Begin Java Link -->
	<script type="text/javascript" language="JavaScript">
	<!--
		var id=new tabcontent('tab_menu');
		  id.init();
	//-->
	</script>
	<!-- Begin Java Link -->
	<script type="text/javascript" language="JavaScript">
	<!--
		var id=new tabcontent('rg_menu2');
		  id.init();
	//-->
	</script>


	<?php

	/**
	 * Make our menu from the config api
	 */
	// ----[ Set the tablename and create the config class ]----
	include(ROSTER_LIB . 'config.lib.php');
	$config = new roster_config( $roster->db->table('addon_config'), '`addon_id` = "' . $addon['addon_id'] . '"' );

	// ----[ Get configuration data ]---------------------------
	$config->getConfigData();

	// ----[ Build the page items using lib functions ]---------
	$menu .= $config->buildConfigMenu('rostercp-addon-' . $addon['basename']);

	?>
