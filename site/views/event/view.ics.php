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
 * HTML View class for the Cal Component
 *
 * @since  0.0.1
 */
class CalViewEvent extends JViewLegacy {
	
	public $item;
	public $params;
	
	
	function display($tpl = null) {
		// Assign data to the view
		$this->item = $this->get('Item'); 
		
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
 
			return false;
		}
 
		$this->loadHelper('ics');
		$config = JFactory::getConfig();
		// now onto displaying
		
		echo IcsHelper::HTTPHeaders($this->item->alias.'.ics');
		echo IcsHelper::header($config->get('sitename'));
		echo IcsHelper::event($this->item);
		echo IcsHelper::footer();
	}
}