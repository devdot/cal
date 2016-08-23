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
 * Item Model for an Event.
 *
 * @since  1.6
 */
class CalModelEvent extends JModelAdmin {
	public $typeAlias = 'com_cal.event';
	
	public function getForm($data = array(), $loadData = true) {
		// Get the form.
		$form = $this->loadForm('com_cal.event', 'event', array('control' => 'jform', 'load_data' => $loadData));
		
		if (empty($form)) {
			return false; //return false if loading the form has failed
		}
		
		return $form;
	}
	
	public function getTable($type = 'Event', $prefix = 'CalTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);//proxy loading the table
	}
	
	protected function loadFormData() {
		$app = JFactory::getApplication();

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_cal.edit.resource.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}
		
		
		return $data;
	}
	
	public function save($data) {
		
		
		return parent::save($data);
	}
	
	
}