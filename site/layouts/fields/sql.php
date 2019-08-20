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

$field = $displayData['field'];

$value = $field->value;

if ($value == '')
{
	return;
}

$db        = JFactory::getDbo();
$value     = (array) $value;
$condition = '';

foreach ($value as $v)
{
	if (!$v)
	{
		continue;
	}

	$condition .= ', ' . $db->q($v);
}

$query = $field->getAttribute('query');
$keyColumn = $field->getAttribute('key_field');
$valueColumn = $field->getAttribute('value_field');

// Run the query with a having condition because it supports aliases
$db->setQuery($query . ' having ' . $keyColumn . ' in (' . trim($condition, ',') . ')');

try
{
	$items = $db->loadObjectlist();
}
catch (Exception $e)
{
	// If the query failed, we fetch all elements
	$db->setQuery($query);
	$items = $db->loadObjectlist();
}

$texts = array();

foreach ($items as $item)
{
	if (in_array($item->$keyColumn, $value))
	{
		$texts[] = $item->$valueColumn;
	}
}

echo htmlentities(implode(', ', $texts));
