<?php

require 'core/config.php';
require 'core/database.php';
require 'core/utils.php';
require 'model/Notification.php';
require 'phpemailer/emailFunction.php';



$action = Utils::getValue('action');
$notify = new Notification();

switch ($action) {
    case 'remind_expiring': /************* NOTIFY ACCOUNT MANAGER VIA EMAIL ********/

        $res = array();
        $res = $notify->getContractExpiring();

        if(count($res) > 0){

            $cc = array();
            foreach ($res as $key => $value) {
                $mailBody = "";
                $mailSubject ="[CMS] Contract Expiration Notice";
                $mailBody .="<p>Hi there.</p>";
                $mailBody .="<p>Here's the list of customer details nearing of its expiry date.</p>";
                $mailBody .="<table border='1' cellpadding='5'><thead><tr><th>Company</th><th>Expiration</th><th>Category</th></tr></thead><tbody>";
                foreach($value['details'] as $d_key => $details){
                    $mailBody .= "<tr>";
                    $mailBody .= "<td>{$details['comp']}</td>";
                    $mailBody .= "<td>{$details['expiration']}</td>";
                    $mailBody .= "<td>{$details['cat_name']}</td>";
                    $mailBody .= "</tr>";
                }
                $mailBody .="</tbody></table>";
                _EMAIL($value['email'],$cc,$mailSubject,$mailBody,"");
            }
        };

      
        break;
    default:
        throw new Exception("Action type not found.");
        break;
}


?>