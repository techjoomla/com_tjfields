<?php
/**
 * @package    TJFields
 * 
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2019 TechJoomla. All rights reserved
 * @license    GNU General Public License version 2, or later
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');
JFormHelper::loadFieldClass('category');

/**
 * Form Field class for the Joomla Platform.
 * Supports an HTML select list of categories
 *
 * @since  1.5
 */
class JFormFieldItemCategory extends JFormFieldCategory
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.5
	 */
	public $type = 'Itemcategory';

	/**
	 * Method to get the field options for category
	 * Use the extension attribute in a form to specify the.specific extension for
	 * which categories should be displayed.
	 * Use the show_root attribute to specify whether to show the global category root in the list.
	 *
	 * @return  array    The field option objects.
	 *
	 * @since   1.5
	 */
	protected function getOptions()
	{
		$jinput = JFactory::getApplication()->input;
		$client = $jinput->get('client', '', "STRING");
		$this->element->addAttribute('extension', $client);

		return parent::getOptions();
	}
}
