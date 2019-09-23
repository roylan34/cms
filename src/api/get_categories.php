<?php

require 'core/config.php';
require 'core/utils.php';
require 'model/Categories.php';


$contract = new Categories();

print Utils::jsonEncode($contract->getAllCategories());

?>