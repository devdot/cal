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
class CalModelCal extends JModelList {

 
	
	protected function getListQuery() {
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		//$user = JFactory::getUser();
        
        //only need those columns
        $query->select(array("a.id", "a.name", 'a.start', 'a.recurring_id',
			'b.name AS user_name', 'a.created_by', 
			'c.name AS location_name', 'a.location_id',
			'd.title AS cat_name', 'a.catid'));
		$query->from("#__cal_events AS a");
		$query->leftJoin('#__users AS b ON b.id = a.created_by');
		$query->leftJoin('#__cal_locations AS c ON c.id = a.location_id');
		$query->leftJoin('#__categories AS d ON d.id = a.catid');
        $query->order('created DESC');
		$query->setLimit(10);

		return $query;
	}
	
	public function getBuildInfo() {
		// just fetch the build xml file
		$xml = simplexml_load_file(JPATH_COMPONENT.'/build.xml');
		
		return $xml;
	}
 
}