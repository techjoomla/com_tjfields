<?php
/**
 * @version    SVN: <svn_id>
 * @package    TJ-Fields
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2018 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Component\ComponentHelper;

jimport('joomla.html.html');
JFormHelper::loadFieldClass('list');

/**
 * List of fields
 *
 * @since  1.3
 */
class JFormFieldtjfieldfields extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 *
	 * @since	1.3
	 */
	protected $type = 'tjfieldfields';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 *
	 * @since	1.3
	 */
	protected function getInput()
	{
		jimport('joomla.filesystem.file');

		$installUcm = 0;

		$installCluster = 0;

		// To check com_tjucm component is installed
		if (ComponentHelper::getComponent('com_tjucm', true)->enabled)
		{
			$installUcm = 1;
		}

		// To check com_cluster component is installed
		if (ComponentHelper::getComponent('com_cluster', true)->enabled)
		{
			$installCluster = 1;
		}

		$options = array();
		$options[] = JHtml::_('select.option', 'text', JText::_('COM_TJFIELDS_TEXT'));
		$options[] = JHtml::_('select.option', 'radio', JText::_('COM_TJFIELDS_RADIO'));
		$options[] = JHtml::_('select.option', 'checkbox', JText::_('COM_TJFIELDS_CHECKBOX'));
		$options[] = JHtml::_('select.option', 'tjlist', JText::_('COM_TJFIELDS_TJLIST'));
		$options[] = JHtml::_('select.option', 'single_select', JText::_('COM_TJFIELDS_SINGLE_SELECT'));
		$options[] = JHtml::_('select.option', 'multi_select', JText::_('COM_TJFIELDS_MULTI_SELECT'));
		$options[] = JHtml::_('select.option', 'sql', JText::_('COM_TJFIELDS_SQL'));
		$options[] = JHtml::_('select.option', 'textarea', JText::_('COM_TJFIELDS_TEXTAREA'));
		$options[] = JHtml::_('select.option', 'textareacounter', JText::_('COM_TJFIELDS_TEXTAREACOUNTER'));
		$options[] = JHtml::_('select.option', 'calendar', JText::_('COM_TJFIELDS_CALENDAR'));
		$options[] = JHtml::_('select.option', 'editor', JText::_('COM_TJFIELDS_EDITOR'));
		$options[] = JHtml::_('select.option', 'email', JText::_('COM_TJFIELDS_EMAIL'));
		$options[] = JHtml::_('select.option', 'file', JText::_('COM_TJFIELDS_FILE'));
		$options[] = JHtml::_('select.option', 'spacer', JText::_('COM_TJFIELDS_SPACER'));
		$options[] = JHtml::_('select.option', 'subform', JText::_('COM_TJFIELDS_SUBFORM'));
		$options[] = JHtml::_('select.option', 'image', JText::_('COM_TJFIELDS_IMAGE'));
		$options[] = JHtml::_('select.option', 'audio', JText::_('COM_TJFIELDS_AUDIO'));
		$options[] = JHtml::_('select.option', 'video', JText::_('COM_TJFIELDS_VIDEO'));
		$options[] = JHtml::_('select.option', 'itemcategory', JText::_('COM_TJFIELDS_ITEM_CATEGORY'));
		$options[] = JHtml::_('select.option', 'number', JText::_('COM_TJFIELDS_NUMBER'));
		$options[] = JHtml::_('select.option', 'hidden', JText::_('COM_TJFIELDS_HIDDEN'));

		if ($installUcm === 1)
		{
			$options[] = JHtml::_('select.option', 'ucmsubform', JText::_('COM_TJFIELDS_UCMSUBFORM'));
			$options[] = JHtml::_('select.option', 'related', JText::_('COM_TJFIELDS_RELATED'));
		}

		if ($installCluster === 1)
		{
			$options[] = JHtml::_('select.option', 'cluster', JText::_('COM_TJFIELDS_CLUSTER'));
		}

		$options[] = JHtml::_('select.option', 'ownership', JText::_('COM_TJFIELDS_OWNERSHIP'));
		$options[] = JHtml::_('select.option', 'color', JText::_('COM_TJFIELDS_COLOR'));

		$options = array_merge(parent::getOptions(), $options);

		$view = JFactory::getApplication()->input->get('view', '', 'STRING');

		$onchange = ($view == 'field') ? "show_option_div(this.value);" : "this.form.submit();";

		return JHtml::_('select.genericlist', $options, $this->name,
		'class="required" onchange="' . $onchange . '"', 'value', 'text', $this->value, 'jform_type'
		);
	}
}
