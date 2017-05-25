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
 * CT Import Rule Table class.
 *
 * @since  1.0
 */
class CalTableCT_ImportRule extends JTable {
	
	public function __construct(&$db) {
		parent::__construct('#__cal_ct_import', 'id', $db); //table, primary key and database object
		$this->setColumnAlias('published', 'state');
	}
	
	public function publish($pks = null, $state = 1, $userId = 0) {
		if($state == -1)
			return true; //no archiving (there isn't any GUI for this anyways)
		return parent::publish($pks, $state, $userId);
	}
	
	public function store($updateNulls = false) {
		//do nothing special (yet)
		return parent::store($updateNulls);
	}
}