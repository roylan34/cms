<?php

/**
* Developed by: Roelan Geil Eroy
* JSON Web Tokens
*
**/

Class JWT{


	protected static $key = 'ligklp178y';
	protected static $header_flags = array(
		'typ' => 'JWT',
		'alg' => 'HS256'
	);


	 /**
     * Decode a string with URL-safe Base64.
     *
     * @param string $input A Base64 encoded string
     *
     * @return string A decoded string
     */
    public static function urlsafeB64Decode($input)
    {
    	if(empty($input)) {
    		throw new InvalidArgumentException('Argument must not empty.');
    	}
    	$b64 = base64_decode($input);

    	$url = strtr($b64, '-_', '+/');

        return $url; 
    }
    /**
     * Encode a string with URL-safe Base64.
     *
     * @param string $input The string you want encoded
     *
     * @return string The base64 encode of what you passed in
     */
    public static function urlsafeB64Encode($input)
    {

    	if(empty($input)) {
    		throw new InvalidArgumentException('Argument must not empty.');
    	}
    	$b64 = base64_encode($input);

    	$url = strtr($b64, '+/', '-_');

        return rtrim($url,"=");
    }
    public static function hash_equals($str1, $str2) {
	    if(strlen($str1) != strlen($str2)) {
	      	return false;
	    } else {
	      	$res = $str1 ^ $str2;
	      	$ret = 0;
	      	for($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);
	      return !$ret;
	    }
  	}
    /**
    * Generate tokens with a default sha256 algorithm.
    */
	public static function encode($input){

		$b64_header 	= self::urlsafeB64Encode(json_encode(self::$header_flags));
		$b64_payload	= self::urlsafeB64Encode(json_encode($input));

		//Create signature
		$hash = hash_hmac('sha256', $b64_header.'.'.$b64_payload, self::$key, true);

		if($hash===false){
			throw new Exception("Failed to encrypt data.");
		}
		$b64_sign = self::urlsafeB64Encode($hash);

		return $b64_header.'.'.$b64_payload.'.'.$b64_sign;
	}
	public static function verify($clientToken, $sessionSign){

    	if(empty($clientToken) && empty($sessionSign)) {
    		throw new InvalidArgumentException('Arguments must not empty.');
    	}
    	$clientToken_split	= explode('.',$clientToken);

    	if(count($clientToken_split) != 3){
    		throw new Exception("Invalid token.");
    	}

    	$b64_header 	= $clientToken_split[0];
    	$b64_payload 	= $clientToken_split[1];
    	$msg = hash_hmac('sha256', "$b64_header.$b64_payload", self::$key, true);

    	$b64_sign = self::urlsafeB64Encode($msg);

    		if(self::hash_equals($sessionSign, $b64_sign)){
    			return true; //TRUE, if matches the session signature and client signature.
    		}
    		else{
    			header("HTTP/1.1 401 Unauthorized"); //Unauthorized the user to access the api.
                return false;
    		}

    	

	}
}

