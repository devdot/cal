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
 * View for editing a single CT import rule
 *
 * @since  0.0.1
 */
class CalViewCtImportRule extends JViewLegacy {
    
    protected $form;
	protected $item;
	protected $state;
    
	/**
	 * 
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		
		$this->item  = $this->get('Item');
		$this->form  = $this->get('Form');
		$this->state = $this->get('State');
        
        
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
 
			return false;
		}
		
		JFactory::getApplication()->input->set("hidemainmenu", true);
		
		JToolbarHelper::title('Calendar / ChurchTools Import Rule', 'calendar');
		
		//toolbar elements
		JToolbarHelper::apply('ct_importRule.apply');
		JToolbarHelper::save('ct_importRule.save');
		JToolbarHelper::save2new('ct_importRule.save2new');
		
		//this isn't a new item, otherwise the id wouldn't be 0
		if($this->item->id != 0) {
			JToolbarHelper::save2copy('ct_importRule.save2copy');
		}
		JToolbarHelper::cancel('ct_importRule.cancel');
		
		// Display the template
		parent::display($tpl);
	}
}