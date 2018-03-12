<?php
/******************************
 * $Id: index.php,v 1.7.0 2006/06/14 09:16:24 Ulminia Exp $
 ******************************/
 define('ROSTER_INSTALLED',true);
 if ($_GET['edit'] != ''){
$roster->output['show_menu'] = false;
}


include( $addon['dir'] . 'functions.php' );
include( $addon['dir'] . 'conf.php' );
//$roster->output['show_menu'] = false;
$functions = new ssconfig;
define('ROSTER_SCREENTABLE',$roster->db->table('ss',$addon['basename']));
if ( !defined('ROSTER_INSTALLED') )
{
    exit('Detected invalid access to this file!');
}

echo '

<!-- Begin Java Link -->
<script type="text/javascript" language="JavaScript">
<!--
	initializetabcontent("rg_menu")
//-->
</script>

<script language="javascript" type="text/javascript">
<!--
function popitup(url) {
	newwindow=window.open(url,\'name\',\'height=400,width=300\');
	if (window.focus) {newwindow.focus()}
	return false;
}

// -->
</script>

';


//echo $functions->sql_debug;


$addon_cfgn = $addon['config'];

// ----[ Decide what to do next ]---------------------------
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
            if (is_array($_POST['deletess'])){
                  foreach ($_POST['deletess'] as $delete) {
      			$functions->deletesc( $delete );
		      }//$functions->deletesc( $_POST['deletess'] );
		}
            else {
      	
                  $functions->deletesc( $_POST['deletess'] );
		}
            break;

            case 'approve';
                  if ($_POST['approve']){
        	         foreach ($_POST['approve'] as $approve) {
				$functions->approvesc( $approve );
        	         }
                  }
                  if ($_POST['delete']){
            	     foreach ($_POST['delete'] as $delete) {
                              $functions->deletesc( $delete );
			      }
                  }

            break;
            
            case 'upload_wm';
                  $functions->uploadwmImage( $addon['dir'].$addon_cfg['rg_wm_dir'], $_FILES['wmfile']['name'] );
            break;

		default:
		break;
	}
}


if( $_REQUEST['make_dir'] == 'chmod' )
{
	if( $functions->checkDirst( $ssfolder,1,1 ) )
	{
		$functions->setMessage('All folders in Screenshots Changed to 777');
	}
	else
	{
		$functions->setMessage('Change chmod manualy');
	}
}
// ----[ End Decide what to do next ]-----------------------

$addon_cfg = $addon['config'];
                  

$query2 = "SELECT * FROM `".ROSTER_SCREENTABLE."` WHERE `approve` = '' ORDER BY 'order' DESC";
$result2 = $roster->db->query($query2) or die_quietly($roster->db->error(),'Database Error',basename(__FILE__),__LINE__,$query2);
$tnapproved = $roster->db->num_rows($result2);


//open only edit box
if ($_GET['edit'] != ''){

if ($_GET['id'] != '' && $_GET['edit'] == 'cat'){
$roster->output['show_menu'] = false;
$sql = "SELECT * FROM `" . ROSTER_SCREENTABLE . "` WHERE `id` = '".$_GET['id']."'";
$result = $roster->db->query($sql) or die_quietly($roster->db->error(),'Database Error',basename(__FILE__),__LINE__,$query);
$row = $roster->db->fetch($result);
if ($row['rateing'] != 0 && $row['votes'] != 0){
$rank2 = ($row['rateing'] / $row['votes']);
$rank = number_format($rank2, 1, '.', '');
}else{
$rank = "No Votes";
}
$roster->config['logo'] = false;
//$roster->output['show_header'] = false;  // Turn off roster header
$roster->output['show_menu'] = false;    // Turn off roster menu
$roster->output['show_footer'] = false;  // Turn off roster footer 
ob_start();
include_once( $addon['dir'].'inc/edit.tpl' );
$vote = ob_get_contents();
ob_end_clean();

echo $vote;
end;

}

if ($_GET['dir'] != '' && $_GET['edit'] == 'chmod'){
      $roster->output['show_menu'] = false;
      $roster->config['logo'] = false;
//$roster->output['show_header'] = false;  // Turn off roster header
$roster->output['show_menu'] = false;    // Turn off roster menu
$roster->output['show_footer'] = false;  // Turn off roster footer 

      $cmd = '';
      $cmd .= ''.border('sgreen','start',''.$roster->locale->act['admin']['status_chmod'].'').'';
      $cmd .= '<table width="300">';

      if( chmod( $addon['dir'].$dir,0777 ) ){
            $cmd.= '<tr>
            <td class="membersRow1">'.$roster->locale->act['admin']['status_chmod_changed'].'</td></tr>';
            
      }
      else
      {
            $cmd.= '<tr>
            <td class="membersRow1">'.$roster->locale->act['admin']['status_chmod_unchanged'].'</td></tr>';
      }
      $cmd .= '</table>'.border('sgreen','end').'<br>';
      echo $cmd;


}

}
