<?php
require_once 'utils.php';

/* Uploading files.
* @exist_dir = User foldername paramater if has uploaded files.
*/
function fileUpload($_FILES, $action, $exist_dir){
	// ############ Edit settings ##############
	$upload_dir  = "../attachment/"; //specify upload directory ends with / (slash)
	$uploaded_result = array();

			switch ($action) {
				case 'add_files':
					$count_dir   = count(scandir($upload_dir)) - 2; //Subtracted 2, to exclude the . , .. (dots) directory.
					$new_dir_name = "PO-".$count_dir;

					mkdir($upload_dir.$new_dir_name,0777,true); //Create a new directory if no files uploaded yet.

					  		foreach ($_FILES as $key => $value) {		  
				  		  	  	//Get the file datas.
							  	$tmpFilePath = $_FILES[$key]["tmp_name"];
							  	$file_size = $_FILES[$key]["size"];
							  	$file_name = Utils::normalizeFilename($_FILES[$key]["name"]);
							  	$file_type = $_FILES[$key]["type"];
								$file_path = $upload_dir.$file_name;

						    		if(move_uploaded_file($tmpFilePath, $upload_dir.$new_dir_name."/".$file_name)){
						    			$uploaded_result["attachment_status"] = "true";
						    			$uploaded_result["attachment_message"] = "Success! file uploaded";
						    			$uploaded_result["attachment_uploaded"] = $new_dir_name;
						    		}
						    		else{
						    			$uploaded_result["attachment_status"] = "false";
						    			$uploaded_result["attachment_message"] = "Error uploading file!";
						    		} 

						   	}		
						   	print Utils::jsonEncode($uploaded_result);			
					break;				
				case 'update_files':
					  $filename_exist = '';

						if(!Utils::isEmpty($exist_dir)){ //Check if has a argument directory name that determine has already file uploaded if no created directory.
					    	$filename_exist = $exist_dir;
					    }
					  	else{
					  		$count_dir   = count(scandir($upload_dir)) - 2; 
							$filename_exist = "PO-".$count_dir;

							mkdir($upload_dir.$filename_exist,0777,true); 

					   		//print Utils::jsonEncode(array("attachment_status" => "false", "attachment_message" => $exist_dir. " directory exist"));
					   	}


					   		foreach ($_FILES as $key => $value) {		  
				  		  	  	//Get the file datas.
							  	$tmpFilePath = $_FILES[$key]["tmp_name"];
							  	$file_name = Utils::normalizeFilename($_FILES[$key]["name"]);

						    		if(move_uploaded_file($tmpFilePath, $upload_dir.$filename_exist."/".$file_name)){
						    			$uploaded_result["attachment_status"] = "true";
						    			$uploaded_result["attachment_message"] = "Success! file uploaded";
						    			$uploaded_result["attachment_uploaded"] = $filename_exist;
						    		}
						    		else{
						    			$uploaded_result["attachment_status"] = "false";
						    			$uploaded_result["attachment_message"] = "Error uploading file!";						    			
						    		} 					    		
						    		
						   	}
						   print Utils::jsonEncode($uploaded_result);

					break;
				default:
					trigger_error($action. 'action not exist.');
					break;
			}

}

//Validate files supported.
function validateFileUpload($_FILES){
		if(isset($_FILES) && count($_FILES) > 0){
	 		$total = count($_FILES);
	 		$uploaded_result = array();
	 		$uploaded_message = "";
		  	$fileSize = 0;
		  	$fileName = "";
		  	$fileType = "";

			 	// Loop through each file
		  		  foreach ($_FILES as $key => $value) {				  
		  		  	  //Get the temp file path
					  $fileSize = $_FILES[$key]["size"];
					  $fileName = $_FILES[$key]["name"];
					  $fileType = $_FILES[$key]["type"];


					  //allowed file size Server side check
					  if ($fileSize > 2097152){
					  	 $uploaded_message .= $fileName." File is too big!,</br> it should be less than 2 MB. \n";
					  }

					 //allowed file type Server side check
					  switch (strtolower($fileType)) {
						case 'image/png':
						case 'image/jpeg':
						case 'application/pdf':
						break;
					  	default:	  	   
					  	   $uploaded_message .= $fileName." unsupported file type. \n";
					  }					  
				}
				    
				if(!Utils::isEmpty($uploaded_message)){
					//Show error message if validation equals condition above.
					$uploaded_result["attachment_status"] = "false";
					$uploaded_result["attachment_message"] = $uploaded_message;
					print json_encode($uploaded_result);
					return false;
				}
				else{
					//Upload files				
					return true;
				}
			  
		}

}


if(isset($_FILES) && count($_FILES) > 0){
	//File attachment upload
	if($res =(validateFileUpload($_FILES) == false)){
		return false;
	} 
	else{
		$action = Utils::getValue('action');
		$file_name = Utils::getValue('file_name');
		fileUpload($_FILES,$action,$file_name);
	}
}
else{
	print Utils::jsonEncode(array('attachment_status' => 'true', 'attachment_message' =>'', "attachment_uploaded" => '' ));
}