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
    'status'      => Utils::getValue('status'),
    'user_id'      => Utils::getValue('user_id')
);
switch ($action) {
    case 'all':
            print Utils::jsonEncode($contract->getArchive($data));
        break;
    default:
        throw new Exception("Action type not found");
        break;
}


?>