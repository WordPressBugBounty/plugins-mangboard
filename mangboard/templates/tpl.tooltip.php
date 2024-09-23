<?php
if(!function_exists('mbw_get_tooltip_template')){
	function mbw_get_tooltip_template($data){
		if(has_filter('mf_board_tooltip_template')){
			$template		= apply_filters("mf_board_tooltip_template", $data);
			if(!empty($template)) return $template;
		}

		if(is_array($data)){
			if(empty($data["class"])) $data["class"]				= "tooltip";
			else $data["class"]				= "mb-tooltip ".$data["class"];
			if(empty($data["text"])) return;

			if(empty($data["img"])){
				return ' <span class="'.esc_attr($data["class"]).'" title="'.esc_attr(htmlspecialchars($data["text"])).'">[?]</span>';
			}else{
				return ' <img class="'.esc_attr($data["class"]).'" src="'.esc_url($data["img"]).'" title="'.esc_attr(htmlspecialchars($data["text"])).'"/>';
			}
		}else if(is_string($data)){
			return ' <span class="mb-tooltip" title="'.esc_attr(htmlspecialchars($data)).'">[?]</span>';
		}
	}
}
?>