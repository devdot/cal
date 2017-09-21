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
	
	
	public function __construct($doNothing = false) {
		if($doNothing) //for manual usage
			return;
		
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
	
	public function isLoggedIn() {
		return $this->loginStatus;
	}
	
	public function setUrl($url) {
		//shouldn't be used except for configuration
		$this->url = $url;
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
		$result = $this->query('login', 'loginWithToken', array('id' => $id, 'token' => $token), true, true);
		
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
	
	public function getAllEvents() {
		$catstring = JComponentHelper::getParams('com_cal')->get('ct_categories');
		
		//make it into an array
		$cats = explode(',', $catstring);
		$badConfig = false;
		foreach($cats as $key => $cat) {
			$cats[$key] = trim($cat);
			if(!is_numeric($cat)) {
				$badConfig = true;
				continue;
			}
			$cats[$key] = (int) $cat;
		}
		
		if($catstring === null || $badConfig) {
			JFactory::getApplication()->enqueueMessage('ChurchTools API category IDs are not correctly configurated.', 'warning');
			return array();
		}
		
		//now we get all events per getCalPerCategory
		$perCat = $this->query('churchcal', 'getCalPerCategory', array('category_ids' => $cats))->data;
		
		$events = array();
		
		//now process all this mess
		foreach($cats as $cat) {
			$category = $perCat->$cat;
			foreach($category as $obj) {
				$event = new stdClass();
				$event->id = $obj->id;
				$event->subid = false;
				$event->name = $obj->bezeichnung;
				$event->start = self::dateToJDate($obj->startdate);
				$event->end = self::dateToJDate($obj->enddate);
				$event->modified = self::dateToJDate($obj->modified_date);
				$event->category_id = $obj->category_id;
				
				$duration = $event->end->toUnix() - $event->start->toUnix();
				
				//now check if there are associated events
				if(!isset($obj->csevents)) {
					//there are none, just put in the event and finish with all of this
					//TODO filter for old events
					$events[] = $event;
					continue;
				}
				
				foreach($obj->csevents as $cs) {
					//overwrite data from this subevent
					$subevent = clone $event;
					$subevent->subid = $cs->id;
					$subevent->start = self::dateToJDate($cs->startdate);
					if(isset($cs->enddate))
						$subevent->end = self::dateToJDate($cs->enddate);
					else {
						//add onto it
						$subevent->end = new JDate($subevent->start->toUnix() + $duration);
					}
					
					//check ct event exceptions
					$subid = $subevent->subid;
					if(isset($obj->exceptions->$subid)) {
						$except = $obj->exceptions->$subid;
						if(isset($except->except_date_start)) {
							$subevent->start = new JDate($except->except_date_start);
						}
						if(isset($except->except_date_end)) {
							$subevent->end = new JDate($except->except_date_end);
						}
						elseif(isset($except->except_date_start)) {
							//no end but custom start - add onto it
							$subevent->end = new JDate($subevent->start->toUnix() + $duration);
						}
						
						//now check for delete
						if($subevent->start->toUnix() === $subevent->end->toUnix()) {
							//we just skip this event, it's been broken of recurrance
							//we might let this in an set a flag? let the user interpret?
							continue;
						}
					}
					
					//TODO filter for old events
					$events[] = $subevent;
				}
			}
			
		}
		
		return $events;
	}
}