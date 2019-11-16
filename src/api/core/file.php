<?php

require_once 'utils.php';

class File{

    static function removeFile($dir_name, $arr_files){ //Remove one or more individually.

        if(empty($dir_name) && !is_array($arr_files) && count($arr_files) <= 0){
            throw new Exception('Argument error');
        }
        
        function cb($val, $key, $prefix){
            unlink( Utils::getFileServerPath($prefix.'/'. $val) );
        }
        array_walk( $arr_files, 'cb', $dir_name);
    }
    static function getListFiles(){ //Get all files to preview.

        $dir_name = Utils::getValue('dir_name');
        $postListFiles = Utils::getFiles($dir_name);
         
        return $postListFiles;
   }
    static function zip(){ //Download all files with zip compression.
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
                return "./assets/attachment_zip/".$dir_name.".zip";
        }
    }
    static function download(){ //Force download file individual.
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
    }

}







