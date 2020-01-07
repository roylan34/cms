<?php

require 'core/config.php';
require 'core/database.php';
require 'core/utils.php';
require 'model/Contract_logs.php';


$logs = new ContractLogs();
$res = $logs->getLogs(Utils::getValue('id'));
print Utils::jsonEncode($res);

?>