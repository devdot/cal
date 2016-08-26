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
 * Controller for a single event
 *
 * @since  1.6
 */
class CalControllerEvent extends JControllerForm {
	
	public function __construct($config = array()) {
		parent::__construct($config);
		
		$this->registerTask('recurring', 'recurring'); //new task for make children of an recurring event
	}
	
	public function recurring($key = null, $urlVar = null) {
	// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app   = JFactory::getApplication();
		$lang  = JFactory::getLanguage();
		$model = $this->getModel();
		$table = $model->getTable();
		$context = "$this->option.edit.$this->context";
		$task = $this->getTask();

		$id = (int) array_shift($this->input->get('cid'));

		// Attempt to save the data.
		$res = $model->recurring($id);
		if (!$res) {
			// Redirect back to the edit screen.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToListAppend(), false
				)
			);

			return false;
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
				'index.php?option=' . $this->option . '&view=' . $this->view_list
				. $this->getRedirectToListAppend(), false
			)
		);

		return true;
	}
}