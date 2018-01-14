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
 * Cal ChurchTools Import Status View
 *
 * @since  0.0.1
 */
class CalViewCtImportStatus extends JViewLegacy {
    
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
        $this->state        = $this->get('State');
		
        JToolbarHelper::title('Calendar / ChurchTools Import Status', 'calendar');
        
		JToolbarHelper::custom('ct_importStatus.import', 'download', 'COM_CAL_TASK_IMPORT', 'COM_CAL_TASK_IMPORT');
		JToolbarHelper::back();
		
        CalHelper::addSubmenu('ct');
        
		$this->ct = CalHelperCT::getInstance();

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
 
			return false;
		}

        
        $this->sidebar = JHtmlSidebar::render();
		// Display the template
		parent::display($tpl);
	}
}