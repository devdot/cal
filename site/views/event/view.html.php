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
 * HTML View class for the Cal Component
 *
 * @since  0.0.1
 */
class CalViewEvent extends JViewLegacy {
	
	public $state;
	public $item;
	public $params;
	public $related;
	
	public $mapsUse;
	public $mapsKey;
	public $category;
	
	function display($tpl = null) {
		// Assign data to the view
		$this->state = $this->get('State');
		$this->item = $this->get('Item'); 
		$this->params = $this->state->get('params');
		
		$this->mapsUse = JComponentHelper::getParams('com_cal')->get('maps_use');
		$this->mapsKey = JComponentHelper::getParams('com_cal')->get('maps_key');
		$this->category = JCategories::getInstance('Cal')->get($this->item->catid);
		
		if($tpl == null) //only load related events for the default view
			$this->related = $this->get('RelatedEvents');
		
		$this->loadHelper('cal');

		// set the title more fitting to the content
		$title = $this->params->get('page_title', '');
		$this->document->setTitle($this->item->name.' / '.$title);
		
		
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