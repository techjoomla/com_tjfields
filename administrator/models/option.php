<?php
/**
 * @package    TjFields
 *
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2019 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

/**
 * Methods supporting a list of Tjfields option record.
 *
 * @since  _DEPLOY_VERSION_
 */
class TjfieldsModelOption extends JModelItem
{
	/**
	 * Constructor.
	 *
	 * @param   Array  $config  An optional associative array of configuration settings.
	 *
	 * @since  _DEPLOY_VERSION_
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'field_id', 'a.field_id',
				'options', 'a.options',
				'value', 'a.value'
			);
		}

		parent::__construct($config);
	}


	/**
	 * Method to get data items.
	 *
	 * @return  mixed  A data items on success, false on failure.
	 *
	 * @since  _DEPLOY_VERSION_
	 */
	public function getItem()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Get the ID from state if not provided
		$id = (!empty($id)) ? (int) $id : (int) $this->getState('filter.id');

		// If no ID is found, return false
		if (!$id) {
			return false;
		}

		// Build the query to fetch record by ID
		$query->select('*')
			->from($db->quoteName('#__tjfields_options'))
			->where($db->quoteName('id') . ' = ' . (int) $id);
		$db->setQuery($query);

		// Fetch the result
		$item = $db->loadObject();

		return $item;
	}
}
