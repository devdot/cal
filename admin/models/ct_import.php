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
 * Cal Locations Model
 *
 * @since  0.0.1
 */
class CalModelCT_Import extends JModelList {
	/**
	 * @var array messages
	 */
	protected $messages;
 
    public function __construct($config = array()) {
        //not quite sure what this is about, so....
        //https://techjoomla.com/developers-blogs/joomla-development/joomla-using-jlayouts-search-tools-on-joomla-3-x-at-admin-backend-for-list-views.html
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'id',
				'state', 'state',
                'name', 'name'
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
        $published = $app->getUserStateFromRequest($this->context . 'filter.state', 'filter_state', '', 'string');
        $this->setState('filter.state', $published);
        
        $this->setState('filter.name', 'ASC');
 
        // Other code goes here
 
        // List state information.
        parent::populateState('name', 'ASC');
    }
    
	protected function getListQuery() {
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		//$user = JFactory::getUser();
        
        //only need those columns
        $query->select(array("id", "name", "state"));
        $query->from("#__cal_ct_import");
        
        //the system takes care of limits, also putting the query together
        
        //if the filter is inactive, it's state is ''
        if(is_numeric($this->getState("filter.state"))) {
            if($this->getState("filter.state") === '0')
                $query->where("state = 0");
            else
                $query->where("state = 1");
        }
        
        if(!empty($this->getState("filter.search"))) {
            $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($this->getState("filter.search")), true) . '%'));
            $query->where('name LIKE '.$search);
        }
        
        $query->order($db->escape($this->getState('list.ordering', 'name') . ' ' . $this->getState('list.direction', 'ASC')));
        //$query->setQuery("SELECT ID, name, published FROM #__cal_locations");
		
		return $query;
	}
}