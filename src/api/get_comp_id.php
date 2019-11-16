<?php

require 'core/config.php';
require 'core/database.php';
require 'core/utils.php';
require 'model/Company.php';


$comp_name = new Company();

print Utils::jsonEncode($comp_name->getCompanyNameById(Utils::getValue('id')));

?>