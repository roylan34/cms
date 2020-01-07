<?php
// show error reporting
// -1 enable, 0 turnoff
error_reporting(-1);

//set ORIGIN
//purpose to blocked external request out from specified origin.
$server_name = $_SERVER['SERVER_NAME'];
$origin      = ($server_name == 'localhost' ? 'localhost:3000': 'webportal.delsanonline.com');
header('Access-Control-Allow-Origin: http://'.$origin);
header('Access-Control-Allow-Credentials: true');

// set your default time-zone
date_default_timezone_set('Asia/Manila');