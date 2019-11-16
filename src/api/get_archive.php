<?php

require 'core/config.php';
require 'core/database.php';
require 'core/utils.php';
require 'model/Contract.php';


$contract = new Contract();

$action = Utils::getValue('action');
$id     = Utils::getValue('id');
switch ($action) {
    case 'all':
            print Utils::jsonEncode($contract->getArchive());
        break;
    default:
        throw new Exception("Action type not found");
        break;
}


?>