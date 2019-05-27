<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

/**
 * Form Field class for the Joomla Platform.
 * Supports a generic list of options.
 *
 * @since  1.7.0
 */
class JFormFieldRelated extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.7.0
	 */
	protected $type = 'Related';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   3.7.0
	 */
	protected function getOptions()
	{
		// Load TJ-Fields language file
		$lang = Factory::getLanguage()->load('com_tjfields', JPATH_ADMINISTRATOR);

		$fieldname = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname);

		$options = array();

		if (!$this->multiple)
		{
			$options = array(array('value' => '', "text" => Text::_("JGLOBAL_SELECT_AN_OPTION")));
		}

		$db = Factory::getDbo();
		Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_tjfields/tables');
		$fieldTable = Table::getInstance('field', 'TjfieldsTable', array('dbo', $db));
		$fieldTable->load(array('name' => $fieldname));

		// UCM fields and fields from which options are to be generated
		$fieldParams = json_decode($fieldTable->params);
		$realtedFields = $fieldParams->fieldName;

		foreach ($realtedFields as $realtedField)
		{
			if (empty($realtedField->client) || empty($realtedField->fieldIds))
			{
				continue;
			}

			// Get all of the submitted records ID for given UCM Type
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__tj_ucm_data'));
			$query->where($db->quoteName('client') . ' = ' . $db->quote($realtedField->client));

			if ($fieldParams->clusterAware == 1)
			{
				$jInput = Factory::getApplication()->input;
				$clusterId = $jInput->get("cluster_id", 0, "INT");

				if (!empty($clusterId))
				{
					$query->where($db->quoteName('cluster_id') . ' = ' . $clusterId);
				}
			}

			$db->setQuery($query);
			$result = $db->loadColumn();

			if (!empty($result))
			{
				$ucmRecordIds = implode(",", $result);
				$fieldIds = implode(",", $realtedField->fieldIds);

				// Get field values for the fields configured in related fields for the given UCM Type
				$query = $db->getQuery(true);
				$query->select($db->quoteName('ud.id', 'value'));
				$query->select("GROUP_CONCAT(" . $db->quoteName('fv.value') . " SEPARATOR ' ') AS text");
				$query->from($db->quoteName('#__tj_ucm_data', 'ud'));
				$query->join('INNER', $db->qn('#__tjfields_fields_value', 'fv') . ' ON (' . $db->qn('ud.id') . ' = ' . $db->qn('fv.content_id') . ')');
				$query->where($db->quoteName('field_id') . ' IN( ' . $fieldIds . ')');
				$query->where($db->quoteName('content_id') . ' IN( ' . $ucmRecordIds . ')');
				$query->group($db->quoteName('content_id'));

				$db->setQuery($query);

				// Load the results as a list of stdClass objects (see later for more options on retrieving data).
				$result = $db->loadAssocList();
			}

			$options = array_merge($options, $result);
		}

		foreach ($options as $k => $option)
		{
			$options[$k]['value'] = htmlspecialchars(trim($option['value']), ENT_COMPAT, 'UTF-8');
		}

		return $options;
	}

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   3.7.0
	 */
	protected function getInput()
	{
		$html = parent::getInput();

		$fieldname = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname);

		$user = Factory::getUser();
		$db = Factory::getDbo();
		Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_tjfields/tables');
		$fieldTable = Table::getInstance('field', 'TjfieldsTable', array('dbo', $db));
		$fieldTable->load(array('name' => $fieldname));

		// UCM fields and fields from which options are to be generated
		$fieldParams = json_decode($fieldTable->params);
		$realtedFields = (array) $fieldParams->fieldName;

		if (count($realtedFields) == 1)
		{
			Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_tjucm/tables');
			$ucmTypeTable = Table::getInstance('Type', 'TjucmTable', array('dbo', $db));
			$ucmTypeTable->load(array('unique_identifier' => $realtedFields['fieldName0']->client));

			// Check if user is authorised to add the record in given UCM Type
			$canCreate = $user->authorise('core.type.createitem', 'com_tjucm.type.' . $ucmTypeTable->id);

			if ($canCreate)
			{
				foreach ($realtedFields['fieldName0']->fieldIds as $fieldId)
				{
					$canCreate = ($user->authorise('core.field.addfieldvalue', 'com_tjfields.field.' . $fieldId)) ? true : false;

					if (empty($canCreate))
					{
						break;
					}
				}
			}

			$showAddNewRecordLink = $fieldParams->showAddNewRecordLink;

			if ($canCreate && !empty($showAddNewRecordLink))
			{
				$masterUcmLink = Route::_('index.php?option=com_tjucm&view=itemform&client=' . $ucmTypeTable->unique_identifier, false);
				$html .= "<div><a target='_blank' href='" . $masterUcmLink . "'>" . Text::_("COM_TJFIELDS_FORM_DESC_FIELD_RELATED_ADD_RECORD") . "</a></div>";
			}
		}

		return $html;
	}
}
