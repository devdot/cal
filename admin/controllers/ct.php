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
class CalControllerCt extends JControllerLegacy {
	
	public function __construct($config = array()) {
		parent::__construct($config);
		$this->registerTask('import', 'import'); //task for auto import
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
	public function getModel($name = 'CT', $prefix = 'CalModel', $config = array('ignore_request' => true)) {
		//Joomla routing will use this function to proxy my model (CalModelLocation)
		//for task locations.publish it will call CalModelLocation::publish();
		return parent::getModel($name, $prefix, $config);
	}
	
	public function import() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		//dump it to the model
		$model = $this->getModel();
		$res = $model->import();
		
		if (!$res) {
			// Redirect back to the edit screen.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(JRoute::_('index.php?option=com_cal&view=ct', false));

			return false;
		}
		else {
			//successful
			$this->setMessage(JText::_('COM_CAL_ITEMS_SAVED'));
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_cal&view=ct', false));
	}
}