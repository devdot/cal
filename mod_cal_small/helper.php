<?php

class ModCalSmallHelper {  
    public static function getEvents() {
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
					->select(array('id', 'name', 'start', 'end'))
					->from('#__cal_events')
					->where('state = 1')
					->where('recurring_schedule = ""')
					->where('end > NOW()')
					->where('access = 1') //hardcoded ...
					->order('start ASC');
		$db->setQuery($query, 0, 10);
		$upcoming = $db->loadObjectList();
		
		$query = $db->getQuery(true)
					->select(array('id', 'name', 'start', 'end'))
					->from('#__cal_events')
					->where('state = 1')
					->where('end <= NOW()')
					->where('access = 1') //hardcoded ...
					->order('start DESC');
		$db->setQuery($query, 0, 5);
		$prev = $db->loadObjectList();
		
		$r = array_chunk($upcoming, 5);
		return array("prev" => array_reverse($prev), "main" => $r[0], "next" => isset($r[1])?$r[1]:array());
	}
}