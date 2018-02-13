<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cal
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
/**
 * HTML View class for the Cal Component and format type csv
 *
 * @since  0.0.1
 */
class CalViewEvents extends JViewLegacy {
	
	public $items;
	
	function display($tpl = null) {
		$this->items = $this->get('Items'); 
		
		$document = JFactory::getDocument();
		
		// generate the filename
		$filename = 'events_'.date("Y-m-d__H_i").'.csv';
		
		// set headers
		$document->setMimeEncoding('text/csv');
		$document->setCharset('ANSI');
		header('Content-Disposition: attachment; filename=' . $filename);
	
		
		// print the header
		$columns = array('ID', 'StartDate', 'StartTime', 'EndDate', 'EndTime', 'Category', 'Title', 'Description', 'Location', 'City', 'Link');
		self::printCsvLine($columns);
		
		foreach($this->items as $item) {
			$title = iconv("UTF-8", "Windows-1252", $this->escape($item->name));
			$cat = iconv("UTF-8", "Windows-1252",$this->escape($item->cat_name));
			$loc = iconv("UTF-8", "Windows-1252",$this->escape($item->location_name));
			$city = iconv("UTF-8", "Windows-1252",$this->escape($item->city));
			
			// build the description
			$desc = $item->introtext.$item->fulltext;
			
			// check for recurring children
			if($desc == '' && $item->recurring_id != NULL)
				$desc = $item->parent_introtext.$item->parent_fulltext;
			
			// strip tags and remove newlines
			$desc = strip_tags($desc);
			$desc = preg_replace("/\r|\n/", "", $desc);
			
			$desc = iconv("UTF-8", "Windows-1252",$this->escape($desc));
			
			// URL link to event	
			$link   = JURI::base().JRoute::_('index.php?option=com_cal&view=event&id='.$item->id);
			
			self::printCsvLine(array($item->id,
				JHTML::date($item->start, "Y-m-d"), 
				JHTML::date($item->start, "H:i:s"),
				JHTML::date($item->end, "Y-m-d"),
				JHTML::date($item->end, "H:i:s"),
				$cat,
				$title,
				$desc,
				$loc,
				$city,
				$link
				));
		}
	}
	
	private static function printCsvLine($arr) {
		echo '"'.implode('";"', $arr).'"'."\r\n";
	}
}