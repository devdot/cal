<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cal
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
/**
 * Cal helper for churchtools
 *
 */
class CalHelperCT {
 
	private static $instance; //this is a singleton class
	
	private $loginStatus = false;
	private $loginCookie;
	
	private $url;
	
	public static function getInstance() {
		if(is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	
	public function __construct() {
		//save url
		$url = JComponentHelper::getParams('com_cal')->get('ct_url');
		if(empty($url)) {
			//send a warning and leave (if they don't actually use the API this shouldn't be too bad)
			JFactory::getApplication()->enqueueMessage('ChurchTools API URL is not configured.', 'warning');
			return;
		}
		$this->url = $url.'/?q='; //little speedup right here
		
		//login with the configured user
		$this->login(JComponentHelper::getParams('com_cal')->get('ct_id'), JComponentHelper::getParams('com_cal')->get('ct_token'));
	}
	
	public function query($module, $func, $params, $saveCookies = false, $ignoreLogin = false) {
		if(!($ignoreLogin || $this->loginStatus)) {
			//we shouldn't query when we're not logged in ...
			JFactory::getApplication()->enqueueMessage('ChurchTools API is not logged in.', 'error');
			return array('status' => 'fail', 'data' => 'CalHelperCT: Not logged in!');
		}
		
		//add func param to the params list, we need it there
		$params['func'] = $func;
		
		
		//build options array for http request
		$options = array(
		'http'=>array(
			'header' => 'Cookie:'.$this->loginCookie."\r\nContent-type: application/x-www-form-urlencoded\r\n",
			'method' => 'POST',
			'content' => http_build_query($params),
			)
		);
		//create the stream and get the contents
		$context = stream_context_create($options);
		$result = file_get_contents($this->url.$module.'/ajax', false, $context);

		$obj = json_decode($result);
		//catch fatal errors (error should only return upon API fails, not semantic failure)
		if ($obj->status == 'error') {
			//yes, the error message is not in data, when the status code is error ...
			throw new Exception('ChurchTools API error ('.$module.'/'.$func.'): '.$result->message, 500);
		}

		//only save cookies when we are logging in
		if($saveCookies)
			$this->saveLoginCookie($http_response_header);

		//we don't handle anything else here
		return $obj;
	}
	
	protected function saveLoginCookie($http_response) {
		foreach ($http_response as $hdr) {
			if (preg_match('/^Set-Cookie:\s*([^;]+)/', $hdr, $matches)) {
				$tmp = explode('=', $matches[1]);

				//put them into the string, no need to hold them in an array
				$this->loginCookie .= $tmp[0].'='.$tmp[1].';';
			}
		}
	}
	
	public function login($id, $token) {
		//check if we're already logged in
		if($this->loginStatus)
			return;
		
		//send the query to ct and enable cookie saving
		$result = $this->query('login', 'loginWithToken', array('id' => $id, 'token', $token), true, true);
		
		if($result->status == 'success') {
			//successfully logged in, remember that
			$this->loginStatus = true;
			return true;
		}
		
		//push the error for the user to handle (CT API returns readable errors)
		throw new Exception('ChurchTools API login failure: '.$result->data, 500);
		
		//we failed :(
		return false;
	}
}