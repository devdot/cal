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
 * Event Table class.
 *
 * @since  1.0
 */
class CalTableArchiveEvent extends JTable {
	
	public function __construct(&$db) {
		parent::__construct('#__cal_archive', 'id', $db); //table, primary key and database object
		$this->setColumnAlias('published', 'state');
	}
	
	public function publish($pks = null, $state = 1, $userId = 0) {
		if($state == -1)
			return true; //we don't handle archiving this way
			//might do that in the future though
		return parent::publish($pks, $state, $userId);
	}
	
	
	public function store($updateNulls = true, $forceInsert = false) {
		$result = true;

		$k = $this->_tbl_keys;

		// Implement JObservableInterface: Pre-processing by observers
		$this->_observers->update('onBeforeStore', array($updateNulls, $k));

		$currentAssetId = 0;


		// If a primary key exists update the object, otherwise insert it.
		if ($this->hasPrimaryKey() and !$forceInsert) {
			$this->_db->updateObject($this->_tbl, $this, $this->_tbl_keys, $updateNulls);
		}
		else {
			$this->_db->insertObject($this->_tbl, $this, $this->_tbl_keys[0]);
		}
		

		// Implement JObservableInterface: Post-processing by observers
		$this->_observers->update('onAfterStore', array(&$result));

		return $result;
	}
}