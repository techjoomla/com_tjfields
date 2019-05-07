/*
 * @package    Com_Tjfields
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2019 TechJoomla. All rights reserved
 * @license    GNU General Public License version 2, or later
 */

var ownership = {
	/* This function to get all users in tjucm via ajax */
	getUsers: function (element) {
		let selectOption = '';
		jQuery('.user-ownership, .chzn-results').empty();
		jQuery.ajax({
			url: Joomla.getOptions('system.paths').base + "/index.php?option=com_cluster&task=clusterusers.getUsersByClientId&format=json",
			type: 'POST',
			data: element,
			dataType:"json",
			success: function (data) {
				for(index = 0; index < data.length; ++index)
				{
					selectOption = '';
					if (element.user_id == data[index].value)
					{
						selectOption = ' selected="selected" ';
					}
					let op="<option value='"+data[index].value+"' "+selectOption+" > " + data[index]['text'] + "</option>" ;
					jQuery('.user-ownership').append(op);
				}

				/* IMP : to update to chz-done selects*/
				jQuery(".user-ownership").trigger("liszt:updated");
			}
		});
	},
	/* This function to populate all users in ownership field of tjucm form */
	setUsers: function (element) {
		let clientId = '';
		element.user_id = jQuery("#ownership_user").val();

		// Check class exists or not
		if (jQuery(".cluster-ownership").length > 0)
		{
			clientId = jQuery(".cluster-ownership").val();

			element.client = clientId;
			element.iscluster = 1;
		}

		if ((jQuery.trim(clientId) != '' && clientId != 'undefined') || (jQuery(".user-ownership").length > 0 && jQuery(".cluster-ownership").length == 0))
		{
			this.getUsers(element);
		}
	}
}

jQuery(document).ready(function() {

	// Check class exists or not
	if (jQuery(".user-ownership").length > 0)
	{
		let dataFields = {client: 0, iscluster: 0, user_id: 0};

		//Get All users for user field
		ownership.setUsers(dataFields);
	}

	/* This function to get users based on cluster value in tjucm via ajax */
	jQuery('.cluster-ownership').change(function(){

		// Check class exists or not
		if (!jQuery(".user-ownership").length > 0)
		{
			return false;
		}

		let dataFields = {client: jQuery(this).val() ,iscluster: 1, user_id: jQuery("#ownership_user").val()};

		//Get All associated users
		ownership.getUsers(dataFields);
	});
});
