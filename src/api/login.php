<?php

require 'core/config.php';
require 'core/database.php';
require 'core/utils.php';
require 'model/Auth.php';

$auth = new Auth();
$action = Utils::getValue('action');

switch ($action) {
    case 'login':
        $data = array(
            'username'  => Utils::getValue('username'),
            'password' => Utils::encrypt(Utils::getValue('password'))
        );
    
        $resAuth = $auth->login($data);
        print Utils::jsonEncode($resAuth);

        break;
    case 'logout':

    
        break;
    default:
        throw new Exception("Action type not found");    
        break;
}


?>
