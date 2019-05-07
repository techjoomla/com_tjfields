/* New JS file for TJ-Fields */
var tjFields = {
	relatedField: {
		populateFields: function(clientObj) {
			let client = jQuery(clientObj).val();
			let clientElementId = jQuery(clientObj).attr('id');
			let fieldsElementId = clientElementId.replace("client", "fieldIds");

			jQuery.ajax({
				url: Joomla.getOptions('system.paths').base + "/index.php?option=com_tjfields&task=fields.getFields&format=json&" + Joomla.getOptions('csrf.token') + "=1",
				type: 'POST',
				data:{
					client: client
				},
				dataType: "json",
				success: function (result) {

					jQuery("#"+fieldsElementId).html("");

					if (result.data)
					{
						jQuery.each(result.data, function( index, field ) {
							jQuery("#"+fieldsElementId).append("<option value="+field.id+">"+field.label+"</option>");
						});

						jQuery("#"+fieldsElementId).trigger("liszt:updated");
					}
				}
			});
		}
	}
};
