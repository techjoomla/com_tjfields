<?php
/**
 * @package     TJ-Fields
 * @subpackage  Form
 * author       TechJoomla <extensions@techjoomla.com>
 * @copyright   Copyright (c) 2009-2019 TechJoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

if (!key_exists('field', $displayData) || !key_exists('fieldXml', $displayData))
{
	return;
}

JLoader::import('tjfields', JPATH_SITE . '/components/com_tjfields/helpers/');

$xmlField = $displayData['fieldXml'];
$field = $displayData['field'];
$isSubFormField = (isset($displayData['isSubFormField'])) ? $displayData['isSubFormField'] : 0;
$subFormFileFieldId = (isset($displayData['subFormFileFieldId'])) ? $displayData['subFormFileFieldId'] : 0;

if ($field->value)
{
	JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjfields/tables');
	$fieldsValueTable = JTable::getInstance('Fieldsvalue', 'TjfieldsTable');
	$fieldsValueTable->load(array('value' => $field->value));

	$extraParamArray = array();
	$extraParamArray['id'] = $fieldsValueTable->id;

	// Creating media link by check subform or not
	if ($isSubFormField)
	{
		$extraParamArray['subFormFileFieldId'] = $subFormFileFieldId;
	}

	$tjFieldHelper = new TjfieldsHelper;
	$mediaLink = $tjFieldHelper->getMediaUrl($field->value, $extraParamArray);

	// To get the file name from URL
	$substrString = substr($mediaLink, strlen('fpht=') + strpos($mediaLink, 'fpht='));
	$substrString = substr($substrString, 0, strpos($substrString, '='));

	// Decode the filename
	$fileName = base64_decode($substrString);

	echo "<a href=" . $mediaLink . ">" . JText::_("COM_TJFIELDS_FILE_DOWNLOAD") . "</a>";

	// To display the file name if exist and skip the prepended file name value
	if (!empty($fileName))
	{
		echo '<strong class="ml-15"> ' . substr($fileName, strpos($fileName, '_', 12) + 1) . '</strong>';
	}
}
