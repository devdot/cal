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

JLoader::register('CalHelperCT', JPATH_COMPONENT . '/helpers/ct.php');

/**
 * Cal ChurchTools Import Rules View
 *
 * @since  0.0.1
 */
class CalViewCT_Import extends JViewLegacy {
    
    protected $sidebar;
   
	public $filterForm;
    public $activeFilters;
    public $state;
    public $items;
	
	public $ct;
    
	/**
	 * Display the ChurchTools View
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
		
        JToolbarHelper::title('Calendar / ChurchTools Import Rules', 'calendar');
        
		JToolbarHelper::addNew('ct_importRule.add');
        JToolbarHelper::editList('ct_importRule.edit');
        JToolbarHelper::publish('ct_import.publish');
        JToolbarHelper::unpublish('ct_import.unpublish');
        JToolbarHelper::trash('ct_import.trash');
        
        CalHelper::addSubmenu('ct');
        
		$this->ct = CalHelperCT::getInstance();
        
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