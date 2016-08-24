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
 * Cal Events Model
 *
 * @since  0.0.1
 */
class CalModelEvents extends JModelList {
	/**
	 * @var array messages
	 */
	protected $messages;
 
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 
                'state',
                'cat_name',
				'catid',
                'name',
				'start',
				'end',
				'access',
				'access_name',
				'location'
            );
        }
        //hand along to superclass constructor
        parent::__construct($config);
    }
    
    protected function populateState($ordering = null, $direction = null) {
        // Initialise variables.
        $app = JFactory::getApplication('administrator');
 
        // Other code goes here
 
        //take data from user requests and put it into the user state
        //first param: state name (for accessing)
        //second param: I don't really know
        //third: default value, if first is empty
        //fourth: filter for first param
        $type = $app->getUserStateFromRequest($this->context . 'filter.state', 'filter_type', '', 'string');
        $this->setState('filter.state', $type);
		$catid = $app->getUserStateFromRequest($this->context . 'filter.catid', 'filter_type', '', 'string');
        $this->setState('filter.catid', $catid);
		$access = $app->getUserStateFromRequest($this->context . 'filter.access', 'filter_type', '', 'string');
        $this->setState('filter.access', $access);
        
		
        $this->setState('filter.start', 'DESC');
 
        // Other code goes here
 
        // List state information.
        parent::populateState('state', 'DESC');
    }
    
	protected function getListQuery() {
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		//$user = JFactory::getUser();
        
        //only need those columns
        $query->select(array("a.id", "a.name", "a.state", 'a.catid', 'a.alias', 'a.location_id',
			'a.checked_out', 'a.editor', 'a.checked_out_time', 'a.start', 'a.end',
			'c.name AS location_name',
			'b.title AS cat_name, d.title AS access_name'));
		$query->from("#__cal_events AS a");
		$query->leftJoin('#__categories AS b ON b.id = a.catid');
		$query->leftJoin('#__cal_locations AS c ON c.id = a.location_id');
		$query->leftJoin('#__viewlevels AS d ON d.id = a.access');
        
		if(is_numeric($this->getState('filter.state'))) {
			$state = (int) $this->getState('filter.state');
			$query->where('a.state = '.$state);
		}
		else {
			$query->where('a.state >= 0');
		}
		
		if(is_numeric($this->getState('filter.catid'))) {
			$catid = (int) $this->getState('filter.catid');
			$query->where('a.catid = '.$catid);
		}
		
		if(is_numeric($this->getState('filter.access'))) {
			$access = (int) $this->getState('filter.access');
			$query->where('a.access = '.$access);
		}
		
        //the system takes care of limits, also putting the query together
		
        if(!empty($this->getState("filter.search"))) {
            $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($this->getState("filter.search")), true) . '%'));
            $query->where('name LIKE '.$search);
        }
        
		$order = $db->escape($this->getState('list.ordering', 'name'));
		$dir   = $db->escape($this->getState('list.direction', 'ASC'));
		
		$order = str_replace("cat_name", "b.title", $order); //replace for ordering cat_name (different name in array than in db)
		
        $query->order($order . ' ' . $dir);
		
		return $query;
	}
}