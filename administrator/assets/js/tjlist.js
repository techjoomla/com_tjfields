/*
 * @package    Tjfields
 * @subpackage Com_Tjfields
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2019 TechJoomla. All rights reserved
 * @license    GNU General Public License version 2, or later
 */
var tjlist = {
	tagInput : true,
	addOtherOption: function (element) {
		jQuery(tjlist.getTextBox(element)).insertAfter(element.next(".chzn-container"));

		setTimeout(function() {
			element.siblings('div.tjfieldTjListOtherText').children('.bootstrap-tagsinput').children().focus();
		}, 100);
	},
	removeOtherOption: function (element) {
		element.siblings('div.tjfieldTjListOtherText').remove();
	},
	getTextBox: function (element) {
		var inputName     = element.attr('name'),
			inputId       = element.attr('id'),
			isRequired    = (element.attr('required') != undefined) ? 'required="required"' : '',
			requiredClass = (isRequired != '') ? 'required' : '';

		// Start -To add tags type inputbox design
		if (this.tagInput)
		{
			jQuery('.bootstrap-tagsinput').remove();
		}

		this.load_tagsinputjs();
		this.tagInput = false;
		// End

		return '<div class="tjfieldTjListOtherText"><br/><input data-role="tagsinput" placeholder="' + Joomla.JText._('COM_TJFIELDS_OTHER_VALUE') + '" ' + isRequired + ' type="text" name="' + inputName + '" id="' + inputId + '" value="" class=" focus ' + requiredClass + '" aria-invalid="false"></div>';
	},
	load_tagsinputjs: function()
	{
	  var head= document.getElementsByTagName('head')[0];
	  var script= document.createElement('script');

	  script.src= Joomla.getOptions('system.paths').base +'/administrator/components/com_tjfields/assets/js/bootstrap-tagsinput.min.js';
	  head.appendChild(script);
	}
}

jQuery(document).ready(function() {
	jQuery(document).on("change", ".tjfieldTjList", function () {

		var selectedVal = jQuery(this).val();

		if (
			(jQuery.inArray(Joomla.JText._('COM_TJFIELDS_TJLIST_OTHER_OPTION_VALUE'), selectedVal) !== -1)
			||
			(selectedVal == Joomla.JText._('COM_TJFIELDS_TJLIST_OTHER_OPTION_VALUE'))
		)
		{
			if (jQuery('input[name="' + jQuery(this).attr('name') + '"]').length == 0)
			{
				tjlist.addOtherOption(jQuery(this));
			}
		}
		else
		{
			tjlist.removeOtherOption(jQuery(this));
		}
	});
});
