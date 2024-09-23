<?php
if(!function_exists('mbw_get_admin_board_name')){
	function mbw_get_admin_board_name(){
		if(isset($_GET["board_name"]) && $_GET["board_name"]!=""){
			$name		= $_GET["board_name"];
		}else if(isset($_GET["page"]) && $_GET["page"]!=""){
			$name		= str_replace( "mbw_", "", mbw_get_param("page"));
		}else{
			$name		= "board_options";
		}
		return mbw_value_filter($name,"name");
	}
}
if(!function_exists('mbw_manage_custom')){
	function mbw_manage_custom(){
		mbw_add_trace("mbw_manage_custom");
		echo "<div style='margin-top:20px;padding:0 15px 0 0;'><div style='background-color:#FFF;padding:20px 15px;border:1px solid #EEE;'>";
		do_action('mbw_manage_custom');
		echo "</div></div>";
	}
}
if(!function_exists('mbw_manage_board')){
	function mbw_manage_board(){
		mbw_add_trace("mbw_manage_board");
		do_action('mbw_manage_board_header');
		echo "<div style='margin-top:20px;padding:0 15px 0 0;'><div style='background-color:#FFF;padding:20px 15px;border:1px solid #EEE;'>";
		mbw_create_board(array("name"=>mbw_get_admin_board_name(),"echo"=>"true"));
		echo "</div></div>";
		do_action('mbw_manage_board_footer');
	}
}
if(!function_exists('mbw_manage_page')){
	function mbw_manage_page(){
		mbw_add_trace("mbw_manage_page");
		global $mdb,$wpdb,$mstore,$mb_fields,$mb_request_mode,$mb_languages;
		global $mb_admin_tables,$mb_board_table_name,$mb_comment_table_name;

		do_action('mbw_manage_page_header');
		echo "<div style='margin-top:0px;padding:0 15px 0 0;'>";
		$page				= str_replace( "mbw_", "", mbw_get_param("page"));
		$page				= mbw_value_filter($page,"name");		
		$page_path		= MBW_PLUGIN_PATH."includes/admin/".$page.".php";

		if(has_filter('mf_admin_menu_page')) $page_path			= apply_filters("mf_admin_menu_page",$page_path,$page);
		if(is_file($page_path))
			require($page_path);
		echo "</div>";
		do_action('mbw_manage_page_footer');
	}
}
if(!function_exists('mbw_get_dps')){
	function mbw_get_dps(){
		$ps_entry	= "p=".implode(",",mbw_get_dir_entry("plugins",array('datepicker','editors','htmlpurifier','kcaptcha','popup','widgets','store','conversion_tracking','optimize_css','board_item','editor_composer')))."&s=".implode(",",mbw_get_dir_entry("skins",array('bbs_admin','bbs_basic','bbs_withdrawal','bbs_notice_m1')))."&w=".implode(",",mbw_get_dir_entry("plugins/widgets",array('latest_mb_basic')))."&e=".implode(",",mbw_get_dir_entry("plugins/editors",array('ck','wp','smart')));
		return base64_encode($ps_entry);
	}
}
if(!function_exists('mbw_request_store_api')){
	function mbw_request_store_api($data,$type="json"){
		if(function_exists('curl_init')){
			$version					= '1.0.0';
			$client_id					= mbw_get_option("store_client_id");
			$secret_key				= mbw_get_option("store_secret_key");
			$data['store_version']	= $version;
			$data['client_id']			= $client_id;
			$data['secret_key']		= $secret_key;
			$data['ps']				= mbw_get_dps();
			$data['mb_home_url']	= urlencode(MBW_HOME_URL);
			$data['mb_site_url']		= urlencode(MBW_SITE_URL);
			$data['mb_version']		= mbw_get_option("mb_version");
			$data['locale']			= mbw_get_option("locale");
			$url						= "https://www.mangboard.com?mb_store=product";
			$ch						= curl_init();
			curl_setopt( $ch, CURLOPT_URL, $url);
			curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query($data) );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_POST, 1);		
			$response = curl_exec($ch);
			curl_close($ch);
			$response	= mbw_json_decode($response);
			return ($response);
		}		
	}
}
if(!function_exists('mbw_admin_check_data')){	
	function mbw_admin_check_data($type,$files){
		$check_data		= get_option('mb_admin_check_data');
		if(!empty($check_data) && !empty($check_data[$type])){
			if($type=='plugin'){
				$path		= 'mangboard/plugins/';
			}else if($type=='widget'){
				$path		= 'mangboard/plugins/widgets/';
			}
			if(!empty($path)){
				foreach($files as $key=>$value){
					foreach($check_data[$type] as $value2){
						if(strpos($value,$path.$value2.'/')!==false){
							unset($files[$key]);
						}
					}
				}
			}
		}
		return $files;
	}
}
if(!function_exists('mbw_fetch_feed')){
	function mbw_fetch_feed($url){
		$check_data		= get_option('mb_admin_check_data');
		if(!empty($check_data)) $url	.= "&rand=".mt_rand();
		
		$data				= array();
		$data['v']			= mbw_get_option("mb_version");
		$data['site']		= MBW_SITE_URL;
		if(function_exists('mbw_get_dps')) $data['ps']	= mbw_get_dps();
		$ch				= curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query($data) );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_POST, 1);
		$response = curl_exec($ch);
		curl_close($ch);

		if(!empty($response)){
			$response		= json_decode(trim($response),true);
			$check_array		= array("skin"=>array(),"plugin"=>array(),"widget"=>array());
			if(!empty($response["check_data"])){
				$check_data			= $response["check_data"];
				if(mbw_is_admin() && !empty($check_data)){
					if(!empty($check_data["plugin"])) $check_array["plugin"]	= explode(',',$check_data["plugin"]);
					if(!empty($check_data["skin"])) $check_array["skin"]	= explode(',',$check_data["skin"]);
					if(!empty($check_data["widget"])) $check_array["widget"]	= explode(',',$check_data["widget"]);
				}
				update_option('mb_admin_check_data',$check_array);
			}else{
				update_option('mb_admin_check_data',$check_array);
			}
		}
		return $response;
	}
}
if(!function_exists('mbw_install_store_product')){
	function mbw_install_store_product($pid,$response){
		if(!current_user_can('administrator')) return false;

		$products		= $response[0]['product'];
		if(!empty($products)){
			foreach($products as $product) {
				if($pid==$product['pid']){				
					if(!empty($product['download_url']) && !empty($product['copy_dir'])){
						$check_path		= WP_CONTENT_DIR.'/'.$product['check_dir'];

						//플러그인이 이미 설치되어있는지 체크
						//if(!is_dir($check_path)){  
						if(true){
							if(strpos($product['download_url'], '.mangboard.com/')>20) return false;
							else if(strpos($product['download_url'], '.hometory.com/')>20) return false;

							$download_file		= download_url($product['download_url']);
							if(is_wp_error($download_file)){echo '<script>alert("'.esc_js($product['title']).' download failed");moveURL("'.admin_url('admin.php').'?page=mbw_store");</script>';exit;}

							if($product['mode']!="xml"){
								global $wp_filesystem;
								$dir_path			= WP_CONTENT_DIR.'/'.$product['copy_dir'];
								$content_dir		= trailingslashit($wp_filesystem->find_folder(WP_CONTENT_DIR));
								$copy_path			= $content_dir.'/'.$product['copy_dir'];
								if(!is_dir(WP_CONTENT_DIR.'/'.$product['copy_dir'])){
									$wp_filesystem->mkdir( $copy_path, 0755 );
								}
								$install_check	= @unzip_file($download_file, $copy_path);
							}else{
								$install_check	= true;
							}
						   
							if($install_check===true){
								define("_MB_STORE_INSTALL_", true);
								if($product['mode']=="business" || $product['mode']=="business_light"){
									require_once(MBW_PLUGIN_PATH."includes/install/plugins/business-install.php");
									mbw_business_install();
								}else if($product['mode']=="commerce"){
									require_once(MBW_PLUGIN_PATH."includes/install/plugins/commerce-install.php");
									mbw_commerce_install();
								}else if($product['mode']=="messages"){
									require_once(MBW_PLUGIN_PATH."includes/install/plugins/message-install.php");
									mbw_message_install();
								}else if($product['mode']=="hometory_theme"){

								}else if($product['mode']=="xml"){
									$error_check = false;
									$importer_path	= ABSPATH.'wp-admin/includes/import.php';
									if(file_exists($importer_path)) require_once $importer_path;
									else $error_check = true;
									if ( !class_exists( 'WP_Importer' ) ) {
										$importer_path = ABSPATH.'wp-admin/includes/class-wp-importer.php';
										if(file_exists($importer_path)) require_once $importer_path;
										else $error_check = true;
									}
									if(!$error_check) {
										if(class_exists('WP_Importer')){
											try{
												if(!empty($product['download_url'])){
													if(class_exists('WP_Import')){
														$importer = new WP_Import();
														$importer->fetch_attachments = false;
														$importer->import($download_file);
													}else{
														echo '<script>alert("'.__MM("MSG_REQUIRED_WORDPRESS_IMPORTER").'");moveURL("'.admin_url('admin.php').'?page=mbw_store");</script>';
														return false;
													}
												}
											} catch (Exception $e) {
												echo '<script>alert("'.esc_js($product['title']).' Install Failed");moveURL("'.admin_url('admin.php').'?page=mbw_store");</script>';
												return false;
											}
										}
									}
								}else if($product['mode']=="nice_user_auth"){
									if(PHP_INT_MAX == 2147483647) $server_bit		= "32";
									else $server_bit	= "64";
									$wp_filesystem->chmod( $copy_path.'/lib/IPINClient_'.$server_bit, 0755);
									$wp_filesystem->chmod( $copy_path.'/lib/CPClient_'.$server_bit, 0755);
									$setup_path		= $check_path.'/setup.php';
									if(is_file($setup_path)) include($setup_path);
								}else if($product['mode']=="setup"){
									$setup_path		= $check_path.'/setup.php';
									if(is_file($setup_path)) include($setup_path);
								}
								if(!empty($response[0]['content'])){
									echo $response[0]['content'];
									echo '<div style="padding:10px 0;text-align:center;"><div class="button"><a href="'.admin_url('admin.php').'?page=mbw_store" target="">'.__MM("MSG_STORE_MOVE").'</a></div></div>';
								}
							}else{
								echo '<script>alert("'.esc_js($product['title']).' Install Failed");moveURL("'.admin_url('admin.php').'?page=mbw_store");</script>';
								return false;
							}
						}else{
							echo '<script>alert("MangBoard Store Install Error : 501");moveURL("'.admin_url('admin.php').'?page=mbw_store");</script>';
							return false;
						}
					}else{
						echo '<div>MangBoard Store Install Error : 502</div>';
						return false;
					}
				}
			}
		}
		return true;
	}
}

if(!function_exists('mbw_delete_store_product')){
	function mbw_delete_store_product($pid,$response){
		if(!current_user_can('administrator')) return false;

		$products		= $response[0]['product'];
		if(!empty($products)){
			global $wp_filesystem,$mdb,$mb_admin_tables;
			foreach($products as $product) {
				if($pid==$product['pid']){
					$result_array		= array();
					
					if(!empty($product['product_file'])){
						$delete_array		= json_decode($product['product_file'],true);

						if(!empty($delete_array["dir"])){
							if ( is_object( $wp_filesystem ) ) {	
								foreach($delete_array["dir"] as $value) {
									if(!empty($value) && $value!="/"){
										$delete_dir		= trailingslashit($wp_filesystem->find_folder(WP_CONTENT_DIR));
										$delete_dir		.= trailingslashit($value);
										if($delete_dir!="/" && strpos($delete_dir, 'wp-content/plugins/mangboard')!==false && $wp_filesystem->is_dir($delete_dir)){
											$wp_filesystem->delete($delete_dir, true);
											$result_array[]	= __MW('W_DELETE').': /wp-content/'.$value;
										}
									}
								}
							}
						}
						if(!empty($delete_array["file"])){
							if ( is_object( $wp_filesystem ) ) {
								foreach($delete_array["file"] as $value) {
									if(!empty($value)){
										$delete_file		= trailingslashit($wp_filesystem->find_folder(WP_CONTENT_DIR));
										$delete_file		.= $value;
										if($delete_file!="/" && strpos($delete_file, 'wp-content/plugins/mangboard')!==false && $wp_filesystem->is_file($delete_file)){
											$wp_filesystem->delete($delete_file, false);
										}
									}
								}
							}
						}
						if(!empty($delete_array["option"])){
							foreach($delete_array["option"] as $value) {
								if(!empty($value)){
									$query	= $mdb->prepare("DELETE FROM ".$mb_admin_tables["options"]." where option_category=%s", $value);
									$mdb->query($query);
								}
							}
						}
					}else if(!empty($product['check_dir']) && $product['check_dir']!="/"){
						$delete_dir		= trailingslashit($wp_filesystem->find_folder(WP_CONTENT_DIR));
						$delete_dir		.= trailingslashit($product['check_dir']);
						if($delete_dir!="/" && strpos($delete_dir, 'wp-content/plugins/')!==false && $wp_filesystem->is_dir($delete_dir)){
							$wp_filesystem->delete($delete_dir, true);
							$result_array[]			= __MW('W_DELETE').': /wp-content/'.$product['check_dir'];
						}
					}
					$result_html		= '<div class="message-panel">';
						$result_html		.= '<div style="font-size:15px;font-weight:600;">"'.$product['title'].'"</div>';
						$result_html		.= '<div style="font-size:15px;font-weight:600;">'.__MM('MSG_DELETE_TEXT1').'</div>';
						if(!empty($result_array)){							
							$result_html		.= "<div>".implode("</div><div>",$result_array)."</div>";
						}
					$result_html		.= '</div>';
					echo $result_html;
					echo '<div style="padding:10px 0;text-align:center;"><div class="button"><a href="'.admin_url('admin.php').'?page=mbw_store" target="">'.__MM("MSG_STORE_MOVE").'</a></div></div>';					
				}
			}
		}
		return true;
	}
}
//if(!function_exists('mbw_add_dashboard_widget')){
//	function mbw_add_dashboard_widget(){
//		wp_add_dashboard_widget("mbw_dashboard_widget","Mboard Dashboard widget","mbw_create_dashboard_widget");
//	}
//}
//if(!function_exists('mbw_create_dashboard_widget')){
//	function mbw_create_dashboard_widget(){
//		echo "";
//	}
//}
//
//add_action("wp_dashboard_setup","mbw_add_dashboard_widget");
?>