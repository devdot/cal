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
 * Cal component helper.
 *
 * @param   string  $submenu  The name of the active view.
 *
 * @return  void
 *
 * @since   1.6
 */
abstract class CalHelper {
	/**
	 * Configure the Linkbar.
	 *
	 * @return Bool
	 */

	public static $weekdays = array('Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag');
	
	 public static function getTimeZone() {
		 //first the user timezone
		 //if there is no user-set timezone, use the server timezone
        $userTz = JFactory::getUser()->getParam('timezone');
        $timeZone = JFactory::getConfig()->get('offset');
        if($userTz) {
            $timeZone = $userTz;
        }
        return new DateTimeZone($timeZone);
    }
	
	public static function oneDay($start, $end) {
		return ($start->day == $end->day && $start->month == $end->month);
	}
	
	public static function weekday($date) {
		return CalHelper::$weekdays[$date->format('w')];
	}
}