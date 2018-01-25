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
		return JHTML::date($timestamp,'Ymd\THis\Z', 'UTC');
	}
	
	public static function dateSqlToCal($sql) {
		return  JHTML::date($sql,'Ymd\THis\Z', 'UTC');
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
	
	public static function getLocationString($event) {
		$str = $event->addrStreet;
		if(!empty($str))
			$str .= ', ';
		$str .= $event->addrZip;
		if(!empty($str))
			$str .= ' ';
		$str .= $event->addrCity;
		if(!empty($str))
			$str = $event->loc_name.' ('.$str.')';
		elseif(!empty($event->geoX))
			$str = $event->loc_name.' ('.$event->geoX.', '.$event->geoY.')';
		else
			$str = $event->loc_name;
		return self::escapeString($str);
	}
	
	public static function getDescriptionString($event) {
		$str = $event->introtext.$event->fulltext;
		return self::escapeString(($str)); //ther might be issues with lines longer that 75 in ics
	}
	
	public static function event($event) {
		$config = JFactory::getConfig();
		
		echo "BEGIN:VEVENT\n";
		echo 'SUMMARY:'.self::escapeString($event->name)."\n";
		echo 'UID:'.$event->id."\n";
		echo 'DTSTART:'.self::dateSqlToCal($event->start)."\n";
		echo 'DTEND:'.self::dateSqlToCal($event->end)."\n";
		echo 'LOCATION:'.self::getLocationString($event)."\n";
		echo 'ORGANIZER;CN='.$config->get('sitename').':MAILTO:'.$config->get('mailfrom')."\n";
		echo 'URL:'.JRoute::_('index.php?option=com_cal&view=event&format=ics&id='.$event->id, true, -1)."\n";
		echo 'X-ALT-DESC;FMTTYPE=text/html:'.self::getDescriptionString($event)."\n"; //only put in the html description, we don't provide extra normal description
		echo "END:VEVENT\n";
		
	}
	
}
