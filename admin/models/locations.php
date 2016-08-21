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
 
	protected function getListQuery() {
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		//$user = JFactory::getUser();
        
        $query->select(array("ID", "name", "addrStreet", "addrZip", "published"));
        $query->from("#__cal_locations");
        $query->order("name ASC");
        
        //$query->setQuery("SELECT ID, name, published FROM #__cal_locations");
		
		return $query;
	}
}