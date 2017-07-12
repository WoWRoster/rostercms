<?php

include_once($addon['inc_dir'] . 'functions.lib.php');
$func = New mainFunctions;

switch ($method)
{
	case 'load':
		
		$dir = $addon['image_path'].'news/thumbs';
		$files = scandir($dir);

		$output = '';
		$ret= array();
		foreach($files as $file)
		{
			if($file == "." || $file == "..")
				continue;
			$filePath=$dir."/".$file;
			$path_parts = pathinfo($file);
			$f = explode('-',$path_parts['filename']);
			$details = array();
			$details['name']=$file;
			$details['path']=$filePath;
			$details['file']=$f[0];
			$details['size']=filesize($filePath);
			$ret[] = $details;

			$output .= '<div class="card card-inverse">
  <img class="card-img" src="'.$filePath.'" alt="Card image">
  <div class="card-img-overlay">
    <button type="button" class="btn btn-info btn-md" id="isel" data-image="'.$f[0].'" data-src="'.$filePath.'" data-target="h_imagel">select</button>
  </div>
</div>';
		}

		echo $output;//json_encode($ret);

	break;
	
	case 'delete':
		
		$file_name = '';
		$target_dir = $addon['dir'].'images/news/';
		
		$ext = substr($file_name, strrpos($file_name, '.')+1);
		$e = explode('-',$file_name);
		$new_base = $e[0];
		$new_thumb 		= $new_base.'-thumb.'.$ext;
		$new_name 		= $new_base.'-image.'.$ext;
		$target_file = $target_dir . $new_name;
		$target_file_thumb = $target_dir . 'thumbs/'.$new_thumb;
		
		if (file_exists($target_file)) 
		{
			unlink($target_file);
		}
		echo "Deleted File ".$new_name."<br>";
		
		if (file_exists($target_file_thumb)) 
		{
			unlink($target_file_thumb);
		}
		
		echo "Deleted File ".$new_thumb."<br>";

		
	
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
			$error =$_FILES["myfile"]["error"];
			//You need to handle  both cases
			//If Any browser does not support serializing of multiple files using FormData() 
			if(!is_array($_FILES["myfile"]["name"])) //single file
			{
				$fileName = $_FILES["myfile"]["name"];
				//move_uploaded_file($_FILES["myfile"]["tmp_name"],$output_dir.$fileName);
				$ret = $func->upload_image($_FILES["myfile"]["tmp_name"], $fileName);
			}
			else  //Multiple files, file[]
			{
				$fileCount = count($_FILES["myfile"]["name"]);
				for($i=0; $i < $fileCount; $i++)
				{
					$fileName = $_FILES["myfile"]["name"][$i];
					//move_uploaded_file($_FILES["myfile"]["tmp_name"][$i],$output_dir.$fileName);
					$ret = $func->upload_image($_FILES["myfile"]["tmp_name"][$i], $fileName);
				}

			}
			echo json_encode($ret);
		}
 
	break;
}