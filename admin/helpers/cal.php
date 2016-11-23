<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
/**
 * HelloWorld component helper.
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
 
	public static function addSubmenu($submenu) {
		JHtmlSidebar::addEntry("Overview", "?option=com_cal", $submenu == 'cal');
        JHtmlSidebar::addEntry("Events", "?option=com_cal&view=events", $submenu == 'events');
        JHtmlSidebar::addEntry("Locations", "?option=com_cal&view=locations", $submenu == 'locations');
        JHtmlSidebar::addEntry("Categories", "?option=com_categories&extension=com_cal", $submenu == 'categories');
        JHtmlSidebar::addEntry("Resources", "?option=com_cal&view=resources", $submenu == 'resources');
        JHtmlSidebar::addEntry("Archive", "?option=com_cal&view=archive", $submenu == 'archive');
	
		if($submenu != 'categories')
			JToolbarHelper::preferences('com_cal');
	}
	
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
}