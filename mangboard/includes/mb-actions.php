<?php
add_action('wp_head', 'mbw_head',1);
if(!function_exists('mbw_head')){
	function mbw_head(){
		mbw_add_trace("mbw_head");
		mbw_head_meta();	
		mbw_analytics("today_visit");
	}
}
if(!function_exists('mbw_head_meta')){
	function mbw_head_meta(){
		if(mbw_get_trace("mbw_head_meta")==""){
			mbw_add_trace("mbw_head_meta");
			mbw_analytics("today_page_view");
			global $mstore,$mdb,$mb_admin_tables,$mb_fields,$mb_board_table_name;
			global $post,$mb_table_prefix;
			
			if(empty($mb_board_table_name)) $mb_board_table_name		= mbw_get_board_table_name(mbw_get_board_name());

			$title					= "";
			$image_path			= "";
			$description			= "";
			$keywords			= "";
			$author				= "";
			$tag					= "";
			$published_time		= "";
			$updated_time		= "";
			$page_type			= 'article';
			$site_name			= get_bloginfo('name');
			$page_url				= "";
			$canonical			= "";
			$is_secret				= 0;
			$sale_status			= 1;
			$post_id				= 0;
			if(!empty($post->ID)) {
				$post_id		= $post->ID;
			}
			if(function_exists('get_permalink')){
				$canonical			= get_permalink();
			}
			if(isset($_SERVER["HTTP_HOST"])){
				if(isset($_SERVER["REQUEST_URI"])){
					$page_url			= "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
				}else{
					$page_url			= "http://".$_SERVER["HTTP_HOST"];
				}
				if(empty($canonical)) $canonical		= $page_url;
			}

			if(!empty($mb_fields["select_board"]) && mbw_get_param("mode")=="view" && (strpos($mb_board_table_name, $mb_table_prefix.'commerce_')!==0 || strpos($mb_board_table_name, $mb_table_prefix.'commerce_product')===0)){
				$where_query		= $mdb->prepare(" WHERE ".$mb_fields["select_board"]["fn_pid"]."=%d", mbw_get_param("board_pid"));
				//$select_field		= implode(",",array_values($mstore->get_board_select_fields(array("fn_title","fn_content","fn_image_path","fn_user_picture","fn_user_name","fn_tag","fn_reg_date","fn_modify_date"))));
				$board_item		= mbw_get_board_item_query("select * from ".$mb_board_table_name.$where_query." limit 1");
				
				if(!empty($board_item)){
					$is_secret				= mbw_get_board_item("fn_is_secret",false);
					$canonical			= mbw_get_url(array('vid'=>mbw_get_board_item("fn_pid",false)),"","");
					$page_url				= $canonical;
					$title					= mbw_get_board_item("fn_title",false);
					$author				= mbw_get_board_item("fn_user_name",false);
					if(empty($title)) $title		= $author;
					$keywords			= mbw_get_board_item("fn_tag",false);
					$reg_date			= mbw_get_board_item("fn_reg_date",false);
					$modify_date		= mbw_get_board_item("fn_modify_date",false);
					$reg_time			= strtotime($reg_date);
					$published_time	= gmdate(DATE_ATOM, $reg_time);
					if(!empty($modify_date)){
						$modify_time		= strtotime($modify_date);					
						$updated_time	= gmdate(DATE_ATOM, $modify_time);
					}else{
						$modify_time		= "";
						$updated_time	= "";
					}

					if(mbw_get_board_name()=='commerce_product' && mbw_get_board_item("fn_product_description",false)!=""){
						$page_type	= 'product';
						$description	= mbw_get_board_item("fn_product_description",false);
						$sale_status	= intval(mbw_get_board_item("fn_sale_status",false));
					}else if(mbw_get_board_item("fn_content",false)!=""){
						$description	= mbw_get_board_item("fn_content",false);
						if(mbw_get_board_item("fn_data_type")=="html") $description			= mbw_htmlspecialchars_decode($description);
					}
					if(mbw_get_board_item("fn_image_path")!=""){
						$image_path	= mbw_get_image_url("url",mbw_get_board_item("fn_image_path"));
					}else if(mbw_get_board_item("fn_user_picture")!=""){
						$image_path	= mbw_get_image_url("url",mbw_get_board_item("fn_user_picture"));
					}else{
						if(is_singular()){
							$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
							if(!empty($large_image_url[0])) $image_path		= $large_image_url[0];
						}
					}
				}
			}else{				
				$post_content	= "";
				if(!empty($post->post_content)){
					$post_content		= $post->post_content;
					$post_content		= preg_replace("/\[.*\]/s", "", $post_content); 
				}

				$seo				= mbw_get_seo_meta($post_id);
				$title				= $seo["mb_seo_title"];
				$description	= $seo["mb_seo_description"];
				$keywords		= $seo["mb_seo_keyword"];

				if(empty($title)) $title				= wp_title("", false);
				if(empty($title)) $title				= $site_name;
				if(empty($description)) {
					if(is_front_page()) $description		= get_bloginfo( 'description' );
					else if(is_singular() && !empty($post_content)) $description		= $post_content;
					else $description		= $title;
				}
				if(is_singular()){				
					$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'large' );
					if(!empty($large_image_url[0])) $image_path		= $large_image_url[0];
					//$author			= get_the_author_meta('display_name', $post->post_author);

					$published_time	= get_the_date( DATE_W3C );
					$updated_time	= get_the_modified_date( DATE_W3C );
				}
			}
			if(empty($image_path) && mbw_get_option("seo_default_image")!="") $image_path		= mbw_get_option("seo_default_image");

			$title				= str_replace("\"", "'", strip_tags(html_entity_decode($title)));
			$title				= trim(str_replace("&nbsp;", " ", $title));
			$image_path	= trim(str_replace("\"", "'", $image_path));
			if(!empty($keywords)){ 
				$keywords		= str_replace("\"", "'", strip_tags(html_entity_decode($keywords)));
				$keywords		= trim(str_replace("&nbsp;", " ", $keywords));
			}
			
			if(!empty($description)){
				$description	= preg_replace("/<script(.*?)<\/script>/is", "", $description);
				$description	= preg_replace("/<style(.*?)<\/style>/is", "", $description);
				$description	= str_replace(array("\r\n","\n","\t","&nbsp;"," ","  "), " ", strip_tags(mbw_htmlspecialchars_decode($description)));
				$description	= str_replace(array("  ","   ","    "), " ", $description);
				$description	= trim(str_replace("\"", "'", $description));

				if(!empty($description)){ 
					if(function_exists('mb_substr')) $description	= mb_substr($description, 0, 200, mbw_get_option("encoding"));
					else $description	= substr($description, 0, 200);
				}
			}		

			$page_url	= mbw_check_url(strip_tags($page_url));
			$canonical	= mbw_check_url(strip_tags($canonical));

			$script		= '<script type="text/javascript">';

			$script		.= 'var shareData				= {"url":"","title":"","image":"","content":""};';
			$script		.= 'shareData["url"]			= "'.esc_url_raw($page_url).'";';
			$script		.= 'shareData["title"]			= "'.esc_js($title).'";';
			$script		.= 'shareData["image"]		= "'.esc_url_raw($image_path).'";';
			$script		.= 'shareData["content"]	= "'.esc_js($description).'";';


			if(mbw_get_option("naver_site_verification")!=""){
				echo '<meta name="naver-site-verification" content="'.esc_attr(mbw_get_option("naver_site_verification")).'" />'.PHP_EOL;
			}
			if(mbw_get_option("google_site_verification")!=""){
				echo '<meta name="google-site-verification" content="'.esc_attr(mbw_get_option("google_site_verification")).'" />'.PHP_EOL;
			}

			if(mbw_get_option("use_seo") && empty($is_secret)){
				echo '<!-- Mangboard SEO Start -->'.PHP_EOL;
				echo '<link rel="canonical" href="'.esc_url_raw($canonical).'" />'.PHP_EOL;
				echo '<meta property="og:url" content="'.esc_url_raw($page_url).'" />'.PHP_EOL;
				if(is_front_page()){
					echo '<meta property="og:type" content="website" />'.PHP_EOL;
				}else{
					echo '<meta property="og:type" content="'.esc_attr($page_type).'" />'.PHP_EOL;
				}				
				if(!empty($title)){
					echo '<meta property="og:title" content="'.esc_attr($title).'" />'.PHP_EOL;
					echo '<meta name="title" content="'.esc_attr($title).'" />'.PHP_EOL;
					echo '<meta name="twitter:title" content="'.esc_attr($title).'" />'.PHP_EOL;
				}				
				if(!empty($description)){
					echo '<meta property="og:description" content="'.esc_attr($description).'" />'.PHP_EOL;
					echo '<meta property="description" content="'.esc_attr($description).'" />'.PHP_EOL;
					echo '<meta name="description" content="'.esc_attr($description).'" />'.PHP_EOL;
					echo '<meta name="twitter:card" content="summary" />'.PHP_EOL;
					echo '<meta name="twitter:description" content="'.esc_attr($description).'" />'.PHP_EOL;
				}
				if(!empty($image_path)){
					echo '<meta property="og:image" content="'.esc_attr($image_path).'" />'.PHP_EOL;
					echo '<meta name="twitter:image" content="'.esc_attr($image_path).'" />'.PHP_EOL;
				}
				if($page_type=='product'){					
					if($sale_status!=5 && $sale_status!=6){
						echo '<meta property="product:price:amount" content="'.esc_attr(mbw_get_board_item("fn_regular_price")).'" />'.PHP_EOL;
						echo '<meta property="product:price:currency" content="KRW" />'.PHP_EOL;
						echo '<meta property="product:sale_price:amount" content="'.esc_attr(mbw_get_board_item("fn_sale_price")).'" />'.PHP_EOL;
						echo '<meta property="product:sale_price:currency" content="KRW" />'.PHP_EOL;
					}
					if(mbw_get_board_item("fn_product_brand")!="" && mbw_get_board_item('fn_product_type')!=""){
						$product_type		= mbw_get_board_item('fn_product_type');
						if(strpos($product_type,"reservation")!==0 && strpos($product_type,"price")!==0){
							echo '<meta property="product:brand" content="'.esc_attr(mbw_get_board_item("fn_product_brand")).'">'.PHP_EOL;
						}
					}
					if(mbw_get_board_item("fn_category1")!="") echo '<meta property="product:category" content="'.esc_attr(mbw_get_board_item("fn_category1")).'">'.PHP_EOL;
					if(mbw_get_board_item("fn_category2")!="") echo '<meta property="product:category" content="'.esc_attr(mbw_get_board_item("fn_category2")).'">'.PHP_EOL;
					if(mbw_get_board_item("fn_category3")!="") echo '<meta property="product:category" content="'.esc_attr(mbw_get_board_item("fn_category3")).'">'.PHP_EOL;					
				}
				if(!empty($keywords)){
					echo '<meta name="keywords" content="'.esc_attr($keywords).'" />'.PHP_EOL;
					if(!is_front_page()){ 
						$tags		= explode(',',$keywords);
						foreach ( $tags as $tag ) {
							echo '<meta property="article:tag" content="'.esc_attr($tag).'" />'.PHP_EOL;
						}
					}
				}
				if(!empty($author)){
					echo '<meta name="author" content="'.esc_attr($author).'" />'.PHP_EOL;
				}
				if(!empty($published_time)){
					echo '<meta property="article:published_time" content="'.esc_attr($published_time).'" />'.PHP_EOL;
				}
				if(!empty($updated_time) && $published_time!=$updated_time){
					echo '<meta property="article:modified_time" content="'.esc_attr($updated_time).'" />'.PHP_EOL;
					echo '<meta property="og:updated_time" content="'.esc_attr($updated_time).'" />'.PHP_EOL;
				}				
				echo '<meta property="og:locale" content="'.esc_attr(mbw_get_option("locale")).'" />'.PHP_EOL;
				echo '<meta property="og:site_name" content="'.esc_attr($site_name).'" />'.PHP_EOL;
				echo '<!-- Mangboard SEO End -->'.PHP_EOL;
			}

			$mb_user_level	= mbw_get_user("fn_user_level");
			//복사 방지 스크립트		
			if(mbw_get_option("prevent_content_copy") && $mb_user_level<mbw_get_option("admin_level")){
				$script	.= mbw_get_prevent_content_copy();		
			}
			$script			.= '</script>';

			global $mb_scripts; 
			$mb_scripts[]		= $script;
			
			// 디비 버젼 체크
			global $mb_version,$mb_db_version;			
			if(mbw_get_option("db_version")!=$mb_db_version){
				if(is_file(MBW_PLUGIN_PATH."includes/install/update.php")){
					require(MBW_PLUGIN_PATH."includes/install/update.php");
				}
			}
			
			if(mbw_get_option("mb_version")!=$mb_version) mbw_update_option('mb_version',$mb_version);

			if($mstore->get_board_name()!="" && mbw_get_trace("mbw_check_shortcode")!="" && !empty($mb_fields["board_options"]["fn_post_id"])){
				$post_id		= mbw_get_board_option("fn_post_id");
				if(empty($post_id) && !empty($post->ID)){
					$mdb->query($mdb->prepare("update ".$mb_admin_tables["board_options"]." set ".$mb_fields["board_options"]["fn_post_id"]."=%d where `".$mb_fields["board_options"]["fn_board_name2"]."`=%s;", $post->ID,$mstore->get_board_name()));
				}
			}
		}	
	}
}

add_action('wp_loaded', 'mbw_loaded_head', 25);
if(!function_exists('mbw_loaded_head')){
	function mbw_loaded_head(){
		add_filter('pre_get_document_title','mbw_filter_header_title', 25 ,1);
		if(current_theme_supports('title-tag')){
			add_filter('document_title_parts','mbw_filter_header_title',25,1);			
		}else{
			add_filter('wp_title','mbw_filter_header_title',25,1);
		}
	}
}
add_action('wp_head', 'mbw_meta_generator',99);
if(!function_exists('mbw_meta_generator')){
	function mbw_meta_generator(){
		echo '<meta name="generator" content="Powered by MangBoard" />'.PHP_EOL;
	}
}
if(!function_exists('mbw_filter_header_title')){
	function mbw_filter_header_title($w_title){
		if(!is_singular()) return $w_title;
		global $post,$mstore,$mdb,$mb_admin_tables,$mb_fields,$mb_board_table_name,$mb_table_prefix;
		if(empty($mb_board_table_name)) $mb_board_table_name		= mbw_get_board_table_name(mbw_get_board_name());
		if(strpos($mb_board_table_name, $mb_table_prefix.'commerce_')===0 && strpos($mb_board_table_name, $mb_table_prefix.'commerce_product')!==0) return $w_title;
		if(!empty($mb_fields["select_board"]) && mbw_get_param("mode")=="view" && mbw_get_param("board_pid")!=""){
			if(mbw_get_board_option("fn_board_type")=="board" || (mbw_get_board_option("fn_board_type")=="link" && isset($mb_fields["select_board"]["fn_is_secret"]))){
				$where_query		= $mdb->prepare(" WHERE ".$mb_fields["select_board"]["fn_pid"]."=%d and ".$mb_fields["select_board"]["fn_is_secret"]."=0", mbw_get_param("board_pid"));
			}else{
				$where_query		= $mdb->prepare(" WHERE ".$mb_fields["select_board"]["fn_pid"]."=%d", mbw_get_param("board_pid"));
			}
			if(!mbw_is_admin_table($mb_board_table_name) && !empty($mb_fields["select_board"]["fn_title"])){
				$title			= $mdb->get_var("select ".$mb_fields["select_board"]["fn_title"]." from ".$mb_board_table_name.$where_query." limit 1");
				if(!empty($title)){
					$title			= str_replace("\"", "'", strip_tags(html_entity_decode($title)));
					$title			= trim(str_replace("&nbsp;", " ", $title));
					if(is_array($w_title)){
						if(isset($w_title["title"])) $w_title["title"]		= $title." ";
					}else{
						$w_title		= $title." ";
					}
				}
			}
		}
		return $w_title;
	}
}

add_action('wp_head', 'mbw_print_head_scripts',200);
if(!function_exists('mbw_print_head_scripts')){
	function mbw_print_head_scripts(){
		mbw_add_trace("mbw_print_head_scripts");
		if(mbw_get_option("facebook_pixel_id")!=""){
			echo '<script type="text/javascript"> !function(f,b,e,v,n,t,s)  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?  n.callMethod.apply(n,arguments):n.queue.push(arguments)};  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version="2.0";  n.queue=[];t=b.createElement(e);t.async=!0;  t.src=v;s=b.getElementsByTagName(e)[0];  s.parentNode.insertBefore(t,s)}(window, document,"script",  "https://connect.facebook.net/en_US/fbevents.js");  fbq("init", "'.esc_js(mbw_get_option('facebook_pixel_id')).'");  fbq("track", "PageView");</script><noscript><img height="1" width="1" style="display:none"  src="https://www.facebook.com/tr?id='.esc_js(mbw_get_option('facebook_pixel_id')).'&ev=PageView&noscript=1"/></noscript>';
		}
		if(mbw_get_option("google_analytics_id")!=""){			
			echo "<script async src='https://www.googletagmanager.com/gtag/js?id=".esc_js(mbw_get_option("google_analytics_id"))."'></script><script>window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag('js', new Date());gtag('config', '".esc_js(mbw_get_option("google_analytics_id"))."');</script>";
		}
		if(mbw_get_option("naver_analytics_id")!=""){
			echo '<script type="text/javascript" src="//wcs.naver.net/wcslog.js"></script> <script type="text/javascript"> if(!wcs_add) {var wcs_add = {};}; wcs_add["wa"] = "'.esc_js(mbw_get_option("naver_analytics_id")).'";wcs.inflow();</script>';
		}
	}
}
add_action('wp_footer', 'mbw_footer',15);
if(!function_exists('mbw_footer')){	
	function mbw_footer(){
		mbw_add_trace("mbw_footer");
		if(mbw_get_option("naver_analytics_id")!=""){
			echo '<script type="text/javascript">if(!_nasa){ var _nasa={};} wcs_do(_nasa);</script>';
		}
		if(mbw_get_option("resize_responsive") && mbw_get_trace("mbw_get_resize_responsive")==""){
			$resize_responsive_script	= mbw_get_resize_responsive(mbw_get_vars("device_type"));
			if(!empty($resize_responsive_script)){
				echo '<script type="text/javascript">'.$resize_responsive_script.'</script>';
			}
		}
		mbw_footer_scripts();
	}
}
add_action('wp_print_footer_scripts', 'mbw_print_footer_scripts',15);
if(!function_exists('mbw_print_footer_scripts')){	
	function mbw_print_footer_scripts(){
		mbw_footer_scripts();
	}
}
add_action('admin_footer', 'mbw_footer_scripts',15);
if(!function_exists('mbw_footer_scripts')){	
	function mbw_footer_scripts(){
		if(mbw_get_trace("mbw_footer_scripts")==""){
			mbw_add_trace("mbw_footer_scripts");
			global $mb_api_urls;
			$script		= '<script type="text/javascript">';
			$script		.= 'if(typeof(mb_urls)==="undefined"){var mb_urls = {};}; ';
			foreach($mb_api_urls as $key => $value){
				$script		.= 'mb_urls["'.$key.'"]			= "'.esc_js($mb_api_urls[$key]).'";';
			}
			$script		.= '</script>';
			echo $script;
		}
	}
}
add_action('wp_logout', 'mbw_wp_logout',16);
if(!function_exists('mbw_wp_logout')){
	function mbw_wp_logout(){	
		mbw_add_trace("mbw_wp_logout");
	}
}
add_filter('query_vars','mbw_plugin_add_trigger',10,1);
if(!function_exists('mbw_plugin_add_trigger')){
	function mbw_plugin_add_trigger($vars) {
		$vars[] = 'mb_trigger';
		$vars[] = 'mb_user';
		$vars[] = 'mb_ext';
		return $vars;
	}
}
add_action('template_redirect', 'mbw_plugin_trigger_check');
if(!function_exists('mbw_plugin_trigger_check')){	
	function mbw_plugin_trigger_check() {
		$mb_trigger	= get_query_var('mb_trigger');
		$mb_ext			= get_query_var('mb_ext');
		if($mb_trigger == "rss") {
			if(is_file(MBW_PLUGIN_PATH."includes/mb-rss.php"))
				require(MBW_PLUGIN_PATH."includes/mb-rss.php");
			exit;
		}else if($mb_trigger == "rss2") {
			if(is_file(MBW_PLUGIN_PATH."includes/mb-rss2.php"))
				require(MBW_PLUGIN_PATH."includes/mb-rss2.php");
			exit;
		}else if($mb_trigger == "rss3") {
			if(is_file(MBW_PLUGIN_PATH."includes/mb-rss3.php"))
				require(MBW_PLUGIN_PATH."includes/mb-rss3.php");
			exit;
		}else if($mb_trigger == "file") {
			$file_type			= "application/octet-stream";
			if(mbw_get_param("file_type")!="") $file_type	= mbw_get_param("file_type");
			$file_name		= date("Ymd").".xls";
			if(mbw_get_param("file_name")!="") $file_name	= trim(mbw_get_param("file_name"));
			$file_content		= mbw_value_filter(mbw_get_param("file_content"));
			$file_path			= "";
			if(strpos($file_content, 'tempfile_')===0){
				if(strpos($file_content, 'tempfile_excel')===0){
					$file_path		= MBW_UPLOAD_PATH.'excel/'.$file_content.'.xlsx';
					$file_name	= str_replace(".xls", ".xlsx", $file_name);
				}
			}
			$file_name		= strip_tags($file_name);
			$file_type			= strip_tags($file_type);
			header('Expires: 0');
			header('Pragma: public');
			header('Cache-Control: must-revalidate');
			header('Content-Description: File Transfer');
			header("Content-type: ".$file_type.";charset=UTF-8");
			if(preg_match('/(MSIE|Trident)/i', $_SERVER['HTTP_USER_AGENT'])){
				header("Content-Disposition: attachment; filename=\"".rawurlencode($file_name)."\"");
			}else{
				header("Content-Disposition: attachment; filename=\"".$file_name."\"");
			}
			if(empty($file_path)){
				echo '<html><head><meta http-equiv="Content-Type" content="'.esc_attr($file_type).'; charset=UTF-8"></head><body>';
				echo mbw_get_param("file_content");
				echo '</body></html>';
			}else if(is_file($file_path)){
				ob_clean();
				flush();
				@readfile($file_path);
				@unlink($file_path);
			}
			exit;
		}else if((get_query_var('mb_user')) == "logout") {
			$logout_redirect_to		= home_url();
			if(has_filter('mf_user_logout_redirect_to')) 
				$logout_redirect_to		= apply_filters("mf_user_logout_redirect_to",$logout_redirect_to);
			mbw_logout();
			header('Location: '.$logout_redirect_to);
			exit;
		}else if($mb_ext == "file") {
			include(MBW_PLUGIN_PATH."includes/mb-file.php");
			exit;
		}else if($mb_ext == "seditor" && mbw_get_param("se_skin")!="") {
			if(function_exists('show_admin_bar')) show_admin_bar(false);
			$skin_name		= mbw_value_filter(mbw_get_param("se_skin"));
			include(MBW_PLUGIN_PATH."plugins/editors/smart/".$skin_name.".php");
			exit;
		}else if($mb_ext == "heditor" && mbw_get_param("se_skin")!="") {
			if(function_exists('show_admin_bar')) show_admin_bar(false);
			$skin_name		= mbw_value_filter(mbw_get_param("se_skin"));
			include(MBW_PLUGIN_PATH."plugins/editors/hometory_smart/".$skin_name.".php");
			exit;
		}else if($mb_ext == "seditor_uploader") {
			if(function_exists('show_admin_bar')) show_admin_bar(false);
			include(MBW_PLUGIN_PATH."plugins/editors/smart/sample/photo_uploader/photo_uploader.php");
			exit;
		}else if($mb_ext == "seditor_callback") {
			if(function_exists('show_admin_bar')) show_admin_bar(false);
			include(MBW_PLUGIN_PATH."plugins/editors/smart/sample/photo_uploader/callback.php");
			exit;
		}else if($mb_ext == "captcha" && mbw_get_option("kcaptcha_image_path")!="") {
			include(mbw_get_option("kcaptcha_image_path"));
			exit;
		}
	}
}
add_action( 'wp_ajax_mb_board', 'mbw_api_callback' );
add_action( 'wp_ajax_mb_comment', 'mbw_api_callback' );
add_action( 'wp_ajax_mb_user', 'mbw_api_callback' );
add_action( 'wp_ajax_mb_heditor', 'mbw_api_callback' );
add_action( 'wp_ajax_mb_template', 'mbw_api_callback' );
add_action( 'wp_ajax_mb_commerce', 'mbw_api_callback' );
add_action( 'wp_ajax_mb_uploader', 'mbw_api_callback' );
add_action( 'wp_ajax_mb_custom', 'mbw_api_callback' );

add_action( 'wp_ajax_skin_mb_board', 'mbw_api_callback' );
add_action( 'wp_ajax_skin_mb_comment', 'mbw_api_callback' );
add_action( 'wp_ajax_skin_mb_user', 'mbw_api_callback' );
add_action( 'wp_ajax_skin_mb_heditor', 'mbw_api_callback' );
add_action( 'wp_ajax_skin_mb_template', 'mbw_api_callback' );
add_action( 'wp_ajax_skin_mb_commerce', 'mbw_api_callback' );
add_action( 'wp_ajax_skin_mb_custom', 'mbw_api_callback' );

add_action( 'wp_ajax_nopriv_mb_board', 'mbw_api_callback' );
add_action( 'wp_ajax_nopriv_mb_comment', 'mbw_api_callback' );
add_action( 'wp_ajax_nopriv_mb_user', 'mbw_api_callback' );
add_action( 'wp_ajax_nopriv_mb_heditor', 'mbw_api_callback' );
add_action( 'wp_ajax_nopriv_mb_template', 'mbw_api_callback' );
add_action( 'wp_ajax_nopriv_mb_commerce', 'mbw_api_callback' );
add_action( 'wp_ajax_nopriv_mb_uploader', 'mbw_api_callback' );
add_action( 'wp_ajax_nopriv_mb_custom', 'mbw_api_callback' );

add_action( 'wp_ajax_nopriv_skin_mb_board', 'mbw_api_callback' );
add_action( 'wp_ajax_nopriv_skin_mb_comment', 'mbw_api_callback' );
add_action( 'wp_ajax_nopriv_skin_mb_user', 'mbw_api_callback' );
add_action( 'wp_ajax_nopriv_skin_mb_heditor', 'mbw_api_callback' );
add_action( 'wp_ajax_nopriv_skin_mb_template', 'mbw_api_callback' );
add_action( 'wp_ajax_nopriv_skin_mb_commerce', 'mbw_api_callback' );
add_action( 'wp_ajax_nopriv_skin_mb_custom', 'mbw_api_callback' );

if(!function_exists('mbw_api_callback')){
	function mbw_api_callback() {
		mbw_add_trace("mbw_api_callback");
		global $mdb,$mstore,$mb_fields,$mb_request_mode,$mb_languages,$send_data;
		global $mb_admin_tables,$mb_board_table_name,$mb_comment_table_name;
		$action		= mbw_get_param("action");
		$file_name		= str_replace( "_", "-", $action).".php";

		if($action=="mb_uploader"){
			if(is_file(MBW_PLUGIN_PATH."includes/".$file_name))
				require(MBW_PLUGIN_PATH."includes/".$file_name);
		}else if(strpos($action, 'skin')===0){
			$file_name		= str_replace( "skin-", "", $file_name);
			if(is_file(MBW_SKIN_PATH."api/".$file_name))
				require(MBW_SKIN_PATH."api/".$file_name);
		}else{
			if(is_file(MBW_PLUGIN_PATH."api/".$file_name))
				require(MBW_PLUGIN_PATH."api/".$file_name);
		}	
	}
}

//최근 게시물 데이타 저장
if(!function_exists('mbw_latest_api_body')){
	function mbw_latest_api_body(){	
		global $mdb,$mb_fields,$mb_admin_tables,$mstore;
		global $mb_board_table_name,$mb_comment_table_name;
		$where_query			= "";
		$query_command	= "";

		if(mbw_get_param("board_action")=="write" || mbw_get_param("board_action")=="reply"){
			$option_name		= "";
			$board_pid			= 0;
			$data					= array();
			$latest_data			= array();

			$board_name					= mbw_get_param("board_name");
			$data["title"]					= "";
			$data["time"]					= mbw_get_timestamp();
			$data["post_id"]				= $mdb->get_var($mdb->prepare("SELECT ".$mb_fields["board_options"]["fn_post_id"]." FROM ".$mb_admin_tables["board_options"]." where ".$mb_fields["board_options"]["fn_board_name2"]."=%s limit 1",$board_name));		
			
			if(mbw_get_param("mode")=="comment"){
				$option_name				= "mb_latest_comment_data";
				$data["parent_pid"]		= intval(mbw_get_param("parent_pid"));
				$data["pid"]				= intval(mbw_get_param("comment_pid"));
				$data["table"]				= $mb_comment_table_name;
				$data["name"]				= $board_name;
			}else if(mbw_get_param("mode")=="write"){		
				$option_name				= "mb_latest_board_data";			
				$data["pid"]				= intval(mbw_get_param("board_pid"));
				$data["table"]				= $mb_board_table_name;
				$data["name"]				= $board_name;
				if(mbw_is_admin_table($mb_board_table_name)) return;
			}
			if(!empty($option_name)){
				$latest_data		= get_option($option_name);
				if($latest_data === false || empty($latest_data) ||  !is_array($latest_data)) $latest_data				= array();		
				
				//삭제되서 중복된 데이타가 있으면 제거하기
				foreach($latest_data as $key=>$value){				
					if($value["table"]==$data["table"] && $value["pid"]==$data["pid"]){
						unset($latest_data[$key]);
					}
				}
				$latest_data[]		= $data;
				if(count($latest_data)>20) array_shift($latest_data);
				update_option($option_name,$latest_data);
			}
		}	
	}
}
if(!function_exists('mbw_board_skin_search_add_value')){
	function mbw_board_skin_search_add_value(){	
		if(mbw_get_param('idx')!=""){
			echo '<input type="hidden" name="idx" value="'.esc_attr(mbw_get_param('idx')).'" />';
		}
	}
}
add_action('mbw_board_skin_search', 'mbw_board_skin_search_add_value',5);
add_action('mbw_board_skin_footer', 'mbw_set_form_session',5);
add_action('mbw_board_api_body', 'mbw_latest_api_body',5);
add_action('mbw_comment_api_body', 'mbw_latest_api_body',5); 
//Shortcode Action 등록

add_shortcode("mb_image", 'mbw_create_image_panel');
if(!function_exists('mbw_create_image_panel')){
	function mbw_create_image_panel($args, $content=""){
		if(!empty($content)) $args["content"]		= $content;
		$data					= mbw_init_item_data("image",$args);	
		$image_html		= "";
	
		if(!empty($data["align"]))
			$image_html	= $image_html.'<div style="text-align:'.$data["align"].';">';
		else 
			$image_html	= $image_html.'<div>';
		$image_html	= $image_html.mbw_get_input_template("image",$data);
		$image_html	= $image_html.'</div>';
		return $image_html;		
	}
}

add_action('mbw_board_skin_header', 'mbw_add_board_setup_button');		//게시판 설정 버튼 추가
if(!function_exists('mbw_add_board_setup_button')){
	function mbw_add_board_setup_button(){
		if(mbw_is_admin() && !mbw_is_admin_page()){
			if(mbw_get_trace("mbw_add_board_setup_button")==""){
				mbw_add_trace("mbw_add_board_setup_button");
				if(mbw_get_board_name()!="" && mbw_get_board_option("fn_pid")!=""){
					$button_name		= __MW("W_SETTING");
					$button_html		= '<button onclick="movePage(\''.admin_url('admin.php').'?page=mbw_board_options&board_name=board_options&mode=write&board_action=modify&board_pid='.mbw_get_board_option("fn_pid").'\');return false;" class="btn btn-default btn-setup" title="'.$button_name.'" type="button"><span>'.$button_name.'</span></button>';
					if(mbw_get_vars("device_type")=="desktop"){
						$button_name		= __MW("W_MENU_BOARD");
						$button_html		.= '<button onclick="movePage(\''.admin_url('admin.php').'?page=mbw_board_options&board_name='.mbw_get_board_option("fn_board_name2").'\');return false;" class="btn btn-default btn-manage" title="'.$button_name.'" type="button"><span>'.$button_name.'</span></button>';
					}
					mbw_add_left_button("list",$button_html);
				}
			}
		}
	}
}
//특정 위치에 있는 css 파일 불러오기
//[mb_page_style path="skins/bbs_basic/style.css"]
add_action('mbw_shortcode', 'mbw_load_page_style',1);
if(!function_exists('mbw_load_page_style')){
	function mbw_load_page_style($content){
		$mb_shortcode		= "mb_page_style";
		if(strpos($content, '['.$mb_shortcode." path=") !== false){
			$index1			= strpos($content,'['.$mb_shortcode." path=")+21;
			$path				= substr($content,$index1,strpos($content,"\"",$index1)-$index1);
			$path_array		= explode(",",$path);
			foreach($path_array as $value){
				if(!empty($value)) loadStyle(MBW_PLUGIN_URL.$value);
			}
		}
	}
}
add_shortcode('mb_page_style', 'mbw_create_page_style');
if(!function_exists('mbw_create_page_style')){
	function mbw_create_page_style($args, $content=""){
		return "";
	}
}

if(!function_exists('mbw_board_form_post_id')){
	function mbw_board_form_post_id(){	
		if(mbw_get_param("mode")=="list"){
			$args	= mbw_get_vars("shortcode_args");
			if(!empty($args) && !empty($args["post_id"])){
				echo '<input type="hidden" name="link_post_id" id="link_post_id" value="'.esc_attr($args["post_id"]).'" />';
			}
		}
	}
}
add_action('mbw_board_skin_form', 'mbw_board_form_post_id',5); 
add_action('mbw_board_skin_search', 'mbw_board_form_post_id',5); 
add_action('mbw_board_skin_search2', 'mbw_board_form_post_id',5); 

if(!function_exists('mbw_filter_board_model')){
	function mbw_filter_board_model($model){
		$args	= mbw_get_vars("shortcode_args");
		$post_id		= "";
		if(!empty($args)){
			if(!empty($args["post_id"])){
				$post_id		= $args["post_id"];
			}else if(!empty($args["link_id"])){
				$post_id		= $args["link_id"];
			}
		}else if(mbw_get_param("link_post_id")!=""){
			$post_id		= mbw_get_param("link_post_id");
		}
		if(!empty($post_id)){
			$link_attr		= ',"link":"post_id","link_id":"'.$post_id.'"';
			$model			= str_replace(',"link":"view"',$link_attr,$model);
		}
		$mode				= mbw_get_param('mode');
		$model_key		= mbw_get_model_key();
		$model_data1		= mbw_json_decode($model);
		$model_data2		= array();
		$is_modify			= false;
		$shortcode_args	= mbw_get_vars("shortcode_args");
		$key_name			= 'hide_'.$model_key;

		if(mbw_get_vars("device_type")=="mobile" && !empty($shortcode_args["mobile_".$key_name])){
			$hide_field		= $shortcode_args["mobile_".$key_name];
		}else if(mbw_get_vars("device_type")=="tablet" && !empty($shortcode_args["tablet_".$key_name])){
			$hide_field		= $shortcode_args["tablet_".$key_name];
		}else if(!empty($shortcode_args[$key_name])){
			$hide_field		= $shortcode_args[$key_name];
		}else return $model;
		
		if(mbw_get_vars("device_type")=="mobile" && !empty($shortcode_args["mobile_".$key_name.'_level'])){
			$check_level		= intval($shortcode_args["mobile_".$key_name.'_level']);
		}else if(mbw_get_vars("device_type")=="tablet" && !empty($shortcode_args["tablet_".$key_name.'_level'])){
			$check_level		= intval($shortcode_args["tablet_".$key_name.'_level']);
		}else if(!empty($shortcode_args[$key_name.'_level'])){
			$check_level		= intval($shortcode_args[$key_name.'_level']);
		}else $check_level		= 0;
		if(!empty($check_level)){
			$user_level				= mbw_get_user("fn_user_level");
			if($user_level>$check_level) return $model;
		}
		if(mbw_get_vars("device_type")=="mobile" && !empty($shortcode_args["mobile_".$key_name.'_group'])){
			$check_group		= ($shortcode_args["mobile_".$key_name.'_group']);
		}else if(mbw_get_vars("device_type")=="tablet" && !empty($shortcode_args["tablet_".$key_name.'_group'])){
			$check_group		= ($shortcode_args["tablet_".$key_name.'_group']);
		}else if(!empty($shortcode_args[$key_name.'_group'])){
			$check_group		= ($shortcode_args[$key_name.'_group']);
		}else $check_group		= "";
		if(!empty($check_group)){
			$check_group		= ','.$check_group.',';
			if(mbw_is_login()) $user_group			= mbw_get_user("fn_user_group");
			else $user_group			= 'guest';
			if(strpos($check_group, ','.$user_group.',')===false) return $model;
		}
		if(mbw_get_vars("device_type")=="mobile" && !empty($shortcode_args["mobile_".$key_name.'_except_group'])){
			$check_group		= ($shortcode_args["mobile_".$key_name.'_except_group']);
		}else if(mbw_get_vars("device_type")=="tablet" && !empty($shortcode_args["tablet_".$key_name.'_except_group'])){
			$check_group		= ($shortcode_args["tablet_".$key_name.'_except_group']);
		}else if(!empty($shortcode_args[$key_name.'_except_group'])){
			$check_group		= ($shortcode_args[$key_name.'_except_group']);
		}else $check_group		= "";
		if(!empty($check_group)){
			$check_group		= ','.$check_group.',';
			if(mbw_is_login()) $user_group			= mbw_get_user("fn_user_group");
			else $user_group			= 'guest';
			if(strpos($check_group, ','.$user_group.',')!==false) return $model;
		}
		
		if(!empty($hide_field)){
			$hide_field		= ','.$hide_field.',';
			foreach($model_data1 as $key => $data){
				if((!empty($data['field']) && strpos($hide_field, ','.$data['field'].',')!==false) || (!empty($data['type']) && strpos($hide_field, ','.$data['type'].',')!==false)){
					$is_modify			= true;
					continue;
				}
				$model_data2[]		= $data;
			}
		}
		if($is_modify){
			return json_encode($model_data2);
		}else{
			return $model;
		}
	}
}
add_filter('mf_board_model', 'mbw_filter_board_model',5,1); 
if(!function_exists('mbw_filter_widget_latest_items')){
	function mbw_filter_widget_latest_items($items,$data,$w_query,$permalink="",$widget=""){
		if(empty($data['name'])) return $items;
		else $name			= mbw_value_filter(str_replace('"',"",$data['name']),"name");
		if(strlen($name)>3){
			if(!empty($data['join'])){
				global $mdb,$mstore,$mb_admin_tables,$mb_fields;

				if(empty($data['list_size'])) $list_size			= 5;
				else $list_size			= intval($data['list_size']);
				if(!empty($data['list_page'])) $list_page	= intval($data['list_page'])-1;
				else $list_page		= 0;
				if($list_page<0) $list_page	= 0;

				if(empty($data['category1'])) $category1			= "";
				else $category1			= $data['category1'];
				if(empty($data['category2'])) $category2			= "";
				else $category2			= $data['category2'];
				if(empty($data['category3'])) $category3			= "";
				else $category3			= $data['category3'];

				if(empty($data['order_by'])) $order_by			= "reg_date";
				else $order_by			= mbw_value_filter($data['order_by'],"name");
				if($order_by=="pid" || $order_by=="gid") $order_by	= "reg_date";
				if(empty($data['order_type'])) $order_type			= "desc";
				else $order_type			= mbw_value_filter($data['order_type'],"name");
				if(empty($data['link_type'])) $link_type			= "view";
				else $link_type			= $data['link_type'];

				$url_param		= "";
				if(!empty($category1)) $url_param	.= "category1=".$category1."&";
				if(!empty($category2)) $url_param	.= "category2=".$category2."&";
				if(!empty($category3)) $url_param	.= "category3=".$category3."&";
				if($link_type=="item") $url_param		.= "item=";
				else $url_param		.= "vid=";

				if(!empty($items)){
					foreach($items as $key=>$value){
						$items[$key]['board_name']		= $name;
						$items[$key]['board_url']			= $permalink;
					}
				}
				$join_array		= explode(',',$data['join']);
				$index			= 0;
				if(!empty($join_array)){
					foreach($join_array as $item){
						if($item==$name) continue;
						$post_id	= $mdb->get_var($mdb->prepare("SELECT ".$mb_fields["board_options"]["fn_post_id"]." FROM ".$mb_admin_tables["board_options"]." where ".$mb_fields["board_options"]["fn_board_name2"]."=%s limit 1", $item));
						if(empty($post_id)) continue;
						$permalink		= get_permalink($post_id);
						if(strpos($permalink, '?') === false)	$permalink		= $permalink."?";
						else $permalink		= $permalink."&";
						$permalink		.= $url_param;

						$item				= mbw_value_filter($item);
						$order_by		= mbw_value_filter($order_by);
						$order_type		= trim(strtolower(mbw_value_filter($order_type)));
						if($order_type=="desc"){
							$order_type		= "desc";
						}else{
							$order_type		= "asc";
						}
						$latest_items		= $mdb->get_results($mdb->prepare("SELECT * FROM " . mbw_get_table_name($item) . $w_query." order by %1s %1s limit %d,%d;",$order_by,$order_type,$list_page,$list_size), ARRAY_A);

						foreach($latest_items as $key=>$value){
							$latest_items[$key]['board_name']		= $item;
							$latest_items[$key]['board_url']		= $permalink;
						}
						$items			= array_merge($items,$latest_items);
						$index++;
						if($index>4) break;
					}
				}
				$items			= mbw_array_sort($items, $order_by, $order_type);
				$items			= array_slice($items, 0, $list_size);
			}
		}else{
			if($data['name']=="wp"){
				if(empty($data['list_size'])) $list_size			= "4";
				else $list_size			= mbw_value_filter($data['list_size'],"int");
				if(empty($data['order_by'])) $order_by			= "date";		//date, comment_count, ID
				else $order_by			= mbw_value_filter($data['order_by'],"name");
				if(empty($data['order_type'])) $order_type			= "desc";		//desc, asc, meta_value, meta_value_num
				else $order_type			= mbw_value_filter($data['order_type'],"name");
				if(empty($data['post_type'])) $post_type			= "post";			//post, page
				else{
					if(strpos($data['post_type'],',') === false){
						$post_type			= mbw_value_filter($data['post_type'],"name");
					}else{
						$post_type			= explode(',',mbw_value_filter($data['post_type'],"basic"));
					}
				}
				if(empty($data['post_status'])) $post_status			= "publish";		//publish, inherit, auto-draft, draft, trash
				else $post_status			= mbw_value_filter($data['post_status'],"name");
				if(empty($data['image_size'])) $image_size			= "medium";		//full, large, medium, thumbnail
				else $image_size			= mbw_value_filter($data['image_size'],"name");
				if(empty($data['list_type'])) $list_type			= "";			// 대표이미지가 등록된 게시물만 불러올 경우 image로 입력
				else $list_type			= mbw_value_filter($data['list_type'],"name");

				$args = array( "post_type" => $post_type, "post_status" => $post_status, "numberposts" => intval($list_size), "orderby" => $order_by,"order" => $order_type );
				if($post_type!="attachment"){
					if(strpos($w_query, 'image_path!=""') !== false || $list_type=="image"){	// list_type 값이 image 로 설정되었거나 갤러리 위젯일 경우 대표 이미지 있는 게시물만 불러오도록 설정					
						$args["meta_query"] = array( array( "key" => "_thumbnail_id", "value" => "0", "compare" =>">="));					
					}
				}else{
					$args["post_status"] = "inherit";
				}
				if(!empty($data['include'])){		//최근 게시물로 불러올 post_id를 쉼표로 구분해서 입력
					$args["include"] = explode(',',mbw_value_filter($data['include'],"number"));
				}
				if(!empty($data['exclude'])){		//최근 게시물에서 제외할 post_id를 쉼표로 구분해서 입력
					$args["exclude"] = explode(',',mbw_value_filter($data['exclude'],"number"));
				}
				if(!empty($data['category_name'])){		//카테고리 슬러그
					$args["category_name"] = ($data['category_name']);
				}
				if(!empty($data['category'])){		//카테고리 tag ID
					$args["category"] = mbw_value_filter($data['category'],"name");
				}
				if(!empty($data['meta_key'])){
					$args["meta_key"] = mbw_value_filter($data['meta_key'],"name");
				}

				$date_query		= array();
				if(!empty($data['date_after'])){
					$date_query["after"]		= mbw_value_filter(trim($data['date_after']),"date1");
					if($data['date_after']!=$date_query["after"]){		// -7 days 형태로 입력이 되었을 경우
						$date_query["after"]		= date('Y-m-d',strtotime($data['date_after']));
					}
				}
				if(!empty($data['date_before'])){
					$date_query["before"]		= mbw_value_filter(trim($data['date_before']),"date1");
					if($data['date_before']!=$date_query["before"]){	// -7 days 형태로 입력이 되었을 경우
						$date_query["before"]		= date('Y-m-d',strtotime($data['date_before']));
					}
				}
				if(!empty($date_query)){
					$args["date_query"] = $date_query;
				}
				$posts = get_posts($args);
				if(!empty($posts)){
					foreach($posts as $post){
						if(!empty($post->ID)){
							$item	= array("pid"=>"","gid"=>"","title"=>"","reg_date"=>"","user_name"=>"","content"=>"","category1"=>"","category2"=>"","category3"=>"","data_type"=>"html","image_path"=>"","comment_count"=>"","board_url"=>"","vote_good_count"=>"0","vote_bad_count"=>"0","hit"=>"","ext1"=>"","ext2"=>"","ext3"=>"","ext4"=>"","ext5"=>"","ext6"=>"","ext7"=>"","ext8"=>"","ext9"=>"","ext10"=>"");

							$post_pid						= $post->ID;
							if(isset($post->post_title)) $item["title"]					= strip_tags($post->post_title);
							if(isset($post->post_date)) $item["reg_date"]			= $post->post_date;
							if(isset($post->post_author)) $item["user_name"]		= strip_tags(get_the_author_meta("display_name", $post->post_author));
							if(isset($post->comment_count)) $item["comment_count"]	= $post->comment_count;
							$item["board_url"]			= get_permalink($post_pid);

							if(isset($post->post_content)){
								$post_content		= $post->post_content;
								$post_content		= trim(str_replace(array("\r\n","\n","&nbsp;"," ","  "), " ", $post_content));
								$post_content		= preg_replace("/<script(.*?)<\/script>/is", "", $post_content);
								$post_content		= preg_replace("/<style(.*?)<\/style>/is", "", $post_content);
								$post_content		= trim(strip_tags($post_content));
								$item["content"]		= $post_content;
							}
							if($post_type=="attachment"){
								if(!empty($post->guid)){
									$item["image_path"]		= $post->guid;
								}
							}else{
								$wp_category		= get_the_category($post_pid);
								if(!empty($wp_category)){
									$category_data			= get_the_category($post_pid)[0];
									if(isset($category_data->slug) && $category_data->slug!="uncategorized" && !empty($category_data->name)){
										$item["category1"]		= strip_tags($category_data->name);
									}
								}
								$thumbnail_id	= get_post_thumbnail_id($post_pid);
								if(!empty($thumbnail_id)){
									$item["image_path"]		= wp_get_attachment_image_src( $thumbnail_id, $image_size )[0];
								}
							}
							$items[]			= $item;
						}
					}
				}
			}
		}
		return $items;
	}
}
add_filter('mf_widget_latest_items', 'mbw_filter_widget_latest_items',1,5);
if(!function_exists('mbw_set_theme_body_classs')){
	function mbw_set_theme_body_class($class){
		mbw_add_trace("mbw_set_theme_body_class");
		if(mbw_is_login()) $user_level	= mbw_get_user("fn_user_level");
		else $user_level	= 0;
		$add_class		= "mb-level-".$user_level;
		$theme_name	= wp_get_theme();
		if(strtolower($theme_name)!="hometory"){
			$device_type	= mbw_get_vars("device_type"); 
			$add_class	.= " mb-".$device_type."2";
		}
		$board_name	= mbw_get_board_name();
		if(!empty($board_name)){
			$add_class	.= " mb-name-".$board_name;
			if(mbw_get_param("mode")!="") $mode	= mbw_get_param("mode");
			else $mode	= "list";
			$add_class	.= " mb-mode-".$mode;
		}
		$add_class	= str_replace("_", "-", $add_class);

		if(is_array($class)){
			$class[]	= $add_class;
		}else if(is_string($class)){
			$class		.= " ".$add_class;
		}
		return $class;
	}
}
add_filter( 'body_class', 'mbw_set_theme_body_class', 100,1);
add_filter( 'admin_body_class', 'mbw_set_theme_body_class', 100,1);
/*
//WP Super Cache 플러그인 사용시 게시물,댓글 작성시에 캐시 초기화
function mbw_super_cache_clear_cache(){
	$clear_action			= array("write","modify","reply","delete","multi_modify","multi_delete","vote_good","vote_bad");	
	if(in_array(mbw_get_param("board_action"), $clear_action)){
		if(function_exists('wp_cache_clear_cache')) wp_cache_clear_cache();
	}
}
if(defined('WP_CACHE') && WP_CACHE){
	add_action('mbw_comment_api_body', 'mbw_super_cache_clear_cache',10); 
	add_action('mbw_board_api_body', 'mbw_super_cache_clear_cache',10); 
}
*/
?>