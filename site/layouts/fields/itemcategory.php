<?php
/**
 * @package	TJ-Fields
 * @author	 TechJoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2019 TechJoomla. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

if (!key_exists('field', $displayData))
{
	return;
}

$field = $displayData['field'];
$categoryId = (int) $field->value;

if ($categoryId)
{
	$db = JFactory::getApplication();
	JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_category/tables');
	$categoryTable = JTable::getInstance('Category', 'JTable', array('dbo', $db));
	$categoryTable->load($categoryId);

	echo htmlspecialchars($categoryTable->title, ENT_COMPAT, 'UTF-8');
}