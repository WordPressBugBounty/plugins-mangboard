<?php
if(!function_exists('mbw_get_list_template')){
	function mbw_get_list_template($data,$tag=null,$echo=true){
		global $mstore,$mb_tags;
		
		if($tag==null){
			$tag					= array("t_tr"=>"tr","t_th"=>"th","t_td"=>"td");
		}
		
		$data					= mbw_init_item_data("list",$data,$tag);
		
		$template_start		= "";
		$template_end		= "";
		
		if(!empty($tag["t_td"])){
			$template_start	= '<'.$tag["t_td"].$data["td_class"].__STYLE($data["td_style"]).'>';
			$template_end	= '</'.$tag["t_td"].'>';
		}
		$link_url			= "";
		$link_attr		= "";

		if(!empty($data["link"])){
			if(strpos($data["link"],'fn_')===0){
				if(mbw_get_board_item($data["link"])!="") $link_url		= mbw_get_board_item($data["link"]);
				else $link_url		= mbw_get_url(array('vid'=>mbw_get_board_item('fn_pid')));			
			}else if($data["link"]=="view"){
				if(!empty($data["link_url"])){
					if($data["link_url"]=="vid"){
						$link_url		= mbw_get_url(array('vid'=>mbw_get_board_item('fn_pid')),"","");
					}else{
						$link_url		= mbw_get_url(array('vid'=>mbw_get_board_item('fn_pid')),$data["link_url"],"");
					}					
				}else{
					$link_url		= mbw_get_url(array('vid'=>mbw_get_board_item('fn_pid')));
				}
			}else if($data["link"]=="lightbox"){
				$link_url		= mbw_get_image_url("url",mbw_get_board_item('fn_image_path'));
				$link_attr	= ' rel="lightbox"';
			}else if($data["link"]=="post_id" && !empty($data["link_id"])){
				$link_url		= mbw_get_url(array('vid'=>mbw_get_board_item('fn_pid')),get_permalink($data["link_id"]),"");
			}else if(mbw_get_option($data["link"])!=""){
				$link_url		= mbw_get_url(array('vid'=>mbw_get_board_item('fn_pid')),mbw_check_permalink(mbw_get_option($data["link"])),"");
			}else{
				$link_url		= $data["link"];
			}
			if(strpos($link_url, '//') === false && strpos($link_url, '?') === false && strpos($link_url, 'http') !== 0) $link_url	= "http://".$link_url;
			if(!empty($data["link_target"])){
				$template_start	.= '<a href="'.$link_url.'"'.$link_attr.' target="'.$data["link_target"].'" title="'.esc_attr(strip_tags(mbw_get_board_item('fn_title',false))).'">';
			}else{
				$template_start	.= '<a href="'.$link_url.'"'.$link_attr.' title="'.esc_attr(strip_tags(mbw_get_board_item('fn_title',false))).'">';
			}
			
			$template_end	= '</a>'.$template_end;
		}

		if(isset($data["display_check"])) $data	= mbw_is_display_item($data);
			
		if($data["display"]=="hide" || $data["type"]=="hide"){
			$data["td_style"]			= $data["td_style"].";display:none;";
			$data["display"]		= "hide";
		}else if($data["display"]=="none" || $data["type"]=="none" || $data["type"]=="search"){
			return;
		}
		if(!empty($data['add_start_html'])) $template_start	.= $data['add_start_html'];		
		if(!empty($data["type"])){
			if($data["type"]=='date'){
				//오늘 작성한 글일 경우 시간으로 출력
				if( strpos($data["value"], date("Y-m-d", mbw_get_timestamp())) !== false){
					$template_start	.= '<span>'.substr($data["value"],11,5).'</span>';
				}else{
					$template_start	.= '<span>'.substr($data["value"],0,10).'</span>';
				}
			}else if($data["type"]=='date2'){
				$template_start	.= '<span>'.substr($data["value"],0,10).'</span>';
			}else if($data["type"]=='gallery_date'){
				//오늘 작성한 글일 경우 시간으로 출력
				if( strpos($data["value"], date("Y-m-d", mbw_get_timestamp())) !== false){
					$template_start	.= '<span>'.substr($data["value"],11,5).'</span> | <span>'.mbw_get_board_item("fn_hit").'</span>';
				}else{
					$template_start	.= '<span>'.substr($data["value"],0,10).'</span> | <span>'.mbw_get_board_item("fn_hit").'</span>';
				}
			}else if($data["type"]=='pid'){
				if(intval(mbw_get_board_item("fn_is_notice"))==1) $data["value"]		= "<span class='mb-notice mb-notice-pid'>".__MW("W_NOTICE")."</span>";
				$template_start	.= '<span>'.$data["value"].'</span>';
			}else if($data["type"]=='list_check'){
				$template_start	.= '<input'.$data["ext"].__STYLE($data["style"]).' type="checkbox" name="'.mbw_set_form_name("check_array[]").'" value="'.esc_attr(mbw_get_board_item('fn_pid')).'"/>';

			}else if($data["type"]=='hidden_pid'){
				$template_start	.= '<span>'.$data["value"].'</span><input type="hidden" name="'.mbw_set_form_name($data["item_name"]).'_array[]" value="'.$data["value"].'" />';				
			}else if($data["type"]=='title_q_icon'){
				$template_start	.= '<span class="faq-q">&nbsp;Q</span>';				
			}else if($data["type"]=='title_faq_icon'){
				if(!empty($data["onclick"])){
					$title_url		= ' href="javascript:;" onclick="'.$data["onclick"].';return false;" class="list_'.esc_attr(mbw_get_board_item('fn_pid')).'"';
				}else{
					$args	= mbw_get_vars("shortcode_args");
					if(!empty($args) && !empty($args["post_id"])){
						$title_url		= ' href="'.esc_url(mbw_get_url(array('vid'=>mbw_get_board_item('fn_pid')),get_permalink($args["post_id"]))).'"';
					}else if(!empty($data["link"]) && !empty($link_url)){
						$title_url		= ' href="'.esc_url($link_url).'"';
					}else{
						$title_url		= ' href="'.esc_url(mbw_get_url(array('vid'=>mbw_get_board_item('fn_pid')))).'"';
					}
				}
				$template_start	.= '<a'.$title_url.'><div class="mb-icon-box"></div></a>';
			}else if($data["type"]=='title_faq'){
				$add_comment		= "";
				$add_icon				= "";
				$add_start_icon		= "";
				$add_end_icon		= "";

				//카테고리 표시
				if(mbw_get_param("list_type")=="list"){
					$shortcode_args		= mbw_get_vars("shortcode_args");
					$use_list_category	= "true";
					if(!empty($shortcode_args["use_list_category"])) $use_list_category	= $shortcode_args["use_list_category"];
					if($use_list_category!="false" && mbw_get_board_item("fn_category1")!=""){
						$category_item_class		= mbw_get_category_item_class(mbw_get_board_item("fn_category1"));
						$data["value"]		= '<span class="category1-text '.esc_attr($category_item_class).'">['.mbw_get_board_item("fn_category1").']</span> '.$data["value"];
					}
				}
				//댓글 개수 표시하기
				if(mbw_get_option("use_view_comment") && mbw_get_board_option("fn_use_comment") == 1 && intval(mbw_get_board_item("fn_comment_count"))>0){
					$add_comment		= "<span class='cmt-count'> [<span class='cmt-count-num'>".mbw_get_board_item("fn_comment_count")."</span>]</span>";
				}
				if(!empty($data["onclick"])){
					$title_url		= ' href="javascript:;" onclick="'.$data["onclick"].';return false;" class="list_'.esc_attr(mbw_get_board_item('fn_pid')).'"';
				}else{
					$args	= mbw_get_vars("shortcode_args");
					if(!empty($args) && !empty($args["post_id"])){
						$title_url		= ' href="'.esc_url(mbw_get_url(array('vid'=>mbw_get_board_item('fn_pid')),get_permalink($args["post_id"]))).'"';
					}else if(!empty($data["link"]) && !empty($link_url)){
						$title_url		= ' href="'.esc_url($link_url).'"';
					}else{
						$title_url		= ' href="'.esc_url(mbw_get_url(array('vid'=>mbw_get_board_item('fn_pid')))).'"';
					}
				}

				$template_start	.= '<a'.$title_url.' title="'.strip_tags($data["value"]).'"><span>'.$add_start_icon.$data["value"].$add_comment.$add_end_icon.'</span></a>';

			}else if($data["type"]=='title_webzine'){
				$add_comment		= "";
				$add_icon				= "";
				$add_start_icon		= "";
				$add_end_icon		= "";

				//카테고리 표시
				if(mbw_get_param("list_type")=="list"){
					$shortcode_args		= mbw_get_vars("shortcode_args");
					$use_list_category	= "true";
					if(!empty($shortcode_args["use_list_category"])) $use_list_category	= $shortcode_args["use_list_category"];
					if($use_list_category!="false" && mbw_get_board_item("fn_category1")!=""){
						$category_item_class		= mbw_get_category_item_class(mbw_get_board_item("fn_category1"));
						$data["value"]		= '<span class="category1-text '.esc_attr($category_item_class).'">['.mbw_get_board_item("fn_category1").']</span> '.$data["value"];
					}
				}
				//댓글 개수 표시하기
				if(mbw_get_option("use_view_comment") && mbw_get_board_option("fn_use_comment") == 1 && intval(mbw_get_board_item("fn_comment_count"))>0){
					$add_comment		= "<span class='cmt-count'> [<span class='cmt-count-num'>".mbw_get_board_item("fn_comment_count")."</span>]</span>";
				}
				$write_date			= strtotime( mbw_get_board_item('fn_reg_date') );
				//최근글일 경우 아이콘 표시					
				if(mbw_get_timestamp()-(60*60*24)<$write_date)
					$add_end_icon		= $add_end_icon.' <img class="list-i-new" alt="new" style="vertical-align:middle;" src="'.MBW_SKIN_URL.'images/icon_new.gif" />';

				//비밀글일 경우 아이콘 표시
				if(intval(mbw_get_board_item("fn_is_secret"))==1) 
					$add_start_icon		= $add_start_icon.' <img class="list-i-secret" alt="secret" src="'.MBW_SKIN_URL.'images/icon_secret.gif" /> ';					
								
				if(!empty($data["onclick"])){
					$title_url		= ' href="javascript:;" onclick="'.$data["onclick"].';return false;" class="list_'.esc_attr(mbw_get_board_item('fn_pid')).'"';
				}else{
					$args	= mbw_get_vars("shortcode_args");
					if(!empty($args) && !empty($args["post_id"])){
						$title_url		= ' href="'.esc_url(mbw_get_url(array('vid'=>mbw_get_board_item('fn_pid')),get_permalink($args["post_id"]))).'"';
					}else if(!empty($data["link"]) && !empty($link_url)){
						$title_url		= ' href="'.esc_url($link_url).'"';
					}else{
						$title_url		= ' href="'.esc_url(mbw_get_url(array('vid'=>mbw_get_board_item('fn_pid')))).'"';
					}
				}

				$reg_date		= mbw_get_board_item("fn_reg_date");
					
				if( strpos($reg_date, date("Y-m-d", mbw_get_timestamp())) !== false){
					$reg_date	= substr($reg_date,11,5);
				}else{
					$reg_date	= substr($reg_date,0,10);
				}

				$template_start	.= '<a'.$title_url.' title="'.strip_tags($data["value"]).'"><div class="webzine-item-title"><span>'.$add_start_icon.$data["value"].$add_comment.$add_end_icon.'</span></div><div class="webzine-item-content">';

				$user_pid			= mbw_get_user('fn_pid');
				$user_level			= intval(mbw_get_user("fn_user_level"));
				if(intval(mbw_get_board_item("fn_is_secret"))==1){
					if((intval(mbw_get_board_option("fn_secret_level"))<=$user_level) || (mbw_is_login() && $user_pid==mbw_get_board_item("fn_user_pid",false))){
						$webzine_content			= mbw_get_board_item("fn_content",false);
					}else{
						$webzine_content			= __MM("MSG_SECRET");
					}			
				}else{
					$webzine_content			= mbw_get_board_item("fn_content",false);
				}

				if(mbw_get_board_item("fn_data_type")=="html") $webzine_content			= mbw_htmlspecialchars_decode($webzine_content);
				$webzine_content			= strip_tags(html_entity_decode($webzine_content, ENT_QUOTES));
				$webzine_content			= trim(str_replace(array("&nbsp;","　"," ","  "), " ", $webzine_content));

				if(empty($data["content_maxlength"])) $data["content_maxlength"]		= 200;
				if(isset($data["content_maxlength"])){					
					$maxlength				= intval($data["content_maxlength"]);
					if(function_exists('mb_strlen')) $content_length	= mb_strlen($webzine_content, mbw_get_option("encoding"));
					else $content_length	= strlen($webzine_content);

					if($maxlength<$content_length){
						if(!isset($data["maxtext"])){
							$data["maxtext"]		= "...";
						}
						if(function_exists('mb_substr')) $webzine_content		= mb_substr($webzine_content, 0, $maxlength, mbw_get_option("encoding")).$data["maxtext"];
						else $webzine_content		= substr($webzine_content, 0, $maxlength).$data["maxtext"];
					}
				}

				$template_start	.= mbw_htmlspecialchars($webzine_content);
				$template_start	.= '</div></a>';
				$template_start	.= '<div class="webzine-item-info"><span class="info-group">';
					$title_format	= 'name_date_hit';
					if(!empty($data["title_format"])){
						$title_format	= $data["title_format"];
					}else{
						$shortcode_args		= mbw_get_vars("shortcode_args");
						if(!empty($shortcode_args["title_format"])) $title_format	= $shortcode_args["title_format"];
					}
					$format_array				= explode('_',$title_format);
					if(!empty($format_array)){
						$mobile_template				= array();
						$mobile_template['name']		= '<span class="info-name">'.mbw_get_board_item("fn_user_name",false).'</span>';
						$mobile_template['date']		= '<span class="info-date">'.$reg_date.'</span>';
						$mobile_template['hit']			= '<span class="info_hit">'.mbw_get_board_item("fn_hit").'</span>';
						foreach($format_array as $name){
							if(!empty($name) && isset($mobile_template[$name])) $template_start	.= $mobile_template[$name];
						}
					}
				$template_start	.= '</span></div>';

			}else if(strpos($data["type"],'title')===0){
				$add_comment		= "";
				$add_icon				= "";
				$add_start_icon		= "";
				$add_end_icon		= "";
				
				//카테고리 표시
				if(mbw_get_param("list_type")=="list"){
					$shortcode_args		= mbw_get_vars("shortcode_args");
					$use_list_category	= "true";
					if(!empty($shortcode_args["use_list_category"])) $use_list_category	= $shortcode_args["use_list_category"];
					if($use_list_category!="false" && mbw_get_board_item("fn_category1")!=""){
						$category_item_class		= mbw_get_category_item_class(mbw_get_board_item("fn_category1"));
						$data["value"]		= '<span class="category1-text '.esc_attr($category_item_class).'">['.mbw_get_board_item("fn_category1").']</span> '.$data["value"];
					}
				}

				if($data["type"]=="title_checkbox" && intval(mbw_get_board_option("fn_delete_level")) <= mbw_get_user("fn_user_level"))
					$template_start	.= '<input'.$data["ext"].__STYLE($data["style"]).' type="checkbox" name="'.mbw_set_form_name("check_array[]").'" value="'.esc_attr(mbw_get_board_item('fn_pid')).'"/>';

				$reply_space		= mbw_get_icon("reply",intval(mbw_get_board_item("fn_reply_depth")));
				
				if(intval(mbw_get_board_item("fn_is_notice"))==1){

					if(mbw_get_vars("device_type")=="mobile" && mbw_get_param("list_type")=="list")
						$data["value"]		= "<span class='icon_notice'>[".__MW("W_NOTICE")."] </span>".$data["value"];

					$data["value"]		= "<span class='mb-notice mb-notice-title'>".$data["value"]."</span>";
				}

				//댓글 개수 표시하기
				if(mbw_get_option("use_view_comment") && mbw_get_board_option("fn_use_comment") == 1 && intval(mbw_get_board_item("fn_comment_count"))>0){
					$add_comment		= "<span class='cmt-count'> [<span class='cmt-count-num'>".mbw_get_board_item("fn_comment_count")."</span>]</span>";
				}

				
				$write_date			= strtotime( mbw_get_board_item('fn_reg_date') );

				if(mbw_get_param("list_type")=="list"){					
					
					//내용에 첨부파일이 있을 경우 아이콘 표시
					if(intval(mbw_get_board_item("fn_file_count"))>0){
						$add_end_icon		= $add_end_icon.' <img class="list-i-file" alt="file" src="'.MBW_SKIN_URL.'images/icon_file.gif" />';
					//내용에 이미지가 있을 경우 아이콘 표시
					}else if((mbw_get_board_item("fn_image_path"))!=""){
						$add_end_icon		= $add_end_icon.' <img class="list-i-img" alt="img" src="'.MBW_SKIN_URL.'images/icon_image.gif" />';
					}
					
					//최근글일 경우 아이콘 표시					
					if(mbw_get_timestamp()-(60*60*24)<$write_date)
						$add_end_icon		= $add_end_icon.' <img class="list-i-new" alt="new" style="vertical-align:middle;" src="'.MBW_SKIN_URL.'images/icon_new.gif" />';
									
					//비밀글일 경우 아이콘 표시
					if(intval(mbw_get_board_item("fn_is_secret"))==1) 
						$add_start_icon		= $add_start_icon.' <img class="list-i-secret" alt="secret" src="'.MBW_SKIN_URL.'images/icon_secret.gif" /> ';					

					if(strpos(mbw_get_board_item("fn_agent"),"m_")===0){		//Mobile 접속글				
						//$add_end_icon		= $add_end_icon.' <img class="list-i-img" alt="img" src="'.MBW_SKIN_URL.'images/icon_image.gif" />';
					}else if(strpos(mbw_get_board_item("fn_agent"),"t_")===0){		//Tablet 접속글				
						//$add_end_icon		= $add_end_icon.' <img class="list-i-img" alt="img" src="'.MBW_SKIN_URL.'images/icon_image.gif" />';
					}else if(strpos(mbw_get_board_item("fn_agent"),"d_")===0){		//Web 접속글				
						//$add_end_icon		= $add_end_icon.' <img class="list-i-img" alt="img" src="'.MBW_SKIN_URL.'images/icon_image.gif" />';
					}
				}else if(mbw_get_param("list_type")=="calendar"){
					//최근글일 경우 아이콘 표시					
					if(mbw_get_timestamp()-(60*60*24)<$write_date){
						$add_end_icon		= $add_end_icon.' <img class="list-i-new" alt="new" style="vertical-align:middle;" src="'.MBW_SKIN_URL.'images/icon_new.gif" />';
					}
					//비밀글일 경우 아이콘 표시
					if(intval(mbw_get_board_item("fn_is_secret"))==1) 
						$add_start_icon		= $add_start_icon.' <img class="list-i-secret" alt="secret" src="'.MBW_SKIN_URL.'images/icon_secret.gif" /> ';
				}

				if(!empty($data["onclick"])){
					$title_url		= ' href="javascript:;" onclick="'.$data["onclick"].';return false;" class="list_'.esc_attr(mbw_get_board_item('fn_pid')).'"';
				}else{
					$add_url		= "";
					if(mbw_get_param("list_type")=="calendar"){
						$calendar_date_ymd		= mbw_get_vars("calendar_date_ymd");
						if(!empty($calendar_date_ymd)){
							$add_url		= '&calendar_date='.$calendar_date_ymd;
						}						
						$title_url		= ' href="'.mbw_get_url(array('vid'=>mbw_get_board_item('fn_pid'),"calendar_date"=>"")).$add_url.'"';
					}else{
						$title_url		= ' href="'.mbw_get_url(array('vid'=>mbw_get_board_item('fn_pid'))).$add_url.'"';
					}
				}

				$data["size"]		= "&size=middle";
				$file_attr				= "";
				$file_name			= mbw_get_board_item('fn_image_path');

				if(empty($data["img_width"])) $data["img_width"]			= "50px";
				if(empty($data["img_height"])) $data["img_height"]		= $data["img_width"];

				if($data["type"]=="title_img" && $file_name!="" && intval(mbw_get_board_item("fn_is_secret"))!=1){
					$index1			= strpos($file_name,"_")+1;
					$file_name		= substr($file_name,$index1,strlen($file_name)-$index1);
					if(empty($data["alt"])) $file_attr			= $file_attr.' alt="'.$file_name.'"';
					if(empty($data["title"])) $file_attr			= $file_attr.' title="'.$file_name.'"';
					if(empty($data["background-size"])) $data["background-size"] = "cover";
					if(empty($data["background-position"])) $data["background-position"] = "center center";

					$template_start	.= '<a'.$title_url.' title="'.strip_tags($data["value"]).'">';
					$template_start	.= '<div class="pull-left" style="margin-right:7px !important;"><div class="border-eee-1"><div'.$data["ext"].__STYLE("width:".$data["img_width"].";height:".$data["img_height"].";background-image:url(".mbw_get_image_url("url_small",mbw_get_board_item('fn_image_path')).");background-position:".$data["background-position"].";background-size:".$data["background-size"].";".$data["style"]).' ></div></div></div>';
					$template_start	.= '</a>';
				}

				if(mbw_get_vars("device_type")=="mobile" && $data["type"]!='title2'){

					if(mbw_get_param("list_type")=="list"){
						$reg_date		= mbw_get_board_item("fn_reg_date");
					
						if( strpos($reg_date, date("Y-m-d", mbw_get_timestamp())) !== false){
							$reg_date	= substr($reg_date,11,5);
						}else{
							$reg_date	= substr($reg_date,0,10);
						}						
			
						$template_start	.= '<a'.$title_url.' title="'.esc_attr(strip_tags($data["value"])).'"><div><span>'.$reply_space.$add_start_icon.$data["value"].$add_comment.$add_end_icon.'</span><br>';
						$template_start	.= '<span class="info-group">';
							$title_format	= 'name_date_hit';
							if(!empty($data["title_format"])){
								$title_format	= $data["title_format"];
							}else{
								$shortcode_args		= mbw_get_vars("shortcode_args");
								if(!empty($shortcode_args["title_format"])) $title_format	= $shortcode_args["title_format"];
							}
							$format_array				= explode('_',$title_format);
							if(!empty($format_array)){
								$mobile_template				= array();
								//$mobile_template['name']		= '<span class="info-name">'.mbw_get_board_item("fn_user_name",false).'</span>';
								$mobile_template['date']		= '<span class="info-date">'.$reg_date.'</span>';
								//$mobile_template['hit']			= '<span class="info_hit">'.mbw_get_board_item("fn_hit").'</span>';
								foreach($format_array as $name){
									if(!empty($name) && isset($mobile_template[$name])) $template_start	.= $mobile_template[$name];
								}
							}
						$template_start	.= '</span>';
						$template_start	.= '</div></a>';

						//$template_start	.= '<div class="cmt-box"><a href="'.mbw_get_url(array('vid'=>mbw_get_board_item('fn_pid'))).'" title="comment">'.$add_comment.'</a></div>';
					}else 
						$template_start	.= '<a'.$title_url.' title="'.strip_tags($data["value"]).'"><span>'.$reply_space.$add_start_icon.$data["value"].$add_comment.$add_end_icon.'</span></a>';
						
				}else if(mbw_get_vars("device_type")=="tablet"){
					$template_start	.= '<a'.$title_url.' title="'.strip_tags($data["value"]).'"><span>'.$reply_space.$add_start_icon.$data["value"].$add_comment.$add_end_icon.'</span></a>';
				}else{
					$template_start	.= '<a'.$title_url.' title="'.strip_tags($data["value"]).'"><span>'.$reply_space.$add_start_icon.$data["value"].$add_comment.$add_end_icon.'</span></a>';				
				}
			}else{
				$template_start	.= mbw_get_item_template("list",$data);
			}
		}else{
			$template_start	.= '<span>'.$data["value"].'</span>';
		}

		if(!empty($data["tooltip"])) $template_start	.= mbw_get_tooltip_template($data["tooltip"]);
		if(!empty($data['add_middle_html'])) $template_start	.= $data['add_middle_html'];		
		if(!empty($data["description"])) $template_start	.= '<span class="mb-description">'.$data["description"].'</span>';
		if(!empty($data['add_end_html'])) $template_end		= $data['add_end_html'].$template_end;
		

		$template_start	.= $template_end;
		if($echo) echo $template_start;
		else return $template_start;
	}
}
?>