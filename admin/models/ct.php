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
 * Cal CT Model
 *
 * @since  0.0.1
 */
class CalModelCt extends JModelLegacy {

	
	public function import() {
		//applies the import rules
		
		//get the ct events
		$ct = CalHelperCT::getInstance();
		$events = $ct->getAllEvents();
		
		//let's get all rules
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->from('#__cal_ct_import')->select('*')->where('state = 1');
		
		//execute this query
		$db->setQuery($query);
		try {
			$ret = $db->loadObjectList();
		}
		catch (RuntimeException $e) {
			JError::raiseWarning(500, $e->getMessage());
			return -1;
		}
		
		//safe our rules as objects (are stored as json)
		$rules = array();
		
		//extract rules from the rows
		foreach($ret as $row) {
			$rule = json_decode($row->rules);
			$rule->forecast = time() + 3600*24*$rule->forecast;
			$rules[] = $rule;
		}
		
		//event table for checking if it's already here
		$eventTable = $this->getTable('Event', 'CalTable');
		
		$counter = 0;
		
		foreach($events as $event) {
			//go through each event and apply all rules one by one (first rule gets it)
			
			//change subid to fit for db
			if($event->subid == false)
				$event->subid = null;
			
			//TODO check modified (if that's really wanted ...)
			//skip if we already got it ...
			if($eventTable->load(array('ct_id' => $event->id, 'ct_subid' => $event->subid)))
				continue;
			// for weird reasons we have to do it manually (should be covered above)
			if($event->subid == NULL && $eventTable->load(array('ct_id' => $event->id)))
				continue;
			
			foreach ($rules as $rule) {
				//only apply the first rule that's applicable
				if($this->applyRule($event, $rule)) {
					// rule has been applied
					$counter++;
					break;
				}
			}
		}
		
		return $counter;
	}
	
	private function applyRule($event, $rule) {
		//returns true when the rule is applicable and has been applied
		
		//first check whether the rules even applies
		//check forecast
		if($event->start->toUnix() > $rule->forecast)
			return false;
	
		//go through all the options
		if(isset($rule->ct->id) && $rule->ct->id != $event->id)
			return false;
		if(isset($rule->ct->category_id) && $rule->ct->category_id != $event->category_id)
			return false;
		if(isset($rule->ct->name) && $rule->ct->name != $event->name)
			return false;
		
		
		//ok, now just import it according to the settings
		$table = $this->getTable('Event', 'CalTable');
		
		//basically go over property, check if there is a default rule for it and if so, set it
		if(isset($rule->cal->name))
			$table->set('name', $rule->cal->name);
		if(isset($rule->cal->introtext))
			$table->set('introtext', $rule->cal->introtext);
		if(isset($rule->cal->fulltext))
			$table->set('fulltext', $rule->cal->fulltext);
		if(isset($rule->cal->state))
			$table->set('state', $rule->cal->state);
		if(isset($rule->cal->catid))
			$table->set('catid', $rule->cal->catid);
		if(isset($rule->cal->metakey))
			$table->set('metakey', $rule->cal->metakey);
		if(isset($rule->cal->metadesc))
			$table->set('metadesc', $rule->cal->metadesc);
		if(isset($rule->cal->access))
			$table->set('access', $rule->cal->access);	
		if(isset($rule->cal->location_id))
			$table->set('location_id', $rule->cal->location_id);
		if(isset($rule->cal->link))
			$table->set('link', $rule->cal->link);
		if(isset($rule->cal->images))
			$table->set('images', $rule->cal->images);
		
		
		//set user (creator) information
		$date   = JFactory::getDate()->toSql();
		$userId = JFactory::getUser()->id;

		$table->set('created', $date);
		$table->set('created_by', $userId);
		
		//and ct import information
		$table->set('ct_id', $event->id);
		$table->set('ct_subid', $event->subid);
		$table->set('ct_modified', $event->modified->toSql());
		
		
		//now just set from ct event
		//def will only overwrite when it's not already set (so it won't overwrite what's defined by the rules)
		$table->def('start', $event->start->toSql());
		$table->def('end', $event->end->toSql());
		$table->def('name', $event->name);
		
		
		//set alias
		$table->set('alias', JFilterOutput::stringURLSafe($table->get('name')).'-'.JFilterOutput::stringURLSafe($event->start->format('Y-m-d')));
		
		//not sure what to do about failure at this point (let's just hope the next rule will not fail and failure is intended
		if(!$table->check()){
			return false;
		}
		// may fail because of duplicate alias
		if(!$table->store()) {
			return false;
		}
		
		//successfully applied the rule
		return true;
	}
}