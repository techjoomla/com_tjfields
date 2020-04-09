var tjFieldsFileField = {
	previewMedia: function (url){

	var tjUcmMediaUrl = 'https://docs.google.com/gview?url='+url+'&embedded=true';
		SqueezeBox.open(tjUcmMediaUrl ,{handler: "iframe", size: {x: window.innerWidth-50, y: window.innerHeight-50}});
	}
}