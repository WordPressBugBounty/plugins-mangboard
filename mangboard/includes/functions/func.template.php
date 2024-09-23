<?php
if(!function_exists('mbw_init_item_data')){
	function mbw_init_item_data($mode,$data,$tag=null){
		global $mstore;

		if(!empty($data["mb_data"])) return $data;

		$key_data		= array("","tr_","th_","td_");
		foreach($key_data as $key){
			if(!empty($tag[$key."style"])){
				if(!empty($data[$key."style"])) $data[$key."style"]		= $data[$key."style"].";".$tag[$key."style"];
				else $data[$key."style"]		= $tag[$key."style"];
			}else if(empty($data[$key."style"])) $data[$key."style"]		= "";

			if(!empty($tag[$key."class"])){
				if(!empty($data[$key."class"])) $data[$key."class"]		= $data[$key."class"]." ".$tag[$key."class"];
				else $data[$key."class"]		= $tag[$key."class"];
			}else if(empty($data[$key."class"])) $data[$key."class"]		= "";
			
			if(!empty($data["responsive"])){
				$data["responsive"]		= " ".$data["responsive"];
				$data[$key."class"]	= $data[$key."class"].$data["responsive"];
			}
			if(!empty($data[$key."class"])){
				$data[$key."o_class"]	= trim($data[$key."class"]);
				$data[$key."class"]		= ' class="'.esc_attr(trim($data[$key."class"])).'"';
			}
		}		
		
		if(empty($data["display"])) $data["display"]	= "show";
		if(!empty($data["type"]) && $data["type"]=='db_type'){
			if(!empty($data["type_field"])) $data["type"]						= mbw_get_board_item($data["type_field"]);
			if(!empty($data["data_field"])) $data["data"]						= mbw_get_board_item($data["data_field"]);
			if(!empty($data["label_field"])) $data["label"]						= mbw_get_board_item($data["label_field"]);
			if(!empty($data["style_field"])) $data["style"]						= mbw_get_board_item($data["style_field"]);
			if(!empty($data["class_field"])) $data["class"]						= mbw_get_board_item($data["class_field"]);
			if(!empty($data["event_field"])) $data["event"]					= mbw_get_board_item($data["event_field"]);
			if(!empty($data["attribute_field"])) $data["attribute"]			= mbw_get_board_item($data["attribute_field"]);
			if(!empty($data["tooltip_field"])) $data["tooltip"]				= mbw_get_board_item($data["tooltip_field"]);
			if(!empty($data["description_field"])) $data["description"]	= mbw_get_board_item($data["description_field"]);
		}
		$data								= mbw_init_item_property($mode,$data);

		$data["mode"]					= mbw_get_param("mode");
		$data["board_action"]		= mbw_get_param("board_action");

		if($mode=="write" || $mode=="comment"){
			if(!empty($data[mbw_get_param("board_action")]))
				$data["type"]		= $data[mbw_get_param("board_action")];
		}
		if(mbw_is_login() && !empty($data["login"])) $data["type"]	= $data["login"];

		if(!empty($data["name"]))	$data["name"]		= __MW($data["name"]);
		if(!empty($data["add_name"])) $data["name"]		.= __MW($data["add_name"]);

		if(!empty($data["description_".mbw_get_param("board_action")])) $data["description"]		= __MM($data["description_".mbw_get_param("board_action")]);
		else if(!empty($data["description"]))	$data["description"]		= __MM($data["description"]);
		
		if(!empty($data["field"])){

			if(!empty($data["type"]) && ($data["type"]=='view')) $item_filter		= false;
			else if(!empty($data["filter"]) && ($data["filter"]=='false')) $item_filter		= false;
			else if(strpos($data["type"],'text')===0) $item_filter		= false;
			else if(mbw_get_param("board_action")=="modify" && (mbw_get_param("mode")=="write" || mbw_get_param("mode")=="comment"))  $item_filter		= false;
			else $item_filter		= true;

			if(!isset($data["value"])){
				if($mode=="comment"){
					$data["value"]			= mbw_get_comment_item($data["field"],$item_filter);
					$type						= "comment";
				}else{
					$data["value"]			= mbw_get_board_item($data["field"],$item_filter);
					$type						= "board";
				}
			}	
			if(!empty($data["translate"])){
				$data["value"]		= __MW($data["value"]);
			}
			if(!empty($data["label"])) $data["label"]		= __MW($data["label"]);
			if(!empty($data["data"])) $data["data"]			= __MW($data["data"]);
			if(!empty($data["format"])){
				$data["value"]			= mbw_set_format($data["value"],$data["format"]);
			}
			
			if($data["value"]!=="" && ($data["type"]=="" || $data["type"]=="static" || $data["type"]=="view") && (!empty($data["label"]) || !empty($data["data"]))){
				if(!isset($data["label"]) && isset($data["data"])) $data["label"]		= $data["data"];
				if(!isset($data["data"]) && isset($data["label"])) $data["data"]		= $data["label"];
				$delimiter			= ",";
				if(isset($data["delimiter"])) $delimiter		= $data["delimiter"];
				$t_data				= explode($delimiter,$data["data"]);
				$t_label				= explode($delimiter,$data["label"]);
				$count				= count($t_data);
				for($i=0;$i<$count;$i++){
					if($data["value"]==$t_data[$i]){
						if(isset($t_label[$i])) $data["value"]		= $t_label[$i];
					}
				}
			}
			if(isset($data["value"])){
				$data["o_value"]		= $data["value"];
			}
			//글자 길이 제한 설정
			if(isset($data["maxlength"])){				
				$value				= mbw_htmlspecialchars_decode($data["value"]);
				if(function_exists('mb_strlen')) $value_length		= mb_strlen($value,mbw_get_option("encoding"));
				else $value_length		= strlen($value);
				if($mode!="write" && intval($data["maxlength"])<$value_length){
					if(!isset($data["maxtext"])){
						$data["maxtext"]		= "...";
					}
					if(function_exists('mb_substr')) $data["value"]				= mbw_htmlspecialchars(mb_substr($value, 0, $data["maxlength"], mbw_get_option("encoding")).$data["maxtext"]);
					else $data["value"]				= mbw_htmlspecialchars(substr($value, 0, $data["maxlength"]).$data["maxtext"]);
				}else{
					$data["attribute"]			= $data["attribute"]." maxlength='".$data["maxlength"]."'";
				}
			}else if(isset($data["substr"]) && !empty($data["value"])){
				$value				= mbw_htmlspecialchars_decode($data["value"]);
				if(function_exists('mb_strlen')) $value_length		= mb_strlen($value,mbw_get_option("encoding"));
				else $value_length		= strlen($value);
				if(strpos($data["substr"],',')===false){
					$start			= 0;
					$length		= intval($data["substr"]);
				}else{
					$pos_array	= explode(",",$data["substr"]);
					$start			= intval($pos_array[0]);
					$length		= intval($pos_array[1]);
				}
				if($length<$value_length){
					if(function_exists('mb_substr')) $data["value"]				= mbw_htmlspecialchars(mb_substr($value, $start, $length, mbw_get_option("encoding")));
					else $data["value"]				= mbw_htmlspecialchars(substr($value, $start, $length));
				}
			}
			if(isset($data["prepend_text"]) && isset($data["value"])){
				$data["value"]			= $data["prepend_text"].$data["value"];
			}
			if(isset($data["append_text"]) && isset($data["value"])){
				$data["value"]			= $data["value"].$data["append_text"];
			}
		}else{
			if(!isset($data["value"])){
				$data["value"]			= "";
			}
		}
		if($mode=='view' || $mode=='write'){
			$board_mode	= mbw_get_param("mode");
			$layout_type		= mbw_get_vars($board_mode."_layout_type");
			if(!empty($layout_type) && (empty($data["tpl"]) || $data["tpl"]!='tag')){
				if(strpos($layout_type,'responsive-box')===0){
					$required_text		= "";
					if(isset($data["required"])) $required_text		= $data["required"];
					if(empty($data["add_label_html"])) $data["add_label_html"] = "";
					if(empty($data["add_start_html"])) $data["add_start_html"] = "";
					if(empty($data["add_end_html"])) $data["add_end_html"] = "";

					if(!empty($data["responsive_box_class"])) $data["responsive_box_class"] = " ".$data["responsive_box_class"];
					else if(!empty($data["tr_class"])) $data["responsive_box_class"] = " ".$data["tr_o_class"];
					else $data["responsive_box_class"] = "";
					if(!empty($data["responsive_label_class"])) $data["responsive_label_class"] = " ".$data["responsive_label_class"];
					else if(!empty($data["th_class"])) $data["responsive_label_class"] = " ".$data["th_o_class"];
					else $data["responsive_label_class"] = "";
					if(!empty($data["responsive_content_class"])) $data["responsive_content_class"] = " ".$data["responsive_content_class"];
					else if(!empty($data["td_class"])) $data["responsive_content_class"] = " ".$data["td_o_class"];
					else $data["responsive_content_class"] = "";

					$responsive_html	= '<div class="mb-responsive-box'.$data["responsive_box_class"].'"><div class="mb-responsive-box-item">';
					if(empty($data["colspan"])) $responsive_html	.= '<div class="mb-responsive-box-label'.$data["responsive_label_class"].'"><label for="'.$data["item_id"].'">'.$data["name"].$required_text.'</label></div>'.$data["add_label_html"];
					$responsive_html	.= '<div class="mb-responsive-box-content'.$data["responsive_content_class"].'">';
					$data["add_start_html"]		= $responsive_html.$data["add_start_html"];
					$data["add_end_html"]		= $data["add_end_html"].'</div></div></div>';
					$data["colspan"]				= '2';
				}
			}
		}
		$data					= apply_filters("mf_template_item",$data);
		
		if((mbw_get_param("board_action")!="modify" || mbw_get_param("mode")!="write") && $data["value"]==="" && isset($data["default"])) $data["value"]		=  $data["default"];

		$data["mb_data"]	= "init";
		return $data;
	}
}
if(!function_exists('mbw_init_subitem_data')){
	function mbw_init_subitem_data($mode,$data,$tag=null){
		global $mstore;

		if(!empty($data["mb_data"])) return $data;

		$key_data		= array("","tr_","th_","td_");
		foreach($key_data as $key){
			if(!empty($tag[$key."style"])){
				if(!empty($data[$key."style"])) $data[$key."style"]		= $data[$key."style"].";".$tag[$key."style"];
				else $data[$key."style"]		= $tag[$key."style"];
			}else if(empty($data[$key."style"])) $data[$key."style"]		= "";

			if(!empty($tag[$key."class"])){
				if(!empty($data[$key."class"])) $data[$key."class"]		= $data[$key."class"]." ".$tag[$key."class"];
				else $data[$key."class"]		= $tag[$key."class"];
			}else if(empty($data[$key."class"])) $data[$key."class"]		= "";
			
			if(!empty($data["responsive"])){
				$data["responsive"]		= " ".$data["responsive"];
				$data[$key."class"]	= $data[$key."class"].$data["responsive"];
			}
			if(!empty($data[$key."class"])){
				$data[$key."o_class"]	= trim($data[$key."class"]);
				$data[$key."class"]		= ' class="'.esc_attr(trim($data[$key."class"])).'"';				
			}
		}
		$data["mb_data"]	= "init";
		return $data;
	}
}
if(!function_exists('mbw_init_item_property')){
	function mbw_init_item_property($mode,$data){		
		
		global $mb_vars;

		$key_data		= array("field","type","name","width","height","data","style","parent");
		foreach($key_data as $key){
			if(empty($data[$key])) $data[$key]		= "";
		}
		$key_data		= array("attribute","event");
		foreach($key_data as $key){
			if(!empty($data[$key])) $data[$key]		= " ".$data[$key];
			else $data[$key]		= "";
		}
		$key_data		= array("placeholder","title","id");
		foreach($key_data as $key){
			if(!empty($data[$key])) $data[$key]		= " ".$key.'="'.$data[$key].'"';
			else $data[$key]		= "";
		}
		
		//"device_속성"이 지정되어 있을 경우 해당 device에서 실행시 해당하는 속성으로 설정
		if(mbw_get_vars("device_type")!="desktop"){			
			$device_data		= array("field","type","width","height","img_width","img_height","maxlength","content_maxlength","name","label","data","class","colspan","th_class","td_class","style","th_style","td_style","parent","display","event","title","description","add_start_html","add_end_html","add_middle_html","prepend_text","append_text");
			$device_type		= mbw_get_vars("device_type");
			foreach($device_data as $key){
				if(!empty($data[$device_type."_".$key])){
					$data[$key]			= $data[$device_type."_".$key];
				}
			}
		}
		if(empty($data["item_name"])){
			if(!empty($data["field"])) $data["item_name"]	= str_replace('fn_',"",$data["field"]);
			else if(!empty($data["type"])) $data["item_name"]	= $data["type"];
			else $data["item_name"]	= "";
		}
		if(empty($data["item_id"])){
			if(!empty($data["item_name"])){
				$id_suffix				= "";
				if($mode!="list"){
					$item_index		= mbw_get_template_index()+1;
					if($mode!=""){
						$id_suffix			= "_".substr($mode,0,1).$item_index;
					}else{
						$id_suffix			= "_i".$item_index;
					}
					mbw_set_template_index($item_index);
				}
				$data["item_id"]	= mbw_get_id_prefix().$data["item_name"].$id_suffix;
			}else{
				$data["item_id"]	= "";
			}
		}
		if(empty($data["type"])){
			if($mode=="write"){
				$data["type"]				= "text";
				$data["mode_type"]		= "write_text";
			}
		}
		if(empty($data["class"])) $data["class"]		= "";
		$data["ext"]			= $data["class"].$data["attribute"].$data["title"].$data["placeholder"].$data["id"].$data["event"];
		return $data;
	}
}
if(!function_exists('mbw_get_item_template')){
	function mbw_get_item_template($mode, $data){
		$template_start		= "";
		$template_end			= "";

		if(empty($data["mb_data"]))
			$data		= mbw_init_item_data($mode,$data);

		if(!empty($data["type"])){
			if(!empty($data["combo"])){
				if(isset($data["combo"][0])){
					$event_type		= "onclick";
					$item_name		= $data["item_name"];
					$item_index		= "";
					if(!empty($data["item_index"])) $item_index		= $data["item_index"];
					if($data["type"]=="select") $event_type		= "onchange";
					$data["ext"]		= $data["ext"]." ".$event_type."=\"template_combo_handler('".esc_js($data["type"])."',this,'".esc_js($item_name.$item_index)."')\"";
					$value			= "";
					if(isset($data["value"])) $value		= $data["value"];
					
					$template_start			.= '<div class="mb-combo-wrapper">';
					$template_start			.= mbw_create_template($mode, $data);
					if(!empty($data['add_combo_html'])) $template_start	.= $data['add_combo_html'];	
					$template_combo		= "";

					foreach($data["combo"] as $item){					

						if(!empty($item["class"])){
							$item["o_class"]	= trim($item["class"]);
							$item["class"]		= ' class="'.$item["o_class"].'"';							
						}else{
							$item["o_class"]	= "";
							$item["class"]		= "";							
						}
						$combo_data				= mbw_init_item_property($mode,$item);						
						if(!isset($combo_data["match_value"])) $combo_data["match_value"]		= "";						
						
						$t_data		= $combo_data;
						if(!isset($t_data["value"])){
							if(!empty($t_data["field"])){
								if($mode=="comment"){
									$t_data["value"]			= mbw_get_comment_item($t_data["field"],false);
								}else{
									$t_data["value"]			= mbw_get_board_item($t_data["field"],false);
								}
							}else{
								$t_data["value"]			= "";								
							}
						}

						if($t_data["value"]!=="" && ($t_data["type"]=="" || $t_data["type"]=="static" || $t_data["type"]=="view") && (!empty($t_data["label"]) || !empty($t_data["data"]))){
							if(!isset($t_data["label"]) && isset($t_data["data"])) $t_data["label"]		= $t_data["data"];
							if(!isset($t_data["data"]) && isset($t_data["label"])) $t_data["data"]		= $t_data["label"];
							$delimiter			= ",";
							if(isset($t_data["delimiter"])) $delimiter		= $t_data["delimiter"];
							$t_data2				= explode($delimiter,$t_data["data"]);
							$t_label2				= explode($delimiter,$t_data["label"]);
							$count				= count($t_data2);
							for($i=0;$i<$count;$i++){
								if($t_data["value"]==$t_data2[$i]){
									if(isset($t_label2[$i])) $t_data["value"]		= $t_label2[$i];
								}
							}
						}
						if(isset($t_data["value"])){
							$t_data["o_value"]		= $t_data["value"];
						}

						if((mbw_get_param("board_action")!="modify" || mbw_get_param("mode")!="write") && $t_data["value"]==="" && isset($combo_data["default"])) $t_data["value"]		=  $combo_data["default"];

						if(isset($t_data["prepend_text"]) && isset($t_data["value"])){
							$t_data["value"]			= $t_data["prepend_text"].$t_data["value"];
						}
						if(isset($t_data["append_text"]) && isset($t_data["value"])){
							$t_data["value"]			= $t_data["value"].$t_data["append_text"];
						}

						if(strpos(",".$combo_data["match_value"].",", ",".$value.",")===false){					
							$style		= "display:none;";
						}else{
							$style		= "";
						}						
						$template_combo	.= '<div class="mb-combo-box mb-combo-'.esc_attr($item_name.$item_index).'-'.esc_attr(str_replace(",","",$combo_data["match_value"])).'" style="'.esc_attr($style).'">';
						if(!empty($combo_data['add_start_html'])) $template_combo	.= $combo_data['add_start_html'];
						if(!empty($t_data["tpl"]) && $t_data["tpl"]!="item"){
							$t_data				= mbw_init_subitem_data($mode,$t_data);
							$template_combo	.= mbw_get_extension_template($t_data);
						}else{
							$template_combo	.= mbw_create_template($mode, $t_data);
						}
						if(!empty($combo_data['add_end_html'])) $template_combo	.= $combo_data['add_end_html'];
						if(!empty($combo_data["description"])) $template_combo	.= '<span class="mb-description">'.$combo_data["description"].'</span>';						
						$template_combo	.= '</div>';
						
					}
					$style					= "";
					$parent_tag			= "div";
					$parent_class		= "mb-combo-items";
					if(!empty($data["parent_tag"])) $parent_tag		= $data["parent_tag"];
					if(!empty($data["parent_class"])) $parent_class	.= " ".$data["parent_class"];
					if(!empty($data["parent_style"])) $style				.= $data["parent_style"].";";

					$template_start	.= '<'.esc_attr($parent_tag).' class="'.esc_attr($parent_class).'" style="'.esc_attr($style).'">';
						$template_start	.= $template_combo;					
					$template_start	.= '</'.esc_attr($parent_tag).'>';
					$template_start	.= '</div>';

					unset($data["combo"]);
				}else{

					if(!empty($data["combo"]["class"])){
						$data["combo"]["o_class"]		= trim($data["combo"]["class"]);
						$data["combo"]["class"]		= ' class="'.esc_attr($data["combo"]["o_class"]).'"';
					}else{
						$data["combo"]["o_class"]		= "";
						$data["combo"]["class"]		= "";						
					}
					$item_index		= "";
					if(!empty($data["item_index"])) $item_index		= $data["item_index"];

					$combo_data				= mbw_init_item_property($mode,$data["combo"]);					
					
					if(empty($combo_data["match_type"])) $combo_data["match_type"]		= "show";
					if(!isset($combo_data["match_value"])) $combo_data["match_value"]		= "";					
					$event_type		= "onclick";
					if($data["type"]=="select") $event_type		= "onchange";

					$data["ext"]		= $data["ext"]." ".$event_type."=\"template_match_handler('".esc_js($data["type"])."',this,'".esc_js($combo_data["item_name"].$item_index)."','".esc_js($combo_data["match_type"])."','".esc_js($combo_data["match_value"])."')\"";
					$value			= "";
					if(isset($data["value"])) $value		= $data["value"];
					
					$template_start			.= mbw_create_template($mode, $data);
					if(!empty($data['add_combo_html'])) $template_start	.= $data['add_combo_html'];	

					$data							= $combo_data;
					if(!isset($data["value"])){
						if(!empty($data["field"])){
							if($mode=="comment"){
								$data["value"]			= mbw_get_comment_item($data["field"],false);
							}else{
								$data["value"]			= mbw_get_board_item($data["field"],false);
							}
						}else{
							$data["value"]			= "";							
						}
					}

					if($data["value"]!=="" && ($data["type"]=="" || $data["type"]=="static" || $data["type"]=="view") && (!empty($data["label"]) || !empty($data["data"]))){
						if(!isset($data["label"]) && isset($data["data"])) $data["label"]		= $data["data"];
						if(!isset($data["data"]) && isset($data["label"])) $data["data"]		= $data["label"];
						$delimiter			= ",";
						if(isset($data["delimiter"])) $delimiter		= $data["delimiter"];
						$t_data2				= explode($delimiter,$data["data"]);
						$t_label2				= explode($delimiter,$data["label"]);
						$count				= count($t_data2);
						for($i=0;$i<$count;$i++){
							if($data["value"]==$t_data2[$i]){
								if(isset($t_label2[$i])) $data["value"]		= $t_label2[$i];
							}
						}
					}
					if(isset($data["value"])){
						$data["o_value"]		= $data["value"];
					}
	
					if((mbw_get_param("board_action")!="modify" || mbw_get_param("mode")!="write") && $data["value"]==="" && isset($combo_data["default"])) $data["value"]		=  $combo_data["default"];
					unset($data["combo"]);

					if(isset($data["prepend_text"]) && isset($data["value"])){
						$data["value"]			= $data["prepend_text"].$data["value"];
					}
					if(isset($data["append_text"]) && isset($data["value"])){
						$data["value"]			= $data["value"].$data["append_text"];
					}

					$style					= "";
					$parent_tag			= "div";
					$parent_class		= "";
					if(!empty($data["parent_tag"])) $parent_tag		= $data["parent_tag"];
					if(!empty($data["parent_class"])) $parent_class	= $data["parent_class"];
					if(!empty($data["parent_style"])) $style				.= $data["parent_style"].";";
	
					if(isset($value)){
						if(($combo_data["match_type"]=="show" && strpos(",".$combo_data["match_value"].",", ",".$value.",")===false) || ($combo_data["match_type"]=="hide" && strpos(",".$combo_data["match_value"].",", ",".$value.",")!==false)){
							$style		.= "display:none;";
						}
					}else{
						if($combo_data["match_type"]=="show") $style		.= "display:none;";
					}				
					$template_start	.= '<'.esc_attr($parent_tag).' class="mb-combo-box mb-combo-'.esc_attr($combo_data["item_name"].$item_index)." ".esc_attr($parent_class).'" style="'.esc_attr($style).'">';
						if(!empty($combo_data['add_start_html'])) $template_start	.= $combo_data['add_start_html'];
						if(!empty($data["tpl"]) && $data["tpl"]!="item"){
							$data				= mbw_init_subitem_data($mode,$data);
							$template_start	.= mbw_get_extension_template($data);
						}else{
							$template_start	.= mbw_create_template($mode, $data);
						}
						if(!empty($combo_data['add_end_html'])) $template_start	.= $combo_data['add_end_html'];
						if(!empty($combo_data["description"])) $template_start	.= '<span class="mb-description">'.$combo_data["description"].'</span>';
					$template_start	.= '</'.esc_attr($parent_tag).'>';
				}
				return $template_start;
			}else if(!empty($data["join"])){
				if(isset($data["join"][0])){
					$item_name				= $data["item_name"];
					$template_start			.= '<span class="mb-join-wrapper">';
					$template_start			.= mbw_create_template($mode, $data);
					if(!empty($data['add_join_html'])) $template_start	.= $data['add_join_html'];	
					$template_join				= "";

					foreach($data["join"] as $item){					
						if(!empty($item["class"])){
							$item["o_class"]	= trim($item["class"]);
							$item["class"]		= ' class="'.$item["o_class"].'"';							
						}else {
							$item["o_class"]	= "";
							$item["class"]		= "";							
						}
						$join_data						= mbw_init_item_property($mode,$item);					
						$t_data							=  $join_data;
						if(!isset($t_data["value"])){
							if(!empty($t_data["field"])){
								if($mode=="comment"){
									$t_data["value"]			= mbw_get_comment_item($t_data["field"],false);
								}else{
									$t_data["value"]			= mbw_get_board_item($t_data["field"],false);
								}
							}else{
								$t_data["value"]			= "";
							}
						}

						if($t_data["value"]!=="" && ($t_data["type"]=="" || $t_data["type"]=="static" || $t_data["type"]=="view") && (!empty($t_data["label"]) || !empty($t_data["data"]))){
							if(!isset($t_data["label"]) && isset($t_data["data"])) $t_data["label"]		= $t_data["data"];
							if(!isset($t_data["data"]) && isset($t_data["label"])) $t_data["data"]		= $t_data["label"];
							$delimiter			= ",";
							if(isset($t_data["delimiter"])) $delimiter		= $t_data["delimiter"];
							$t_data2				= explode($delimiter,$t_data["data"]);
							$t_label2				= explode($delimiter,$t_data["label"]);
							$count				= count($t_data2);
							for($i=0;$i<$count;$i++){
								if($t_data["value"]==$t_data2[$i]){
									if(isset($t_label2[$i])) $t_data["value"]		= $t_label2[$i];
								}
							}
						}
						if(isset($t_data["value"])){
							$t_data["o_value"]		= $t_data["value"];
						}

						if((mbw_get_param("board_action")!="modify" || mbw_get_param("mode")!="write") && $t_data["value"]==="" && isset($join_data["default"])){
							$t_data["value"]		=  $join_data["default"];
						}

						if(isset($t_data["prepend_text"]) && isset($t_data["value"])){
							$t_data["value"]			= $t_data["prepend_text"].$t_data["value"];
						}
						if(isset($t_data["append_text"]) && isset($t_data["value"])){
							$t_data["value"]			= $t_data["value"].$t_data["append_text"];
						}

						$template_join	.= '<span class="mb-join-box mb-join-'.esc_attr($item_name).'">';
						if(!empty($join_data['add_start_html'])) $template_join		.= $join_data['add_start_html'];
						if(!empty($t_data["tpl"]) && $t_data["tpl"]!="item"){
							$t_data			= mbw_init_subitem_data($mode,$t_data);
							$template_join	.= mbw_get_extension_template($t_data);
						}else{
							$template_join	.= mbw_create_template($mode, $t_data);
						}
						if(!empty($join_data['add_end_html'])) $template_join		.= $join_data['add_end_html'];
						$template_join	.= '</span>';
					}
					$style					= "";
					$parent_tag			= "span";
					$parent_class		= "mb-join-items";
					if(!empty($data["parent_tag"])) $parent_tag		= $data["parent_tag"];
					if(!empty($data["parent_class"])) $parent_class	.= " ".$data["parent_class"];
					if(!empty($data["parent_style"])) $style				.= $data["parent_style"].";";

					$template_start	.= '<'.esc_attr($parent_tag).' class="'.esc_attr($parent_class).'" style="'.esc_attr($style).'">';
						$template_start	.= $template_join;					
					$template_start	.= '</'.esc_attr($parent_tag).'>';
					$template_start	.= '</span>';

					unset($data["join"]);
				}else{

					if(!empty($data["join"]["class"])){
						$data["join"]["o_class"]	= trim($data["join"]["class"]);
						$data["join"]["class"]		= ' class="'.esc_attr($data["join"]["o_class"]).'"';						
					}else{
						$data["join"]["o_class"]	= "";
						$data["join"]["class"]		= "";						
					}
					$join_data					= mbw_init_item_property($mode,$data["join"]);
					$template_start			.= mbw_create_template($mode, $data);
					if(!empty($data['add_join_html'])) $template_start	.= $data['add_join_html'];	
					$data							=  $join_data;
					if(!isset($data["value"])){
						if(!empty($data["field"])){
							if($mode=="comment"){
								$data["value"]			= mbw_get_comment_item($data["field"],false);
							}else{
								$data["value"]			= mbw_get_board_item($data["field"],false);
							}
						}else{
							$data["value"]			= "";
						}
					}

					if($data["value"]!=="" && ($data["type"]=="" || $data["type"]=="static" || $data["type"]=="view") && (!empty($data["label"]) || !empty($data["data"]))){
						if(!isset($data["label"]) && isset($data["data"])) $data["label"]		= $data["data"];
						if(!isset($data["data"]) && isset($data["label"])) $data["data"]		= $data["label"];
						$delimiter			= ",";
						if(isset($data["delimiter"])) $delimiter		= $data["delimiter"];
						$t_data2				= explode($delimiter,$data["data"]);
						$t_label2				= explode($delimiter,$data["label"]);
						$count				= count($t_data2);
						for($i=0;$i<$count;$i++){
							if($data["value"]==$t_data2[$i]){
								if(isset($t_label2[$i])) $data["value"]		= $t_label2[$i];
							}
						}
					}
					if(isset($data["value"])){
						$data["o_value"]		= $data["value"];
					}					
					if((mbw_get_param("board_action")!="modify" || mbw_get_param("mode")!="write") && $data["value"]==="" && isset($join_data["default"])) $data["value"]		=  $join_data["default"];
					unset($data["join"]);

					if(isset($data["prepend_text"]) && isset($data["value"])){
						$data["value"]			= $data["prepend_text"].$data["value"];
					}
					if(isset($data["append_text"]) && isset($data["value"])){
						$data["value"]			= $data["value"].$data["append_text"];
					}

					$style					= "";
					$parent_tag			= "span";
					$parent_class		= "";
					if(!empty($data["parent_tag"])) $parent_tag		= $data["parent_tag"];
					if(!empty($data["parent_class"])) $parent_class	= $data["parent_class"];
					if(!empty($data["parent_style"])) $style				.= $data["parent_style"].";";

					$template_start	.= '<'.esc_attr($parent_tag).' class="mb-join-box mb-join-'.esc_attr($join_data["item_name"])." ".esc_attr($parent_class).'" style="'.esc_attr($style).'">';
					if(!empty($join_data['add_start_html'])) $template_start		.= $join_data['add_start_html'];
					if(!empty($data["tpl"]) && $data["tpl"]!="item"){
						$data				= mbw_init_subitem_data($mode,$data);
						$template_start	.= mbw_get_extension_template($data);
					}else{
						$template_start	.= mbw_create_template($mode, $data);
					}
					if(!empty($join_data['add_end_html'])) $template_start		.= $join_data['add_end_html'];
					$template_start	.= '</'.esc_attr($parent_tag).'>';
				}

				return $template_start;
			}else{
				return mbw_create_template($mode, $data);
			}			
		}		
	}
}


if(!function_exists('mbw_create_template')){
	function mbw_create_template($mode, $data){
		global $mdb,$mstore,$mb_languages;		
				
		if(empty($data["label"]) && isset($data["data"])) $data["label"]	= $data["data"];
		if(empty($data["data"]) && isset($data["label"])) $data["data"]	= $data["label"];
		if(empty($data["ext"])) $data["ext"]			= "";
		if(empty($data["style"])) $data["style"]		= "";

		$template_start		= "";
		
		if($mode=="list"){
			$data["item_name"]		= $data["item_name"]."[".(mbw_get_item_index()-1)."]";
			$data["item_id"]			= $data["item_id"].mbw_get_item_index();
		}else if($mode=="comment"){
			//$data["item_id"]			= "";
		}
		if(!empty($data["type"])){
			$item_type		= $data["type"];
			$templates		= $mstore->get_templates();
			if(!empty($templates)){
				foreach($templates as $key=>$value){
					if(strpos($item_type,$key."_")===0){
						if(function_exists($templates[$key]))
							$template_start		= call_user_func($templates[$key],$mode, $data);
						break;
					}
				}
			}
			if($template_start=="none" || $template_start=="empty") $template_start		= "";
			else if(empty($template_start) && function_exists('mbw_get_input_template')) $template_start		= call_user_func("mbw_get_input_template",$mode, $data);
		}
		
		return $template_start;
	}
}

if(!function_exists('mbw_get_extension_template')){
	function mbw_get_extension_template($data){
		$template_start		= "";
		$data						= mbw_init_item_property(mbw_get_param("mode"),$data);

		if($data["tpl"]=="tag"){
			$template_start	= mbw_get_tag_template($data);
		}else if($data["tpl"]=="html"){			
			if(!empty($data["content"])) $template_start	= html_entity_decode($data["content"]);			
			else if(!empty($data["code"])) $template_start	= mbw_htmlspecialchars_decode($data["code"]);			
			else if(!empty($data["text"])) $template_start	= mbw_htmlspecialchars_decode($data["text"]);
			if(!empty($template_start)){				
				$template_start	= str_replace(array("“","”","″"),'"',$template_start);
				$template_start	= str_replace(array("‘","’","′"),"'",$template_start);
				$template_start	= str_replace('script',"",$template_start);
			}
		}else if($data["tpl"]=="include_file"){
			$path					= "";
			if(!empty($data["skin_name"])){
				$data["skin_name"]		= trim(str_replace(".","",mbw_value_filter($data["skin_name"])),'/');
				if(empty($data["skin_name"])) return;
				$path				= MBW_PLUGIN_PATH."skins/".$data["skin_name"].'/';
			}else return;
			if(!empty($path) && !empty($data["file_name"])){
				$data["file_name"]	= str_replace("..","",$data["file_name"]);
				$file_name			= mbw_value_filter($data["file_name"]);
				if(strpos($file_name, 'http') !== 0){
					$file_path		= $path.basename($file_name);
					if(is_file($file_path)) include($file_path);
				}
			}		
		}
		return $template_start;
	}
}

if(!function_exists('mbw_get_board_type_search_template')){
	function mbw_get_board_type_search_template(){
		echo '<div class="border-bottom-ccc-1" style="margin-bottom:10px !important;padding:10px 0 !important;text-align:right;">';
		mbw_create_search_template("board_type");
		echo '</div>';
	}
}

if(!function_exists('mbw_get_date_search_template')){
	function mbw_get_date_search_template(){
		echo '<div class="border-bottom-ccc-1" style="margin-bottom:10px !important;padding:10px 0 !important;text-align:right;">';
		mbw_create_search_template("date_range");
		echo '</div>';
	}
}

if(!function_exists('mbw_create_search_template')){
	function mbw_create_search_template($type=""){
		global $mb_vars,$mdb,$mb_admin_tables,$mb_fields;
		global $mb_shop_order_state,$mb_shop_sale_status;

		if($type=="board_type"){
			echo '<div>';	
			$search_field		= "fn_".$type;
			$board_type		= $mdb->get_results($mdb->prepare("select distinct %1s from %1s where %1s=1;",$mb_fields["board_options"][$search_field],$mb_admin_tables["board_options"],$mb_fields["board_options"]["fn_is_show"]),ARRAY_A);
			$type_array		= array();
			foreach($board_type as $value){
				$type_array[]			= $value[$mb_fields["board_options"][$search_field]];
			}
			$input_values		= __MW("W_ALL").",".implode(",",$type_array);
			$input_keys			= ",".implode(",",$type_array);
			$input_type			= "radio";				//select or radio
			echo '<input type="hidden" name="search_add_field1" value="'.esc_attr($search_field).'" />';
			echo mbw_get_item_template("search",array("item_name"=>"search_add_text1","type"=>$input_type,"width"=>"100px","data"=>$input_keys,"label"=>$input_values,"value"=>mbw_get_param("search_add_text1")));
			echo '</div>';
		}else if($type=="date_range"){		
			wp_enqueue_style('jquery-ui-css');
			echo '<div>';
			if(mbw_get_vars("device_type")!="mobile"){
				echo '<label for="search_range_today"><input type="radio" name="search_range" id="search_range_today" onclick="setSearchDate(\'today\')">'.__MW("W_TODAY").'</label>';
				echo '<label for="search_range_yesterday"><input type="radio" name="search_range" id="search_range_yesterday" onclick="setSearchDate(\'yesterday\')">'.__MW("W_YESTERDAY").'</label>';
				echo '<label for="search_range_week"><input type="radio" name="search_range" id="search_range_week" onclick="setSearchDate(\'week\')">'.__MW("W_ONE_WEEK").'</label>';
				echo '<label for="search_range_month"><input type="radio" name="search_range" id="search_range_month" onclick="setSearchDate(\'month\')">'.__MW("W_ONE_MONTH").'</label>';
				echo '<label for="search_range_this_month"><input type="radio" name="search_range" id="search_range_this_month" onclick="setSearchDate(\'this_month\')">'.__MW("W_THIS_MONTH").'</label>';
				echo '<label for="search_range_last_month"><input type="radio" name="search_range" id="search_range_last_month" onclick="setSearchDate(\'last_month\')">'.__MW("W_LAST_MONTH").'</label>';
				echo '<label for="search_range_total"><input type="radio" name="search_range" id="search_range_total" onclick="setSearchDate(\'total\')">'.__MW("W_TOTAL").'</label>';
			}
			echo '<input type="text" id="start_date" class="show-datepicker" name="start_date" style="width:100px !important;" placeholder="'.__MW("W_START_DATE").'" value="'.esc_attr(mbw_get_param("start_date")).'" /> ~ ';
			echo '<input type="text" id="end_date" class="show-datepicker" name="end_date" style="width:100px !important;" placeholder="'.__MW("W_END_DATE").'" value="'.esc_attr(mbw_get_param("end_date")).'" />';
			echo mbw_get_btn_template(array("name"=>"Search","onclick"=>"sendSearchData()","class"=>"btn btn-default btn-search margin-left-5"));
			echo "</div>";
		}
	}
}

if(!function_exists('mbw_get_copy_move_template')){
	function mbw_get_copy_move_template(){
		if(mbw_get_trace("mbw_get_copy_move_template")==""){
			mbw_add_trace("mbw_get_copy_move_template");
			$item_copy_html		= mbw_get_item_template("view",array("type"=>"admin_select_board_list","item_name"=>"select_board_name","data"=>"","value"=>"","class"=>"margin-right-5"));
			if(!empty($item_copy_html)) $item_copy_html		= $item_copy_html.mbw_get_btn_template(array("name"=>"Move","onclick"=>"showMoveConfirm('multi_move')","class"=>"btn btn-default")).mbw_get_btn_template(array("name"=>"Copy","onclick"=>"showMoveConfirm('multi_copy')","class"=>"btn btn-default"));
			mbw_add_left_button("list",$item_copy_html);
		}
	}
}

if(!function_exists('mbw_add_template')){
	function mbw_add_template($key,$func){
		global $mstore;
		$mstore->set_template($key,$func);		
	}
}

if(!function_exists('mbw_is_display_item')){
	function mbw_is_display_item($data){
		if(empty($data[mbw_get_param("board_action")])){
			$check_data		= explode(":",$data["display_check"]);
			$check_result		= false;
			$display_type		= $check_data[0];
			if($display_type=="all"){
				$check_result			= true;
			}else if($display_type=="empty" && mbw_get_board_item($data["field"],false)==""){
				$check_result			= true;
			}else if($display_type=="zero" && (mbw_get_board_item($data["field"],false)==="0" || mbw_get_board_item($data["field"],false)===0)){
				$check_result			= true;
			}else if($display_type=="login" && mbw_is_login()){
				$check_result			= true;
			}else if($display_type=="dir"){
				$path			= $check_data[2];
				$path			= trim(str_replace(".","",$path),'/');
				$path			= MBW_PLUGIN_PATH.$path;
				if(is_dir($path)) $check_result			= true;
			}else if($display_type=="file"){
				$path			= $check_data[2];
				$path			= trim(str_replace(".","",$path),'/');
				$path			= MBW_PLUGIN_PATH.$path;
				if(is_file($path)) $check_result			= true;
			}else if($display_type=="equal"){
				$field		= $data["field"];
				if(!empty($check_data[3])) $field		= $check_data[3];
				if(strpos(",".$check_data[2].",", ",".mbw_get_board_item($field,false).",")!==false) $check_result			= true;
			}else if($display_type=="not_equal"){
				$field		= $data["field"];
				if(!empty($check_data[3])) $field		= $check_data[3];
				if(strpos(",".$check_data[2].",", ",".mbw_get_board_item($field,false).",")===false) $check_result			= true;				
			}else if($display_type=="not_login" && !mbw_is_login()){
				$check_result			= true;
			}
			if($check_result){
				if($check_data[1]=="show" || $check_data[1]=="none" || $check_data[1]=="hide") $data["display"]		= $check_data[1];
				else $data["type"]		= $check_data[1];
			}else{
				if($check_data[1]=="show") $data["type"]		= "none";
			}

		}
		return $data;
	}
}
if(!function_exists('mbw_get_default_editor')){
	function mbw_get_default_editor($data){
		mbw_set_board_option("fn_editor_type","N");
		if(empty($data["width"])) $data["width"]			= '100%';
		if(empty($data["height"])) $data["height"]		= '300px';
		return '<textarea'.$data["ext"].__STYLE("width:".$data["width"].";height:".$data["height"].";".$data["style"]).'  name="'.esc_attr($data["item_name"]).'" id="'.esc_attr($data["item_id"]).'" title="'.esc_attr($data["name"]).'" ">'.($data["value"]).'</textarea><input type="hidden" name="data_type" id="data_type" value="text" /><script type="text/javascript">setEditorType("N");</script>';
	}
}
if(!function_exists('mbw_get_list_setup_data')){
	function mbw_get_list_setup_data($model,$table_name=""){
		global $mstore,$mdb;

		$template_list_width		= "";
		$template_list_search	= "";
		$template_list_title		= "";
		$list_fields				= "";
		$list_cols					= 0;
		$list_data					= array();

		foreach($model as $data){
			if(mbw_check_item($data)){
				if(!isset($data["field"])) $data["field"] = "";
				if(!isset($data["name"])) $data["name"] = "";

				if(!empty($data["name"]))	$data["name"]		= __MW($data["name"]);

				if(!empty($data[mbw_get_vars("device_type")."_width"])) $data["width"]		= $data[mbw_get_vars("device_type")."_width"];
				else if(!isset($data["width"])) $data["width"] = "50px";
						
				if(!empty($data["display"])) $item_display		= $data["display"];
				else $item_display		= "";
				if(!empty($data["type"])) $item_type		= $data["type"];
				else $item_type		= "";

				if(empty($data["th_style"])) $data["th_style"]		= "";
				if(empty($data["col_class"])) $data["col_class"]		= "";
				if(empty($data["th_class"])) $data["th_class"]		= "";

				$add_class_name		= 'item';
				if(!empty($data["field"])){
					$add_class_name		= str_replace('fn_',"",$data["field"]);
				}else if(!empty($data["type"])){
					$add_class_name		= $data["type"];
				}
				$add_class_name		= str_replace('_','-',$add_class_name);
				if(!empty($data["col_class"])){ $data["col_class"]	.= ' '; }
				$data["col_class"]	.= 'mb-col-'.$add_class_name;
				if(!empty($data["th_class"])){ $data["th_class"]	.= ' '; }
				$data["th_class"]		.= 'mb-th-'.$add_class_name;
				
				if($item_display!="hide" && $item_display!="none" && $item_type!="none" && $item_type!="search"){
					if(!empty($data["responsive"])){
						if(!empty($data["col_class"])){ $data["col_class"]	.= ' '; }
						$data["col_class"]		.= $data["responsive"];
						if(!empty($data["th_class"])){ $data["th_class"]	.= ' '; }
						$data["th_class"]			.= $data["responsive"];
					}

					$order_type		= mbw_get_param("order_type");
					if($order_type=="desc"){
						$order_type		= "asc";
					}else{
						$order_type		= "desc";
					}

					if(!empty($data["col_class"])) $data["col_class"]		= ' class="'.esc_attr(trim($data["col_class"])).'"';
					if($data["field"]==mbw_get_param("order_by") && !empty($data["th_class"])) $data["th_class"]		= $data["th_class"].' order-'.$order_type;
					if(!empty($data["th_class"])) $data["th_class"]		= ' class="'.esc_attr(trim($data["th_class"])).'"';

					if($data["width"]!="" && $data["width"]!="*"){
						$template_list_width	.= '<col style="width:'.esc_attr($data["width"]).'"'.$data["col_class"].' />';
					}else{
						$template_list_width	.= '<col'.$data["col_class"].' />';
					}

					if(isset($data["order"]) && ($data["order"]=="false" || $data["order"]=="0")){
						$template_list_title	.= '<th scope="col"'.$data["th_class"].__STYLE($data["th_style"]).'><span>'.esc_html($data["name"]).'</span></th>';
					}else if(isset($data["type"]) && $data["type"]=="list_check"){
						$template_list_title	.= '<th scope="col"'.$data["th_class"].__STYLE($data["th_style"]).'><input type="checkbox" name="mb_check_all" style="min-width:16px;min-height:16px;"/></th>';
					}else if($data["field"]==mbw_get_param("order_by")){
						$template_list_title	.= '<th scope="col"'.$data["th_class"].__STYLE($data["th_style"]).' class="order-'.esc_attr($order_type).'"><a href="'.esc_url(mbw_get_url(array('order_by'=>$data["field"],'order_type'=>$order_type))).'" title="'.esc_attr($data["name"]).'"><span>'.esc_html($data["name"]).'</span></a></th>';
					}else{
						$template_list_title	.= '<th scope="col"'.$data["th_class"].__STYLE($data["th_style"]).'><a href="'.esc_url(mbw_get_url(array('order_by'=>$data["field"],'order_type'=>$order_type))).'" title="'.esc_attr($data["name"]).'"><span>'.esc_html($data["name"]).'</span></a></th>';
					}
					$list_cols		= $list_cols+1;
				}

				$check_selected	= "";
				if(mbw_get_param("search_field")==$data["field"]) $check_selected		= " selected";

				if(!isset($data["search"]) || ($data["search"]!="false" && $data["search"]!="0")){
					if($data["name"]!="") $template_list_search	.= '<option value="'.esc_attr($data["field"]).'" '.$check_selected.'>'.esc_html($data["name"]).'</option>';				
				}			

				if($list_fields==""){
					$list_fields			= $data["field"];
				}else{
					$list_fields			= $list_fields.",".$data["field"];
				}
			}
		}
		if(mbw_get_param("search_field")=="fn_user_pid" && mbw_get_param("search_text")!=""){
			if(strpos($template_list_search,"fn_user_pid")===false){
				$template_list_search	.= '<option value="fn_user_pid" selected>'.__MW("W_USER_PID").'</option>';				
			}
		}

		if(mbw_get_param("page_size")!=""){		
			$page_size			= intval(mbw_get_param("page_size"));
		}else{
			$page_size			= intval(mbw_get_board_option("fn_page_size"));
		}		
		
		if(empty($table_name)){
			$total_count		= $mdb->get_var(mbw_get_add_query(array("column"=>"count(*)"), "where"));
		}else{
			$total_count		= $mdb->get_var(mbw_get_add_query(array("column"=>"count(*)","table"=>$table_name), "where"));
		}
		$total_page			= ceil($total_count / $page_size);
		$board_page		= intval(mbw_get_param("board_page"));


		if($board_page > $total_page && $total_count>0){
			$board_page		= $total_page;
			mbw_set_param("board_page", $board_page);
		}

		$page_start		= ($board_page - 1) * $page_size;

		if($page_start > $total_count){
			if($page_size<$total_count) $page_start = $total_count-$page_size;
			else $page_start = 0;
		}
		if($page_start<0) $page_start = 0;

		$list_data["page"]			= $board_page;
		$list_data["page_size"]		= $page_size; 
		$list_data["page_start"]		= $page_start; 
		$list_data["total_count"]	= $total_count; 
		$list_data["total_page"]		= $total_page; 

		$list_data["width"]			= $template_list_width;
		$list_data["title"]				= $template_list_title;
		$list_data["cols"]				= $list_cols;
		$list_data["search"]			= $template_list_search;
		$list_data["fields"]			= $list_fields;

		return $list_data;
	}
}
if(!function_exists('mbw_get_template_path')){
	function mbw_get_template_path($name,$type="plugin",$ext="php"){
		$add_name		= mbw_get_param("tpl_name");
		if($type=='theme'){
			if(!empty($add_name)) {
				$path2			= get_stylesheet_directory()."/".MBW_PLUGIN_DIR."/templates/".$name.$add_name.".".$ext;
				if(is_file($path2)) return $path2;
			}
			$path1			= get_stylesheet_directory()."/".MBW_PLUGIN_DIR."/templates/".$name.".".$ext;
			if(is_file($path1)) return $path1;
		}		
		if(!empty($add_name)) {
			$path2			= MBW_PLUGIN_PATH."templates/".$name.$add_name.".".$ext;
			if(is_file($path2)) return $path2;
		}
		$path1			= MBW_PLUGIN_PATH."templates/".$name.".".$ext;
		if(is_file($path1)) return $path1;
		else return false;
	}
}
if(!function_exists('mbw_get_template_url')){
	function mbw_get_template_url($name,$type="plugin",$ext="php"){
		if($type=='theme'){
			$path					= "/".MBW_PLUGIN_DIR."/templates/".$name.".".$ext;
			if(is_file(get_stylesheet_directory().$path)) return get_stylesheet_directory_uri().$path;
		}
		$path					= "templates/".$name.".".$ext;
		if(is_file(MBW_PLUGIN_PATH.$path)) return MBW_PLUGIN_URL.$path;
		return false;
	}
}
?>