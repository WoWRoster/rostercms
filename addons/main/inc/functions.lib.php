<?php

class mainFunctions
{

	var $block = array();
	var $msg = array();

	function newsUPDATE($post,$html)
	{
		global $roster, $addon;

		$img = null;
			if ( isset($_POST['h_imagel']) )
			{
				$img = $_POST['h_imagel'];
			}
			
		$news = preg_replace('#<script(.*?)>(.*?)</script>#is', '',  $post['news']);
		$news = nl2br ($news);
		$query = "UPDATE `" . $roster->db->table('news',$addon['basename']) . "` SET "
					. "`poster` = '" . $post['author'] . "', "
					. "`title` = '" . $post['title'] . "', "
					. "`news_type` = '" . $post['news_type'] . "', "
					. "`text` = '" . $news . "', "
					. "`html` = '" . $html . "', "
					. "`img` = '" . $img . "' "
					. "WHERE `news_id` = '" . $post['id'] . "';";

		if( $roster->db->query($query) )
		{
			$roster->set_message($roster->locale->act['news_edit_success']);
		}
		else
		{
			$roster->set_message('There was a DB error while editing the article.', '', 'error');
			$roster->set_message('<pre>' . $roster->db->error() . '</pre>', 'MySQL Said', 'error');
		}
		return;
	}

	function newsADD($post,$html)
	{
		global $roster, $addon;

		if( $roster->auth->getAuthorized('news_can_post') )
		{
			$img = null;
			if ( isset($_POST['h_imagel']) )
			{
				$img = $_POST['h_imagel'];
			}
			
			$news = preg_replace('#<script(.*?)>(.*?)</script>#is', '',  $post['news']);
			$news = nl2br ($news);
			$query = "INSERT INTO `" . $roster->db->table('news',$addon['basename']) . "` SET "
						. "`poster` = '" . $roster->auth->user['user_display'] . "', "
						. "`poster_id` = '".$roster->auth->user['id']."', "
						. "`poster_ip` = '".$_SERVER['REMOTE_ADDR']."', "
						. "`poster_ipx` = '00.00.00', "
						. "`title` = '" . $post['title'] . "', "
						. "`news_type` = '" . $post['news_type'] . "', "
						. "`text` = '" . $news . "', "
						. "`html` = '" . $html . "', "
						. "`img` = '" . $img . "', "
						. "`date` = '". $roster->db->escape(gmdate('Y-m-d H:i:s')). "';";

			if( $roster->db->query($query) )
			{
				$roster->set_message($roster->locale->act['news_add_success']);
			}
			else
			{
				$roster->set_message('There was a DB error while adding the article.', '', 'error');
				$roster->set_message('<pre>' . $roster->db->error() . '</pre>', 'MySQL Said', 'error');
			}
		}
		else
		{
			echo 'crap i broke';
		}
	}

	function upload_slide($upfile)
	{
		global $roster, $addon;
		
		$ext = substr($_FILES['b_image']['name'], strrpos($_FILES['b_image']['name'], '.')+1);
		$filename = hash_file('md5', $_FILES['b_image']['tmp_name']).'.'.$ext;
		echo $filename.'<br>';
		$target_path = $addon['dir'] .'images'. DIR_SEP . $filename;
		$path = $addon['dir'] .'images'. DIR_SEP . 'slider'. DIR_SEP;
		$slider = $path .'slider-'. $filename;
		$thumb = $path .'thumb-'. $filename;

		if(move_uploaded_file($_FILES['b_image']['tmp_name'], $target_path))
		{
			
			$image = new ImageResize($target_path);
			$image->resizeToHeight(420);//resize(275, 200);
			$image->crop(850, 420);
			$image->save($slider);
			
			$image1 = new ImageResize($target_path);
			$image1->resizeToHeight(47);//resize(275, 200);
			$image1->crop(100, 47);
			$image1->save($thumb);

			$query = "INSERT INTO `" . $roster->db->table('slider', $addon['basename']) . "` SET "
				. "`b_title` = '" . $_POST['b_title'] . "', "
				. "`b_desc` = '" . $_POST['b_desc'] . "', "
				. "`b_url` = '" . $_POST['b_url'] . "', "
				. "`b_video` = '" . $_POST['b_video'] . "', "
				. "`b_image` = '" . $filename . "';";

			if( $roster->db->query($query) )
			{
				$roster->set_message(sprintf($roster->locale->act['slider_add_success'], $_FILES['b_image']['name']));
			}
			else
			{
				unlink($target_path);
				unlink($slider);
				unlink($thumb);
				$roster->set_message($roster->locale->act['slider_error_db'], '', 'error');
				$roster->set_message('<pre>' . $roster->db->error() . '</pre>', 'MySQL Said', 'error');
			}
		}
		else
		{
			$roster->set_message(sprintf($roster->locale->act['slider_file_error'], $target_path), $roster->locale->act['b_add'], 'error');
		}
	
	}
	function upload_image($file,$name = '')
	{
		global $roster, $addon;

		$base_file = $file;
		$ext = substr($name, strrpos($name, '.')+1);
		$new_base = hash_file('md5', $base_file);
		// small
		$new_thumb 		= $new_base.'-thumb.'.$ext;
		$new_thumb_b 	= $new_base.'-thumb';
		// med
		$new_name 		= $new_base.'-image.'.$ext;
		$new_name_b		= $new_base.'-image';
		// large

		$target_dir = $addon['dir'].'images/news/';
		$target_file = $target_dir . $new_name;
		$target_file_thumb = $target_dir . 'thumbs/'.$new_thumb;
		$uploadOk = 1;
		$imageFileType = $ext;
		// Check if image file is a actual image or fake image

			$check = getimagesize($file);
			if($check !== false)
			{
				$this->set_msg('File is an image - ' . $check["mime"] . '.', '', 'error');
				$uploadOk = 1;
			}
			else
			{
				$this->set_msg('File is not an image.', '', 'error');
				$uploadOk = 0;
			}
		
		// Check if file already exists
		if (file_exists($target_file))
		{
			return array("jquery-upload-file-error" =>'Sorry, file already exists');
			$uploadOk = 0;
		}
		// Check file size
		if ($_FILES["h_image"]["size"] > 50000000)
		{
			return array("jquery-upload-file-error" =>'Sorry, your file is too large.');
			$uploadOk = 0;
		}
		// Allow certain file formats
		if($imageFileType != "jpg" && $imageFileType != "jpeg" )
		{
			return array("jquery-upload-file-error" =>'Sorry, only JPG, JPEG files are allowed ('.$imageFileType.').');
			$uploadOk = 0;
		}
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0)
		{
			$this->set_msg('Image not uploaded 1.', '', 'error');
		// if everything is ok, try to upload file
		}
		else
		{
			if (move_uploaded_file($file, $target_file))
			{
				$image = new ImageResize($target_file);
				//$image->resizeToHeight(300);
				$image->resizeToHeight(200);//resize(275, 200);
				$image->crop(275, 200);
				$image->save($target_file_thumb);
				
				list($w, $h) = getimagesize($target_file);
				if ($w < 850)
				{
					$image1 = new ImageResize($target_file);
					$image1->resizeToWidth(850,true);
					$image1->save($target_file);
				}
				
				$this->set_msg('image uploaded');
				//return $new_base;
			}
			else
			{
				$this->set_msg('Image not uploaded 2.', '', 'error');
			}
		}
		return $this->get_msg();
	}
	function set_msg($msg)
	{
		$this->msg[] = $msg;
	}
	function get_msg()
	{
		return implode('<br>', $this->msg);
	}
	function makeUSERmenu( $sections )
	{
		global $roster;

		// Save current locale array
		// Since we add all locales for button name localization, we save the current locale array
		// This is in case one addon has the same locale strings as another, and keeps them from overwritting one another
		$localetemp = $roster->locale->wordings;

		$menue = array();

		// Add all addon locale files
		foreach( $roster->addon_data as $addondata )
		{
			foreach( $roster->multilanguages as $lang )
			{
				$roster->locale->add_locale_file(ROSTER_ADDONS . $addondata['basename'] . DIR_SEP . 'locale' . DIR_SEP . $lang . '.php',$lang);
			}
		}

		$section = "'" . implode("','",array_keys($sections)) . "'";

		// --[ Fetch button list from DB ]--
		$query = "SELECT `mb`.*, `a`.`basename` "
			   . "FROM `" . $roster->db->table('menu_button') . "` AS mb "
			   . "LEFT JOIN `" . $roster->db->table('addon') . "` AS a "
			   . "ON `mb`.`addon_id` = `a`.`addon_id` "
			   . "WHERE `a`.`addon_id` IS NULL "
			   . "OR `a`.`active` = 1;";

		$result = $roster->db->query($query);

		if (!$result)
		{
			die_quietly('Could not fetch buttons from database .  MySQL said: <br />' . $roster->db->error(),'Roster',__FILE__,__LINE__,$query);
		}

		while ($row = $roster->db->fetch($result,SQL_ASSOC))
		{
			$palet['b' . $row['button_id']] = $row;
		}

		$roster->db->free_result($result);

		// --[ Fetch menu configuration from DB ]--
		$query = "SELECT * FROM `" . $roster->db->table('menu') . "` WHERE `section` IN (" . $section . ") ORDER BY `config_id`;";

		$result = $roster->db->query($query);

		if (!$result)
		{
			die_quietly('Could not fetch menu configuration from database. MySQL said: <br />' . $roster->db->error(),'Roster',__FILE__,__LINE__,$query);
		}

		while($row = $roster->db->fetch($result,SQL_ASSOC))
		{
			$data[$row['section']] = $row;
		}

		$roster->db->free_result($result);

		$page = array();
		$arrayButtons = array();

		foreach( $sections as $name => $visible )
		{
			if( isset($data[$name]) )
			{
				$page[$name] = $data[$name];
			}
		}

		// --[ Parse DB data ]--
		foreach( $page as $name => $value )
		{
			$config[$name] = explode(':',$value['config']);
			foreach( $config[$name] as $pos=>$button )
			{
				if( isset($palet[$button]) )
				{
					$arrayButtons[$name][$pos] = $palet[$button];
				}
			}
		}

		foreach( $arrayButtons as $id => $page )
		{
			switch( $id )
			{
				case 'char':
					$panel_label = $roster->data['name'] . ' @ ' . $roster->data['region'] . '-' . $roster->data['server'];
					break;

				default:
					$panel_label = (isset($roster->locale->act['menupanel_' . $id]) ? sprintf($roster->locale->act['menu_header_scope_panel'], $roster->locale->act['menupanel_' . $id]) : '');
					break;
			}

			$menue[$panel_label][] = array(
				'ID' => $id,
				'OPEN' => !$sections[$id],
				'LABEL' => $panel_label
				);

			foreach( $page as $button )
			{
				if( !empty($button['icon']) )
				{
					if( strpos($button['icon'],'.') !== false )
					{
						$button['icon'] = ROSTER_PATH . 'addons/' . $button['basename'] . '/images/' . $button['icon'];
					}
					else
					{
						$button['icon'] = $roster->config['interface_url'] . 'Interface/Icons/' . $button['icon'] . '.' . $roster->config['img_suffix'];
					}
				}
				else
				{
					$button['icon'] = $roster->config['interface_url'] . 'Interface/Icons/inv_misc_questionmark.' . $roster->config['img_suffix'];
				}

				if( !in_array($button['scope'],array('util','user','realm','guild','char')) || $button['addon_id'] == 0 )
				{
					$button['url'] = makelink($button['url']);
				}
				elseif( substr($button['url'],0,7) != 'http://')
				{
					$button['url'] = makelink($button['scope'] . '-' . $button['basename'] . (empty($button['url']) ? '' : '-' . $button['url']));
				}

				$button['title'] = isset($roster->locale->act[$button['title']]) ? $roster->locale->act[$button['title']] : $button['title'];
				if( strpos($button['title'],'|') )
				{
					list($button['title'],$button['tooltip']) = explode('|',$button['title'],2);
					$button['tooltip'] = ' ' . makeOverlib($button['tooltip'],$button['title'],'',1,'',',WRAP');
				}
				else
				{
					$button['tooltip'] = ' ' . makeOverlib($button['title']);
				}

				$menue[$button['scope']][] = array(
					'TOOLTIP'  => $button['tooltip'],
					'ICON'     => $button['icon'],
					'NAME'     => $button['title'],
					'SCOPE'    => $button['scope'],
					'BASENAME' => $button['basename'],
					'U_LINK'   => $button['url']
					);
			}
		}

		// Restore our locale array
		unset($localetemp);
		return $menue;
	}
	
	function image_resize($src, $dst, $width, $height, $crop=0)
	{
		global $roster;

		if (!file_exists($src)) return $roster->set_message('image "'.$src.'" not found!');
		
		if(!list($w, $h) = getimagesize($src)) return "Unsupported picture type!";

		$type = strtolower(substr(strrchr($src,"."),1));
		if($type == 'jpeg') $type = 'jpg';
		switch($type)
		{
			case 'bmp': $img = imagecreatefromwbmp($src); break;
			case 'gif': $img = imagecreatefromgif($src); break;
			case 'jpg': $img = imagecreatefromjpeg($src); break;
			case 'png': $img = imagecreatefrompng($src); break;
			default : return "Unsupported picture type!";
		}

		// resize
		if($crop)
		{
			if($w < $width or $h < $height) return "Picture is too small!";
			$ratio = max($width/$w, $height/$h);
			$h = $height / $ratio;
			$x = ($w - $width / $ratio) / 2;
			$w = $width / $ratio;
		}
		else
		{
			if($w < $width and $h < $height) return "Picture is too small!";
			$ratio = min($width/$w, $height/$h);
			//$width = $w * $ratio;
			//$height = $h * $ratio;
			$x = 0;
		}

		$new = imagecreatetruecolor($width, $height);

		// preserve transparency
		if($type == "gif" or $type == "png")
		{
			imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
			imagealphablending($new, false);
			imagesavealpha($new, true);
		}

		imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);

		switch($type)
		{
			case 'bmp': imagewbmp($new, $dst); break;
			case 'gif': imagegif($new, $dst); break;
			case 'jpg': imagejpeg($new, $dst); break;
			case 'png': imagepng($new, $dst); break;
		}
		return true;
	}
	/**
	 * Build the list of plugins to include based on roster scope and if plugins have plugins
	 *
	 *
	 */

	function _build_select($array, $name, $selected=null)
	{
		global $roster;
		
		$output = ' <select id="'.$name.'" name="'.$name.'" >';

		foreach ($array as $a => $b)
		{
			$output .= '<option value="'. $a .'" '. ($a == $selected ? 'selected' : '') .'>'. ucfirst ($b) ."</option>\n";
		}
		$output .= '</select>';
		
		return $output;
	}
	
	function _initPlugins()
	{
		global $roster, $addon;
		$plugins = $roster->plugin_data;
		
		$blocks = array();
		$query = "SELECT * FROM `" . $roster->db->table('blocks',$addon['basename']) . "` ORDER BY `block_location` ASC;";
		$result = $roster->db->query($query);
		while( $row = $roster->db->fetch($result) )
		{
				$blocks[$row['block_name']] = $row;
		}

		foreach ($blocks as $name => $data)
		{
			if (isset($plugins[$name]['active']) && $data['block_name'] == $plugins[$name]['basename'])
			{
				$xplugin = getplugin($name);
						
				foreach( $roster->multilanguages as $lang )
				{
					$roster->locale->add_locale_file($xplugin['locale_dir'] . $lang . '.php', $lang);
				}
				$plugin['scope'] = explode('|',$plugins[$name]['scope']);
				
				if (in_array( $roster->scope, $plugin['scope'] ) )
				{

					$classfile = ROSTER_PLUGINS . $name . DIR_SEP . $name . '.php';
					require($classfile);
					$pluginstuff = new $name($xplugin);

					$blocks[$name] = array(
						'name'   => $roster->locale->act[$name]['title'],//$pluginstuff->fullname,
						'output' => $pluginstuff->output,
						'icon'   => $pluginstuff->icon
					);
					unset($pluginstuff);
				}
			}
			else
			{
				$query = "DELETE FROM `" . $roster->db->table('blocks',$addon['basename']) . "` WHERE `block_name` = '".$name."';";
				$result = $roster->db->query($query);
				unset($blocks[$name]);
			}
		}
		
		$this->block = $blocks;
		return true;

	}
	
	function truncate($text, $length, $suffix = '&hellip;', $isHTML = true) {
		$i = 0;
		$simpleTags=array('br'=>true,'hr'=>true,'input'=>true,'image'=>true,'link'=>true,'meta'=>true);
		$tags = array();
		if($isHTML){
			preg_match_all('/<[^>]+>([^<]*)/', $text, $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
			foreach($m as $o){
				if($o[0][1] - $i >= $length)
					break;
				$t = substr(strtok($o[0][0], " \t\n\r\0\x0B>"), 1);
				// test if the tag is unpaired, then we mustn't save them
				if($t[0] != '/' && (!isset($simpleTags[$t])))
					$tags[] = $t;
				elseif(end($tags) == substr($t, 1))
					array_pop($tags);
				$i += $o[1][1] - $o[0][1];
			}
		}

		// output without closing tags
		$output = substr($text, 0, $length = min(strlen($text),  $length + $i));
		// closing tags
		$output2 = (count($tags = array_reverse($tags)) ? '</' . implode('></', $tags) . '>' : '');

		// Find last space or HTML tag (solving problem with last space in HTML tag eg. <span class="new">)
		$p1 = preg_split('/<.*>| /', $output, -1, PREG_SPLIT_OFFSET_CAPTURE);
		$p2 = end($p1);
		$pos = end($p2);
		//$pos = (int)end(end(preg_split('/<.*>| /', $output, -1, PREG_SPLIT_OFFSET_CAPTURE)));
		// Append closing tags to output
		$output.=$output2;

		// Get everything until last space
		$one = substr($output, 0, $pos);
		// Get the rest
		$two = substr($output, $pos, (strlen($output) - $pos));
		// Extract all tags from the last bit
		preg_match_all('/<(.*?)>/s', $two, $tags);
		// Add suffix if needed
		if (strlen($text) > $length) { $one .= $suffix; }
		// Re-attach tags
		$output = $one . implode($tags[0]);

		//added to remove  unnecessary closure
		$output = str_replace('</!-->','',$output); 

		return $output;
	}

}
