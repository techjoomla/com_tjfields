<?php
/**
 * @package     TJ-Fields
 * @subpackage  Plg_Privacy_TjFields
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2018 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

JLoader::register('PrivacyPlugin', JPATH_ADMINISTRATOR . '/components/com_privacy/helpers/plugin.php');
JLoader::register('PrivacyRemovalStatus', JPATH_ADMINISTRATOR . '/components/com_privacy/helpers/removal/status.php');

use Joomla\CMS\User\User;

/**
 * TjFields Privacy Plugin.
 *
 * @since  __DEPLOY_VERSION__
 */
class PlgPrivacyTjFields extends PrivacyPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected $autoloadLanguage = true;

	/**
	 * Database object
	 *
	 * @var    JDatabaseDriver
	 * @since  __DEPLOY_VERSION__
	 */
	protected $db;

	/**
	 * Processes an export request for TjFields user data
	 *
	 * This event will collect data for the following tables:
	 *
	 * - #__tjfields_fields
	 * - #__tjfields_groups
	 * - #__tjfields_fields_value
	 *
	 * @param   PrivacyTableRequest  $request  The request record being processed
	 * @param   JUser                $user     The user account associated with this request if available
	 *
	 * @return  PrivacyExportDomain[]
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function onPrivacyExportRequest(PrivacyTableRequest $request, JUser $user = null)
	{
		if (!$user)
		{
			return array();
		}

		/** @var JTableUser $user */
		$userTable = User::getTable();
		$userTable->load($user->id);

		$domains = array();
		$domains[] = $this->createTjFieldsFields($userTable);
		$domains[] = $this->createTjFieldsFieldGroups($userTable);
		$domains[] = $this->createTjFieldsFieldValues($userTable);

		return $domains;
	}

	/**
	 * Create the domain for the TjFields fields
	 *
	 * @param   JTableUser  $user  The JTableUser object to process
	 *
	 * @return  PrivacyExportDomain
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	private function createTjFieldsFields(JTableUser $user)
	{
		$domain = $this->createDomain('Fields', 'Fields created by a user');

		$query = $this->db->getQuery(true)
			->select($this->db->quoteName(array('id', 'label', 'name', 'type', 'created_by', 'client', 'group_id')))
			->from($this->db->quoteName('#__tjfields_fields'))
			->where($this->db->quoteName('created_by') . '=' . $user->id);

		$fields = $this->db->setQuery($query)->loadAssocList();

		if (!empty($fields))
		{
			foreach ($fields as $field)
			{
				$domain->addItem($this->createItemFromArray($field, $field['id']));
			}
		}

		return $domain;
	}

	/**
	 * Create the domain for the TjFields groups
	 *
	 * @param   JTableUser  $user  The JTableUser object to process
	 *
	 * @return  PrivacyExportDomain
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	private function createTjFieldsFieldGroups(JTableUser $user)
	{
		$domain = $this->createDomain('Groups', 'Field groups created by a user');

		$query = $this->db->getQuery(true)
			->select($this->db->quoteName(array('id', 'state', 'created_by', 'name', 'client')))
			->from($this->db->quoteName('#__tjfields_groups'))
			->where($this->db->quoteName('created_by') . '=' . $user->id);

		$groups = $this->db->setQuery($query)->loadAssocList();

		if (!empty($groups))
		{
			foreach ($groups as $group)
			{
				$domain->addItem($this->createItemFromArray($group, $group['id']));
			}
		}

		return $domain;
	}

	/**
	 * Create the domain for the TjFields field values
	 *
	 * @param   JTableUser  $user  The JTableUser object to process
	 *
	 * @return  PrivacyExportDomain
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	private function createTjFieldsFieldValues(JTableUser $user)
	{
		$domain = $this->createDomain('Field values', 'Field values submitted by a user');

		$query = $this->db->getQuery(true)
			->select($this->db->qn(array('id', 'field_id', 'content_id', 'value', 'user_id', 'email_id', 'client', 'option_id')))
			->from($this->db->qn('#__tjfields_fields_value'))
			->where('(' . $this->db->qn('user_id') . '=' . $user->id . ' OR ' . $this->db->qn('email_id') . '=' . $this->db->quote($user->email) . ')');

		$fieldValues = $this->db->setQuery($query)->loadAssocList();

		if (!empty($fieldValues))
		{
			foreach ($fieldValues as $fieldValue)
			{
				$domain->addItem($this->createItemFromArray($fieldValue, $fieldValue['id']));
			}
		}

		return $domain;
	}
}
