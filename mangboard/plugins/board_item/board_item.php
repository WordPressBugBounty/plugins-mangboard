<?php
//[mb_board_item name="board1" pid="1" style=""]
add_shortcode('mb_board_item', 'mbw_create_board_item_panel');
if(!function_exists('mbw_create_board_item_panel')){
	function mbw_create_board_item_panel($args){
		global $mdb,$mb_fields,$mb_admin_tables;
		if(empty($args['name'])) return;
		$name			=  mbw_value_filter(trim($args['name']),"name");
		if(!empty($args['pid'])) $pid				= mbw_value_filter($args['pid'],"int");
		else $pid			= "";
		//모바일 pid 설정이 존재할 경우 모바일 pid로 변경
		if(mbw_get_vars("device_type")=="mobile" && !empty($args['mobile_pid'])) $pid		= mbw_value_filter($args['mobile_pid'],"name");

		if(!empty($args['style'])) $style		= str_replace('"',"'",esc_attr($args['style']));
		else $style				= "";
		if(!empty($args['class'])) $class		= " ".esc_attr($args['class']);
		else $class				= "";
		if(!empty($args['field'])) $field		= mbw_value_filter(trim($args['field']),"name");
		else $field				= 'content';

		$field					= str_replace('fn_','',$field);
		$pid					= intval($pid);
		$fields					= $mb_fields["board"];
		$board_options		= $mdb->get_row($mdb->prepare("select * from ".$mb_admin_tables["board_options"]." where ".$mb_fields["board_options"]["fn_board_name2"]."=%s",$name), ARRAY_A);

		if(empty($board_options)){
			echo __MM("MSG_EXIST_ERROR2", array(esc_html($name),__MW("W_BOARD")));
		}else if($board_options[$mb_fields["board_options"]["fn_board_type"]]=="board"){
			$view_level			= intval($board_options[$mb_fields["board_options"]["fn_view_level"]]);
			$list_level			= intval($board_options[$mb_fields["board_options"]["fn_list_level"]]);
			if($list_level==0 && $view_level==0){				
				if(!empty($pid)){
					$item			= $mdb->get_row($mdb->prepare("select * from %1s where %1s=%d limit 1;",mbw_get_board_table_name($name),$fields["fn_pid"],$pid), ARRAY_A);
				}else{
					$item			= $mdb->get_row($mdb->prepare("select * from %1s order by pid desc limit 1;",mbw_get_board_table_name($name)), ARRAY_A);
				}
				if(empty($item[$fields["fn_is_secret"]]) && ($item[$fields["fn_is_show"]]=="1") ){
					if(isset($item[$field])){
						$content		= $item[$field];
						if($field=='content'){
							if($item[$fields["fn_data_type"]]=="html"){
								$content			= mbw_htmlspecialchars_decode($content);
								if(function_exists('mbw_replace_image_url')) $content			= mbw_replace_image_url($content);
							}
							$content		= make_clickable($content);
							if(!empty($item[$fields["fn_level"]]) && (intval($item[$fields["fn_level"]])>7) ) $content		= do_shortcode($content);
						}else if($field=='image_path'){
							$content	= mbw_get_image_url("url",$content);
						}
						echo '<div class="mb-'.esc_attr(mbw_get_vars("device_type")).'"><div class="mb-board"><div class="mb-content-item'.esc_attr($class).'" style="'.esc_attr($style).'">'.$content.'</div></div></div>';
					}else{
						echo "<div>".__MM("MSG_ITEM_NOT_EXIST")."</div>";
					}
				}else{
					if(!empty($item[$fields["fn_is_secret"]])){
						echo "<div>".__MM("MSG_SECRET_CONTENT_DISPLAY_ERROR")."</div>";
					}else{
						echo "<div>".__MM("MSG_PRIVATE_CONTENT_DISPLAY_ERROR")."</div>";
					}
				}
			}else{
				echo "<div>".__MM("MSG_BOARD_CONTENT_LEVEL_ERROR")."</div>";
			}
		}else{
			echo "<div>".__MM("MSG_BOARD_CONTENT_DISPLAY_ERROR")."</div>";
		}
	}
}
?>