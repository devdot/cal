<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cal
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * Model for the tokenGenerator
 *
 * @since  1.6
 */
class CalModelCtTokenGenerator extends JModelForm {
	//public $typeAlias = 'com_cal.ct_tokenGenerator';
	
	public function getForm($data = array(), $loadData = true) {
		// Get the form.
		$form = $this->loadForm('com_cal.ct_tokenGenerator', 'ct_tokenGenerator', array('control' => 'jform', 'load_data' => $loadData));
		
		if (empty($form)) {
			return false; //return false if loading the form has failed
		}
		
		
		return $form;
	}
	
	protected function loadFormData() {
		$app = JFactory::getApplication();

		$data = $app->getUserState('com_cal.edit.ct_tokenGenerator.data', array());
		
		return $data;
	}
	
	public function save($data) {
		//get an uninitialized helper
		JLoader::register('CalHelperCT', JPATH_COMPONENT . '/helpers/ct.php');
		$ct = new CalHelperCT(true);
		
		$ct->setUrl($data['url'].'/?q=');
		
		//try to login
		$loginData = array('email' => $data['email'], 'password' => $data['password']);
		$result = $ct->query('login', 'login', $loginData, false, true);
		
		if($result->status == 'fail') {
			//login failed, show it to the user
			JFactory::getApplication()->enqueueMessage('Login failed: '.$result->data, 'error');
			return false;
		}
		
		//now generate the token
		$result = $ct->query('login', 'getUserLoginToken', $loginData, false, true);
		if($result->status == 'fail') {
			//login failed, show it to the user
			JFactory::getApplication()->enqueueMessage('Token generation failed: '.$result->data, 'error');
			return false;
		}
		$token = $result->data->token;
		$id = $result->data->id;
	
		JFactory::getApplication()->enqueueMessage('Received token: '.$result->data->token);
		
		//check login token (really shouldn't ever fail but ...)
		$result = $ct->query('login', 'loginWithToken', array('token' => $token, 'id' => $id), false, true);
		if($result->status == 'fail') {
			//login failed, show it to the user
			JFactory::getApplication()->enqueueMessage('Login with generated token failed: '.$result->data, 'error');
			return false;
		}
		
		//now onto saving the newly aquired data
		$params = JComponentHelper::getParams('com_cal');
		$params->set('ct_url', $data['url']);
		$params->set('ct_token', $token);
		$params->set('ct_id', $id);

		// Save the parameters
		$componentid = JComponentHelper::getComponent('com_cal')->id;
		$table = JTable::getInstance('extension');
		$table->load($componentid);
		$table->bind(array('params' => $params->toString()));

		// check for error
		if (!$table->check()) {
			JFactory::getApplication()->enqueueMessage('Error when saving: '.$table->getError(), 'error');
			return false;
		}
		// Save to database
		if (!$table->store()) {
			JFactory::getApplication()->enqueueMessage('Error when saving: '.$table->getError(), 'error');
			return false;
		}
		
		JFactory::getApplication()->enqueueMessage('Saved login configuration.');
		
		//now test the newly saved connection (will display errors)
		$test = CalHelperCT::getInstance();
		
		
		return true;
	}
}