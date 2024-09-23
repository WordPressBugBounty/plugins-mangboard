<?php
if(!function_exists('mbw_admin_store_page')){
	function mbw_admin_store_page($path,$page){
		if($page=="store"){
			$store_path		= MBW_PLUGIN_PATH."plugins/store/store_page.php";
			return $store_path;
		}else{
			return $path;
		}		
	}
}
add_filter('mf_admin_menu_page', 'mbw_admin_store_page',5,2); 

if(!function_exists('mbw_add_store_menu')){
	function mbw_add_store_menu(){
		add_submenu_page('mbw_dashboard', 'STORE', 'STORE', 'administrator', 'mbw_store', 'mbw_manage_page');
	}
}
add_action('admin_menu', 'mbw_add_store_menu',38);
?>