<?php
//템플릿 함수 등록(템플릿 타입의 접두사, 템플릿 함수명)
if(function_exists('mbw_add_template')) mbw_add_template("kcaptcha","mbw_get_kcaptcha_template");

if(!function_exists('mbw_get_kcaptcha_template')){
	function mbw_get_kcaptcha_template($mode, $data){
		$template_start	= "";
		$item_type		= $data["type"];		
		if(!empty($data["item_id"])){
			$t_id		= ' id="'.esc_attr($data["item_id"]).'"';
		}else{
			$t_id		= "";
		}
		if(!empty($data["name"])){
			$t_title		= ' title="'.esc_attr($data["name"]).'"';
			$t_alt		= ' alt="'.esc_attr($data["name"]).'"';
		}else{
			$t_title		= "";
			$t_alt		= "";
		}
		
		if($item_type=='kcaptcha_img'){
			if(empty($data["width"])) $data["width"]			= "60px";
			if(empty($data["height"])) $data["height"]		= "42px";
			$item_id				= "mb_kcaptcha";
			$index				= 1;
			if(mbw_get_vars('kcaptcha_index')!="") $index	= intval(mbw_get_vars('kcaptcha_index'))+1;

			$kcaptcha_mode		= intval(mbw_get_option("kcaptcha_mode"));
			if($kcaptcha_mode==0){ //사용안함
				return "";
			}else if($kcaptcha_mode!=1){
				$kcaptcha_cookie		= mbw_get_cookie("mb_security_mode");
				if(!empty($kcaptcha_cookie)) $kcaptcha_mode		= $kcaptcha_cookie;
				if($kcaptcha_mode==3){	
					$kcaptcha_url		= MBW_HOME_URL.'/?mb_ext=captcha&mode='.esc_attr($mode).'&board_action='.esc_attr(mbw_get_param("board_action"));
				}else{
					$kcaptcha_url		= mbw_get_option("kcaptcha_image_url").'?mode='.esc_attr($mode).'&board_action='.esc_attr(mbw_get_param("board_action"));
				}
			}

			if($kcaptcha_mode!=1 && (function_exists("imagejpeg") || function_exists("imagepng") || function_exists("imagegif"))){
				if($index==1){
					$template_start	= '<img'.__STYLE("width:80px;height:32px;vertical-align:top;").' onclick="mb_reloadImage()" class="mb_kcaptcha cursor_pointer" src="'.esc_url($kcaptcha_url).'" id="'.esc_attr($item_id).'" class="border-ccc-1"'.$t_alt.'/>';				
				}else{
					$template_start	= '<img'.__STYLE("width:80px;height:32px;vertical-align:top;").' onclick="mb_reloadImage_class()" class="mb_kcaptcha cursor_pointer" src="'.esc_url($kcaptcha_url).'" id="'.esc_attr($item_id.$index).'" class="border-ccc-1"'.$t_alt.'/>';				
				}		
				mbw_set_vars('kcaptcha_index',$index);
			}else{
				$session = @session_id();
				if(empty($session)) @session_start();
				if(mbw_get_param("board_action")!=""){
					$mode		= $mode."_".mbw_get_param("board_action");
				}
				if(strpos($mode, 'comment') === false && isset($_SESSION[$mode.'_captcha_time']) && $_SESSION[$mode.'_captcha_time']>=time()) exit;

				$keystring										= rand(1,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
				$_SESSION[$mode.'_captcha_keystring']		= $keystring;
				$_SESSION[$mode.'_captcha_time']			= time();

				$template_start	.= '<input'.$data["ext"].__STYLE("width:".esc_attr($data["width"]).";height:".esc_attr($data["height"])." !important;border:1px solid #ccc;vertical-align:middle;margin:0 !important;".esc_attr($data["style"])).' maxlength="6" value="'.esc_attr($keystring).'" title="'.esc_attr($keystring).'"  readonly />';
			}
			$template_start	.= '<input'.$data["ext"].__STYLE("width:".esc_attr($data["width"]).";height:".esc_attr($data["height"])." !important;".esc_attr($data["style"])).' name="'.esc_attr(mbw_set_form_name($data["type"])).'" maxlength="6" value="'.esc_attr($data["value"]).'"'.esc_attr($t_id).' type="text"'.$t_title.' />';
		}		
		return $template_start;
	}
}
?>