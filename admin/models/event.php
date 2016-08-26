<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cal
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Item Model for an Event.
 *
 * @since  1.6
 */
class CalModelEvent extends JModelAdmin {
	public $typeAlias = 'com_cal.event';
	
	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 */
	public function getItem($pk = null) {
		if ($item = parent::getItem($pk)) { //articletext is a combination of fulltext and introtext
			//two options: introtext and fulltext or fulltext alone
			//introtext alone can't exists (technically you can still just put a &nbsp; there...)
			$item->articletext = trim($item->fulltext) != '' ? $item->introtext . "<hr id=\"system-readmore\" />" . $item->fulltext : $item->introtext;
		}
		return $item;
	}
	
	public function getForm($data = array(), $loadData = true) {
		// Get the form.
		$form = $this->loadForm('com_cal.event', 'event', array('control' => 'jform', 'load_data' => $loadData));
		
		if (empty($form)) {
			return false; //return false if loading the form has failed
		}
		
		if($this->getItem()->recurring_id) {
			//recurring child
			$form->setFieldAttribute('start', 'readonly', 'true', $group = null);
			$form->setFieldAttribute('end', 'readonly', 'true', $group = null);
		}
		
		return $form;
	}
	
	public function getTable($type = 'Event', $prefix = 'CalTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);//proxy loading the table
	}
	
	protected function loadFormData() {
		$app = JFactory::getApplication();

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_cal.edit.resource.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}
		
		if(!$data->recurring_id && !empty($data->recurring_schedule)) {
			//it's a parant
			$schedule = json_decode($data->recurring_schedule);
			$data->recurring_selector = $schedule->type; //put the type in for the selector
		}
		
		return $data;
	}
	
	public function save($data) {
		$input  = JFactory::getApplication()->input;
		$defaultSchedule = '{"type":0}';
		
		if(empty($data['alias'])) {
			//create an alias for the lazy user
			$data['alias'] = JFilterOutput::stringURLSafe($data['name']);
		}
		
		//split articletext into intro and fulltext
		$text = split("<hr id=\"system-readmore\" />", $data['articletext'], 2); //only split once (in two elements)
		if(count($text) == 2) {
			$data['introtext'] = $text[0];
			$data['fulltext'] = $text[1];
		}
		else {
			$data['introtext'] = "";
			$data['fulltext'] = $text[0];
		}
		
		if(isset($data['make_recurring']) && (int) $data['make_recurring'] == 1) {
			//user wants to make this recurring
			if((int) $data['id'] == 0)
				$data['recurring_schedule'] = $defaultSchedule;
			else {
				//check if he's allowed to make it recurring
				//can't be a child nor be already recurring
				$db = $this->getDbo();
				$query = $db->getQuery(true)
				->select('recurring_id, recurring_schedule')
				->from('#__cal_events')
				->where('id = '.$data['id']);
				$db->setQuery($query);
				$results = $db->loadObjectList();
				
				if($results[0]->recurring_id) {
					//not 0, so it's a child
					JError::raiseWarning(500, JText::_("COM_CAL_ERROR_MAKE_RECURRING_IS_CHILD"));
					return false;
				}
				elseif(empty($results[0]->recurring_schedule)) {
					//the schedule is empty, we can make it recurring parent by giving the default schedule
					$data['recurring_schedule'] = $defaultSchedule;
				}
			}
		}
		elseif(isset($data['stop_recurring']) && (int) $data['stop_recurring'] == 1) {
			//user wants to break out this event of a recurring series
			//check if he can do that (only children can stop being part of recurrance)
			$db = $this->getDbo();
			$query = $db->getQuery(true)
			->select('recurring_id, recurring_schedule')
			->from('#__cal_events')
			->where('id = '.$data['id']);
			$db->setQuery($query);
			$results = $db->loadObjectList();
			
			if(!empty($results[0]->recurring_schedule)) {
				//only parents have a schedule
				JError::raiseWarning(500, JText::_("COM_CAL_ERROR_STOP_RECURRING_IS_PARENT"));
				return false;
			}
			elseif($results[0]->recurring_id) {
				//recurring_id is not 0
				$data['recurring_id'] = 0;
				//TODO
				//copy missing data from parent
			}
		}
		
		
		// Alter the title for save as copy
		if ($input->get('task') == 'save2copy') {
			//copy-pasta from com_content
			$origTable = clone $this->getTable();
			$origTable->load($input->getInt('id'));
			
			if ($data['title'] == $origTable->title) {
				list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);
				$data['title'] = $title;
				$data['alias'] = $alias;
			}
			else {
				if ($data['alias'] == $origTable->alias) {
					$data['alias'] = '';
				}
			}

			$data['state'] = 0;
		}
		
		if(isset($data['recurring_selector'])) {
			$type = (int) $data['recurring_selector'];
			$data["recurring_schedule"] = json_encode(array("type" => $type));
		}
		
		if(!parent::save($data)) {
			//something above failed, don't even try now
			return false;
		}
		
		//save many-to-many relation of resources
		$resources = json_decode($data['resources']);
		
		if($data['id'] != 0) { //its a new entry, no need to check existing relations
			//first load the current relations
			//we need to check which are new and which we need to delete or keep
			$relations = array();
			$db = $this->getDbo();
			$query = $db->getQuery(true)
				->select('id, resource_id')
				->from('#__cal_events_resources')
				->where('event_id = '.$data['id']);
			$db->setQuery($query);
			try {
				$relations = $db->loadObjectList();
			}
			catch (RuntimeException $e) {
				JError::raiseWarning(500, $e->getMessage());
				return false;
			}

			$add = array(); //resource id
			$keep = array(); //resource id
			$remove = array(); //relation id
			foreach($relations as $relation) { //check for each existing relation whether to keep it
				if(in_array((int) $relation->resource_id, $resources))
					$keep[] = $relation->resource_id;
				else
					$remove[] = (int) $relation->id;
			}
			foreach($resources as $resource) { //check which resources need to be added
				if(!in_array($resource, $keep))
						$add[] = $resource;
			}
		}
		else {
			$add = $resources; //add all
			$remove = array(); //and remove none (because this is a new entry)
		}

		$event_id = (int) $this->getItem()->get('id');

		//now work it
		foreach($add as $resource_id) {
			$db = $this->getDbo(); //first add them all
			$query = $db->getQuery(true)
			->insert('#__cal_events_resources')
			->columns('resource_id, event_id')
			->values($resource_id.','.$event_id);
			$db->setQuery($query);
			$db->execute();
		}
		foreach($remove as $relation_id) {
			$db = $this->getDbo(); //and now delete it all
			$query = $db->getQuery(true)
			->delete('#__cal_events_resources')
			->where('id = '.$relation_id);
			$db->setQuery($query);
			$db->execute();
		}
		
		return true;
	}
	
	public function delete(&$pks) {
		//so some checks for parents
		$db = $this->getDbo();
		$del = array();
		foreach($pks as $pk) {
			$query = $db->getQuery(true)
				->select('recurring_schedule, recurring_id')
				->from('#__cal_events')
				->where('id = '.$pk);
			$db->setQuery($query);
			try {
				$ret = $db->loadObjectList();
			}
			catch (RuntimeException $e) {
				JError::raiseWarning(500, $e->getMessage());
				return false;
			}
			if((int) $ret[0]->recurring_id != 0) {
				//we can't delete children
				JError::raiseWarning(500, 'COM_CAL_ERROR_RECURRING_CHILD');
				continue;
			}
			
			if(!empty($ret[0]->recurring_schedule)) {
				//this is a parent, delete all children
				$query = $db->getQuery(true)
					->select('id')
					->from('#__cal_events')
					->where('recurring_id = '.$pk);
				$db->setQuery($query);
				try {
					$ret = $db->loadObjectList();
				}
				catch (RuntimeException $e) {
					JError::raiseWarning(500, $e->getMessage());
					return false;
				}
				foreach($ret as $child) {
					//put them all on the list
					$del[] = (int) $child->id;
				}
			}
			$del[] = $pk; //and put the parent on the list as well
		}
		$pks = $del;
		
		if(!parent::delete($pks)) {
			//it failed  somewhere else
			return false;
		}
		
		if(!empty($pks)) {
			//now onto resources: delete all associated resources
			$query = $db->getQuery(true)
				->delete('#__cal_events_resources');
			$arr = array();
			foreach($pks as $pk) {
				$arr[] = 'event_id='.$pk; //just delete 'em all
			}
			$query->where(implode(" OR ", $arr)); //making it this way ensures good functionality
			$db->setQuery($query);
			try {
				$db->execute();
				return true;
			}
			catch (RuntimeException $e) {
				JError::raiseWarning(500, $e->getMessage());
				return false;
			}
		}
	}
	
	public function recurring($id) {
		$db = $this->getDbo();
		
		$user = JFactory::getUser();
		
		//make children for this recurring parent
		$parent = $this->getTable();
		$parent->load($id);	
		
		if((int) $parent->recurring_id != 0 || empty($parent->recurring_schedule)) {
			JError::raiseWarning(500, 'COM_CAL_ERROR_RECURRING_IS_NO_PARENT');
			return false;
		}
		$schedule = json_decode($parent->recurring_schedule);
		
		//THIS IS A CONSTANT
		//there shouldn't be constants here but what ever
		//somebody could make this more clean
		//how long events will be forecast
		$forecast = time() + 3600*24*31; //20 days
		
		$start	= new JDate($parent->start);
		$end	= new JDate($parent->end);
		$start_ = $start->toUnix();
		$end_	= $end->toUnix();
		$duration = $end_ - $start_;
		
		$dates = array(); //array of all events to make
		
		
		//first get the last child
		$query = $db->getQuery(true)
			->select('id, start')
			->from('#__cal_events')
			->where('recurring_id = '.$id)
			->order('start DESC')
			->setLimit(1);
		$db->setQuery($query);
		try {
			$ret = $db->loadObjectList();
		}
		catch (RuntimeException $e) {
			JError::raiseWarning(500, $e->getMessage());
			return false;
		}
		if(!empty($ret)) {
			$latest = new JDate($ret[0]->start);
			$latest_ = $latest->toUnix();
		}
		else {
			//we at least should created one event child, there is none yet
			$dates[] = clone $start;
			$latest = clone $start;
			$latest_ = $start_;
		}
		
		if($forecast - $latest_ < 0 && empty($dates)) {
			//the event is beyond forecast date in the future
			//or has been forecast long enough
			return 2;
		}
		
		switch($schedule->type) {
			case 0:
				$interval = new DateInterval('P1W');
			case 1:
				if(!isset($interval))
					$interval = new DateInterval('P2W');
				
				$date = clone $latest;
				$date->add($interval);
				while($date->toUnix() <= $forecast) {
					$dates[] = clone $date;
					$date->add($interval);
				}
				
				break;
		}
		
		if(empty($dates)) {
			//no new dates to add
			return 2;
		}
		
		$query = $db->getQuery(true);
		$query->insert("#__cal_events")
				->columns('alias, start, end, name, state, catid, created, created_by, modified, modified_by, access, location_id, recurring_id');
				//these are the columns controlled by the parent (or first time required like created)
		
		$arr = array('"'.$parent->name.'"',
					$parent->state,
					$parent->catid,
					'NOW()',
					$user->id,
					'NOW()',
					$user->id,
					$parent->access,
					$parent->location_id,
					$parent->id);

		$std = implode(',', $arr);
		
		// now go through all the dates and give them their start and end
		//also make sure alias is always unique by putting the date into it
		foreach($dates as $s) {
			$e = new JDate($s->toUnix() + $duration);
			
			$alias = $parent->alias.'-'.JFilterOutput::stringURLSafe($s->format('Y-m-d'));
			$query->values('"'.$alias.'","'.$s->toSql().'","'.$e->toSql().'",'.$std);
		}
		$db->setQuery($query);
		$db->execute();
		return true;
	}
	
}