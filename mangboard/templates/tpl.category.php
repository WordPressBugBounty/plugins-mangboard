<?php
if(!function_exists('mbw_get_category_template')){
	function mbw_get_category_template($type, $data=""){
		if(has_filter('mf_board_category_template')){
			$template		= apply_filters("mf_board_category_template",$type, $data);
			if(!empty($template)) return $template;
		}

		$board_name			= mbw_get_board_name();	
		$add_script				= "";
		$template_category	= "";
		$t_name					= "Category";		

		// 계층 구조가 있는지 여부 파악
		if(strpos($data, '{')===false)
			$hierarchical		= false;
		else
			$hierarchical		= true;
		if($data=="") 
			$type		= "NONE";

		$category1				= mbw_get_param("category1");
		$category2				= mbw_get_param("category2");
		$category3				= mbw_get_param("category3");
		
		// SELECT 박스로 카테고리 보여주기
		if(strpos($type, "SELECT")===0 && $data!=""){
			$add_event			= "";
			if($type=="SELECT_AJAX"){
				if(mbw_get_param("list_type")=="calendar") $add_event		= "sendSearchCalendarData();";
				else $add_event		= "sendListTemplateData();";
			}else if($type=="SELECT_RELOAD"){
				$add_event			= "sendSearchData();";
			}
			
			if(!$hierarchical){
				if(mbw_get_board_item("fn_category1")!="") $category1				= mbw_htmlspecialchars_decode(mbw_get_board_item("fn_category1"));
				if(!empty($category1) && strpos($category1, ',') !== false){
					$category1_array		= explode(',',$category1);
					$category1		= $category1_array[0];
				}
				$template_category	.= mbw_get_item_template("search",array("field"=>"fn_category1","item_id"=>$board_name."_category1","event"=>"onchange='".$add_event."'","type"=>"select","title"=>$t_name.'1',"data"=>",".$data,"label"=>__MW("W_ALL").",".$data,"value"=>$category1));
				$template_category	.= '<input type="hidden" name="'.esc_attr(mbw_set_form_name("category2")).'" value="'.esc_attr($category2).'" />';
				$template_category	.= '<input type="hidden" name="'.esc_attr(mbw_set_form_name("category3")).'" value="'.esc_attr($category3).'" />';
			}else{
				if(mbw_get_board_item("fn_category1")!="") $category1				= mbw_htmlspecialchars_decode(mbw_get_board_item("fn_category1"));
				if(mbw_get_board_item("fn_category2")!="") $category2				= mbw_htmlspecialchars_decode(mbw_get_board_item("fn_category2"));
				if(mbw_get_board_item("fn_category3")!="") $category3				= mbw_htmlspecialchars_decode(mbw_get_board_item("fn_category3"));
				$template_category	.= mbw_get_item_template("search",array("field"=>"fn_category1","item_id"=>$board_name."_category1","event"=>"onchange='category_select(1);".$add_event."'","type"=>"select","title"=>$t_name.'1',"data"=>"","value"=>"","style"=>"display:none;"));
				$template_category	.= mbw_get_item_template("search",array("field"=>"fn_category2","item_id"=>$board_name."_category2","event"=>"onchange='category_select(2);".$add_event."'","type"=>"select","title"=>$t_name.'2',"data"=>"","value"=>"","style"=>"display:none;"));
				$template_category	.= mbw_get_item_template("search",array("field"=>"fn_category3","item_id"=>$board_name."_category3","event"=>"onchange='category_select(3);".$add_event."'","type"=>"select","title"=>$t_name.'3',"data"=>"","value"=>"","style"=>"display:none;"));	
				$add_script		= '<script type="text/javascript">jQuery( document ).ready(function() {';
				if(!empty($category1)) $add_script		.= 'mb_categorys["value1"]	= "'.esc_js($category1).'";';
				if(!empty($category2)) $add_script		.= 'mb_categorys["value2"]	= "'.esc_js($category2).'";';
				if(!empty($category3)) $add_script		.= 'mb_categorys["value3"]	= "'.esc_js($category3).'";';
				$add_script		.= 'category_select(0);}); </script>';
			}
		// 카테고리를 탭메뉴로 보여주기
		}else if(strpos($type, "TAB")===0 && $data!=""){

			$tab_menu				= array();
			if($category1=="") $tab_menu_class			= "tab-menu-on";
			else $tab_menu_class			= "tab-menu-off";
			if($type=="TAB_RELOAD"){
				$template_category	.= mbw_get_btn_template(array("name"=>"All","onclick"=>"sendTabReload('')","class"=>$tab_menu_class));
			}else if($type=="TAB_AJAX"){
				$template_category	.= mbw_get_btn_template(array("name"=>"All","onclick"=>"selectTabMenu(this,'')","class"=>$tab_menu_class));
			}
			$category1_array		= array();			
			if(!empty($category1) && strpos($category1, ',') !== false){
				$category1_array		= explode(',',$category1);
			}
			if(!$hierarchical){
				$tab_menu				= explode(",",$data);
			}else{
				$data		= mbw_json_decode(trim($data));
				if(!empty($data[0])){
					foreach($data[0] as $key => $value){
						$tab_menu[]				= $key;
					}
				}
			}
			
			$tab_menu_class								= "tab-menu-off";
			foreach($tab_menu as $value){
				if(strpos($value, "=>")!==false){
					$value_array	= explode("=>",$value);
					$data				= $value_array[0];
					$label			= $value_array[1];
				}else{
					$data				= $value;
					$label			= $value;
				}
				if($data==$category1) $tab_menu_class			= "tab-menu-on";
				else if(!empty($category1_array) && in_array($data, $category1_array)) $tab_menu_class			= "tab-menu-on";
				else $tab_menu_class			= "tab-menu-off";
				
				if($type=="TAB_RELOAD"){
					$template_category	.= mbw_get_btn_template(array("name"=>$label,"onclick"=>"sendTabReload('".esc_js(rawurlencode($data))."')","class"=>$tab_menu_class));
				}else if($type=="TAB_AJAX"){
					$template_category	.= mbw_get_btn_template(array("name"=>$label,"onclick"=>"selectTabMenu(this,'".esc_js($data)."')","class"=>$tab_menu_class));
				}
			}
			$template_category	.= '<input type="hidden" name="'.esc_attr(mbw_set_form_name("category1")).'" value="'.esc_attr($category1).'" />';
			if($category1=="" && $category2!="") mbw_set_param("category2","");
			$template_category	.= '<input type="hidden" name="'.esc_attr(mbw_set_form_name("category2")).'" value="'.esc_attr($category2).'" />';
			if($category2=="" && $category3!="") mbw_set_param("category3","");
			$template_category	.= '<input type="hidden" name="'.esc_attr(mbw_set_form_name("category3")).'" value="'.esc_attr($category3).'" />';
		}else{
			$template_category	.= '<input type="hidden" name="'.esc_attr(mbw_set_form_name("category1")).'" value="'.esc_attr($category1).'" />';
			if($category1=="" && $category2!="") mbw_set_param("category2","");
			$template_category	.= '<input type="hidden" name="'.esc_attr(mbw_set_form_name("category2")).'" value="'.esc_attr($category2).'" />';
			if($category2=="" && $category3!="") mbw_set_param("category3","");
			$template_category	.= '<input type="hidden" name="'.esc_attr(mbw_set_form_name("category3")).'" value="'.esc_attr($category3).'" />';
		}			
		$template_category	.= $add_script;				
		return $template_category;
	}
}
?>