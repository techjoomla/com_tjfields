<?php
/**
 * @package    Tjfields
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2019 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

JFormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of allocated cluster
 *
 * @since  __DEPLOY_VERSION__
 */

class JFormFieldOwnerShip extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $type = 'Ownership';

	/**
	 * Method to get a list of options for cluster field.
	 *
	 * @return array An array of JHtml options.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getOptions()
	{
		$user = Factory::getUser();
		$options = array();

		if (!$user->id)
		{
			return $options;
		}

		$doc = Factory::getDocument();
		$doc->addScript(JUri::root() . 'administrator/components/com_tjfields/assets/js/ownershipfield.js');

		$data = $this->getLayoutData();

		$fieldValue = $data['field']->value;

		// Used to keep pre selected user value in 'Ownership' type field
		echo '<input name="ownership_user" id="' . $this->id . 'value' . '" type="hidden" value="' . $fieldValue . '" />';

		$options = array();

		// Initialize array to store dropdown options
		$options[] = HTMLHelper::_('select.option', "", Text::_('COM_TJFIELDS_OWNERSHIP_USER'));

		return $options;
	}
}
