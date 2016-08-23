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
}