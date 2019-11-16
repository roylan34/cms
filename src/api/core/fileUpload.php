<?php
require_once 'config.php';
require_once 'utils.php';

/* Uploading files.
* @exist_dir = User foldername paramater if has uploaded files.
*/
function fileUpload($files, $action, $id_add, $exist_dir, ?string $status =null, ?string $exist_renew_dir = null){
	// ############ Edit settings ##############
	$path_dir  = "../../assets/attachment/"; //specify upload directory ends with / (slash)
	$uploaded_result = array();

			switch ($action) {
                case 'add':
                    if(isset($files) && count($files) > 0 && !Utils::isEmpty($id_add)){
					    $parent_dir_name = "DOC-".$id_add;
                        $sub_dir_name = $parent_dir_name.DIRECTORY_SEPARATOR.'initial';

                        if(mkdir($path_dir.$sub_dir_name,0777,true) == true){//Create a new directory if no files uploaded yet.
                           
                            foreach ($files as $key => $value) {	  
                                //Get the file datas.
                                $tmpFilePath = $files[$key]["tmp_name"];
                                $file_size = $files[$key]["size"];
                                $file_name = Utils::normalizeFilename($files[$key]["name"]);
                                $file_type = $files[$key]["type"];
                                $file_path = $path_dir.$file_name;

                                if(move_uploaded_file($tmpFilePath, $path_dir.$sub_dir_name.DIRECTORY_SEPARATOR.$file_name)){
                                    $uploaded_result["attachment_status"] = "success";
                                    $uploaded_result["attachment_message"] = "Success! file uploaded";
                                    $uploaded_result["attachment_uploaded"] = $parent_dir_name;
                                }
                                else{
                                    $uploaded_result["attachment_status"] = "failed";
                                    $uploaded_result["attachment_message"] = "Error uploading file!";
                                    $uploaded_result["attachment_uploaded"] = "";
                                } 

                            }	
                        }
                        else{
                            $uploaded_result["attachment_status"] = "failed";
                            $uploaded_result["attachment_message"] = "Error uploading file!";
                        } 

                        return $uploaded_result;
                    }
                    else{
                        return array('attachment_status' => 'failed', 'attachment_message' =>'', "attachment_uploaded" => '' );
                    }			
                    break;
                case 'renew':
                        if(isset($files) && count($files) > 0){
                            $parent_dir_path = $path_dir.$exist_dir;
                            $renew_dir = $parent_dir_path.DIRECTORY_SEPARATOR.'renew';
                            if(!file_exists($renew_dir)){
                                mkdir($renew_dir,0777); 
                            }

                            $renew_dirname = "rn-".$id_add;

                            $sub_dir_name = $renew_dir.DIRECTORY_SEPARATOR.$renew_dirname;
                            if(!file_exists($sub_dir_name)){
                                mkdir($sub_dir_name,0777); 
                            }
                        }
                        foreach ($files as $key => $value) {		  
                            //Get the file datas.
                            $tmpFilePath = $files[$key]["tmp_name"];
                            $file_name = Utils::normalizeFilename($files[$key]["name"]);

                                if(move_uploaded_file($tmpFilePath, $sub_dir_name.DIRECTORY_SEPARATOR.$file_name)){
                                    $uploaded_result["attachment_status"] = "success";
                                    $uploaded_result["attachment_message"] = "Success! file uploaded";
                                    $uploaded_result["attachment_uploaded"] = $renew_dirname;
                                }
                                else{
                                    $uploaded_result["attachment_status"] = "failed";
                                    $uploaded_result["attachment_message"] = "Error uploading file!";					    			
                                } 					    		
                                
                        }
                        return $uploaded_result;


                    break;				
				case 'edit':
                    if(isset($files) && count($files) > 0){
                        $parent_dir_name = '';
                        $initial_dir = '';
                        //Check if has saved directory name
						if(!Utils::isEmpty($exist_dir)){ 
                            $parent_dir_name = $exist_dir;
					    }
					  	else{
                            $parent_dir_name = "DOC-".$id_add;
                        }

                        $parent_dir_path = $path_dir.$parent_dir_name;
                        //Create parent directory if not exist.
                        if(!file_exists($parent_dir_path)){
                            mkdir($parent_dir_path,0777); 
                        }

                        //Check the status.
                        if($status == 'INITIAL'){;
                            $initial_dir = $parent_dir_path.DIRECTORY_SEPARATOR.'initial';
                            if(!file_exists($initial_dir)){
                                mkdir($initial_dir,0777); 
                            }
                        }
                        else if($status == 'RENEW'){ 
                            $initial_dir = $parent_dir_path.DIRECTORY_SEPARATOR.'renew'.DIRECTORY_SEPARATOR.$exist_renew_dir;
                        }
                        $sub_dir_name = $initial_dir;


                        foreach ($files as $key => $value) {		  
                            //Get the file datas.
                            $tmpFilePath = $files[$key]["tmp_name"];
                            $file_name = Utils::normalizeFilename($files[$key]["name"]);

                                if(move_uploaded_file($tmpFilePath, $sub_dir_name.DIRECTORY_SEPARATOR.$file_name)){
                                    $uploaded_result["attachment_status"] = "success";
                                    $uploaded_result["attachment_message"] = "Success! file uploaded";
                                    $uploaded_result["attachment_uploaded"] = $parent_dir_name;
                                }
                                else{
                                    $uploaded_result["attachment_status"] = "failed";
                                    $uploaded_result["attachment_message"] = "Error uploading file!";					    			
                                } 					    		
                                
                        }
                        return $uploaded_result;
                    }

					break;
				default:
					trigger_error($action. 'action not exist.');
					break;
			}

}

//Validate files supported.
function validateFileUpload($files){
		if(isset($files) && count($files) > 0){
	 		$total = count($files);
	 		$uploaded_result = array();
	 		$uploaded_message = "";
		  	$fileSize = 0;
		  	$fileName = "";
		  	$fileType = "";

			 	// Loop through each file
		  		  foreach ($files as $key => $value) {				  
		  		  	  //Get the temp file path
					  $fileSize = $files[$key]["size"];
					  $fileName = $files[$key]["name"];
					  $fileType = $files[$key]["type"];


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
				    
				if(Utils::isEmpty($uploaded_message)){
					//Show error message if validation equals condition above.
					$uploaded_result["valid_attachment_status"] = "ok";
					$uploaded_result["valid_attachment_message"] = "";
				}
				else{
                    $uploaded_result["valid_attachment_status"] = "invalid";
					$uploaded_result["valid_attachment_message"] = $uploaded_message;
				}
        }
        else{
            $uploaded_result["valid_attachment_status"] = "empty";
        }
        return $uploaded_result;
}