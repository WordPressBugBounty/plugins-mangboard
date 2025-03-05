function mb_resizeEditorVideoItem(){
	if(jQuery(".mb-video-container").length>0){
		jQuery(".mb-video-container>iframe").each(function(){
			if(jQuery(this).attr("width")=="100%"){
				if(jQuery(this).width()!=0){
					jQuery(this).css("height", Math.ceil( parseInt(jQuery(this).width()) * 0.625 )+"px");
				}
			}else if(jQuery(this).attr("height")=="100%"){
			}else{
				if(jQuery(this).width()!=0){
					jQuery(this).parent().css("max-width", jQuery(this).attr("width")+"px");
					jQuery(this).css("height", Math.ceil( parseInt(jQuery(this).width()) * parseInt(jQuery(this).attr("height")) / parseInt(jQuery(this).attr("width")) )+"px");
				}
			}
		});
	}
}
jQuery(document).ready(function(){
	setTimeout(function(){
		mb_resizeEditorVideoItem();
	}, 100);
	jQuery(window).on("resize orientationchange",mb_resizeEditorVideoItem);
});