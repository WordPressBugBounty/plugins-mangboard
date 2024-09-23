<?php
//결제 전환추적
add_action('mbw_commerce_footer', 'mbw_order_conversion_tracking',1);
if(!function_exists('mbw_order_conversion_tracking')){	
	function mbw_order_conversion_tracking(){
		mbw_add_trace("mbw_order_conversion_tracking");
		if(mbw_get_board_name()=='commerce_billing' && !empty($_SESSION['commerce_order_id'])){		
			//구글 결제시작			
			if(mbw_get_option("google_analytics_id")!=""){
				echo "<script type='text/javascript'>gtag('event', 'begin_checkout');</script>";
			}
			//페이스북 결제시작
			if(mbw_get_option("facebook_pixel_id")!=""){
				echo '<script type="text/javascript">fbq("track", "InitiateCheckout");</script>';
			}
		}
		if(mbw_get_board_name()=='commerce_order_result' && mbw_get_board_item('fn_pid')!=''){		
			$order_price		= mbw_get_board_item('fn_order_price');
			if(empty($order_price)) $order_price		= 1;			
			//구글 결제완료
			if(mbw_get_option("google_analytics_id")!=""){
				echo "<script type='text/javascript'> gtag('event', 'purchase', {'transaction_id': '".esc_js(mbw_get_board_item('fn_billing_id'))."','affiliation': '".esc_js(get_bloginfo('name'))."','value': ".esc_js($order_price).",'currency': 'KRW'});</script>";
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