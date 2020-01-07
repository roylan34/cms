<?php

require 'jwt.php';

session_start();

//Get client cookies token.
$token = $_COOKIE['token'];

//Get stored session token
$sessionSign = explode(".", $_SESSION['token']);

if(!JWT::verify($token, $sessionSign[2])) { 
    echo "Unauthorized access.";
    exit;
}