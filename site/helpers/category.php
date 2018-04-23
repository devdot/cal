<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_cal
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Calendar Component Category Tree
 *
 * @since  1.6
 */
class CalCategories extends JCategories
{
	/**
	 * Class constructor
	 *
	 * @param   array  $options  Array of options
	 *
	 * @since   11.1
	 */
	public function __construct($options = array()) {
		$options['table'] = '#__events';
		$options['extension'] = 'com_cal';

		parent::__construct($options);
	}
}
