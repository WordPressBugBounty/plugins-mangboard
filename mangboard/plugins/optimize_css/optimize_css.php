<?php
if(!function_exists('mbw_init_optimize_css')){
	function mbw_init_optimize_css(){
		if(mbw_get_trace("mbw_init_optimize_css")==""){
			mbw_add_trace("mbw_init_optimize_css");
			//loadStyle(MBW_PLUGIN_URL."plugins/optimize_css/css/style.css");
			$theme_array		= array('avada','enfold','the7','astra');
			$theme_name		= wp_get_theme();
			$name_array	= explode(' ',$theme_name);
			if(count($name_array)>1) $theme_name		= $name_array[0];
			$theme_name		= trim(strtolower($theme_name));
			if(in_array($theme_name, $theme_array)){
				loadStyle(MBW_PLUGIN_URL."plugins/optimize_css/css/".$theme_name.".css");
			}
		}
	}
}
add_action('wp_enqueue_scripts', 'mbw_init_optimize_css');
add_action('admin_enqueue_scripts', 'mbw_init_optimize_css');
?>