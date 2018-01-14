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
 * Item Model for an Event.
 *
 * @since  1.6
 */
class CalModelCtImportRule extends JModelAdmin {
	public $typeAlias = 'com_cal.ctimportrule';
	
	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 */
	public function getItem($pk = null) {
		if ($item = parent::getItem($pk)) { //articletext is a combination of fulltext and introtext
			//from com_content
			//Unpack the data for the rules
			//$registry = new Registry;
			//$registry->loadString($item->rules);
			//$item->images = $registry->toArray();
		}
		return $item;
	}
	
	public function getForm($data = array(), $loadData = true) {
		// Get the form.
		$form = $this->loadForm('com_cal.ctimportrule', 'ctimportrule', array('control' => 'jform', 'load_data' => $loadData));
		
		if (empty($form)) {
			return false; //return false if loading the form has failed
		}
		
		return $form;
	}
	
	public function getTable($type = 'CtImportRule', $prefix = 'CalTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);//proxy loading the table
	}
	
	protected function loadFormData() {
		$app = JFactory::getApplication();

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_cal.edit.ctimportrule.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}
		
		return $data;
	}
}