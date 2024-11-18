function mb_resizeEditorVideoItem(){
	if(jQuery(".mb-video-container").length>0){
		jQuery(".mb-video-container>iframe").each(function(){
			if(jQuery(this).attr("width")=="100%"){
				jQuery(this).css("height", Math.ceil( parseInt(jQuery(this).css("width")) * 0.625 )+"px");
			}else if(jQuery(this).attr("height")=="100%"){
			}else{
				jQuery(this).parent().css("max-width", jQuery(this).attr("width")+"px");
				jQuery(this).css("height", Math.ceil( parseInt(jQuery(this).css("width")) * parseInt(jQuery(this).attr("height")) / parseInt(jQuery(this).attr("width")) )+"px");
			}
		});
	}
}
jQuery(document).ready(function(){
	mb_resizeEditorVideoItem();
	jQuery(window).on("resize orientationchange",mb_resizeEditorVideoItem);
});