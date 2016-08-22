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
 * Contact Table class.
 *
 * @since  1.0
 */
class CalTableResource extends JTable {
	
	public function __construct(&$db) {
		parent::__construct('#__cal_resources', 'id', $db); //table, primary key and database object
	}
	
	public function publish($pks = null, $state = 1, $userId = 0) {
		//overwrite for actually deleting stuff
		//catch when state is -2
		
		if($state != -2)
			return true; //publish shouldn't be called with anything different anyways
						//we only use publish for deleting stuff, so there is no need to consider processing other states
		
		
		// Sanitize input
		$userId = (int) $userId;
		$state  = (int) $state;

		if (!is_null($pks))
		{
			if (!is_array($pks))
			{
				$pks = array($pks);
			}

			foreach ($pks as $key => $pk)
			{
				if (!is_array($pk))
				{
					$pks[$key] = array($this->_tbl_key => $pk);
				}
			}
		}

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			$pk = array();

			foreach ($this->_tbl_keys AS $key)
			{
				if ($this->$key)
				{
					$pk[$key] = $this->$key;
				}
				// We don't have a full primary key - return false
				else
				{
					$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));

					return false;
				}
			}

			$pks = array($pk);
		}


		foreach ($pks as $pk)
		{
			// Update the publishing state for rows with the given primary keys.
			$query = $this->_db->getQuery(true)
				->delete($this->_tbl);//we don't update but delete it all


			// Build the WHERE clause for the primary keys.
			$this->appendPrimaryKeys($query, $pk);

			$this->_db->setQuery($query);

			try
			{
				$this->_db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->setError($e->getMessage());

				return false;
			}

		}

		$this->setError('');

		return true;
	}
}