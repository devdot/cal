<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Item Model for a Location.
 *
 * @since  1.6
 */
class CalModelLocation extends JModelAdmin {
	public $typeAlias = 'com_cal.location';
	
	public function getForm($data = array(), $loadData = true) {
		
		//do I need that? I'm not getting a field from there I guess...
		//JForm::addFieldPath('JPATH_ADMINISTRATOR/components/com_users/models/fields');
		
		// Get the form.
		$form = $this->loadForm('com_cal.location', 'location', array('control' => 'jform', 'load_data' => $loadData));
		
		//I don't even know what the following does
		if (empty($form))
		{
			return false;
		}
		
		return $form;
	}
	
	public function getTable($type = 'Location', $prefix = 'CalTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}
	
	protected function loadFormData() {
		$app = JFactory::getApplication();

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_cal.edit.location.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}
	
	public function save($data) {
		//fetch geolocation from google
		if($data['geoAuto'] && JComponentHelper::getParams('com_cal')->get('maps_use')) {
			$key = JComponentHelper::getParams('com_cal')->get('maps_key');
			$addr = $data['addrStreet'].','.$data['addrZip'].'+'.$data['addrCity'].','.$data['addrCountry'];
			$url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.str_replace(' ', '+', $addr).'&key='.$key;
		
			$json = file_get_contents($url);
			$obj = json_decode($json);
			if(isset($obj->results[0]->geometry->location)) {
				$data['geoX'] = $obj->results[0]->geometry->location->lat;
				$data['geoY'] = $obj->results[0]->geometry->location->lng;
			}
		}
		//handle geoLocation
		if(!is_numeric($data['geoX']) or !is_numeric($data['geoY'])) {
			$data['geoX'] = null;
			$data['geoY'] = null;
		}
		
		//now hand it of to the parent
		return parent::save($data); 
	}
	
	protected function prepareTable($table) {
		//extra handling for geolocation
		if($table->geoX == '0' && $table->geoY == '0') {
			$table->geoX = null;
			$table->geoY = null;
		}
	}
}