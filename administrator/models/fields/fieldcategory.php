<?php
/**
 * @version    SVN: <svn_id>
 * @package    TJ-Fields
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access.
defined('_JEXEC') or die();

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Form\Field\ListField;

/**
 * Class for custom gateway element
 *
 * @since  1.0.0
 */
if (JVERSION < '4.0')
{
	class JFormFieldFieldcategory extends FormField
	{
		protected $type = 'Fieldcategory';

		protected $name = 'Fieldcategory';

		/**
		 * Function to genarate html of custom element
		 *
		 * @return  HTML
		 *
		 * @since  1.0.0
		 */
		public function getInput()
		{
			$this->value = $this->getSelectedCategories();
			$controlName = (isset($this->options['control'])) ? $this->options['control'] : '';

			return $this->fetchElement($this->name, $this->value, $this->element, $controlName);
		}

		/**
		 * Function to fetch a tooltip
		 *
		 * @param   string  $name          name of field
		 * @param   string  $value         value of field
		 * @param   string  &$node         node of field
		 * @param   string  $control_name  control_name of field
		 *
		 * @return  HTML
		 *
		 * @since  1.0.0
		 */
		public function fetchElement($name, $value, &$node, $control_name)
		{
			$jinput       = Factory::getApplication()->input;
			$id           = $jinput->get('id', '', 'int');
			$clientStr    = $jinput->get("client");
			$ClientDetail = explode('.', $clientStr);
			$client       = $ClientDetail[0];
			$options      = array();

			// Fetch only published category. Static public function options($extension, $config = array('filter.published' => array(0,1)))
			$categories    = HTMLHelper::_('category.options', $client, array('filter.published' => array(1)));
			$category_list = array_merge($options, $categories);

			foreach ($category_list as $category)
			{
				$options[] = HTMLHelper::_('select.option', $category->value, $category->text);
			}

			$fieldName = $name;
			$disabled  = '';

			if (!empty($id))
			{
				$disabled = 'disabled="true"';
			}

			$class = (JVERSION < '4.0.0') ? '' : 'form-select';
			$html  = HTMLHelper::_('select.genericlist', $options, $fieldName,
			'class="inputbox ' . $class . '"  multiple="multiple" size="5" ' . $disabled, 'value', 'text', $value, $control_name . $name
			);

			return $html;
		}

		/**
		 * Function to fetch a tooltip
		 *
		 * @param   string  $label         label of field
		 * @param   string  $description   description of field
		 * @param   string  &$node         node of field
		 * @param   string  $control_name  control_name of field
		 * @param   string  $name          name of field
		 *
		 * @return  HTML
		 *
		 * @since  1.0.0
		 */
		public function fetchTooltip($label, $description, &$node, $control_name, $name)
		{
			return null;
		}

		/**
		 * Fetch category list for field
		 *
		 * @return  category array
		 *
		 * @since  1.0.0
		 */
		public function getSelectedCategories()
		{
			$catList = array();
			$jinput  = Factory::getApplication()->input;
			$fieldId = $jinput->get("id");

			if (!empty($fieldId))
			{
				$db    = Factory::getDBO();
				$query = $db->getQuery(true)
							->select('category_id')
							->from($db->quoteName('#__tjfields_category_mapping'))
							->where('field_id=' . $fieldId);
				$db->setQuery($query);

				return $db->loadColumn();
			}
		}
	}
}
else
{
	class JFormFieldFieldcategory extends ListField
	{
		/**
		 * Function to genarate html of custom element
		 *
		 * @return  HTML
		 *
		 * @since  2.0.1
		 */
		public function getInput()
		{
			$this->value = $this->getSelectedCategories();
			$controlName = (isset($this->options['control'])) ? $this->options['control'] : '';
			$html        = parent::getInput();

			return $html;
		}

		/**
		 * Function to fetch a tooltip
		 *
		 * @param   string  $name          name of field
		 * @param   string  $value         value of field
		 * @param   string  &$node         node of field
		 * @param   string  $control_name  control_name of field
		 *
		 * @return  HTML
		 *
		 * @since  2.0.1
		 */
		public function getOptions()
		{
			$jinput       = Factory::getApplication()->input;
			$clientStr    = $jinput->get("client");
			$ClientDetail = explode('.', $clientStr);
			$client       = $ClientDetail[0];
			$options      = array();

			// Fetch only published category. Static public function options($extension, $config = array('filter.published' => array(0,1)))
			$categories    = HTMLHelper::_('category.options', $client, array('filter.published' => array(1)));
			$category_list = array_merge($options, $categories);

			foreach ($category_list as $category)
			{
				$options[] = HTMLHelper::_('select.option', $category->value, $category->text);
			}

			return $options;
		}

		/**
		 * Fetch category list for field
		 *
		 * @return  category list array
		 *
		 * @since  2.0.1
		 */
		public function getSelectedCategories()
		{
			$catList = array();
			$fieldId = Factory::getApplication()->input->get("id");

			if (!empty($fieldId))
			{
				$db    = Factory::getDBO();
				$query = $db->getQuery(true)
							->select('category_id')
							->from($db->quoteName('#__tjfields_category_mapping'))
							->where('field_id=' . $fieldId);
				$db->setQuery($query);
				$catList = $db->loadColumn();
			}

			return $catList;
		}
	}
}
