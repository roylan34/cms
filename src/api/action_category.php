<?php

require 'core/config.php';
require 'core/database.php';
require 'core/utils.php';
require 'model/Category.php';

$acc = new Category();
$action = Utils::getValue('action');
$data = array(
    'id'        => Utils::getValue('id'),
    'category'  => Utils::ucFirstLetter(Utils::getValue('category')),
    'status'    => Utils::getValue('status')
);

switch ($action) {
    case 'add':
        $resAdd = $acc->add($data);
        print Utils::jsonEncode($resAdd);

        break;
    case 'edit':
        $resUpdate = $acc->update($data);
        print Utils::jsonEncode($resUpdate);
        
        break;
    default:
        throw new Exception("Action type not found");    
        break;
}

?>
