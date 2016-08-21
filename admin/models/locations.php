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
 * Cal Model
 *
 * @since  0.0.1
 */
class CalModelLocations extends JModelList {
	/**
	 * @var array messages
	 */
	protected $messages;
 
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'ID', 'ID',
                'published', 'published',
                'hasGeoloc', 'hasGeoloc',
                'zip', 'addrZip',
                'street', 'addrStreet',
                'name', 'name'
            );
        }
 
        parent::__construct($config);
    }
    
    protected function populateState($ordering = null, $direction = null) {
        // Initialise variables.
        $app = JFactory::getApplication('administrator');
 
        // Other code goes here
 
        $published = $app->getUserStateFromRequest($this->context . 'filter.published', 'filter_published', '', 'string');
        $this->setState('filter.published', $published);
        
        $geo = $app->getUserStateFromRequest($this->context . 'filter.hasGeoloc', 'filter_published', '', 'string');
        $this->setState('filter.hasGeoloc', $geo);
        
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
        
        $query->select(array("ID", "name", "addrStreet", "addrZip", "published"));
        $query->from("#__cal_locations");
        
        if(is_numeric($this->getState("filter.published"))) {
            if($this->getState("filter.published") === '0')
                $query->where("published = 0");
            else
                $query->where("published = 1");
        }
        
        if(is_numeric($this->getState("filter.hasGeoloc"))) {
            if($this->getState("filter.hasGeoloc") === '0')
                $query->where("geoLoc IS NULL");
            else
                $query->where("geoLoc IS NOT NULL");
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