var userAgentIsMobile = false;
var subFormFieldCount = 0;

jQuery(document).ready(function() {
	/* For mobile devices need to use a button to initialise camera as its not initialised on page load*/
	if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
		userAgentIsMobile = true;
	}

	/* Initialise camera on page load by clicking a hidden button - the button is used in case of mobile devices as we cant activate camera on page load on mobile devices*/
	jQuery.each(jQuery("#item-form").find(jQuery("input[onClick^='set_camera(']")), function(key, value) {
		if (jQuery('#' + jQuery(value).attr('id').replace('take_another', '_hasvalue')).val() == '0') {
			jQuery(value).click();
		}
	});

	/* Hide button to capture image on mobile device as this is done via mobile interface*/
	jQuery.each(jQuery("#item-form").find(jQuery("input[id^='_take_snapshot']")), function(key, value) {
		if (userAgentIsMobile) {
			document.getElementById(jQuery(value).attr('id')).style = 'display:none;';
		}
	});

	/* Update the options of related field for new record of subform */
	jQuery(document).on('subform-row-add', function(event, row) {
		subFormFieldCount = jQuery(row).attr('data-group').replace(jQuery(row).attr('data-base-name'), "");
		var likeName = 'jform_' + jQuery(row).attr('data-base-name') + '__' + jQuery(row).attr('data-group').substring(0, jQuery(row).attr('data-group').length - 7);

		/* Update the id of elements used in field when a new group of field is added for subform */
		jQuery.each(jQuery(row).find(jQuery("input[id^='" + likeName + "']")), function(key, value) {
			jQuery(value).attr('id', jQuery(value).attr('id').replace("X__", subFormFieldCount + '__'));
			jQuery(value).attr('name', jQuery(value).attr('id').replace("X__", subFormFieldCount + '__'));
		});

		/* Update the id of elements used in field when a new group of field is added for subform */
		jQuery.each(jQuery(row).find(jQuery("div[id^='" + likeName + "']")), function(key, value) {
			jQuery(value).attr('id', jQuery(value).attr('id').replace("X__", subFormFieldCount + '__'));
			jQuery(value).attr('name', jQuery(value).attr('id').replace("X__", subFormFieldCount + '__'));
		});

		/* Initialise camera on page load by clicking a hidden button - the button is used in case of mobile devices as we cant activate camera on page load on mobile devices*/
		jQuery.each(jQuery(row).find(jQuery("input[onClick^='set_camera(']")), function(key, value) {
			if (jQuery('#' + jQuery(value).attr('id').replace('take_another', '_hasvalue')).val() == '0') {
				jQuery(value).click();
			}
		});

		/* Update the id of elements used in field when a new group of field is added for subform */
		jQuery.each(jQuery(row).find(jQuery("input[onClick^='set_camera(']")), function(key, value) {
			jQuery(value).attr('onClick', jQuery(value).attr('onClick').replace('X__', subFormFieldCount + '__'));
		});

		/* Update the id of elements used in field when a new group of field is added for subform */
		jQuery.each(jQuery(row).find(jQuery("input[onClick^='take_snapshot(']")), function(key, value) {
			jQuery(value).attr('onClick', jQuery(value).attr('onClick').replace('X__', subFormFieldCount + '__'));
		});

		/* Update the id of elements used in field when a new group of field is added for subform */
		jQuery.each(jQuery(row).find(jQuery("input[onClick^='use_snapshot(']")), function(key, value) {
			jQuery(value).attr('onClick', jQuery(value).attr('onClick').replace('X__', subFormFieldCount + '__'));
		});

		/* Hide button to capture image on mobile device as this is done via mobile interface*/
		jQuery.each(jQuery(row).find(jQuery("input[id^='_take_snapshot']")), function(key, value) {
			if (userAgentIsMobile) {
				document.getElementById(jQuery(value).attr('id')).style = 'display:none;';
			}
		});
	});
});

/* Method to take snapshot - used for non mobile devices*/
function take_snapshot(thisId) {
	Webcam.snap(function(data_uri) {
		document.getElementById(thisId + '_capture_img').style = 'display:none';
		document.getElementById(thisId + '_captured_img').style = '';
		jQuery('#' + thisId + '_captured_img').find("img").attr("src", data_uri);
		jQuery('#' + thisId).val(jQuery('#' + thisId + '_captured_img').find("img").attr("src")).trigger('change');
		document.getElementById(thisId + '_take_snapshot').style = 'display:none;';
		document.getElementById(thisId + '_take_another').style = '';
	});
}

/* Method to take snapshot - used for mobile devices*/
function use_snapshot(thisId) {
	Webcam.snap(function(data_uri) {
		document.getElementById(thisId + '_capture_img').style = 'display:none';
		document.getElementById(thisId + '_captured_img').style = '';
		jQuery('#' + thisId + '_captured_img').find("img").attr("src", data_uri);
		Webcam.reset();

		if (userAgentIsMobile) {
			document.getElementById(thisId + '_take_snapshot').style = 'display:none;';
			document.getElementById(thisId + '_use').style = 'display:none;';
		}

		jQuery('#' + thisId).val(jQuery('#' + thisId + '_captured_img').find("img").attr("src")).trigger('change');

		document.getElementById(thisId + '_take_snapshot').style = 'display:none;';
		document.getElementById(thisId + '_take_another').style = '';
	});
}

/* Method to initialise and re-initialise camera - used for non mobile devices*/
function set_camera(thisId) {
	thisId = thisId.replace('X__', subFormFieldCount + '__');
	document.getElementById(thisId + '_capture_img').style = '';
	document.getElementById(thisId + '_captured_img').src = '';
	jQuery('#' + thisId + '_captured_img').find("img").attr("src", '');
	document.getElementById(thisId + '_captured_img').style = 'display:none';
	document.getElementById(thisId + '_take_another').style = 'display:none;';
	document.getElementById(thisId + '_take_snapshot').style = '';
	document.getElementById(thisId + '_use').style = 'display:none;';

	if (userAgentIsMobile) {
		document.getElementById(thisId + '_take_snapshot').style = 'display:none;';
		document.getElementById(thisId + '_use').style = '';
	}

	/* WebcamJs code to initialise camera - used for mobile devices*/
	Webcam.set({
		width: document.getElementById(thisId + '__camera_width').value,
		height: document.getElementById(thisId + '__camera_height').value,
		image_format: 'jpeg',
		jpeg_quality: 180
	});

	/* WebcamJs code to attach camera outupt to a div on the page*/
	Webcam.attach('#' + thisId + '_capture_img');
}
