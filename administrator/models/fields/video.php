<?php
/**
 * @version    SVN:<SVN_ID>
 * @package    TJFields
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2018 TechJoomla. All rights reserved
 * @license    GNU General Public License version 2, or later
 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JFormFieldUrl', JPATH_BASE . '/libraries/joomla/form/fields/url.php');

/**
 * Form Field video class
 * Supports a multi line area for entry of plain text with count char
 *
 * @since  11.1
 */
class JFormFieldVideo extends JFormFieldUrl
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Video';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   3.1.2 (CMS)
	 */
	protected function getInput()
	{
		require_once JPATH_SITE . '/components/com_tjfields/helpers/tjfields.php';

		$tjFieldHelper = new TjfieldsHelper;

		$layoutData = $this->getLayoutData();

		// Trim the trailing line in the layout file
		$html = rtrim($this->getRenderer($this->layout)->render($layoutData), PHP_EOL);

		$isValidYoutubeUrl = $tjFieldHelper->isValidYoutubeUrl($layoutData['value']);

		$isValidVimeoUrl = $tjFieldHelper->isValidVimeoUrl($layoutData['value']);

		$videoUrl = '';

		if ($isValidYoutubeUrl)
		{
			$videoUrl = 'https://www.youtube.com/embed/' . $isValidYoutubeUrl;
		}
		elseif ($isValidVimeoUrl)
		{
			$videoUrl = 'https://player.vimeo.com/video/' . $isValidVimeoUrl;
		}

		if (!empty($videoUrl) && $layoutData['field']->element->attributes()->rendervideolink)
		{
			$html .= '<iframe width="320" height="240" src="' . $videoUrl . '"></iframe>';
		}

		if (!empty($layoutData['value']) && $layoutData['field']->element->attributes()->showvideolink)
		{
			$html .= '<br><a href=' . $layoutData['value'] . '> ' . $layoutData['value'] . '</a>';
		}

		return $html;
	}

	/**
	 * Method to get the data to be passed to the layout for rendering.
	 *
	 * @return  array
	 *
	 * @since 3.7
	 */
	protected function getLayoutData()
	{
		$data = parent::getLayoutData();

		// Initialize some field attributes.
		$maxLength    = !empty($this->maxLength) ? ' maxlength="' . $this->maxLength . '"' : '';

		// Note that the input type "url" is suitable only for external URLs, so if internal URLs are allowed
		// we have to use the input type "text" instead.
		$inputType    = $this->element['relative'] ? 'type="text"' : 'type="url"';

		$extraData = array(
			'maxLength' => $maxLength,
			'inputType' => $inputType,
		);

		return array_merge($data, $extraData);
	}
}
