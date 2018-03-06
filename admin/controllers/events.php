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
		$this->unregisterTask('archive');
		$this->registerTask('archive', 'archive'); //new task for transfering old events to the archive
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
		
		// count the total generated children
		$generated = 0;
		$success = true;
		foreach($parents as $id) {
			//let the model do its work
			$res = $model->recurring($id);
			if($res === -1) {
				$success = false;
				$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
			}
			else
				$generated += $res;
		}
		
		if (!$success) {
			// Redirect back
			$this->setMessage($this->getError(), 'error');
		}
		elseif($generated === 0) {
			$this->setMessage(JText::_('COM_CAL_RECURRING_NO_NEW'));
		}
		else {
			//successful
			$this->setMessage(JText::sprintf('COM_CAL_N_RECURRING_EVENTS_GENERATED', $generated));
		}
		
		$this->setRedirect(
			JRoute::_(
				'index.php?option=' . $this->option . '&view=' . $this->view_list, false
			)
		);

		return $returnCode;
	}
	
	public function archive() {
		// Check for request forgeries.
		if($this->token)
			JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$model = $this->getModel('Events');
		$eventModel = $this->getModel('Event');
		
		$pks = $model->getArchivableEvents();
		
		
		
		//now move them all
		//
		if(!count($pks)) {
			$this->setMessage(JText::_('COM_CAL_ARCHIVE_NO_NEW'));
		}
		elseif(!$eventModel->moveToArchive($pks)) {
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
		}
		else {
			$this->setMessage(JText::plural('COM_CAL_ARCHIVE_EVENTS_SAVED', count($pks)));
		}

		$this->setRedirect(
			JRoute::_(
				'index.php?option=com_cal&view=archive', false
			)
		);
		return true;
		
		
	}
}