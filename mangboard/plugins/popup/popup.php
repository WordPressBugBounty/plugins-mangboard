<?php
if(!function_exists('mbw_init_popup')){
	function mbw_init_popup(){
		$plugin_url	= plugins_url('', __FILE__)."/";
		loadScript($plugin_url."js/main.js");
		$device_type		= mbw_get_vars("device_type");
		if($device_type=="desktop"){
			loadStyle($plugin_url."css/style.css");
		}else if($device_type=="tablet"){
			loadStyle($plugin_url."css/style.css");
		}else if($device_type=="mobile"){
			loadStyle($plugin_url."css/style.css");
		}	
	}
}
add_action('wp_enqueue_scripts', 'mbw_init_popup');
add_action('admin_enqueue_scripts', 'mbw_init_popup');
?>