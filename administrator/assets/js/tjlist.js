/*
 * @package    Tjfields
 * @subpackage Com_Tjfields
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2019 TechJoomla. All rights reserved
 * @license    GNU General Public License version 2, or later
 */
var tjlist = {

	addOtherOption: function (element) {

		jQuery(tjlist.getTextBox(element)).insertAfter(element.next(".chzn-container"));
	},
	removeOtherOption: function (element) {

		element.siblings('div.tjfieldTjListOtherText').remove();
	},
	getTextBox: function (element) {
		let inputName     = element.attr('name'),
			inputId       = element.attr('id'),
			isRequired    = (element.attr('required') != undefined) ? 'required="required"' : '',
			requiredClass = (isRequired != '') ? 'required' : '';

		return '<div class="tjfieldTjListOtherText"><br/><input ' + isRequired + ' type="text" name="' + inputName + '" id="' + inputId + '" value="" class=" ' + requiredClass + '" aria-invalid="false"></div>';
	}
}

jQuery(document).ready(function() {
	jQuery(document).on("change", ".tjfieldTjList", function () {

		let selectedVal = jQuery(this).val();

		if (
			(jQuery.inArray(Joomla.JText._('COM_TJFIELDS_TJLIST_OTHER_OPTION_VALUE'), selectedVal) !== -1)
			||
			(selectedVal == Joomla.JText._('COM_TJFIELDS_TJLIST_OTHER_OPTION_VALUE'))
		)
		{
			if (jQuery('input[name="' + jQuery(this).attr('name') + '"').length == 0)
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
