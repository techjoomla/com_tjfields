var tjFieldsFileField = {
	previewMedia: function (tjUcmMediaUrl){
		SqueezeBox.open(tjUcmMediaUrl ,{handler: "iframe", size: {x: window.innerWidth-50, y: window.innerHeight-50}});
	}
}
