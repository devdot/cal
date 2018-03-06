<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_cal
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Routing class from com_cal
 *
 * @since  3.3
 */
class CalRouter extends JComponentRouterBase {
	
	protected $eventsStart;
	protected $calMenuItems = array();
	
	public function __construct($app = null, $menu = null) {
		parent::__construct($app, $menu);
		
		//get all menuitems related to this component
		$arr = $this->menu->getItems('component', 'com_cal');
		
		//now presort them by view (bucketsort HYPE)
		foreach($arr as $item) {
			$this->calMenuItems[$item->query['view']][] = $item;
		}
		 
		 //get the first menu item that routes to com_cal and use it as hub
		$this->eventsStart = $this->calMenuItems['cal'][0]; //use this one forever
	}
	
	/**
	 * Build the route for the com_cal component
	 *
	 * @param   array  &$query  An array of URL arguments
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 *
	 * @since   3.3
	 */
	public function build(&$query) {
		$segments = array();
		
		// the html format is assumed
		if(isset($query['format']) && $query['format'] == 'html')
			unset($query['format']);
		
		// Unset limitstart=0 since it's pointless
		if (isset($query['limitstart']) && $query['limitstart'] == 0) {
			unset($query['limitstart']);
		}

		// We need a menu item.  Either the one specified in the query, or the current active one if none specified
		if (empty($query['Itemid'])) {
			$menuItem = $this->menu->getActive();
			$menuItemGiven = false;
		}
		else {
			$menuItem = $this->menu->getItem($query['Itemid']);
			$menuItemGiven = true;
		}
		
		// Check again
		if ($menuItemGiven && isset($menuItem) && $menuItem->component != 'com_cal') {
			$menuItemGiven = false;
			unset($query['Itemid']);
		}

		if (isset($query['view'])) {
			$view = $query['view'];
		}
		else {
			// We need to have a view in the query or it is an invalid URL
			return $segments;
		}
		
		// Are we dealing with something that is attached to a menu item?
		if (($menuItem instanceof stdClass)
			&& $menuItem->query['view'] == $query['view']
			&& isset($query['id'])
			&& $menuItem->query['id'] == (int) $query['id']) {
			unset($query['view']);

			if (isset($query['catid'])) {
				unset($query['catid']);
			}

			if (isset($query['layout'])) {
				unset($query['layout']);
			}

			unset($query['id']);
			
			//return
			if(isset($query['format']) && $query['format'] == 'ics' && count($segments) > 0) {
				unset($query['format']);
				$segments[count($segments) - 1] .= '.ics';
			}
			return $segments;
		}
		
		//check whether there is already a menu item for this
		if(isset($this->calMenuItems[$view])) {
			foreach($this->calMenuItems[$view] as $item) {
				//if($item->query['view'] != $view)
				//	continue;
				if(isset($query['id']) && $item->query['id'] != $query['id'])
					continue;
				//might need more distinctinon in the future
				//found the fitting one
				unset($query['view']);
				unset($query['id']);

				//reset menu item id
				$query['Itemid'] = $item->id;

				//return
				if(isset($query['format']) && $query['format'] == 'ics' && count($segments) > 0) {
					unset($query['format']);
					$segments[count($segments) - 1] .= '.ics';
				}
				return $segments;
			}
		}
		
		if ($view == 'event') {
			$menuItem = $this->eventsStart;
			$query['Itemid'] = $this->eventsStart->id;
			
			
			unset($query['view']);
			


			// Make sure we have the id and the alias
			if (strpos($query['id'], ':') === false) {
				$db = JFactory::getDbo();
				$dbQuery = $db->getQuery(true)
					->select('alias')
					->from('#__cal_events')
					->where('id=' . (int) $query['id']);
				$db->setQuery($dbQuery);
				$alias = $db->loadResult();
				$query['id'] = $alias . ':' . $query['id'];

			}
			
			$segments[] = $query['id'];

			unset($query['id']);
			unset($query['catid']);
		}
		elseif($view == $menuItem->query['view']) {
			unset($query['view']);
		}

		/*
		 * If the layout is specified and it is the same as the layout in the menu item, we
		 * unset it so it doesn't go into the query string.
		 */
		if (isset($query['layout'])) {
			if ($menuItemGiven && isset($menuItem->query['layout'])) {
				if ($query['layout'] == $menuItem->query['layout']) {
					unset($query['layout']);
				}
			}
			else {
				if ($query['layout'] == 'default') {
					unset($query['layout']);
				}
			}
		}

		$total = count($segments);

		for ($i = 0; $i < $total; $i++) {
			$segments[$i] = str_replace(':', '-', $segments[$i]);
		}
		
		//return
		if(isset($query['format']) && $query['format'] == 'ics' && count($segments) > 0) {
			unset($query['format']);
			$segments[count($segments) - 1] .= '.ics';
		}
		return $segments;
	}

	/**
	 * Parse the segments of a URL.
	 *
	 * @param   array  &$segments  The segments of the URL to parse.
	 *
	 * @return  array  The URL attributes to be used by the application.
	 *
	 * @since   3.3
	 */
	public function parse(&$segments) {
		$total = count($segments);
		$vars = array();

		/*for ($i = 0; $i < $total; $i++) {
			$segments[$i] = preg_replace('/-/', ':', $segments[$i], 1);
		}*/ // we don't need this anymore (but keep it in case we do and I'm stupid)

		// Get the active menu item.
		$item = $this->menu->getActive();
		//$db = JFactory::getDbo();
		
		// assume html format
		$vars['format'] = 'html';
		
		//check last segment for format
		if(substr($segments[$total - 1], -4) == '.ics') {
			$vars['format'] = 'ics';
			$segments[$total - 1] = substr($segments[$total - 1], 0, -4);
		}


		if($item == $this->eventsStart && $total == 1) {
			//just a simple event that's routed over eventsStart
			$vars['view'] = 'event';
			$expl = explode('-', $segments[0]);
			$vars['id'] = (int) array_pop($expl); //this field is {alias}-{id}, but the alias very likely contains '-' so we need the last element
			
			return $vars;
		}
		
		return $vars;
	}
}

function calBuildRoute(&$query) {
	$router = new CalRouter;
	
	return $router->build($query);
}


function calParseRoute($segments) {
	$router = new CalRouter;

	return $router->parse($segments);
}
