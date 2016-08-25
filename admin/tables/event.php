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
 * Event Table class.
 *
 * @since  1.0
 */
class CalTableEvent extends JTable {
	
	public function __construct(&$db) {
		parent::__construct('#__cal_events', 'id', $db); //table, primary key and database object
		$this->setColumnAlias('published', 'state');
	}
	
	public function publish($pks = null, $state = 1, $userId = 0) {
		if($state == -1)
			return true; //we don't handle archiving this way
			//might do that in the future though
		return parent::publish($pks, $state, $userId);
	}
	
	public function store($updateNulls = false) {
		$date   = JFactory::getDate()->toSql();
		$userId = JFactory::getUser()->id;

		$this->modified = $date;
		$this->modified_by = $userId;
		
		if($this->id == 0) {
			//a new event
			$this->created = $date;
			$this->created_by = $userId;
		}
		
		// Verify that the alias is unique
		$table = JTable::getInstance('Event', 'CalTable');

		if ($table->load(array('alias' => $this->alias, 'catid' => $this->catid)) && ($table->id != $this->id || $this->id == 0))
		{
			$this->setError(JText::_('COM_CAL_ERROR_UNIQUE_ALIAS'));

			return false;
		}
		
		return parent::store($updateNulls);
	}
}