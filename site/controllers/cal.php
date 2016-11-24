<?php
 
// No direct access.
defined('_JEXEC') or die;

 
class CalControllerCal extends JControllerLegacy {
 
	private $key;
	private $active;
	
	public function __construct($config = array()) {
		parent::__construct($config);
		
		$this->key = JComponentHelper::getParams('com_cal')->get('cron_key');
		$this->active = (bool)JComponentHelper::getParams('com_cal')->get('cron_use');
		
		$this->registerTask('recurring', 'recurring');
		$this->registerTask('archive', 'archive');
	}
	
	public function recurring() {
		if(!$this->active)
			die; //die when there is no reason to live anymore.
		
		//get the key from the user
		$key = JFactory::getApplication()->getUserStateFromRequest('key', 'key', '', 'string');
		//check whether it's correct. if not, die instantly
		if($key != $this->key)
			die;
		
		
		//NOT WORKING HERE
		//loading backend classes from frontend is a huge mess and does not really work
		//we don't use this for exactly this reason
		//we also would need a user to be set (for saving the editor of recurring children)
		
		//load the controller
		jimport('joomla.application.component.controller');
		JLoader::import('events', JPATH_COMPONENT_ADMINISTRATOR.'/controllers');
		$controller = new CalControllerEvents(array('token' => false)); //I'm not using JControllerLegacy::getInstance('event'); ... that might cause issues
		
		//load the models
		JLoader::import('events', JPATH_COMPONENT_ADMINISTRATOR.'/models');
		JLoader::import('event', JPATH_COMPONENT_ADMINISTRATOR.'/models');
		
		//$eventsModel = new CalModelEvents();
		var_dump( JPATH_COMPONENT_ADMINISTRATOR.'/models');
	}
	
	public function display($cachable = false, $urlparams = array()) {
		//dummy, because we don't want anything to happen here
		die; //die here, so nothing can be displayed
	}

 
}