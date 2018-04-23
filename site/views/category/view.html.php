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
 * HTML Category View class for the Cal Component
 *
 * @since  0.0.1
 */
class CalViewCategory extends JViewLegacy {
	
	public $state;
	public $item;
	public $params;
	public $events;
	
	function display($tpl = null) {
		// Assign data to the view
		$this->state = $this->get('State');
		$this->item = $this->get('Item'); 
		$this->params = $this->state->get('params');
		$this->events = $this->get('Events');
		
		$this->loadHelper('cal');

		// set the title more fitting to the content
		$title = $this->params->get('page_title', '');
		$this->document->setTitle($this->item->title.' / '.$title);
		
		
		// check for custom meta data and insert
		if(!empty($this->item->metakey))
			$this->document->setMetaData('keywords', $this->item->metakey);
		if(!empty($this->item->metadesc))
			$this->document->setMetaData('description', $this->item->metadesc);
		
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
 
			return false;
		}
 
		// Display the view
		parent::display($tpl);
	}
}