<?php
include( $addon['dir'] . 'inc/imageresize.php');
use \Eventviva\ImageResize;
class ssconfig2
{

 //global $wordings;

	var $message;
	var $message2;
	var $message3;
	var $sql_debug;
	var $id;
	var $min;
	var $max;
	var $c;
	
	function create_cat($data)
	{
		global $roster, $addon;
		
		$access = implode(":",$_POST['config_access']);
		$vals = array(
			'cat_name'		=> $data['title'],
			'cat_desc'		=> $data['desc'],
			'cat_stat'		=> '0',
			'cat_access'	=> $access,
			'cat_lock'		=> '0',
		);
		$query = "INSERT INTO `" . $roster->db->table('catagories',$addon['basename']) . "` ".$roster->db->build_query( 'INSERT' , $vals );
		//( 'null', '".$data['title']."', '".$data['desc']."','0','".$data['access']."');";
		$result = $roster->db->query($query);
		if( !$result )
		{
			$roster->set_message('DB error while creating new addon record. <br /> MySQL said:' . $roster->db->error(),$roster->locale->act['installer_error']);
		}
		else
		{
			$roster->set_message('Catagory "'.$_POST['title'].'" created');
		}
	
	}

	function delete_cat( $data )
	{
		global $roster, $addon;
		
		$msg = '';
		$query1 = 'DELETE FROM `' . $roster->db->table('catagories',$addon['basename']) . "` WHERE `id` = '" . $data['id'] . "';";
		$result1 = $roster->db->query($query1);
		if( !$result )
		{
			$roster->set_message('Database Error: ' . $roster->db->error() . '<br />SQL: ' . $query);
		}
		else
		{
			$roster->set_message('Catagory Deleted');
		}
	}

	function lock_cat( $data )
	{
		global $roster, $addon;
		
		$query = "UPDATE `" . $roster->db->table('catagories',$addon['basename']) . "` SET `cat_lock` = '1' WHERE `id` = '".$data['id']."';";
		$result = $roster->db->query($query);
		if( !$result )
		{
			$roster->set_message('Database Error: ' . $roster->db->error() . '<br />SQL: ' . $query);
		}
		else
		{
			$roster->set_message('Catagory locked');
		}
	}
	
	function unlock_cat( $data )
	{
		global $roster, $addon;
		
		$query = "UPDATE `" . $roster->db->table('catagories',$addon['basename']) . "` SET `cat_lock` = '0' WHERE `id` = '".$data['id']."';";
		$result = $roster->db->query($query);
		if( !$result )
		{
			$roster->set_message('Database Error: ' . $roster->db->error() . '<br />SQL: ' . $query);
		}
		else
		{
			$roster->set_message('Catagory locked');
		}
	}
	
	function da_cat( $data )
	{
		global $roster, $addon;
		
		$query = "UPDATE `" . $roster->db->table('catagories',$addon['basename']) . "` SET `cat_stat` = '1' WHERE `id` = '".$data['id']."';";
		$result = $roster->db->query($query);
		if( !$result )
		{
			$roster->set_message('Database Error: ' . $roster->db->error() . '<br />SQL: ' . $query);
		}
		else
		{
			$roster->set_message('Catagory deactivated');
		}
	}
	
	function a_cat( $data )
	{
		global $roster, $addon;
		
		$query = "UPDATE `" . $roster->db->table('catagories',$addon['basename']) . "` SET `cat_stat` = '1' WHERE `id` = '".$data['id']."';";
		$result = $roster->db->query($query);
		if( !$result )
		{
			$roster->set_message('Database Error: ' . $roster->db->error() . '<br />SQL: ' . $query);
		}
		else
		{
			$roster->set_message('Catagory activated');
		}
	}
	
	function get_cat()
	{
		global $roster, $addon;
		
		$query = "SELECT * FROM `" . $roster->db->table('catagories',$addon['basename']) . "` WHERE `cat_lock` = '0' AND `cat_stat` = '1' ORDER BY `id` ASC";
		$result = $roster->db->query($query);

		if( !$result )
		{
			die_quietly($roster->db->error, 'Roster Auth', __FILE__,__LINE__,$query);
		}

		$cats = array();
		while( $row = $roster->db->fetch($result) )
		{
			if( $roster->auth->getAuthorized( $row['cat_access'] ) )
			{
				$cats[$row['id']] = $row['cat_name'];
			}
		}
		return $cats;
	}
	function get_cat_full()
	{
		global $roster, $addon;
		
		$query = "SELECT * FROM `" . $roster->db->table('catagories',$addon['basename']) . "` WHERE `cat_lock` = '0' AND `cat_stat` = '1' ORDER BY `id` ASC";
		$result = $roster->db->query($query);

		if( !$result )
		{
			die_quietly($roster->db->error, 'Roster Auth', __FILE__,__LINE__,$query);
		}

		$cats = array();
		while( $row = $roster->db->fetch($result) )
		{
			$cats[$row['id']] = $row;
		}
		return $cats;
	}
	
	function build_cat_dropdown()
	{

		$cat = $this->get_cat();
		$output = '<select id="catagories" name="catagories" >';

		foreach ($cat as $acc => $a)
		{
			$output .= '<option value="'. $acc .'" >'. $a ."</option>\n";
		}
		$output .= '</select>';

		return $output;
	}
	function upload_image($desc, $caption, $catagory, $access)
	{
		global $roster, $addon;
		//echo '<pre>';print_r($_FILES);echo '</pre>';
		$base_file = $_FILES['missionsfile']['name'];
		$ext = substr($base_file, strrpos($base_file, '.')+1);
		$new_base = time();
		$new_name = $new_base.'-image.'.$ext;
		$new_thumb = $new_base.'-thumb.'.$ext;
		$new_name_b	= $new_base.'-image';//.'.$ext;
		$new_thumb_b = $new_base.'-thumb';//.'.$ext;
		//echo $base_file.' - '.$ext.'<br>';
		$target_dir = $addon['dir'].'screenshots/';
		$target_file = $target_dir . $new_name;
		$target_file_thumb = $target_dir . 'thumbs/'.$new_thumb;
		$uploadOk = 1;
		$imageFileType = $ext;//pathinfo($target_file,PATHINFO_EXTENSION);
		// Check if image file is a actual image or fake image

			$check = getimagesize($_FILES["missionsfile"]["tmp_name"]);
			if($check !== false)
			{
				//echo "File is an image - " . $check["mime"] . ".";
				$uploadOk = 1;
			}
			else
			{
				//echo "File is not an image.";
				$uploadOk = 0;
			}
		
		// Check if file already exists
		if (file_exists($target_file))
		{
			//echo "Sorry, file already exists.";
			$uploadOk = 0;
		}
		// Check file size
		if ($_FILES["missionsfile"]["size"] > 50000000)
		{
			//echo "Sorry, your file is too large.";
			$uploadOk = 0;
		}
		// Allow certain file formats
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" )
		{
			//echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed (".$imageFileType.") .";
			$uploadOk = 0;
		}
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0)
		{
			//echo "Sorry, your file was not uploaded.";
		// if everything is ok, try to upload file
		}
		else
		{
			$approved = '';
			if ($roster->auth->getAuthorized( 'roster_cp' ) OR $roster->auth->getAuthorized( 'gallery_autoapprove' ))
			{
				$approved = 'YES';
			}
			if (move_uploaded_file($_FILES["missionsfile"]["tmp_name"], $target_file))
			{
				list($w, $h) = getimagesize($target_file);
				if ($h > 1001)
				{
					$image1 = new ImageResize($target_file);
					$image1->resizeToheight(1000,true);
					$image1->save($target_file);
				}
				
				$sql2 = "INSERT INTO `".$roster->db->table('ss',$addon['basename'])."` ( `id` , `file` , `caption` , `disc` , `ext`, `catagory`, `approve` ) VALUES ( 
				NULL , 
				'".$new_base."', 
				'".$caption."', 
				'".$desc."' , 
				'".$ext."', 
				'".$catagory."', 
				'".$approved."');";
				$result2 = $roster->db->query($sql2) or die_quietly($roster->db->error(),'Database Error',basename(__FILE__),__LINE__,$sql2);
							  
				$image = new ImageResize($target_file);
				$image->resizeToWidth(275);//resize(275, 200);
				$image->crop(275, 200);
				$image->save($target_file_thumb);
			}
			else
			{
				//echo "Sorry, there was an error uploading your file.";
			}
		}
	}
}