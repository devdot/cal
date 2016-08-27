<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cal
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
/**
 * Cal Resource heler
 *
 * @param   string  $submenu  The name of the active view.
 *
 * @return =  void
 *
 * @since   1.6
 */
abstract class CalHelperResources {
	/**
	 * Configure the Linkbar.
	 *
	 * @return = String
	 */
 
	public static function type($type) {
		$s = '';
		switch((int) $type) {
			case 0:
				$s = 'COM_CAL_RESOURCE_TYPE_OBJECT';
				break;
			case 1:
				$s = 'COM_CAL_RESOURCE_TYPE_ROOM';
				break;
			case 2:
				$s = 'COM_CAL_RESOURCE_TYPE_SERVICE';
				break;
			case 3:
				$s = 'COM_CAL_RESOURCE_TYPE_USER';
				break;
			case 4:
				$s = 'COM_CAL_RESOURCE_TYPE_USERGROUP';
				break;
		}
		return JText::_($s);
	
	}
}