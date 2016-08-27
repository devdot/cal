<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_categories
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');
JLoader::register('CalHelperResources', JPATH_COMPONENT . '/helpers/resources.php');

/**
 * Form Field class for the Joomla Framework.
 *
 * @since  1.6
 */
class JFormFieldResourceEdit extends JFormFieldList
{

	/**
	 * Copy-pasta from categoryedit.php (com_categories)
	 *
	 * @var    string
	 * @since  1.6
	 */
	public $type = 'ResourceEdit';


	/**
	 * Method to get a list of resources.
	 * Use the parent element to indicate that the field will be used for assigning parent categories.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   1.6
	 */
	protected function getOptions() {
		$options = array();


		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('id AS value, name AS text, type')
			->from('#__cal_resources')
			->order('type ASC, name ASC');

		// Get the options.
		$db->setQuery($query);

		try {
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e) {
			JError::raiseWarning(500, $e->getMessage());
		}

		// Pad the option text with spaces using depth level as a multiplier.
		foreach ($options as $option) {
			//adding some nice contextual information
			$option->text = CalHelperResources::type($option->type).": ".$option->text;
		}
		$a = new stdClass(); //first element with select text
		$a->value = '';
		$a->text = ''; //JText::_('COM_CAL_RESOURCE_SELECT_TYPE') leaving it empty makes it default to joomla's version of select text
		array_unshift($options, $a); //push on the front


		

		// Merge any additional options in the XML definition.
		return array_merge(parent::getOptions(), $options);
	}
}
