<?php
$editor_type							= "S";
$editor_name							= "Smart Editor";
$mb_editors[$editor_type]			= array("type"=>$editor_type,"name"=>$editor_name,"script"=>"if(typeof(oEditors)!=='undefined'){ oEditors.getById['se_content'].exec('UPDATE_CONTENTS_FIELD', []);}; sendBoardWriteData();");

if(!function_exists('mbw_load_editor_s')){
	function mbw_load_editor_s(){		
		if(mbw_get_trace("mbw_load_editor_s")==""){
			mbw_add_trace("mbw_load_editor_s");
			wp_enqueue_script('smart-editor-js');
		}
	}
}
add_action('mbw_load_editor_'.$editor_type, 'mbw_load_editor_s',5); 
if(!function_exists('mbw_editor_smart_init')){
	function mbw_editor_smart_init(){
		if(mbw_get_vars("device_type")=="desktop"){
			wp_register_script('smart-editor-js', MBW_PLUGIN_URL.'plugins/editors/smart/js/service/HuskyEZCreator.js');
		}else{
			wp_register_script('smart-editor-js', MBW_PLUGIN_URL.'plugins/editors/smart/js/service/HuskyEZCreator_mobile.js');
		}
		
		if(mbw_get_board_option("fn_editor_type")=="S" && mbw_get_param("mode")=="write"){
			mbw_load_editor_s();			
		}
	}
}
add_action('wp_enqueue_scripts', 'mbw_editor_smart_init',5);
add_action('admin_enqueue_scripts', 'mbw_editor_smart_init',5);

if(!function_exists('mbw_editor_smart_template')){
	function mbw_editor_smart_template($action, $data){
		
		/*if(mbw_get_vars("device_type")=="mobile" || mbw_get_vars("device_type")=="tablet"){		
			if(function_exists('mbw_editor_ck_template')){
				mbw_editor_ck_template($action, $data);
				echo '<script type="text/javascript">setEditorType("C");</script>';
			}else{
				echo mbw_get_default_editor($data);
			}
		}else */{
			if(mbw_get_trace("mbw_load_editor_s")==""){
				mbw_load_editor_s();
			}
			$item_html		= "";
			mbw_set_board_option("fn_editor_type","S");
			if(empty($data["width"])) $data["width"]			= '100%';
			if(empty($data["height"])) $data["height"]		= '360px';

			
			$use_photo_upload			= "true";
			if(mbw_get_vars("device_type")!="desktop" && mbw_get_vars("use_editor_mobile_upload")=="false"){
				$use_photo_upload			= "false";
			}

			$locale			= mbw_get_option("locale");

			if($locale=='en_US' || $locale=='en') $editor_locale	= 'en_US';
			else if($locale=='ja') $editor_locale	= 'ja_JP';
			else if($locale=='zh_CN' || $locale=='zh') $editor_locale	= 'zh_CN';
			else if($locale=='zh_TW') $editor_locale	= 'zh_TW';
			else if($locale=='ko_KR' || $locale=='ko') $editor_locale	= 'ko_KR';
			else $editor_locale		= 'ko_KR';

			if(!empty($data["editor_id"])){
				$editor_id			= $data["editor_id"];
			}else{
				$editor_id			= "se_content";
			}
			if(mbw_get_trace("mbw_load_editor_s_".$editor_id)==""){
				if(mbw_get_trace("mbw_head")!="" || mbw_is_admin_page()){
					mbw_add_trace("mbw_load_editor_s_".$editor_id);
				}
			}else{
				return;
			}
			
			$font_name			= mbw_get_vars("mb-font-name");
			$font_local_name	= mbw_get_vars("mb-font-local-name");
			if(empty($font_name)){
				$font_name			= "Nanum Gothic";
				$font_local_name	= "나눔고딕";
			}
			if(empty($font_local_name)){
				$font_local_name	= $font_name;
			}

			$editor_url		= mbw_check_url(MBW_HOME_URL);
			if(strpos($editor_url, '?') === false)	$editor_url		.= "/?";
			else $editor_url		.= "&";
			$board_name		= mbw_get_board_name();
			$editor_skin			= $editor_url."mb_ext=seditor&se_skin=SmartEditor2Skin_".$editor_locale."&se_locale=".$editor_locale."&board_name=".$board_name;
			
			if(empty($data["value"])){
				if($font_local_name!=$font_name){
					if($editor_locale!='ko_KR'){
						$font_local_name	= trim(str_replace(" ", "", $font_name));						
					}
					$data["value"]		= "<p style=\"line-height:1.8;\"><span style=\"font-size:13px;font-family:'".esc_attr($font_local_name)."','".esc_attr($font_name)."',sans-serif;\"><br></span></p>";
				}else{
					$data["value"]		= "<p style=\"line-height:1.8;\"><span style=\"font-size:13px;font-family:'".esc_attr($font_name)."',sans-serif;\"><br></span></p>";
				}
			}
			$item_html		= "";
			$item_html		.= '<input type="hidden" name="'.mbw_set_form_name("data_type").'" id="data_type" value="html" />';
			$item_html		.= '<textarea'.$data["ext"].__STYLE("width:".$data["width"].";height:".$data["height"].";".$data["style"].";visibility:hidden;").' name="'.esc_attr($data["item_name"]).'" id="'.esc_attr($editor_id).'" title="'.esc_attr($data["name"]).'">'.($data["value"]).'</textarea>';
			$item_html		.= '<script type="text/javascript">if(typeof(oEditors)==="undefined"){var oEditors = [];};';
				$item_html		.= 'jQuery(document).ready(function(){nhn.husky.EZCreator.createInIFrame({oAppRef: oEditors,elPlaceHolder: "'.esc_js($editor_id).'",sSkinURI:"'.esc_url_raw($editor_skin).'",fCreator:"createSEditor2",htParams:{bUseToolbar:true,bSkipXssFilter : true,I18N_LOCALE:"'.esc_js($editor_locale).'",bUsePhotoUpload:'.esc_js($use_photo_upload).',bUseVerticalResizer:true,bUseModeChanger:true';
				if($font_local_name!=$font_name){
					$font_name			= $font_local_name.",".$font_name;
				}
				if(mbw_get_vars("mb-font-name")!=""){
					$item_html		.= ',aAdditionalFontList:[["'.esc_js($font_name).'","'.esc_js($font_local_name).'"]]';
				}
				$item_html		.= '},fOnAppLoad:function(){   oEditors.getById["'.esc_attr($editor_id).'"].setDefaultFont("'.($font_name).'", 10);   }}); ';
			$item_html		.= '});</script>';
			echo $item_html;
		}
	}
}
add_action('mbw_editor_'.$editor_type, 'mbw_editor_smart_template',5,2);
?>