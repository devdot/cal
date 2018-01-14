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
 * CT Import Status controller class.
 *
 * @since  1.6
 */
class CalControllerCtImportStatus extends JControllerAdmin {
	
	protected $token; //whether the jsession token should be checked
	
	public function __construct($config = array()) {
		parent::__construct($config);
		$this->registerTask('import', 'import'); //task for manual import
	}
	
	public function import() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		//get the sent ids
		$cid = $this->input->get('cid');
		
		$keys = array();
		foreach($cid  as $str) {
			//convert cid back to id and subid
			$ex = explode('_', $str, 2);
			$ex[0] = (int) $ex[0];
			if($ex[1] === '')
				$ex[1] = false;
			else
				$ex[1] = (int) $ex[1];
			
			if(!isset($keys[$ex[0]])) {
				$keys[$ex[0]] = array();
			}
			if($ex[1] != false)
				$keys[$ex[0]][$ex[1]] = true;
		}
		
		//dump it to the model
		$model = $this->getModel('CtImportStatus', 'CalModel');
		$res = $model->import($keys);
		
		if (!$res) {
			// Redirect back to the edit screen.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));

			return false;
		}
		else {
			//successful
			$this->setMessage(JText::_('COM_CAL_ITEMS_SAVED'));
		}
		
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
		
		
	}
}