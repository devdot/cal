<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cal
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Cal ChurchTools controller class.
 *
 * @since  1.6
 */
class CalControllerCtTokenGenerator extends JControllerLegacy {
	
	public function __construct($config = array()) {
		parent::__construct($config);
		
		//$this->registerTask('tokenGenerator', 'tokenGenerator'); //new task for the tokenGenerator
		$this->registerTask('apply', 'save');
	}
	
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The name of the model.
	 * @param   string  $prefix  The prefix for the PHP class name.
	 * @param   array   $config  Array of configuration parameters.
	 *
	 * @return  JModelLegacy
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'CtTokenGenerator', $prefix = 'CalModel', $config = array('ignore_request' => true)) {
		//Joomla routing will use this function to proxy my model (CalModelLocation)
		//for task locations.publish it will call CalModelLocation::publish();
		return parent::getModel($name, $prefix, $config);
	}
	
	public function save() {
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app   = JFactory::getApplication();
		$model = $this->getModel();
		$data  = $this->input->post->get('jform', array(), 'array');
		
		
		$form = $model->getForm($data, false);

		if (!$form) {
			$app->enqueueMessage($model->getError(), 'error');

			return false;
		}

		// Test whether the data is valid.
		$validData = $model->validate($form, $data);
		
		// Attempt to save the data.
		if (!$model->save($validData)) {
			// Save the data in the session.
			$app->setUserState('com_cal.edit.cttokengenerator.data', $validData);

			// Redirect back to the edit screen.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(JRoute::_('index.php?option=com_cal&view=cttokengenerator', false));

			return false;
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_cal&view=cttokengenerator', false));
		
		return true;
	}
}