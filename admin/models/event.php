<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cal
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

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
			
			//from com_content
			// Convert the images field to an array.
			$registry = new Registry;
			$registry->loadString($item->images);
			$item->images = $registry->toArray();
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
			$form->setFieldAttribute('name', 'readonly', 'true', $group = null);
			$form->setFieldAttribute('start', 'readonly', 'true', $group = null);
			$form->setFieldAttribute('end', 'readonly', 'true', $group = null);
			$form->setFieldAttribute('catid', 'readonly', 'true', $group = null);
			$form->setFieldAttribute('location_id', 'readonly', 'true', $group = null);
		}
		
		return $form;
	}
	
	public function getTable($type = 'Event', $prefix = 'CalTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);//proxy loading the table
	}
	
	protected function loadFormData() {
		$app = JFactory::getApplication();

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_cal.edit.event.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}
		
		if(!$data->recurring_id && !empty($data->recurring_schedule)) {
			//it's a parant
			$schedule = json_decode($data->recurring_schedule);
			$data->recurring_selector = $schedule->type; //put the type in for the selector
			$data->recurring_end = $schedule->end;
		}
		
		return $data;
	}
	
	public function save($data) {
		//TODO: make things cleaner
		//should use a table for loading the existing record
		//also variables $isParent and $isChild for recurrance handling
		//it's a bit of a mess right now
		$db = $this->getDbo();
		
		//validation
		$start = new JDate($data['start']);
		$end = new JDate($data['end']);
		if($start->toUnix() >= $end->toUnix()) {
			//start must be before end
			JError::raiseWarning(500, JText::_("COM_CAL_ERROR_START_AFTER_END"));
			return false;
		}
		
		$input  = JFactory::getApplication()->input;
		$defaultSchedule = '{"type":0,"end":""}';
		
		if(empty($data['alias'])) {
			//create an alias for the lazy user
			$data['alias'] = JFilterOutput::stringURLSafe($data['name']);
		}
		
		//convert images back to string for db
		if (isset($data['images']) && is_array($data['images'])) {
			$registry = new Registry;
			$registry->loadArray($data['images']);

			$data['images'] = (string) $registry;
		}
		
		//split articletext into intro and fulltext
		$text = explode("<hr id=\"system-readmore\" />", $data['articletext'], 2); //only split once (in two elements)
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
				$parent = $this->getTable();
				$parent->load($results[0]->recurring_id);
				
				$data['recurring_id'] = 0; //set this to 0 to break off
				
				if(empty($data['introtext']))
					$data['introtext'] = $parent->introtext;
				if(empty($data['fulltext']))
					$data['fulltext'] = $parent->fulltext;
				if(empty($data['metakey']))
					$data['metakey'] = $parent->metakey;
				if(empty($data['metadesc']))
					$data['metadesc'] = $parent->metadesc;
				if(empty($data['link']))
					$data['link'] = $parent->link;
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
			//we got ourselves a recurring parent
			$type = (int) $data['recurring_selector'];
			$data["recurring_schedule"] = json_encode(array("type" => $type, "end" => $data['recurring_end']));
			
			//now update our children's data
			$query = $db->getQuery(true)
				->update("#__cal_events")
				->set('name='.$db->quote($data['name']))
				->set('location_id='.(int) $data['location_id'])
				->set('catid='.(int) $data['catid'])
				->set('access='.(int) $data['access'])
				->set('state='.(int) $data['state'])
				->where('recurring_id='.(int) $data['id']);
			$db->setQuery($query);
			$db->execute();
		}
		
		if(!parent::save($data)) {
			//something above failed, don't even try now
			return false;
		}
		
		if(isset($data['recurring_selector'])) {
			//now call for rescheduling of our children
			//must be after saving to parent so this function will actually work
			$this->recurring((int) $data['id']);
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
		
		return true;
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
				
		//how long events will be forecast
		$forecast = time() + 3600*24*JComponentHelper::getParams('com_cal')->get('forecast', 150);
		
		$start	= new JDate($parent->start);
		$end	= new JDate($parent->end);
		$start_ = $start->toUnix();
		$end_	= $end->toUnix();
		$duration = $end_ - $start_;
		
		if($schedule->end != "") {
			$stop	= new JDate($schedule->end);	
		}
		else {
			$stop = new JDate(0);
		}
		$stop_	= $stop->toUnix();
		$dates = array(); //array of all events to make
		
		if($stop_ > $start_ && $forecast > $stop_ ) {
			//if stop is smaller than start, forecast for ever
			//the event should not be forecast that far
			$forecast = $stop_;
			var_dump(date("Y-m-d", $forecast));
		}
		
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
		if($schedule->type < 3) {
			switch($schedule->type) {
				case 0:
					$interval = new DateInterval('P1W');
					break;
				case 1:
						$interval = new DateInterval('P2W');
					break;	
				case 2:
					$interval = new DateInterval('P1M');
					break;		
			}
			$date = clone $latest;
			$date->add($interval);
			while($date->toUnix() <= $forecast) {
				$dates[] = clone $date;
				$date->add($interval);
			}
		}
		else {
			//weekday of the month
			//relative date schedule
			function weekOfMonth($date) {
				//http://stackoverflow.com/questions/32615861/get-week-number-in-month-from-date-in-php
				//Get the first day of the month.
				$firstOfMonth = strtotime(date("Y-m-01", $date));
				//Apply above formula.
				return intval(date("W", $date)) - intval(date("W", $firstOfMonth)) + 1;
			}
			$week = weekOfMonth($start_);
			$date = clone $latest;
			$interval = new DateInterval('P1W');
			
			//so basically we add up one week until we hit forecast maximum
			//if the interator dates' week is equal to starts' week,
			//we found an actuall matching week
			//NOTE: might skip February when there is no week 5 in Feb!!!
			$date->add($interval);
			while($date->toUnix() <= $forecast) {
				if(weekofMonth($date->toUnix()) == $week) {
					$dates[] = clone $date; //found one matching week
				}
				$date->add($interval);
			}
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
	
		
	public function publish(&$pks, $value = 1) {
		$dispatcher = JEventDispatcher::getInstance();
		$user = JFactory::getUser();
		$table = $this->getTable();
		$pks = (array) $pks;

		// Include the plugins for the change of state event.
		JPluginHelper::importPlugin($this->events_map['change_state']);

		// Access checks.
		foreach ($pks as $i => $pk) {
			$table->reset();

			if ($table->load($pk)) {
				if (!$this->canEditState($table)) {
					// Prune items that you can't change.
					unset($pks[$i]);

					JLog::add(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), JLog::WARNING, 'jerror');

					return false;
				}

				// If the table is checked out by another user, drop it and report to the user trying to change its state.
				if (property_exists($table, 'checked_out') && $table->checked_out && ($table->checked_out != $user->id))
				{
					JLog::add(JText::_('JLIB_APPLICATION_ERROR_CHECKIN_USER_MISMATCH'), JLog::WARNING, 'jerror');

					// Prune items that you can't change.
					unset($pks[$i]);

					return false;
				}
			}
			
			//check for special snowflake recurring parents
			if(!empty($table->recurring_schedule)) {;
				//get all children
				$db = $this->getDbo();
				$query = $db->getQuery(true)
					->select('id')
					->from('#__cal_events')
					->where('recurring_id = '.$table->id);
				$db->setQuery($query);
				try {
					$ret = $db->loadObjectList();
				}
				catch (RuntimeException $e) {
					JError::raiseWarning(500, $e->getMessage());
					return false;
				}

				//now just add them all
				foreach($ret as $child) {
					$pks[] = $child->id; //as easy as this
				}
			}
		}
		
		// Attempt to change the state of the records.
		if (!$table->publish($pks, $value, $user->get('id'))) {
			$this->setError($table->getError());

			return false;
		}

		$context = $this->option . '.' . $this->name;

		// Trigger the change state event.
		$result = $dispatcher->trigger($this->event_change_state, array($context, $pks, $value));

		if (in_array(false, $result, true)) {
			$this->setError($table->getError());

			return false;
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}
	
	
	public function moveToArchive($pks) {
		$db = $this->getDbo();
		
		$event = $this->getTable();
		$archive = $this->getTable('ArchiveEvent');
		
		$success = true;
		
		//now run though them all
		foreach($pks as $pk) {
		
			if(!$event->load($pk, true)) {
				$success = false;
				continue;
			}

			//we don't get any data from it's recurring parent (neither fill blanks)

			$archive->reset();

			//now save everything
			if(!$archive->bind($event)){
				$success = false;
				continue;
			}
			if(!$archive->check()){
				$success = false;
				continue;
			}
			if(!$archive->store(true, true)){
				$success = false;
				continue;
			}
			
			
			
			//now delete this entry fron cal_events
			$event->delete();
		}
		
		
		return $success;
	}
	
}