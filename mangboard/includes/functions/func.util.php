<?php
if(!function_exists('loadScript')){
	function loadScript($src, $handle="",$deps=array('jquery')){
		// JS 파일 등록
		if(mbw_is_ssl())
			$src		= str_replace("http://", "https://", $src);
		if(empty($handle)){
			if(strpos($src, '/mangboard/') === false)	$handle		= basename($src); 
			else{
				$temp	= explode('/mangboard/', $src);
				$handle	= str_replace("/", "-", $temp[1]);
			}
			$handle	= str_replace( array(".","_"), "-", $handle);
		}
		wp_enqueue_script($handle, $src, $deps, mbw_get_option("mb_index"));
	}
}
if(!function_exists('loadStyle')){
	function loadStyle($src, $handle="", $deps=array()){
		// CSS 파일 등록
		if(mbw_is_ssl())
			$src		= str_replace("http://", "https://", $src);
		if(empty($handle)){
			if(strpos($src, '/mangboard/') === false)	$handle		= basename($src); 
			else{
				$temp	= explode('/mangboard/', $src);
				$handle	= str_replace("/", "-", $temp[1]);
			}
			$handle	= str_replace( array(".","_"), "-", substr($handle, 0, -4));
		}
		wp_enqueue_style($handle, $src, $deps, mbw_get_option("mb_index"));
	}
}
if(!function_exists('mbw_load_postcode_script')){
	function mbw_load_postcode_script($type){
		if(mbw_get_request_mode()=="Frontend"){
			if($type=="daum"){
				if(mbw_is_ssl()){
					wp_enqueue_script("kakao-postcode", "https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js", array('jquery'), null);
					//loadScript("https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js","kakao-postcode");
				}else{
					wp_enqueue_script("kakao-postcode", "http://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js", array('jquery'), null);
					//loadScript("http://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js","kakao-postcode");
				}
			}
		}
	}
}
if(!function_exists('mbw_validate_redirect')){
	function mbw_validate_redirect($redirect_to, $url=""){
		if(!empty($url)) $site_url		= $url;
		else $site_url		= mbw_check_url(MBW_HOME_URL);
		return wp_validate_redirect($redirect_to, $site_url);
	}
}
if(!function_exists('mbw_do_action')){
	function mbw_do_action($type){
		if (has_action($type))
			do_action($type);
	}
}
if(!function_exists('mbw_mail')){
	function mbw_mail($to, $title, $content, $headers, $attachments = array()){
		if(function_exists('mbw_send_mail')){
			mbw_send_mail( $to, $title, $content, $headers, $attachments);
		}else{
			wp_mail( $to, $title, $content, $headers, $attachments);
		}		
	}
}
if(!function_exists('mbw_sms')){
	function mbw_sms($phone,$message,$title="",$files = array(),$type="sms",$use_multi=true){
		if(function_exists('mbw_send_sms')){
			mbw_send_sms($phone,$message,$title,$files,$type,$use_multi);
		}
	}
}

if(!function_exists('mbw_get_url')){
	function mbw_get_url($data=NULL,$url="",$type="full"){
		global $mb_basic_params;
		$get_params		= array();		
		$param_data		= array();
		$mode_check		= false;
		$vid_check			= false;
		if(!empty($data) && !empty($data["vid"])){
			$mode_check			= true;
			if(strpos($url,'vid=')!==false) $vid_check			= true;
		}

		if($type=="full"){
			foreach($mb_basic_params as $key => $value){				
				if($mode_check && ($key=="mode" || $key=="board_pid")) continue;
				if(mbw_get_param($key)!="") $param_data[$key]		= mbw_get_param($key);
			}
			$check_param		= array("list_type","order_by","order_type","board_name");
			foreach($check_param as $value){
				if(empty($_REQUEST[$value]) && isset($param_data[$value])){
					unset($param_data[$value]);
				}
			}
			if(!empty($param_data["board_action"])){
				if($param_data["board_action"]=='load' || mbw_get_param('mode')=='list' || mbw_get_param('mode')=='view'){
					unset($param_data["board_action"]);
				}else if(!empty($data) && !empty($data["mode"]) && ($data["mode"]=='list' || $data["mode"]=='view')){
					unset($param_data["board_action"]);
				}
			}
		}
		if(!empty($data)){
			if($mode_check && $vid_check) unset($data['vid']);
			$param_data = array_merge( $param_data, $data);
		}
		if(!isset($param_data["search_text"])){
			if(isset($param_data["search_field"])) unset($param_data["search_field"]);
		}else if($param_data["search_text"]==""){
			unset($param_data["search_text"]);
			if(isset($param_data["search_field"])) unset($param_data["search_field"]);
		}
		if(isset($param_data["board_page"]) && $param_data["board_page"]=="1"){
			unset($param_data["board_page"]);
		}		

		if($url==""){
			if(mbw_is_admin_page()){
				if(!empty($_REQUEST['page'])){
					$result_url		= admin_url('admin.php?page='.$_REQUEST["page"]);
				}else{
					$result_url		= admin_url('admin.php?page=mbw_board_options');
				}
			}else{
				if(mbw_get_param('wp_post_id')!="") $wp_post_id		= mbw_get_param('wp_post_id');
				else $wp_post_id		= mbw_get_option('wp_post_id');
				if(empty($wp_post_id)){
					$wp_post_id		= get_the_ID();
					if(empty($wp_post_id)){
						if(mbw_get_param('page_id')!="") $wp_post_id		= mbw_get_param('page_id');
						else if(mbw_get_board_option("fn_post_id")!="") $wp_post_id		= mbw_get_board_option("fn_post_id");
					}
				}
				if(!empty($wp_post_id)){
					$result_url		= get_permalink($wp_post_id);
				}else{
					$result_url		= get_permalink();
				}
			}
		}else{
			$result_url			= $url;
		}
		if(strpos($result_url, '?') === false || strpos($result_url, 'page_id=') !== false ){
			if(isset($param_data['page_id'])){
				unset($param_data['page_id']);
			}
		}
		foreach((array) $param_data as $key => $value){
			if(!empty($value) || $value=="0") $get_params[] = $key."=".rawurlencode(stripslashes($value));
		}		
		$result_param		= (implode( '&', $get_params ));

		if(strpos($result_url, '?') === false)	$result_url		= $result_url."?";
		else $result_url		= $result_url."&";
		$result_url		= $result_url.$result_param;		
		if(mbw_is_ssl() && strpos($result_url, 'http://')===0) $result_url		= mbw_get_ssl_url($result_url);
		return strip_tags(rtrim($result_url,"&"));
	}
}
if(!function_exists('mbw_check_url')){
	function mbw_check_url($url){
		if(mbw_is_ssl())
			$url				= mbw_get_ssl_url($url);
		return strip_tags($url);		
	}
}

if(!function_exists('mbw_get_current_url')){
	function mbw_get_current_url(){
		if(!empty($_SERVER["HTTP_HOST"])){
			$url	= $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]; 
			if(mbw_is_ssl()) $url			= "https://".$url;
			else $url			= "http://".$url;
		}else{
			$url	= mbw_check_url(MBW_HOME_URL).$_SERVER["REQUEST_URI"]; 
		}
		return strip_tags($url);
	}
}

if(!function_exists('mbw_get_ssl_url')){
	function mbw_get_ssl_url($url){
		$ssl_url			= "";
		$query			= "";

		if(strpos($url, 'https://') === 0){
			return $url;
		}else if(strpos($url, 'http') === 0 && mbw_get_option("ssl_mode")){
			$parse_url					= parse_url($url);
			
			if(empty($parse_url["path"])) $parse_url["path"]		= "";
			if(empty($parse_url["host"])) $parse_url["host"]		= "";
			if(!empty($parse_url["query"])) $query		= "?".$parse_url["query"];
			if(mbw_get_option("ssl_domain")!=""){					
				$parse_url["host"]		= str_replace(array("http://","https://"), "", untrailingslashit(mbw_get_option("ssl_domain")));
			}
			if(mbw_get_option("ssl_port")!="" && mbw_get_option("ssl_port")!="443"){
				$parse_url["host"]	= $parse_url["host"].":".mbw_get_option("ssl_port");
			}
			$ssl_url		= "https://".$parse_url["host"].$parse_url["path"].$query;
		}else if(mbw_is_ssl()){
			$ssl_url				= str_replace("http://", "https://", $url);
		}else{
			$ssl_url				= $url;
		}
		return strip_tags($ssl_url);
	}
}
if(!function_exists('mbw_get_http_url')){
	function mbw_get_http_url($url){
		$parse_url		= parse_url($url);		
		$port				= "";
		$query			= "";
		if(empty($parse_url["host"])) $parse_url["host"]		= "";
		if(empty($parse_url["path"])) $parse_url["path"]		= "";
		if(!empty($parse_url["port"]) && mbw_get_option("ssl_port")!=$parse_url["port"]) $port		= ":".$parse_url["port"];
		if(!empty($parse_url["query"])) $query		= "?".$parse_url["query"];

		return strip_tags("http://".$parse_url["host"].$port.$parse_url["path"].$query);
	}
}

if(!function_exists('mbw_check_permalink')){
	function mbw_check_permalink($post_name,$site_url=""){		
		global $mb_post_url;
		if(!empty($mb_post_url[$post_name])) return $mb_post_url[$post_name];

		if(empty($site_url)) $site_url		= mbw_check_url(MBW_HOME_URL);
		$permalink_structure				= get_option("permalink_structure");
		$url		= $site_url;

		if($permalink_structure=="/%postname%/"){
			$url		= $site_url.'/'.$post_name.'/';
		}else if($permalink_structure==""){
			global $wpdb;
			$page_id		= $wpdb->get_var($wpdb->prepare("SELECT ID FROM ".$wpdb->posts." WHERE post_name=%s", $post_name)); 
			if(!empty($page_id)){
				$url			= $site_url.'/?page_id='.$page_id;
			}
		}else{						
			if(mbw_get_request_mode()!="API"){
				global $wpdb;
				$page_id		= $wpdb->get_var($wpdb->prepare("SELECT ID FROM ".$wpdb->posts." WHERE post_name=%s", $post_name)); 
				if(!empty($page_id)){
					$url			= get_permalink($page_id);
				}
			}else{
				$url		= $site_url.'/'.$post_name.'/';
			}
		}
		$url		= mbw_check_url($url);
		$mb_post_url[$post_name]		= $url;
		return strip_tags($url);
	}
}

if(!function_exists('mbw_is_search_engine')){
	function mbw_is_search_engine(){
		if(isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider|feedfetcher|ia_archiver|scooter|teoma|lycos|libwww-perl|facebookexternalhit/i', $_SERVER['HTTP_USER_AGENT'])) return true;
		else if(!empty($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']=="http://www.naver.com") return true;		
		else return false;
	}
}


if(!function_exists('mbw_stripslashes')){
	function mbw_stripslashes($data){
		if(is_array($data)){
			foreach($data as $key => $value){				
				if(!empty($value)){
					$data[$key]			= stripslashes($value);
				}
			}
		}else{
			if(!empty($data)){
				$data			= stripslashes($data);
			}
		}
		return $data;
	}
}

if(!function_exists('mbw_htmlspecialchars')){
	function mbw_htmlspecialchars($data, $flags=ENT_QUOTES){
		if(is_array($data)){
			foreach($data as $key => $value){
				if(!empty($value)){
					$data[$key]			= htmlspecialchars(stripslashes($value), $flags, "UTF-8");
				}
			}
		}else{
			if(!empty($data)){
				$data			= htmlspecialchars(stripslashes($data), $flags, "UTF-8");
			}
		}
		return $data;
	}
}
if(!function_exists('mbw_htmlspecialchars2')){
	function mbw_htmlspecialchars2($data, $flags=ENT_QUOTES){
		if(is_array($data)){
			foreach($data as $key => $value){
				if(!empty($value)){
					$data[$key]			= htmlspecialchars($value, $flags, "UTF-8");
				}
			}
		}else{
			if(!empty($data)){
				$data			= htmlspecialchars($data, $flags, "UTF-8");
			}
		}
		return $data;
	}
}
if(!function_exists('mbw_htmlspecialchars_decode')){
	function mbw_htmlspecialchars_decode($data, $flags=ENT_QUOTES){
		if(is_array($data)){
			foreach($data as $key => $value){
				if(!empty($value)){
					$data[$key]			= htmlspecialchars_decode($value, $flags);
				}
			}
		}else{
			if(!empty($data)){
				$data			= htmlspecialchars_decode($data, $flags);
			}
		}
		return $data;
	}
}

if(!function_exists('mbw_variable_type')){
	function mbw_variable_type($value){
		if(is_numeric($value)){
			if(strlen($value)==1){
				return intval($value);
			}else if(strpos($value, '0')===0){
				return $value;
			}else if(strlen($value)<14){
				if(strlen(intval($value))==strlen($value)){
					return intval($value);
				}else{
					return floatval($value);
				}
			}else{
				return $value;
			}
		}else return $value;
	}
}

if(!function_exists('log_trace')){
	function log_trace($message,$log_check=true){
		if($log_check && mbw_is_search_engine()) return;
		$log_path			= MBW_LOG_PATH;
		if(!is_dir($log_path)){
			@mkdir($log_path, 0777, true);
			@chmod($log_path, 0777);
		}
		$filename	=  date('ymd',mbw_get_timestamp());
		$file			=	@fopen($log_path."log_".$filename.".txt","a+");
		//@fwrite($file,date('y/m/d H:i:s',mbw_get_timestamp())." : "."URL: ".$_SERVER["PHP_SELF"].":".$_SERVER["REMOTE_ADDR"].":".$_SERVER['HTTP_USER_AGENT']."\n");

		if(is_array($message)){
			@fwrite($file,date('y/m/d H:i:s',mbw_get_timestamp())." : ".print_r($message,true).$_SERVER["REMOTE_ADDR"]."\n");
		}else if($message!=="ALL_REQUEST")
		{
			@fwrite($file,date('y/m/d H:i:s',mbw_get_timestamp())." : ".$message." [".$_SERVER["REMOTE_ADDR"]."]\n");
		} else
		{
			$logText		= "";
			if(is_array($_GET)) 
			{			
				foreach($_GET  as $key => $value)	
					$logText		= $logText.$key.":".$value.", ";
				
				if($logText!="")
					@fwrite($file,date('y/m/d H:i:s',mbw_get_timestamp())." : "."GET: ".$logText." [".$_SERVER["REMOTE_ADDR"]."]\n");
			}
			$logText		= "";
			if(is_array($_POST)) 
			{		
				foreach($_POST  as $key => $value)
					if(is_string($value))
						$logText		= $logText.$key.":".$value.", ";
					else if(is_array($value)){
						foreach($value  as $key2 => $value2)
							if(is_string($value2))
								$logText		= $logText.$key2.":".$value2.", ";
					}

				if($logText!="")
					@fwrite($file,date('y/m/d H:i:s',mbw_get_timestamp())." : "."POST: ".$logText." [".$_SERVER["REMOTE_ADDR"]."]\n"); 					
			}
		}	

		@chmod($log_path."log_".$filename.".txt",0777);
		@fclose($file);
	}
}

if(!function_exists('mbw_json_decode')){
	function mbw_json_decode($data,$output="ARRAY"){
		if(!is_string($data)) return $data;

		$data				= trim($data, "[]");
		$last_pos		= strrpos($data,"}")+1;
		if(substr($data,$last_pos,1)==","){
			$data		= substr($data,0,$last_pos);
		}
		if($output=="ARRAY"){
			return json_decode("[".$data."]",true);
		}else{
			return json_decode("[".$data."]");
		}		
	}
}

if(!function_exists('mbw_get_decryption')){
	function mbw_get_decryption($data){
		$get_params		= array();
		$temp_values		= array();
		$decode_data		= base64_decode($data);
		if(strpos($decode_data, '=')===false) return array();
		$param_data		= explode("&", $decode_data);
		foreach($param_data as $value){
			$temp_values		= explode("=", $value);
			if(count($temp_values)==2)
				$get_params[$temp_values[0]]		= $temp_values[1];
		}
		return $get_params;
	}
}

if(!function_exists('mbw_is_access_ip')){
	function mbw_is_access_ip($ip){
		global $mdb,$mb_admin_tables,$mb_fields;

		$remote_ip				= $ip;
		$ip_not_allow			= $mdb->get_var(mbw_get_add_query(array("column"=>"count(*)","table"=>$mb_admin_tables["access_ip"]), array(array("field"=>$mb_fields["access_ip"]["fn_ip"],"value"=>$remote_ip),array("field"=>$mb_fields["access_ip"]["fn_type"],"value"=>"0"))));
		//차단한 IP일 경우 접근 차단
		if($ip_not_allow>0){
			echo 'IP address ['.esc_attr($ip).'] is not allowed';
			exit;
		}

		$where_data			= array();
		$where_data[]			= array("field"=>$mb_fields["access_ip"]["fn_type"],"value"=>"1");

		$ip_allow_count				= $mdb->get_var(mbw_get_add_query(array("column"=>"count(*)","table"=>$mb_admin_tables["access_ip"]), $where_data));
		if($ip_allow_count>0){		//접근 허용한 IP목록이 있으면 허용한 IP만 접근 허용
			$where_data[]			= array("field"=>$mb_fields["access_ip"]["fn_ip"],"value"=>$remote_ip);
			$ip_allow_check		= $mdb->get_var(mbw_get_add_query(array("column"=>"count(*)","table"=>$mb_admin_tables["access_ip"]), $where_data));
			if($ip_allow_check==0){		//접근 허용 목록에 없으면 차단
				echo 'IP address ['.esc_attr($ip).'] is not allowed';
				exit;
			}
		}
	}
}

if(!function_exists('mbw_get_prevent_content_copy')){
	function mbw_get_prevent_content_copy(){
		if(mbw_get_trace("mbw_get_prevent_content_copy")==""){
			mbw_add_trace("mbw_get_prevent_content_copy");
			$prevent_script	= 'function disable_ctrlkey(e){var k;	if(window.event){k=window.event.keyCode;if(window.event.ctrlKey){if(window.event.srcElement.nodeName=="INPUT"||window.event.srcElement.nodeName=="SELECT"||window.event.srcElement.nodeName=="TEXTAREA") return true;else if((k==65||k==67||k== 83||k==88)) return false;}}else{k=e.which;if(e.ctrlKey){if((e.target.nodeName=="INPUT"||e.target.nodeName=="SELECT"||e.target.nodeName=="TEXTAREA")) return true;else if((k==65||k==67||k== 83||k==88)) return false;}}return true;}';
			$prevent_script	= $prevent_script.'function disable_select(e){if(e.target.nodeName!="INPUT"&&e.target.nodeName!="SELECT"&&e.target.nodeName!="TEXTAREA"&&e.target.nodeName!="HTML") return false;}';
			$prevent_script	= $prevent_script.'function disable_select_ie(){if(window.event.srcElement.nodeName !="INPUT" && window.event.srcElement.nodeName!="SELECT" && window.event.srcElement.nodeName!="TEXTAREA") return false;}';
			$prevent_script	= $prevent_script.'function disable_context(e){alert("Context Menu disabled");return false;}';
			$prevent_script	= $prevent_script.'document.onkeydown			= disable_ctrlkey;';
			$prevent_script	= $prevent_script.'document.oncontextmenu		= disable_context;';
			$prevent_script	= $prevent_script.'if(navigator.userAgent.indexOf("MSIE")==-1){document.onmousedown	= disable_select;}else{document.onselectstart=disable_select_ie;}';			
			return $prevent_script;
		}
	}
}

if(!function_exists('mbw_get_resize_responsive')){
	function mbw_get_resize_responsive($device_type){
		if(defined("HT_THEME")) return "";
		if(mbw_get_trace("mbw_get_resize_responsive")==""){
			mbw_add_trace("mbw_get_resize_responsive");

			$responsive_script		= 'function resizeResponsive(){';
				$responsive_script		.= 'var nWidth	= window.innerWidth;';	
				if(mbw_get_vars("device_type")=="desktop"){
					$responsive_array		= array("1200","992","768");
					$responsive_script		.= 'if(nWidth>='.$responsive_array[0].'){jQuery(".mb-desktop").removeClass("mb-desktop").addClass("mb-desktop-large");jQuery(".mb-tablet").removeClass("mb-tablet").addClass("mb-desktop-large");jQuery(".mb-mobile").removeClass("mb-mobile").addClass("mb-desktop-large");';
					$responsive_script		.= '}else if(nWidth>='.$responsive_array[1].'){jQuery(".mb-desktop-large").removeClass("mb-desktop-large").addClass("mb-desktop");jQuery(".mb-tablet").removeClass("mb-tablet").addClass("mb-desktop");jQuery(".mb-mobile").removeClass("mb-mobile").addClass("mb-desktop");';
					$responsive_script		.= '}else if(nWidth>='.$responsive_array[2].'){jQuery(".mb-desktop-large").removeClass("mb-desktop-large").addClass("mb-tablet");jQuery(".mb-desktop").removeClass("mb-desktop").addClass("mb-tablet");jQuery(".mb-mobile").removeClass("mb-mobile").addClass("mb-tablet");';		
					$responsive_script		.= '}else if(nWidth<'.$responsive_array[2].'){jQuery(".mb-desktop-large").removeClass("mb-desktop-large").addClass("mb-mobile");jQuery(".mb-desktop").removeClass("mb-desktop").addClass("mb-mobile");jQuery(".mb-tablet").removeClass("mb-tablet").addClass("mb-mobile");}';
				}else{
					$type		= mbw_get_vars("device_type");
					$responsive_script		.= 'if(window.orientation && (window.orientation==90 || window.orientation==-90)){
						jQuery(".mb-'.$type.'").removeClass("mb-'.$type.'-portrait").addClass("mb-'.$type.'-landscape");
					}else{
						jQuery(".mb-'.$type.'").removeClass("mb-'.$type.'-landscape").addClass("mb-'.$type.'-portrait");
					}';
				}
			$responsive_script		.= '}';

			if(mbw_get_vars("device_type")=="desktop"){
				$responsive_script		.= 'if(typeof jQuery != "undefined"){ jQuery(window).on("resize",resizeResponsive);resizeResponsive();};';
			}else{
				$responsive_script		.= 'if(typeof jQuery != "undefined"){ jQuery(window).on("orientationchange",resizeResponsive);resizeResponsive();};';
			}
			return $responsive_script;
		}
	}
}

if(!function_exists('mbw_get_dir_entry')){
	function mbw_get_dir_entry($dir_name,$add_except=array(),$order="desc"){
		$path					= MBW_PLUGIN_PATH.$dir_name;
		$path					= rtrim($path,'/\\');
		$dir					= dir($path);
		$except				= array_merge(array(".",".."),$add_except);
		$items				= array();

		while (false !== ($entry = $dir->read())){
			if(strpos($entry,'.')!==0 && is_dir($path."/".$entry)){
				if(!in_array($entry, $except))
					$items[]		= $entry;
			}
		}
		if($order=="desc"){
			return array_reverse($items);
		}else{
			return $items;
		}	
	}
}
if(!function_exists('mbw_get_file_entry')){
	function mbw_get_file_entry($dir_name,$add_except=array(),$order="desc"){
		$path					= MBW_PLUGIN_PATH.$dir_name;
		$path					= rtrim($path,'/\\');
		$dir					= dir($path);
		$except				= array_merge(array(".",".."),$add_except);
		$items				= array();

		while (false !== ($entry = $dir->read())){
			if(strpos($entry,'.')!==0 && is_file($path."/".$entry)){
				if(!in_array($entry, $except)){
					$items[]		= str_replace(".php", "", $entry);
				}
			}
		}
		if($order=="desc"){
			return array_reverse($items);
		}else{
			return $items;
		}	
	}
}

if(!function_exists('mbw_set_format')){
	function mbw_set_format($value,$type="currency",$locale="ko_KR"){
		$value						= floatval($value);

		if($type=="currency"){			
			if(has_filter('mf_currency_format_locale')){
				$locale		= apply_filters("mf_currency_format_locale",$locale);
			}else if(strpos($value, '.')!==false) $locale="en_US";

			if($locale=="ko_KR"){
				$value				= '<span class="mb-number">'.number_format($value).'</span><span class="mb-currency">'.__MW("W_CURRENCY").'</span>';				
			}else if($locale=="en_US"){
				$value				= '<span class="mb-currency">$</span><span class="mb-number">'.number_format($value,2).'</span>';				
			}
		}else if($type=="count"){
			$value				= '<span class="mb-count">'.number_format($value).'</span><span class="mb-number-suffix">'.__MW("W_NUMBER_SUFFIX")."</span>";
		}else if($type=="point"){
			if(strpos($value, '.')!==false){
				$value				= '<span class="mb-point">'.number_format($value,2).'</span><span>P</span>';
			}else{
				$value				= '<span class="mb-point">'.number_format($value).'</span><span>P</span>';
			}			
		}else if($type=="number"){			
			if(strpos($value, '.')!==false){
				$value				= '<span class="mb-number">'.number_format($value,2).'</span>';
			}else{
				$value				= '<span class="mb-number">'.number_format($value).'</span>';
			}
		}else if($type=="file_size"){
			$value		= floatval($value)/1024;
			if($value<(1024)){
				$value		= (intval($value*10)/10)."KB";
			}else{
				$value		= (intval($value/10.24)/100)."MB";
			}
		}else{
			if(strpos($value, '.')!==false){
				$value				= '<span class="mb-number">'.number_format($value,2).'</span><span>'.$type.'</span>';
			}else{
				$value				= '<span class="mb-number">'.number_format($value).'</span><span>'.$type.'</span>';
			}			
		}	
		return $value;
	}
}
if(!function_exists('mbw_array_sort')){
	function mbw_array_sort($array, $order_by, $order_type='desc'){
		$result_array		= array();
		$sortable_array	= array();

		if (is_array($array) && count($array) > 0) {
			foreach ($array as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $key2 => $value2) {
						if ($key2 == $order_by) {
							$sortable_array[$key] = $value2;
						}
					}
				} else {
					$sortable_array[$key] = $value;
				}
			}
			switch (strtolower($order_type)) {
				case 'asc':
					asort($sortable_array);
				break;
				case 'desc':
					arsort($sortable_array);
				break;
			}
			foreach ($sortable_array as $key => $value) {
				$result_array[$key] = $array[$key];
			}
		} else return $array;
		return $result_array;
	}
}
if(!function_exists('mbw_convert_to_bytes')){
	function mbw_convert_to_bytes($value,$output='b'){
		$unit			= preg_replace('/[^a-zA-Z]/', "", $value);
		$unit			= strtolower($unit);		
		$number	= floatval(preg_replace('/\D\.\D/', "", $value));

		switch ($unit) {
			case 'p':	//peta
			case 'pb':
				$number *= 1024*1024*1024*1024*1024;break;
			case 't':	//tera
			case 'tb':
				$number *= 1024*1024*1024*1024;break;
			case 'g':	//giga
			case 'gb':
				$number *= 1024*1024*1024;break;
			case 'm':	//mega
			case 'mb':
				$number *= 1024*1024;break;
			case 'k':	//kilo
			case 'kb':
				$number *= 1024;break;
		}

		$output			= strtolower($output);
		switch ($output) {
			case 'b':
				break;
			case 'p':	//peta
			case 'pb':
				$number /= (1024*1024*1024*1024*1024);break;
			case 't':	//tera
			case 'tb':
				$number /= (1024*1024*1024*1024);break;
			case 'g':	//giga
			case 'gb':
				$number /= (1024*1024*1024);break;
			case 'm':	//mega
			case 'mb':
				$number /= (1024*1024);break;
			case 'k':	//kilo
			case 'kb':
				$number /= (1024);break;
		}
		return $number;
	}
}

if(!function_exists('mbw_iconv')){
	function mbw_iconv($charset,$str){
		$value	= $str;
		if(function_exists('iconv')){
			if($charset=="UTF-8"){
				$value	= iconv("EUC-KR","UTF-8",$value);
			}else if($charset=="EUC-KR"){
				$value	= iconv("UTF-8","EUC-KR",$value);
			}			
		}
		return $value;
	}
}

if(!function_exists('mbw_get_icon')){
	function mbw_get_icon($type,$option){
		$icon_html		= "";

		if($type=="reply"){
			//답글일 경우 제목 앞에 공간 채우기
			$reply_depth		= $option;
			$icon_html			= "";
			if($reply_depth>0){
				for($i=1;$i<$reply_depth;$i++){
					$icon_html		= $icon_html."&nbsp;";
				}
				$icon_html		= $icon_html.'<img class="list-i-reply" alt="reply" style="vertical-align:middle;" src="'.MBW_SKIN_URL.'images/icon_reply_head.gif" />';
			}
		}
		return $icon_html;
	}
}
if(!function_exists('mbw_board_date_format1')){
	function mbw_board_date_format1($value,$mode="basic"){
		if(empty($value)) return $value;
		$date		= substr($value,0,-3);
		if(has_filter('mf_board_date_format1')) $date	= apply_filters("mf_board_date_format1",$value,$mode);
		return $date;
	}
}
if(!function_exists('mbw_value_filter')){
	function mbw_value_filter($data,$type="basic"){
		if(empty($data)) return $data;
		if($type=='basic'){
			$pattern		= "/[^0-9a-zA-Z\&\_\,\.\-\=]/";
		}else if($type=='date1'){
			$pattern		= "/[^0-9\s\/\:\-]/";		//숫자,공백,슬래시,:,-
		}else if($type=='name'){
			$pattern		= "/[^0-9a-zA-Z\_\-]/";		//영문,숫자,-,_
		}else if($type=='class'){
			$pattern		= "/[^0-9a-zA-Z\s\_\-]/";		//영문,숫자,공백,-,_
		}else if($type=='color'){
			$pattern		= "/[^0-9a-zA-Z\#\,\.\(\)]/";		//16진수 컬러 코드, rgba 값
		}else if($type=='int'){
			$pattern		= "/[^0-9]/";		//숫자
		}else if($type=='number'){
			$pattern		= "/[^0-9\,\.\-]/";		//숫자,-,.
		}else{
			$pattern		= "/[^0-9a-zA-Z\&\_\,\.\-\=]/";
		}
		if(is_array($data)){
			foreach($data as $key => $value){
				if(is_array($value)){
					$data[$key]	= mbw_value_filter($value,$type);
				}else{
					$data[$key]	= preg_replace($pattern, "", $value);
				}
			}
		}else{
			$data	= preg_replace($pattern, "", $data);
		}		
		return $data;
	}
}
if(!function_exists('mbw_search_text_highlight')){
	function mbw_search_text_highlight($search,$text,$highlight){
		$pattern				= '#(?!<.*?)(%s)(?![^<>]*?>)#i';
		$search				= (array) $search;
		foreach ($search as $value){
			$text				= preg_replace(sprintf($pattern, preg_quote($value)), $highlight, $text);
		}
		return $text;
	}
}
if(!function_exists('mbw_get_nocache_header')){
	function mbw_get_nocache_header(){
		if ( ! headers_sent() ) {
			header("Expires: Wed, 11 Jan 1984 05:00:00 GMT");
			header("Cache-Control: private, must-revalidate,max-age=0, no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
			header("Pragma: no-cache");
		}		
	}
}
if(!function_exists('mbw_get_nocache_meta')){
	function mbw_get_nocache_meta(){
		echo '<meta http-equiv="cache-control" content="max-age=0" />';
		echo '<meta http-equiv="cache-control" content="no-cache" />';
		echo '<meta http-equiv="expires" content="0" />';
		echo '<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />';
		echo '<meta http-equiv="pragma" content="no-cache" />';
	}
}


if(!function_exists('__STYLE')){
	function __STYLE($style){
		if(!empty($style))	return ' style="'.esc_attr($style).'"';
		else return "";
	}
}
if(!function_exists('__MW')){
	function __MW($word,$count=1){		
		if(is_array($word)){			
			foreach($word as $key => $value){				
				if(strpos($value, 'W_')===0 || strpos($value, 'MSG_')===0) $word[$key]	= mbw_get_message($value);
				if(mbw_get_option("wp_multi_language")) $word[$key]		= __($value, "mangboard");
			}
		}else{			
			if(strpos($word, 'W_')===0 || strpos($word, 'MSG_')===0) $word		= mbw_get_message($word);
			if(mbw_get_option("wp_multi_language")) $word	= __($word, "mangboard");

			if($count>1) $word		= $word."s";
		}		
		return $word;
	}
}

if(!function_exists('__MM')){
	function __MM($message,$args=NULL,$count=1){
		if(strpos($message, 'MSG_')===0 || strpos($message, 'W_')===0){
			$message			= mbw_get_message($message);
		}else if(strpos($message, '<br>MSG_')===0 || strpos($message, '<br>W_')===0){
			$message			= '<br>'.mbw_get_message(substr($message, 4));
		}else if(strpos($message, '<br>(MSG_')===0 || strpos($message, '<br>(W_')===0){
			$message			= '<br>('.mbw_get_message(rtrim(substr($message, 5),')')).')';
		}

		if(isset($args) && strpos($message, '%')!==false){
			$args		= __MW($args,$count);
			if(is_array($args)){
				$message			= vsprintf ( $message ,$args);
			}else{
				$message			= sprintf ( $message ,$args);
			}			
		}
		return $message;
	}
}
?>