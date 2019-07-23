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
$date = JFactory::getDate($field->value);
$format = str_replace('%', '', $field->format);

if ($field->value)
{
	echo $date->format($format);
}
else
{
	echo "-";
}
