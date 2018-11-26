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
	protected $type = 'video';

	/**
	 * The SimpleXMLElement object representing the `<field>` tag for the form field object.
	 *
	 * @var    mixed
	 * @since  3.2
	 */
	protected $element;

	/**
	 * Name of the layout being used to render the field
	 *
	 * @var    string
	 * @since  3.6
	 */
	protected $layout = 'joomla.form.field.url';

	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 *
	 * @param   string  $name  The property name for which to the the value.
	 *
	 * @return  mixed  The property value or null.
	 *
	 * @since   3.2
	 */
	public function __get($name)
	{
		require_once JPATH_SITE . '/components/com_tjfields/helpers/tjfields.php';

		switch ($name)
		{
			case 'element';

				return $this->element;
				break;
		}

		return parent::__get($name);
	}

	/**
	 * Method to set certain otherwise inaccessible properties of the form field object.
	 *
	 * @param   string  $name   The property name for which to the the value.
	 * @param   mixed   $value  The value of the property.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function __set($name, $value)
	{
		switch ($name)
		{
			default:
				parent::__set($name, $value);
		}
	}

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
	 * @since   3.2
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		return $return;
	}

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

		if (empty($layoutData['value']))
		{
			return $html;
		}

		if (isset($layoutData['field']->element->attributes()->display_video))
		{
				$html .= '<div class="control-group">';
				$html .= '<a href="#" class="videopopup"/>Click to watch video</a>';
				$html .= '<div class="modal fade" id="videoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">Video</h4>
					</div>
					<div class="modal-body">';
					$html .= $this->rendervideo($layoutData, $layoutData['value']);
				$html .= '
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>';
		}
		else
		{
			$html .= $this->rendervideo($layoutData, $layoutData['value']);
		}

		if (!empty($layoutData['value']) && $layoutData['field']->element->attributes()->showvideolink)
		{
			$html .= '<br><a target="_blank" href=' . $layoutData['value'] . '> ' . $layoutData['value'] . '</a>';
		}

		// Renderer
		$doc = JFactory::getDocument();
		$doc->addScript(JUri::root() . 'administrator/components/com_tjfields/assets/js/mediaplayer/mediaelement-and-player.min.js');
		$doc->addStyleSheet(JUri::root() . 'administrator/components/com_tjfields/assets/css/mediaplayer/mediaelementplayer.min.css');
		$doc->addScript(JUri::root() . 'administrator/components/com_tjfields/assets/js/mediaplayer/vimeo.min.js');
		$doc->addScript(JUri::root() . 'administrator/components/com_tjfields/assets/js/mediaplayer/facebook.min.js');
		$doc->addScript(JUri::root() . 'administrator/components/com_tjfields/assets/js/mediaplayer/facebook.min.js');
		$doc->addScript(JUri::root() . 'administrator/components/com_tjfields/assets/js/mediaplayer/twitch.min.js');
		$doc->addScript(JUri::root() . 'administrator/components/com_tjfields/assets/js/mediaplayer/dailymotion.min.js');
		$doc->addScript(JUri::root() . 'administrator/components/com_tjfields/assets/js/mediaplayer/soundcloud.min.js');

		$doc->addScriptDeclaration('
			jQuery(document).ready(function() {
				jQuery("#player_' . $layoutData['field']->id . '").mediaelementplayer({
					pluginPath: "/path/to/shims/",
					success: function(mediaElement, originalNode, instance) {
				}
				});
			});
		');

		$html .= '</div>';

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

	/**
	 * Method to render image file.
	 *
	 * @param   array   $layoutData  layoutData.
	 * @param   string  $videoUrl    videoUrl
	 *
	 * @return  string
	 *
	 * @since    1.5
	 */
	protected function rendervideo($layoutData, $videoUrl)
	{
		if (isset($layoutData['field']->element->attributes()->autoplay))
		{
			$autoPlay = 'autoplay';
		}

		if (isset($layoutData['field']->element->attributes()->muted))
		{
			$muted = 'muted';
		}

		$html .= '
				<video ' . $autoPlay . ' id="player_' . $layoutData['field']->id . '"
					height="' . $layoutData['field']->element->attributes()->height . '"
					width="' . $layoutData['field']->element->attributes()->width . '
					preload="auto"
					poster ="' . $layoutData['field']->element->attributes()->poster . '"
					controls playsinline webkit-playsinline
					src="' . $videoUrl . '" ' . $muted . '>
				</video>';

		return $html;
	}
}
