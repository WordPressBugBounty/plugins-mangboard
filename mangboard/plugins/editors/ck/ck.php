<?php
$editor_type						= "C";
$editor_name					= "CK Editor";
$mb_editors[$editor_type]		= array("type"=>$editor_type,"name"=>$editor_name,"script"=>"if(typeof(ckeditor)!=='undefined'){ sendBoardWriteData(ckeditor.getData()); }else{sendBoardWriteData();}");

if(!function_exists('mbw_load_editor_c')){
	function mbw_load_editor_c(){		
		if(mbw_get_trace("mbw_load_editor_c")==""){
			mbw_add_trace("mbw_load_editor_c");
			wp_enqueue_script('ck-editor-js');
			loadStyle(MBW_PLUGIN_URL."plugins/editors/ck/css/style.css");
		}
	}
}
add_action('mbw_load_editor_'.$editor_type, 'mbw_load_editor_c',5); 
if(!function_exists('mbw_editor_ck_init')){
	function mbw_editor_ck_init(){
		if(mbw_get_vars("device_type")=="mobile"){
			//wp_register_script('ck-editor-js', '//cdn.ckeditor.com/4.22.1/full/ckeditor.js');
			wp_register_script('ck-editor-js', MBW_PLUGIN_URL.'plugins/editors/ck/js/ckeditor.js');
		}else{
			//wp_register_script('ck-editor-js', '//cdn.ckeditor.com/4.22.1/full/ckeditor.js');
			wp_register_script('ck-editor-js', MBW_PLUGIN_URL.'plugins/editors/ck/js/ckeditor.js');
		}
		if(mbw_get_board_option("fn_editor_type")=="C" && mbw_get_param("mode")=="write"){
			mbw_load_editor_c();
		}
	}
}
add_action('wp_enqueue_scripts', 'mbw_editor_ck_init',5);
add_action('admin_enqueue_scripts', 'mbw_editor_ck_init',5);

if(!function_exists('mbw_editor_ck_template')){
	function mbw_editor_ck_template($action, $data){
		if(mbw_get_trace("mbw_load_editor_c")==""){
			mbw_load_editor_c();
		}
		mbw_set_board_option("fn_editor_type","C");
		
		if(empty($data["width"])) $data["width"]			= '100%';
		if(empty($data["height"])) $data["height"]		= '360px';
		$device_type	= mbw_get_vars("device_type");

		if(!empty($data["editor_id"])){
			$editor_id			= $data["editor_id"];
		}else{
			$editor_id			= "ce_content";
		}

		$item_html		= "";
		$item_html		.= '<input type="hidden" name="'.mbw_set_form_name("data_type").'" id="data_type" value="html" />';
		$item_html		.= '<textarea'.$data["ext"].__STYLE("width:".$data["width"].";height:".$data["height"].";".$data["style"].";visibility:hidden;").' name="'.esc_attr($data["item_name"]).'" id="'.esc_attr($editor_id).'" title="'.esc_attr($data["name"]).'">'.($data["value"]).'</textarea>';
		
		if(mbw_get_vars("device_type")=="mobile") $config_name		= "basic_config";
		else $config_name		= "standard_config";

		$editor_css		= "";
		$editor_css		.= 'ckeditor.addContentsCss( "'.MBW_PLUGIN_URL.'assets/css/bootstrap3-grid.css" );';
		$editor_css		.= 'ckeditor.addContentsCss( "'.MBW_PLUGIN_URL.'assets/css/style.css" );';
		$editor_css		.= 'ckeditor.addContentsCss( "'.MBW_PLUGIN_URL.'plugins/editors/ck/css/style.css" );';
		
		if(is_dir(MBW_PLUGIN_PATH."plugins/editor_composer/")) $editor_css	.= 'ckeditor.addContentsCss( "'.MBW_PLUGIN_URL.'plugins/editor_composer/css/style.css" );';

		$board_name		= mbw_get_board_name();
		$admin_ajax_url		= mbw_check_url(admin_url( 'admin-ajax.php' ));
		if(!mbw_is_ssl() && strpos($admin_ajax_url, 'https://') !== false) $admin_ajax_url		= mbw_get_http_url($admin_ajax_url);
		$item_html		.= '<script type="text/javascript">jQuery(document).ready(function(){';			
			$font_names		= 'Arial/Arial,Helvetica,sans-serif;Comic Sans MS/Comic Sans MS,cursive;Courier New/Courier New,Courier,monospace;Georgia/Georgia,serif;Lucida Sans Unicode/Lucida Sans Unicode,Lucida Grande,sans-serif;Tahoma/Tahoma,Geneva,sans-serif;Times New Roman/Times New Roman,Times,serif;Trebuchet MS/Trebuchet MS,Helvetica,sans-serif;Verdana/Verdana,Geneva,sans-serif;';
			$locale			= mbw_get_option("locale");
			if($locale=='ko_KR' || $locale=='ko'){
				$font_names		= "돋움체;굴림체;바탕체;궁서체;".$font_names;
			}
			$font_name			= mbw_get_vars("mb-font-name");
			$font_url				= mbw_get_vars("mb-font-url");
			$font_local_name	= mbw_get_vars("mb-font-local-name");
			if(!empty($font_name) && !empty($font_url)){
				$editor_css		.= 'ckeditor.addContentsCss("'.esc_url($font_url).'");';				
				if(!empty($font_local_name)){
					$item_html		.= 'CKEDITOR.config.font_defaultLabel = "'.esc_js($font_local_name).'";';
					$item_html		.= 'CKEDITOR.addCss(".cke_editable{font-family:\''.esc_js($font_local_name).'\',\''.esc_js($font_name).'\',sans-serif;}");';
					$font_names		= $font_local_name."/".$font_local_name.",".$font_name.";".$font_names;
				}else{
					$item_html		.= 'CKEDITOR.config.font_defaultLabel = "'.esc_js($font_name).'";';
					$item_html		.= 'CKEDITOR.addCss(".cke_editable{font-family:\''.esc_js($font_name).'\',sans-serif;}");';
					$font_names		= $font_name.";".$font_names;
				}
			}
			$item_html		.= 'CKEDITOR.config.font_names="'.esc_js($font_names).'";';
			$item_html		.= 'ckeditor = CKEDITOR.replace( "'.esc_js($editor_id).'",{"bodyClass":"mb-'.esc_js($device_type).' mb-editor mb-editor-ck","customConfig": "'.MBW_PLUGIN_URL.'plugins/editors/ck/'.esc_js($config_name).'.js","filebrowserUploadUrl":"'.esc_js($admin_ajax_url).'?mode=basic&action=mb_uploader&board_name='.esc_js($board_name).'&CKEditorFuncNum=json&'.mbw_create_nonce("param",$board_name).'"});';
			$item_html		.= $editor_css;
		$item_html		.= ' });</script>';
		echo $item_html;
	}
}
add_action('mbw_editor_'.$editor_type, 'mbw_editor_ck_template',5,2);
?>