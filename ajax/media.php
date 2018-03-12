<?php

switch ($method)
{
	case 'load':
		
		$query = "SELECT * FROM `" . $roster->db->table('media') . "` ;";

		$result = $roster->db->query($query);

		if (!$result)
		{
			die_quietly('Could not fetch buttons from database .  MySQL said: <br />' . $roster->db->error(),'Roster',__FILE__,__LINE__,$query);
		}

		$cat = array();
		$output = '';
		while ($row = $roster->db->fetch($result,SQL_ASSOC))
		{
			$output .= '<div class="gallery_product col-lg-2 col-md-2 col-sm-2 col-xs-6 filter '.$row['post_parent'].'">				
				  <img class="img-responsive" src="'.ROSTER_MEDIA_URL.$row['post_title'].'-thumb.'.$row['post_mime_type'].'" alt="Card image">
				  <div class="card-img-overlay">
					<button type="button" class="btn btn-info btn-md" id="isel" data-id="'.$row['ID']'" data-image="'.$row['post_name'].'.'.$row['post_mime_type'].'" data-src="'.$row['post_name'].'.'.$row['post_mime_type'].'" data-target="h_imagel">Select</button>
					<button type="button" class="btn btn-info btn-md" id="idel" data-id="'.$row['ID']'" data-target="h_imagel">Delete</button>
				  </div>
			</div>';
		}

		echo $output;

	break;
	
	case 'delete':
		
		$query = "SELECT * FROM `" . $roster->db->table( 'media' ) . "` WHERE `ID` = '".$_GET['id']."';";

		$result = $roster->db->query( $query );

		if ( !$result )
		{
			die_quietly('Could not fetch buttons from database .  MySQL said: <br />' . $roster->db->error(),'Roster',__FILE__,__LINE__,$query);
			break;
		}

		$row = $roster->db->fetch( $result );
			
		$file_name = '';
		$target_dir = ROSTER_MEDIA;
		
		$new_base = $row['post_name'];
		$new_thumb 		= $new_base.'-thumb.'.$row['post_mime_type'];
		$new_name 		= $new_base.'-image.'.$row['post_mime_type'];
		$target_file = $target_dir . $new_name;
		$target_file_thumb = $target_dir . $new_thumb;
		$output = '';
		if (file_exists($target_file)) 
		{
			unlink($target_file);
			$output .= "Deleted File ".$new_name."<br>"
		};
		
		if (file_exists($target_file_thumb)) 
		{
			unlink($target_file_thumb);
			$output .= "Deleted File ".$new_thumb."<br>";
		}
		
		echo $output;
	
	break;
	
	case 'upload':
		if(isset($_FILES["myfile"]))
		{
			$ret = array();

			//	This is for custom errors;	
			/*	$custom_error= array();
			$custom_error['jquery-upload-file-error']="File already exists";
			echo json_encode($custom_error);
			die();
			*/
			//d($_FILES);
			$error = $_FILES["myfile"]["error"];
			//You need to handle  both cases
			//If Any browser does not support serializing of multiple files using FormData() 
			if(!is_array($_FILES["myfile"]["name"])) //single file
			{
				$fileName = $_FILES["myfile"]["name"];
				//move_uploaded_file($_FILES["myfile"]["tmp_name"],$output_dir.$fileName);
				$ret = $roster->media->upload_image($_FILES["myfile"]["tmp_name"], $fileName);
			}
			else  //Multiple files, file[]
			{
				$fileCount = count($_FILES["myfile"]["name"]);
				for($i=0; $i < $fileCount; $i++)
				{
					$fileName = $_FILES["myfile"]["name"][$i];
					//move_uploaded_file($_FILES["myfile"]["tmp_name"][$i],$output_dir.$fileName);
					$ret = $roster->media->upload_image($_FILES["myfile"]["tmp_name"][$i], $fileName);
				}

			}
			echo json_encode($ret);
		}
 
	break;
}