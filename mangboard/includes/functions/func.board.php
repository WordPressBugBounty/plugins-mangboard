<?php
if(!function_exists('mbw_hash_password')){
	function mbw_hash_password($password){
		global $wp_hasher;
		if ( empty( $wp_hasher ) ) {
			require_once ABSPATH . WPINC . '/class-phpass.php';
			$wp_hasher = new PasswordHash( 8, true );
		}
		return $wp_hasher->HashPassword(mbw_htmlspecialchars_decode(trim($password)));
	}
}
if(!function_exists('mbw_check_password')){
	function mbw_check_password($password,$hash){
		if(wp_check_password($password,$hash)) return true;
		else return false;
	}
}
if(!function_exists('mbw_get_hash_key')){
	function mbw_get_hash_key($tag,$expiration=0,$uid="",$token=""){
		global $mstore,$mdb,$mb_admin_tables,$mb_fields;
		$hash				= "";
		if(!empty($token))  $access_toekn		= $token;
		else $access_toekn		= mbw_generate_access_token();
		
		$auth_key			= md5(MBW_AUTH_SITE_URL).$access_toekn;
		if(($expiration+intval(mbw_get_option("cookie_expire")))<mbw_get_timestamp()){
			return "";
		}

		global $current_user;
		$user_id		= "";
		if(get_current_user_id()!=0){
			$user_id		= $current_user->data->user_login;
		}else if($mstore->is_login_cookie()){
			$cookie = $mstore->get_login_cookie();
			$cookie_elements = explode('|', $cookie);
			list($user_id, $expiration2, $hmac, $user_mode) = $cookie_elements;
			if(empty($expiration)) $expiration		= $expiration2;
		}else if($uid!=""){
			$user_id		= $uid;
		}
		if($user_id!=""){
			$user_auth_key	= $mdb->get_var($mdb->prepare("select ".$mb_fields["users"]["fn_user_auth_key"]." from `".$mb_admin_tables["users"]."` where `".$mb_fields["users"]["fn_user_id"]."`=%s;", $user_id));
		}else{
			$user_id			= "guest";
			$user_auth_key	= "guest";
		}	
		$tmp_tag		= $tag;
		if($tag=="nonce" && mbw_get_board_name()!=""){
			global $mb_table_prefix;
			$table_name		= mbw_get_board_table_name(mbw_get_board_name());			
			$table_name		= substr($table_name,strlen($mb_table_prefix));
			$name_array		= explode('_', $table_name);
			if(count($name_array)>2) $table_name		= $name_array[0]."_".$name_array[1];
			$tmp_tag			= $tag.'_'.$table_name;
		}
		$key			= md5($user_id . $user_auth_key . '|' . $expiration.$auth_key.'|'.$tmp_tag);
		$hash		= hash_hmac('md5', $user_id . '|' . $expiration, $key);
		if($tag=="nonce"){
			mbw_set_vars("nonce_hash",$hash);
			mbw_set_vars("nonce_expiration",$expiration);		
		}
		return $hash;
	}
}

if(!function_exists('mbw_check_request_size')){
	function mbw_check_request_size(){
		if(!empty($_SERVER['CONTENT_LENGTH'])){
			global $mstore;
			$content_length		= $_SERVER['CONTENT_LENGTH'];
			$upload_max			= mbw_convert_to_bytes(ini_get("upload_max_filesize"));
			$post_max				= mbw_convert_to_bytes(ini_get("post_max_size"));
			if($post_max<$upload_max) $upload_max		= $post_max;

			if($upload_max<$content_length){			
				mbw_error_message("MSG_UPLOAD_SIZE_ERROR", mbw_convert_to_bytes($upload_max,'mb'),"1503");

				if(mbw_get_param('mode')=='basic' && mbw_get_param('action')=='mb_uploader'){
					echo $mstore->get_result_data("message");
				}else{
					echo mbw_data_encode($mstore->result_data);	
				}	
				exit;			
			}			
		}
	}
}
if(!function_exists('mbw_verify_nonce')){
	function mbw_verify_nonce(){
		if(mbw_get_option("anti_spam_protection")===0) return true;
		if(mbw_get_param("mb_nonce_value")!="" && mbw_get_param("mb_nonce_time")!=""){
			if(mbw_is_admin()) return true;			

			$board_name		= mbw_get_board_name();			
			$hash1				= mbw_get_hash_key("nonce",mbw_get_param("mb_nonce_time"));
			if(mbw_get_param("mb_nonce_value")==$hash1) return true;

			if(mbw_get_param("mb_nonce_token")!=""){
				$hash1			= mbw_get_hash_key("nonce",mbw_get_param("mb_nonce_time"),"",mbw_get_param("mb_nonce_token"));
				if(mbw_get_param("mb_nonce_value")==$hash1) return true;
			}

			$table_name		= mbw_get_board_table_name($board_name);
			if(mbw_get_param('wp_nonce_value')!="" && wp_verify_nonce(mbw_get_param('wp_nonce_value'), 'mbw_api_nonce'.$table_name)) return true;

			if(empty($board_name)){
				if(mbw_get_param('mode')=="user" || mbw_get_param('board_action')=="login"){
					global $mstore;		
					$board_name		= "users";
					$mstore->set_board_name($board_name);

					$hash2				= mbw_get_hash_key("nonce",mbw_get_param("mb_nonce_time"));
					if(mbw_get_param("mb_nonce_value")==$hash2) return true;
					$table_name		= mbw_get_board_table_name($board_name);
					if(mbw_get_param('wp_nonce_value')!="" && wp_verify_nonce(mbw_get_param('wp_nonce_value'), 'mbw_api_nonce'.$table_name)) return true;
				}
			}
			if(mbw_get_param('wp_nonce_value')!="" && wp_verify_nonce(mbw_get_param('wp_nonce_value'), 'mbw_api_nonce')) return true;
		}
		return false;	
	}
}

if(!function_exists('mbw_create_nonce')){
	function mbw_create_nonce($type,$name=""){
		global $mstore,$current_user;

		$board_name		= $name;
		$time					= mbw_get_timestamp();
		if(empty($board_name)) {
			$board_name		= mbw_get_board_name();
			$hash				= mbw_get_hash_key("nonce",$time);
		}else{
			$temp_name		= mbw_get_board_name();
			$mstore->set_board_name($board_name);
			$hash				= mbw_get_hash_key("nonce",$time);
			$mstore->set_board_name($temp_name);
		}
		$table_name		= mbw_get_board_table_name($board_name);
		$nonce				= $hash;

		if(mbw_get_param("lang")!=""){
			$lang		= mbw_get_param("lang");
		}else{
			$lang		= mbw_get_option("locale");
		}
		if($type=="form"){
			$result			= '<input type="hidden" name="mb_nonce_value" value="' . $nonce . '" />';
			$result			.= '<input type="hidden" name="mb_nonce_time" value="' . $time . '" />';
			$result			.= wp_nonce_field("mbw_api_nonce".$table_name,"wp_nonce_value",true,false);
			if(mbw_get_param("idx")!=""){
				$result			.= '<input type="hidden" name="mb_idx" value="'.mbw_get_param("idx").'" />';
			}
			if(!empty($lang)){
				$result			.= '<input type="hidden" name="lang" value="'.$lang.'" />';
			}
		}else if($type=="param"){
			$result			= "mb_nonce_value=".$nonce."&mb_nonce_time=".$time."&wp_nonce_value=".wp_create_nonce("mbw_api_nonce".$table_name);
			if(mbw_get_param("idx")!=""){
				$result			.= '&mb_idx='.mbw_get_param("idx");
			}
			if(!empty($lang)){
				$result			.= '&lang='.$lang;
			}
		}
		return $result;
	}
}

if(!function_exists('mbw_get_file_name')){
	function mbw_get_file_name($pid,$name){
		$file_name		= "F".$pid."_".$name;
		if(has_filter('mf_board_file_name')) $file_name			= apply_filters("mf_board_file_name",$file_name);
		return $file_name;
	}
}

if(!function_exists('mbw_get_file_url')){
	function mbw_get_file_url(){
		$security_mode		= intval(mbw_get_cookie("mb_security_mode"));
		if($security_mode==2){
			$file_url		= MBW_PLUGIN_URL."includes/mb-file.php";
		}else{
			$file_url		= mbw_check_url(MBW_HOME_URL)."/?mb_ext=file";
		}
		return $file_url;
	}
}
if(!function_exists('mbw_get_image_url')){
	function mbw_get_image_url($type="",$data=""){
		$param			= "";		
		$image_url		= mbw_get_file_url();

		if(strpos($type, 'url')===0){
			if(strpos($data, 'http') === 0) return $data;
			$upload_url			= MBW_CONTENT_URL."/uploads/mangboard/";
			if(strpos($upload_url, 'http') !== 0){
				if(strpos($upload_url, '/wp-content') === 0){
					$upload_url		= MBW_SITE_URL.$upload_url;
				}
			}
			$file_path			= $data;
			$file_name			= basename($file_path);
			$file_name2		= $file_name;

			if($type=="url_small" || $type=="url_middle"){
				$image_type		= "small";
				if($type=="url_middle")	$image_type		= "middle";
				$temp_name		= (substr($file_name,0,strrpos($file_name, "."))."_".$image_type.substr($file_name,strrpos($file_name, "."),strlen($file_name)));
				$temp_path		= str_replace($file_name, $temp_name, $file_path);
				if(is_file(MBW_UPLOAD_PATH.$temp_path)) $file_name2		= $temp_name;
			}
			$file_path			= str_replace($file_name, rawurlencode($file_name2), $file_path);
			$image_url			= $upload_url.$file_path;
		}else{
			if($type=="download"){
				if(strpos($image_url, '?') === false)	$image_url		.= "?";
				else $image_url		.= "&";
				$image_url		.= "type=download&pid=".$data;
			}else if($type!=""){
				if(strpos($image_url, '?') === false)	$image_url		.= "?";
				else $image_url		.= "&";
				$image_url		.= $type."=".rawurlencode($data);
			}
		}		

		if(has_filter('mf_board_image_url')) $image_url			= apply_filters("mf_board_image_url",$image_url, $type, $data);
		return esc_url_raw($image_url);
	}
}

if(!function_exists('mbw_replace_image_url')){
	function mbw_replace_image_url($content,$image_type="url"){
		$url_data1		= array();
		$url_data2		= array();

		$index1			= 0;
		$file_url			= MBW_HOME_URL."/?mb_ext=file&amp;path=";
		$file_url			= str_replace(array("http://","https://"), "", $file_url);

		while(($index1= strpos($content,$file_url,$index1))!==false){
			$start_index		= $index1;		
			$end_index			= strpos($content,'"',$index1);
			if(empty($end_index)) break;

			$image_url			= substr($content,$start_index,$end_index-$start_index);
			$url					= mbw_get_image_url($image_type,urldecode(str_replace($file_url, "", $image_url)));
			$url_data1[]		= $image_url;			
			$url_data2[]		= str_replace(array("http://","https://"), "", $url);
			$index1				= $end_index;
		}

		//차후에 삭제
		$index1			= 0;
		$file_url			= MBW_PLUGIN_URL."includes/mb-file.php?path=";
		$file_url			= str_replace(array("http://","https://"), "", $file_url);

		while(($index1= strpos($content,$file_url,$index1))!==false){
			$start_index		= $index1;		
			$end_index			= strpos($content,'"',$index1);		
			if(empty($end_index)) break;

			$image_url			= substr($content,$start_index,$end_index-$start_index);
			$url					= mbw_get_image_url($image_type,urldecode(str_replace($file_url, "", $image_url)));
			$url_data1[]		= $image_url;			
			$url_data2[]		= str_replace(array("http://","https://"), "", $url);
			$index1				= $end_index;
		} //end

		if(!empty($url_data1)){
			if(mbw_is_ssl()){
				$site_url			= str_replace(array("http://","https://"), "", MBW_SITE_URL);
				$url_data1[]	= 'http://'.$site_url.'/';
				$url_data2[]	= 'https://'.$site_url.'/';
			}
			$content		= str_replace($url_data1, $url_data2, $content);
		}
		return $content;
	}
}

if(!function_exists('mbw_set_params')){
	function mbw_set_params(){		
		if(mbw_get_trace("mbw_set_params")==""){
			mbw_add_trace("mbw_set_params");
			global $mb_check_set_param;
			if(!$mb_check_set_param){
				global $mstore,$mb_basic_params;

				foreach($mb_basic_params as $key => $value){
					mbw_set_param($key,$value);
				}
				if(!empty($_GET["vid"])){
					$mstore->set_params(array('mode'=>'view','board_pid'=>intval($_GET["vid"])));
				}
				if(!empty($_GET["stag"])){
					$mstore->set_params(array('search_field'=>'fn_tag','search_text'=>mbw_htmlspecialchars_decode($_GET["stag"])));
				}else if(!empty($_GET["sid"])){
					$mstore->set_params(array('search_field'=>'fn_pid','search_text'=>intval($_GET["sid"])));
				}else if(!empty($_GET["suid"])){
					$mstore->set_params(array('search_field'=>'fn_user_pid','search_text'=>intval($_GET["suid"])));
				}
				if(!empty($_GET["mp"])){
					$mstore->set_params(mbw_get_decryption($_GET["mp"]));
				}
				if(!empty($_GET)){
					$mstore->set_params($_GET);					
				}
				if(mbw_get_request_mode()!="API"){
					$mstore->check_get_param();
				}
				if(!empty($_POST)){
					$mstore->set_params($_POST);
				}
				if(!empty($_GET) || !empty($_POST)){
					$mstore->check_post_param();
				}
				
				if(mbw_get_param("redirect_to")!=""){
					if(function_exists('wp_validate_redirect')){
						mbw_set_param("redirect_to",mbw_validate_redirect(mbw_get_param("redirect_to")));
					}
				}
				
				if(mbw_get_param("board_name")!=""){
					$args		= array();
					if(mbw_get_request_mode()=="API"){						
						if(!empty($_SERVER['HTTP_REFERER'])){
							$parse_url		= parse_url($_SERVER['HTTP_REFERER']);
							if(!empty($parse_url['path'])){
								$url				= $parse_url['path'];
							}else{
								$url				= "";
							}
						}else if(mbw_get_param("_wp_http_referer")!=""){
							$parse_url		= parse_url(mbw_get_param("_wp_http_referer"));
							if(!empty($parse_url['path'])){
								$url				= $parse_url['path'];
							}else{
								$url				= "";
							}
						}
						$board_name		= mbw_get_param("board_name");
						$board_name		= mbw_value_filter($board_name,"name");
						$post_content		= "";
						if(!empty($url)){
							$page		= get_page_by_path($url);
							if(!empty($page->post_content)){
								$content			= $page->post_content;
								if(strpos($content,'['.MBW_SHORTCODE_BOARD." name=\"".$board_name)!==false){
									$post_content		= $content;
								}
							}
						}
						//페이지에 망보드 숏코드가 검색되지 않을 경우 게시판과 연결된 포스트에서 숏코드를 한번 더 검색
						if(empty($post_content) && mbw_get_param("load_type")==""){
							$mstore->set_board_name($board_name);
							$mstore->set_board_options($board_name);
							$post_id				= mbw_get_board_option("fn_post_id");
							if(!empty($post_id)){
								$content		= get_post($post_id)->post_content;
								if(strpos($content,'['.MBW_SHORTCODE_BOARD." name=\"".$board_name)!==false){
									$post_content		= $content;
								}
							}
						}
						if(!empty($post_content)){
							$index1					= strpos($post_content,'['.MBW_SHORTCODE_BOARD)+strlen('['.MBW_SHORTCODE_BOARD)+1;
							$index2					= strpos($post_content,']',$index1);
							$shortcode_atts		= trim(substr($post_content,$index1,$index2-$index1));
							$args						= shortcode_parse_atts($shortcode_atts);
						}
					}				
					if(!empty($args)){
						mbw_set_board_params($args);
					}else{
						mbw_set_board_params();
					}					
				}else{
					//쿠키 정보가 있으면 회원정보 세팅
					if(mbw_validate_auth_cookie()){	
						mbw_set_wp_user_data();
					}
				}
				$mb_check_set_param		= true;				
			}
		}
	}
}

if(!function_exists('mbw_set_shortcode_where')){
	function mbw_set_shortcode_where($args=NULL,$is_reset=false){
		if(mbw_get_trace("mbw_set_shortcode_where")=="" || $is_reset){
			mbw_add_trace("mbw_set_shortcode_where");
			if(!empty($args['search_field'])){
				if(function_exists('mbw_set_search_field')) mbw_set_search_field($args);
			}
			if(!empty($args['where'])){
				$where_data			= explode(",",$args['where']);
				if(!empty($args['where_level'])){
					$sign						= "<=";
					$user_level				= mbw_get_user("fn_user_level");
					if(strpos($args['where_level'],'_')!==false){
						$level_data			= explode("_",$args['where_level']);
						$check_level		= intval($level_data[0]);
						if(!empty($level_data[1])) $sign		= mbw_htmlspecialchars_decode(trim($level_data[1]));
					}else{
						$check_level			= intval($args['where_level']);
					}
					if($sign=="="){
						if($user_level!=$check_level) return;
					}else if($sign==">"){
						if($user_level<=$check_level) return;
					}else if($sign==">="){
						if($user_level<$check_level) return;
					}else if($sign=="<"){
						if($user_level>=$check_level) return;
					}else if($sign=="<="){
						if($user_level>$check_level) return;
					}
				}
				foreach($where_data as $item){
					if($item=="user_post1"){
						mbw_set_board_where(array("field"=>"fn_user_pid", "value"=>mbw_get_user("fn_pid")));
					}else if($item=="user_post2"){
						mbw_set_board_where(array("field"=>"fn_user_pid", "value"=>mbw_get_user("fn_pid"), "prefix"=>"(", "operator"=>"or"));
						mbw_set_board_where(array("field"=>"fn_parent_user_pid", "value"=>mbw_get_user("fn_pid"), "suffix"=>")"));
					}else if($item=="not_reply"){
						mbw_set_board_where(array("field"=>"fn_reply", "value"=>"0"));
					}
				}
			}			
		}
	}
}
add_action('mbw_board_shortcode_atts', 'mbw_set_shortcode_where', 10, 2);
if(!function_exists('mbw_set_shortcode_options')){
	function mbw_set_shortcode_options($args=NULL,$is_reset=false){
		if(mbw_get_trace("mbw_set_shortcode_options")=="" || $is_reset){
			$param_array	= array('category_data','category_type','page_size','comment_size','block_size','use_secret','use_notice','use_list_title','use_list_search','use_board_vote_good','use_board_vote_bad','use_comment','use_comment_vote_good','use_comment_vote_bad','ext1','ext2','ext3','ext4','ext5');
			$device_type		= mbw_get_vars("device_type");
			foreach($param_array as $name){
				if(isset($args[$device_type.'_'.$name])){
					mbw_set_board_option('fn_'.$name,esc_attr($args[$device_type.'_'.$name]));
				}else if(isset($args[$name])){
					mbw_set_board_option('fn_'.$name,esc_attr($args[$name]));
				}
			}
			$board_mode	= mbw_value_filter(mbw_get_param("mode"),"name");
			if(isset($args[$device_type.'_'.$board_mode.'_layout_type'])){
				mbw_set_vars($device_type.'_'.$board_mode.'_layout_type',mbw_value_filter($args[$device_type.'_'.$board_mode.'_layout_type'],"name"));
				mbw_set_vars($board_mode.'_layout_type',mbw_value_filter($args[$device_type.'_'.$board_mode.'_layout_type'],"name"));
				if(isset($args[$device_type.'_'.$board_mode.'_layout_class'])){
					mbw_set_vars($device_type.'_'.$board_mode.'_layout_class',mbw_value_filter($args[$device_type.'_'.$board_mode.'_layout_class']));
					mbw_set_vars($board_mode.'_layout_class',mbw_value_filter($args[$device_type.'_'.$board_mode.'_layout_class']));
				}
			}else if(isset($args[$board_mode.'_layout_type'])){
				mbw_set_vars($board_mode."_layout_type",mbw_value_filter($args[$board_mode.'_layout_type'],"name"));
				if(isset($args[$board_mode.'_layout_class'])){
					mbw_set_vars($board_mode."_layout_class",mbw_value_filter($args[$board_mode.'_layout_class']));
				}
			}
			if(!empty($args['order_by']) && empty($_REQUEST["order_by"])){
				mbw_set_param("order_by", mbw_value_filter($args['order_by'],"name"));
				if(!empty($args['order_type']) && empty($_REQUEST["order_type"])){
					mbw_set_param("order_type", mbw_value_filter($args['order_type']));
				}
				if(function_exists('mbw_init_board_order')){ mbw_init_board_order(); }
				if(mbw_get_param("order_by")!="" && mbw_get_param("order_type")!=""){
					mbw_set_board_order(array(mbw_get_param("order_by")=>mbw_get_param("order_type"), "fn_pid"=>"desc"));
				}
			}
			if(!empty($args['gallery_image_type'])){
				if(!empty($args['list_type']) && $args['list_type']=='gallery'){
					if($args['gallery_image_type']=='all'){
						mbw_set_board_where(array("field"=>"fn_image_path", "mode"=>"delete"));
					}else if($args['gallery_image_type']=='image'){
						mbw_set_board_where(array("field"=>"fn_image_path", "value"=>"", "sign"=>"!="));
					}
				}
			}
			if($board_mode=='write'){
				mbw_set_board_where(array("field"=>"fn_category1", "mode"=>"delete"));
				mbw_set_board_where(array("field"=>"fn_category2", "mode"=>"delete"));
				mbw_set_board_where(array("field"=>"fn_category3", "mode"=>"delete"));
			}			
		}
	}
}
add_action('mbw_board_shortcode_atts', 'mbw_set_shortcode_options', 20, 2);
if(!function_exists('mbw_set_board_params')){
	function mbw_set_board_params($args=NULL,$is_reset=false){		
		if(mbw_get_trace("mbw_set_board_params")=="" || $is_reset){
			mbw_add_trace("mbw_set_board_params");
			global $mstore;
			global $mb_check_set_board_param;

			$board_name		= "";
			if(mbw_get_board_name()!=""){
				$board_name			= mbw_get_board_name();
			}
			if(isset($args)){
				if(isset($args['name'])){
					if(empty($board_name)) $board_name		= trim($args['name']);
					else if($is_reset) {
						$board_name		= trim($args['name']);
						if(isset($args['list_type'])) mbw_set_param("list_type",mbw_value_filter($args['list_type'],"name"));
					}
				}
				if(isset($args['skin'])){
					mbw_set_param("skin_name",mbw_value_filter($args['skin'],"name"));
				}
				if(mbw_get_param("mode")=="" && isset($args['mode'])){
					mbw_set_param("mode",mbw_value_filter($args['mode'],"name"));
				}
			}else{
				if(empty($board_name) && mbw_get_param("board_name")!=""){
					$board_name		= mbw_get_param("board_name");
				}
			}

			$board_name	= mbw_value_filter($board_name,"name");
			mbw_set_param("board_name",$board_name);
			$mstore->set_board_name($board_name);
			
			if(!empty($board_name)){
				mbw_init_options($board_name,$is_reset);
			}			
			$mstore->set_result_data(array("mode"=>mbw_get_param("mode"),"board_action"=>mbw_get_param("board_action")));		
		}
		if(!empty($args)){
			$shortcode_args		= mbw_get_vars("shortcode_args");
			if(empty($shortcode_args)){
				if(has_filter('mf_board_shortcode_atts')) $args			= apply_filters("mf_board_shortcode_atts",$args);
				mbw_set_vars("shortcode_args",$args);
				do_action( 'mbw_board_shortcode_atts',$args,$is_reset);
			}
		}
	}
}
if(!function_exists('mbw_get_board_table_name')){
	function mbw_get_board_table_name($board_name,$mode="board",$type=""){
		$name	= mbw_value_filter($board_name,"name");
		if($name == mbw_get_board_option("fn_board_name2") && mbw_get_board_option("fn_table_link")!="") $name	= mbw_get_board_option("fn_table_link");
		return mbw_get_table_name($name,$mode,$type);
	}
}
if(!function_exists('mbw_get_table_name')){
	function mbw_get_table_name($board_name,$mode="board",$type=""){
		if(empty($board_name)) return "";
		global $mdb,$mstore,$mb_admin_tables,$mb_fields;
		global $mb_table_prefix,$mb_table_board_suffix,$mb_table_comment_suffix;			

		if(!empty($type)) $board_type		= $type;
		else $board_type		= $mstore->get_board_type($board_name);

		if($mode!="comment" && $board_type=="admin" && !empty($mb_admin_tables[$board_name]))
			return $mb_admin_tables[$board_name];

		if($mode=="comment"){
			if($board_type=="custom"){
				return $board_name.$mb_table_comment_suffix;
			}else if($board_type=="link"){
				$name	= $mdb->get_var($mdb->prepare("SELECT ".$mb_fields["board_options"]["fn_table_link"]." FROM ".$mb_admin_tables["board_options"]." where ".$mb_fields["board_options"]["fn_board_name2"]."=%s limit 1", $board_name));
				if(empty($name)) $name		= $board_name;
				return $mb_table_prefix.$name.$mb_table_comment_suffix;
			}else{
				return $mb_table_prefix.$board_name.$mb_table_comment_suffix;
			}			
		}else{
			if($board_type=="custom"){
				return $board_name;
			}else if($board_type=="link"){
				$name	= $mdb->get_var($mdb->prepare("SELECT ".$mb_fields["board_options"]["fn_table_link"]." FROM ".$mb_admin_tables["board_options"]." where ".$mb_fields["board_options"]["fn_board_name2"]."=%s limit 1", $board_name));
				if(empty($name)) $name		= $board_name;
				return $mb_table_prefix.$name;
			}else {
				return $mb_table_prefix.$board_name;
			}
		}
	}
}
if(!function_exists('mbw_get_editors')){
	function mbw_get_editors(){
		global $mb_editors;
		return $mb_editors;
	}
}

if(!function_exists('mbw_get_request_mode')){
	function mbw_get_request_mode(){
		global $mb_request_mode;
		return $mb_request_mode;
	}
}

if(!function_exists('mbw_set_fields')){
	function mbw_set_fields($type,$fields){
		global $mb_fields;
		$mb_fields[$type]		= $fields;
	}
}
if(!function_exists('mbw_get_fields')){
	function mbw_get_fields($type){
		global $mb_fields;
		if(!empty($mb_fields[$type])) return $mb_fields[$type];
		return "";
	}
}


if(!function_exists('mbw_get_table_list')){
	function mbw_get_table_list($type=""){
		global $mdb,$mb_fields,$mb_admin_tables;
		$type			= mbw_value_filter($type,"name");
		if(empty($type)){
			$query	= "select ".$mb_fields["board_options"]["fn_board_name2"]." from ".$mb_admin_tables["board_options"]." where ".$mb_fields["board_options"]["fn_table_link"]."='' and ".$mb_fields["board_options"]["fn_is_show"]."='1';";
		}else if($type=="all"){
			$query	= "select ".$mb_fields["board_options"]["fn_board_name2"]." from ".$mb_admin_tables["board_options"]." where ".$mb_fields["board_options"]["fn_table_link"]."='' or ".$mb_fields["board_options"]["fn_board_type"]."='custom'";
		}else{
			$query	= $mdb->prepare("select ".$mb_fields["board_options"]["fn_board_name2"]." from ".$mb_admin_tables["board_options"]." where ".$mb_fields["board_options"]["fn_board_type"]."=%s and ".$mb_fields["board_options"]["fn_table_link"]."='' and ".$mb_fields["board_options"]["fn_is_show"]."='1';",$type);
		}
		$items				= $mdb->get_results($query,ARRAY_A);
		$board_list		= array();
		foreach($items as $item){
			$board_list[]		= $item[$mb_fields["board_options"]["fn_board_name2"]];
		}
		return $board_list;
	}
}

if(!function_exists('mbw_get_board_skin')){
	function mbw_get_board_skin($board_name){
		global $mdb,$mb_fields,$mb_admin_tables;
		$skin_name		= $mdb->get_var($mdb->prepare("select ".$mb_fields["board_options"]["fn_skin_name"]." from ".$mb_admin_tables["board_options"]." where `".$mb_fields["board_options"]["fn_board_name2"]."`=%s limit 1", $board_name));
		return $skin_name;
	}
}

//스킨 모델 파일 설정
if(!function_exists('mbw_get_model_field')){
	function mbw_get_model_field($board_name,$mode="board"){
		global $mstore;

		$skin_name		= mbw_get_board_skin($board_name);
		$skin_path		= MBW_PLUGIN_PATH."skin/".$skin_name."/";

		if(is_file($skin_path."includes/".$board_name."-fields.php")) require($skin_path."includes/".$board_name."-fields.php");
		else if(is_file($skin_path."includes/skin-fields.php")) require($skin_path."includes/skin-fields.php");
		else require(MBW_PLUGIN_PATH."includes/mb-fields.php");

		if($mode=="board" && !empty($mb_fields["board"])){		
			return $mb_fields["board"];
		}else if($mode=="comment" && !empty($mb_fields["comment"])){
			return $mb_fields["comment"];
		}
		return array();
	}
}

if(!function_exists('mbw_init_options')){
	function mbw_init_options($board_name,$is_reset=false){		
		mbw_add_trace("mbw_init_options");		
		global $mstore,$mdb,$mb_fields;
		global $mb_admin_tables,$mb_board_table_name,$mb_comment_table_name;		
		global $mb_vars,$mb_words,$mb_request_mode;
	
		if(empty($board_name)) return;	
		 
		//게시판 옵션 설정
		$mstore->set_board_options($board_name,$is_reset);
		$mb_board_table_name				= mbw_get_board_table_name($board_name);

		//게시판 테이블이 존재 하는지 확인
		if($mb_board_table_name!="none" && !$mstore->table_exists($mb_board_table_name)){
			mbw_error_message("MSG_EXIST_ERROR2", array($mb_board_table_name,"Table"),"1301");			
		}else{		
			//댓글 테이블이 존재 하는지 확인
			if(mbw_get_board_option("fn_use_comment") == 1){			
				$mb_comment_table_name		= mbw_get_board_table_name($board_name,"comment");
				if(!$mstore->table_exists($mb_comment_table_name)) mbw_error_message("MSG_EXIST_ERROR2", array($mb_comment_table_name,"Table"),"1301");
			}
			if(!empty($mb_fields["select_board"])){
				$select_fields		= $mb_fields["select_board"];
			}else{
				$select_fields		= $mb_fields["board"];
			}
			
			//공지사항 검색 설정
			/*
			if(!empty($select_fields["fn_is_notice"]) && intval(mbw_get_board_option("fn_use_notice")) == 0){
				mbw_set_board_where(array("field"=>"fn_is_notice", "value"=>"0", "sign"=>"="));	
			}
			*/

			mbw_set_category_params();
			
			if(!empty($select_fields["fn_user_name"]) && mbw_get_param("search_name")!=""){
				mbw_set_board_where(array("field"=>"fn_user_name", "value"=>mbw_get_param("search_name"), "sign"=>"="));
			}

			//날짜 검색 파라미터 설정
			if(mbw_get_param("date_field")=="")
				$date_field		= "fn_reg_date";
			else 
				$date_field		= mbw_get_param("date_field");

			if(!empty($select_fields[$date_field])){
				if(mbw_get_param("search_date")!=""){
					mbw_set_board_where(array("field"=>$date_field, "value"=>mbw_get_param("search_date")." %", "sign"=>"like"));
				}else if(mbw_get_param("start_date")!=""){
					mbw_set_board_where(array("field"=>$date_field, "value"=>mbw_get_param("start_date")."", "sign"=>">="));
					if(mbw_get_param("end_date")!=""){
						mbw_set_board_where(array("field"=>str_replace('fn_',"",$date_field), "value"=>mbw_get_param("end_date")." 23:59:59", "sign"=>"<="));
					}
				}else if(mbw_get_param("search_year")!=""){
					$write_date			= mbw_get_param("search_year");

					if(mbw_get_param("search_month")!=""){
						if(mbw_get_param("search_day")!=""){
							$write_date			= mbw_get_param("search_year")."-".mbw_get_param("search_month")."-".mbw_get_param("search_day");
						}else{
							$write_date			= mbw_get_param("search_year")."-".mbw_get_param("search_month");
						}
					}else{
						$write_date			= mbw_get_param("search_year");
					}
					mbw_set_board_where(array("field"=>$date_field, "value"=>$write_date."%", "sign"=>"like"));
				}else if(mbw_get_param("search_month")!=""){
					if(mbw_get_param("search_day")!=""){
						$write_date			= mbw_get_param("search_month")."-".mbw_get_param("search_day");
					}else{
						$write_date			= "-".mbw_get_param("search_month")."-";
					}
					mbw_set_board_where(array("field"=>$date_field, "value"=>"%".$write_date."%", "sign"=>"like"));
				}else if(mbw_get_param("search_day")!=""){
					mbw_set_board_where(array("field"=>$date_field, "value"=>"%-".mbw_get_param("search_day")." %", "sign"=>"like"));
				}
			}
			
			//검색 키워드 설정
			$board_mode	= mbw_get_param("mode");
			$is_search		= false;
			if($board_mode!='write'){
				if(mbw_get_param("search_field")!="" && mbw_get_param("search_text")!="" && !empty($select_fields[mbw_get_param("search_field")])){
					$sign					= "like";
					$search_field		= mbw_get_param("search_field");
					$search_text		= mbw_htmlspecialchars(mbw_get_param("search_text"));
					if($search_field=="pid" || strpos($search_field,'_pid')!==false){
						$sign		= "=";
					}else{
						$search_text		= "%".$search_text."%";
					}
					mbw_set_board_where(array("field"=>$search_field, "value"=>$search_text, "sign"=>$sign));
					$is_search		= true;
				}
				$i	= 1;
				for($i;$i<=3;$i++){
					if(mbw_get_param("search_add_field".$i)!="" && mbw_get_param("search_add_text".$i)!="" && !empty($select_fields[mbw_get_param("search_add_field".$i)])){
						mbw_set_board_where(array("field"=>mbw_get_param("search_add_field".$i), "value"=>"%".mbw_htmlspecialchars(mbw_get_param("search_add_text".$i))."%", "sign"=>"like"));
						$is_search		= true;
					}
				}
				if(mbw_get_param("se_field1")!=""){
					$i	= 1;
					for($i;$i<=5;$i++){
						if(mbw_get_param("se_field".$i)!="" && mbw_get_param("se_text".$i)!="" && !empty($select_fields[mbw_get_param("se_field".$i)])){						
							$se_text		= mbw_htmlspecialchars(mbw_get_param("se_text".$i));
							$se_field		= mbw_get_param("se_field".$i);
							if(strpos($se_text, ',') !== false){ $is_multi		= '1'; }
							else{ $is_multi		= '0'; }
							$se_sign		= mbw_get_param("se_sign".$i);
							if(!empty($se_sign)){
								if($se_sign=='lt') $se_sign	= '<';
								else if($se_sign=='lt2') $se_sign	= '<=';
								else if($se_sign=='gt') $se_sign	= '>';
								else if($se_sign=='gt2') $se_sign	= '>=';
								else if($se_sign=='like'){
									if($is_multi=='1'){
										$se_text		= '%'.str_replace(',', '%,%', $se_text).'%';
									}else{
										$se_text		= '%'.$se_text.'%';
									}
								}else $se_sign	= '=';
							}else{
								$se_sign	= '=';
							}
							mbw_set_board_where(array("field"=>$se_field, "value"=>$se_text, "sign"=> $se_sign, "multi"=>$is_multi));
							$is_search		= true;
						}
					}
				}
				if($is_search && isset($select_fields["fn_is_secret"])){
					$mb_user_level	= mbw_get_user("fn_user_level");
					if(intval(mbw_get_board_option("fn_secret_level")) > $mb_user_level && !mbw_is_admin()){
						mbw_set_board_where(array("field"=>'fn_is_secret', "value"=>0));
					}
				}
			}

			//정렬 기능 설정
			if(mbw_get_param("order_by")=="fn_pid"){			
				if(!empty($select_fields["fn_is_notice"]) && !empty($select_fields["fn_gid"]) && mbw_get_board_option("fn_board_type")!="admin"){   
					mbw_set_board_order(array("fn_gid"=>mbw_get_param("order_type"), "fn_reply"=>"asc"));
				}else if(!empty($select_fields["fn_gid"]) && mbw_get_board_option("fn_board_type")!="admin"){
					mbw_set_board_order(array("fn_gid"=>mbw_get_param("order_type"), "fn_reply"=>"asc"));
				}else{
					if(mbw_get_param("order_by")!="" && mbw_get_param("order_type")!=""){
						mbw_set_board_order(array(mbw_get_param("order_by")=>mbw_get_param("order_type"), "fn_pid"=>"desc"));
					}
				}			
			}else{
				if(mbw_get_param("order_by")!="" && mbw_get_param("order_type")!=""){
					mbw_set_board_order(array(mbw_get_param("order_by")=>mbw_get_param("order_type"), "fn_pid"=>"desc"));
				}
			}
		}
	}
}

if(!function_exists('mbw_set_category_params')){
	function mbw_set_category_params(){		
		if(true){
			//mbw_add_trace("mbw_set_category_params");
			global $mb_fields;
			//카테고리 검색 설정
			$category_fields		= mbw_get_category_fields();
			$category_index		= 1;
			if(!empty($mb_fields["select_board"])){
				$select_fields		= $mb_fields["select_board"];
			}else{
				$select_fields		= $mb_fields["board"];
			}
			foreach($category_fields as $field){
				if(!empty($select_fields[$field]) && mbw_get_param("category".$category_index)!=""){
					mbw_set_board_where(array("field"=>$field, "value"=>mbw_htmlspecialchars(mbw_get_param("category".$category_index)), "sign"=>"="));
				}
				$category_index++;
			}
		}
	}
}
if(!function_exists('mbw_set_search_field')){
	function mbw_set_search_field($args){
		if(!empty($args["search_field"]) && (mbw_get_param("mode")=='list' || mbw_get_param("board_action")=='load')){
			if(!empty($args["search_text"])){
				mbw_set_board_where(array("field"=>mbw_value_filter($args["search_field"],"name"), "value"=>"%".$args["search_text"]."%", "sign"=>"like"));
			}else if(!empty($args["search_value"])){
				mbw_set_board_where(array("field"=>mbw_value_filter($args["search_field"],"name"), "value"=>$args["search_value"], "sign"=>"="));
			}
		}
	}
}
if(!function_exists('mbw_get_board_class')){
	function mbw_get_board_class(){
		$board_class	= "";
		if(mbw_is_login()) $mb_user_level	= mbw_get_user("fn_user_level");
		else $mb_user_level	= 0;
		$board_class		= 'mb-level-'.$mb_user_level;

		$board_name	= mbw_get_board_name();
		if(!empty($board_name)){
			$board_class		.= ' mb-name-'.$board_name;
		}
		$board_mode	= mbw_get_param("mode");
		if(!empty($board_mode)){
			$board_class		.= ' mb-mode-'.$board_mode;
		}
		$device_type		= mbw_get_vars("device_type");
		if(mbw_get_vars($device_type.'_'.$board_mode."_layout_type")!=""){
			$layout_type			= mbw_get_vars($device_type.'_'.$board_mode."_layout_type");
		}else{
			$layout_type			= mbw_get_vars($board_mode."_layout_type");
		}
		if(!empty($layout_type)){
			if($device_type=="mobile" && mbw_get_vars($device_type.'_'.$board_mode."_layout_type")==""){
				$layout_type		= 'responsive-box4';				
			}
			if(strpos($layout_type,'responsive-box')===0){
				$board_class		.= ' mb-layout-responsive-box mb-layout-'.$layout_type;
			}
		}
		if(mbw_get_vars($device_type.'_'.$board_mode."_layout_class")!=""){
			$layout_type			= mbw_get_vars($device_type.'_'.$board_mode."_layout_type");
			$layout_class			= mbw_get_vars($device_type.'_'.$board_mode."_layout_class");
		}else{
			$layout_class			= mbw_get_vars($board_mode."_layout_class");
		}
		
		if(!empty($layout_class)){			
			$board_class		.= " ".$layout_class;
		}

		$skin_name		= mbw_get_board_option("fn_skin_name");
		if(mbw_get_vars("device_type")=="mobile" && mbw_get_board_option("fn_mobile_skin_name")!="") $skin_name		= mbw_get_board_option("fn_mobile_skin_name");
		if(!empty($skin_name)){
			$board_class		.= ' mb-skin-'.$skin_name;
		}
		$board_class	= str_replace('_', '-', $board_class);
		if(has_filter('mf_board_class')) $board_class			= apply_filters("mf_board_class",$board_class);
		return $board_class;
	}
}

//레벨 권한 체크하기
if(!function_exists('mbw_is_permission_level')){
	function mbw_is_permission_level(){
		if(mbw_is_admin() || mbw_get_board_name()=="") return true;

		global $mstore,$mb_languages;
		mbw_add_trace("mbw_is_permission_level");

		//레벨 권한 체크
		$permission_level						= 0;
		$permission_name					= __MW("W_ACCESS");
		$mb_user_level						= mbw_get_user("fn_user_level");		

		if(mbw_get_param("mode")=="view"){
			$permission_level					= mbw_get_board_option("fn_view_level");
			$permission_name				= __MW("W_VIEW");
			
			//자기글일 경우 보기 허용
			if(mbw_is_user_pid("permission")) $permission_level		= 1;
			
		}else if(mbw_get_param("mode")=="write"){

			if(mbw_get_param("board_action")=="write"){
				$permission_level				= mbw_get_board_option("fn_write_level");
				$permission_name			= __MW("W_WRITE");
			}else if(mbw_get_param("board_action")=="modify" || mbw_get_param("board_action")=="copy"){
				$permission_level				= intval(mbw_get_board_option("fn_write_level"));
				$permission_name			= __MW("W_MODIFY");
				$view_level						= intval(mbw_get_board_option("fn_view_level"));
				if($permission_level<$view_level)	$permission_level		= $view_level;
				//자기글일 경우 수정 허용
				if(mbw_is_user_pid("permission")) $permission_level		= 1;
			}else if(mbw_get_param("board_action")=="reply"){
				$permission_level				= mbw_get_board_option("fn_reply_level");
				$permission_name			= __MW("W_REPLY_WRITE");
			}else{
				$permission_level				= mbw_get_board_option("fn_write_level");
				$permission_name			= __MW("W_WRITE");
			}
		}else if(mbw_get_param("board_action")=="multi_modify"){
			$permission_level					= mbw_get_board_option("fn_modify_level");
			$permission_name				= __MW("W_MODIFY");
		}else if(mbw_get_param("board_action")=="multi_move" || mbw_get_param("board_action")=="multi_copy"){
			$permission_level					= mbw_get_board_option("fn_manage_level");
			$permission_name				= __MW("W_COPY");
		}else if(mbw_get_param("board_action")=="multi_delete"){
			$permission_level					= mbw_get_board_option("fn_delete_level");
			$permission_name				= __MW("W_DELETE");
		}else if(mbw_get_param("mode")=="search" && mbw_get_param("board_action")=="load"){
			$permission_level				= 0;
			$permission_name				= __MW("W_SEARCH");
		}else if(mbw_get_param("mode")=="plugin" && (mbw_get_param("action")=="mb_template" || mbw_get_param("action")=="skin_mb_template")){
			$permission_level				= 0;
			$permission_name				= __MW("W_ACCESS");
		}else if(mbw_get_param("board_action")=="delete"){
			//자기글일 경우 삭제 허용
			if(mbw_is_user_pid("permission")) $permission_level		= 1;
		}else if(mbw_get_param("mode")=="comment"){
			$comment_level			= intval(mbw_get_board_option("fn_comment_level"));
			$view_level					= intval(mbw_get_board_option("fn_view_level"));
			if($comment_level<$view_level)	$comment_level		= $view_level;

			if(mbw_get_param("board_action")=="write"){
				$permission_level				= $comment_level;
				$permission_name			= __MW("W_COMMENT_WRITE");
			}else if(mbw_get_param("board_action")=="reply"){
				$permission_level				= $comment_level;
				$permission_name			= __MW("W_COMMENT_WRITE");
			}else if(mbw_get_param("board_action")=="modify"){
				$permission_level				= $comment_level;
				$permission_name			= __MW("W_COMMENT_WRITE");
			}else{
				$permission_level				= $view_level;
				$permission_name			= __MW("W_VIEW");
			}
		}else if(mbw_get_param("mode")=="user"){
			if(mbw_get_param("board_action")=="login" || mbw_get_param("board_action")=="user_login" || mbw_get_param("board_action")=="logout"){
				$permission_level						= 0;
			}else if(mbw_get_param("board_action")=="menu"){
				$permission_level						= 0;
			}else if(mbw_get_param("board_action")=="modify_password"){
				$permission_level						= 1;
			}		
		}else if(mbw_get_param("mode")=="list"){
			$permission_level					= mbw_get_board_option("fn_list_level");
			$permission_name				= __MW("W_VIEW");
		}else{			
			if(mbw_get_param("board_action")=="write" || mbw_get_param("board_action")=="modify" || mbw_get_param("board_action")=="reply" || mbw_get_param("action")=="mb_uploader"){
				$permission_level				= mbw_get_board_option("fn_write_level");
				$permission_name			= __MW("W_WRITE");
			}else{
				$permission_level					= mbw_get_board_option("fn_list_level");
				$permission_name				= __MW("W_VIEW");
			}
		}
		if(intval($permission_level) > $mb_user_level){
			mbw_set_result_data(array("state"=>"error","script"=>mbw_get_move_script("permission_login")));
			if(mbw_is_login()){
				mbw_error_message(array("MSG_PERMISSION_ERROR","<span>( <span>".$permission_name." ".__MW("W_LEVEL").": ".$permission_level."</span> / <span>".__MW("W_USER_LEVEL").": ".$mb_user_level."</span> )</span>"), __MW("W_ACCESS"),"1102");
			}else{
				mbw_error_message(__MM('MSG_REQUIRE_LOGIN')."<br>(".__MM('MSG_MOVE_LOGIN').')',"","1103");
			}
		}
		return true;
	}
}

if(!function_exists('mbw_set_log')){
	function mbw_set_log($type,$content="",$args=array()){
		global $mstore,$mdb;
		global $mb_admin_tables,$mb_fields,$mb_vars,$mb_table_prefix;

		$send_data				= array();
		$where_data			= array();		

		if(!empty($args["mode"])) $send_data[$mb_fields["logs"]["fn_mode"]]		= $args["mode"];
		else $send_data[$mb_fields["logs"]["fn_mode"]]							= mbw_htmlspecialchars(mbw_get_param("mode"));

		if(!empty($args["board_action"])) $send_data[$mb_fields["logs"]["fn_action"]]		= $args["board_action"];
		else $send_data[$mb_fields["logs"]["fn_action"]]						= mbw_htmlspecialchars(mbw_get_param("board_action"));

		if($send_data[$mb_fields["logs"]["fn_action"]]=="") return;

		if(!empty($args["board_name"])) $send_data[$mb_fields["logs"]["fn_board_name"]]		= $args["board_name"];
		else $send_data[$mb_fields["logs"]["fn_board_name"]]					= mbw_htmlspecialchars(mbw_get_param("board_name"));

		if(!empty($args["user_pid"])) $send_data[$mb_fields["logs"]["fn_user_pid"]]		= $args["user_pid"];
		else $send_data[$mb_fields["logs"]["fn_user_pid"]]					= mbw_htmlspecialchars(mbw_get_param("user_pid"));

		if(!empty($args["user_name"])) $send_data[$mb_fields["logs"]["fn_user_name"]]		= $args["user_name"];
		else $send_data[$mb_fields["logs"]["fn_user_name"]]					= mbw_htmlspecialchars(mbw_get_param("user_name"));

		if(strpos($send_data[$mb_fields["logs"]["fn_board_name"]], $mb_table_prefix)===0) $send_data[$mb_fields["logs"]["fn_board_name"]]		= str_replace($mb_table_prefix, "", $send_data[$mb_fields["logs"]["fn_board_name"]]);

		if($type=="error"){
			$param			= array();
			if(is_array($_REQUEST) && !empty($_REQUEST)){
				$except				= array("mode","board_action","passwd","password","user_password");
				foreach($_REQUEST  as $key => $value)
					if(is_string($value) && !in_array($key, $except)) $param[]	= $key.":".$value;
			}
			if(!empty($param)){
				$content		.= " (".implode( ",", $param).")";
			}
		}

		$send_data[$mb_fields["logs"]["fn_type"]]								= $type;
		if(empty($content)) $content		= $type;
		$send_data[$mb_fields["logs"]["fn_content"]]						= mbw_htmlspecialchars($content);

		$send_data[$mb_fields["logs"]["fn_ip"]]								= $_SERVER["REMOTE_ADDR"];
		$send_data[$mb_fields["logs"]["fn_agent"]]							= $mb_vars["user_agent"];
		$send_data[$mb_fields["logs"]["fn_reg_date"]]						= mbw_get_current_time();
		
		if(mbw_is_login()){
			if(empty($send_data[$mb_fields["logs"]["fn_user_pid"]])) $send_data[$mb_fields["logs"]["fn_user_pid"]]					= mbw_get_user("fn_pid");
			if(empty($send_data[$mb_fields["logs"]["fn_user_name"]])) $send_data[$mb_fields["logs"]["fn_user_name"]]				= mbw_get_user("fn_user_name");
		}
		$mdb->db_query("INSERT",$mb_admin_tables["logs"], $send_data, $where_data);
	}
}


if(!function_exists('mbw_get_empty_item')){
	function mbw_get_empty_item($data){
		$item		= array();
		foreach($data as $key => $value){
			$item[$value]			= "";
		}
		return $item;
	}
}

if(!function_exists('mbw_error_message')){
	function mbw_error_message($message,$args=NULL,$code="1000",$target_name="",$count=1){
		global $mstore,$mb_error_message;

		$error_message			= "";
		if(is_array($message) && !empty($message)){			
			foreach($message  as $key => $value)
				$error_message		.= __MM($message[$key],$args,$count)."<br>";
		}else if(is_string($message))
			$error_message		= __MM($message,$args,$count);

		$mb_error_message[]		= $error_message;
		if(count($mb_error_message)==1){
			$mstore->set_result_data(array("state"=>"error","code"=>$code,"target_name"=>$target_name,"message"=>$error_message));		
			//Error 로그 남기기
			if(mbw_get_option("error_log")) mbw_set_log("error",$error_message);
			return '<div class="mb-error-message-box">'.$error_message.'</div>';
		}
	}
}
if(!function_exists('mbw_echo_error_message')){
	function mbw_echo_error_message(){
		global $mstore;
		$html_message	= $mstore->get_result_data("message");
		$script				= $mstore->get_result_data("script");
		$html_tag			= $mstore->get_result_data("html");
		$user_mode	= mbw_get_option("user_form_mode");
		if(!empty($user_mode)) $user_mode	= strtoupper($user_mode);

		$login_page	= "";
		$args				= mbw_get_vars("shortcode_args");
		if(!empty($args) && !empty($args['login_page'])) $login_page		= $args['login_page'];
		if(!empty($login_page) && !empty($script) && $user_mode=="MB" && !mbw_is_login()){
			loadStyle(MBW_PLUGIN_URL."plugins/user/css/login.css");
			mbw_set_result_data(array("state"=>"login","script"=>""));
			mbw_set_option("user_login_redirect_url","reload");
			mbw_init_user_panel("login");
			$login_template	= "";
			if($login_page=='1'){
				$login_template	.= '<div class="mb-page-login-box" style="visibility:hidden;min-height:600px;">';
					$login_template	.= mbw_create_user_panel(array("name"=>"login"));
				$login_template	.= '</div>';			
				$login_template	.= '<script type="text/javascript">jQuery( document ).ready(function() {jQuery(".mb-page-login-box").css("visibility", "visible")});</script>';
			}
			if(has_filter('mf_page_login_template')) $script			= apply_filters("mf_page_login_template",$script);		
			return $login_template;
		}else{
			if(!empty($html_message)) echo '<div class="mb-error-message-box">'.$html_message.'</div>';
			if(!empty($html_tag)) echo '<div class="mb-error-message-box">'.$html_tag.'</div>';
			if(!empty($script)){
				if(strpos($script,'<script type="text/javascript">')!==0)
					$script	= '<script type="text/javascript">jQuery( document ).ready(function() {'.$script.'});</script>';
				echo $script;
			}
			return "";
		}
	}
}

if(!function_exists('mbw_object_vars')){
	function mbw_object_vars($data){
		$data		= get_object_vars($data);
		$check	= array("field"=>"","name"=>"","width"=>"");
		foreach($check as $key=>$value){
			if(!isset($data[$key])){
				$data[$key]		= $value;
			}
		}
		return $data;
	}
}
if(!function_exists('mbw_load_editor_plugin')){
	function mbw_load_editor_plugin($type=""){
		$editor_type		= mbw_get_board_option("fn_editor_type");
		if(!empty($type)) $editor_type		= $type;
		$editor_type			= strtoupper($editor_type);
		if(has_action('mbw_load_editor_'.$editor_type)) do_action('mbw_load_editor_'.$editor_type);
	}
}
if(!function_exists('mbw_init_javascript')){
	function mbw_init_javascript(){
		global $wp_scripts,$mb_words;

		mbw_add_trace("mbw_init_javascript");
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-form');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-datepicker');

		$jquery_ver		= "1.11.4";
		if(!empty($wp_scripts->registered['jquery-ui-core']->ver)) $jquery_ver		= $wp_scripts->registered['jquery-ui-core']->ver;
		wp_register_style('jquery-ui-css', "//ajax.googleapis.com/ajax/libs/jqueryui/".$jquery_ver."/themes/smoothness/jquery-ui.css");
		if(mbw_is_admin_page()) wp_enqueue_style('jquery-ui-css');

		$path					= MBW_PLUGIN_PATH.'assets/js';
		$dir					= dir($path);
		while (false !== ($entry = $dir->read())){
			if(strpos($entry,'.')!==0 && is_file($path."/".$entry)){
				if(strpos($entry,".js")!==false){
					loadScript(MBW_PLUGIN_URL.'assets/js/'.$entry);
				}
			}
		}
		$path					= MBW_PLUGIN_PATH.'assets/css';
		$dir					= dir($path);
		while (false !== ($entry = $dir->read())){
			if(strpos($entry,'.')!==0 && is_file($path."/".$entry)){
				if(strpos($entry,".css")!==false){
					loadStyle(MBW_PLUGIN_URL.'assets/css/'.$entry);
				}
			}	
		}
		$admin_ajax_url		= mbw_check_url(admin_url( 'admin-ajax.php' ));
		if(!mbw_is_ssl() && strpos($admin_ajax_url, 'https://') !== false) $admin_ajax_url		= mbw_get_http_url($admin_ajax_url);
		wp_localize_script( 'assets-js-common-js', 'mb_ajax_object', array( 'ajax_url' => $admin_ajax_url,'admin_page' => (is_admin()? "true":"false")));
		
		if(mbw_get_option("commerce_version")!="" && mbw_get_option("commerce_version")<"1.0.5") echo '<script type="text/javascript">var mb_urls = {};</script>';
		$script		= "";
		$script		.= '<script type="text/javascript">';
		$script		.= 'var mb_options = {};';
		$script		.= 'var mb_languages = {};';
		$script		.= 'var mb_categorys = {};';
		$script		.= 'var mb_is_login = false;';
		$script		.= 'var mb_hybrid_app = "";';
		$script		.= 'if(typeof(mb_urls)==="undefined"){var mb_urls = {};};';

		if(mbw_is_login()) $script		.= 'mb_is_login			= true; ';

		$script		.= 'mb_options["device_type"]	= "'.esc_js(mbw_get_vars("device_type")).'";';
		$script		.= 'mb_options["nonce"]		= "'.(mbw_create_nonce("param")).'";';
		$script		.= 'mb_options["nonce2"]		= "'.(mbw_create_nonce("param")).'";';		//일부 테마에서 스킨의 헤더 파라미터값을 인코딩 하면서 충돌문제가 발생하여 변수를 분리
		$script		.= 'mb_options["page"]			= "'.esc_js(mbw_get_param("page")).'";';
		if(mbw_get_param("page_id")!=""){
			$script		.= 'mb_options["page_id"]		= "'.esc_js(mbw_value_filter(mbw_get_param("page_id"),"int")).'";';
		}else if(mbw_get_param("post")!=""){
			$script		.= 'mb_options["page_id"]		= "'.esc_js(mbw_value_filter(mbw_get_param("post"),"int")).'";';
		}else{
			$script		.= 'mb_options["page_id"]		= "";';
		}
		$script		.= 'mb_options["locale"]			= "'.esc_js(mbw_get_option("locale")).'";';
		$script		.= 'mb_languages["btn_ok"]		= "'.esc_js($mb_words["OK"]).'";';
		$script		.= 'mb_languages["btn_cancel"]	= "'.esc_js($mb_words["Cancel"]).'";';
		$script		.= 'mb_urls["ajax_url"]				= "'.esc_url_raw($admin_ajax_url).'";';
		$script		.= 'mb_urls["home"]					= "'.(MBW_HOME_URL).'";';
		if(function_exists('get_post_field') && function_exists('get_post')) $script		.= 'mb_urls["slug"]					= "'.esc_js(get_post_field( 'post_name', get_post())).'";';
		$script		.= 'mb_urls["login"]					= "'.esc_url(mbw_get_url(array('redirect_to'=>rawurlencode(mbw_get_current_url())),mbw_get_user_url("login"),"")).'";';
		$script		.= 'mb_urls["plugin"]				= "'.esc_url(mbw_check_url(MBW_PLUGIN_URL)).'";';
		
		$script		.= '</script>';
		if(has_filter('mf_board_init_javascript')) $script			= apply_filters("mf_board_init_javascript",$script);		
		return $script;
	}
}
if(!function_exists('mbw_init_style')){
	function mbw_init_style(){
		$style		= "";
		$style		.= '<style type="text/css">';		
		if(mbw_is_login()) $style		.= '.mb-hide-login{display:none !important;}';
		else $style		.= '.mb-hide-logout{display:none !important;}';
		$style		.= '</style>';
		if(has_filter('mf_board_init_style')) $style			= apply_filters("mf_board_init_style",$style);
		return $style;
	}
}
if(!function_exists('mbw_print_scripts')){
	function mbw_print_scripts(){
		mbw_add_trace("mbw_print_scripts");
		global $mb_scripts;
		if(mbw_get_trace("mbw_init_javascript")!="" && !empty($mb_scripts)){
			foreach($mb_scripts as $item){
				echo $item;
			}
			$mb_scripts		= array();
			mbw_print_styles();
		}
	}
}
if(!function_exists('mbw_print_styles')){
	function mbw_print_styles(){
		mbw_add_trace("mbw_print_styles");
		global $mb_styles;
		if(!empty($mb_styles)){			
			foreach($mb_styles as $item){
				echo $item;
			}
			$mb_styles		= array();
		}
	}
}

if(!function_exists('mbw_get_category_item_index')){
	function mbw_get_category_item_index($name,$category=""){
		$category1_text		= "";
		if(!empty($category)){
			$category1_text		= $category;			
		}else if(mbw_get_vars("category1_text")!=""){
			$category1_text		= mbw_get_vars("category1_text");
		}
		
		$category1				= array();
		if(!empty($category1_text)){
			$category_data			= $category1_text;
			if(strpos($category_data, '{')===false){
				$category1				= explode(",",$category_data);
			}else{
				$data		= mbw_json_decode(trim($category_data));
				if(!empty($data[0])){
					foreach($data[0] as $key => $value){
						$category1[]		= $key;
					}
				}
			}
		}else{
			$category_data		= mbw_get_board_option("fn_category_data");
			if(!empty($category_data)){
				if(strpos($category_data, '{')===false){
					$category1				= explode(",",$category_data);
					mbw_set_vars("category1_text",$category_data);
				}else{
					$data		= mbw_json_decode(trim($category_data));
					if(!empty($data[0])){
						foreach($data[0] as $key => $value){
							$category1[]		= $key;
						}
						mbw_set_vars("category1_text",implode(",",$category1));
					}
				}
			}
		}
		$i					= 0;
		$item_index		= 0;
		if(!empty($category1)){
			foreach($category1 as $value){
				$i++;
				if($value==$name){
					$item_index		= $i;
					break;				
				}
			}
		}
		return $item_index;
	}
}
if(!function_exists('mbw_get_category_item_class')){
	function mbw_get_category_item_class($name,$prefix="category1-item"){		
		$class		= "";
		if(!empty($name)){
			$index		= mbw_get_category_item_index($name);
			if(!empty($index)) $class	= $prefix.$index;
		}
		return $class;		
	}
}

if(!function_exists('mbw_set_form_name')){
	function mbw_set_form_name($name){
		global $mb_send_names;
		$mb_send_names[]		= $name;
		return esc_attr($name);
	}
}

if(!function_exists('mbw_set_form_session')){
	function mbw_set_form_session(){
		if(mbw_get_option("use_form_session")){
			global $mb_form_names,$mb_send_names;
			$session = @session_id();
			if(empty($session)) @session_start();
			if(!empty($mb_send_names)){
				$form_names		= ",".implode( ",", $mb_form_names).",".implode( ",", $mb_send_names).",";
				$_SESSION['mb_form_'.mbw_get_board_name()]			= $form_names;
				$_SESSION['mb_form_'.mbw_get_vars("nonce_hash")]		= $form_names;
			}
		}		
	}
}

if(!function_exists('mbw_check_item')){
	function mbw_check_item($data){
		global $mstore;
		$mb_user_level		= mbw_get_user("fn_user_level");

		if(isset($data["level"])){
			$sign					= ">=";
			$check_level		= 0;

			if(is_array($data["level"])){
				if(!empty($data["level"]["sign"]))	$sign					= $data["level"]["sign"];
				if(!empty($data["level"]["grade"])) $check_level		= intval($data["level"]["grade"]);
			}else if(is_string($data["level"])){
				$check_level		= intval($data["level"]);
			}

			if($sign=="="){
				if($mb_user_level!=$check_level) return false;
			}else if($sign==">"){
				if($mb_user_level<=$check_level) return false;
			}else if($sign==">="){
				if($mb_user_level<$check_level) return false;
			}else if($sign=="<"){
				if($mb_user_level>=$check_level) return false;
			}else if($sign=="<="){
				if($mb_user_level>$check_level) return false;
			}else return true;
		} return true;
	}
}


if(!function_exists('mbw_is_secret')){
	function mbw_is_secret($pid,$passwd=""){
		global $mstore,$mdb,$mb_fields;		
		$mb_user_level		= mbw_get_user("fn_user_level");
		$mb_user_pid			= mbw_get_user("fn_pid");

		if(mbw_is_login()){
			//비밀글 보기 권한이 있을 경우
			if(intval(mbw_get_board_option("fn_secret_level")) <= $mb_user_level){
				return false;
			//본인이 작성한 글, 본인이 작성한 글의 답변글일 경우
			}else if(is_array($pid) && in_array($mb_user_pid, $pid)){
				return false;
			}else if($mb_user_pid==intval($pid)){
				return false;			
			}
		}
		//비밀번호가 일치할 경우
		if(mbw_get_param("secret_passwd")!="" && mbw_check_password(mbw_get_param("secret_passwd"),$passwd)){
			return false;
		}
		return true;
	}
}

if(!function_exists('mbw_get_move_script')){
	function mbw_get_move_script($type){
		global $mstore,$mb_languages;
		$move_script		= "";
		$site_url				= MBW_HOME_URL;

		$mstore->set_result_data(array("state"=>"error"));
		if($type=="login"){			
			$login_url		= mbw_get_url(array('redirect_to'=>rawurlencode(mbw_get_current_url())),mbw_get_user_url("login"),"");
			$move_script	= 'alert("'.__MM('MSG_REQUIRE_LOGIN')." (".__MM('MSG_MOVE_LOGIN').')");moveURL("'.esc_url($login_url).'");';
		}else if($type=="login_redirect"){
			if(mbw_is_login() && has_filter('mf_user_login_redirect_to')){
				 $site_url		= apply_filters("mf_user_login_redirect_to",$site_url);
			}
			$move_script	= 'movePage("'.$site_url.'")';
		}else if($type=="permission_login"){			
			$login_url		= mbw_get_url(array('redirect_to'=>rawurlencode(mbw_get_current_url())),mbw_get_user_url("login"),"");
			if(mbw_is_login()){
				$move_script	= 'alert("'.__MM('MSG_PERMISSION_ERROR',__MW("W_ACCESS"))." (".__MM('MSG_MOVE_PREV').')");moveURL("back");';
			}else{
				//$move_script	= 'alert("'.__MM('MSG_PERMISSION_ERROR',__MW("W_ACCESS"))." (".__MM('MSG_MOVE_LOGIN').')");moveURL("'.$login_url.'");';
				$move_script	= 'moveURL("'.esc_url($login_url).'");';
			}

		}else if($type=="logout"){
			$logout_redirect_to		= $site_url;
			if(has_filter('mf_user_logout_redirect_to')) $logout_redirect_to		= apply_filters("mf_user_logout_redirect_to",$logout_redirect_to);
			$move_script	= "sendLogoutData({'board_action':'logout','redirect_to':'".esc_url($logout_redirect_to)."'});";
		}else if($type=="home"){
			$move_script	= 'movePage("'.esc_url($site_url).'");';
		}else if($type=="back"){
			$move_script	= 'movePage("back");';
		}else{
			$move_script	= 'movePage("'.esc_url($site_url).'");';
		}
		return '<script type="text/javascript">jQuery( document ).ready(function() {'.$move_script.'});</script>';
	}
}
if(!function_exists('mbw_check_cookie')){
	function mbw_check_cookie($data){
		global $mstore,$mdb;
		global $mb_admin_tables,$mb_fields,$mb_vars;

		if($data["save"]=="db"){
			$send_data										= array();
			if(mbw_is_login()){
				$send_data[$mb_fields["cookies"]["fn_user_pid"]]						= mbw_get_user("fn_pid");
				$send_data[$mb_fields["cookies"]["fn_user_name"]]					= mbw_get_user("fn_user_name");
			}else{
				return "login";
			}
			$where_query			= $mdb->prepare(" where user_pid=%d and board_name=%s and cookie_type=%s and cookie_name=%s and cookie_value=%s", mbw_get_user("fn_pid"),mbw_get_param("board_name"),$data["type"],$data["name"],$data["value"]);
			$select_query			= mbw_get_add_query(array("column"=>"count(*)","table"=>$mb_admin_tables["cookies"]));
			$cookie_check			= $mdb->get_var($select_query.$where_query);

			if(empty($cookie_check)){
				$send_data[$mb_fields["cookies"]["fn_board_name"]]					= mbw_get_param("board_name");

				$send_data[$mb_fields["cookies"]["fn_cookie_type"]]					= $data["type"];
				$send_data[$mb_fields["cookies"]["fn_cookie_name"]]					= $data["name"];
				$send_data[$mb_fields["cookies"]["fn_cookie_value"]]					= $data["value"];

				$send_data[$mb_fields["cookies"]["fn_ip"]]								= $_SERVER["REMOTE_ADDR"];
				$send_data[$mb_fields["cookies"]["fn_agent"]]							= $mb_vars["user_agent"];
				$send_data[$mb_fields["cookies"]["fn_reg_date"]]						= mbw_get_current_time();
				
				$mdb->db_query("INSERT",$mb_admin_tables["cookies"], $send_data, array());
				return "success";
			}
		}else if($data["save"]=="file"){
			$cookie_value		= "";
			if(mbw_get_cookie($data["type"])!=""){
				$cookie_value	= mbw_get_cookie($data["type"]);
			}
			$check_value		= mbw_get_param("board_name").":".$data["value"]."";
			if(strpos(",".$cookie_value.",",",".$check_value.",")===false){
				if(!empty($data["expire"])) $expire		= $data["expire"];
				else $expire		= 0;

				$cookie_array		= explode(',',$cookie_value);
				if(count($cookie_array)>20){
					array_shift($cookie_array);
					$cookie_value		= implode(',',$cookie_array);
				}

				mbw_set_cookie($data["type"], $cookie_value.$check_value.",",$expire);
				return "success";
			}			
		}
		return "exist";
	}
}
if(!function_exists('mbw_generate_auth_cookie')){
	function mbw_generate_auth_cookie($user_id="",$user_mode="MB",$expire=0){				
		global $mstore;		

		if(empty($user_id)) return;

		$auth_cookie_name = $mstore->get_auth_cookie_name();
		mbw_set_wp_user_data($user_id);
		$expiration			= mbw_get_timestamp() + intval(mbw_get_option("cookie_expire"));			
		$hash				= mbw_get_hash_key("cookie",$expiration,$user_id);
		$auth_cookie		= $user_id. '|' . $expiration . '|' . $hash . '|' . $user_mode;
		mbw_set_cookie($auth_cookie_name, $auth_cookie,$expire);
	}
}

if(!function_exists('mbw_refresh_auth_cookie')){
	function mbw_refresh_auth_cookie(){
		global $mstore,$mdb,$mb_admin_tables,$mb_fields;

		if ( !$mstore->is_login_cookie() )
			return false;
		
		$cookie					= $mstore->get_login_cookie();
		$cookie_elements		= explode('|', $cookie);

		if ( count($cookie_elements) != 4 )
			return false;
		
		list($user_id, $expiration, $hmac, $user_mode) = $cookie_elements;
		if(!empty($user_id)) $user_access_token	= $mdb->get_var($mdb->prepare("select ".$mb_fields["users"]["fn_user_access_token"]." from `".$mb_admin_tables["users"]."` where `".$mb_fields["users"]["fn_user_id"]."`=%s;",$user_id));
		else $user_access_token	= "";
		$hash			= mbw_get_hash_key("cookie",$expiration,$user_id,$user_access_token);
		$hash2			= mbw_get_hash_key("cookie",$expiration,$user_id);
		
		if($hmac==$hash || $hmac==$hash2){
			$expire		= mbw_get_timestamp() + 7776000;			
			mbw_generate_auth_cookie($user_id,$user_mode,$expire);
			$mdb->query($mdb->prepare("update ".$mb_admin_tables["users"]." set ".$mb_fields["users"]["fn_user_access_token"]."='".mbw_generate_access_token()."' where ".$mb_fields["users"]["fn_user_id"]."=%s;",$user_id));
		}
	}
}

if(!function_exists('mbw_validate_auth_cookie')){
	function mbw_validate_auth_cookie(){
		if(mbw_get_trace("mbw_set_wp_user_data")!="") return true;

		global $mstore,$mdb;
		global $mb_admin_tables,$mb_fields;

		if ( !$mstore->is_login_cookie() )
			return false;
		
		$cookie = $mstore->get_login_cookie();

		$cookie_elements = explode('|', $cookie);

		if ( count($cookie_elements) != 4 )
			return false;

		list($user_id, $expiration, $hmac, $user_mode) = $cookie_elements;
		if(!empty($user_id)) $user_access_token	= $mdb->get_var($mdb->prepare("select ".$mb_fields["users"]["fn_user_access_token"]." from `".$mb_admin_tables["users"]."` where `".$mb_fields["users"]["fn_user_id"]."`=%s;",$user_id));
		else $user_access_token	= "";
		$hash		= mbw_get_hash_key("cookie",$expiration,$user_id,$user_access_token);

		if($hmac==$hash) return true;
		else{
			$hash2		= mbw_get_hash_key("cookie",$expiration,$user_id);
			if($hmac==$hash2) return true;
			else return false;
		}
	}
}
if(!function_exists('mbw_clear_auth_cookie')){
	function mbw_clear_auth_cookie($name=""){
		global $mstore;
		if(!empty($name)){			
			$auth_cookie_name = $name;			
		}else{
			$mstore->clear_cookie();
			mbw_set_cookie("mb_access_token", "");
			$auth_cookie_name = $mstore->get_auth_cookie_name();
		}
		mbw_set_cookie($auth_cookie_name, "");
	}
}


if(!function_exists('mbw_is_image_file')){
	function mbw_is_image_file($name){
		global $mb_image_upload_files;
		$name_array	= explode('.',$name);
		if(count($name_array)==1) return false;
		$file_ext			= strtolower(array_pop($name_array));
		if(!in_array($file_ext, $mb_image_upload_files)){
			return false;
		}	
		return true;
	}
}
if(!function_exists('mbw_get_id_prefix')){
	function mbw_get_id_prefix(){
		global $mb_table_prefix,$mb_board_name;
		$name		= "";
		if(empty($mb_board_name)){
			if(!empty($_REQUEST["board_name"]))
				$name		= $_REQUEST["board_name"];
		}else{
			$name		= $mb_board_name;			
		}
		return $mb_table_prefix.$name."_";
	}
}

if(!function_exists('mbw_generate_access_token')){
	function mbw_generate_access_token(){		
		if(mbw_get_access_token()!=""){
			return mbw_get_access_token();
		}else{
			if(function_exists('wp_generate_password')){
				$auth_key		= wp_generate_password( 20, false );
			}else{
				$auth_key		= md5(time());
			}
			mbw_set_cookie("mb_access_token",$auth_key,(mbw_get_timestamp()+7776000));
			return $auth_key;
		}		
	}
}
if(!function_exists('mbw_update_model_data')){
	function mbw_update_model_data($model, $update_data, $name=""){
		if(!empty($model)){
			if(!empty($name)){
				$board_name			= $name;
			}else{
				$board_name			= mbw_get_board_name();
			}			
			$data_type				= "";
			if(!empty($update_data)){
				if(is_string($model)){
					$data_type		= 'string';
					$model_array	= mbw_json_decode($model);
				}else if(is_array($model)){
					$data_type		= 'array';
					$model_array	= $model;
				}else return $model;
				$append_data		= array();
				$index				= 0;

				foreach($model_array as $key=>$data){
					if(isset($data['field'])){
						foreach($update_data as $item){							
							$item_field			= "";							
							if(isset($item['field'])) $item_field	= $item['field'];							
							if($data['field']==$item_field){
								$item_type			= 'add';
								if(isset($item['type'])) $item_type	= $item['type'];

								if(isset($item['match_field']) && isset($item['match_value'])) {
									$item_value		= mbw_get_board_item($item['match_field']);
									if($item_value!=$item['match_value']) continue;
								}
								if($item_type=='delete'){
									unset($model_array[$key]);
								}else if($item_type=='replace'){
									if(isset($item['model']) && strpos($item['model'], '{') === 0){
										$item_model				= mbw_json_decode($item['model']);
										if(!empty($item_model[0])) $model_array[$key]		= $item_model[0];
									}
								}else if($item_type=='append'){
									if(isset($item['model']) && strpos($item['model'], '{') === 0){
										$index++;
										$item_model				= mbw_json_decode($item['model']);
										if(!empty($item_model[0])) $append_data[$index]	= $item_model;
									}									
								}else {
									if(isset($item['key']) && isset($item['value'])) {
										$item_key		= $item['key'];
										$item_value	= $item['value'];									
										if($item_type=='update'){
											if(isset($data[$item_key])) $data[$item_key]	= $item_value;
										}else if($item_type=='add'){
											$delimiter		= ',';
											if(isset($item["delimiter"])) $delimiter		= $item["delimiter"];
											if(isset($data[$item_key])){
												if(is_string($item_value)) $data[$item_key]	.= $delimiter.$item_value;
												else if(is_array($item_value)){													
													if(isset($data[$item_key][0])){
														array_push($data[$item_key], $item_value);
													}else{
														$temp_data		= array();
														array_push($temp_data, $data[$item_key], $item_value);
														$data[$item_key]		= $temp_data;
													}
												}
											}
										}
										$model_array[$key]		= $data;
									}
								}
							}							
						}
					}
					$index++;
				}
				if(!empty($append_data)){
					foreach($append_data as $key=>$data){
						array_splice( $model_array, $key, 0, $data );
					}
				}
				if($data_type=='string'){
					$model		= mbw_data_encode($model_array);
				}else{
					$model		= $model_array;
				}				
			}
		}
		return $model;
	}
}
if(!function_exists('mbw_analytics')){
	function mbw_analytics($mode,$value=1){
		if(empty($mode) || mbw_is_search_engine()) return;

		global $mdb, $mstore, $mb_admin_tables,$mb_fields,$mb_vars;
		$today				= date('Y-m-d',mbw_get_timestamp());	
		$counter_check		= intval($mdb->get_var($mdb->prepare("SELECT count(*) FROM ".$mb_admin_tables["analytics"]." WHERE ".$mb_fields["analytics"]["fn_date"]."=%s;",$today)));
		if($counter_check==0){
			$total_visit		= $mdb->get_var($mdb->prepare("SELECT total_visit FROM %1s ORDER BY %1s DESC limit 1;",$mb_admin_tables["analytics"],$mb_fields["analytics"]["fn_pid"]));
			if(empty($total_visit))  $total_visit = 1;
			$analytics_field		= $mstore->get_board_select_fields(array("fn_today_visit","fn_today_join","fn_today_write","fn_today_reply","fn_today_comment","fn_today_upload","fn_total_visit","fn_today_page_view","fn_date"),"analytics");
			$mdb->query($mdb->prepare("INSERT INTO ".$mb_admin_tables["analytics"]." (".implode( ",", $analytics_field).") VALUES (0,0,0,0,0,0,%d,0,%s);",$total_visit,$today));
			do_action("mbw_today_analytics_init");
		}		
		if($mode=="today_visit"){
			if(mbw_get_cookie("mb_".$mode)==""){
				if(!empty($_SERVER['HTTP_REFERER'])){
					$ip					= $_SERVER['REMOTE_ADDR'];
					$parse_url		= parse_url($_SERVER['HTTP_REFERER']);
					if(!empty($parse_url['host'])){
						$referer_host		= $parse_url['host'];
						$referer_url		= $_SERVER['HTTP_REFERER'];
						$referer_log		= mbw_get_option("referer_log");
						if($referer_log!=0 && strpos(MBW_HOME_URL, $referer_host) === false){
							$referer_field		= $mstore->get_board_select_fields(array("fn_date","fn_reg_date","fn_referer_host","fn_referer_url","fn_ip","fn_agent"),"referers");
							$mdb->query($mdb->prepare("INSERT INTO ".$mb_admin_tables["referers"]." (".implode( ",", $referer_field).") VALUES (%s,%s,%s,%s,%s,%s)",$today,mbw_get_current_time(),$referer_host,$referer_url,$ip,$mb_vars["user_agent"]));
						}
					}
				}
				mbw_set_cookie("mb_".$mode,"mb_".$mode, mbw_get_timestamp()+(60*60*24));
				$mdb->query($mdb->prepare("UPDATE ".$mb_admin_tables["analytics"]." set today_visit=today_visit+%d,total_visit=total_visit+%d where ".$mb_fields["analytics"]["fn_date"]."=%s;",$value,$value,$today));
				do_action("mbw_".$mode);
			}
		}else if($mode=="today_sales"){
			$mdb->query($mdb->prepare("UPDATE ".$mb_admin_tables["analytics"]." set today_sales=today_sales+%d,today_payment_count=today_payment_count+1 where ".$mb_fields["analytics"]["fn_date"]."=%s;",$value,$today));
			do_action("mbw_".$mode);
		}else{
			$mdb->query($mdb->prepare("UPDATE ".$mb_admin_tables["analytics"]." set %1s = %1s+%d where ".$mb_fields["analytics"]["fn_date"]."=%s;",$mode,$mode,$value,$today));
			do_action("mbw_".$mode);
		}		
	}
}
?>