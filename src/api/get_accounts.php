<?php

require 'core/config.php';
require 'core/database.php';
require 'core/utils.php';
require 'model/Accounts.php';

$action = Utils::getValue('action');
$acc = new Accounts();

switch ($action) {
    case 'all':
        $res = $acc->getAccounts();
        print Utils::jsonEncode($res);
        
        break;
    default:
        throw new Exception('Action type not found.');
        break;
}


?>