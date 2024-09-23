function mb_insertEditorLayout(editor_id,type){	
	var insert_html		= '	';	
	var inner_html		= '';
	var outer_html		= '';
	var text_style			= '';
	var editor_target		= ".mb-editor-composer-"+editor_id;	
	var inner_style		= mb_getEditorBoxStyle(editor_id,"inner");	
	var outer_style		= mb_getEditorBoxStyle(editor_id,"outer");	
	var wrap_style		= mb_getEditorBoxStyle(editor_id,"wrap");
	
	inner_style			+= jQuery(editor_target+" .mbc_inner_style").val();
	outer_style			+= jQuery(editor_target+" .mbc_outer_style").val();
	wrap_style			+= jQuery(editor_target+" .mbc_wrap_style").val();	
	
	var inner_class	= jQuery(editor_target+" .mbc_inner_class").val();
	var outer_class	= jQuery(editor_target+" .mbc_outer_class").val();
	var wrap_class	= jQuery(editor_target+" .mbc_wrap_class").val();	
	
	if(inner_class!='') inner_class	= ' '+inner_class;
	if(outer_class!='') outer_class	= ' '+outer_class;
	if(wrap_class!='') wrap_class	= ' '+wrap_class;

	var line_count		= parseInt(jQuery(editor_target+" .mbc_editor_line_count").val());
	var max_width		= jQuery(editor_target+" .mbc_editor_max_width").val();
	var text_align		= jQuery(editor_target+" .mbc_editor_text_align").val();
	
	if(max_width!='100%'){
		wrap_style		= 'max-width:'+max_width+' !important;'+wrap_style;
	}
	if(text_align!=''){
		text_style		= ' style="text-align:'+text_align+' !important;"';
	}

	inner_html			= '<div class="mb-rb'+inner_class+'" style="'+inner_style+'"><p'+text_style+'><span>&nbsp;</span></p></div>';

	if(type=="L70-30"){		
		outer_html	+= '<div class="col-sm-70 mb-rb'+outer_class+'" style="'+outer_style+'">'+inner_html+'</div>';
		outer_html	+= '<div class="col-sm-30 mb-rb'+outer_class+'" style="'+outer_style+'">'+inner_html+'</div>';
	}else if(type=="L30-70"){		
		outer_html	+= '<div class="col-sm-30 mb-rb'+outer_class+'" style="'+outer_style+'">'+inner_html+'</div>';
		outer_html	+= '<div class="col-sm-70 mb-rb'+outer_class+'" style="'+outer_style+'">'+inner_html+'</div>';
	}else if(type=="L33-67"){		
		outer_html	+= '<div class="col-sm-4 mb-rb'+outer_class+'" style="'+outer_style+'">'+inner_html+'</div>';
		outer_html	+= '<div class="col-sm-8 mb-rb'+outer_class+'" style="'+outer_style+'">'+inner_html+'</div>';
	}else if(type=="L67-33"){
		outer_html	+= '<div class="col-sm-8 mb-rb'+outer_class+'" style="'+outer_style+'">'+inner_html+'</div>';
		outer_html	+= '<div class="col-sm-4 mb-rb'+outer_class+'" style="'+outer_style+'">'+inner_html+'</div>';
	}else if(type=="L16-84"){		
		outer_html	+= '<div class="col-sm-2 mb-rb'+outer_class+'" style="'+outer_style+'">'+inner_html+'</div>';
		outer_html	+= '<div class="col-sm-10 mb-rb'+outer_class+'" style="'+outer_style+'">'+inner_html+'</div>';
	}else if(type=="L84-16"){		
		outer_html	+= '<div class="col-sm-10 mb-rb'+outer_class+'" style="'+outer_style+'">'+inner_html+'</div>';
		outer_html	+= '<div class="col-sm-2 mb-rb'+outer_class+'" style="'+outer_style+'">'+inner_html+'</div>';
	}else if(type=="L25-75"){		
		outer_html	+= '<div class="col-sm-3 mb-rb'+outer_class+'" style="'+outer_style+'">'+inner_html+'</div>';
		outer_html	+= '<div class="col-sm-9 mb-rb'+outer_class+'" style="'+outer_style+'">'+inner_html+'</div>';
	}else if(type=="L75-25"){		
		outer_html	+= '<div class="col-sm-9 mb-rb'+outer_class+'" style="'+outer_style+'">'+inner_html+'</div>';
		outer_html	+= '<div class="col-sm-3 mb-rb'+outer_class+'" style="'+outer_style+'">'+inner_html+'</div>';
	}else if(type=="M331"){		
		outer_html	+= '<div class="col-sm-3-1 mb-rb'+outer_class+'" style="'+outer_style+'">'+inner_html+'</div>';
		outer_html	+= '<div class="col-sm-3-1 col-sm-offset-1-2 mb-rb'+outer_class+'" style="'+outer_style+'">'+inner_html+'</div>';
		outer_html	+= '<div class="col-sm-3-1 col-sm-offset-1-2 mb-rb'+outer_class+'" style="'+outer_style+'">'+inner_html+'</div>';
	//타입이 "col-" 로 시작하는 반응형 클래스 처리
	}else if(type.indexOf("col-")==0){		
		var count		= parseInt(type.substr(4,1));
		var i				= 0;
		for(i=0;i<count;i++){
			outer_html	+= '<div class="'+type+' mb-rb'+outer_class+'" style="'+outer_style+'">'+inner_html+'</div>';
		}
	}	
	
	if(line_count>1){
		var temp_html		= outer_html;
		for(i=0;i<line_count-1;i++){
			outer_html	+= temp_html;
		}
	}
	insert_html		= '<div class="responsive-list'+wrap_class+'" style="'+wrap_style+'">'+outer_html+'</div>';
	mb_insertEditorHtml(editor_id,insert_html);
}
function mb_hideEditorVideoBox(editor_id) {
	var video_target		= ".mb-editor-video-"+editor_id;	
	jQuery(video_target).hide();
	jQuery(video_target+" .mb_editor_video_url").val("");
}
function mb_insertEditorVideo(editor_id) {
	var editor_target		= ".mb-editor-composer-"+editor_id;	
	var video_target		= ".mb-editor-video-"+editor_id;	
	var vinput			= jQuery(video_target+" .mb_editor_video_url").val();
	if(typeof(vinput)=='undefined' || vinput==""){ 
		alert(jQuery(video_target+" input[name=video_input_error1]").val());
		return; 
	}
	var vhtml			= "";
	var wrap_style	= "";
	var width			= "100%";
	var temp_width	= "";
	var height		= parseInt(jQuery(video_target).width() * 0.625);	

	
	var max_width	= jQuery(editor_target+" .mbc_editor_max_width").val();
	var text_align		= jQuery(editor_target+" .mbc_editor_text_align").val();
	
	if(max_width!='100%'){
		wrap_style		= 'max-width:'+max_width+' !important;';

		if(String(max_width).indexOf("%")!=-1){
			temp_width		= parseInt(max_width.replace("%",""));
			height				= parseInt(height * temp_width / 100);
		}else if(String(max_width).indexOf("px")!=-1){
			temp_width		= parseInt(max_width.replace("px",""));
			height				= parseInt(temp_width * 0.625);
		}else{
			height				= parseInt(parseInt(max_width) * 0.625);
		}
	}
	if(text_align!=''){
		if(text_align=="center"){
			wrap_style		+= 'margin:0 auto;';
		}else if(text_align=="right"){
			wrap_style		+= 'margin-left:auto';
		}
	}	
	var cinput		= String(vinput).toLowerCase();
	if(cinput.indexOf("http")===0){
		if(cinput.indexOf(".png")!=-1 || cinput.indexOf(".jpg")!=-1 || cinput.indexOf(".jpeg")!=-1 || cinput.indexOf(".gif")!=-1){
			vhtml			= '<img src="'+vinput+'">';
		}else{
			var add_attribute		= ' allow="autoplay"';
			if(cinput.indexOf("youtube.com")!=-1){
				vinput				= vinput.replace("youtube.com/watch?v=","youtube.com/embed/");
			}else if(cinput.indexOf("youtu.be")!=-1){
				vinput				= vinput.replace("youtu.be","youtube.com/embed/");
			}else if(cinput.indexOf("vimeo.com")!=-1){
				if(cinput.indexOf("player.vimeo.com/video")==-1){
					vinput				= vinput.replace("vimeo.com","player.vimeo.com/video");
				}
			}else if(cinput.indexOf("www.twitch.tv")!=-1){
				if(cinput.indexOf("www.twitch.tv/videos/")!=-1){
					vinput				= vinput.replace("www.twitch.tv/videos/","player.twitch.tv/?video=")+"&parent="+encodeURIComponent(window.location.hostname);
				}
			}else if(cinput.indexOf("tv.kakao.com")!=-1){
				if(cinput.indexOf("tv.kakao.com/v")!=-1){
					vinput				= vinput.replace("tv.kakao.com/v","tv.kakao.com/embed/player/cliplink")+"?service=kakao_tv";
				}
			}else if(cinput.indexOf("tv.naver.com")!=-1){
				if(cinput.indexOf("tv.naver.com/v")!=-1){
					vinput				= vinput.replace("tv.naver.com/v","tv.naver.com/embed");
				}
			}else if(cinput.indexOf("www.dailymotion.com")!=-1){
				if(cinput.indexOf("www.dailymotion.com/video")!=-1){
					vinput				= vinput.replace("www.dailymotion.com/video","www.dailymotion.com/embed/video");
				}
			}else{
				if(vinput.indexOf("autoplay=1")==-1){
					add_attribute		= ' sandbox=""';
				}
			}
			if(vinput.indexOf("?")==-1){
				if(vinput.indexOf("&autoplay=1")!=-1){
					vinput		= vinput.replace("&autoplay=1","?autoplay=1");
				}
			}
			if(vinput.indexOf("&")!=-1){
				vinput			= vinput.substring(0,vinput.indexOf("&"));
			}
			vhtml			= '<iframe width="'+width+'" height="'+height+'px" src="'+vinput+'" frameborder="0" scrolling="no" marginwidth="0" marginheight="0"'+add_attribute+' allowfullscreen=""></iframe>';
		}
	}else{
		vhtml			= vinput;
	}
	if(vhtml.indexOf("iframe")!=-1){
		vhtml		= '<div class="mb-video-container" style="'+wrap_style+'">'+vhtml+'</div>';
	}else{
		vhtml		= vhtml;
	}
	mb_insertEditorHtml(editor_id,vhtml);
	mb_hideEditorVideoBox(editor_id);
}

function mb_insertEditorHtml(editor_id,insert_html){
	var editor_type		= jQuery('#editor_type').val();
	if(editor_type=='S' || editor_type=='HS' || editor_type=='HS2' || editor_type=='HS3'){
		if(typeof(oEditors)!=='undefined'){
			oEditors.getById[editor_id].exec("PASTE_HTML", [insert_html]);
		}
	}else if(editor_type=='C'){
		if(typeof(ckeditor)!=='undefined'){
			//ckeditor.insertHtml(insert_html);
			CKEDITOR.instances[editor_id].insertHtml(insert_html);
		}
	}else if(editor_type=='W'){
		if(typeof(tinyMCE)!=='undefined' && typeof(tinyMCE.activeEditor)!=='undefined'){
			tinyMCE.get(editor_id).execCommand('mceInsertRawHTML', false, insert_html);
			tinyMCE.get(editor_id).focus();
		}
	}
}

jQuery(document).ready(function() {	
	if(jQuery(".mb-editor-composer").length>0){
		jQuery(".mb-editor-composer").each(function( index ) {			
			var editor_id			= jQuery(this).find("input[name=mb-editor-composer-id]").val();
			var editor_target		= ".mb-editor-composer-"+editor_id;	
			
			jQuery(editor_target).closest('td').css('overflow', 'visible');
			jQuery(editor_target+" .mbc-colorpicker>input").colorpicker({ color: "#dddddd", hideButton: true});
			jQuery(editor_target+" .mbc-colorpicker2>input").colorpicker({ color: "#ffffff", hideButton: true});

			jQuery(editor_target+" .mbc_inner_border_width").val(1);
			jQuery(editor_target+" .mbc_inner_padding").val(7);
			jQuery(editor_target+" .mbc_outer_padding").val(7);
			jQuery(editor_target+" .mbc_wrap_padding").val(1);

			jQuery(editor_target+" .mbc_wrap_border_width,"+editor_target+" .mbc_wrap_padding").change(function(){
				mb_setBorderStyleValue(editor_id,"wrap");
			});
			jQuery(editor_target+" .mbc_wrap_border_radius,"+editor_target+" .mbc_wrap_border_width,"+editor_target+" .mbc_wrap_border_color,"+editor_target+" .mbc_wrap_border_style,"+editor_target+" .mbc_wrap_background_color").change(function(){
				mb_previewBorderStyle(editor_id,"wrap");	
			});
			jQuery(editor_target+" .mbc_outer_border_width,"+editor_target+" .mbc_outer_padding").change(function(){
				mb_setBorderStyleValue(editor_id,"outer");
			});
			jQuery(editor_target+" .mbc_outer_border_radius,"+editor_target+" .mbc_outer_border_width,"+editor_target+" .mbc_outer_border_color,"+editor_target+" .mbc_outer_border_style,"+editor_target+" .mbc_outer_background_color").change(function(){
				mb_previewBorderStyle(editor_id,"outer");	
			});
			jQuery(editor_target+" .mbc_inner_border_width,"+editor_target+" .mbc_inner_padding").change(function(){
				mb_setBorderStyleValue(editor_id,"inner");
			});
			jQuery(editor_target+" .mbc_inner_border_radius,"+editor_target+" .mbc_inner_border_width,"+editor_target+" .mbc_inner_border_color,"+editor_target+" .mbc_inner_border_style,"+editor_target+" .mbc_inner_background_color").change(function(){
				mb_previewBorderStyle(editor_id,"inner");	
			});

			mb_previewBorderStyle(editor_id,"wrap");
			mb_previewBorderStyle(editor_id,"outer");
			mb_previewBorderStyle(editor_id,"inner");

			mb_setBorderStyleValue(editor_id,"wrap");
			mb_setBorderStyleValue(editor_id,"outer");
			mb_setBorderStyleValue(editor_id,"inner");

			jQuery(editor_target).show();
			var nWidth	= jQuery(editor_target).outerWidth();
			if(nWidth<740){
				jQuery(editor_target).addClass('mb-editor-composer-narrow1');
			}
		});		
	}
});
function mb_setEditorTabs(editor_id,tab_name,idx,tab_class){
	var editor_target		= ".mb-editor-composer-"+editor_id;	
	jQuery(editor_target+" ."+tab_name).children('.mp-editor-tabs-selected').removeClass('mp-editor-tabs-selected');
	jQuery(editor_target+" ."+tab_name).children("div").eq(idx-1).addClass("mp-editor-tabs-selected");
	jQuery(editor_target+" ."+tab_class).hide();
	jQuery(editor_target+" ."+tab_class+idx).show();
}

function mb_getEditorBoxStyle(editor_id,box){	
	var editor_target		= ".mb-editor-composer-"+editor_id;	
	var boxStyle		= '';
	if( box == "inner" || box == "outer" || box == "wrap" ){
		if(jQuery(editor_target+" .mb-"+box+"-box .mb-editor-border-top-width").val()!="0") boxStyle	+='border-top:'+jQuery(editor_target+" .mb-"+box+"-box .mb-editor-border-top-width").val()+'px '+jQuery(editor_target+" .mbc_"+box+"_border_style").val()+' '+jQuery(editor_target+" .mbc_"+box+"_border_color").val()+';';
		if(jQuery(editor_target+" .mb-"+box+"-box .mb-editor-border-right-width").val()!="0") boxStyle	+='border-right:'+jQuery(editor_target+" .mb-"+box+"-box .mb-editor-border-right-width").val()+'px '+jQuery(editor_target+" .mbc_"+box+"_border_style").val()+' '+jQuery(editor_target+" .mbc_"+box+"_border_color").val()+';';
		if(jQuery(editor_target+" .mb-"+box+"-box .mb-editor-border-bottom-width").val()!="0") boxStyle	+='border-bottom:'+jQuery(editor_target+" .mb-"+box+"-box .mb-editor-border-bottom-width").val()+'px '+jQuery(editor_target+" .mbc_"+box+"_border_style").val()+' '+jQuery(editor_target+" .mbc_"+box+"_border_color").val()+';';
		if(jQuery(editor_target+" .mb-"+box+"-box .mb-editor-border-left-width").val()!="0") boxStyle	+='border-left:'+jQuery(editor_target+" .mb-"+box+"-box .mb-editor-border-left-width").val()+'px '+jQuery(editor_target+" .mbc_"+box+"_border_style").val()+' '+jQuery(editor_target+" .mbc_"+box+"_border_color").val()+';';
		if(jQuery(editor_target+" .mbc_"+box+"_border_radius").val()!="0") {
			var border_radius		= jQuery(editor_target+" .mbc_"+box+"_border_radius").val();
			boxStyle	+='border-radius:'+border_radius+'px;-webkit-border-radius:'+border_radius+'px;-moz-border-radius:'+border_radius+'px;-khtml-border-radius:'+border_radius+'px;';
		}
		if(jQuery(editor_target+" .mbc_"+box+"_background_color").val()!=''){
			boxStyle	+='background-color:'+jQuery(editor_target+" .mbc_"+box+"_background_color").val()+';';
		}
		boxStyle	+='padding:'+jQuery(editor_target+" .mb-"+box+"-box .mb-editor-padding-top").val()+'px '+jQuery(editor_target+" .mb-"+box+"-box .mb-editor-padding-right").val()+'px '+jQuery(editor_target+" .mb-"+box+"-box .mb-editor-padding-bottom").val()+'px '+jQuery(editor_target+" .mb-"+box+"-box .mb-editor-padding-left").val()+'px;'
		return boxStyle;
	}
}
function mb_setBorderStyleValue(editor_id,box){
	var editor_target		= ".mb-editor-composer-"+editor_id;	
	if( box == "inner" || box == "outer" || box == "wrap" ){
		jQuery(editor_target+" .mb-"+box+"-box .mb-editor-border>input").val(jQuery(editor_target+" .mbc_"+box+"_border_width").val());
		jQuery(editor_target+" .mb-"+box+"-box .mb-editor-padding>input").val(jQuery(editor_target+" .mbc_"+box+"_padding").val());
	}
}
function mb_previewBorderStyle(editor_id,box){
	var editor_target		= ".mb-editor-composer-"+editor_id;	
	if( box == "inner" || box == "outer" || box == "wrap" ){
		var border_width	= parseInt(jQuery(editor_target+" .mbc_"+box+"_border_width").val());
		if(border_width>10) border_width		= 10;

		var style1			= 'border: '+border_width+'px '+jQuery(editor_target+" .mbc_"+box+"_border_style").val()+' '+jQuery(editor_target+" .mbc_"+box+"_border_color").val()+';border-radius:'+jQuery(editor_target+" .mbc_"+box+"_border_radius").val()+'px;';
		var style2			= 'background-color:'+jQuery(editor_target+" .mbc_"+box+"_background_color").val();
		var style				= '';
		if(border_width==0) style	= style2;
		else style	= style1+style2;
		
		jQuery(editor_target+" .mb-"+box+"-box .mb-editor-padding").attr('style',style);
		
		if(box=="inner"){			
			jQuery(editor_target+" .mb-outer-box .mb-editor-board-style2").attr('style',style);
			jQuery(editor_target+" .mb-wrap-box .mb-editor-board-style1").attr('style',style);
		}else if(box=="outer"){
			jQuery(editor_target+" .mb-inner-box .mb-editor-board-style4").attr('style',style);
			jQuery(editor_target+" .mb-wrap-box .mb-editor-board-style2").attr('style',style);
		}else if(box=="wrap"){
			jQuery(editor_target+" .mb-inner-box .mb-editor-board-style5").attr('style',style);
			jQuery(editor_target+" .mb-outer-box .mb-editor-board-style4").attr('style',style);
		}		
	}
}
function mb_showEditorAttrBox(editor_id){
	var editor_target		= ".mb-editor-composer-"+editor_id;	
	jQuery(editor_target+' .mb-editor-attr-box').slideDown(300, function() {
		jQuery(editor_target).addClass('mb-editor-composer-open');	
		jQuery(editor_target+' .mb-editor-hide-btn').hide();
		jQuery(editor_target+' .mb-editor-show-btn').show();
	});
}
function mb_hideEditorAttrBox(editor_id){
	var editor_target		= ".mb-editor-composer-"+editor_id;	
	jQuery(editor_target+' .mb-editor-attr-box').slideUp(200);
	jQuery(editor_target+' .mb-editor-hide-btn').show();
	jQuery(editor_target+' .mb-editor-show-btn').hide();
	jQuery(editor_target).removeClass('mb-editor-composer-open');	
}