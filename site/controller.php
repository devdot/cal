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
 * Hello World Component Controller
 *
 * @since  0.0.1
 */
class CalController extends JControllerLegacy {
	function display($cachable = false, $urlparams = array()) {
		// get the view
		$view = JFactory::getApplication()->input->getCmd('view', 'events');
		
		// check for event view
		if($view == 'event') {
			$this->checkAndHandleEventAlias();
		}
		
		parent::display(true); //true asks for caching.
	}
	
	private function checkAndHandleEventAlias() {
		// first get all the data we need
		$app = JFactory::getApplication();
		$id = $app->input->getCmd('id');
		$format = $app->input->getCmd('format', 'html');
		
		// now check against router (might be slow)
		$route = JRoute::_('index.php?option=com_cal&view=event&id='.$id.'&format='.$format);
		if($route != JFactory::getURI()->getPath()) {
			// let's redirect to the correct page
			$this->setRedirect($route);
		}
	}
}