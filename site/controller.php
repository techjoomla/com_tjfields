<?php
/**
 * @version    SVN: <svn_id>
 * @package    Tjfields
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2018 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Language\Text;

/**
 * TJField Controller class
 *
 * @package     Tjfields
 * @subpackage  com_tjfields
 * @since       2.2
 */
class TjfieldsController extends JControllerLegacy
{
	/**
	 * The return URL.
	 *
	 * @var    mixed
	 * @since  1.4
	 */
	protected $returnURL;

	/**
	 * Constructor
	 *
	 * @since 1.4
	 */
	public function __construct()
	{
		require_once JPATH_SITE . '/components/com_tjfields/helpers/tjfields.php';
		$this->returnURL = JURI::root();

		parent::__construct();
	}

	/**
	 * Fuction to get download media file
	 *
	 * @return object
	 */
	public function getMediaFile()
	{
		JLoader::import("/techjoomla/media/storage/local", JPATH_LIBRARIES);
		$app = Factory::getApplication();
		$jinput = $app->input;
		$mediaLocal = TJMediaStorageLocal::getInstance();

		// Here, fpht means file encoded name
		$encodedFileName = $jinput->get('fpht', '', 'STRING');
		$decodedFileName = base64_decode($encodedFileName);

		// Subform File field Id for checking autherization for specific field under subform
		$subformFileFieldId = $jinput->get('subFormFileFieldId', '', 'INT');

		// Get media storage path
		JLoader::import('components.com_tjfields.models.fields', JPATH_SITE);
		$fieldsModel     = BaseDatabaseModel::getInstance('Fields', 'TjfieldsModel', array('ignore_request' => true));
		$data = $fieldsModel->getMediaStoragePath($jinput->get('id', '', 'INT'), $subformFileFieldId);

		$extraFieldParams = json_decode($data->tjFieldFieldTable->params);
		$storagePath = $extraFieldParams->uploadpath;
		$decodedPath = $storagePath . '/' . $decodedFileName;

		if ($data->tjFieldFieldTable->fieldValueId)
		{
			$user = Factory::getUser();

			if ($subformFileFieldId)
			{
				$canView = $user->authorise('core.field.viewfieldvalue', 'com_tjfields.field.' . $subformFileFieldId);
			}
			else
			{
				$canView = $user->authorise('core.field.viewfieldvalue', 'com_tjfields.field.' . $data->tjFieldFieldTable->field_id);
			}

			$canDownload = 0;

			// Allow to view own data
			if ($data->tjFieldFieldTable->user_id != null && ($user->id == $data->tjFieldFieldTable->user_id))
			{
				$canDownload = true;
			}

			if ($canView || $canDownload)
			{
				$down_status = $mediaLocal->downloadMedia($decodedPath, '', '', 0);

				if ($down_status === 2)
				{
					$app->enqueueMessage(Text::_('COM_TJFIELDS_FILE_NOT_FOUND'), 'error');
					$app->redirect($this->returnURL);
				}

				return;
			}
			else
			{
				$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
				$app->redirect($this->returnURL);
			}
		}
		else
		{
			$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
			$app->redirect($this->returnURL);
		}

		jexit();
	}

	/**
	 * Fuction to get download media file
	 *
	 * @return object
	 */
	public function getMedia()
	{
		JLoader::import("/techjoomla/media/storage/local", JPATH_LIBRARIES);
		$app = Factory::getApplication();
		$jinput = $app->input;
		$mediaLocal = TJMediaStorageLocal::getInstance();

		$encodedFileName = $jinput->get('fpht', '', 'STRING');
		$decodedFileName = base64_decode($encodedFileName);

		$db = Factory::getDbo();

		// Load TJ-Fields table
		Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_tjfields/tables');

		// Get field value record
		$fieldValueTable = Table::getInstance('Fieldsvalue', 'TjfieldsTable', array('dbo', $db));
		$fieldValueTable->load(array('value' => $decodedFileName));

		// Get field record
		$fieldTable = Table::getInstance('Field', 'TjfieldsTable', array('dbo', $db));
		$fieldTable->load(array('id' => $fieldValueTable->field_id));

		$fieldType = $fieldTable->type;
		$decodedPath = JPATH_SITE . '/' . $fieldType . 's/tjmedia/' . str_replace(".", "/", $fieldValueTable->client) . '/' . $decodedFileName;

		$status = $mediaLocal->downloadMedia($decodedPath, '', '', 0);

		if ($status === 2)
		{
			$app->enqueueMessage(Text::_('COM_TJFIELDS_FILE_NOT_FOUND'), 'error');
			$app->redirect($this->returnURL);
		}

		jexit();
	}
}
