<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_cal
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * Single item model for an event
 *
 * @package     Joomla.Site
 * @subpackage  com_cal
 * @since       1.5
 */
class CalModelEvent extends JModelForm
{
	/**
	 * The name of the view for a single item
	 *
	 * @since   1.6
	 */
	protected $view_item = 'event';

	/**
	 * A loaded item
	 *
	 * @since   1.6
	 */
	protected $_item = null;

	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context = 'com_cal.event';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState() {
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = $app->input->getInt('id');
		$this->setState('event.id', $pk);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

		$user = JFactory::getUser();

		if ((!$user->authorise('core.edit.state', 'com_cal')) &&  (!$user->authorise('core.edit', 'com_cal'))) {
			$this->setState('filter.published', 1);
			$this->setState('filter.archived', 2);
		}
	}

	/**
	 * Gets an event
	 *
	 * @param   integer  $pk  Id for the cal
	 *
	 * @return  mixed Object or null
	 *
	 * @since   1.6.0
	 */
	public function &getItem($pk = null, $recursive = false) {
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('event.id');

		if ($this->_item === null) {
			$this->_item = array();
		}

		if (!isset($this->_item[$pk])) {
			try {
				$db = $this->getDbo();
				
				$isParent = false;
				$isChild = false;
				$child;
				$parent;
				
				if(!$recursive) {
					//get a pre-query
					$query = $db->getQuery(true);
					$query->select(array("recurring_id", "recurring_schedule"))
							->from('#__cal_events')
							->where('id = '.$pk);
					$db->setQuery($query);
					$res = $db->loadObject();
					
					if (empty($res)) {
						JError::raiseError(404, JText::_('COM_CAL_ERROR_EVENT_NOT_FOUND'));
					}

					//now check the items recurring status
					if($res->recurring_id) {
						//it's a child
						//get additional information from parent
						$isChild = true;
						$parent = $this->getItem($res->recurring_id, true);
					}
					elseif($res->recurring_schedule) {
						//it's a parent
						//get the upcoming event of the child
						$isParent = true;
						
						//ask the db
						$query = $db->getQuery(true);
						$query->select('id')
								->from('#__cal_events')
								->where('start > NOW()')
								->where('recurring_id = '.$pk)
								->order('start ASC');
						$db->setQuery($query, 0, 1);
						$childId = $db->loadResult();
						
						$child = $this->getItem($childId, true);
					}
				
				}
				
				$query = $db->getQuery(true);
				
				$query->select(array("a.id", "a.name", 'a.catid', 'a.alias', 'a.location_id', 'a.access',
					'a.start', 'a.end', 'a.recurring_id',
					'a.introtext', 'a.fulltext', 'a.metakey', 'a.metadesc', 'a.link',
					'b.title AS category_title', 'b.alias AS category_alias', 'b.access AS category_access',
					'c.name AS loc_name', 'c.ID AS loc_id', 'c.addrStreet', 'c.addrZip', 'c.addrCity', 'c.addrCountry', 'c.geoX', 'c.geoY', 'c.link AS loc_link', 'c.desc AS loc_desc'
					))
					->from('#__cal_events AS a')

					// Join on category and locations table.
					->join('LEFT', '#__categories AS b on b.id = a.catid')
					->join('LEFT', '#__cal_locations AS c on c.ID = a.location_id')

					->where('a.id = ' . (int) $pk)
					->where('a.state = 1');
				
				$db->setQuery($query);
				$data = $db->loadObject();
				
				if (empty($data)) {
					JError::raiseError(404, JText::_('COM_CAL_ERROR_EVENT_NOT_FOUND'));
				}
				
				// Compute access permissions.
				if ($access = $this->getState('filter.access')) {
					// If the access filter has been set, we already know this user can view.
				}
				else {
					// If no access filter is set, the layout takes some responsibility for display of limited information.
					$user = JFactory::getUser();
					$groups = $user->getAuthorisedViewLevels();
					
					if(!in_array($data->access, $groups) || !in_array($data->category_access, $groups)) {
						//user is not allowed to see this, pretend there is nothing here
						JError::raiseError(404, JText::_('COM_CAL_ERROR_EVENT_NOT_FOUND'));
					}
				}
				
				if($isParent) {
					$data = CalModelEvent::recurringHelper($data, $child);
				}
				elseif($isChild) {
					$data = CalModelEvent::recurringHelper($parent, $data);
				}
				
				//all this data will get merged into data
				$add = array('isParent' => $isParent, 'isChild' => $isChild);
				
				//add data from above to data
				$data = (object) array_merge((array) $data, $add);
				
				$this->_item[$pk] = $data;
			}
			catch (Exception $e) {
				$this->setError($e);
				$this->_item[$pk] = false;
			}
		}
		

		return $this->_item[$pk];
	}
	
	public function getForm($data = array(), $loadData = true) {
		// Get the form. copy pasta!!
		$form = $this->loadForm('com_cal.event', 'event', array('control' => 'jform', 'load_data' => true));

		if (empty($form)) {
			return false;
		}

		$id = $this->getState('event.id');
		$params = $this->getState('params');
		$event = $this->_item[$id];

		return $form;
	}
	
	public static function recurringHelper($parent, $child) {
		//basically overwrite every blank property of child with parent's property
		if(!$child->introtext)
			$child->introtext = $parent->introtext;
		if(!$child->fulltext)
			$child->fulltext = $parent->fulltext;
		if(!$child->metakey)
			$child->metakey = $parent->metakey;
		if(!$child->metadesc)
			$child->metadesc = $parent->metadesc;
		if(!$child->link)
			$child->link = $parent->link;
		
		return $child;
	}
	
	public function getRelatedEvents($pk = null) {
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('event.id');
		
		$db = $this->getDbo();
		
		$item = $this->_item[$pk];
		$recurring_id = 0;
		//try to find a recurring id
		if($item->isChild) {
			$recurring_id = $item->recurring_id;
		}
		elseif($item->isParent) {
			$recurring_id = $pk;
		}
		
		//try to balance the amount of related events
		$nRecurring = 2; //hardcoded
		$nCategory = 2;
		$nLocation = 2;
		$nUpcoming = 2;
		
		$n = 0; //not static
		
		$evemts = array();
		
		//the common part of the query
		$mQuery = $db->getQuery(true);
		$mQuery->select(array('id', 'name', 'start', 'end'))
				->from('#__cal_events')
				->where('start > NOW()')
				->where('recurring_schedule = ""')
				->where('access = 1') //hardcoded access level
				->where('id != '.$item->id) //also works when pk isParent because their item->id get overwritten by swapping in recurringHelper's params
				->order('start ASC');
		
		//go through the 4 different kind of related events
		if($recurring_id) {
			$query = clone $mQuery;
			$query->where('recurring_id = '.$recurring_id);
			$db->setQuery($query, 0, $nRecurring);
			$res = $db->loadObjectList();
			$n = $nRecurring - count($res); //count up if it wasn't enough
			
			foreach($res as $event) {
				$events[] = $event;
				$mQuery->where('id != '.$event->id);
			}
			
		}
		else
			$nCategory += $nRecurring;
		//category
		$query = clone $mQuery;
		$query->where('catid = '.$item->catid);
		$db->setQuery($query, 0, $nCategory + $n);
		$res = $db->loadObjectList();
		$n = $nCategory + $n - count($res);
		
		foreach($res as $event) {
			$events[] = $event;
			$mQuery->where('id != '.$event->id);
		}
		
		//location
		$query = clone $mQuery;
		$query->where('location_id = '.$item->location_id);
		$db->setQuery($query, 0, $nLocation + $n);
		$res = $db->loadObjectList();
		$n = $nLocation + $n - count($res);
		
		foreach($res as $event) {
			$events[] = $event;
			$mQuery->where('id != '.$event->id);
		}
		
		//upcoming
		$query = $mQuery; //dont need to clone here anymore
		$db->setQuery($query, 0, $nUpcoming + $n);
		$res = $db->loadObjectList();
		
		$events = array_merge($events, $res);
		
		//now sort them by start date
		function sortHelper(&$a, &$b) {
			return $a->start > $b->start; //I guess these date strings should be sortable as strings (if it gets weird it probably doesnt -> convert to UNIX and compare)
		}
		usort($events, 'sortHelper');
		
		return $events;
	}
}
