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
 * Events controller class.
 *
 * @since  1.6
 */
class CalControllerEvents extends JControllerAdmin {
	
	protected $token; //whether the jsession token should be checked
	
	public function __construct($config = array()) {
		parent::__construct($config);
		
		if(isset($config['token']))
			$this->token = (bool) $config['token'];
		else
			$this->token = true;
		
		$this->registerTask('recurring', 'recurring'); //new task for make children of an recurring event
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
	public function getModel($name = 'Event', $prefix = 'CalModel', $config = array('ignore_request' => true)) {
		//Joomla routing will use this function to proxy my model (CalModelLocation)
		//for task locations.publish it will call CalModelLocation::publish();
		return parent::getModel($name, $prefix, $config);
	}
	
	public function recurring() {
	// Check for request forgeries.
		if($this->token)
			JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$parents = $this->getModel('Events')->getRecurringParents();
		
		$model = $this->getModel(); //Event model
		
		//0 failed, 1 ok, 2 no new
		$returnCode = 1;
		foreach($parents as $id) {
			//let the model do its work
			$res = $model->recurring($id);
			if(!$res) {
				$returnCode = 0;
				$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
			}
			if($returnCode && $res == 2)
				$returnCode == 2;
		}
		
		if (!$returnCode) {
			// Redirect back
			$this->setMessage($this->getError(), 'error');
		}
		elseif($res === 2) {
			$this->setMessage(JText::_('COM_CAL_RECURRING_NO_NEW'));
			//2 is return code for no new items
		}
		else {
			//successful
			$this->setMessage(JText::_('COM_CAL_ITEMS_SAVED'));
		}
		
		$this->setRedirect(
			JRoute::_(
				'index.php?option=' . $this->option . '&view=' . $this->view_list, false
			)
		);

		return $returnCode;
	}
}