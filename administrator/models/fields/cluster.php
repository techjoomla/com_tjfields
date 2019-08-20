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
			$options[] = HTMLHelper::_('select.option', $cluster->id, trim($cluster->name));
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
			$this->readonly = true;
		}

		// Add script to initialise ownership field
		$document = JFactory::getDocument();
		$document->addScriptDeclaration('jQuery(document).ready(function() {
			var dataFields = {cluster_id: 0, user_id: 0};
			ownership.setUsers(dataFields, "' . $this->id . '");
		});');

		return parent::getInput();
	}
}
