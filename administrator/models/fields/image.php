<?php
/**
 * @version    SVN:<SVN_ID>
 * @package    TJFields
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2018 TechJoomla. All rights reserved
 * @license    GNU General Public License version 2, or later
 */

defined('JPATH_PLATFORM') or die;
JLoader::import('components.com_tjfields.models.fields.file', JPATH_ADMINISTRATOR);

/**
 * Form Field Image class
 * Supports a multi line area for entry of plain text with count char
 *
 * @since  __DEPLOY_VERSION__
 */
class JFormFieldImage extends JFormFieldFile
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $type = 'image';

	/**
	 * The accepted file type list.
	 *
	 * @var    mixed
	 * @since  __DEPLOY_VERSION__
	 */
	protected $accept;

	/**
	 * The SimpleXMLElement object representing the `<field>` tag for the form field object.
	 *
	 * @var    mixed
	 * @since  __DEPLOY_VERSION__
	 */
	protected $element;

	/**
	 * Name of the layout being used to render the field
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $layout = 'joomla.form.field.file';

	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 *
	 * @param   string  $name  The property name for which to the the value.
	 *
	 * @return  mixed  The property value or null.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __get($name)
	{
		require_once JPATH_SITE . '/components/com_tjfields/helpers/tjfields.php';

		switch ($name)
		{
			case 'accept':

				return $this->accept;
				break;

			case 'element';

				return $this->element;
				break;
		}

		return parent::__get($name);
	}

	/**
	 *Method to set certain otherwise inaccessible properties of the form field object.
	 *
	 * @param   string  $name   The property name for which to the the value.
	 * @param   mixed   $value  The value of the property.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __set($name, $value)
	{
		switch ($name)
		{
			case 'accept':
				$this->accept = (string) $value;
				break;

			case 'multiple':
				$this->multiple = (string) $value;
				break;

			default:
				parent::__set($name, $value);
		}
	}

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the `<field>` tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     JFormField::setup()
	 * @since   __DEPLOY_VERSION__
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		if ($return)
		{
			$this->accept = (string) $this->element['accept'];
			$this->multiple = (string) $this->element['multiple'];
		}

		return $return;
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getInput()
	{
		$layoutData = $this->getLayoutData();
		$html = $this->getRenderer($this->layout)->render($layoutData);

		// Load backend language file
		$lang = JFactory::getLanguage();
		$lang->load('com_tjfields', JPATH_SITE);

		if (!empty($layoutData["value"]))
		{
			$data = parent::buildData($layoutData);
			$html .= $data->html;

			if (!empty($data->mediaLink))
			{
				$html .= $this->renderImage($data, $layoutData);
				$html .= $this->canDownloadFile($data, $layoutData);
				$html .= $this->canDeleteFile($data, $layoutData);
			}

				$html .= '</div>';
				$html .= '</div>';
		}

		return $html;
	}

	/**
	 * Method to get the data to be passed to the layout for rendering.
	 *
	 * @return  array
	 *
	 * @since __DEPLOY_VERSION__
	 */
	protected function getLayoutData()
	{
		$data = parent::getLayoutData();

		$extraData = array(
			'accept'   => $this->accept,
			'multiple' => $this->multiple,
		);

		return array_merge($data, $extraData);
	}

	/**
	 * Method to render image file.
	 *
	 * @param   object  $data        file data.
	 * @param   array   $layoutData  layoutData
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function renderImage($data, $layoutData)
	{
		$path = JUri::root() . 'images/tjmedia/';

		if (!empty($data->clientForm))
		{
			$path .= str_replace(".", "/", $data->clientForm . '/');
		}

		return '<img src="' . $path . $layoutData['value'] . '" height=
		"' . $layoutData['field']->element->attributes()->height . '"width="' . $layoutData['field']->element->attributes()->width . '" ></img>';
	}

	/**
	 * Method to download file.
	 *
	 * @param   object  $data        file data.
	 * @param   array   $layoutData  layoutData
	 *
	 * @return  string
	 *
	 * @since    1.5
	 */
	protected function canDownloadFile($data,$layoutData)
	{
		$mediaLink = JUri::root() . "index.php?option=com_tjfields&task=getMedia&fpht=" . base64_encode($layoutData['value']);

		return '<div> <a href="' . $mediaLink . '">' . JText::_("COM_TJFIELDS_FILE_DOWNLOAD") . '</a>';
	}

	/**
	 * Method to delete file.
	 *
	 * @param   object  $data        file data.
	 * @param   array   $layoutData  layoutData
	 *
	 * @return  string
	 *
	 * @since    1.5
	 */
	protected function canDeleteFile($data,$layoutData)
	{
		$canEdit = 0;

		if ($data->user->authorise('core.field.editfieldvalue', 'com_tjfields.group.' . $data->tjFieldFieldTable->group_id))
		{
			$canEdit = $data->user->authorise('core.field.editfieldvalue', 'com_tjfields.field.' . $data->tjFieldFieldTable->id);
		}

		$canEditOwn = 0;

		if ($data->user->authorise('core.field.editownfieldvalue', 'com_tjfields.group.' . $data->tjFieldFieldTable->group_id))
		{
			$canEditOwn = $data->user->authorise('core.field.editownfieldvalue', 'com_tjfields.field.' . $data->tjFieldFieldTable->id);

			if ($canEditOwn && ($data->user->id != $data->fields_value_table->user_id))
			{
				$canEditOwn = 0;
			}
		}

		$deleteFiledata = '';

		if (!empty($data->mediaLink) && ($canEdit || $canEditOwn) && $layoutData['required'] == '' && $data->fields_value_table->id)
		{
			$deleteFiledata .= ' <span class="btn btn-remove"> <a id="remove_' . $layoutData["id"] . '" href="javascript:void(0);"
				onclick="deleteFile(\'' . base64_encode($layoutData["value"]) . '\',
				 \'' . $layoutData["id"] . '\', \'' . base64_encode($data->fields_value_table->id) . '\',
				  \'' . $data->subFormFileFieldId . '\',\'' . $data->isSubformField . '\');">'
				. JText::_("COM_TJFIELDS_FILE_DELETE") . '</a> </span>';
		}

		return $deleteFiledata;
	}
}
