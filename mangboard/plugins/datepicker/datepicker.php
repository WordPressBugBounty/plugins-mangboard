<?php
if(!function_exists('mbw_init_datepicker')){
	function mbw_init_datepicker(){
		$plugin_url	= plugins_url('', __FILE__)."/";
		loadStyle($plugin_url."css/style.css");
		loadScript($plugin_url."js/datepicker.js");
		mbw_enqueue_style('jquery-ui-css', $plugin_url."css/jquery-ui.min.css");
		if( mbw_is_admin_page() ) {
			wp_enqueue_style('jquery-ui-css');
		}
	}
}
add_action('wp_enqueue_scripts', 'mbw_init_datepicker');
add_action('admin_enqueue_scripts', 'mbw_init_datepicker');
?>