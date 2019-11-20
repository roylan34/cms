<?php

require 'core/config.php';
require 'core/database.php';
require 'core/utils.php';
require 'model/Contract.php';


$contract = new Contract();

$action = Utils::getValue('action');
$id     = Utils::getValue('id');
$data   = array(
    'comp'        => Utils::getValue('comp'),
    'category'    => Utils::getValue('category'),
    'valid_from'  => Utils::getValue('valid_from'),
    'valid_to'    => Utils::getValue('valid_to'),
    'status'      => Utils::getValue('status')
);
switch ($action) {
    case 'all':
            print Utils::jsonEncode($contract->getCurrent($data));
        break;
    case 'edit':
            $status = $contract->getStatus($id);
            $contract->emptyFields();
            $res = $contract->getCurrentById($id);
            //Get attached files
            $attachmentDir = $res['aaData'][0]['attachment'];
            if(count($res['aaData']) > 0 && !Utils::isEmpty($attachmentDir) ){
                if($status == 'initial'){
                    $res['aaData'][0]['attachment_files'] = Utils::getFiles($attachmentDir."/".$status);
                } else { //renew
                    $res['aaData'][0]['attachment_files'] = Utils::getFiles($attachmentDir."/".$status."/".$res['aaData'][0]['ren_attachment']);
                }
            }
            print Utils::jsonEncode($res);
        break;
    case 'renew':
            $res = $contract->getCurrentById($id);
            print Utils::jsonEncode($res);
        break;
    default:
        throw new Exception("Action type not found");
        break;
}


?>