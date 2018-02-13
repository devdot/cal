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
        
		// get the current input to check to format feed
		$input = JFactory::getApplication()->input;
		$isHtml = $input->get('format') == 'html';
		
        //only need those columns (if it is not a feed)
		$sel = array('a.id', 'a.name', 'a.start', 'a.end',
					'b.title AS cat_name', 'a.catid');
		
		// append some more columns for the feed
		if(!$isHtml)
			$sel = array_merge($sel, array('a.created', 'c.name AS location_name', 'c.addrCity AS city', 'a.recurring_id', 
				'a.introtext', 'a.fulltext', 'd.introtext AS parent_introtext', 'd.fulltext AS parent_fulltext'));
		
		$query->select($sel)
				->from('#__cal_events AS a')
				->where('a.state = 1')
				->where('a.recurring_schedule = ""')
				->where('a.end > NOW()')
				->leftJoin('#__categories AS b ON b.id = a.catid');
		
		// join with location as well if it's a feed
		if(!$isHtml) {
			$query->leftJoin('#__cal_locations AS c ON a.location_id = c.id');
			$query->leftJoin('#__cal_events AS d ON a.recurring_id = d.id');
		}
		
		//allow filter for category
		if(is_numeric($this->getState('filter.catid'))) {
			$catid = (int) $this->getState('filter.catid');
			$query->where('a.catid = '.$catid);
		}
		
        //the system takes care of limits, also putting the query together
		$query->limit(0);
		
        if(!empty($this->getState("filter.search"))) {
            $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($this->getState("filter.search")), true) . '%'));
            $query->where('a.name LIKE '.$search);
        }
		
        $query->order('start ASC');
		
		return $query;
	}
	
	
}