<?php
class MangBoard
{
	public function __construct($db=NULL,$mstore=NULL){
	}

	public function get_board_panel($args=NULL){
		mbw_add_trace("board->get_board_panel");
		global $mdb,$mstore;
		global $mb_admin_tables,$mb_board_table_name,$mb_comment_table_name;
		global $mb_fields,$mb_api_urls;
		global $mb_vars,$mb_board_name,$mb_words,$mb_languages;
		global $list_model,$view_model,$write_model;
		
		$mb_board_name	= mbw_get_board_name();
		if(empty($mb_board_name) && !empty($args['name'])) $mb_board_name		= mbw_value_filter($args['name']);
		if(empty($mb_board_table_name) && !empty($mb_board_name)) $mb_board_table_name		= mbw_get_board_table_name($mb_board_name);

		$mb_user_level		= mbw_get_user("fn_user_level");

		if(mbw_get_param("mode")==""){
			if(!empty($args['mode'])){
				mbw_set_param("mode", mbw_value_filter($args['mode']));
				if($args['mode']=="write") mbw_load_editor_plugin();
			}else mbw_set_param("mode", "list");			
		}
		if(mbw_get_param("board_action")==""){
			if(!empty($args['board_action'])) mbw_set_param("board_action", mbw_value_filter($args['board_action']));
			else if(!empty($args['action'])) mbw_set_param("board_action", mbw_value_filter($args['action']));
		}
		if(mbw_get_param("board_pid")==""){			
			if(!empty($args['board_pid'])){
				mbw_set_param("board_pid", mbw_value_filter($args['board_pid']));
			}
		}
		if(!isset($_GET["category1"]) && mbw_get_param("category1")==""){
			if(!empty($args['category1'])) mbw_set_param("category1", $args['category1']);
			if(!empty($args['category2'])) mbw_set_param("category2", $args['category2']);
			if(!empty($args['category3'])) mbw_set_param("category3", $args['category3']);
			mbw_set_category_params();
		}
		if(!empty($args['search_field'])){
			if(function_exists('mbw_set_search_field')) mbw_set_search_field($args);
		}
		if(!empty($args['write_next_url'])){
			mbw_set_option("write_next_page","url");
			mbw_set_option("write_next_url",mbw_validate_redirect(trim($args['write_next_url'])));
		}
		if(mbw_get_param("mode")=="view" && mbw_get_param("board_pid")==""){
			$where_query		= "";
			$where_data			= array();
			$board_field			= $mb_fields["select_board"];
			$category1			= mbw_get_param("category1");
			$category2			= mbw_get_param("category2");
			$category3			= mbw_get_param("category3");
			if(!empty($category1)){
				if(strpos($category1, ',') !== false){
					$category1_array		= explode(',',$category1);
					$filter_array1			= array();
					foreach($category1_array as $item){
						$filter_array1[]		= $mdb->prepare($board_field["fn_category1"]."=%s", $item );
					}
					$where_data[]		= " (".implode( ' OR ', $filter_array1).")";
				}else{
					$where_data[]		= $mdb->prepare($board_field["fn_category1"]."=%s",$category1);
				}
			}
			if(!empty($category2)){
				if(strpos($category2, ',') !== false){
					$category2_array		= explode(',',$category2);
					$filter_array2			= array();
					foreach($category2_array as $item){
						$filter_array2[]		= $mdb->prepare($board_field["fn_category2"]."=%s", $item);
					}
					$where_data[]		= " (".implode( ' OR ', $filter_array2).")";
				}else{
					$where_data[]		= $mdb->prepare($board_field["fn_category2"]."=%s",$category2);
				}
			}
			if(!empty($category3)){
				if(strpos($category3, ',') !== false){
					$category3_array		= explode(',',$category3);
					$filter_array3			= array();
					foreach($category3_array as $item){
						$filter_array3[]		= $mdb->prepare($board_field["fn_category3"]."=%s", $item);
					}
					$where_data[]		= " (".implode( ' OR ', $filter_array3).")";
				}else{
					$where_data[]		= $mdb->prepare($board_field["fn_category3"]."=%s",$category3);
				}
			}

			if(mbw_get_param("search_text")!="" && mbw_get_param("search_field")!="" && !empty($board_field[mbw_get_param("search_field")])){
				$search_field			= mbw_value_filter($board_field[mbw_get_param("search_field")]);
				$where_data[]		= $mdb->prepare($search_field." like %s",'%'.mbw_get_param("search_text").'%');
			}
			$where_data[]		= 'is_secret=0';

			if(!empty($where_data)) $where_query				= " WHERE ".implode(" and ",$where_data);

			$board_pid			= intval($mdb->get_var("select pid from ".$mb_board_table_name.$where_query." ORDER BY pid DESC limit 1"));
			if(!empty($board_pid)) mbw_set_param("board_pid",$board_pid);
		}

		$board_mode			= mbw_value_filter(mbw_get_param("mode"));
		if(mbw_get_param($board_mode."_type")=="" && !empty($args[$board_mode.'_type'])) mbw_set_param($board_mode."_type", mbw_value_filter($args[$board_mode.'_type']));

		if($board_mode=="logout"){
			echo mbw_get_move_script("logout");
		}else{
			mbw_init_board_panel();
			if(empty($args['style'])) $board_style			= "";
			else $board_style			= ' style="'.str_replace('"',"'",esc_attr($args['style'])).'"';

			$device_type			= mbw_get_vars("device_type");
			$class_array			= array();
			if(!empty($args[$device_type.'_'.$board_mode.'_class'])){
				$class_array[]		= mbw_value_filter(trim($args[$device_type.'_'.$board_mode.'_class']),"class");
			}
			if(!empty($args[$device_type.'_class'])){
				$class_array[]		= mbw_value_filter(trim($args[$device_type.'_class']),"class");
			}
			if(!empty($args[$board_mode.'_class'])){
				$class_array[]		= mbw_value_filter(trim($args[$board_mode.'_class']),"class");
			}
			if(!empty($args['class'])){
				$class_array[]		= mbw_value_filter(trim($args['class']),"class");
			}
			if(!empty($_REQUEST["template_class"])){
				$t_class			= str_replace(","," ",$_REQUEST["template_class"]);
				$class_array[]		= mbw_value_filter(trim($t_class),"class");
			}
			if(!empty($class_array)){
				$template_class		= implode(" ", $class_array);
			}else{
				$template_class		= "";
			}
			mbw_set_vars("template_class",$template_class);

			$file_path					= array();
			$file_path["base"]		= MBW_SKIN_PATH;
			$file_path["prefix"]		= "";
			$file_path["mode"]		= $board_mode;

			if(has_filter("mf_board_skin_path")){
				$file_path		= apply_filters("mf_board_skin_path",$file_path);
			}

			$board_class	= "";
			if(function_exists('mbw_get_board_class')) $board_class	= mbw_get_board_class();

			echo '<div id="mb_top" class="mb-'.esc_attr($device_type).'">';
			echo '<div id="'.esc_attr($mb_board_name).'_board" class="mb-board"'.$board_style.'>';
				echo '<div class="'.esc_attr($board_class).'">';
					if(!empty($template_class)){
						echo '<div class="'.esc_attr($template_class).'">';
					}
					if(mbw_get_param("list_type")=="") mbw_set_param("list_type", $mstore->get_list_type());
					require($file_path["base"]."_header.php");
					do_action('mbw_board_header');
					$board_header		= mbw_get_board_option("fn_board_header");
					if(!empty($board_header)) echo do_shortcode($board_header);
					if(mbw_get_param($board_mode."_type")!="" && is_file($file_path["base"].$file_path["prefix"].mbw_get_param($board_mode."_type").".php")){
						require($file_path["base"].$file_path["prefix"].mbw_get_param($board_mode."_type").".php");
					}else if(is_file($file_path["base"].$file_path["prefix"].$file_path["mode"].".php")){
						require($file_path["base"].$file_path["prefix"].$file_path["mode"].".php");
					}
					require($file_path["base"]."_footer.php");
					do_action('mbw_board_footer');
					$board_footer		= mbw_get_board_option("fn_board_footer");
					if(!empty($board_footer)) echo do_shortcode($board_footer);
					if(!empty($template_class)){
						echo '</div>';
					}
				echo '</div>';
			echo '</div>';
			echo '</div>';
			echo '<div style="display:none !important;" class="mb-poweredby"><!-- 웹사이트 제작 플랫폼 - 망보드 --><a href="https://www.mangboard.com" target="_blank" style="font-size:13px;" title="Powered by MangBoard">Powered by MangBoard</a> | <a href="https://www.mangboard.com/store/" target="_blank" style="font-size:13px;" title="워드프레스 쇼핑몰 망보드">워드프레스 쇼핑몰 망보드</a></div>';
		}
	}
}
?>