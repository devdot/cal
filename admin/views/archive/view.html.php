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
 * Cals View
 *
 * @since  0.0.1
 */
class CalViewArchive extends JViewLegacy {
    
    protected $sidebar;
	public $filterForm;
    public $activeFilters;
    public $state;
    public $items;
    
	function display($tpl = null) {
		// Get data from the model
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
        $this->filterForm   = $this->get('FilterForm');
        $this->activeFilters= $this->get('ActiveFilters');
        $this->state        = $this->get('State');
        
        JToolbarHelper::title('Calendar / Archive', 'calendar');
		JToolbarHelper::editList('event.edit');
		JToolbarHelper::publish('events.publish');
		JToolbarHelper::unpublish('events.unpublish');
		JToolbarHelper::checkin('events.checkin');
		JToolbarHelper::trash('events.trash');
        
        JHtml::stylesheet("/administrator/components/com_cal/css/cal.css");

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
 
			return false;
		}
 
        
        /*if ($this->state->get('filter.state') == -2){
			JToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'events.delete', 'JTOOLBAR_EMPTY_TRASH');
		}*/
        
        CalHelper::addSubmenu('archive');
		
		$this->sidebar = JHtmlSidebar::render();
		// Display the template
		parent::display($tpl);
	}
}