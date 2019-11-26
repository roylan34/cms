<?php

session_start();
// session_name('MyDomainA1');
echo $_SESSION['token'];

// $expiration = time() + (1 * 24 * 60 * 60);

// echo 'Next Day: '. date('Y-m-d h:m:s', $expiration) ."\n";
// echo 'SID: '.$sid;

// $value = 'something from somewhere';

// setcookie("TestCookie1", $value);
// setcookie("TestCookie2", $value, time()+3600);  /* expire in 1 hour */
// setcookie("TestCookie3", $value, time()+3600, "/", "example.com", 1);