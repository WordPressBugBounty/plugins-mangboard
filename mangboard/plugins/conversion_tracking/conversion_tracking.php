<?php
if(!function_exists('mbw_init_conversion_tracking')){
	function mbw_init_conversion_tracking(){
		loadScript(MBW_PLUGIN_URL."plugins/conversion_tracking/js/main.js");
	}
}
add_action('wp_enqueue_scripts', 'mbw_init_conversion_tracking',50);
add_action('admin_enqueue_scripts', 'mbw_init_conversion_tracking',50);

//결제 전환추적
add_action('mbw_commerce_footer', 'mbw_order_conversion_tracking',1);
if(!function_exists('mbw_order_conversion_tracking')){	
	function mbw_order_conversion_tracking(){
		mbw_add_trace("mbw_order_conversion_tracking");
		if(mbw_get_board_name()=='commerce_billing' && !empty($_SESSION['commerce_billing_id'])){
			//구글 결제시작
			if(mbw_get_option("google_analytics_id")!=""){
				global $mb_commerce_tables,$mb_fields,$mdb;
				$product_items		= array();
				if(!empty($_SESSION['commerce_billing_id'])){
					//선택된 주문 상품을 불러옴
					$billing_id			= $_SESSION['commerce_billing_id'];
					$billing_id			= preg_replace("/[^0-9a-zA-Z_,-]/u", "", $billing_id);
					$billing_id2			= "'".implode("','",explode(",",$billing_id))."'";

					$select_query		= mbw_get_add_query(array("column"=>"*","user_pid"=>"shop_pid","table"=>$mb_commerce_tables["commerce_cart"]), array(array("field"=>$mb_fields["commerce_cart"]["fn_billing_id"],"value"=>"(".$billing_id2.")","sign"=>"in"),array("field"=>$mb_fields["commerce_cart"]["fn_user_pid"],"value"=>mbw_get_user("fn_pid")),array("field"=>$mb_fields["commerce_cart"]["fn_is_order"],"value"=>0)),array("fn_pid"=>"desc"));
					$product_items		= $mdb->get_results($select_query,ARRAY_A);	
				}
				if(empty($product_items)){
					//선택된 주문 상품이 없을 경우 장바구니 상품을 불러옴
					if(mbw_is_login()){	
						$select_query		= mbw_get_add_query(array("column"=>"*","user_pid"=>"shop_pid","table"=>$mb_commerce_tables["commerce_cart"]), array(array("field"=>$mb_fields["commerce_cart"]["fn_cart_id"],"value"=>"","sign"=>"!="),array("field"=>$mb_fields["commerce_cart"]["fn_user_pid"],"value"=>mbw_get_user("fn_pid")),array("field"=>$mb_fields["commerce_cart"]["fn_is_order"],"value"=>0)),array("fn_pid"=>"desc"));		
					}else{
						$cart_id			= mbw_get_cart_id();
						$select_query	= mbw_get_add_query(array("column"=>"*","user_pid"=>"shop_pid","table"=>$mb_commerce_tables["commerce_cart"]), array(array("field"=>$mb_fields["commerce_cart"]["fn_cart_id"],"value"=>$cart_id),array("field"=>$mb_fields["commerce_cart"]["fn_user_pid"],"value"=>"0"),array("field"=>$mb_fields["commerce_cart"]["fn_is_order"],"value"=>0)),array("fn_pid"=>"desc"));
					}
					$product_items		= $mdb->get_results($select_query,ARRAY_A);
				}
				if(!empty($product_items)){
					$order_price			= 0;
					$options_price		= 0;
					$tag_items			= array();
					foreach($product_items as $item){
						$title				= strip_tags(mbw_htmlspecialchars_decode($item['title']));
						$category1		= $item['category1'];
						$category2		= $item['category2'];
						$category3		= $item['category3'];
						if(!empty($category1)){
							$category1		= strip_tags(mbw_htmlspecialchars_decode($category1));
						}							
						if(!empty($category2)){
							$category2		= strip_tags(mbw_htmlspecialchars_decode($category2));
						}							
						if(!empty($category3)){
							$category3		= strip_tags(mbw_htmlspecialchars_decode($category3));
						}
						$tag_items[]		= array( 'item_id'=>($item['product_pid']),'item_name'=>$title,'price'=>($item['sale_price']),'quantity'=>($item['order_count']),'item_category'=>$category1,'item_category2'=>$category2,'item_category3'=>$category3 );
						$order_price				= $order_price+(floatval($item["sale_price"])*intval($item["order_count"]));
						if(!empty($item["order_options_price"])){
							$options_price		= $options_price+floatval($item["order_options_price"]);
						}
					}
					if(!empty($tag_items)){
						$tag_json			= json_encode($tag_items, JSON_UNESCAPED_UNICODE);
						echo "<script type='text/javascript'> gtag('event', 'begin_checkout', {'currency': 'KRW','value': ".esc_js($order_price+$options_price).",'items': ".$tag_json."});</script>";
					}else{
						echo "<script type='text/javascript'>gtag('event', 'begin_checkout');</script>";
					}
				}else{
					echo "<script type='text/javascript'>gtag('event', 'begin_checkout');</script>";
				}				
			}
			//페이스북 결제시작
			if(mbw_get_option("facebook_pixel_id")!=""){
				echo '<script type="text/javascript">fbq("track", "InitiateCheckout");</script>';
			}
		}else if(mbw_get_board_name()=='commerce_product' && mbw_get_param("mode")=="view" && mbw_get_board_item('fn_pid')!=''){
			if(mbw_get_option("google_analytics_id")!=""){
				$view_item		= array();
				$board_pid		= mbw_get_param("board_pid");
				if(mbw_get_board_item("fn_pid")!=$board_pid){
					global $mdb;
					$board_name	= mbw_get_board_name();
					$query			= $mdb->prepare("select * from ".mbw_get_table_name($board_name)." WHERE pid=%d limit 1;", $board_pid);
					$item				= $mdb->get_row($query,ARRAY_A);
					mbw_set_board_item($item,"",0);
				}
				$title				= strip_tags(mbw_htmlspecialchars_decode(mbw_get_board_item('fn_title')));
				$category1		= mbw_get_board_item('fn_category1');
				$category2		= mbw_get_board_item('fn_category2');
				$category3		= mbw_get_board_item('fn_category3');
				if(!empty($category1)){
					$category1		= strip_tags(mbw_htmlspecialchars_decode($category1));
				}				
				if(!empty($category2)){
					$category2		= strip_tags(mbw_htmlspecialchars_decode($category2));
				}				
				if(!empty($category3)){
					$category3		= strip_tags(mbw_htmlspecialchars_decode($category3));
				}
				$view_item[]		= array( 'item_id'=>(mbw_get_board_item('fn_pid')),'item_name'=>$title,'price'=>(mbw_get_board_item('fn_sale_price')),'quantity'=>1, 'item_category'=>$category1,'item_category2'=>$category2,'item_category3'=>$category3 );
				$tag_json			= json_encode($view_item, JSON_UNESCAPED_UNICODE);
				echo "<script type='text/javascript'> gtag('event', 'view_item', {'currency': 'KRW','value': ".esc_js(mbw_get_board_item('fn_sale_price')).",'items': ".$tag_json."});</script>";
			}
		}else if(mbw_get_board_name()=='commerce_order_result' && mbw_get_board_item('fn_pid')!=''){
			$order_price		= mbw_get_board_item('fn_order_price');
			if(empty($order_price)) $order_price		= 1;
			//구글 결제완료
			if(mbw_get_option("google_analytics_id")!=""){
				$order_products		= mbw_get_board_item('fn_order_products');
				if(!empty($order_products)){
					$order_products		= maybe_unserialize($order_products);
					$tag_items			= array();
					if(!empty($order_products)){
						foreach($order_products as $item){
							$title				= strip_tags(mbw_htmlspecialchars_decode($item['title']));
							$category1		= $item['category1'];
							$category2		= $item['category2'];
							$category3		= $item['category3'];
							if(!empty($category1)){
								$category1		= strip_tags(mbw_htmlspecialchars_decode($category1));
							}							
							if(!empty($category2)){
								$category2		= strip_tags(mbw_htmlspecialchars_decode($category2));
							}							
							if(!empty($category3)){
								$category3		= strip_tags(mbw_htmlspecialchars_decode($category3));
							}
							$tag_items[]		= array( 'item_id'=>($item['product_pid']),'item_name'=>$title,'price'=>($item['sale_price']),'quantity'=>($item['order_count']),'item_category'=>$category1,'item_category2'=>$category2,'item_category3'=>$category3 );
						}
					}
					if(!empty($tag_items)){
						$tag_json			= json_encode($tag_items, JSON_UNESCAPED_UNICODE);
						echo "<script type='text/javascript'> gtag('event', 'purchase', {'transaction_id': '".esc_js(mbw_get_board_item('fn_billing_id'))."','value': ".esc_js($order_price).",'shipping': ".esc_js(mbw_get_board_item('fn_shipping_cost')).",'currency': 'KRW','items': ".$tag_json."});</script>";
					}else{
						echo "<script type='text/javascript'> gtag('event', 'purchase', {'transaction_id': '".esc_js(mbw_get_board_item('fn_billing_id'))."','value': ".esc_js($order_price).",'currency': 'KRW'});</script>";
					}
				}else{
					echo "<script type='text/javascript'> gtag('event', 'purchase', {'transaction_id': '".esc_js(mbw_get_board_item('fn_billing_id'))."','value': ".esc_js($order_price).",'currency': 'KRW'});</script>";
				}
			}
			//네이버 결제완료
			if(mbw_get_option("naver_analytics_id")!=""){
				echo '<script type="text/javascript">if(!_nasa){ var _nasa={};};_nasa["cnv"] = wcs.cnv("1","'.esc_js($order_price).'");</script>';
			}
			//페이스북 결제완료
			if(mbw_get_option("facebook_pixel_id")!=""){
				echo '<script type="text/javascript">fbq("track", "Purchase", {value: '.esc_js($order_price).',currency: "KRW"});</script>';
			}
		}
	}
}
//회원가입 전환추적
add_action('mbw_user_footer', 'mbw_user_register_conversion_tracking',1);
if(!function_exists('mbw_user_register_conversion_tracking')){
	function mbw_user_register_conversion_tracking(){
		if(mbw_get_param("board_action")=='user_login' || mbw_get_param("board_action")=='login'){
			mbw_add_trace("mbw_user_register_conversion_tracking");

			if(!empty($_SERVER['HTTP_REFERER'])){
				$parse_url		= parse_url($_SERVER['HTTP_REFERER']);
				if( ((isset($parse_url['path']) && $parse_url['path']=='/user_register/') || (strpos($_SERVER['HTTP_REFERER'], 'step=2')!== false)) && mbw_get_param("ref")=="register" ){
					//구글 회원가입
					if(mbw_get_option("google_analytics_id")!=""){
						echo "<script type='text/javascript'>gtag('event', 'sign_up');</script>";
					}
					//네이버 회원가입
					if(mbw_get_option("naver_analytics_id")!=""){
						echo '<script type="text/javascript">if(!_nasa){ var _nasa={};};_nasa["cnv"] = wcs.cnv("2","10");</script>';
					}
					//페이스북 회원가입
					if(mbw_get_option("facebook_pixel_id")!=""){
						echo '<script type="text/javascript">fbq("track", "CompleteRegistration");</script>';
					}
				}
			}
		}
	}
}
?>