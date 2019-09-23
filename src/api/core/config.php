<?php
// show error reporting
error_reporting(-1);

//set ORIGIN
//purpose to blocked external request out from the web app.
$server_name = $_SERVER['SERVER_NAME'];
$origin      = ($server_name == 'localhost' ? 'localhost:3000': 'webportal.delsanonline.com');
header('Access-Control-Allow-Origin: http://'.$origin);

// set your default time-zone
date_default_timezone_set('Asia/Manila');