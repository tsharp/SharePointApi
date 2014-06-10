<?php

	function test_auth($location, $Username, $Password, $debug_id = false) {
		$ch = curl_init($location);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_FAILONERROR, FALSE);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($ch, CURLOPT_USERPWD, $Username . ':' . $Password);
		curl_setopt($ch, CURLOPT_SSLVERSION, 3);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
		curl_setopt($ch, CURLOPT_CERTINFO, TRUE);
		// if ($debug_it) {
		  curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1:8888');
		// }
		
		return $response = curl_exec($ch);
	}
	
	    function test_curl($url, $auth) {
    	$cookie_file_path = 'C:\Temp\Cookies.txt';
    	
    	$ch = curl_init($url);
    	curl_setopt($ch, CURLOPT_HEADER, 0);
    	curl_setopt($ch, CURLOPT_USERPWD, $auth);
    	// curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    	curl_setopt($ch, CURLOPT_VERBOSE, true);
    	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path);
     	// curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
     	curl_setopt($ch, CURLOPT_UNRESTRICTED_AUTH , 1);
		// Get headers too with this line
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1:8888');
		$result = curl_exec($ch);
		echo $result;
		// Get cookie
		// preg_match('/^Set-Cookie:\s*([^;]*)/mi', $result, $m);
		
		$info = curl_getinfo($ch);
		
		if ($info['http_code'] >= 400)
		      die("HTTP ERROR {$info['http_code']}");
    }
    // echo test_curl('http://localhost/_vti_bin/Lists.asmx?WSDL', '');
	
	// echo test_auth('', '', '', true)
	
    
    error_reporting(E_ALL);
	ini_set("error_log", "c:/temp/php-error.log");
	
    require_once('SharePointApi.php');
    require_once('Auth\SoapClientAuth.php');
    // require_once('Auth\SharePointOnlineAuth.php');
    require_once('Auth\StreamWrapperHttpAuth.php');

    echo 'Before SharePoint ...';
    // $sp = new \SharePoint\SharePointAPI('','','', 'NTLM');
    // print_r($sp->read('Applicants'));
  
?>