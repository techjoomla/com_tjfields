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

JLoader::import('components.com_tjfields.models.field', JPATH_ADMINISTRATOR);

$xmlField = $displayData['fieldXml'];
$field = $displayData['field'];
$formSource = $field->formsource;

if ($field->value)
{
	foreach ($field->value as $name => $subformData)
	{
		$subformData = (array) $subformData;
		$tjFieldsFieldModel = JModelLegacy::getInstance('Field', 'TjfieldsModel');
		$form = $tjFieldsFieldModel->getSubFormFieldForm($name, $formSource);
		?>
		<div class="row">
		<?php
		foreach ($form->getFieldsets() as $fieldSet)
		{
			$fieldSet = $form->getFieldset($fieldSet->name);

			foreach ($fieldSet as $field)
			{
				?>
				<div class="col-xs-6">
					<?php
					echo JText::_($field->getAttribute('label'));
					?>
				</div>
				<div class='col-xs-6'>
					<?php
					if ($field->type == 'List' || $field->type == 'Radio')
					{
						$fieldXml = $form->getFieldXml($field->getAttribute('name'));
						$field->value = $subformData[$field->getAttribute('name')];

						$layout = new JLayoutFile('list', JPATH_ROOT . '/components/com_tjfields/layouts/fields');
						echo $layout->render(array('fieldXml' => $fieldXml, 'field' => $field));
					}
					elseif ($field->type == 'Checkbox')
					{
						if ($subformData[$field->getAttribute('name')])
						{
							?>
							<input type="checkbox" checked="checked">
							<?php
						}
						else
						{
							?>
							<input type="checkbox">
							<?php
						}
					}
					else
					{
						echo $subformData[$field->getAttribute('name')];
					}
					?>
				</div>
				<?php
			}
		}
		?>
		</div>
		<hr>
		<?php
	}
}