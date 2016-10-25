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
 * Single item model for an event
 *
 * @package     Joomla.Site
 * @subpackage  com_cal
 * @since       1.5
 */
class CalModelEvent extends JModelForm
{
	/**
	 * The name of the view for a single item
	 *
	 * @since   1.6
	 */
	protected $view_item = 'event';

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
	protected $_context = 'com_cal.event';

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
		$this->setState('event.id', $pk);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

		$user = JFactory::getUser();

		if ((!$user->authorise('core.edit.state', 'com_cal')) &&  (!$user->authorise('core.edit', 'com_cal'))) {
			$this->setState('filter.published', 1);
			$this->setState('filter.archived', 2);
		}
	}

	/**
	 * Gets an event
	 *
	 * @param   integer  $pk  Id for the cal
	 *
	 * @return  mixed Object or null
	 *
	 * @since   1.6.0
	 */
	public function &getItem($pk = null) {
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('event.id');

		if ($this->_item === null) {
			$this->_item = array();
		}

		if (!isset($this->_item[$pk])) {
			try {
				$db = $this->getDbo();
				$query = $db->getQuery(true);

				$query->select(array("a.id", "a.name", 'a.catid', 'a.alias', 'a.location_id', 'a.access',
					'a.start', 'a.end', 'a.recurring_id', 'a.recurring_schedule',
					'a.introtext', 'a.fulltext', 'a.metakey', 'a.metadesc', 'a.link',
					'b.title AS category_title', 'b.alias AS category_alias', 'b.access AS category_access',
					'c.name AS loc_name', 'c.ID AS loc_id', 'c.addrStreet', 'c.addrZip', 'c.addrCity', 'c.addrCountry', 'c.geoLoc', 'c.link AS loc_link', 'c.desc AS loc_desc'
					))
					->from('#__cal_events AS a')

					// Join on category and locations table.
					->join('LEFT', '#__categories AS b on b.id = a.catid')
					->join('LEFT', '#__cal_locations AS c on c.ID = a.location_id')

					->where('a.id = ' . (int) $pk)
					->where('a.state = 1');
				
				$db->setQuery($query);
				$data = $db->loadObject();
				
				if (empty($data)) {
					JError::raiseError(404, JText::_('COM_CAL_ERROR_EVENT_NOT_FOUND'));
				}
				
				// Compute access permissions.
				if ($access = $this->getState('filter.access')) {
					// If the access filter has been set, we already know this user can view.
				}
				else {
					// If no access filter is set, the layout takes some responsibility for display of limited information.
					$user = JFactory::getUser();
					$groups = $user->getAuthorisedViewLevels();
					
					if(!in_array($data->access, $groups) || !in_array($data->category_access, $groups)) {
						//user is not allowed to see this, pretend there is nothing here
						JError::raiseError(404, JText::_('COM_CAL_ERROR_EVENT_NOT_FOUND'));
					}
				}
				
				$this->_item[$pk] = $data;
			}
			catch (Exception $e) {
				$this->setError($e);
				$this->_item[$pk] = false;
			}
		}
		

		return $this->_item[$pk];
	}
	
	public function getForm($data = array(), $loadData = true) {
		// Get the form. copy pasta!!
		$form = $this->loadForm('com_cal.event', 'event', array('control' => 'jform', 'load_data' => true));

		if (empty($form)) {
			return false;
		}

		$id = $this->getState('event.id');
		$params = $this->getState('params');
		$event = $this->_item[$id];

		return $form;
	}
}
