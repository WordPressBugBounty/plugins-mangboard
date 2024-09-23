jQuery( document ).ready(function() {
	//팝업 태그를 body에 추가
	var popup_html	= "";
	popup_html		= popup_html+'<div class="mb-board">';
	popup_html		= popup_html+'<div class="pop-bg" id="mb_pop_bg" onclick="hidePopupAlert()"></div>';
	popup_html		= popup_html+'<div class="pop-main mb-'+mb_options["device_type"]+'" >';
		popup_html		= popup_html+'<div class="pop-html" id="mb_pop_html">';
			popup_html		= popup_html+'<div id="mb_pop_html_head"></div>';
			popup_html		= popup_html+'<div id="mb_pop_html_body"></div>';
		popup_html		= popup_html+'</div>';
		popup_html		= popup_html+'<div class="mb-pop pop-info" id="mb_pop_info">';
			popup_html		= popup_html+'<div id="mb_pop_info_head"></div>';
			popup_html		= popup_html+'<div id="mb_pop_info_body"></div>';
		popup_html		= popup_html+'</div>';
		popup_html		= popup_html+'<div class="mb-pop pop-confirm" id="mb_pop_confirm">';
			popup_html		= popup_html+'<div class="pop-confirm-head" id="mb_pop_confirm_head"></div>';
			popup_html		= popup_html+'<div class="pop-confirm-body" id="mb_pop_confirm_body"></div>';
		popup_html		= popup_html+'</div>';
		popup_html		= popup_html+'<div class="mb-pop pop-alert" id="mb_pop_alert">';
			popup_html		= popup_html+'<div class="pop-alert-head" id="mb_pop_alert_head"></div>';
			popup_html		= popup_html+'<div class="pop-alert-body" id="mb_pop_alert_body"></div>';
		popup_html		= popup_html+'</div>';
		
	popup_html		= popup_html+'</div>';
	popup_html		= popup_html+'</div>';

     jQuery("body").append(popup_html);
});

var confirmCallbackSend;
var confirmCallbackCancel;
var alertCallbackSend;
var popupCallbackData;
var select_menu		= "";
var eventX				= 0;
var eventY				= 0;
var scrollMode			= true;
var popupMode			= "popup";

function setPopupScrollEvent(){
	jQuery(window).on({
		'mousewheel': function(e){
			if(scrollMode) setPopupPosition("scroll");
		},'scroll': function(e){
			if(scrollMode) setPopupPosition("scroll");
		}
	})
}
function setPopupPosition(mode){
	if(typeof(mode)!=='undefined' && mode=="scroll" && jQuery("#mb_pop_bg").css('display')=='none') return;

	var pHeight			= 120;
	var nWidth			= jQuery(window).width();
	var nHeight			= jQuery(window).height();
	var scrollTop			= jQuery(document).scrollTop();
	
	var popupObj			= jQuery("#mb_pop_confirm");
	if(mb_options["device_type"]=="mobile") pHeight				= 50;

	popupObj.css('left',(nWidth-popupObj.outerWidth())/2);
	if(popupObj.outerHeight()>400){ pHeight			= 30; }
	popupObj.css('top',(scrollTop-pHeight+((nHeight-popupObj.outerHeight())/2)));

	popupObj		= jQuery("#mb_pop_alert");
	popupObj.css('left',(nWidth-popupObj.outerWidth())/2);
	if(popupObj.outerHeight()>400){ pHeight			= 30; }
	popupObj.css('top',(scrollTop-pHeight+((nHeight-popupObj.outerHeight())/2)));

	popupObj		= jQuery("#mb_pop_info");
	popupObj.css('left',(nWidth-popupObj.outerWidth())/2);
	popupObj.css('top',((scrollTop+((nHeight-popupObj.outerHeight())/2))));

	popupObj		= jQuery("#mb_pop_html");
	if(popupObj.outerHeight()>600){ 
		pHeight			= 10;
	}else if(popupObj.outerHeight()>400){ 
		pHeight			= 20;
	}
	var nTop			= (scrollTop-pHeight+((nHeight-popupObj.outerHeight())/2));
	if(nTop<scrollTop){
		if((pHeight+nTop)<scrollTop){
			nTop	= scrollTop;
		}else{
			nTop	= parseInt((pHeight+nTop)/2);
		}
	}
	if(nTop<scrollTop){ nTop	= scrollTop; }
	if(nTop<0){ nTop = 0; }

	if(popupObj.outerHeight()>=nHeight){
		popupObj.css('position','fixed');
		if(popupObj.outerHeight()>=nHeight){
			popupObj.css('top',0);
		}else{
			popupObj.css('top',nTop);
		}
		if(mb_options["device_type"]=="mobile") popupObj.css('width',"100%");
		popupObj.css('height',"100%");
		if(popupObj.outerWidth()>=nWidth){
			popupObj.css('left',0);
		}else{
			popupObj.css('left',(nWidth-popupObj.outerWidth())/2);
		}
		scrollMode		= false;
		jQuery('body').css('overflow-y','hidden');
	}else{
		popupObj.css('position','absolute');
		popupObj.css('top',nTop);
		if(popupObj.outerWidth()>=nWidth){
			popupObj.css('left',0);
		}else{
			popupObj.css('left',(nWidth-popupObj.outerWidth())/2);
		}
	}
}
setPopupScrollEvent();

function showConfirmPopup(message,data,send,cancel){
	popupCallbackData				= data;
	confirmCallbackSend			= send;

	if(typeof(cancel)==='undefined') confirmCallbackCancel = hidePopupBox;
	else confirmCallbackCancel		= cancel;

	//위치 설정
	var pop_head		= '';
	var pop_body		= '';
	var pop_btn_ok		= mb_languages["btn_ok"];
	var pop_btn_cancel	= mb_languages["btn_cancel"];
	if(typeof(data)!=='undefined'){
		if(typeof(data.btn_ok)!=='undefined' && data.btn_ok!=""){
			pop_btn_ok		= data.btn_ok;
		}
		if(typeof(data.btn_cancel)!=='undefined' && data.btn_cancel!=""){
			pop_btn_cancel		= data.btn_cancel;
		}
	}
	if(typeof(data)!=='undefined' && typeof(data.type)!=='undefined' && data.type=="passwd"){
		pop_head		= '<div class="pop-title pop-title-confirm" id="mb_pop_confirm_text">'+message+'</div>';
		pop_body			= pop_body+'<div id="mb_pop_form">';
			pop_body			= pop_body+'<form class="pop-form" action="javascript:sendConfirmCallbackData()" id="mb_confirm_form" method="post" name="mb_confirm_form"><div><input id="mb_confirm_passwd" name="passwd" class="pop-input-passwd" type="password" /></div></form>';

		pop_body			= pop_body+'</div>';		
	}else{
		pop_head		= '<div class="pop-title" id="mb_pop_confirm_text"><div class="pop-title-head">Message</div><div class="pop-title-message">'+message+'</div></div>';
	}
	pop_body			= pop_body+'<div class="btn-box-center">';
	pop_body			= pop_body+'<a href="javascript:;" id="mb_pop_confirm_cancel" onclick="cancelConfirmCallbackData()" class="btn btn-default btn-cancel margin-right-10"><span>'+pop_btn_cancel+'</span></a>';
	pop_body			= pop_body+'<a href="javascript:;" id="mb_pop_confirm_ok" onclick="sendConfirmCallbackData()" class="btn btn-default btn-ok"><span>'+pop_btn_ok+'</span></a>';
	pop_body			= pop_body+'</div>';

	if(typeof(data)!=='undefined'){
		if(typeof(data.head)!=='undefined')  pop_head		= data.head;
		if(typeof(data.body)!=='undefined')  pop_body		= data.body;
	}

	showPopupBox("Confirm",pop_head,pop_body);
}
function showAlertPopup(response,send){
	if(!response || typeof(response)==='undefined') return;

	var code					= "";
	var message				= "";
	var target_name			= "";
	var mode					= "";
	var board_action		= "";
	
	if(typeof(send)==='undefined') alertCallbackSend			= null;
	else alertCallbackSend			= send;
	
	if(typeof(response.code)!=='undefined') code				= response.code;
	if(typeof(response.message)!=='undefined') message		= response.message;
	if(typeof(response.target_name)!=='undefined') target_name		= response.target_name;
	if(typeof(response.mode)!=='undefined') mode				= response.mode;
	if(typeof(response.board_action)!=='undefined') board_action				= response.board_action;
	if(message=="") return;
	if(code=="1103"){
		showConfirmPopup(message, {}, function(){moveURL(mb_urls["login"]);});
		return;
	}
	var pop_head		= '<div class="pop-title" id="mb_pop_alert_text"><div class="pop-title-head">Message</div><div class="pop-title-message">'+message+'</div></div>';
	var pop_body		= '';
	pop_body			= pop_body+'<div class="btn-box-center">';
	pop_body			= pop_body+'<a href="javascript:;"  id="mb_pop_alert_ok" onclick="showAlertCallback(\''+code+'\',\''+target_name+'\',\''+mode+'\',\''+board_action+'\');" class="btn btn-default btn-ok"><span>'+mb_languages["btn_ok"]+'</span></a>';
	pop_body			= pop_body+'</div>';
	showPopupBox("Alert",pop_head,pop_body);
}
function showPopupBox(type,head,body,data,mode){
	if(typeof(mode)!=='undefined' && mode!=''){
		popupMode			= mode;
	}else{
		popupMode			= "popup";
	}

	scrollMode			= true;
	initLoadingBox();
	var popupID		= "#mb_pop_confirm";	

	if(type=="Alert"){
		popupID		= "#mb_pop_alert";
		if(jQuery("#mb_pop_bg").css('display')=='none') jQuery("#mb_pop_confirm").hide();	

		setTimeout(function(){ jQuery(popupID).addClass("mb-ani-pop-open");	 }, 1);		
	}else if(type=="Info"){
		popupID		= "#mb_pop_info";
		setTimeout(function(){ jQuery(popupID).addClass("mb-ani-pop-fadein"); }, 1);
	}else if(type=="Confirm"){
		popupID		= "#mb_pop_confirm";
		setTimeout(function(){ jQuery(popupID).addClass("mb-ani-pop-open");	 }, 1);		
	}else if(type=="Html"){
		popupID		= "#mb_pop_html";
	}else{
		popupID		= "#mb_pop_alert";
		setTimeout(function(){ jQuery(popupID).addClass("mb-ani-pop-open");	 }, 1);		
	}
	if(typeof(data)==='undefined') data		= {};
	if(typeof(data.position)==='undefined') data.position = "center";
	if(typeof(data.bg)==='undefined') data.bg = true;
	if(typeof(data.eventX)==='undefined') data.eventX = 0;
	if(typeof(data.eventY)==='undefined') data.eventY = 0;
	
	jQuery(popupID+"_head").html(head);
	jQuery(popupID+"_body").html(body);	

	if(data.position=="click"){
		jQuery(popupID).css("top",parseInt((data.eventY+jQuery(document).scrollTop())/10)*10-25);
		jQuery(popupID).css("left",parseInt(data.eventX/10)*10+28);
	}else{
		setPopupPosition();
	}
	if(data.bg) showPopupBG();
	
	jQuery(popupID).show();	

	if(type=="Alert"){
		jQuery("#mb_pop_alert_ok").focus();
	}else if(type=="Confirm"){
		jQuery("#mb_confirm_passwd").focus();
	}
	
}

function initLoadingBox(){
	jQuery("#mb_pop_html_head").html("");
	jQuery("#mb_pop_html_body").html("");
	jQuery("#mb_pop_html").css({'width':'','height':''});
	jQuery("#mb_pop_html").hide();
}

function showLoadingBox(){
	showPopupBox("Html",'<img src="'+mb_urls["plugin"]+'assets/images/loader.gif" width="50px" height="50px" style="opacity: 0.7;filter: alpha(opacity=0.7);">','<div style="color:#fff;font-size:12px;min-height:19px;">Loading</div>');
}
function hideLoadingBox(){
	initLoadingBox();
	if(jQuery("#mb_pop_alert").css('display')=='none' && jQuery("#mb_pop_confirm").css('display')=='none'){
		jQuery("#mb_pop_bg").hide();
	}
	if(!scrollMode){
		jQuery('body').css('overflow-y','auto');
		scrollMode		= true;
	}
}

function showPopupBG(){
	hideInfoBox();
	jQuery('#mb_pop_bg').height(jQuery(document).height());	
	jQuery("#mb_pop_bg").show();
}

var popup_user_name	= "";
function getUserMenu(uid,pid,name){
	if(typeof(name)!=='undefined' && name!=""){
		popup_user_name		= name;
	}else{
		popup_user_name		= "";
	}
	var menu_options		= "*";	
	if(select_menu==uid+"_"+pid){
		select_menu		= "";
		hideInfoBox();
	}else{		
		sendUserMenu(uid,pid,menu_options,event);
		select_menu		= uid+"_"+pid;
	}
}
function showUserMenu(data){		
	var menu_head		= data["head"];
	var menu_body		= "";
	var menu_foot		= data["foot"];

	var user_name		= "";
	if(typeof(data["user"]["name"])!=='undefined' && data["user"]["name"]!=""){
		user_name		= data["user"]["name"];
	}else if(popup_user_name!=""){
		user_name		= popup_user_name;
	}	
	if(user_name!="") menu_head				= menu_head+'<div class="pop-info-head">'+user_name+'</div>';

	menu_body			= '<ul class="pop-info-body list-unstyled">';	

	if(data["body"].length>0){
		jQuery.each(data["body"], function(key,value){
			menu_body		= menu_body+'<li>'+value+'</li>';
		});
		menu_body		= menu_body+'</ul>';
		showPopupBox("Info","",('<div class="pop-user-menu">'+menu_head+menu_body+menu_foot+"</div>"),{"position":"click","bg":false,"eventX":eventX,"eventY":eventY});
	}
}

function sendUserMenu(uid,board_pid,option,event){
	if(typeof(event)!=='undefined'){
		eventX	= event.clientX;
		eventY		= event.clientY - jQuery('body').offset().top;
	}
	if(option=="*") option	= "search,info,email,homepage,blog";

	var param	= "board_name="+mb_options["board_name"]+"&option="+option+"&list_type="+mb_options["list_type"]+"&page="+mb_options["page"]+"&page_id="+mb_options["page_id"];
	if(jQuery('#'+mb_options["board_name"]+'_form_board_search').length>0) param		+= "&"+jQuery('#'+mb_options["board_name"]+'_form_board_search').serialize();
	param	+= "&mode=user&board_action=menu"+"&pid="+uid+"&board_pid="+board_pid;

	sendDataRequest2(mb_urls["template_api"], param, sendUserMenuHandler);
}
function sendUserMenuHandler(response, state){		
	if(response.state == "success"){
		showUserMenu(response.data);
	}else{
		showAlertPopup(response);
	}
}
function hideInfoBox() {
	jQuery("#mb_pop_info").hide();
	jQuery("#mb_pop_info").removeClass("mb-ani-pop-fadein");
}
function hidePopupBox() {	
	confirmCallbackSend		= null;
	jQuery(".mb-input-focus").removeClass("mb-input-focus");
	jQuery("#mb_pop_info").hide();
	jQuery("#mb_pop_html").hide();	
	jQuery("#mb_pop_confirm").hide();	
	jQuery("#mb_pop_confirm").removeClass("mb-ani-pop-open");
	jQuery("#mb_pop_alert").removeClass("mb-ani-pop-open");
	jQuery("#mb_pop_info").removeClass("mb-ani-pop-fadein");
	jQuery("#mb_pop_bg").hide();
	if(!scrollMode){
		jQuery('body').css('overflow-y','auto');
		scrollMode		= true;
	}
	popupMode			= "popup";
}
function showAlertCallback(code,target_name,mode,board_action){	
	hidePopupAlert(code,target_name,mode,board_action);
	sendAlertCallbackData();	
}
function hidePopupAlert(code,target_name,mode,board_action) {
	if(popupMode=="modal") return;
	if(typeof(code)!=='undefined' && code!=='undefined' && code!=''){
		if(confirmCallbackSend) jQuery("#mb_pop_confirm").show();		

		if(code.substr(0,2)=="12"){	//입력 관련 에러일 경우 포커스 설정
			if(typeof(target_name)!=='undefined'  && target_name!=='undefined' && target_name!=''){
				var target;
				if(jQuery("#mb_pop_confirm").css('display')!='none'){
					if(target_name.indexOf(" ") !== -1){		//이름에 공백이 있을 경우
						if(jQuery("#mb_confirm_form").find(target_name).length>0){
							target		= jQuery("#mb_confirm_form").find(target_name).first();
						}
					}else{
						if(jQuery("#mb_confirm_form").find("input[name='"+target_name+"']").length>0){
							target		= jQuery("#mb_confirm_form").find("input[name='"+target_name+"']").first();
						}else if(jQuery("#mb_confirm_form").find(target_name).length>0){
							target		= jQuery("#mb_confirm_form").find(target_name).first();
						}
					}					
				}else{
					if(code=="1299"){
						if(jQuery(target_name).filter(':visible').length>0){
							target		= jQuery(target_name).filter(':visible').first();
						}
					}else{
						if(target_name.indexOf(" ") !== -1){		//이름에 공백이 있을 경우
							if(jQuery(target_name).filter(':visible').length>0){
								target		= jQuery(target_name).filter(':visible').first();
							}
						}else{
							if(jQuery("[name='"+target_name+"']").filter(':visible').length>0){
								if(typeof(mode)!=='undefined' && mode=='comment' && typeof(board_action)!=='undefined' && (board_action=='reply' || board_action=='modify')){
									target		= jQuery(".cmt-reply-box [name='"+target_name+"']").filter(':visible').first();
								}else{
									target		= jQuery("[name='"+target_name+"']").filter(':visible').first();
								}
							}else if(jQuery(target_name).filter(':visible').length>0){
								target		= jQuery(target_name).filter(':visible').first();
							}
						}						
					}
				}
				jQuery(".mb-input-focus").removeClass("mb-input-focus");
				if(typeof(target)!=='undefined'  && target!=='undefined'){
					jQuery("html, body").animate({scrollTop: target.offset().top-200}, 300, function(){
						target.focus();
						target.addClass("mb-input-focus");
					});
				}
			}
		}
	}else{
		jQuery(".mb-input-focus").focus();
	}
	
	jQuery("#mb_pop_alert").hide();
	jQuery("#mb_pop_alert").removeClass("mb-ani-pop-open");
	jQuery("#mb_pop_html").hide();
	jQuery("#mb_pop_info").hide();
	if(jQuery("#mb_pop_confirm").css('display')=='none'){
		jQuery("#mb_pop_bg").hide();
		initLoadingBox();
	}
	if(!scrollMode){
		jQuery('body').css('overflow-y','auto');
		scrollMode		= true;
	}
	popupMode			= "popup";
}
function sendAlertCallbackData() {	
	if(alertCallbackSend) alertCallbackSend();
	else alertCallbackSend		= null;
}
function sendConfirmCallbackData() {	
	showLoadingBox();
	jQuery("#mb_pop_confirm").hide();
	if(confirmCallbackSend) confirmCallbackSend(popupCallbackData);
	else confirmCallbackSend		= null;
}
function cancelConfirmCallbackData() {	
	confirmCallbackSend		= null;
	if(confirmCallbackCancel) confirmCallbackCancel();
	else confirmCallbackCancel		= null;
}