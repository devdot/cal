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
 * Cal ChurchTools View
 *
 * @since  0.0.1
 */
class CalViewCt extends JViewLegacy {
    
    protected $sidebar;
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
        $this->state        = $this->get('State');
		
        JToolbarHelper::title('Calendar / ChurchTools', 'calendar');
        
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