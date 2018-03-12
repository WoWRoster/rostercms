<?php
/******************************
 * $Id: index.php,v 1.7.0 2006/06/14 09:16:24 Ulminia Exp $
 ******************************/
 
 if (isset($_GET['edit']) != ''){
$roster->output['show_menu'] = false;
}
$roster->output['html_head'] = '
<script src="addons/RosterGallery/css/SpryTooltip.js" type="text/javascript"></script>
<link href="' . ROSTER_PATH . 'addons/RosterGallery/css/samples.css" rel="stylesheet" type="text/css" />
<link href="' . ROSTER_PATH . 'addons/RosterGallery/css/SpryTooltip.css" rel="stylesheet" type="text/css" />

<script language="JavaScript" type="text/javascript" src="addons/RosterGallery/css/xpath.js"></script>
<script language="JavaScript" type="text/javascript" src="addons/RosterGallery/css/SpryData.js"></script>
<script language="JavaScript" type="text/javascript" src="addons/RosterGallery/css/SpryTooltip.js"></script>
<link href="addons/RosterGallery/css/SpryTooltip.css" rel="stylesheet" type="text/css" />
<style type="text/css">
.tooltip {
	background-color: #FFFFCC;
}
#multipleSample, .multipleSample2 {
	width: 300px;
	float: left;
}
#tooltipMultipleSample {
	border: 1px;
	width: 400px;
}
#classonme {
	width: 150px;
	height: 120px;
}
#classonme.enlarge {
	width: 160px;
	height: 130px;
	border: 2px solid red;
}
.optionName, .optionValue {
	font-weight: bold;
	font-size: 14px;
	color: red;
}
.optionValue {
	color: blue;
}
</style>

';

include( $addon['dir'] . 'inc/functions.php' );
include( $addon['dir'] . 'inc/conf.php' );
//$roster->output['show_menu'] = false;
$functions = new ssconfig;
define('ROSTER_SCREENTABLE',$roster->db->table('ss',$addon['basename']));
if ( !defined('ROSTER_INSTALLED') )
{
    exit('Detected invalid access to this file!');
}

echo '

<script language="javascript" type="text/javascript">
<!--
function popitup(url) {
	newwindow=window.open(url,\'name\',\'height=425,width=350\');
	if (window.focus) {newwindow.focus()}
	return false;
}

// -->
</script>



';



//echo $functions->sql_debug;




/*
OK here is where we check and see if the table is installed if not it installes it using the install script
*/

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
//getConfigData();
$addon = getaddon($addon['basename']);

if( isset($_GET['make_dir']) == 'chmod' )
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

// this refreshes the array for any changes

                  
                  
                  
                  if (isset($_GET['edit']) != ''){

if (isset($_GET['ide']) != '' && isset($_GET['edit']) == 'cat'){
//$roster->output['show_header'] = false;  // Disable the roster header
$roster->output['show_menu'] = false;    // Disable roster menu
$roster->config['logo'] = '';
$pagebar = null;
$menu = '';
$messages = '';
$ide = $_GET['ide'];
//echo $_GET['ide'].'<br>';
$imgdirthumb = $addon['url_path'].'screenshots/thumbs/';
$imgdirthumb2 = $addon['url_path'].'screenshots/';
$roster->output['show_menu'] = false;
$sqle = "SELECT * FROM `" . ROSTER_SCREENTABLE . "` WHERE `id` = '".$ide."'";
//echo $sqle.'<br>';
$resulte = $roster->db->query($sqle) or die_quietly($roster->db->error(),'Database Error',basename(__FILE__),__LINE__,$querye);
$rowe = $roster->db->fetch($resulte);
//echo $rowe['ide'];
if ($rowe['rateing'] != 0 && $rowe['votes'] != 0){
$rank2 = ($rowe['rateing'] / $rowe['votes']);
$rank = number_format($rank2, 1, '.', '');
}else{
$rank = "No Votes";
}


$messages = $functions->getMessage();
$messages .= $functions->getAdminMessage();

if( !empty($messages) )
{
	$messages .= '';
}
$x = 1;
$msg = border('sblue','start',$roster->locale->act['admin']['messages']).'<table width="200"><tr><td width="200" class="membersRow2"><center>'.$messages.' </td></tr></table>'.border('sblue','end').'<br>';


$html = border('sgreen','start',$rowe['caption'].' Current Rating '.$rank);
$html .= '

<form method="post" action="'.makelink('&amp;edit=cat&amp;ide='.$ide.'').'" enctype="multipart/form-data" onsubmit="submitonce(this)">
<table cellspacing="0" cellpadding="0" border="0">
<tr>
<td>';
$html .= '<img src="' . $imgdirthumb.$rowe['file'].'-thumb.'.$rowe['ext'].'" ></a><br>';
$html .= '
</td>
</tr>
  <tr>
    <td class="simpleborderbot sgreenborderbot"></td>
  </tr>  
<tr>
    <td class="simpleborderbot sgreenborderbot"></td>
  </tr>
<tr>
<td>
<select class="sc_select" name="new_cat"><option value="" selected="selected">--['.$addon['config']['rg_'.$rowe['catagory'].''].']--</option>';

     for($n=1;$n<=10;$n++)
      {
            if ($n != 11){
            
            $total = $addon['config']['rg_catcount'];
            $current = $addon['config']['rg_cat'.$n.'en'];
          if ($addon['config']['rg_cat'.$n.'en'] == 1){
          if ($addon['config']['rg_'.$rowe['catagory'].''] != $addon['config']['rg_cat'.$n]){
                  
              $html .= "<option value=\"".$addon['config']['rg_cat'.$n.'id']."\" >".$addon['config']['rg_cat'.$n]."</option>\n";
            }
            }
            }
      }
	  $html .= '</select>

        <input type="hidden" name="ss_op" value="change_cat" />
        <input type="hidden" name="id" value="'.$ide.'" />
        <input type="hidden" name="old_cat" value="'.$rowe['catagory'].'" />
        <input type="hidden" name="path" value="'.$imgdirthumb2.'" />
        <input type="hidden" name="file" value="'.$rowe['file'].'.'.$rowe['ext'].'" />
          <input type="submit" value="'.$roster->locale->act['admin']['edit'].'" name="edit" /></td>
      </tr>
</td>
</tr>
';
$html .= '</table></form>';
$html .= border('sgreen','end');
if( !empty($messages) )
{
echo $msg;
}
echo $html;
//die;
//$html .= $vote;


}

}else{

$query2 = "SELECT * FROM `".ROSTER_SCREENTABLE."` WHERE `approve` = '' ORDER BY 'order' DESC";
$result2 = $roster->db->query($query2) or die_quietly($roster->db->error(),'Database Error',basename(__FILE__),__LINE__,$query2);
$tnapproved = $roster->db->num_rows($result2);


//open only edit box
$adddir = $addon['dir'];
$backgFilesArr = $functions->listFiles( $adddir.$addon['config']['rg_wm_dir'],array('png') );
                  
$messages = $functions->getMessage();
$messages .= $functions->getAdminMessage();
if ($tnapproved > 0){
$messages .= '<font color=green>There are '.$tnapproved.' images that need approving</font>';
}

if (!is_writeable($addon['dir'].'screenshots/')){
$messages .= sprintf('Screenshots folder is not writable<br />Click <a href="%1$s">HERE</a> to try to chmod [<span class="green">%2$s</span>]',makelink('&amp;make_dir=chmod'),'screenshots/');
}

if( !empty($messages) )
{
	$messages .= '';
}
$x = 1;
$msg = border('sblue','start',$roster->locale->act['admin']['messages']).'<table width="500"><tr><td width="500" class="membersRow2"><center>';
$msg .= $messages;
$msg .= '<br>'.$roster->locale->act['admin']['configurationgreeting'];
$msg .= '<br><span style="font-size:10px;color:red;">Logged in Admin:</span><form style="display:inline;" name="roster_logout" action="?p=rostercp" method="post"><span style="font-size:10px;color:#FFFFFF"><input type="hidden" name="logout" value="1" />[<a href="javascript:document.roster_logout.submit();">Logout</a>]</span></form></center><br /></td></tr></table>';
$msg .= border('sblue','end').'<br>';


echo '<link rel="stylesheet" type="text/css" href="'.$addon['dir'].'style.css" />';
$menu .= $uploadimg;
echo $msg.'';

echo "
<form action=\"\" method=\"post\" enctype=\"multipart/form-data\" id=\"config\" onsubmit=\"return confirm('".$roster->locale->act['confirm_config_submit']."');submitonce(this);\">
<input type=\"submit\" value=\"".$roster->locale->act['config_submit_button']."\" />\n
<input type=\"reset\" name=\"Reset\" value=\"".$roster->locale->act['config_reset_button']."\" onclick=\"return confirm('".$roster->locale->act['confirm_config_reset']."')\"/>\n
<input type=\"hidden\" name=\"ss_op\" value=\"settings\" />\n<br /><br />\n
".$html.'
';
echo '
<script type="text/javascript">
var rg_menu=new tabcontent(\'rg_menu\');
rg_menu.init();
</script>

<script type="text/javascript">
var rg_menu1=new tabcontent(\'rg_menu1\');
rg_menu1.init();
</script>
<script type="text/javascript">
var rg_menu2=new tabcontent(\'rg_menu2\');
rg_menu2.init();
</script>
<script type="text/javascript">
var rg_menu3=new tabcontent(\'rg_menu3\');
rg_menu3.init();
</script>


';


}
