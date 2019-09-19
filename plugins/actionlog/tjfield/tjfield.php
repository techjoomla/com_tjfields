<?php
/**
 * @package     Tjfield
 * @subpackage  PlgActionlogTjfield
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (c) 2009-2019 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

// No direct access.
defined('_JEXEC') or die();

JLoader::register('ActionlogsHelper', JPATH_ADMINISTRATOR . '/components/com_actionlogs/helpers/actionlogs.php');

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Table\Table;

/**
 * JGive Actions Logging Plugin.
 *
 * @since  __DEPLOY_VERSION__
 */
class PlgActionlogTjfield extends CMSPlugin
{
	/**
	 * Application object.
	 *
	 * @var    JApplicationCms
	 * @since  __DEPLOY_VERSION__
	 */
	protected $app;

	/**
	 * Database object.
	 *
	 * @var    JDatabaseDriver
	 * @since  __DEPLOY_VERSION__
	 */
	protected $db;

	/**
	 * Load plugin language file automatically so that it can be used inside component
	 *
	 * @var    boolean
	 * @since  __DEPLOY_VERSION__
	 */
	protected $autoloadLanguage = true;

	/**
	 * Proxy for ActionlogsModelUserlog addLog method
	 *
	 * This method adds a record to #__action_logs contains (message_language_key, message, date, context, user)
	 *
	 * @param   array   $messages            The contents of the messages to be logged
	 * @param   string  $messageLanguageKey  The language key of the message
	 * @param   string  $context             The context of the content passed to the plugin
	 * @param   int     $userId              ID of user perform the action, usually ID of current logged in user
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function addLog($messages, $messageLanguageKey, $context, $userId = null)
	{
		JLoader::register('ActionlogsModelActionlog', JPATH_ADMINISTRATOR . '/components/com_actionlogs/models/actionlog.php');

		/* @var ActionlogsModelActionlog $model */
		$model = BaseDatabaseModel::getInstance('Actionlog', 'ActionlogsModel');
		$model->addLog($messages, $messageLanguageKey, $context, $userId);
	}

	/**
	 * On saving/updateting field group data logging method
	 *
	 * Method is called after field group data is stored in the database.
	 * This method logs who created/edited any field group data
	 *
	 * @param   Array    $fieldGroup  Holds the Field Group data
	 * @param   Int      $typeId      Id of ucm type.
	 * @param   Boolean  $isNew       True if a new report is stored.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function tjfieldOnAfterFieldGroupSave($fieldGroup, $typeId, $isNew)
	{
		if ($isNew)
		{
			if (!$this->params->get('logActionForFieldGroupSave', 1))
			{
				return;
			}
		}
		else
		{
			if (!$this->params->get('logActionForFieldGroupUpdate', 1))
			{
				return;
			}
		}

		$context = JFactory::getApplication()->input->get('option');

		$user = JFactory::getUser();

		if ($isNew)
		{
			$messageLanguageKey = 'PLG_ACTIONLOG_TJFIELD_FIELD_GROUP_CREATED';
			$action             = 'add';
		}
		else
		{
			$messageLanguageKey = 'PLG_ACTIONLOG_TJFIELD_FIELD_GROUP_UPDATED';
			$action             = 'update';
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('title');
		$query->from($db->quoteName('#__tj_ucm_types'));
		$query->where($db->quoteName('id') . " = " . $db->quote($typeId));
		$db->setQuery($query);
		$typeTitle = $db->loadResult();

		$message = array(
			'action'        => $action,
			'id'            => $fieldGroup['fieldGroupId'],
			'title'         => $fieldGroup['title'],
			'typeTitle'     => $typeTitle,
			'typeLink' => 'index.php?option=com_tjucm&view=type&layout=edit&id=' . $typeId,
			'itemlink'      => 'index.php?option=com_tjfields&&view=group&layout=edit&id=' . $fieldGroup['fieldGroupId'] . '&client=' . $fieldGroup['client'],
			'userid'        => $user->id,
			'username'      => $user->username,
			'accountlink'   => 'index.php?option=com_users&task=user.edit&id=' . $user->id,
		);

		$this->addLog(array($message), $messageLanguageKey, $context, $user->id);
	}

	/**
	 * On saving field group data logging method
	 *
	 * Method is called after field group data is stored in the database.
	 * This method logs who created/edited any field group data
	 *
	 * @param   Int      $pk     Holds the Field Group data
	 * @param   Boolean  $value  True if a new report is stored.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function tjfieldOnAfterFieldGroupChangeState($pk, $value)
	{
		if (!$this->params->get('logActionForFieldGroupStateChange', 1))
		{
			return;
		}

		$context = JFactory::getApplication()->input->get('option');

		$user = JFactory::getUser();
		$ucmType = $this->getucmType($pk);

		switch ($value)
		{
			case 0:
				$messageLanguageKey = 'PLG_ACTIONLOG_TJFIELD_FIELD_GROUP_UNPUBLISHED';
				$action             = 'unpublish';
				break;
			case 1:
				$messageLanguageKey = 'PLG_ACTIONLOG_TJFIELD_FIELD_GROUP_PUBLISHED';
				$action             = 'publish';
				break;
			case -2:
				$messageLanguageKey = 'PLG_ACTIONLOG_TJFIELD_FIELD_GROUP_TRASHED';
				$action             = 'trash';
				break;
			default:
				$messageLanguageKey = '';
				$action             = '';
				break;
		}

		$tjfieldsTablegroup = Table::getInstance('group', 'TjfieldsTable', array());
		$tjfieldsTablegroup->load(array('id' => $pk));

		if ($tjfieldsTablegroup != null)
		{
			$message = array(
					'action'        => $action,
					'id'            => $tjfieldsTablegroup->id,
					'title'         => $tjfieldsTablegroup->title,
					'itemlink'      => 'index.php?option=com_tjfields&&view=group&layout=edit&id=' . $tjfieldsTablegroup->id . '&client=' . $tjfieldsTablegroup->client,
					'typeTitle'     => $ucmType['title'],
					'typeLink' => 'index.php?option=com_tjucm&view=type&layout=edit&id=' . $ucmType['id'],
					'userid'        => $user->id,
					'username'      => $user->username,
					'accountlink'   => 'index.php?option=com_users&task=user.edit&id=' . $user->id,
			);

			$this->addLog(array($message), $messageLanguageKey, $context, $userId);
		}
	}

	/**
	 * Method is called after field group is to be deleted.
	 * This method logs who deleted any field group data
	 *
	 * @param   Int  $pk  Holds the Field Group data
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function tjfieldOnAfterFieldGroupDelete($pk)
	{
		$ucmType = array();
		$tjfieldsTablegroup = Table::getInstance('group', 'TjfieldsTable', array());
		$tjfieldsTablegroup->load(array('id' => $pk));

		if ($pk != null)
		{
			$ucmType = $this->getucmType($pk);
		}

		$context = JFactory::getApplication()->input->get('option');

		$user = JFactory::getUser();
		$messageLanguageKey = 'PLG_ACTIONLOG_TJFIELD_FIELD_DELETED';

		$message = array(
				'action'        => 'delete',
				'title'         => $tjfieldsTablegroup->title,
				'typeTitle'     => $ucmType['title'],
				'typeLink' => 'index.php?option=com_tjucm&view=type&layout=edit&id=' . $ucmType['id'],
				'userid'        => $user->id,
				'username'      => $user->username,
				'accountlink'   => 'index.php?option=com_users&task=user.edit&id=' . $user->id,
		);

		$this->addLog(array($message), $messageLanguageKey, $context, $user->id);
	}

	/**
	 * On saving field data logging method
	 *
	 * Method is called after field data is stored in the database.
	 * This method logs who created/edited any field group data
	 *
	 * @param   Array    $field         Holds the Field data
	 * @param   Array    $fieldGroupID  Holds the Field data
	 * @param   Array    $typeID        Holds the Field data
	 * @param   Boolean  $isNew         True if a new report is stored.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function tjfieldOnAfterFieldSave($field, $fieldGroupID, $typeID, $isNew)
	{
		if (!$this->params->get('logActionForFieldSave', 1))
		{
			return;
		}

		$context = JFactory::getApplication()->input->get('option');

		$user = JFactory::getUser();
		$tjucmTableType = Table::getInstance('type', 'TjucmTable', array());
		$tjucmTableType->load(array('id' => $typeID));

		if ($isNew)
		{
			$messageLanguageKey = 'PLG_ACTIONLOG_TJFIELD_FIELD_CREATED';
			$action             = 'add';
		}
		else
		{
			$messageLanguageKey = 'PLG_ACTIONLOG_TJFIELD_FIELD_UPDATED';
			$action             = 'update';
		}

		// User X has deleted field PQR under type ABC
		$message = array(
			'action'      => $action,
			'id'          => $field['id'],
			'title'       => $field['title'],
			'type'        => $tjucmTableType->title,
			'typelink'    => 'index.php?option=com_tjucm&view=type&layout=edit&id=' . $typeID,
			'itemlink'    => 'index.php?option=com_tjfields&view=field&layout=edit&id=' . $field['id'],
			'userid'      => $user->id,
			'username'    => $user->username,
			'accountlink' => 'index.php?option=com_users&task=user.edit&id=' . $user->id,
		);

		$this->addLog(array($message), $messageLanguageKey, $context, $user->id);
	}

	/**
	 * Method is called after field is to be deleted.
	 * This method logs who deleted any field data
	 *
	 * @param   Int  $id  Holds the Field id
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function tjfieldOnAfterFieldDelete($id)
	{
		$ucmType = array();
		$tjfieldsTable = Table::getInstance('field', 'TjfieldsTable', array());
		$tjfieldsTable->load(array('id' => $id));

		if ($tjfieldsTable != null)
		{
			$client = $tjfieldsTable->client;
		}

		if ($client != null)
		{
			$ucmType = $this->getUcmTypeByClient($client);
		}

		$context = JFactory::getApplication()->input->get('option');

		$user = JFactory::getUser();
		$messageLanguageKey = 'PLG_ACTIONLOG_TJFIELD_FIELD_DELETED';

		$message = array(
				'action'        => 'delete',
				'title'         => $tjfieldsTable->title,
				'typeTitle'     => $ucmType['title'],
				'typeLink'      => 'index.php?option=com_tjucm&view=type&layout=edit&id=' . $ucmType['id'],
				'userid'        => $user->id,
				'username'      => $user->username,
				'accountlink'   => 'index.php?option=com_users&task=user.edit&id=' . $user->id,
		);

		$this->addLog(array($message), $messageLanguageKey, $context, $user->id);
	}

	/**
	 * Method is called after field data is stored in the database.
	 * This method logs who published/unpublished any field data
	 *
	 * @param   Int      $pk     Holds the Field data
	 * @param   Boolean  $value  True if a new field is stored.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function tjfieldOnAfterFieldChangeState($pk, $value)
	{
		$context = JFactory::getApplication()->input->get('option');

		$user = JFactory::getUser();
		$ucmType = $this->getucmType($pk);

		switch ($value)
		{
			case 0:
				$messageLanguageKey = 'PLG_ACTIONLOG_TJFIELD_FIELD_UNPUBLISHED';
				$action             = 'unpublish';
				break;
			case 1:
				$messageLanguageKey = 'PLG_ACTIONLOG_TJFIELD_FIELD_PUBLISHED';
				$action             = 'publish';
				break;
			case -2:
				$messageLanguageKey = 'PLG_ACTIONLOG_TJFIELD_FIELD_TRASHED';
				$action             = 'trash';
				break;
			default:
				$messageLanguageKey = '';
				$action             = '';
				break;
		}

		$tjfieldsTable = Table::getInstance('field', 'TjfieldsTable', array());
		$tjfieldsTable->load(array('id' => $pk));

		if ($tjfieldsTable != null)
		{
			$client = $tjfieldsTable->client;
		}

		if ($client != null)
		{
			$ucmType = $this->getUcmTypeByClient($client);
		}

		$message = array(
				'action'        => $action,
				'id'            => $tjfieldsTable->id,
				'title'         => $tjfieldsTable->title,
				'itemlink'      => 'index.php?option=com_tjfields&&view=field&layout=edit&id=' . $tjfieldsTable->id . '&client=' . $tjfieldsTable->client,
				'typeTitle'     => $ucmType['title'],
				'typeLink'      => 'index.php?option=com_tjucm&view=type&layout=edit&id=' . $ucmType['id'],
				'userid'        => $user->id,
				'username'      => $user->username,
				'accountlink'   => 'index.php?option=com_users&task=user.edit&id=' . $user->id,
		);

		$this->addLog(array($message), $messageLanguageKey, $context, $user->id);
	}

	/**
	 * Get ucmType details on the basis on fieldgroup id
	 *
	 * @param   Int  $id  fieldgroup id.
	 *
	 * @return  array
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getucmType($id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('client');
		$query->from($db->quoteName('#__tjfields_groups'));
		$query->where($db->quoteName('id') . " = " . $db->quote($id));
		$db->setQuery($query);
		$client = $db->loadResult();

		if ($client != null)
		{
		$type = $this->getUcmTypeByClient($client);

		return $type;
		}
	}

	/**
	 * Get ucmType details on the basis on client
	 *
	 * @param   String  $client  fieldgroup id.
	 *
	 * @return  array
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getUcmTypeByClient($client)
	{
		if ($client != null)
		{
		$db2 = JFactory::getDbo();
		$query2 = $db2->getQuery(true);
		$query2->select(array('id','title'));
		$query2->from($db2->quoteName('#__tj_ucm_types'));
		$query2->where($db2->quoteName('unique_identifier') . " = " . $db2->quote($client));
		$db2->setQuery($query2);
		$type = $db2->loadAssoc();

		return $type;
		}
	}
}
