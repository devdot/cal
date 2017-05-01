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
 * Cal ChurchTools View
 *
 * @since  0.0.1
 */
class CalViewCT_TokenGenerator extends JViewLegacy {
    
    protected $sidebar;
    public $state;
    public $items;
	public $form;
	
    
	/**
	 * Display the ChurchTools View
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null) {
		// Get data from the model
        $this->state        = $this->get('State');
		$this->form			= $this->get('Form');
		
        JToolbarHelper::title('Calendar / ChurchTools', 'calendar');
		JToolbarHelper::apply('ct_tokenGenerator.apply');
        JToolbarHelper::back();
        
        CalHelper::addSubmenu('ct');
        
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