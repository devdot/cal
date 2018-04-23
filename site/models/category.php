<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_cal
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * Single category of calendar component
 *
 * @package     Joomla.Site
 * @subpackage  com_cal
 * @since       1.5
 */
class CalModelCategory extends JModelItem
{
	/**
	 * The name of the view for a single item
	 *
	 * @since   1.6
	 */
	protected $view_item = 'category';

	/**
	 * A loaded item
	 *
	 * @since   1.6
	 */
	protected $_item = null;

	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context = 'com_cal.category';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState() {
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = $app->input->getInt('id');
		$this->setState('category.id', $pk);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
	}

	/**
	 * Gets an category
	 *
	 * @param   integer  $pk  Id for the cal
	 *
	 * @return  mixed Object or null
	 *
	 * @since   1.6.0
	 */
	public function &getItem($pk = null) {
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('category.id');

		if ($this->_item === null) {
			$this->_item = array();
		}

		if (!isset($this->_item[$pk])) {
			try {
				// get the entry from db
				$category = JCategories::getInstance('cal')->get($pk);		
				
				if ($category == null) {
					JError::raiseError(404, JText::_('COM_CAL_ERROR_CATEGORY_NOT_FOUND'));
				}
				
				$this->_item[$pk] = $category;
			}
			catch (Exception $e) {
				$this->setError($e);
				$this->_item[$pk] = false;
			}
		}
		

		return $this->_item[$pk];
	}
	
	
	public function &getEvents($pk = null) {
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('category.id');
		
		$db = $this->getDbo();
		
		// build a simple query
		$query = $db->getQuery(true);
		$query->select(array('id', 'name', 'start', 'end'))
				->from('#__cal_events')
				->where('start > NOW()')
				->where('recurring_schedule = ""') // no recurring heads
				->where('access = 1') // hardcoded access level
				->where('catid = '.$pk) // only select the current category
				->order('start ASC')
				->setLimit(10);
		
		$db->setQuery($query);
		$res = $db->loadObjectList();
		
		return $res;
	}
}
