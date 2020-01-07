<?php

require 'core/config.php';
require 'core/database.php';
require 'core/utils.php';
require 'model/Accounts.php';

$acc = new Accounts();
$action = Utils::getValue('action');
$data = array(
    'id'        => Utils::getValue('id'),
    'current_pass'      => Utils::getValue('current_pass'),
    'new_pass'          => Utils::getValue('new_pass'),
    'confirm_new_pass'  => Utils::getValue('confirm_new_pass')
);

switch ($action) {
    case 'change-pass':

        $resChange = $acc->changePass($data);
        print Utils::jsonEncode($resChange);
    
        break;
    default:
        throw new Exception("Action type not found");    
        break;
}

?>
