<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Item Model for a Location.
 *
 * @since  1.6
 */
class CalModelLocation extends JModelAdmin {
	public $typeAlias = 'com_cal.location';
	
	public function getForm($data = array(), $loadData = true) {
		
		//do I need that? I'm not getting a field from there I guess...
		//JForm::addFieldPath('JPATH_ADMINISTRATOR/components/com_users/models/fields');
		
		// Get the form.
		$form = $this->loadForm('com_cal.location', 'location', array('control' => 'jform', 'load_data' => $loadData));
		
		//I don't even know what the following does
		if (empty($form))
		{
			return false;
		}
		
		return $form;
	}
	
	public function getTable($type = 'Location', $prefix = 'CalTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}
	
	protected function loadFormData() {
		$app = JFactory::getApplication();

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_cal.edit.location.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}
	
	
	
}