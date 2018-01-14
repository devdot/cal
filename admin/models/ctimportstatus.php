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
 * Cal CT import status Model
 *
 * @since  0.0.1
 */
class CalModelCtImportStatus extends JModelList {
	/**
	 * @var array messages
	 */
	protected $messages;
    
    protected function populateState($ordering = null, $direction = null) {
        // Initialise variables.
        $app = JFactory::getApplication('administrator');
 
        // Other code goes here
 
        //take data from user requests and put it into the user state
        //first param: state name (for accessing)
        //second param: I don't really know
        //third: default value, if first is empty
        //fourth: filter for first param
        $published = $app->getUserStateFromRequest($this->context . 'filter.state', 'filter_state', '', 'string');
        $this->setState('filter.state', $published);
        
        $this->setState('filter.name', 'ASC');
 
        // Other code goes here
 
        // List state information.
        parent::populateState();
    }
	
	public function getItems() {
		// Get a storage key.
		$store = $this->getStoreId();

		// Try to load the data from internal storage.
		if (isset($this->cache[$store])) {
			return $this->cache[$store];
		}

		try {
			// Load the list items and add the items to the internal cache.
			$this->cache[$store] = $this->getItemsFromCT();
		}
		catch (RuntimeException $e) {
			$this->setError($e->getMessage());

			return false;
		}

		return $this->cache[$store];
	}
	
	public function getItemsFromCT() {
		//this function gets our events from CT, enriches data and formats as if it came from a db
		
		$ct = CalHelperCT::getInstance();
		
		$events = $ct->getAllEvents();
		
		//we'll need the db
		$db = $this->getDbo();
		
		foreach($events as $event) {
			//0: not in db
			//1: in db
			//2: has been modified
			$event->ctState = 0;
			$event->event_id = false;
			
			//check if it's in the db
			$query = $db->getQuery(true);
			$query->from('#__cal_events')->select('id, ct_modified');
			$query->where('ct_id = '.$event->id);
			if($event->subid)
				$query->where('ct_subid = '.$event->subid);
			
			//execute this query
			$db->setQuery($query);
			try {
				$ret = $db->loadObjectList();
			}
			catch (RuntimeException $e) {
				JError::raiseWarning(500, $e->getMessage());
				return false;
			}
			
			if(!empty($ret)) {
				//we actually found something
				//now check the modified that
				$modified = new JDate($ret[0]->ct_modified);
				
				if($modified->toUnix() < $event->modified->toUnix()) {
					//we got an update
					$event->ctState = 2;
				}
				else {
					$event->ctState = 1;
				}
				
				//pass on the id of the associated event
				$event->event_id = $ret[0]->id;
			}
			
		}
		
		return $events;
		
	}
	
	public function import($keys) {
		$success = true;
		
		//get the ct events
		$ct = CalHelperCT::getInstance();
		$events = $ct->getAllEvents();
		
		//need this for later
		$db = $this->getDbo();
		
		//event table
		$eventTable = $this->getTable('Event', 'CalTable');
		
		foreach($events as $event) {
			//catch those we are not to import
			if(!isset($keys[$event->id]))
				continue;
			if($event->subid && !isset($keys[$event->id][$event->subid]))
				continue;
			
			
			//check if it's in the db
			$query = $db->getQuery(true);
			$query->from('#__cal_events')->select('id, ct_modified');
			$query->where('ct_id = '.$event->id);
			if($event->subid)
				$query->where('ct_subid = '.$event->subid);
			
			//execute this query
			$db->setQuery($query);
			try {
				$ret = $db->loadObjectList();
			}
			catch (RuntimeException $e) {
				JError::raiseWarning(500, $e->getMessage());
				$success = false;
				continue;
			}
			
			//now just reset the previous table entry
			$eventTable->reset();
			
			if(!empty($ret)) {
				//we actually found something
				//in this case just update
				$eventTable->load($ret[0]->id);
			}
			else {
				$eventTable->set('id', null);
				
				//make subid = false to null for db
				if($event->subid == false)
					$event->subid = null;
				
				//insert new event
				$eventTable->set('ct_id', $event->id);
				$eventTable->set('ct_subid', $event->subid);
				
				//set creator and date
				$date   = JFactory::getDate()->toSql();
				$userId = JFactory::getUser()->id;
				
				$eventTable->set('created', $date);
				$eventTable->set('created_by', $userId);
				
				//set alias
				$eventTable->set('alias', JFilterOutput::stringURLSafe($event->name).'-'.JFilterOutput::stringURLSafe($event->start->format('Y-m-d')));
			}
			
			//set the properties
			$eventTable->set('ct_modified', $event->modified->toSql());
			$eventTable->set('start', $event->start->toSql());
			$eventTable->set('end', $event->end->toSql());
			$eventTable->set('name', $event->name);
			

			if(!$eventTable->check()){
				$success = false;
				continue;
			}
			if(!$eventTable->store()) {
				//we had a fail
				$success = false;
			}
		}
		return $success;
	}
}