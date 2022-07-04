<?php
/**
 * @package    Tjfields
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2022 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Supports an HTML select list of categories
 *
 * @since  1.0
 */
class JFormFieldUniqueid extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since    1.0
	 */
	protected $type = 'uniqueid';

	/**
	 * Method to get the field input markup.
	 *
	 * @return    string    The field input markup.
	 *
	 * @since    1.6
	 */
	protected function getInput()
	{
		$html  = array();
		$input = Factory::getApplication()->input;
		$ucmId = $input->get("id", 0, "INT");

		if (empty($this->value))
		{
			$characters = '0123456789';
			$charactersLength = strlen($characters);
			$randomString = '';

			for ($i = 0; $i < $this->element->attributes()->uniqueid_digitcount; $i++)
			{
				$randomString .= $characters[rand(0, $charactersLength - 1)];
			}
			
			$uniqueidValue = $this->element->attributes()->uniqueid_prefix . '_' . $randomString . '_';
		}

		if (!empty($uniqueidValue))
		{
			$html[] = '<input type="hidden" name="' . $this->name . '" value="'. $uniqueidValue.'" readonly />';
		}

		return implode($html);
	}
}
