<?php
/**
* 01/25/2017
* @credits: Prestashop 15-104 lines.
*
* This file contains a class for utilities.
*
*/
 error_reporting(-1); // 0 = turn off, -1 = turn on

Class Utils{

	static private $appName = "cms";

	static public function isEmpty($field)
	{
		return $field === '' OR $field === NULL OR $field === 'undefined';
	}

	/**
	* Sanitize a string
	*/
	static public function safeOutput($string, $html = false)
	{
	 	if (!$html)
			$string = @htmlentities(strip_tags($string), ENT_QUOTES, 'utf-8');
		return $string;
	}

	/**
	* Get a value from $_POST / $_GET
	* if unavailable, take a default value
	*
	* @param string $key Value key
	* @param mixed $defaultValue (optional)
	* @return mixed Value
	*/
	static public function getValue($key, $defaultValue = false)
	{
	 	if (!isset($key) OR empty($key) OR !is_string($key))
			return false;
		$ret = (isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : $defaultValue));
		if (is_string($ret) === true)
			$ret = urldecode(preg_replace('/((\%5C0+)|(\%00+))/i', '', urlencode($ret)));
		return !is_string($ret)? $ret : stripslashes($ret);
	}

	static public function getIsset($key)
	{
	 	if (!isset($key) OR empty($key) OR !is_string($key))
			return false;
	 	return isset($_POST[$key]) ? true : (isset($_GET[$key]) ? true : false);
	}

	/**
	* Check for HTML field validity (no XSS please !)
	*
	* @param string $html HTML field to validate
	* @return boolean Validity is ok or not
	*/
	static public function isCleanHtml($html)
	{
		$jsEvent = 'onmousedown|onmousemove|onmmouseup|onmouseover|onmouseout|onload|onunload|onfocus|onblur|onchange|onsubmit|ondblclick|onclick|onkeydown|onkeyup|onkeypress|onmouseenter|onmouseleave';
		return (!preg_match('/<[ \t\n]*script/ui', $html) && !preg_match('/<.*('.$jsEvent.')[ \t\n]*=/ui', $html)  && !preg_match('/.*script\:/ui', $html));
	}

	static public function encrypt($data)
	{
	    $key = '@gTSqK82GADBp.1';
	    $method = 'AES-256-ECB';
	    $ivSize = openssl_cipher_iv_length($method);
	    $iv = openssl_random_pseudo_bytes($ivSize);

	    $encrypted = openssl_encrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv);
	    
	    // For storage/transmission, we simply concatenate the IV and cipher text
	    $encrypted = base64_encode($iv . $encrypted);

	    return $encrypted;
	}

	static public function decrypt($data)
	{
	    $key = '@gTSqK82GADBp.1';
	    $method = 'AES-256-ECB';
	    $data = base64_decode($data);
	    $ivSize = openssl_cipher_iv_length($method);
	    $iv = substr($data, 0, $ivSize);
	    $decrypted = openssl_decrypt(substr($data, $ivSize), $method, $key, OPENSSL_RAW_DATA, $iv);

	    return $decrypted;
	}

	static public function jsonEncode($arr)
	{
		header('Content-Type: application/json; charset-utf8');
		if(!is_array($arr))
			return false;
	    return json_encode($arr);
	}

	static public function jsonDecode($arr)
	{
		if(!is_array($arr))
			return false;
	    return json_decode($arr);
	}

	static public function ucFirstLetter($str){
		if(!is_string($str))
			return false;
		return ucwords(strtolower($str));
	}

	static public function upperCase($str){
		if (!is_string($str))
			return false;
		return strtoupper($str);
	}

	static public function getSysDate(){
		date_default_timezone_set('Asia/Manila');
		$info = getdate();
		$date = $info['mday'];
		$month = $info['mon'];
		$year = $info['year'];
		$dat_e = $year."-".$month."-".$date;

		if(!empty($year) && !empty($month) && !empty($date)){
			return $dat_e;
		}else{
			return $dat_e;
		}
	}

	static public function getSysTime(){
		date_default_timezone_set('Asia/Manila');
		$info = getdate();
		$hour = $info['hours'];
		$min = $info['minutes'];
		$sec = $info['seconds'];
		$time = $hour.":".$min.":".$sec;
		if(!empty($hour) && !empty($min) && !empty($sec)){
			return $time;
		}else{
			return $time;
		}
	}

	static public function getBaseUrl() 
	{
	    // output: /myproject/index.php
	    $currentPath = $_SERVER['PHP_SELF']; 
	    
	    // output: Array ( [dirname] => /myproject [basename] => index.php [extension] => php [filename] => index ) 
	    $pathInfo = pathinfo($currentPath); 
	    
	    // output: localhost
	    $hostName = $_SERVER['HTTP_HOST']; 
	    
	    // output: http://
	    $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https://'?'https://':'http://';
	    
	    // return: http://localhost/myproject/
	    return $protocol.$hostName.$pathInfo['dirname']."/";
	}

	static public function getFilePath($dirName) 
	{
	    // output: /myproject/index.php
	    $currentPath = $_SERVER['PHP_SELF']; 
	    
	    // output: localhost
	    $hostName = $_SERVER['HTTP_HOST']; 
	    
	    // output: http://
	    $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https://'?'https://':'http://';
	   
	    // return: http://localhost/myproject/
	     return $protocol.$hostName."/".self::$appName."/assets/attachment/".$dirName;
	}

	static public function getFiles($dirName){
		 $tmp_dir =  $_SERVER['DOCUMENT_ROOT'].'/'.self::$appName.'/assets/attachment/'.$dirName; //Get the relative path.
		 $imgPath = self::getFilePath($dirName);
         $array_files = array();
         
         // $files = array_slice(scandir($tmp_dir),2); //Scan only 1 level directory from $tmp_dir with DESCENDING ORDER = 1.
         if(file_exists($tmp_dir)){ //Check if filename exist.
	         $files = scandir($tmp_dir);
	         $scan_files = array_slice($files,2);
		         foreach ($scan_files as $key => $value) {
		         	$array_files[$key]['uid'] = $key; //In case of error use mb_convert_encoding()
		         	$array_files[$key]['name'] = $value; //In case of error use mb_convert_encoding()
		         	$array_files[$key]['url'] = $imgPath.'/'.rawurlencode($value);
		         }
	    }
     	
         return $array_files;
	}
	static public function getFileServerPath($dirName){
		return $_SERVER['DOCUMENT_ROOT'].'/'.self::$appName.'/assets/attachment/'.$dirName;
	} 

	/**
	* Remove empty elements in array.
	*
	* @param array $arrElem to filter.
	* @return filtered array elements.
	*/
	static public function filterEmptyArr($arrElem){
		if(is_array($arrElem)){
			return array_filter($arrElem, function($v){
				return $v != '';
			});
		}
		throw new InvalidArgumentException("filterEmptyArr function only accepts array.");
		
	}

	static public function normalizeFilename($str = '')
	{
	     $str = strip_tags($str); 
	    $str = preg_replace('/[\r\n\t ]+/', ' ', $str);
	    $str = preg_replace('/[\"\*\/\:\<\>\?\'\|]+/', ' ', $str);
	    $str = strtolower($str);
	    $str = html_entity_decode( $str, ENT_QUOTES, "utf-8" );
	    $str = htmlentities($str, ENT_QUOTES, "utf-8");
	    $str = preg_replace("/(&)([a-z])([a-z]+;)/i", '$2', $str);
	    // $str = str_replace(' ', '-', $str);
	    // $str = rawurlencode($str);
	    // $str = str_replace('%', '-', $str);
	    return $str;
	}
}

