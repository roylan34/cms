<?php

require 'core/config.php';
require 'core/database.php';
require 'core/utils.php';
require 'model/Accounts.php';

$acc = new Accounts();
$action = Utils::getValue('action');
$data = array(
    'id'        => Utils::getValue('id'),
    'username'  => Utils::getValue('username'),
    'firstname' => Utils::ucFirstLetter(Utils::getValue('firstname')),
    'lastname'  => Utils::ucFirstLetter(Utils::getValue('lastname')),
    'status'    => Utils::getValue('status'),
    'role'      => Utils::getValue('role'),
    'email'     => Utils::lowerCase(Utils::getValue('email'))
);

switch ($action) {
    case 'add':
        $data['password'] = Utils::encrypt(Utils::getValue('password'));
        $resAdd = $acc->add($data);
        print Utils::jsonEncode($resAdd);

        break;
    case 'edit':
        if(Utils::getValue('password')){  $data['password'] = Utils::encrypt(Utils::getValue('password')); }

        $resUpdate = $acc->update($data);
        print Utils::jsonEncode($resUpdate);
    
        break;
    default:
        throw new Exception("Action type not found");    
        break;
}

?>
