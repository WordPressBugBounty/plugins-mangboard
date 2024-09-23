function sendFormDataRequest(form, action, successCallback, errorCallback, type, dataType){
	if(typeof(action)==='undefined' || action=='') action = 'mb_board';
	if(typeof(type)==='undefined') type = "POST";
	if(typeof(dataType)==='undefined') dataType = "json";
	if(typeof(successCallback)==='undefined') successCallback	= function s(a,b){};
	if(typeof(errorCallback)==='undefined') errorCallback	= function e(a){};

	if(String(action).indexOf('http')!==0){
		action	= mb_ajax_object.ajax_url+"?action="+action+"&admin_page="+mb_ajax_object.admin_page+"&hybrid_app="+mb_hybrid_app;
	}
	form.attr("action", action);
	form.ajaxForm({
		type: type,
		async: true,
		crossDomain: true,
		dataType : dataType,
		xhrFields:{withCredentials:true},
		success:function(data, state){
			hideLoadingBox();
			successCallback(data, state);
		},error:function(e){
			//console.log(JSON.stringify(e));
			hideLoadingBox();
			if(e.responseJSON && e.responseJSON.state=="success"){
				successCallback(e.responseJSON, e.responseJSON.state);
			}else{
				errorCallback(e);
			}
		}
	});
	form.submit();
	showLoadingBox();
}

function sendDataRequest(action, param, successCallback, errorCallback, type, dataType){
	if(typeof(action)==='undefined' || action=='') action = 'mb_template';
	if(typeof(type)==='undefined') type = "POST";
	if(typeof(dataType)==='undefined') dataType = "json";
	if(typeof(successCallback)==='undefined') successCallback	= function s(a,b){};
	if(typeof(errorCallback)==='undefined') errorCallback	= function e(a){};

	if(param.indexOf('mb_nonce_value=')==-1){
		if(param=="") param	= mb_options["nonce2"];
		else param	= param+"&"+mb_options["nonce2"];
	}

	if(String(action).indexOf('http')!==0){
		param	= param+"&action="+action+"&admin_page="+mb_ajax_object.admin_page+"&hybrid_app="+mb_hybrid_app;
		action	= mb_ajax_object.ajax_url;
	}
	if(param.indexOf('board_action=board_hit')==-1) showLoadingBox();

	jQuery.ajax({
		url: action,
		type: type,
		data: param,
		success:function(data,state){
			hideLoadingBox();
			successCallback(data, state);
		},error:function(e){			
			//console.log(JSON.stringify(e));
			hideLoadingBox();			
			if(e.responseJSON && e.responseJSON.state=="success"){
				successCallback(e.responseJSON, e.responseJSON.state);
			}else{
				errorCallback(e);
			}
		},timeout: 15000,
		cache: false,
		dataType: dataType
	});
}

function sendDataRequest2(action, param, successCallback, errorCallback, type, dataType){
	if(typeof(action)==='undefined' || action=='') action = 'mb_template';
	if(typeof(type)==='undefined') type = "POST";
	if(typeof(dataType)==='undefined') dataType = "json";
	if(typeof(successCallback)==='undefined') successCallback	= function s(a,b){};
	if(typeof(errorCallback)==='undefined') errorCallback	= function e(a){};

	if(param.indexOf('mb_nonce_value=')==-1){
		if(param=="") param	= mb_options["nonce2"];
		else param	= param+"&"+mb_options["nonce2"];
	}

	if(String(action).indexOf('http')!==0){
		param	= param+"&action="+action+"&admin_page="+mb_ajax_object.admin_page+"&hybrid_app="+mb_hybrid_app;
		action	= mb_ajax_object.ajax_url;
	}
	jQuery.ajax({
		url: action,
		type: type,
		data: param,
		success:function(data,state){
			successCallback(data, state);
		},error:function(e){			
			//console.log(JSON.stringify(e));
			hideLoadingBox();			
			errorCallback(e);
		},timeout: 15000,
		cache: false,
		dataType: dataType
	});
}

function mb_insertHtml(name,message){
	jQuery(name).html(message);	
}
function mb_appendHtml(name,message){
	jQuery(name).append(message);	
}

function checkCSSDisplay(name,time){
	if(typeof(time)==='undefined') time = 0;
	var objTarget		= jQuery(name);
	if(objTarget.css("display")=="none"){
		if(time==0) objTarget.show();
		else objTarget.slideDown( time );
	}else{		
		if(time==0) objTarget.hide();
		else objTarget.slideUp( time );
	}
}

function checkCSSDisplayID(id,time){
	if(typeof(time)==='undefined') time = 0;
	var objTarget		= jQuery("#"+id);
	if(objTarget.css("display")=="none"){
		if(time==0) objTarget.show();
		else objTarget.slideDown( time );
	}else{		
		if(time==0) objTarget.hide();
		else objTarget.slideUp( time );
	}
}

function checkBoxDisplayID(obj, id){
	if(jQuery(obj).prop('checked')){		
		jQuery("#"+id).show();
	}else{		
		jQuery("#"+id+" input").val("");
		jQuery("#"+id).hide();
	}
}

function set_category_data(data, id,value){
	if(typeof(data)!=='undefined'){

		jQuery("#"+id+" option").remove();
		var index		= id.substr(-1);

		if(typeof(mb_languages["selectbox"+index])!='undefined' && mb_languages["selectbox"+index]!="")
			jQuery("#"+id).append('<option value="">'+mb_languages["selectbox"+index]+'</option>');

		if(typeof(data)==='object'){		
			var add_html		= "";
			jQuery.each(data, function(key, entry) {
				if(value!="" && key==value){
					add_html	+= '<option value="'+key+'" selected>'+key+'</option>';
				}else{
					add_html	+= '<option value="'+key+'">'+key+'</option>';
				}			 
			});
			if(add_html!=""){
				jQuery("#"+id).append(add_html);
			}
			jQuery("#"+id).css("display","inline-block");
		}else{
			jQuery("#"+id).html('<option value=""></option>');
			jQuery("#"+id).hide();
		}	
	}else{
		jQuery("#"+id).html('<option value=""></option>');
		jQuery("#"+id).hide();
	}
}
function movePage(url, param){	
	moveURL(url, param)
}

function moveViewPage(pid,board_name,page){
	var param		= "vid="+pid;
	if(typeof(board_name)!=='undefined'&& board_name!="") param		= param+"&board_name="+board_name;
	if(typeof(page)!=='undefined' && page!="") param			= param+"&page="+page;
	moveURL("", param)
}

function moveURL(url, param, loading){
	var isLoading		= false;
	if(typeof(loading)!=='undefined') isLoading = loading;
	if(isLoading) showLoadingBox();

	if(typeof(param)!=='undefined' && param!=""){
		if(url.indexOf('?')==-1){
			url		= url+'?';
		}else{
			url		= url+'&';
		}
		url		= url+param;
	}
	if(url.indexOf('category1=')!=-1){
		if(url.indexOf('category1=&')!=-1){
			url		= url.replace('category2=&','');
			url		= url.replace('category3=&','');
		}else{
			url		= url.replace(/(category)(\d{1})=&/g,'');
		}
	}
	if(url.indexOf('&#038;')!=-1){
		url		= url.replace(/&#038;/g,'&');
	}
	var match_count = (url.match(/page_id=/g) || []).length;
	if(match_count>0) {
		url		= url.replace(/page_id=&/g,'');
		if(match_count>1) {
			var index1		= url.lastIndexOf('page_id=');
			var index2		= url.indexOf('&',index1);
			if(index2==-1){
				url					= url.slice(0,index1-1);
			}else{
				url					= url.slice(0,index1-1)+url.slice(index2);
			}
		}
	}
	if(url=="reload"){
		window.location.reload();
	}else if(url=="back"){
		window.history.back();
	}else if(url=="forward"){
		window.history.forward();
	}else if(url=="referer" || url=="referrer"){
		url		= document.referrer;
		if(url.indexOf('%26')==-1){
			url		= decodeURIComponent(url);
		}else{
			url		= url.replace(/%26/g,"##26##");
			url		= decodeURIComponent(url);
			url		= url.replace(/##26##/g,"%26");
		}
		window.location.href		= url;
	}else{
		if(url.indexOf('%26')==-1){
			url		= decodeURIComponent(url);
		}else{
			url		= url.replace(/%26/g,"##26##");
			url		= decodeURIComponent(url);
			url		= url.replace(/##26##/g,"%26");
		}
		window.location.href		= url;
	}
}
function openWindow(url,name,option){
	var objPopup;
	if(url.indexOf('&#038;')!=-1){
		url		= url.replace(/&#038;/g,'&');
	}
	if(typeof(mb_hybrid_app)==='undefined' || mb_hybrid_app==""){
		if(typeof(option)==='undefined') option	= "width=600,height=450,toolbar=no,location=no,status=no,menubar=no,top=200,left=300,scrollbars=no,resizable=no";
		if(typeof(mb_options)!=='undefined' && typeof(mb_options["device_type"])!=='undefined' && mb_options["device_type"]=="mobile"){
			objPopup		= window.open(url,name);
		}else{
			objPopup		= window.open(url,name,option);
		}
	}else{
		 objPopup		= openMobileWindow(url,name,option);
	}
	return objPopup;
}

function category_select(index){
	if(index==0){
		set_category_data(category_data,mb_options["board_name"]+"_category1",mb_categorys["value1"]);			
		if(mb_categorys["value1"]!=undefined && mb_categorys["value1"]!="" && jQuery("#"+mb_options["board_name"]+"_category1 option").filter(":selected").val()!=''){
			set_category_data(category_data[jQuery("#"+mb_options["board_name"]+"_category1 option").filter(":selected").val()],mb_options["board_name"]+"_category2",mb_categorys["value2"]);
		}else jQuery("#"+mb_options["board_name"]+"_category2").hide();
		if(mb_categorys["value2"]!=undefined && mb_categorys["value2"]!="" && jQuery("#"+mb_options["board_name"]+"_category2 option").filter(":selected").val()!=''){
			set_category_data(category_data[jQuery("#"+mb_options["board_name"]+"_category1 option").filter(":selected").val()][jQuery("#"+mb_options["board_name"]+"_category2 option").filter(":selected").val()],mb_options["board_name"]+"_category3",mb_categorys["value3"]);
		}else jQuery("#"+mb_options["board_name"]+"_category3").hide();
	}else if(index==1){

		set_category_data(category_data[jQuery("#"+mb_options["board_name"]+"_category1 option").filter(":selected").val()],mb_options["board_name"]+"_category2","");
		if (jQuery("#"+mb_options["board_name"]+"_category1 option").filter(":selected").val()!=""){
			set_category_data(category_data[jQuery("#"+mb_options["board_name"]+"_category1 option").filter(":selected").val()][jQuery("#"+mb_options["board_name"]+"_category2 option").filter(":selected").val()],mb_options["board_name"]+"_category3","");
		}else{
			set_category_data("",mb_options["board_name"]+"_category3","");
		}

	}else if(index==2){
		if (jQuery("#"+mb_options["board_name"]+"_category1 option").filter(":selected").val()!=""){
			set_category_data(category_data[jQuery("#"+mb_options["board_name"]+"_category1 option").filter(":selected").val()][jQuery("#"+mb_options["board_name"]+"_category2 option").filter(":selected").val()],mb_options["board_name"]+"_category3","");
		}else{
			set_category_data("",mb_options["board_name"]+"_category3","");
		}		
	}
}
var mb_selectFileName		= "";
function sendBoardFileData(file_pid,file_name){
	var data				= "mode=file&board_action=file_download&board_name="+mb_options["board_name"]+"&file_pid="+file_pid+"&file_name="+encodeURIComponent(file_name);
	mb_selectFileName		= file_name;
	sendDataRequest2(mb_urls["board_api"], data, sendBoardFileDataHandler);
}
function sendBoardFileDataHandler(response, state)
{
	if(response.state == "success"){
		if(typeof(response.data)!=='undefined' && typeof(response.data.file_path)!=='undefined'){
			if(mb_hybrid_app=="ios" && typeof(response.data.file_path2)!=='undefined'){
				var file_url	= mb_urls["base"];
				file_url			= file_url.replace('/wp-content/plugins','/wp-content/uploads');
				homeSendMessage({"mode":"FILE_DOWNLOAD","value": file_url+response.data.file_path2,"name": mb_selectFileName});
			}else{
				downloadFile(mb_urls["file"],"path="+encodeURIComponent(response.data.file_path));
			}			
		}
	}else{
		showAlertPopup(response);
	}
}
function downloadFile(url, param){
	if(typeof(param)!=='undefined' && param!=""){
		if(url.indexOf('?')==-1){
			url		= url+'?';
		}else{
			url		= url+'&';
		}
		url		= url+param;
	}
	if(typeof(mb_hybrid_app)==='undefined' || mb_hybrid_app==""){
		window.location.href		= decodeURIComponent(url+"&file_name="+mb_selectFileName+"&type=download");
	}else{
		window.location.href		= decodeURIComponent(url+"&type=download&file_name="+mb_selectFileName);
	}
}

var listTemplateMode		= "";
var listTemplateBoard		= "";
var listTemplateCheck		= true;
var listTemplateAction		= "";


function sendTabReload(data,idx){
	if(typeof(idx)==='undefined') idx	= "1";
	if(jQuery("input[name=category"+idx+"]")) jQuery("input[name=category"+idx+"]").val(data);
	if(idx==1 && data==''){
		if(jQuery("input[name=category2]")) jQuery("input[name=category2]").val(data);
		if(jQuery("input[name=category3]")) jQuery("input[name=category3]").val(data);
	}else if(idx==2 && data==''){
		if(jQuery("input[name=category3]")) jQuery("input[name=category3]").val(data);
	}
	sendSearchData();
}

function setEditorType(type){
	if(document.getElementById("editor_type")){
		document.getElementById("editor_type").value	= type;
	}
}
function sendListTemplateData(data){
	listTemplateCheck	= true;
	if(typeof(data)==='undefined') data = {};
	if(typeof(data.board_name)==='undefined' || data.board_name==='undefined' || data.board_name==='') board_name = mb_options["board_name"];	
	else board_name = data.board_name;	
	if(typeof(data.mode)==='undefined' || data.mode==='undefined') mode = "";
	else mode = data.mode;
	if(typeof(data.page)==='undefined' || data.page==='undefined' || data.page==='') page = 1;
	else page = data.page;	
	
	listTemplateBoard					= board_name;
	listTemplateMode					= mode;	
	var params		= jQuery('#'+listTemplateBoard+'_form_board_search').serialize();
	if(jQuery('#'+listTemplateBoard+'_form_board_search2').length>0) params		= params+"&"+jQuery('#'+listTemplateBoard+'_form_board_search2').serialize();
	params		= params+"&"+jQuery('#'+listTemplateBoard+'_form_board_list').serialize()+"&board_action=load";

	if(typeof(data.category)!=='undefined'){
		if(typeof(data.idx)==='undefined' || data.idx==='undefined' || data.idx==='') idx = "1";
		else idx = data.idx;
		params					= params+"&category"+idx+"="+encodeURIComponent(data.category);
	}else if(typeof(data.page_type)!=='undefined' && data.page_type=="ajax"){
		if(typeof(mb_categorys["value1"])!=='undefined' && mb_categorys["value1"]!=""){		
			params					= params+"&category1="+encodeURIComponent(mb_categorys["value1"]);
			if(typeof(mb_categorys["value2"])!=='undefined' && mb_categorys["value2"]!=""){		
				params					= params+"&category2="+encodeURIComponent(mb_categorys["value2"]);
				if(typeof(mb_categorys["value3"])!=='undefined' && mb_categorys["value3"]!=""){		
					params					= params+"&category3="+encodeURIComponent(mb_categorys["value3"]);
				}
			}
		}
	}

	if(typeof(data.page_type)!=='undefined'){
		params					= params+"&page_type="+encodeURIComponent(data.page_type);
	}		
	params					= params+"&board_page="+page;	
	sendDataRequest(mb_urls["template_api"], params, sendListTemplateDataHandler);			
}

function sendListTemplateDataHandler(response, state){		
	if(listTemplateCheck){
		if(response.state == "success"){
			if(listTemplateMode!="append"){
				jQuery("#"+listTemplateBoard+"_board_body>tr").remove();
				jQuery("#"+listTemplateBoard+"_board_body>div").remove();
			}

			if(response.data["body"]) jQuery("#"+listTemplateBoard+"_board_body").append(response.data["body"]);
			if(response.data["pagination"]!="") jQuery('#'+listTemplateBoard+'_pagination_box').html(response.data["pagination"]);
			else{
				jQuery('#'+listTemplateBoard+'_pagination_box').html("");				
			}
		}else{
			showAlertPopup(response);
		}
		listTemplateCheck		= false;
	}
}


function getPostcode(type,id) {
	if(typeof(mb_hybrid_app)==='undefined' || mb_hybrid_app==""){
		new daum.Postcode({
			oncomplete: function(data) {
				var fullAddr = ""; 
				var extraAddr = "";

				if(data.userSelectedType === "R"){
					fullAddr = data.roadAddress;
				}else{
					fullAddr = data.jibunAddress;
				}

				if(data.userSelectedType === "R"){
					if(data.bname !== ""){extraAddr += data.bname;}
					if(data.buildingName !== ""){extraAddr += (extraAddr !== "" ? ", " + data.buildingName : data.buildingName);}
					fullAddr += (extraAddr !== "" ? " ("+ extraAddr +")" : "");
				}

				//document.getElementById(type+"_postcode").value = data.postcode1+"-"+data.postcode2;
				document.getElementById(type+"_postcode").value = data.zonecode;
				document.getElementById(type+"_address1").value = fullAddr;
				jQuery("#"+type+"_address2").focus();
			}
		}).open();
	}else{
		openWindow(mb_urls["home"]+"/?mb_app=postcode&type="+type);
	}
}
function getPostcodeIframe(type,id) {
	if(typeof(id)==='undefined' || id=='') id = 'mb_kakao_postcode1';
	var element_wrap	= document.getElementById(id);
	var currentScroll		= Math.max(document.body.scrollTop, document.documentElement.scrollTop);
	new daum.Postcode({
		oncomplete: function(data) {
			var fullAddr = ""; 
			var extraAddr = "";

			if(data.userSelectedType === "R"){
				fullAddr = data.roadAddress;
			}else{
				fullAddr = data.jibunAddress;
			}
			if(data.userSelectedType === "R"){
				if(data.bname !== ""){extraAddr += data.bname;}
				if(data.buildingName !== ""){extraAddr += (extraAddr !== "" ? ", " + data.buildingName : data.buildingName);}
				fullAddr += (extraAddr !== "" ? " ("+ extraAddr +")" : "");
			}
			//document.getElementById(type+"_postcode").value = data.postcode1+"-"+data.postcode2;
			document.getElementById(type+"_postcode").value = data.zonecode;
			document.getElementById(type+"_address1").value = fullAddr;
			
			element_wrap.style.display = 'none';
			jQuery("html, body").scrollTop( currentScroll );
			jQuery("#"+type+"_address2").focus();
		},
		onresize : function(size) {
			element_wrap.style.height = (size.height+30)+'px';
		},
		width : '100%',
		height : '100%'
	}).embed(element_wrap);
	element_wrap.style.display = 'block';
}
function template_match_handler(type,obj,name,match_type,match_value){
	var value		= "";
	if(type=="checkbox"){
		value		= jQuery(obj).prop('checked') ? "1":"0";
	}else if(type=="radio" || type=="select"){
		value		= jQuery(obj).val();
	}else{
		if(jQuery(obj).val()!=""){
			value		= jQuery(obj).val();
		}else{
			value		= jQuery(obj).find('input').first().val();
		}		
	}
	match_value	= ","+match_value+",";
	value				= ","+value+",";
	var target		= jQuery(".mb-combo-"+name);
	if(match_type=="show"){
		if(match_value.indexOf(value)!=-1) target.css("display","inline-block");
		else{
			target.hide();
		}
	}else if(match_type=="hide"){
		if(match_value.indexOf(value)!=-1){
			target.hide();
		}else target.css("display","inline-block");
	}
}
var template_combo_hide	= {};
function template_combo_handler(type,obj,name){
	var value		= "";
	if(type=="checkbox"){
		value		= jQuery(obj).prop('checked') ? "1":"0";
	}else{
		value		= jQuery(obj).val();
	}
	var combo_wrapper		= jQuery(obj).closest('.mb-combo-wrapper').find('.mb-combo-items');
	combo_wrapper.children().hide();
	combo_wrapper.find(":input").prop("disabled", true);
	combo_wrapper.find('.mb-combo-'+name+'-'+value).show();
	combo_wrapper.find('.mb-combo-'+name+'-'+value).find(":input").prop("disabled", false);

	if(typeof(template_combo_hide[name])!=='undefined' && template_combo_hide[name]!=''){
		template_combo_display_check('show',name,template_combo_hide[name]);
	}	
	if(combo_wrapper.find('.mb-combo-'+name+'-'+value+' .mb-combo-box-hide-element').length>0){
		var combo_hide		= combo_wrapper.find('.mb-combo-'+name+'-'+value+' .mb-combo-box-hide-element').val();
		if(combo_hide!=''){
			template_combo_display_check('hide',name,combo_hide);
		}
	}
}
function template_combo_display_check(type,name,value){
	if(typeof(mb_options["board_name"])!=='undefined' && value!=''){
		var items			= value.split(",");
		var item_name	= mb_options["board_name"];
		for(var i=0; i < items.length; i++) {
			if(type=='hide'){
				jQuery('tr#mb_'+item_name+'_tr_'+items[i]).find(":input").prop("disabled", true);
				jQuery('tr#mb_'+item_name+'_tr_'+items[i]).hide();
			}else{
				jQuery('tr#mb_'+item_name+'_tr_'+items[i]).find(":input").prop("disabled", false);
				jQuery('tr#mb_'+item_name+'_tr_'+items[i]).show();
			}
		}
		if(type=='hide'){
			template_combo_hide[name] = value;
		}else{
			template_combo_hide[name] = "";
		}
	}
}
function checkEnterKey(callback,param){
	if(event.keyCode == 13){

		if(typeof(param)==='undefined')
			callback();
		else
			callback(param);
	}
}
var openTarget;
var openPid				= "";
var openColspan		= 0;
var openColspanIndex	= 0;
function openContents(obj, name, index, action){		
	if(typeof(index)!=='undefined') openColspanIndex	= index;
	if(typeof(action)!=='undefined') open_action	= action;
	else open_action	= "content";

	openTarget		= jQuery(obj).closest("tr");	
	if(openTarget.next().hasClass("mb-open-box")){
		if(openTarget.next().css("display")=="none"){
			//openTarget.next().fadeIn('slow');
			openTarget.next().show();
			openTarget.next().find(".mb-open-slide").slideDown(300);
			openTarget.find(".mb-icon-box").addClass('mb-icon-close');
		}else{			
			//openTarget.next().fadeOut('slow');
			openTarget.next().find(".mb-open-slide").slideUp(300,function(){openTarget.next().hide();});
			openTarget.find(".mb-icon-box").removeClass('mb-icon-close');
		}
	}else{
		//콘텐츠 데이타 불러오기		
		if(typeof(name)==='undefined' || name=="") name	= mb_options["board_name"];
		var board_pid = jQuery(obj).attr("class").split("_").pop(); 		
		var data		= "board_name="+name+"&mode=list&board_action="+open_action+"&board_pid="+board_pid;
		sendDataRequest2(mb_urls["template_api"], data, sendContentDataHandler);		
	}
}

function isJsonType(data){
	if(data.indexOf("{")!==-1) return true;
	else return false;
}
function sendContentDataHandler(response, state){		
	if(response.state == "success"){
		var content_html		= '<tr class="mb-open-box">';
		var colspan				= openTarget.find("td").length;
		if(openColspanIndex>0){
			colspan		= colspan - openColspanIndex;
			for(i=0;i<openColspanIndex;i++){
				content_html		= content_html+'<td></td>';
			}
		}
		content_html		= content_html+'<td colspan="'+colspan+'"><div class="mb-open-slide" style="display:none"><div class="mb-open-content">'+response.data+'</div></div></td></tr>';
		openTarget.after(content_html);		
		//openTarget.next().hide();
		//openTarget.next().fadeIn('slow');
		openTarget.next().show();
		openTarget.next().find(".mb-open-slide").slideDown(300);
	}else{
		showAlertPopup(response);
	}
}
function mb_reloadImage_class(name){
	if(typeof(name)==='undefined' || name=='') name = "mb_kcaptcha";

	var img_url			= jQuery("."+name).attr("src");
	var timestamp		= new Date().getTime();

	if(img_url.indexOf('?')==-1){
		img_url		= img_url+'?time=';
	}else{
		img_url		= img_url+'&time=';
	}
	img_url		= img_url+timestamp;

	jQuery("."+name).attr("src",img_url)
}
function mb_reloadImage(id){
	if(typeof(id)==='undefined') id = "mb_kcaptcha";

	var img_url			= jQuery("#"+id).attr("src");
	var timestamp		= new Date().getTime();

	if(img_url.indexOf('?')==-1){
		img_url		= img_url+'?time=';
	}else{
		img_url		= img_url+'&time=';
	}
	img_url		= img_url+timestamp;

	jQuery("#"+id).attr("src",img_url)
}

function checkMaxNumber(obj,max){
	if(typeof(max)!=='undefined'){
		if(max<parseInt(jQuery(obj).val())){
			jQuery(obj).val(max);		
		}		
	}	
}
Number.prototype.to2 = function(){return this<10?'0'+this:this;}
function setSearchDate(type){
	var date				= new Date();
	end_date			= date.getFullYear()+"-"+(date.getMonth()+1).to2()+"-"+(date.getDate()).to2();

	if(type=="month"){
		var date2			= new Date(date.getFullYear(),date.getMonth(),0);
		start_date			= date2.getFullYear()+"-"+(date2.getMonth()+1).to2()+"-"+(date.getDate()).to2();
	}else if(type=="total" || type=="empty"){
		start_date			= ""; end_date			= "";
	}else{
		if(type=="today"){
			start_date			= end_date;
		}else if(type=="yesterday"){
			date.setTime(date.getTime() - (24 * 60 * 60 * 1000));
			end_date			= date.getFullYear()+"-"+(date.getMonth()+1).to2()+"-"+(date.getDate()).to2();
		}else if(type=="tomorrow"){
			date.setTime(date.getTime() + (24 * 60 * 60 * 1000));
			end_date			= date.getFullYear()+"-"+(date.getMonth()+1).to2()+"-"+(date.getDate()).to2();
		}else if(type=="week"){
			date.setTime(date.getTime() - (24 * 60 * 60 * 1000 * 7));
		}else if(type=="last_year"){
			date					= new Date(date.getFullYear()-1,date.getMonth()+1,0);
			end_date				= date.getFullYear()+"-"+(date.getMonth()+1).to2()+"-"+(date.getDate()).to2();
			date					= new Date(date.getFullYear(),date.getMonth(),1);
		}else if(type=="last_month"){
			date					= new Date(date.getFullYear(),date.getMonth(),0);
			end_date			= date.getFullYear()+"-"+(date.getMonth()+1).to2()+"-"+(date.getDate()).to2();
			date					= new Date(date.getFullYear(),date.getMonth(),1);
		}else if(type=="this_month"){
			date					= new Date(date.getFullYear(),date.getMonth(),1);
		}else if(type=="next_month"){
			date					= new Date(date.getFullYear(),(date.getMonth()+2),0);
			end_date			= date.getFullYear()+"-"+(date.getMonth()+1).to2()+"-"+(date.getDate()).to2();
			date					= new Date(date.getFullYear(),date.getMonth(),1);
		}
		start_date			= date.getFullYear()+"-"+(date.getMonth()+1).to2()+"-"+(date.getDate()).to2();
	}
	jQuery("input[name='start_date']").val(start_date);
	jQuery("input[name='end_date']").val(end_date);
}

function inputOnlyNumber(event){
	var code = event.which ? event.which : event.keyCode;
	if(code == 0 || code == 9 || code == 8 || code == 46 || code == 110 || code == 188 || code == 37 || code == 39 || code == 190 || (96<=code && code <= 105)){
		return;
	}
	if( (code < 48) || (code > 57) ){
		return false;
	}	
}

function moveBodyScrollPosition(name,top,time){
	if(typeof(top)==='undefined') top = 40;
	if(typeof(time)==='undefined') time = 0;
	jQuery("html, body").animate({scrollTop: jQuery(name).offset().top-top}, time);
}

function imgResize(objImage,nWidth,nHeight){
	if(typeof(nWidth)==='undefined') nWidth = 50;
	if(typeof(nHeight)==='undefined') nHeight = nWidth;

	nWidth		= parseInt(nWidth);
	nHeight		= parseInt(nHeight);

	var imgFile			= new Image();
	imgFile.src			= objImage.src;

	var imgWidth		= imgFile.width;
	var imgHeight		= imgFile.height;
	
	if(imgWidth>imgHeight)
	{
		imgHeight = imgHeight * nWidth/imgWidth;
		imgWidth  = nWidth;
		
		if(imgHeight>nHeight)
		{
			imgWidth  = imgWidth * nHeight/imgHeight;
			imgHeight = nHeight;			
		}
		
	} else if(imgWidth<=imgHeight)
	{
		imgWidth  = imgWidth * nHeight/imgHeight;
		imgHeight = nHeight;
		
		if(imgWidth>nWidth)
		{
			imgHeight = imgHeight * nWidth/imgWidth;
			imgWidth  = nWidth;
		}
	} else
	{
		imgWidth  = nWidth;
		imgHeight = nHeight;
	}
	objImage.width		= imgWidth;
	objImage.height		= imgHeight;
}
function checkSendApiURL(){
	if(typeof(mb_urls['board_api'])==='undefined' && typeof(mb_urls['template_api'])==='undefined') {
		mb_urls['board_api']			= "mb_board";
		mb_urls['comment_api']	= "mb_comment";
		mb_urls['user_api']			= "mb_user";
		mb_urls['heditor_api']		= "mb_heditor";
		mb_urls['template_api']		= "mb_template";
		mb_urls['custom_api']		= "mb_custom";
		mb_urls['commerce_api']	= "mb_commerce";
	}
}

jQuery(document).ready(function() {
	if(jQuery.isFunction(jQuery(".tooltip").tipTip)){
		jQuery(".tooltip").tipTip();
	}
	//숫자만 입력받기
	jQuery(".mbi-only-int").keyup (function () {
		jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g,""));
    });
	jQuery(".mbi-only-num").keyup (function () {
		jQuery(this).val(jQuery(this).val().replace(/[^0-9,.]/g,""));
    });
	//숫자만 입력받고 최대 자릿수가 되면 다음 포커스로 이동
	jQuery(".mbi-next-focus-num").keyup (function () {
		var maxLength = jQuery(this).attr("maxlength");
		jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g,""));
        if (this.value.length >= maxLength) {
			if(jQuery(this).next('.mbi-next-focus-num').length>0){
				jQuery(this).next('.mbi-next-focus-num').focus();
			}else if(jQuery(this).next().next('.mbi-next-focus-num').length>0){
				jQuery(this).next().next('.mbi-next-focus-num').focus();
			}
            return false;
        }
    });
	//텍스트를 입력받고 최대 자릿수가 되면 다음 포커스로 이동
	jQuery(".mbi-next-focus").keyup (function () {
		var maxLength = jQuery(this).attr("maxlength");
        if (this.value.length >= maxLength) {
			if(jQuery(this).next('.mbi-next-focus').length>0){
				jQuery(this).next('.mbi-next-focus').focus();
			}else if(jQuery(this).next().next('.mbi-next-focus').length>0){
				jQuery(this).next().next('.mbi-next-focus').focus();
			}            
            return false;
        }
    });
	jQuery(".mb-user-phone").keyup (function () {
		var key = event.charCode || event.keyCode || 0;
		var value	= jQuery(this).val();		
		value		= value.replace(/[^0-9\-\(\)\+\s]/g,"");
		if(value.length>2){
			if(value.indexOf('010')==0){
				if(key!=8 && (value.length==3 || value.length==8)) value += '-';
				if(value.indexOf('-')==-1){
					if(value.length>10){
						value		= value.substr(0, 3)+'-'+value.substr(3, 4)+'-'+value.substr(7,4);
					}
				}else{
					value	= value.replace("--", "-");
				}
			}
		}
		jQuery(this).val(value);
    });
	checkSendApiURL();
});