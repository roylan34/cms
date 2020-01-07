<?php

require 'core/config.php';
require 'core/database.php';
require 'core/utils.php';
require 'core/fileUpload.php';
require 'core/file.php';
require 'model/Contract.php';
require 'model/Contract_logs.php';
require 'model/Renew.php';


$contract = new Contract();
$renew = new Renew();
$logs = new ContractLogs();

$action         = Utils::getValue('action');
$data = array(
    'comp'          => Utils::getValue('comp'),
    'category'      => Utils::getValue('category'),
    'valid_from'   => Utils::getValue('valid_from'),
    'valid_to'      => Utils::getValue('valid_to'),
    'days_to_reminds'   => Utils::getValue('days_to_reminds'),
    'notes'             => Utils::getValue('notes'),
    'id'                => Utils::getValue('id'),
    'attachment_dir'    => Utils::getValue('attachment_dir'),
    'remove_files'      => Utils::getValue('remove_files'),
    'attachment_renew_dir' => Utils::getValue('attachment_renew_dir'),
    'user_id'           => Utils::getValue('user_id')
);
switch ($action) {
    case 'add':
            $resAdd = $contract->add($data);
            if($resAdd['status'] =='success'){
                //Log action
                $logs->add(Utils::upperCase($action), $resAdd['last_id'], $data['category']);

                //Upload attachment
                $isValid = validateFileUpload($_FILES);
                if($isValid['valid_attachment_status']== 'ok'){
                    $isUpload = fileUpload($_FILES, $action, $resAdd['last_id'], null);
                    if($isUpload['attachment_status']=="success"){ //if successfully uploaded the file the last is to update the column attachment from its created folder name.
                        $contract->updateAttachmentName($resAdd['last_id'], $isUpload['attachment_uploaded']);
                        $file_res = true;
                    }
                } 

                $resAdd['attachment'] = $isValid['valid_attachment_status'];
            }
            print Utils::jsonEncode($resAdd);
        break;
    case 'renew':
        $resUpdate = $contract->updateRenew($data);
        $contract->emptyFields();

        if($resUpdate['status'] =='success'){
            $parent_dir = Utils::getValue('attachment_dir');

            //Log action
            $logs->add(Utils::upperCase($action), $data['id'], $data['category']);

            //Insert renewal history
            $resRenew = $renew->add($data);
            //Upload attachment
            $isValid = validateFileUpload($_FILES);
            if($isValid['valid_attachment_status'] == 'ok'){
 
                $isUpload = fileUpload($_FILES, $action, $resRenew['last_id'], $parent_dir);
                if($isUpload['attachment_status']=='success'){
  
                    //Update column attachment 
                    $renew->updateAttachmentName($resRenew['last_id'], $isUpload['attachment_uploaded']);
                    $attachment_dir =$isUpload['attachment_uploaded'];
                  
                    //Get attached files
                    $resUpdate['attachment_files'] = Utils::getFiles($parent_dir."/renew/".$attachment_dir);
                }
            } 
         
            $resUpdate['attachment'] = $isValid['valid_attachment_status'];
        }
        print Utils::jsonEncode($resUpdate);
        break;
    case 'edit':
        $resUpdate = $contract->update($data);
        if($resUpdate['status'] =='success'){
            $status     = $contract->getStatus($data['id']) ;

            //Log action
            $logs->add(Utils::upperCase($action), $data['id'], $data['category']);

            $existing_parent_dir = $data['attachment_dir'];
            $existing_renew_dir = $data['attachment_renew_dir'];

            if($status == 'INITIAL'){
                $sub_dir = "/".$status;
            } else { //renew
                $sub_dir = "/".$status."/".$existing_renew_dir;
            }

            //Remove attachment
            $file = json_decode(Utils::getValue('remove_files'));
            File::removeFile($existing_parent_dir.$sub_dir, $file);

            //Upload attachment
            $isValid = validateFileUpload($_FILES);
            if($isValid['valid_attachment_status'] == 'ok'){

                $isUpload = fileUpload($_FILES, $action, $data['id'], $existing_parent_dir, $status, $existing_renew_dir);
                if($isUpload['attachment_status']=='success'){
 
                    //If empty update column attachment 
                    if(Utils::isEmpty($existing_parent_dir)){
                        $contract->updateAttachmentName($data['id'], $isUpload['attachment_uploaded']);
                        $uploaded_parent_dir =$isUpload['attachment_uploaded'];
                    }
                    else{ 
                        $uploaded_parent_dir =$existing_parent_dir; 
                    }

                    //Get attached files
                    $resUpdate['attachment_files'] = Utils::getFiles($uploaded_parent_dir.$sub_dir);
                       
                }
            } 
        
            $resUpdate['attachment'] = $isValid['valid_attachment_status'];
        }
        print Utils::jsonEncode($resUpdate);   
        break;
    case 'update_status':
        $status = Utils::getValue('status');
        $res = $contract->updateStatus($data['id'], $status);
        print Utils::jsonEncode($res);
        break;
    default:
        throw new Exception("Action type not found");
        break;
}