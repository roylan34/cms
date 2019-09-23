<?php

require 'core/config.php';
require 'core/utils.php';
require 'model/Contract.php';


$contract = new Contract();

print Utils::jsonEncode($contract->getCurrent());

?>