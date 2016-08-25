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
		
		
		return $data;
	}
	
	public function save($data) {
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
		
		if(!parent::save($data)) {
			//something above failed, don't even try now
			return false;
		}
		
		//save many-to-many relation of resources
		$resources = json_decode($data['resources']);
		
		if(count($resources) == 0) //nothing to do anyways
			return true;
		
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
	
	
}