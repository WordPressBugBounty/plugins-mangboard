<?php
if(!function_exists('mbw_init_popup')){	
	function mbw_init_popup(){
		loadScript(MBW_PLUGIN_URL."plugins/popup/js/main.js");
		$device_type		= mbw_get_vars("device_type");
		if($device_type=="desktop"){
			loadStyle(MBW_PLUGIN_URL."plugins/popup/css/style.css");
		}else if($device_type=="tablet"){
			loadStyle(MBW_PLUGIN_URL."plugins/popup/css/style.css");
		}else if($device_type=="mobile"){
			loadStyle(MBW_PLUGIN_URL."plugins/popup/css/style.css");
		}	
	}
}
add_action('wp_enqueue_scripts', 'mbw_init_popup');
add_action('admin_enqueue_scripts', 'mbw_init_popup');
?>