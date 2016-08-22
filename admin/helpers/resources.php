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
 * @return  void
 *
 * @since   1.6
 */
abstract class CalHelperResources {
	/**
	 * Configure the Linkbar.
	 *
	 * @return String if successful, false if failed
	 */
 
	public static function type($type) {
		switch((int) $type) {
			case 0:
				return 'COM_CAL_RESOURCE_TYPE_OBJECT';
			case 1:
				return 'COM_CAL_RESOURCE_TYPE_ROOM';
			case 2:
				return 'COM_CAL_RESOURCE_TYPE_SERVICE';
			case 3:
				return 'COM_CAL_RESOURCE_TYPE_USER';
			case 4:
				return 'COM_CAL_RESOURCE_TYPE_USERGROUP';
			default:
				return false;
		}
	
	}
}