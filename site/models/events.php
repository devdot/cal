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
 * Events Model
 *
 * @since  0.0.1
 */
class CalModelEvents extends JModelList {
	
	public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array( 
				'catid',
				'cat_name',
                'name'
            );
        }
        //hand along to superclass constructor
        parent::__construct($config);
    }
    
    protected function populateState($ordering = null, $direction = null) {
        // Initialise variables.
        $app = JFactory::getApplication();
 
        //$this->setState('filter.extension', 'com_cal');
		$params = $app->getParams();
		$this->setState('params', $params);
		
		
		$catid = $app->getUserStateFromRequest($this->context . 'filter.catid', 'filter_type', '', 'string');
        $this->setState('filter.catid', $catid);
		
		parent::populateState();
		
		$limitstart = $app->input->get('limitstart', 0, 'uint');
		$this->setState('list.start', $limitstart); //need to put that here, normally this should be done inside parent but it's buggy
		
    }
    
	protected function getListQuery() {
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		//$user = JFactory::getUser();
        
        //only need those columns
		$query->select(array('a.id', 'a.name', 'a.start', 'a.end',
					'b.title AS cat_name', 'a.catid'))
				->from('#__cal_events AS a')
				->where('state = 1')
				->where('recurring_schedule = ""')
				->where('end > NOW()')
				->leftJoin('#__categories AS b ON b.id = a.catid');
		
		//allow filter for category
		if(is_numeric($this->getState('filter.catid'))) {
			$catid = (int) $this->getState('filter.catid');
			$query->where('a.catid = '.$catid);
		}
		
        //the system takes care of limits, also putting the query together
		
        if(!empty($this->getState("filter.search"))) {
            $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($this->getState("filter.search")), true) . '%'));
            $query->where('a.name LIKE '.$search);
        }
		
        $query->order('start ASC');
		
		return $query;
	}
	
	
}