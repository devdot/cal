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
 * CT Import Rules controller class.
 *
 * @since  1.6
 */
class CalControllerCtImport extends JControllerAdmin {
	
	protected $token; //whether the jsession token should be checked
	
	public function __construct($config = array()) {
		parent::__construct($config);
		
		if(isset($config['token']))
			$this->token = (bool) $config['token'];
		else
			$this->token = true;
		
		//$this->registerTask('recurring', 'recurring'); //new task for make children of an recurring event
	}

	
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The name of the model.
	 * @param   string  $prefix  The prefix for the PHP class name.
	 * @param   array   $config  Array of configuration parameters.
	 *
	 * @return  JModelLegacy
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'CT_ImportRule', $prefix = 'CalModel', $config = array('ignore_request' => true)) {
		//Joomla routing will use this function to proxy my model (CalModelLocation)
		//for task locations.publish it will call CalModelLocation::publish();
		return parent::getModel($name, $prefix, $config);
	}
}