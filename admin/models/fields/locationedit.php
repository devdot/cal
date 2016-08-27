<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_categories
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\Utilities\ArrayHelper;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @since  1.6
 */
class JFormFieldLocationEdit extends JFormFieldList
{

	/**
	 * Copy-pasta from categoryedit.php (com_categories)
	 *
	 * @var    string
	 * @since  1.6
	 */
	public $type = 'LocationEdit';


	/**
	 * Method to get a list of locations.
	 * Use the parent element to indicate that the field will be used for assigning parent categories.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   1.6
	 */
	protected function getOptions() {
		$options = array();

		// Let's get the id for the current item, either category or content item.
		$jinput = JFactory::getApplication()->input;

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('id AS value, name AS text, addrStreet, addrCity')
			->from('#__cal_locations')
			->where('published = 1') //only published locations
			->order('name ASC');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		// Pad the option text with spaces using depth level as a multiplier.
		foreach ($options as $option) {
			//adding some nice contextual information
			$str = $option->addrStreet;
			if(!empty($str) and !empty($option->addrCity))
				$str .= ', ';
			if(!empty($option->addrCity))
				$str .= $option->addrCity;
			if(!empty($str))
				$option->text .= ' ('.$str.')';
		}


		

		// Merge any additional options in the XML definition.
		return array_merge(parent::getOptions(), $options);
	}
}
