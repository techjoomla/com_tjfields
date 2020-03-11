<?php
/**
 * @package     Tjfields
 * @subpackage  Com_Tjfields
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (c) 2009-2020 TechJoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\HTML\HTMLHelper;

JLoader::register('JFormFieldRadio', JPATH_BASE . '/libraries/joomla/form/fields/radio.php');

/**
 * Form Field Rating class
 *
 * @since  __DEPLOY_VERSION__
 */
class JFormFieldRating extends JFormFieldRadio
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $type = 'rating';

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
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
			$this->minrating = isset($this->element['minrating']) ? (int) $this->element['minrating'] : 0;
			$this->maxrating = isset($this->element['maxrating']) ? (int) $this->element['maxrating'] : 0;
			$this->ratingstep = isset($this->element['ratingstep']) ? (float) $this->element['ratingstep'] : 0.0;
			$this->ratingstyle = isset($this->element['ratingstyle']) ? $this->element['ratingstyle'] : '';
		}

		return $return;
	}

	/**
	 * Method to get the rating field input markup.
	 * Use the rows and columns attributes to specify the dimensions of the area.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getInput()
	{
		$html = '';
		$html .= '<fieldset id="' . $this->id . '" class=" rating ' . $this->ratingstyle . '">';

		for ($rating = $this->maxrating; $rating >= $this->minrating; $rating--)
		{
			$checked = '';

			if ($rating == $this->value)
			{
				$checked = 'checked="checked"';
			}

			$html .= '<input type="radio" id="' . $this->id . $rating . '" name="' . $this->name . '" value="' . $rating . '"' . $checked .
			' /><label class = "full" for="' . $this->id . $rating . '" title="' . $rating . $this->ratingstyle . '"></label>';

			if ($this->ratingstep)
			{
				$halfRating = ($this->ratingstep) / (2);
				$ratingValue = $rating - $halfRating;

				$checked = '';

				if ($ratingValue == $this->value)
				{
					$checked = 'checked="checked"';
				}

				$html .= '<input type="radio" id="' . $this->id . $ratingValue . '" name="' . $this->name . '" value="' . $ratingValue .
				'"' . $checked . ' /><label class = "half" for="' . $this->id . $ratingValue . '" title="' . $ratingValue . $this->ratingstyle . '"></label>';
			}

			$rating = $rating - ($this->ratingstep - 1);
		}

		$html .= '</fieldset>';

		return $html;
	}
}
