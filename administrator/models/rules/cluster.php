<?php
/**
 * @package    TJ-Fields
 *
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2019 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;
use Joomla\String\StringHelper;

/**
 * JFormRule for com_contact to make sure the email address is not blocked.
 *
 * @since  1.6
 */
class JFormRuleCluster extends JFormRule
{
	/**
	 * Method to test for banned email addresses
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 * @param   Registry          $input    An optional Registry object with the entire data set to validate against the entire form.
	 * @param   JForm             $form     The form object for which the field is being tested.
	 *
	 * @return  boolean  True if the value is valid, false otherwise.
	 */
	public function test(SimpleXMLElement $element, $value, $group = null, Registry $input = null, JForm $form = null)
	{
		$required = ($element['required'] instanceof SimpleXMLElement) ? $element['required']->__toString() : 'false';

		if ($required == 'true')
		{
			if ($value == "" || empty($value))
			{
				return false;
			}
		}

		JLoader::import("/components/com_subusers/includes/rbacl", JPATH_ADMINISTRATOR);
		JLoader::import("/components/com_cluster/includes/cluster", JPATH_ADMINISTRATOR);
		$clustersModel = ClusterFactory::model('Clusters', array('ignore_request' => true));
		$clusters = $clustersModel->getItems();
		$usersClusters = array();

		if (!empty($clusters))
		{
			foreach ($clusters as $clusterList)
			{
				if (RBACL::check(JFactory::getUser()->id, 'com_cluster', 'core.edititem', $clusterList->id) || RBACL::check(JFactory::getUser()->id, 'com_cluster', 'core.editallitem'))
				{
					if (!empty($clusterList->id))
					{
						$clusterObj = new stdclass;
						$clusterObj->text = $clusterList->name;
						$clusterObj->value = $clusterList->id;

						$usersClusters[] = $clusterObj;
					}
				}
			}
		}

		if ($value != "")
		{
			foreach ($usersClusters as $cluster)
			{
				if ($value == $cluster->value)
				{
					return true;
				}
			}

			return false;
		}

		return true;
	}
}
