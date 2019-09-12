<?php

require_once 'utils.php';

$action = Utils::getValue('action');
switch ($action) {

   case 'remove-file': //Remove file individually.
        $dir_name  = Utils::getValue('dirname');
        $file_name = Utils::getValue('filename');

          if($dir_name && $file_name){
            $message = array();
              if( unlink( Utils::getImageServerPath($dir_name.'/'. rawurldecode($file_name)) ) ){
                  $message[0] = 'success';
              }else{
                   $message[0] = 'failed';
              }
              print Utils::jsonEncode($message);
          }

     break;
   case 'list-files': //Get all files from attachment folder.

         $dir_name = Utils::getValue('dir_name');
         $tmp_dir =  '..'.DIRECTORY_SEPARATOR.'attachment'.DIRECTORY_SEPARATOR.$dir_name; //Get the relative path.
         $array_files = array();

         $scanned_dir = scandir($tmp_dir,1); //Scan only 1 level directory from $tmp_dir with DESCENDING ORDER = 1.
         foreach($scanned_dir as $key => $val){
               if(preg_match("/^(.)+(..)$/i", $val)){ //Check if it is file type not directory.
                  $array_files[] = urlencode($val);
               }  

         } 
         print Utils::jsonEncode($array_files);


      break;
   case 'post-list-files': //Get all files to preview.

         $dir_name = Utils::getValue('dir_name');
         $postListFiles = Utils::getImages($dir_name);
         
         print Utils::jsonEncode($postListFiles);

      break;
   case 'zip': //Download all files with zip compression.
         if(isset($_GET['download']) && !empty($_GET['download'])){
            $dir_name = $_GET['dir_name'];
            $zip = new ZipArchive();
            $filename = "../attachment_zip/".$dir_name.".zip";

               if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
                  exit("cannot open <$filename>\n");
               }

               $dir = "../attachment/{$dir_name}/";

               // Create zip
               createZip($zip,$dir);

               $zip->close();

             //Output the filepath as use to download zip file.
              echo "./assets/attachment_zip/".$dir_name.".zip";
           }
      break;
   case 'file': //Force download file individual.
            $err = 'Sorry, the file you are requesting is unavailable.';
           if (isset($_GET['file_name']) && basename($_GET['file_name']) == $_GET['file_name']) {
              $filename = $_GET['file_name'];
              $dir_name = $_GET['dir_name'];
              if (!$filename) {
                  // if variable $filename is NULL or false display the message
                  echo $err;
                  } else {
                     
                     // define the path to your download folder plus assign the file name
                     $path = "../attachment/".$dir_name."/".$filename;
                     // check that file exists and is readable
                     if (file_exists($path) && is_readable($path)) {
                     // get the file size and send the http headers
                        $size = filesize($path);
                        header('Content-Type: application/octet-stream');
                        header('Content-Length: '.$size);
                        header('Content-Disposition: attachment; filename='.$filename);
                        header('Content-Transfer-Encoding: binary');
                        // open the file in binary read-only mode
                        // display the error messages if the file can´t be opened
                        $file = @ fopen($path, 'rb');
                        if ($file) {
                           // stream the file and exit the script when complete
                           fpassthru($file);
                           exit;
                        } else {
                           echo $err;
                        }
                     } 
                     else {
                        echo $err;
                     }
                  }

            } else {
              $filename = NULL;
            }
      break;
   default:
     trigger_error($action." not exist.");
      break;
}


// Create zip
function createZip($zip,$dir){
 if (is_dir($dir)){

  if ($dh = opendir($dir)){
   while (($file = readdir($dh)) !== false){
 
    // If file
    if (is_file($dir.$file)) {
     if($file != '' && $file != '.' && $file != '..'){
 
      $zip->addFile($dir.$file,basename($file));
     }
    }else{
     // If directory
     if(is_dir($dir.$file) ){

      if($file != '' && $file != '.' && $file != '..'){

       // Add empty directory
       $zip->addEmptyDir($dir.$file,basename($file));

       $folder = $dir.$file.'/';
 
       // Read data of the folder
       createZip($zip,$folder);
      }
     }
 
    }
 
   }
   closedir($dh);
  }
 }
}






