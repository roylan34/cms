<?php

require 'core/config.php';
require 'core/database.php';
require 'core/utils.php';
require 'model/Company.php';


$list_comp = new Company();

print Utils::jsonEncode($list_comp->getListCompany(Utils::getValue('comp_name')));

?>