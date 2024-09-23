jQuery(document).on('click', '.mb-upload-wp-media', function() {
	event.preventDefault();
	var input_id				= jQuery(this).closest('.mb-upload-media-wrap').find('input').attr('id');
	var btnContent		= '';
	if ( window.wp && wp.media ) {
		window.mb_media_frame = window.mb_media_frame || new wp.media.view.MediaFrame.Select({title: jQuery(this).attr('title'),button: {text: "선택하기"}, multiple: false});
		window.mb_media_frame.on('select', function() {
			var attachment	= window.mb_media_frame.state().get('selection').first();
			var img_url			= attachment.attributes.url;
			var mime			= attachment.attributes.mime;
			var regex			= /^image\/(?:jpe?g|png|gif|x-icon)$/i;

			if ( mime.match(regex) ) {
				jQuery('#'+input_id+"_image").attr("src",img_url);
				jQuery('#'+input_id+"_image").parent().show();
			}
			jQuery('#'+input_id).val(img_url);
			window.mb_media_frame.off('select');
		}).open();
	} 
	return false;
});
