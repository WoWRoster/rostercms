<?php
/**
 * WoWRoster.net WoWRoster
 *
 * Roster Media Lib
 *
 *
 * @copyright  2002-2011 WoWRoster.net
 * @license    http://www.gnu.org/licenses/gpl.html   Licensed under the GNU General Public License v3.
 * @package    WoWRoster
 * @subpackage media
*/

if( !defined('IN_ROSTER') )
{
	exit('Detected invalid access to this file!');
}

/**
 * Roster Media Lib
 *
 * @package    WoWRostermediaErrorControl
 */
include( ROSTER_LIB . 'imageresize.php');
use \Eventviva\ImageResize;
class roster_media
{
	var $block = array();
	var $msg = array();
	
	function get_all_media()
	{
		global $roster;
		
		$queryb = "SELECT * FROM `" . $roster->db->table('media') . "` WHERE `b_active` = '1' ORDER BY `id` DESC;";
		$resultsb = $roster->db->query($queryb);
		$total = $roster->db->num_rows($resultsb);

		$images = array();
		while( $rowb = $roster->db->fetch($resultsb) )
		{
			$images[$rowb['addon_id']] = $rowb;
		}
		
		return $images;
	
	}
	
	
	
	function upload_image($file,$name = '',$cat='core')
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

		$target_dir = ROSTER_MEDIA;
		$target_file = $target_dir . $new_name;
		$target_file_thumb = $target_dir . $new_thumb;
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
		$allowed = array('jpg','jpeg','png','gif');
		if( !in_array( $imageFileType, $allowed ) ) //$imageFileType != "jpg" && $imageFileType != "jpeg" )
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
				
				$query = "INSERT INTO `" . $roster->db->table('media') . "` SET "
						. "`post_date` = '". $roster->db->escape(gmdate('Y-m-d H:i:s')). "', "
						. "`post_title` = '".$new_base."', "
						. "`post_name` = '".$new_name_b."', "
						. "`post_parent` = '".$cat."', "
						. "`post_type` = 'image', "
						. "`post_mime_type` = '".$imageFileType."'; ";

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
	
}