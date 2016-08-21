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
 * Contact Table class.
 *
 * @since  1.0
 */
class CalTableLocation extends JTable {
	
	public function __construct(&$db) {
		parent::__construct('#__cal_locations', 'ID', $db);

		//JTableObserverTags::createObserver($this, array('typeAlias' => 'com_contact.contact'));
		//JTableObserverContenthistory::createObserver($this, array('typeAlias' => 'com_contact.contact'));
	}
}