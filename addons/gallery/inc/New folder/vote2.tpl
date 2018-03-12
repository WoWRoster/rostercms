<?php


if ( !defined('ROSTER_INSTALLED') )
{
    exit('Detected invalid access to this file!');
}
$id = $_GET['id'];


$sqlvx = "SELECT * FROM `" . ROSTER_SCREENTABLE . "` WHERE `id` = '".$id."'";
$resultvx = $roster->db->query($sqlvx) or die_quietly($roster->db->error(),'Database Error',basename(__FILE__),__LINE__,$sqlvx);
$rowvx = $roster->db->fetch($resultvx);
if ($rowvx['rateing'] != 0 && $rowvx['votes'] != 0){
$rank2 = ($rowvx['rateing'] / $rowvx['votes']);
$rank = number_format($rank2, 1, '.', '');
}else{
$rank = "No Votes";

if (strlen($rowvx['caption']) > 27)
                  {
				$caption = substr(stripslashes($caption), 0, 24) . "...";
			}
			else
			{
				$caption = stripslashes($caption);
			}
			
}//echo $sqlv.'<br>';
print border('sgreen','start',$caption.'');
echo '

<form method="post" action="'.makelink('&amp;id='.$rowvx['id'].'&amp;op=vote').'" enctype="multipart/form-data" onsubmit="submitonce(this)">
<table cellspacing="0" cellpadding="0" border="0">
<tr>
<td>';
$path = $imgdirthumb.$rowvx['file'].'-thumb.'.$rowvx['ext'];
$path2 = $imgdir.$rowvx['catagory'].'/'.$rowvx['file'].'.'.$rowvx['ext'];
echo $functions->getimage($rowvx['id'] , $addon_cfg['rg_u_ovlb'], '1', $rowvx['disc'], $rowvx['caption'], $path, $path2, $rating, ($addon_cfg['rg_width']*1.5), ($addon_cfg['rg_height']*1.5), $rowvx['catagory']);
echo '
</td>
</tr>
<tr>
<td class="divider_sgreen" ><img src="img/pixel.gif" width="1" height="1" alt="" /></td>
</tr>
<tr>
<td>'.$rowvx['disc'].'</td></tr>  
<tr>
<td class="divider_sgreen" ><img src="img/pixel.gif" width="1" height="1" alt="" /></td>
</tr>
<tr>
<td>
 Current Rating '.$rank.'<br>
<select class="sc_select" name="vote"><option value="" selected="selected">--None--</option>';

     for ( $k='1';$k<='10';$k++ )
		{
		echo "<option value=\"".$k."\" >".$k."</option>";
		}
	  echo '</select>

        <input type="hidden" name="ss_op" value="vote" />
        <input type="hidden" name="id" value="'.$rowvx['id'].'" />
          <input type="submit" value="Vote Now!" name="votenow" /></td>
      </tr>
</td>
</tr>
';
echo '</table></form>';
print border('sgreen','end');
echo getAllTooltips();
