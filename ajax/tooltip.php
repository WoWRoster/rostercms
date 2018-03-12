<?php

if( !defined('IN_ROSTER') )
{
	exit('Detected invalid access to this file!');
}

$type = $id = '';
if ($roster->pages[1] != '')
{
	$type = $roster->pages[1];
}
else
{
	return 'Error';
	exit();
}
if ($_GET['id'] != '')
{
	$id = $_GET['id'];
}
else
{
	return 'Error';
	exit();
}

	$output = '';
	//echo '<pre>';print_r($api->act);
	switch ($type)
	{
		case 'itemptr':
			$var = file_get_contents('http://ptr.wowhead.com/item='.$id.'?power');
			$ret = str_replace("\$WowheadPower.registerItem('".$id."', 'ptr', ", '', $var);
			$ret = str_replace(");", '', $ret);
			$ret = str_replace('"', '\"', $ret);
			$ret = preg_replace("/\t(\w*)\:/", '"\1":', $ret);
			$ret = preg_replace("/\ '(.*)',/", '"\1",', $ret);
			$ret = preg_replace("/\ '(.*)'/", '"\1"', $ret);
			$ret = preg_replace("/q(\d{1})/", 'item-\1', $ret);
			//echo $ret;
			//class="q4"
			
			$ret = str_replace("\'", "'", $ret);
			$d = json_decode($ret,true);
			
			$output = stripslashes($d['tooltip_ptr']);
			
			
		break;
		
		case 'item':

			//list($id1,$member_id) = 
			$url_data = explode('|',$id);
			list($item_id,$enchant,$gem0,$gem1,$gem2,$gem3,$suffixID,$uniqueID,$level,$reforgeId,$upgradeId,$bonus1,$bonus2,$bonus3,$bonus4,) = explode(':',$url_data[0]);
			if ( isset($url_data[1]) )
			{
				
				//echo $item_id.' - '.$enchant.' - '.$gem0.' - '.$gem1.' - '.$gem2.' - '.$gem3.' - '.$suffixID.' - '.$uniqueID.' - '.$level.' - '.$reforgeId.' - '.$upgradeId.' - '.$bonus1.' - '.$bonus2.' - '.$bonus3.' - '.$bonus4.'<br>';
				$query12  = " SELECT *"
				. " FROM `" . $roster->db->table('items') . "`"
				. " WHERE `member_id` = '".$url_data[1]."'"
				. " AND `item_id` = '".$url_data[0]."';";
				$result12 = $roster->db->query($query12);
				$da = $roster->db->fetch($result12);
				$uitem = json_decode($da['json'],true);
				//d($uitem);
			}
			else
			{
				$uitem = null;
			}
			
			$params = array();
			$params['id'] = $item_id;
			if (!empty($uitem['bonusLists']))
			{
				$params['bl'] = implode(',',$uitem['bonusLists']);
			}
			if (!isset($uitem['artifactId']))
			{
				$params['context'] = $uitem['context'];
			}
			$i = $roster->api2->fetch('item',$params);
			$output = $roster->api2->item->item($i['id'],$i,$uitem,null);

		break;
		
		case 'spell':

			$a = $roster->api2->fetch('spell',array('id'=>$id));

			$output = '<div xmlns="http://www.w3.org/1999/xhtml" class="tc-tooltip">
				<h3>'.$a['name'].'</h3>
				<div>
					<span class="float-right">'.$a['range'].'</span>
					'.$a['powerCost'].'
					<span class="clear"><!-- --></span>
				</div>

				<div>
					<span class="float-right">'.$a['cooldown']'</span>
					'.$a['castTime'].'
					<span class="clear"><!-- --></span>
				</div>

				<div class="color-tooltip-yellow">
				'.str_replace('\n\n','<br>',$a['description']).'
				</div>
			</div>';
		break;
		
		case 'talent':
			$sqlg = "SELECT * FROM `".$roster->db->table('talents_data') ."` WHERE `talent_id` = '".$id."' ";
			$resultg = $roster->db->query($sqlg);
			$row = $roster->db->fetch($resultg);

			$output = '<div xmlns="http://www.w3.org/1999/xhtml" class="tc-tooltip">
				<h3>'.$row['name'].'</h3>
				<div>
					'.$row['tooltip'].'
				</div>
			</div>';

		break;
		
		case 'glyph':
		
			$sqlg = "SELECT * FROM `api_data_glyphs` WHERE `id` = '".$id."' ";
			$resultg = $roster->db->query($sqlg);
			$rowg = $roster->db->fetch($resultg);
			$tooltip = '';
			$tooltip .= $rowg['name'].'<br>';
			$tooltip .=	(isset($rowg['subtext']) ? ''.$rowg['subtext'].'<br>' : '');
			$tooltip .=	(isset($rowg['htmlDescription']) ? $rowg['htmlDescription'] : '');
			$output	= $tooltip;//$tooltip;
		break;
		
		case 'text':
			$body = $_POST['tooltip'];		
			$body = substr($body, 5);//str_replace("text-", "", $_POST['tooltip']);
			$v = '';
			if (isset($_POST['caption']))
			{
				$v = base64_decode( $_POST['caption'] );
			}
			$output = $v.base64_decode( $body );
		break;
		
		case 'char':
		
			list($sr, $name) = explode('|', $id);
			list($server, $region) = explode(':', $sr);
			
			$server = str_replace('+','-',$server);
			//$api = new WowAPI(swregion($region));
			$char = $roster->api2->fetch('character',array('name'=>$name,'server'=>$server,'fields'=>'guild,talents'));//$roster->api->Char->getCharInfo($server,$name,'1:3');
			
		
			$class=$race='';
		
			$class = $roster->api->act['id_to_class'][$char['class']];
			$race = $roster->api->act['id_to_race'][$char['race']];
			$t_icon = $t_name = '';
			foreach ($char['talents'] as $key => $spec)
				{
					if (isset($spec['selected']) && $spec['selected'] ==1 )
					{
						$t_icon = $spec['spec']['icon'];
						$t_name = $spec['spec']['name'];
					}
				}
			$output = '<div class="character-tooltip">
						<div class="cicon">
							<span class="icon-frame frame-56">
								<img src="http://'.strtolower($region).'.battle.net/static-render/'.strtolower($region).'/'.$char['thumbnail'].'" alt="" width="56" height="56" />
								<span class="frame"></span>
							</span>
						</div>
						<div class="cinfo">
							<div class="cname">'.$char['name'].'</div>
							<div class="class' . str_replace(' ','',$class) . 'txt">
									'.$char['level'].' '.$race.' '.$class.'
							</div>
							<div class="color-tooltip-alliance">
								'.$char['guild']['name'].'
							</div>
							<div class="color-tooltip-yellow">'.$char['realm'].'</div>
							<span class="clear"><!-- --></span>
							<span class="character-spec">
								<span class="icon">
									<span class="icon-frame frame-18 ">
										<img src="http://media.blizzard.com/wow/icons/18/'.$t_icon.'.jpg" alt="" class="img-middle" width="18" height="18" />
										<span class="frame"></span>
									</span>
								</span>
								<span class="name"> '.$t_name.'</span>
								<span class="clear"><!-- --></span>
							</span>
						</div>
					</div>';
		
		
		break;
	}
	
	echo $output;
	if ( isset($_GET['debugf']) )
	{
		include_once (ROSTER_BASE . 'footer.php');
	}
	exit();
	
	
	
	function api_escape( $string )
	{
		if( version_compare( phpversion(), '4.3.0', '>' ) )
		{
			return mysql_real_escape_string( $string );
		}
		else
		{
			return mysql_escape_string( $string );
		}
	}
	