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
			//네이버 및 구글 결제시작
			if(mbw_get_option("google_analytics_id")!="" || mbw_get_option("naver_analytics_id")!="" || mbw_get_option("facebook_pixel_id")!=""){
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
					$google_tag			= array();
					$naver_tag			= array();
					$pixel_tag				= array();
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
						$google_tag[]			= array('item_id'=>($item['product_pid']),'item_name'=>$title,'price'=>($item['sale_price']),'quantity'=>($item['order_count']),'item_category'=>$category1,'item_category2'=>$category2,'item_category3'=>$category3 );
						$naver_tag[]				= array('id'=>($item['product_pid']),'name'=>$title,'payAmount'=>($item['sale_price']),'quantity'=>($item['order_count']),'category'=>$category1);
						$pixel_tag[]				= array('id'=>($item['product_pid']),'quantity'=>($item['order_count']));

						$order_price				= $order_price+(floatval($item["sale_price"])*intval($item["order_count"]));
						if(!empty($item["order_options_price"])){
							$options_price		= $options_price+floatval($item["order_options_price"]);
						}
					}
					if(mbw_get_option("google_analytics_id")!=""){
						if(!empty($google_tag)){
							$tag_json			= json_encode($google_tag, JSON_UNESCAPED_UNICODE);
							echo "<script type='text/javascript'> if(window.gtag){ gtag('event', 'begin_checkout', {'currency': 'KRW','value': ".esc_js($order_price+$options_price).",'items': ".$tag_json."}); }</script>";
						}else{
							echo "<script type='text/javascript'> if(window.gtag){ gtag('event', 'begin_checkout'); }</script>";
						}
					}
					if(mbw_get_option("naver_analytics_id")!=""){
						if(!empty($naver_tag)){
							$tag_json			= json_encode($naver_tag, JSON_UNESCAPED_UNICODE);
							echo "<script type='text/javascript'> if(window.wcs){ var _conv={};_conv.type = 'begin_checkout';_conv.items=".$tag_json.";wcs.trans(_conv); }</script>";
						}else{
							echo "<script type='text/javascript'> if(window.wcs){ var _conv={};_conv.type = 'begin_checkout';wcs.trans(_conv); }</script>";
						}
					}
					if(mbw_get_option("facebook_pixel_id")!=""){
						if(!empty($pixel_tag)){
							$tag_json			= json_encode($pixel_tag, JSON_UNESCAPED_UNICODE);
							echo "<script type='text/javascript'> if(window.fbq){ fbq('track', 'InitiateCheckout', {'value': ".esc_js($order_price+$options_price).",'currency': 'KRW','contents': ".$tag_json."}); }</script>";
						}else{
							echo "<script type='text/javascript'> if(window.fbq){ fbq('track', 'InitiateCheckout'); }</script>";
						}
					}
				}		
			}
		}else if(mbw_get_board_name()=='commerce_product' && mbw_get_param("mode")=="view" && mbw_get_board_item('fn_pid')!=''){
			//네이버 및 구글 상품 상세페이지
			if(mbw_get_option("google_analytics_id")!="" || mbw_get_option("naver_analytics_id")!="" || mbw_get_option("facebook_pixel_id")!=""){				
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
				if(mbw_get_option("google_analytics_id")!=""){
					$google_tag		= array();
					$google_tag[]	= array( 'item_id'=>(mbw_get_board_item('fn_pid')),'item_name'=>$title,'price'=>(mbw_get_board_item('fn_sale_price')),'quantity'=>1, 'item_category'=>$category1,'item_category2'=>$category2,'item_category3'=>$category3 );
					$tag_json			= json_encode($google_tag, JSON_UNESCAPED_UNICODE);
					echo "<script type='text/javascript'> if(window.gtag){ gtag('event', 'view_item', {'currency': 'KRW','value': ".esc_js(mbw_get_board_item('fn_sale_price')).",'items': ".$tag_json."}); }</script>";
				}
				if(mbw_get_option("naver_analytics_id")!=""){
					$naver_tag		= array();
					$naver_tag[]		= array( 'id'=>(mbw_get_board_item('fn_pid')),'name'=>$title,'payAmount'=>(mbw_get_board_item('fn_sale_price')),'quantity'=>1, 'category'=>$category1);
					$tag_json			= json_encode($naver_tag, JSON_UNESCAPED_UNICODE);
					echo "<script type='text/javascript'> if(window.wcs){ var _conv={};_conv.type = 'view_product';_conv.items=".$tag_json.";wcs.trans(_conv); }</script>";
				}
				if(mbw_get_option("facebook_pixel_id")!=""){
					echo '<script type="text/javascript"> if(window.fbq){ fbq("track", "ViewContent", {content_ids: ["'.esc_js(mbw_get_board_item('fn_pid')).'"],content_name: "'.esc_js($title).'",content_category: "'.esc_js($category1).'",content_type: "product",value: '.esc_js(mbw_get_board_item('fn_sale_price')).',currency: "KRW"}); }</script>';
				}
			}
		}else if(mbw_get_board_name()=='commerce_order_result' && !empty($_SESSION['commerce_billing_id'])){
			//결제완료
			if(mbw_get_option("google_analytics_id")!="" || mbw_get_option("naver_analytics_id")!="" || mbw_get_option("facebook_pixel_id")!=""){

				global $mb_commerce_tables,$mb_fields,$mdb;
				//주문번호로 결제 상품을 불러옴
				$billing_id			= $_SESSION['commerce_billing_id'];
				$billing_id			= preg_replace("/[^0-9a-zA-Z_,-]/u", "", $billing_id);
				$billing_id2			= "'".implode("','",explode(",",$billing_id))."'";
				$select_query		= mbw_get_add_query(array("column"=>"*","join"=>"none"), array(array("field"=>$mb_fields["commerce_order"]["fn_billing_id"],"value"=>"(".$billing_id2.")","sign"=>"in")),array("fn_pid"=>"desc"));
				$order_items			= $mdb->get_results($select_query,ARRAY_A);

				if(!empty($order_items)){
					$google_tag			= array();
					$naver_tag			= array();
					$pixel_tag				= array();
					$order_price			= 0;
					$order_id				= $order_items[0]["order_id"];					
					foreach($order_items as $item){
						if(!empty($item['order_products'])){
							$order_products		= maybe_unserialize($item['order_products']);
							foreach($order_products as $product){
								$title				= strip_tags(mbw_htmlspecialchars_decode($product['title']));
								$category1		= $product['category1'];
								$category2		= $product['category2'];
								$category3		= $product['category3'];
								if(!empty($category1)){
									$category1		= strip_tags(mbw_htmlspecialchars_decode($category1));
								}							
								if(!empty($category2)){
									$category2		= strip_tags(mbw_htmlspecialchars_decode($category2));
								}							
								if(!empty($category3)){
									$category3		= strip_tags(mbw_htmlspecialchars_decode($category3));
								}
								$google_tag[]	= array('item_id'=>($product['product_pid']),'item_name'=>$title,'price'=>($product['sale_price']),'quantity'=>($product['order_count']),'item_category'=>$category1,'item_category2'=>$category2,'item_category3'=>$category3 );
								$naver_tag[]		= array('id'=>($product['product_pid']),'name'=>$title,'payAmount'=>($product['sale_price']),'quantity'=>($product['order_count']),'category'=>$category1);
								$pixel_tag[]		= array('id'=>($product['product_pid']),'quantity'=>($product['order_count']));
							}
						}
						$order_price		= $order_price+(floatval($item["order_price"]));
					}
					if(empty($order_price)) $order_price		= 1;
					if(mbw_get_option("google_analytics_id")!=""){
						if(!empty($google_tag)){
							$tag_json			= json_encode($google_tag, JSON_UNESCAPED_UNICODE);
							echo "<script type='text/javascript'> if(window.gtag){ gtag('event', 'purchase', {'transaction_id': '".esc_js($order_id)."','value': ".esc_js($order_price).",'shipping': ".esc_js(mbw_get_board_item('fn_shipping_cost')).",'currency': 'KRW','items': ".$tag_json."}); }</script>";
						}else{
							echo "<script type='text/javascript'> if(window.gtag){ gtag('event', 'purchase', {'transaction_id': '".esc_js($order_id)."','value': ".esc_js($order_price).",'currency': 'KRW'}); }</script>";
						}
					}
					if(mbw_get_option("naver_analytics_id")!=""){
						if(!empty($naver_tag)){
							$tag_json			= json_encode($naver_tag, JSON_UNESCAPED_UNICODE);
							echo "<script type='text/javascript'> if(window.wcs){ var _conv={};_conv.type = 'purchase';_conv.value='".esc_js($order_price)."';_conv.id='".esc_js($order_id)."';_conv.items=".$tag_json.";wcs.trans(_conv); }</script>";
						}else{
							echo "<script type='text/javascript'> if(window.wcs){ var _conv={};_conv.type = 'purchase';_conv.value='".esc_js($order_price)."';_conv.id='".esc_js($order_id)."';wcs.trans(_conv); }</script>";
						}
					}
					//페이스북 결제완료
					if(mbw_get_option("facebook_pixel_id")!=""){
						if(!empty($pixel_tag)){
							$tag_json			= json_encode($pixel_tag, JSON_UNESCAPED_UNICODE);
							echo "<script type='text/javascript'> if(window.fbq){ fbq('track', 'Purchase', {'value': ".esc_js($order_price).",'currency': 'KRW','contents': ".$tag_json."}); }</script>";
						}else{
							echo "<script type='text/javascript'> if(window.fbq){ fbq('track', 'Purchase', {'value': ".esc_js($order_price).",'currency': 'KRW'}); }</script>";
						}						
					}
				}
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
						echo "<script type='text/javascript'> if(window.gtag){ gtag('event', 'sign_up'); }</script>";
					}
					//네이버 회원가입
					if(mbw_get_option("naver_analytics_id")!=""){
						echo "<script type='text/javascript'> if(window.wcs){ var _conv = {};_conv.type = sign_up';wcs.trans(_conv); }</script>";
					}
					//페이스북 회원가입
					if(mbw_get_option("facebook_pixel_id")!=""){
						echo '<script type="text/javascript"> if(window.fbq){ fbq("track", "CompleteRegistration"); }</script>';
					}
				}
			}
		}
	}
}
?>