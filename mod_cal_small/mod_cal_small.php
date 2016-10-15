<?php
 
// No direct access
defined('_JEXEC') or die;
// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';
 
$events = ModCalSmallHelper::getEvents();
require JModuleHelper::getLayoutPath('mod_cal_small');