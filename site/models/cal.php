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
	
	private $start;
	private $end;
	
	public function __construct($config = array()) {
		parent::__construct($config);
		
		// get timezone
		JLoader::import('cal', JPATH_COMPONENT.'/helpers/');
		$tz = CalHelper::getTimeZone();
	
		
		//set start date
		//its the last sunday
		$this->start = new JDate(strtotime('last Sunday UTC')); //probably not the most efficient way of doing this ...
		$this->end = new JDate($this->start->getTimestamp() + 3024000); //5 weeks, one week = 604800
		
		//hopefully this timezone stuff works ...
		$this->start->setTimezone($tz);
		$this->end->setTimezone($tz);
	}
	
	public function getItems() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		//fetch all the events we need for now
		$query->select(array('id', 'name', 'start', 'end'))
				->from('#__cal_events')
				->where('state = 1')
				->where('recurring_schedule = ""')
				->where('end > "'.$this->start->toISO8601(true).'"')
				->where('start < "'.$this->end->toISO8601(true).'"')
				->order('start ASC');
		//load from db
		$db->setQuery($query);
		//and return
		return $db->loadObjectList();
	}
	
	public function getStart() {
		return $this->start;
	}
	
	public function getEnd() {
		return $this->end;
	}
	
	protected function populateState($ordering = null, $direction = null) {
		$app = JFactory::getApplication();

		$params = $app->getParams();
		$this->setState('params', $params);

		$this->setState('filter.published',	1);
		$this->setState('filter.access', $app->input->get('access', 1, 'int'));
	}
}