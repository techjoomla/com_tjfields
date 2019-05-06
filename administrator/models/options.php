<?php
/**
 * @version    SVN: <svn_id>
 * @package    TJField
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2014-2016 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Tjfields option records.
 *
 * @since  2.5
 */
class TjfieldsModelOptions extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   Array  $config  An optional associative array of configuration settings.
	 *
	 * @since    1.6
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
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @since   1.0
	 */
	protected function getListQuery()
	{
		// Filter by client (Set state from external view to render client specific fields)

		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select($db->quoteName('*'));
		$query->select($db->quoteName(array('id', 'field_id', 'options', 'value')));
		$query->from('`#__tjfields_options` AS a');

		// Filter by group state
		$fieldId = $this->getState('filter.field_id');

		if (is_numeric($fieldId))
		{
			$query->where('a.field_id = ' . (int) $fieldId);
		}

		return $query;
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.0
	 */
	public function getItems()
	{
		$items = parent::getItems();

		return $items;
	}
}
