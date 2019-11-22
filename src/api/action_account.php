<?php

require 'core/config.php';
require 'core/database.php';
require 'core/utils.php';
require 'model/Accounts.php';

$acc = new Accounts();
$action = Utils::getValue('action');
$data = array(
    'username'  => Utils::getValue('username'),
    'password'  => Utils::encrypt(Utils::getValue('password')),
    'firstname' => Utils::ucFirstLetter(Utils::getValue('firstname')),
    'lastname'  => Utils::ucFirstLetter(Utils::getValue('lastname')),
    'status'    => Utils::getValue('status'),
    'role'      => Utils::getValue('role'),
    'email'     => Utils::lowerCase(Utils::getValue('email'))
);

switch ($action) {
    case 'add':
        $resAdd = $acc->add($data);
        print Utils::jsonEncode($resAdd);

        break;
    default:
        throw new Exception("Action type not found");    
        break;
}

?>
