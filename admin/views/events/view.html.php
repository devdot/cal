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
class CalViewEvents extends JViewLegacy {
    
    protected $sidebar;
    public $filterForm;
    public $activeFilters;
    public $state;
    public $items;
    
	/**
	 * Display the Resources View
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null) {
		// Get data from the model
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
        $this->filterForm   = $this->get('FilterForm');
        $this->activeFilters= $this->get('ActiveFilters');
        $this->state        = $this->get('State');
        
        JToolbarHelper::title('Calendar / Events', 'calendar');
        JToolbarHelper::addNew('event.add');
		JToolbarHelper::editList('event.edit');
		JToolbarHelper::publish('events.publish');
		JToolbarHelper::unpublish('events.unpublish');
		JToolbarHelper::checkin('events.checkin');
		JToolbarHelper::trash('events.trash');
        
        CalHelper::addSubmenu('events');
        
        
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
 
			return false;
		}
 
        
        $this->sidebar = JHtmlSidebar::render();
		// Display the template
		parent::display($tpl);
	}
}