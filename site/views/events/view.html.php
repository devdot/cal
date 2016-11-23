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
class CalViewEvents extends JViewLegacy {
	
	public $state;
	public $items;
	public $params;
	public $filterForm;
    public $activeFilters;
	
	function display($tpl = null) {
		// Assign data to the view
		$this->state = $this->get('State');
		$this->items = $this->get('Items'); 
		$this->params = $this->state->get('params');
		$this->pagination	= $this->get('Pagination');
        $this->filterForm   = $this->get('FilterForm');
        $this->activeFilters= $this->get('ActiveFilters');
		
		
		$this->loadHelper('cal');
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
 
			return false;
		}
 
		// Display the view
		parent::display($tpl);
	}
}