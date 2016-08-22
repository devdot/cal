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
class CalViewResources extends JViewLegacy {
    
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
	function display($tpl = null)
	{
		// Get data from the model
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
        $this->filterForm   = $this->get('FilterForm');
        $this->activeFilters= $this->get('ActiveFilters');
        $this->state        = $this->get('State');
        
        JToolbarHelper::title('Calendar / Resources', 'calendar');
        //JToolbarHelper::preferences('com_cal');
        JToolbarHelper::addNew('resource.add');
		JToolbarHelper::editList('resource.edit');
		JToolbarHelper::trash('resources.trash');
        
        CalHelper::addSubmenu('resources');
        
        
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