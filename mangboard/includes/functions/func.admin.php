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
		$name	= mbw_value_filter($name,"name");
		mbw_set_param("board_name",$name);
		return $name;
	}
}
if(!function_exists('mbw_manage_custom')){
	function mbw_manage_custom(){
		mbw_add_trace("mbw_manage_custom");
		echo '<div class="mb-admin-custom" style="margin-top:20px;padding:0 15px 0 0;"><div style="background-color:#FFF;padding:20px 15px;border:1px solid #EEE;overflow-x:auto;">';
			do_action('mbw_manage_custom');
		echo '</div></div>';
	}
}
if(!function_exists('mbw_manage_board')){
	function mbw_manage_board(){
		mbw_add_trace("mbw_manage_board");
		do_action('mbw_manage_board_header');
		echo '<div class="mb-admin-board" style="margin-top:20px;padding:0 15px 0 0;"><div style="background-color:#FFF;padding:20px 15px;border:1px solid #EEE;overflow-x:auto;">';
			mbw_create_board(array("name"=>mbw_get_admin_board_name(),"echo"=>"true"));
		echo '</div></div>';
		do_action('mbw_manage_board_footer');
	}
}
if(!function_exists('mbw_manage_page')){
	function mbw_manage_page(){
		mbw_add_trace("mbw_manage_page");
		global $mdb,$wpdb,$mstore,$mb_fields,$mb_request_mode,$mb_languages;
		global $mb_admin_tables,$mb_board_table_name,$mb_comment_table_name;

		do_action('mbw_manage_page_header');
		echo '<div class="mb-admin-page" style="margin-top:0px;padding:0 15px 0 0;">';
			$page				= str_replace( "mbw_", "", mbw_get_param("page"));
			$page				= mbw_value_filter($page,"name");
			
			if ( mbw_get_option("store_path") !="" ) {
				$template_path	= WP_CONTENT_DIR.mbw_get_option("store_path");
				if(!is_file($template_path."includes/admin/".$page.".php")){
					$template_path	= MBW_PLUGIN_PATH;
				}
			} else {
				$template_path	= MBW_PLUGIN_PATH;
			}		
			$page_path		= $template_path."includes/admin/".$page.".php";

			if(has_filter('mf_admin_menu_page')){
				$page_path			= apply_filters("mf_admin_menu_page",$page_path,$page);
			}
			if(is_file($page_path)){
				require($page_path);
			}
		echo '</div>';
		do_action('mbw_manage_page_footer');
	}
}

if(!function_exists('mbw_install_add_board_options')){
	function mbw_install_add_board_options($options,$name="mb_board_options"){
		$insert_prefix		= "INSERT INTO `%1s` (`board_name`, `description`, `skin_name`, `model_name`, `table_link`, `mobile_skin_name`, `board_header`, `board_footer`, `board_content_form`, `editor_type`, `api_type`, `page_size`, `comment_size`, `block_size`, `category_type`, `category_data`, `use_board_vote_good`, `use_board_vote_bad`, `use_comment`, `use_comment_vote_good`, `use_comment_vote_bad`, `use_secret`, `use_notice`, `use_list_title`, `use_list_search`, `list_level`, `view_level`, `write_level`, `reply_level`, `delete_level`, `modify_level`, `secret_level`, `manage_level`, `comment_level`, `point_board_read`, `point_board_write`, `point_board_reply`, `point_comment_write`, `board_type`, `reg_date`, `is_show`) VALUES ";
		mbw_install_insert_query($insert_prefix,$options,$name,"board_name");
	}
}
if(!function_exists('mbw_install_add_options')){
	function mbw_install_add_options($options,$name="mb_options"){
		$insert_prefix		= "INSERT INTO `%1s` (`option_load`, `option_category`, `option_title`, `option_name`, `option_value`, `option_data`, `option_label`, `option_class`, `option_style`, `option_event`, `option_attribute`, `option_type`, `description`) VALUES ";
		mbw_install_insert_query($insert_prefix,$options,$name,"option_name");
	}
}
if(!function_exists('mbw_install_add_options2')){
	function mbw_install_add_options2($options,$name="mb_options"){
		$insert_prefix		= "INSERT INTO `%1s` (`option_load`, `option_category`, `option_title`, `option_name`, `option_value`, `option_data`, `option_label`, `option_class`, `option_style`, `option_event`, `option_attribute`, `option_type`, `description`, `is_show`) VALUES ";
		mbw_install_insert_query($insert_prefix,$options,$name,"option_name");
	}
}
if(!function_exists('mbw_install_insert_query')){
	function mbw_install_insert_query($insert_prefix,$options,$name="",$field=""){
		if(!empty($name) && !empty($options)){
			global $wpdb;
			foreach($options as $key=>$option){
				$row_check			= 0;
				if(!empty($field)){
					$row_check		= intval($wpdb->get_var($wpdb->prepare('SELECT count(*) from %1s where %1s=%s;',$name, $field, $key)));
				}
				if($row_check==0){
					$query	= $wpdb->prepare($insert_prefix.$option,$name);					
					$check	= $wpdb->query($query);
					if(!$check){ $wpdb->query($query); }
				}
			}
		}
	}
}
if(!function_exists('mbw_get_dps')){
	function mbw_get_dps(){
		$ps_entry	= "c=".get_option("mb_install_product")."&p=".implode(",",mbw_get_dir_entry("plugins",array('datepicker','editors','htmlpurifier','kcaptcha','popup','widgets','store','conversion_tracking','optimize_css','board_item','editor_composer')))."&s=".implode(",",mbw_get_dir_entry("skins",array('bbs_admin','bbs_basic','bbs_withdrawal','bbs_notice_m1')))."&w=".implode(",",mbw_get_dir_entry("plugins/widgets",array('latest_mb_basic')))."&e=".implode(",",mbw_get_dir_entry("plugins/editors",array('ck','wp','smart')));
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
			$data['php_version']	= PHP_VERSION;
			$data['locale']			= mbw_get_option("locale");
			$url						= "https://mangboard.com?mb_store=product";
			$ch						= curl_init();
			curl_setopt( $ch, CURLOPT_URL, $url);
			curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query($data) );
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true);
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
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true);
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
				update_option('mb_admin_check_data', $check_array, false);
			}else{
				update_option('mb_admin_check_data', $check_array, false);
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
			global $wp_filesystem;
			$install_path		= MBW_PLUGIN_PATH;
			$base_path		= preg_replace( '#/plugins/mangboard/?$#', '', $install_path );
			// wp_filesystem 객체 함수를 이용하기 위해서는 절대경로를 find_folder를 이용하여 public_html로 시작하는 상대경로로 변경 필요
			// wp_filesystem->find_folder: /home/demo/public_html =>/public_html
			$base_dir			= $wp_filesystem->find_folder($base_path);
			// mbw_get_option("store_path") 함수는 설정된 경로의 폴더가 존재하지 않을 경우 빈값으로 설정되기 때문에 MBW_STORE_DIR 를 이용하여 가져옴
			if ( mbw_get_option("use_store_path") ) {
				$store_path		= MBW_STORE_DIR;
			} else {
				$store_path		= "";
			}
			
			foreach($products as $product) {
				if($pid==$product['pid']){				
					if(!empty($product['download_url']) && !empty($product['copy_dir'])){						
						$check_path		= $base_path.'/'.$product['check_dir'];

						// 플러그인이 설치되어있는지 체크해서 설치되어 있을 경우에만 설치
						// if(!is_dir($check_path)){  //업데이트 목적의 재설치를 위해 주석처리
						if(true){
							if ( strpos($product['download_url'], 'https://mangboard.com/')===false && strpos($product['download_url'], 'https://hometory.com/')===false && strpos($product['download_url'], '.mangboard.com/')===false && strpos($product['download_url'], '.hometory.com/')===false ) {
								return false;
							} else if ( strpos($product['download_url'], 'mangboard.com/')>20 ) {
								return false;
							} else if ( strpos($product['download_url'], 'hometory.com/')>20 ) {
								return false;
							}

							$download_file		= download_url($product['download_url']);
							if(is_wp_error($download_file)){echo '<script>alert("'.esc_js($product['title']).' download failed");moveURL("'.esc_url(admin_url('admin.php')).'?page=mbw_store");</script>';exit;}
							
							$install_check	= false;
							if ( $product['mode'] != "xml" ) {
								if ( is_object( $wp_filesystem ) && !empty($product['copy_dir']) ) {
									if ( !empty($store_path) && strpos($product['copy_dir'],'plugins/mangboard') === 0 ) {
										$install_path		= wp_normalize_path($base_path.$store_path);
										$check_path		= $install_path.substr($product['check_dir'], 18);
										if ( $product['copy_dir'] == "plugins/mangboard" ) {
											$copy_dir		= $base_dir.ltrim($store_path, '/\\');
										}else{
											$copy_dir		= $base_dir.ltrim($store_path, '/\\').substr($product['check_dir'], 18);
										}										
									} else {
										$copy_dir		= $base_dir.$product['copy_dir'];
									}
									$copy_dir		= wp_normalize_path($copy_dir);
									// 스토어 설치 폴더 생성
									if ( !is_dir($install_path) ) {
										mbw_store_path_mkdir($base_path, $store_path);
									}
									// 스토어 상품 폴더 생성
									if ( !$wp_filesystem->is_dir($copy_dir) ) {
										$chmod			= defined( 'FS_CHMOD_DIR' ) ? FS_CHMOD_DIR : 0755;
										$wp_filesystem->mkdir( $copy_dir, $chmod );
									}
									if ( $wp_filesystem->is_dir($copy_dir) ) {
										$install_check	= @unzip_file($download_file, $copy_dir);										
									}
								}
							}else{
								$install_check	= true;
							}
						   
							if ( $install_check === true ) {
								define("_MB_STORE_INSTALL_", true);
								if($product['mode']=="business" || $product['mode']=="business_light"){
									require_once($install_path."includes/install/plugins/business-install.php");
									mbw_business_install();
								}else if($product['mode']=="commerce"){
									require_once($install_path."includes/install/plugins/commerce-install.php");
									mbw_commerce_install();
								}else if($product['mode']=="messages"){
									require_once($install_path."includes/install/plugins/message-install.php");
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
														echo '<script>alert("'.__MM("MSG_REQUIRED_WORDPRESS_IMPORTER").'");moveURL("'.esc_url(admin_url('admin.php')).'?page=mbw_store");</script>'; // phpcs:ignore
														return false;
													}
												}
											} catch (Exception $e) {
												echo '<script>alert("'.esc_js($product['title']).' Install Failed");moveURL("'.esc_url(admin_url('admin.php')).'?page=mbw_store");</script>';
												return false;
											}
										}
									}
								}else if($product['mode']=="nice_user_auth"){
									if ( is_file($check_path.'/setup.php') ) {
										include($check_path.'/setup.php');
									}
								}else if($product['mode']=="setup"){									
									if ( is_file($check_path.'/setup.php') ) {
										include($check_path.'/setup.php');
									}									
								}
								if(!empty($response[0]['content'])){
									echo $response[0]['content']; // phpcs:ignore
									echo '<div style="padding:10px 0;text-align:center;"><div class="button"><a href="'.esc_url(admin_url('admin.php')).'?page=mbw_store" target="">'.__MM("MSG_STORE_MOVE").'</a></div></div>'; // phpcs:ignore
								}
							}else{
								echo '<script>alert("'.esc_js($product['title']).' Install Failed");moveURL("'.esc_url(admin_url('admin.php')).'?page=mbw_store");</script>';
								return false;
							}
						}else{
							echo '<script>alert("MangBoard Store Install Error : 501");moveURL("'.esc_url(admin_url('admin.php')).'?page=mbw_store");</script>';
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
//스토어 설치 저장경로 폴더 생성
if(!function_exists('mbw_store_path_mkdir')){
	function mbw_store_path_mkdir($base_path, $store_path, $chmod = false){
		global $wp_filesystem;
		$base_dir			= trailingslashit($wp_filesystem->find_folder($base_path)).ltrim($store_path, '/\\');
		$chmod			= $chmod ?: ( defined( 'FS_CHMOD_DIR' ) ? FS_CHMOD_DIR : 0755 );
		if ( strpos($base_dir, 'wp-content') !== false ) {		
			$wp_filesystem->mkdir( $base_dir, $chmod );
			$wp_filesystem->mkdir( $base_dir."api", $chmod );
			$wp_filesystem->mkdir( $base_dir."models", $chmod );
			$wp_filesystem->mkdir( $base_dir."plugins", $chmod );
			$wp_filesystem->mkdir( $base_dir."plugins/widgets", $chmod );
			$wp_filesystem->mkdir( $base_dir."skins", $chmod );
			$wp_filesystem->mkdir( $base_dir."templates", $chmod );
				
			if ( $wp_filesystem->is_dir($base_dir) ) {
				$target_path	= $base_path.$store_path;
				$plugin_code = "<?php\n" .
				"/**\n" .
				" * Plugin Name: MangBoard Store\n" .
				" * Plugin URI: https://mangboard.com/\n" .
				" * Description: MangBoard Store 기능을 이용하여 설치된 상품파일 저장 및 관리\n" .
				" * Version: 1.0.3\n" .
				" * Author: Hometory\n" .
				" * Author URI: https://www.hometory.com/\n" .
				" */\n\n" .
				"\n?>";
				$bytes_written = file_put_contents( $target_path.'index.php', $plugin_code, LOCK_EX );
			}
		}
	}		
}
//스토어 상품 설치 폴더 확인 (mangboard 폴더와 store_path에 설정된 폴더)
if(!function_exists('mbw_check_store_dir')){	
	function mbw_check_store_dir($path){
		if ( empty($path) ) return false;
	
		if ( is_dir(WP_CONTENT_DIR.'/'.$path) ) {
			return true;
		} else if ( mbw_get_option("store_path") !="" && strpos($path,'plugins/mangboard') === 0 && is_dir(WP_CONTENT_DIR.mbw_get_option("store_path").substr($path, 18)) ) {
			return true;
		} else {
			return false;
		}
	}
}

if(!function_exists('mbw_delete_store_product')){
	function mbw_delete_store_product($pid,$response){
		if(!current_user_can('administrator')) return false;
		$products		= $response[0]['product'];
		if(!empty($products)){
			global $wp_filesystem,$mdb,$mb_admin_tables;			
			$base_path		= WP_CONTENT_DIR;
			$store_path		= mbw_get_option("store_path");
						
			foreach($products as $product) {
				if($pid==$product['pid']){
					$result_array		= array();
					
					if(!empty($product['product_file'])){
						$delete_array		= json_decode($product['product_file'],true);

						if(!empty($delete_array["dir"])){
							if ( is_object( $wp_filesystem ) ) {									
								
								foreach($delete_array["dir"] as $value) {
									if ( !empty($value) && $value != "/" ) {										
										// store_path 가 설정되어 있을 경우 해당 경로에서 삭제 폴더가 존재하는지 확인 후 삭제
										if ( !empty($store_path) && strpos($value,'plugins/mangboard') === 0 ) {
											$delete_path		= $base_path.$store_path.substr($value, 18);
											if ( !is_dir($delete_path) ) {
												$delete_path		= $base_path.'/'.$value;
											}
										} else {
											// mangboard 폴더에 설치되어 있을 경우
											$delete_path		= $base_path.'/'.$value;
										}
										$delete_path		= wp_normalize_path($delete_path);
										$delete_dir		= $wp_filesystem->find_folder($delete_path);
										if ( !empty($delete_dir) ) {
											if(strpos($delete_dir, 'wp-content')!==false && $wp_filesystem->is_dir($delete_dir)){
												$wp_filesystem->delete($delete_dir, true);
												$result_array[]			= __MW('W_DELETE').': '.$delete_dir;
											}
										}
									}
								}
							}
						}
						if(!empty($delete_array["file"])){
							if ( is_object( $wp_filesystem ) ) {
								foreach($delete_array["file"] as $value) {
									if(!empty($value)){										
										// store_path 가 설정되어 있을 경우 해당 경로에서 삭제 폴더가 존재하는지 확인 후 삭제
										if ( !empty($store_path) && strpos($value,'plugins/mangboard') === 0 ) {
											$delete_path		= $base_path.$store_path.substr($value, 18);
											if ( !is_file($delete_path) ) {
												$delete_path		= $base_path.'/'.$value;
											}
										} else {
											// mangboard 폴더에 설치되어 있을 경우
											$delete_path		= $base_path.'/'.$value;
										}
										$delete_path	= wp_normalize_path($delete_path);
										$delete_dir	= $wp_filesystem->find_folder( dirname( $delete_path ) );
										if ( !empty($delete_dir) ) {											
											$delete_file	= trailingslashit($delete_dir).basename( $delete_path );
											if(strpos($delete_file, 'wp-content')!==false && $wp_filesystem->is_file($delete_file)){
												$wp_filesystem->delete( $delete_file );
												$result_array[]		= __MW('W_DELETE').': '.$delete_file;
											}
										}
									}
								}								
							}
						}
						if ( !empty($delete_array["option"]) ) {
							foreach($delete_array["option"] as $value) {
								if ( !empty($value) ) {
									$query	= $mdb->prepare("DELETE FROM ".$mb_admin_tables["options"]." where option_category=%s", $value);
									$mdb->query($query);
								}
							}
						}
					} else if( !empty($product['check_dir']) && $product['check_dir'] != "/" ){
						// store_path 가 설정되어 있을 경우 해당 경로에서 삭제 폴더가 존재하는지 확인 후 삭제
						if ( !empty($store_path) && strpos($product['check_dir'],'plugins/mangboard') === 0 ) {
							$delete_path		= $base_path.$store_path.substr($product['check_dir'], 18);
							if ( !is_dir($delete_path) ) {
								$delete_path		= $base_path.'/'.$product['check_dir'];
							}
						} else {
							// mangboard 폴더에 설치되어 있을 경우
							$delete_path		= $base_path.'/'.$product['check_dir'];
						}
						$delete_path		= wp_normalize_path($delete_path);
						$delete_dir		= $wp_filesystem->find_folder($delete_path);
						if ( !empty($delete_dir) ) {
							if(strpos($delete_dir, 'wp-content')!==false && $wp_filesystem->is_dir($delete_dir)){
								$wp_filesystem->delete($delete_dir, true);
								$result_array[]			= __MW('W_DELETE').': '.$delete_dir;
							}
						}
					}
					$result_html		= '<div class="message-panel">';
						$result_html		.= '<div style="font-size:15px;font-weight:600;">"'.$product['title'].'"</div>';
						$result_html		.= '<div style="font-size:15px;font-weight:600;">'.__MM('MSG_DELETE_TEXT1').'</div>';
						if(!empty($result_array)){							
							$result_html		.= "<div>".implode("</div><div>",$result_array)."</div>";
						}
					$result_html		.= '</div>';
					echo $result_html; // phpcs:ignore
					echo '<div style="padding:10px 0;text-align:center;"><div class="button"><a href="'.esc_url(admin_url('admin.php')).'?page=mbw_store" target="">'.__MM("MSG_STORE_MOVE").'</a></div></div>'; // phpcs:ignore
				}
			}
		}
		return true;
	}
}

if(!function_exists('mbw_template_api_admin_table_data')){	
	function mbw_template_api_admin_table_data(){
		if(mbw_get_param("mode")=="plugin"){			
			if(mbw_get_param("board_action")=="admin_table_data"){
				if(mbw_is_admin()){//start

					global $mstore,$mdb,$mb_fields,$mb_board_table_name,$mb_comment_table_name;

					$table_data		= "";
					if(mbw_is_admin()) {
						$api_fields				= $mb_fields["select_board"];

						$select_pid				= mbw_get_param("select_pid");
						$select_pid				= mbw_value_filter($select_pid);
						if(!empty($select_pid)){
							$pid_format			= array();
							$pid_array			= explode(",", $select_pid);
							foreach($pid_array as $value) $pid_format[]		= "%d";
							$select_query				= mbw_get_add_query(array("column"=>"*","join"=>"none")).$mdb->prepare(' where pid in ('.implode(",",$pid_format).') order by pid desc', $pid_array);
						}else{
							$select_query				= mbw_get_add_query(array("column"=>"*","join"=>"none"), "where", "order")." limit 0, 5000";
						}
						$items						= $mdb->get_results($select_query,ARRAY_A);

						$table_data		= "<table>";
						$table_array		= array();
						$table_item		= array();
						$table_fields		= explode(",", mbw_get_param("fields"));
						$table_titles		= explode(",", mbw_get_param("titles"));

						if(count($items)> 0){
							$table_data			.= '<tr>';
							foreach($table_titles	 as $title){
								$table_data			= $table_data.'<td style="width:100px;border:solid 3px #EEE;">'.$title.'</td>';
								$table_item[]			= $title;
							}			
							$table_data			.= '</tr>';
							$table_array[]		= $table_item;
							foreach($items as $item){
								$table_data			.= '<tr>';
								$table_item			= array();

								foreach($table_fields as $field){	
									if($field=='fn_home_address'){
										$table_data			.= '<td style="width:100px;border:solid 0.5px #EEE;">'.mbw_htmlspecialchars_decode($item[$api_fields['fn_home_address1']].' '.$item[$api_fields['fn_home_address2']]).'</td>';
										$table_item[]			= mbw_htmlspecialchars_decode($item[$api_fields['fn_home_address1']].' '.$item[$api_fields['fn_home_address2']]);
									}else if(strpos($field,'fn_')===0){
										$table_data			.= '<td style="width:100px;border:solid 0.5px #EEE;">'.mbw_htmlspecialchars_decode($item[$api_fields[$field]]).'</td>';
										$table_item[]			= mbw_htmlspecialchars_decode($item[$api_fields[$field]]);
									}else{
										$table_data			.= '<td style="width:100px;border:solid 0.5px #EEE;">'.$field.'</td>';
										$table_item[]			= $field;
									}					
								}
								$table_data			.= '</tr>';
								$table_array[]		= $table_item;
							}
						}			
						$table_data		.= "</table>";
					}
					$admin_data			= $table_data;
					if(has_filter('mf_admin_table_data')){
						$admin_data		= apply_filters("mf_admin_table_data",$admin_data, $table_array);
					}					
					$mstore->set_result_data(array("data"=>$admin_data));					
					
				}//end
			}
		}
	}
}
add_action('mbw_template_api_header', 'mbw_template_api_admin_table_data');

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