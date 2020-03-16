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

class JFormFieldCluster extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $type = 'Cluster';

	/**
	 * Fiedd to decide if options are being loaded externally and from xml
	 *
	 * @var		integer
	 * @since	__DEPLOY_VERSION__
	 */
	protected $loadExternally = 0;

	/**
	 * The form field value.
	 *
	 * @var    mixed
	 * @since  __DEPLOY_VERSION__
	 */
	protected $value = '';

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
	 * @since   DEPLOY_VERSION
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		// If the field is required and we have only one option to select then dont need to how the option
		if ($this->required)
		{
			$optionCount = 0;
			$optionValue = "";

			foreach ($this->options as $option)
			{
				if ($option->value)
				{
					$optionValue = $option->value;
					$optionCount++;
				}
			}

			if ($optionCount == 1)
			{
				$this->hidden = true;
				$this->value = $optionValue;
				$this->default = $optionValue;

				// Render the field as hidden if only one option to select
				echo "<input type='hidden' name='" . $this->name . "' id='" . $this->id ."' value='" . $this->value . "' />";
			}
		}

		return $return;
	}

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

		// Initialize array to store dropdown options
		$options[] = HTMLHelper::_('select.option', "", Text::_('COM_TJFIELDS_OWNERSHIP_CLUSTER'));

		// Get com_subusers component status
		$clusterExist = ComponentHelper::getComponent('com_cluster', true)->enabled;

		if (!$clusterExist)
		{
			return $options;
		}

		JLoader::import("/components/com_cluster/includes/cluster", JPATH_ADMINISTRATOR);
		$clusterUserModel = ClusterFactory::model('ClusterUser', array('ignore_request' => true));
		$clusters = $clusterUserModel->getUsersClusters($user->id);

		// Create oprion for each cluster
		foreach ($clusters as $cluster)
		{
			$options[] = HTMLHelper::_('select.option', $cluster->cluster_id, trim($cluster->name));
		}

		if (!$this->loadExternally)
		{
			// Merge any additional options in the XML definition.
			$options = array_merge(parent::getOptions(), $options);
		}

		return $options;
	}

	/**
	 * Method to get a list of options for a list input externally and not from xml.
	 *
	 * @return	array	An array of JHtml options.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getOptionsExternally()
	{
		$this->loadExternally = 1;

		return $this->getOptions();
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
		$clusterId = Factory::getApplication()->input->getInt('cluster_id', 0);

		if (!empty($clusterId))
		{
			$this->value = $clusterId;
		}

		return parent::getInput();
	}
}
