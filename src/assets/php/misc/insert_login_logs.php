<?php
/**
* 06/15/2017
*
* This file contains inserting the Login/logout of user and account manager.
*
*/


require_once '../database.php';
require_once '../utils.php';

	$id_name     = Utils::getValue('user_id');
	$description = Utils::getValue('description');
	$date_log    = Utils::getSysDate().' '.Utils::getSysTime();  
	$conn        = Database::getInstance();

	if($id_name){
		$conn->insertQuery('tbl_accounts_login_logs','id_name,date_log,description,ip_address',
			  '"'.$id_name.'",
			  "'.$date_log.'",
			  "'.$description.'",
			  "'.get_client_ip().'"');
	}


function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}




