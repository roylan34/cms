<?php

function _EMAIL($recipients,$cc,$subject,$body,$identifier){
		
	require_once("class.phpmailer.php");	
	require("emailSetup.php");

	
	$unique_email = array();	
	foreach($recipients as $recipient)
	{
		foreach($recipient as $email => $name)
		{
			if(!in_array(strtolower($email),$unique_email)){
				$mail->AddAddress($email, $name);
				array_push($unique_email,strtolower($email));
			}
		}
	}
	if( count($cc)>0 ){
		$unique_email = array();	
		foreach($cc as $ccz)
		{
			foreach($ccz as $email => $name)
			{
				if(!in_array(strtolower($email),$unique_email)){
					$mail->AddCC($email, $name);
					array_push($unique_email,strtolower($email));
				}
			}
		}
	}
	
	
	//$mail->AddAddress("ryan.cardoza@delsanonline.com", "Ryan");
	$mail->Subject = $subject;
	$mail->IsHTML(true);
	$mail->Body = $body;
	$mail->Body .= "<br />";
	$mail->Body .="******************************************<br /> ";
	$mail->Body .="<div style='margin-left:50px'><b>PLEASE DON'T REPLY</b></div> ";
	$mail->Body .="******************************************<br />";
	if($mail->Send()){		
		if(!empty($identifier)){			
			return json_encode(array('status'=>'success','identifier'=>$identifier,'sendto'=>$recipients,'cc'=>$cc));
		}else{
			return json_encode(array('status'=>'success','sendto'=>$recipients,'cc'=>$cc));	
		}
	}else{
		if(!empty($identifier)){
			return json_encode(array('status'=>'emailWarning','identifier'=>$identifier));
		}else{
			return json_encode(array('status'=>'emailWarning'));	
		}
	} 
}
?>