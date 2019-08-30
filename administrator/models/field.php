<?php
/**
 * @version    SVN: <svn_id>
 * @package    TJField
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2014-2018 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access.
defined('_JEXEC') or die;
jimport('joomla.application.component.modeladmin');

use Joomla\Registry\Registry;

/**
 * Tjfields model.
 *
 * @since  2.5
 */
class TjfieldsModelField extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_TJFIELDS';

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   String  $type    Type
	 * @param   String  $prefix  Prefix
	 * @param   Array   $config  Config
	 *
	 * @return	JTable	A database object
	 *
	 * @since  1.6
	 */
	public function getTable($type = 'Field', $prefix = 'TjfieldsTable', $config = array())
	{
		JLoader::import('components.com_tjfields.tables.field', JPATH_ADMINISTRATOR);

		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   Array    $data      An optional array of data for the form to interogate.
	 * @param   Boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm|boolean  A JForm object on success, false on failure
	 *
	 * @since  1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		JForm::addFormPath(JPATH_ADMINISTRATOR . '/components/com_tjfields/models/forms');
		JForm::addFormPath(JPATH_ADMINISTRATOR . '/components/com_tjfields/models/forms/types/forms');
		$form = $this->loadForm('com_tjfields.field', 'field', array('control' => 'jform','load_data' => $loadData));

		// Load field params in the field form
		if (!empty($data['type']))
		{
			$path = JPATH_SITE . '/administrator/components/com_tjfields/models/forms/types/forms/' . $data['type'] . '.xml';

			// If category XML esists then add global fields XML in current JForm object else create new object of Global Fields
			if (!empty($form) && JFile::exists($path))
			{
				$form->loadFile($path, true, '/form/*');
			}
		}

		if (!empty($form))
		{
			// Dont allow to save radio/single/multi selects without any options
			if (isset($data['type']))
			{
				if ($data['type'] == 'radio' || $data['type'] == 'single_select' || $data['type'] == 'multi_select' || $data['type'] == 'tjlist')
				{
					$form->setFieldAttribute('fieldoption', 'required', true);
				}
			}

			if ($form->getValue('type') == 'ucmsubform')
			{
				$form->setValue('showonlist', null, 0);
				$form->removeField('showonlist');
			}
		}

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  \JForm|boolean  \JForm object on success, false on error.
	 *
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		$app = JFactory::getApplication();
		$input = $app->input;

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_tjfields.edit.field.data', array());
		$id = $input->get('id', '0', 'INT');

		if (!empty($id))
		{
			$data = $this->getItem();
		}

		$this->preprocessData('com_tjfields.field', $data);

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   Integer  $pk  PK
	 *
	 * @return  mixed  Object on success, false on failure.
	 *
	 * @since  1.6
	 */
	public function getItem($pk = null)
	{
		$input = JFactory::getApplication()->input;

		if ($item = parent::getItem($pk))
		{
			// Do any procesing on fields here if needed
			if ($input->get('id', '', 'INT'))
			{
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select('opt.id as optionid,opt.options as name,opt.value FROM #__tjfields_options as opt');
				$query->where('opt.field_id=' . $input->get('id', '', 'INT'));
				$query->order('opt.ordering ASC');
				$db->setQuery($query);
				$option_name = $db->loadObjectlist();

				$item->fieldoption = $option_name;
			}
		}

		return $item;
	}

	/**
	 * Method Prepare and sanitise the table prior to saving.
	 *
	 * @param   Array  $table  table
	 *
	 * @return  void
	 *
	 * @since  1.6
	 */
	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');

		if (empty($table->id))
		{
			// Set ordering to the last item if not set
			if (@$table->ordering === '')
			{
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__tjfields_fields');
				$max = $db->loadResult();
				$table->ordering = $max + 1;
			}
		}
	}

	/**
	 * Method Save Option
	 *
	 * @param   Array  $data  Post
	 *
	 * @return  int|boolean A int on success, false on failuer
	 *
	 * @since  1.6
	 */
	public function save($data)
	{
		$app = JFactory::getApplication();
		$fields_in_DB = array();
		$options_filled = array();
		$table = $this->getTable();
		$form = $this->getForm($data);
		JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_tjfields/tables');

		// Sanitize the field data
		foreach ($data as $k => $validatedFieldData)
		{
			if (!is_array($validatedFieldData))
			{
				$data[$k] = htmlspecialchars($validatedFieldData, ENT_COMPAT, 'UTF-8');
			}
		}

		// Get the validation messages.
		$errors = $this->getErrors();

		// Check for errors.
		if (!empty($errors))
		{
			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_tjfields.edit.field.data', $data);

			// Tweak *important
			$app->setUserState('com_tjfields.edit.field.id', $data['id']);

			return false;
		}

		$input = JFactory::getApplication()->input;
		$data['label'] = trim($data['label']);

		// Set field title as field label
		if (!empty($data['label']))
		{
			$data['title'] = $data['label'];
		}

		if ($input->get('task') == 'save2copy')
		{
			unset($data['id']);
			$data['label'] = trim($data['label']);
			$name = explode("(", $data['label']);
			$name = trim($name['0']);
			$name = str_replace("`", "", $name);
			$db = JFactory::getDBO();

			// Create a new query object.
			$query = $db->getQuery(true);

			$query->select($db->quoteName('a.*'));
			$query->from($db->quoteName('#__tjfields_fields', 'a'));
			$query->where($db->quoteName('a.label') . ' LIKE ' . $db->quote($name . '%'));
			$query->where($db->quoteName('a.client') . ' LIKE ' . $db->quote($data['client'] . '%'));
			$query->where($db->quoteName('a.group_id') . ' = ' . $db->quote($data['group_id']));

			// Reset the query using our newly populated query object.
			$db->setQuery($query);
			$posts = $db->loadAssocList();
			$postsCount = count($posts) + 1;
			$data['label'] = $name . ' (' . $postsCount . ")";
			$data['created_by'] = JFactory::getUser()->id;
		}

		// Add clint type in data as it is not present in jform
		$input = $app->input;
		$data['client_type'] = $input->post->get('client_type', '', 'STRING');
		$data['saveOption'] = 0;

		if ($data['type'] == "radio" || $data['type'] == "single_select" || $data['type'] == "multi_select" || $data['type'] == "tjlist")
		{
			$data['saveOption'] = 1;
		}

		// Change the name only if the field is newly created....don't do on edit fields
		if ($data['id'] == 0)
		{
			// Escape apostraphe
			$data_name = trim(preg_replace('/[^A-Za-z0-9\-\']/', '', $data['name']));
			$client = explode('.', $data['client']);
			$client = $client[0];
			$data_unique_name = $client . '_' . $data['client_type'] . '_' . $data_name;

			// To store cluster type fields in core UCM hence rename the field name
			if ($data['type'] == 'cluster')
			{
				$data_unique_name = $client . '_' . $data['client_type'] . '_' . $data['type'] . 'clusterid';
			}

			// To store ownership type fields in core UCM hence rename the field name
			if ($data['type'] == 'ownership')
			{
				$data_unique_name = $client . '_' . $data['client_type'] . '_' . $data['type'] . 'createdby';
			}

			$data['name'] = $data_unique_name;
		}

		// Remove extra value which are not needed to save in the fields table
		$TjfieldsHelper = new TjfieldsHelper;
		$data['params']['accept'] = preg_replace('/\s+/', '', $data['params']['accept']);
		$data['params'] = json_encode($data['params']);

		if ($table->save($data) === true)
		{
			$id = $table->id;
		}

		// Check if name feild is unique
		$is_unique = $TjfieldsHelper->checkIfUniqueName($data['name']);

		if ($is_unique > 1)
		{
			// Append id to the name
			$change_name_if_same = $TjfieldsHelper->changeNameIfNotUnique($data['name'], $id);
		}

		// Save javascript functions.
		$js = $input->post->get('tjfieldsJs', '', 'ARRAY');

		if (!empty($js))
		{
			$jsfunctionSave = $this->jsfunctionSave($js, $id);
		}

		// If the field is inserted.
		if ($id)
		{
			$options = $data['fieldoption'];

			if ($data['saveOption'] == 1)
			{
				// Firstly Delete Fields Options That are Removed
				$field_options = $TjfieldsHelper->getOptions($id);

				foreach ($field_options as $fokey => $fovalue)
				{
					if ($fovalue->id)
					{
						$fields_in_DB[] = $fovalue->id;
					}
				}

				// Check for empty options
				if (count($options) == 0)
				{
					JFactory::getApplication()->enqueueMessage(JText::_('COM_TJFIELDS_INVALID_OPTION_VALUES'), 'error');

					return $id;
				}

				foreach ($options as $key => $value)
				{
					// Check for empty options
					if ((!empty($value['name']) && $value['value'] == '') || ($value['name'] == '' && !empty($value['value'])))
					{
						JFactory::getApplication()->enqueueMessage(JText::_('COM_TJFIELDS_INVALID_OPTION_VALUES'), 'error');

						return $id;
					}

					if ($value['optionid'])
					{
						$options_filled[] = $value['optionid'];
					}
				}

				if ($fields_in_DB)
				{
					$diff_ids = array_diff($fields_in_DB, $options_filled);

					if (!empty($diff_ids))
					{
						$this->delete_option($diff_ids);
					}
				}

				if (empty($options))
				{
					$this->delete_option($fields_in_DB);
				}
				else
				{
					// Save option fields.
					$order = 1;

					foreach ($options as $option)
					{
						$obj = new stdClass;
						$obj->options = htmlspecialchars($option['name'], ENT_COMPAT, 'UTF-8');
						$obj->value = htmlspecialchars($option['value'], ENT_COMPAT, 'UTF-8');
						$obj->field_id = $id;
						$obj->ordering = $order;
						$order++;

						// If edit options
						if (isset($option['optionid']) && !empty($option['optionid']))
						{
							if ($option['name'] != '' && $option['value'] != '')
							{
								$obj->id = $option['optionid'];

								if (!$this->_db->updateObject('#__tjfields_options', $obj, 'id'))
								{
									echo $this->_db->stderr();

									return $id;
								}
							}
						}
						else
						{
							if ($option['name'] != '' && $option['value'] != '')
							{
								$obj->id = '';

								if (!$this->_db->insertObject('#__tjfields_options', $obj, 'id'))
								{
									echo $this->_db->stderr();

									return $id;
								}
							}
						}
					}
				}
			}

			// Save/update field and category mapping
			$selectedCategories = !empty($data['category']) ? $data['category'] : array();

			// 1 Fetch cat mapping for field from DB
			$DBcat_maping = $TjfieldsHelper->getFieldCategoryMapping($id);

			if ($selectedCategories)
			{
				$newlyAddedCats = array_diff($selectedCategories, $DBcat_maping);
				$deletedCats = array_diff($DBcat_maping, $selectedCategories);

				if (!empty($deletedCats))
				{
					$arrayId = array($id);
					$this->deleteFieldCategoriesMapping($arrayId, $deletedCats);
				}

				if (!empty($newlyAddedCats))
				{
					// Add newly added  category mapping
					foreach ($newlyAddedCats as $cat)
					{
						$obj = new stdClass;
						$obj->field_id = $id;
						$obj->category_id = $cat;

						if (!$this->_db->insertObject('#__tjfields_category_mapping', $obj, 'id'))
						{
							echo $this->_db->stderr();

							return $id;
						}
					}
				}
			}
			else
			{
				$arrayId = array($id);
				$this->deleteFieldCategoriesMapping($arrayId, array());
			}

			// Create XML for the current client.
			$TjfieldsHelper->generateXml($data);

			return $id;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method Save JS FUnction
	 *
	 * @param   Array    $jsarray  JSArray
	 * @param   Integer  $fieldid  Field Id
	 *
	 * @return  boolean A false if failure
	 *
	 * @since  1.6
	 */
	public function jsfunctionSave($jsarray, $fieldid)
	{
		$obj = new stdClass;
		$obj->js_function = '';

		foreach ($jsarray as $js)
		{
			if ($js['jsoptions'] != '' && $js['jsfunctionname'] != '')
			{
				$obj->js_function .= $js['jsoptions'] . '-' . $js['jsfunctionname'] . '||';
			}

			$obj->id = $fieldid;
		}

		if (!empty($obj->js_function))
		{
			if (!$this->_db->updateObject('#__tjfields_fields', $obj, 'id'))
			{
				echo $this->_db->stderr();

				return false;
			}
		}
	}

	/**
	 * Method To Delete Option
	 *
	 * @param   array  $delete_ids  Id for delete record
	 *
	 * @return  boolean A false on failure
	 *
	 * @since  1.6
	 */
	public function delete_option($delete_ids)
	{
		$db = JFactory::getDBO();

		foreach ($delete_ids as $key => $value)
		{
			$query = 'DELETE FROM #__tjfields_options WHERE id = "' . $value . '"';
			$db->setQuery($query);

			if (!$db->execute())
			{
				echo $db->stderr();

				return false;
			}
		}
	}

	/**
	 * Method for Delete Field Categories Mapping
	 *
	 * @param   array  $field_id  Id
	 * @param   array  $cats      Category
	 *
	 * @return  Boolean
	 *
	 * @since	1.6
	 */
	public function deleteFieldCategoriesMapping($field_id = array(), $cats = array())
	{
		$db = JFactory::getDBO();

		try
		{
			$query = $db->getQuery(true);

			if (!empty($field_id))
			{
				$conditions = array($db->quoteName('field_id') . ' IN (' . implode(",", $field_id) . ")");

				if (!empty($cats))
				{
					$conditions[] = $db->quoteName('category_id') . ' IN (' . implode(",", $cats) . ")";
				}

				$query->delete($db->quoteName('#__tjfields_category_mapping'));
				$query->where($conditions);
				$db->setQuery($query);

				$db->execute();
			}
		}
		catch (RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}
	}

	/**
	 * Method to inject field attributes in jform object
	 *
	 * @param   Integer  $form   form
	 * @param   String   $data   form
	 * @param   String   $group  group
	 *
	 * @return  Boolean
	 *
	 * @since	1.6
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'content')
	{
		$dataObject = $data;

		if (is_array($dataObject))
		{
			$dataObject = (object) $dataObject;
		}

		if (empty($dataObject->type))
		{
			$dataObject->type = 'text';
		}

		if (isset($dataObject->type))
		{
			$path = JPATH_ADMINISTRATOR . '/components/com_tjfields/models/forms/types/forms/' . $dataObject->type . '.xml';
			$form->loadFile($path, true, '/form/*');
		}

		// Trigger the default form events.
		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * Method to get related field options
	 *
	 * @param   Integer  $fieldId  Field Id
	 *
	 * @return  Array
	 *
	 * @since	1.6
	 */
	public function getRelatedFieldOptions($fieldId)
	{
		if (empty($fieldId))
		{
			return false;
		}

		$db = JFactory::getDbo();
		$jInput = JFactory::getApplication()->input;
		JLoader::import('components.com_tjfields.tables.field', JPATH_ADMINISTRATOR);
		$fieldTable = JTable::getInstance('Field', 'TjfieldsTable', array('dbo', $db));
		$fieldTable->load(array('id' => $fieldId));

		// Get decoded data object
		$fieldParams = new Registry($fieldTable->params);

		// UCM fields and fields from which options are to be generated
		$realtedFields  = $fieldParams->get('fieldName');

		$options = array();

		if (empty($fieldParams->get('multiple')))
		{
			$options = array(array('value' => '', "text" => JText::_("JGLOBAL_SELECT_AN_OPTION")));
		}

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

			if ($fieldParams->get('clusterAware') == 1)
			{
				$clusterId = $jInput->get("cluster_id", 0, "INT");

				if (!empty($clusterId))
				{
					$query->where($db->quoteName('cluster_id') . ' = ' . $clusterId);
				}
			}

			$parentContentId = $jInput->get('id', 0, 'INT');

			if ($fieldParams->get('showParentRecordsOnly') == 1)
			{
				if (!empty($parentContentId))
				{
					$query->where($db->quoteName('parent_id') . ' = ' . $parentContentId . ' OR ' . $db->quoteName('id') . ' = ' . $parentContentId);
				}
				else
				{
					return false;
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

				$options = array_merge($options, $result);
			}
		}

		foreach ($options as $k => $option)
		{
			$options[$k]['value'] = htmlspecialchars(trim($option['value']), ENT_COMPAT, 'UTF-8');
		}

		return $options;
	}

	/**
	 * Method to delete one or more fields.
	 *
	 * @param   array  &$pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 *
	 * @since   1.4.2
	 */
	public function delete(&$pks)
	{
		$db = JFactory::getDbo();
		$pks = (array) $pks;

		foreach ($pks as $pk)
		{
			if (empty($pk))
			{
				continue;
			}

			// Delete options of the field if any exists
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__tjfields_options'));
			$query->where($db->quoteName('field_id') . ' = ' . (int) $pk);
			$db->setQuery($query);
			$result = $db->execute();

			// Delete fields data from fields value table
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__tjfields_fields_value'));
			$query->where($db->quoteName('field_id') . ' = ' . (int) $pk);
			$db->setQuery($query);
			$result = $db->execute();

			if ($result)
			{
				// Delete field data
				$status = parent::delete($pk);

				if ($status === false)
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}

		return true;
	}
}
