<?php
$desktop_model					= array();
$tablet_model					= array();
$mobile_model				= array();
$desktop_model['version']		= "1.0.0";

$desktop_model['list']		= '
{"type":"list_check","width":"50px","level":"10","class":"list_check","mobile_width":"30px"},
{"field":"fn_option_title","name":"W_TYPE","width":"200px","mobile_width":"80px","type":"title","td_class":"text-left","translate":"true"},
{"field":"fn_option_value","name":"W_CONTENT","width":"","type":"db_type","type_field":"fn_option_type","data_field":"fn_option_data","label_field":"fn_option_label","style_field":"fn_option_style","class_field":"fn_option_class","event_field":"fn_option_event","attribute_field":"fn_option_attribute","description_field":"fn_description","td_class":"text-left"},
{"field":"admin_btn","name":"","name_btn":"W_MODIFICATION","width":"70px","mobile_width":"60px","type":"admin_option_modify"}
';


//글보기 스킨 수정
$desktop_model['view']		= '
{"tpl":"tag","tag_name":"table","type":"start","name":"W_VIEW_MSG","width":"200px,*","mobile_width":"90px,*","class":"table table-view"},
{"field":"fn_option_title","name":"W_TYPE","width":"300px"},
{"field":"fn_option_name","name":"W_OPTION_NAME","width":"300px"},
{"field":"fn_option_value","name":"W_OPTION_VALUE","width":"300px"},
{"field":"fn_option_type","name":"W_INPUT_TYPE","width":"300px"},
{"field":"fn_option_label","name":"W_INPUT_OPTION_NAME","width":"600px"},
{"field":"fn_option_data","name":"W_INPUT_OPTION","width":"600px"},
{"field":"fn_option_style","name":"W_INPUT_STYLE_SET","width":"600px"},
{"field":"fn_option_class","name":"W_INPUT_CLASS_SET","width":"600px"},
{"field":"fn_option_event","name":"W_INPUT_EVENT_SET","width":"600px"},
{"field":"fn_option_attribute","name":"W_INPUT_ATTRIBUTE_SET","width":"600px"},
{"field":"fn_description","name":"W_EXPLANATION_OPTIONS","width":"600px"},
{"field":"fn_option_category","name":"W_CATEGORY","width":"300px"},
{"tpl":"tag","tag_name":"table","type":"end"}
';

//글작성 스킨 수정
$desktop_model['write']		= '
{"tpl":"tag","tag_name":"table","type":"start","name":"W_WRITE","width":"200px,*","mobile_width":"90px,*","class":"table table-write"},
{"field":"fn_option_title","name":"W_TYPE","width":"300px"},
{"field":"fn_option_name","name":"W_OPTION_NAME","width":"300px"},
{"field":"fn_option_value","name":"W_OPTION_VALUE","width":"300px"},
{"field":"fn_option_type","name":"W_INPUT_TYPE","width":"300px","description":"<br>W_INPUT_OPTION_SET"},
{"field":"fn_option_label","name":"W_INPUT_OPTION_NAME","width":"600px","description":"<br>'.__MW('W_ON_OFF').' ('.__MW('W_INPUT_SELECT_RADIO').')"},
{"field":"fn_option_data","name":"W_INPUT_OPTION","width":"600px","description":"<br>true,false ('.__MW('W_INPUT_SELECT_RADIO').')"},
{"field":"fn_option_style","name":"W_INPUT_STYLE_SET","width":"600px","description":"<br>W_INPUT_STYLE_SET_DESC"},
{"field":"fn_option_class","name":"W_INPUT_CLASS_SET","width":"600px","description":"<br>W_INPUT_CLASS_SET_DESC"},
{"field":"fn_option_event","name":"W_INPUT_EVENT_SET","width":"600px","description":"<br>W_INPUT_EVENT_SET_DESC"},
{"field":"fn_option_attribute","name":"W_INPUT_ATTRIBUTE_SET","width":"600px","description":"<br>W_INPUT_ATTRIBUTE_SET_DESC"},

{"field":"fn_description","name":"W_EXPLANATION_OPTIONS","width":"600px"},
{"field":"fn_option_category","name":"W_CATEGORY","width":"300px"},
{"tpl":"tag","tag_name":"table","type":"end"}
';

$tablet_model									= $desktop_model;
$mobile_model								= $desktop_model;
mbw_set_fields("select_board",$mb_fields["options"]);

if(mbw_get_param("show")=="all"){
	mbw_set_param("page_size",300);
}else{
	mbw_set_board_where(array("field"=>"fn_is_show", "value"=>"1"));
}

if(!function_exists('mbw_options_api_footer')){
	function mbw_options_api_footer(){
		mbw_options_meta_refresh();	
	}
}
add_action('mbw_board_api_footer', 'mbw_options_api_footer',5); 

if(!function_exists('mbw_board_options_skin_footer')){
	function mbw_board_options_skin_footer(){
		if(mbw_is_admin_page() && mbw_get_param("mode")=="list"){
			echo '<script type="text/javascript">jQuery(document).ready(function(){';
				echo 'if(jQuery(".mb-tr-hide").length>0) {jQuery(".mb-tr-hide").closest("tr").hide(); }';
				echo 'jQuery("input[type=\'text\'],textarea,select,input[type=\'radio\']",jQuery("#tbl_board_list")).on("input",function(){  var objTarget	= jQuery(this).closest("tr").find("input[name=\'check_array[]\']"); if(!objTarget.is(":checked")){ objTarget.trigger("click"); }	}); ';
			echo '}); </script>';
		}
	}
}
add_action('mbw_board_skin_footer', 'mbw_board_options_skin_footer',5); 

mbw_set_category_fields(array("fn_option_category"));		//카테고리 필드 수정
if(mbw_is_admin_page()){		//어드민 페이지에서만 실행	
	if(mbw_get_request_mode()=="Frontend"){		// 게시판 모드일 경우에만
		//카테고리 데이타 수정
		$category		= $mdb->get_distinct_values($mb_admin_tables["options"],$mb_fields["options"]["fn_option_category"]);		//option_category 필드에서 고유한 값을 배열로 가져옴
		if(!empty($category)) mbw_set_board_option("fn_category_data", implode(",",$category));
		$button_html		= mbw_get_btn_template(array("name"=>"Modify","onclick"=>"sendBoardListData({'mode':'list','board_action':'multi_modify'})","class"=>"btn btn-default"));
		mbw_add_left_button("list",$button_html);
	}
}
?>