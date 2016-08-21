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
 * General Controller of Cal component
 *
 * @package     Joomla.Administrator
 * @subpackage  com_cal
 * @since       0.0.7
 */
class CalController extends JControllerLegacy
{
	/**
	 * The default view for the display method.
	 *
	 * @var string
	 * @since 12.2
	 */
	protected $default_view = 'cal';
    
    public function display($cachable = false, $urlparams = array()) {
        
        //JLoader::register('ContactHelper', JPATH_ADMINISTRATOR . '/components/com_contact/helpers/contact.php');

		$view   = $this->input->get('view', 'cal');
		$layout = $this->input->get('layout', 'default');
		$id     = $this->input->getInt('id');
        
		// Check for edit form.
		//if ($view == 'contact' && $layout == 'edit' && !$this->checkEditId('com_contact.edit.contact', $id)) {
			// Somehow the person just went to the form - we don't allow that.
		//	$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
		//	$this->setMessage($this->getError(), 'error');
		//	$this->setRedirect(JRoute::_('index.php?option=com_contact&view=contacts', false));

		//	return false;
		//}

		return parent::display();
        
    }
}