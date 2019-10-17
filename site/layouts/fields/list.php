<?php
/**
 * @package	TJ-Fields
 * @author	 TechJoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2019 TechJoomla. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

if (!key_exists('field', $displayData) || !key_exists('fieldXml', $displayData))
{
	return;
}

$xmlField = $displayData['fieldXml'];
$field = $displayData['field'];

if (!$xmlField instanceof SimpleXMLElement)
{
	return;
}

$xmlOptions = $xmlField->children();
$options = array();

foreach ($xmlOptions as $xmlOption)
{
	$xmlOptionText = ($xmlOption instanceof SimpleXMLElement) ? $xmlOption->__toString() : '';
	$xmlOptionValue = ($xmlOption['value'] instanceof SimpleXMLElement) ? $xmlOption['value']->__toString() : '';
	$options[$xmlOptionValue] = $xmlOptionText;
}

if ($field->value)
{
	if (!is_array($field->value))
	{
		// If single select
		if (isset($options[$field->value]))
		{
			$options[$field->value] = htmlspecialchars($options[$field->value], ENT_COMPAT, 'UTF-8');
			echo JText::_(ucfirst($options[$field->value]));
		}
	}
	else
	{
		// If multi select
		foreach ($field->value as $value)
		{
			if (isset($options[$value]))
			{
				$options[$value] = htmlspecialchars($options[$value], ENT_COMPAT, 'UTF-8');
				echo JText::_(ucfirst($options[$value]));
				echo "<br>";
			}
		}
	}
}