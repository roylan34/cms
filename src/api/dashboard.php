<?php

require 'core/config.php';
require 'core/database.php';
require 'core/utils.php';
require 'model/Contract.php';


$action = Utils::getValue('action');
$data = array(
    'valid_to' => Utils::getValue('valid_to'),
    'user_id'  => Utils::getValue('user_id'),
);
$con = new Contract();

switch ($action) {
    case 'calendar':
        $res = $con->getCalendarForecast($data);
        print Utils::jsonEncode($res);
        
        break;
    default:
        throw new Exception('Action type not found.');
        break;
}
