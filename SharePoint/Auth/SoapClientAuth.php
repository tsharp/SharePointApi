<?php
namespace SharePoint\Auth;

/**
 *    SoapClientAuth for accessing Web Services protected by HTTP authentication
 *    Author: tc
 *    Last Modified: 04/08/2011
 *    Update: 14/03/2012 - Fixed issue with CURLAUTH_ANY not authenticating to NTLM servers
 *    Download from: http://tcsoftware.net/blog/
 *
 *    Copyright (C) 2011  tc software (http://tcsoftware.net)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * SoapClientAuth
 * The interface and operation of this class is identical to the PHP SoapClient class (http://php.net/manual/en/class.soapclient.php)
 * except this class will perform HTTP authentication for both SOAP messages and while downloading WSDL over HTTP and HTTPS.
 * Provide the options login and password in the options array of the constructor.
 *
 * @author tc
 * @copyright Copyright (C) 2011 tc software
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://php.net/manual/en/class.soapclient.php
 * @link http://tcsoftware.net/
 */
class SoapClientAuth extends \SoapClient {

	public $Username = NULL;
	public $Password = NULL;

	function cache_wsdl($wsdl) {
    	$ch = curl_init($wsdl);
    	curl_setopt($ch, CURLOPT_HEADER, 0);
    	curl_setopt($ch, CURLOPT_USERPWD, $this->Username . ':' . $this->Password);
    	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    	// curl_setopt($ch, CURLOPT_VERBOSE, true);
    	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     	curl_setopt($ch, CURLOPT_UNRESTRICTED_AUTH , 1);
		// Get headers too with this line
		curl_setopt($ch, CURLOPT_HEADER, 0);
		// For Fiddler Debugging
		// curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1:8888');
		$result = curl_exec($ch);		
		$info = curl_getinfo($ch);
		curl_close($ch);
		
		if ($info['http_code'] >= 400)
		      die("HTTP ERROR {$info['http_code']}");
		      
		$temp_file = tempnam(sys_get_temp_dir(), 'PCMedicsSharePoint');
		file_put_contents($temp_file, $result);
		return $temp_file;
	}
	
	/**
	 *
	 * @param string $wsdl
	 * @param array $options
	 */
	public function __construct($wsdl, $options = NULL) {
		$wrappers = stream_get_wrappers();

		stream_wrapper_unregister('http');
		stream_wrapper_register('http', '\SharePoint\Auth\StreamWrapperHttpAuth');

		if (in_array("https", $wrappers)) {
			stream_wrapper_unregister('https');
			stream_wrapper_register('https', '\SharePoint\Auth\StreamWrapperHttpAuth');
		}

		if ($options) {
			$this->Username = $options['login'];
            \SharePoint\Auth\StreamWrapperHttpAuth::$Username = $this->Username;
			$this->Password = $options['password'];
            \SharePoint\Auth\StreamWrapperHttpAuth::$Password = $this->Password;
		}

		$wsdl_file = $this->cache_wsdl($wsdl);
		parent::SoapClient($wsdl_file, ($options ? $options : array()));
		unlink($wsdl_file);

		stream_wrapper_restore('http');
		if (in_array("https", $wrappers)) stream_wrapper_restore('https');

	}

	/**
	 * @param string $request
	 * @param string $location
	 * @param string $action
	 * @param int $version
	 * @param int $one_way
	 *
	 * @return mixed|string
	 * @throws \Exception
	 */
	public function __doRequest($request, $location, $action, $version, $one_way = 0) {

		$headers = array(
			'User-Agent: PHP-SOAP',
			'Content-Type: text/xml; charset=utf-8',
			'SOAPAction: "' . $action . '"',
			
			'Expect: 100-continue',
			'Connection: Keep-Alive'
		);

		$this->__last_request_headers = $headers;
		$ch = curl_init($location);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $request);

		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_FAILONERROR, FALSE);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
		curl_setopt($ch, CURLOPT_UNRESTRICTED_AUTH , 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

		curl_setopt($ch, CURLOPT_USERPWD, $this->Username . ':' . $this->Password);
		curl_setopt($ch, CURLOPT_SSLVERSION, 3);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
		curl_setopt($ch, CURLOPT_CERTINFO, TRUE);

		// For Fiddler Debugging
		// curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1:8888');
		
		$response = curl_exec($ch);

		if (($info = curl_getinfo($ch)) && $info['http_code'] == 200) {
			return $response;
		}
		else {
			if ($info['http_code'] == 401) {
				throw new \Exception ('Access Denied', 401);
			}
			else {
				if (curl_errno($ch) != 0) {
					throw new \Exception(curl_error($ch), curl_errno($ch));
				}
				else {
					throw new \Exception('Error', $info['http_code']);
				}
			}
		}
	}
}