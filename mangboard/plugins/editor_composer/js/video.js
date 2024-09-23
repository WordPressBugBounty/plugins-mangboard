function mb_resizeEditorVideoItem(){
	jQuery(".mb-video-container>iframe").each(function(){
		if(jQuery(this).attr("width")=="100%"){
			jQuery(this).css("height", Math.ceil( parseInt(jQuery(this).css("width")) * 0.625 )+"px");
		}else{
			jQuery(this).parent().css("max-width", jQuery(this).attr("width")+"px");
			jQuery(this).css("height", Math.ceil( parseInt(jQuery(this).css("width")) * parseInt(jQuery(this).attr("height")) / parseInt(jQuery(this).attr("width")) )+"px");
		}
	});
}
jQuery(document).ready(function(){
	mb_resizeEditorVideoItem();
	jQuery(window).on("resize orientationchange",mb_resizeEditorVideoItem);
});