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
abstract class IcsHelper {
	
	// from https://gist.github.com/jakebellacera/635416
	
	public static function HTTPHeaders($filename) {
		header('Content-type: text/calendar; charset=utf-8');
		header('Content-Disposition: attachment; filename=' . $filename);
	}
	
	public static function dateToCal($timestamp) {
		return date('Ymd\THis\Z', $timestamp);
	}
	
	public static function dateSqlToCal($sql) {
		$date = new JDate($sql);
		return $date->format('Ymd\THis\Z');
	}

	public static function escapeString($string) {
	  return preg_replace('/([\,;])/','\\\$1', $string);
	}
	
	public static function header($prodid) {
		echo "BEGIN:VCALENDAR\n";
		echo "VERSION:2.0\n";
		echo "METHOD:PUBLISH\n";
		echo "PRODID:-//$prodid//$prodid//DE\n";
	}
	
	public static function footer() {
		echo "END:VCALENDAR";
	}
	
	public static function event($event) {
		$config = JFactory::getConfig();
		
		echo "BEGIN:VEVENT\n";
		echo 'SUMMARY:'.self::escapeString($event->name)."\n";
		echo 'UID:'.$event->id."\n";
		echo 'DSTART:'.self::dateSqlToCal($event->start)."\n";
		echo 'DEND:'.self::dateSqlToCal($event->end)."\n";
		echo 'LOCATION:'.self::escapeString($event->loc_name)."\n";
		echo 'ORGANIZER;CN='.$config->get('sitename').':MAILTO:'.$config->get('mailfrom')."\n";
		echo 'URL:'.JRoute::_('index.php?option=com_cal&view=event&format=ics&id='.$event->id, true, -1)."\n";
		echo "END:VEVENT\n";
		
	}
	
}
