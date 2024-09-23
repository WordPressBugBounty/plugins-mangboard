<?php
/**
 * Uninstall plugin
 */

// If uninstall not called from WordPress exit
if(!defined('WP_UNINSTALL_PLUGIN')){ exit; }

//실수에 의한 삭제를 방지하기 위해서는 아래 true 코드를 false 로 수정하거나 
//Uninstall.php 파일을 삭제하시기 바랍니다
//"true" 삭제 실행, "false" 삭제 방지
if(true)
{
	global $wpdb;	
	define("MBW_PLUGIN_PATH", plugin_dir_path(__FILE__));
	require(MBW_PLUGIN_PATH."includes/mb-config.php");

	//게시판 테이블 삭제
	$items		= $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE table_link="" and board_type!="admin"',$mb_admin_tables["board_options"]), ARRAY_A);
	foreach($items as $item){
		@$wpdb->query($wpdb->prepare('DROP TABLE IF EXISTS %1s;',$mb_table_prefix.$item["board_name"]));
		if($item["board_type"]=="board"){
			@$wpdb->query($wpdb->prepare('DROP TABLE IF EXISTS %1s;',$mb_table_prefix.$item["board_name"].$mb_table_comment_suffix));
		}
	}

	//관리자 테이블 삭제
	foreach($mb_admin_tables as $key=>$value){
		@$wpdb->query( "DROP TABLE IF EXISTS " . $mb_admin_tables[$key]);
	}
	delete_option('mb_user_synchronize_index');
	delete_option('mb_latest_board_data');
	delete_option('mb_latest_comment_data');

	//업로드 파일 삭제
	global $wp_filesystem;
	if ( is_object( $wp_filesystem ) ) {
		$delete_dir		= trailingslashit($wp_filesystem->find_folder(WP_CONTENT_DIR));
		$delete_dir		.= "uploads/mangboard/";
		if($delete_dir!="/" && strpos($delete_dir, '/mangboard')!==false && $wp_filesystem->is_dir($delete_dir)){
			$wp_filesystem->delete($delete_dir, true);
		}
	}
}
?>