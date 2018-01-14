<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cal
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Controller for a single CT import rule
 * 
 * @since  1.6
 */
class CalControllerCtImportRule extends JControllerForm {
	function __construct($config = array()) {
		//we aren't following the naming schema, so that why we got to rename here
		//the default constructor would set this to ct_importRules and we don't want that
		$this->view_list = 'ct_import';
		
		parent::__construct($config);
	}
}