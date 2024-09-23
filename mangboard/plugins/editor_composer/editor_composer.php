<?php
if(!function_exists('mbw_footer_video_scripts')){	
	function mbw_footer_video_scripts(){
		loadScript(MBW_PLUGIN_URL."plugins/editor_composer/js/video.js","mb-editor-video");
	}
}
add_action('wp_footer', 'mbw_footer_video_scripts',15);

if(!function_exists('mbw_init_editor_composer')){
	function mbw_init_editor_composer(){
		if(mbw_get_trace("mbw_init_editor_composer")==""){
			mbw_add_trace("mbw_init_editor_composer");
			wp_enqueue_script( 'jquery-ui-widget', false, array('jquery') );
			loadStyle(MBW_PLUGIN_URL."plugins/editor_composer/css/evol-colorpicker.min.css");
			loadScript(MBW_PLUGIN_URL."plugins/editor_composer/js/evol-colorpicker.min.js","",array('jquery','jquery-ui-widget'));
			loadStyle(MBW_PLUGIN_URL."plugins/editor_composer/css/style.css");
			loadScript(MBW_PLUGIN_URL."plugins/editor_composer/js/main.js");
		}
	}
}

//템플릿 함수 등록(템플릿 타입의 접두사, 템플릿 함수명)
add_action('mbw_editor_S','mbw_smart_editor_composer_template',6,2);
add_action('mbw_editor_HS','mbw_smart_editor_composer_template',6,2);
add_action('mbw_editor_HS2','mbw_smart_editor_composer_template',6,2);
add_action('mbw_editor_HS3','mbw_smart_editor_composer_template',6,2);
add_action('mbw_editor_C','mbw_ck_editor_composer_template',6,2);
if(!function_exists('mbw_smart_editor_composer_template')){
	function mbw_smart_editor_composer_template($action, $data){
		if(!empty($data["editor_id"])){
			$editor_id			= $data["editor_id"];
		}else{
			$editor_id			= "se_content";
		}
		mbw_get_editor_composer_template("smart", $editor_id, $data);
	}
}
if(!function_exists('mbw_ck_editor_composer_template')){
	function mbw_ck_editor_composer_template($action, $data){
		if(!empty($data["editor_id"])){
			$editor_id			= $data["editor_id"];
		}else{
			$editor_id			= "ce_content";
		}
		mbw_get_editor_composer_template("ck", $editor_id, $data);
	}
}
if(!function_exists('mbw_get_editor_composer_template')){
	function mbw_get_editor_composer_template($type="", $editor_id="", $data=array()){
		$board_name		= mbw_get_board_name();
		if(empty($board_name)) return false;

		if(empty($editor_id)) $editor_id			= "se_content";

		if(mbw_get_option("ecomposer_use_level")!=""){
			$mb_ecomposer_except_board		= mbw_get_option("ecomposer_except_board");
			$mb_ecomposer_use_board			= mbw_get_option("ecomposer_use_board");
			$mb_ecomposer_use_level				= mbw_get_option("ecomposer_use_level");
		}else{
			include(MBW_PLUGIN_PATH."plugins/editor_composer/config.php");
		}		

		
		$user_level			= intval(mbw_get_user("fn_user_level"));
		//허용 예외 게시판일 경우 컴포저 표시 안함
		if(!empty($mb_ecomposer_except_board) && strpos(','.$mb_ecomposer_except_board.',', ','.$board_name.',') !== false) return false;
		//허용 게시판이 아닐 경우 컴포저 표시 안함
		if(!empty($mb_ecomposer_use_board)){
			if(strpos(','.$mb_ecomposer_use_board.',', ','.$board_name.',') === false) return false;
		}
		//회원 레벨이 허용 레벨보다 작으면 컴포저 표시 안함
		if($mb_ecomposer_use_level>$user_level)  return false;
		mbw_init_editor_composer();
		global $mb_words;

		$box_template		= '
		<div class="mb-editor-board-style5">
			<div class="mb-editor-border mb-editor-board-style4">
				<div class="mb-editor-label1">Border</div>
				<input type="text" name="mb-editor-border-top-width" data-name="mb-editor-border-top-width" class="mb-editor-border-top-width" data-attribute="border" value="0">
				<input type="text" name="mb-editor-border-right-width" data-name="mb-editor-border-right-width" class="mb-editor-border-right-width" data-attribute="border" value="0">
				<input type="text" name="mb-editor-border-bottom-width" data-name="mb-editor-border-bottom-width" class="mb-editor-border-bottom-width" data-attribute="border" value="0">
				<input type="text" name="mb-editor-border-left-width" data-name="mb-editor-border-left-width" class="mb-editor-border-left-width" data-attribute="border" value="0">
				<div class="mb-editor-padding mb-editor-board-style3">
					<div class="mb-editor-label2">Padding</div>
					<input type="text" name="mb-editor-padding-top" data-name="mb-editor-padding-top" class="mb-editor-padding-top" data-attribute="padding" value="0">
					<input type="text" name="mb-editor-padding-right" data-name="mb-editor-padding-right" class="mb-editor-padding-right" data-attribute="padding" value="0">
					<input type="text" name="mb-editor-padding-bottom" data-name="mb-editor-padding-bottom" class="mb-editor-padding-bottom" data-attribute="padding" value="0">
					<input type="text" name="mb-editor-padding-left" data-name="mb-editor-padding-left" class="mb-editor-padding-left" data-attribute="padding" value="0">
					<div class="mb-editor-board-style2"><div class="mb-editor-board-style1">Text</div></div>
				</div>
			</div>
		</div>';

		$responsive_class1		= 'col-sm-6';
		$responsive_class2		= 'col-sm-6';
		$attr_template		= '	
		<div>
			<div class="'.esc_attr($responsive_class1).' mb-box-attr-border">
				<div class="mb-box-attr-label">Border</div>
				<div class="mb-box-attr-option">
					<select class="mbc_#{box}_border_width" style="min-width:50px !important;width:50px !important;vertical-align:top;" title="Border Width"><option value="0" selected>0px</option><option value="1">1px</option><option value="2">2px</option><option value="3">3px</option><option value="4">4px</option><option value="5">5px</option><option value="6">6px</option><option value="7">7px</option><option value="8">8px</option><option value="9">9px</option><option value="10">10px</option><option value="11">11px</option><option value="12">12px</option><option value="13">13px</option><option value="14">14px</option><option value="15">15px</option><option value="16">16px</option><option value="17">17px</option><option value="18">18px</option><option value="19">19px</option><option value="20">20px</option><option value="30">30px</option><option value="40">40px</option><option value="50">50px</option></select>
					<select class="mbc_#{box}_border_style" style="min-width:70px !important;width:70px !important;margin-left:-5px;" title="Border Style"><option value="Solid">Solid</option>
					<option value="Dotted">Dotted</option><option value="Dashed">Dashed</option><option value="Double">Double</option><option value="Groove">Groove</option><option value="Ridge">Ridge</option><option value="Inset">Inset</option><option value="Outset">Outset</option></select>
					<div class="mbc-colorpicker input-group colorpicker-component" style="display:inline-block;margin-left:-5px;vertical-align:top;"><input type="text" value="" class="mbc_#{box}_border_color" class="form-control" style="width:70px !important;min-width:70px !important;padding:5px 3px;"  title="Border Color"><span class="input-group-addon radius-0"><i style="width:28px;height:28px;"></i></span></div>
				</div>
			</div>
			<div class="'.esc_attr($responsive_class2).'">
				<div class="mb-box-attr-label">Border Radius</div>
				<div class="mb-box-attr-option">
					<select class="mbc_#{box}_border_radius" style="" title="Border Radius"><option value="0" selected="">0px</option><option value="1">1px</option>
					<option value="2">2px</option><option value="3">3px</option><option value="4">4px</option><option value="5">5px</option><option value="10">10px</option><option value="15">15px</option><option value="20">20px</option><option value="25">25px</option><option value="30">30px</option><option value="35">35px</option></select>
				</div>
			</div>
			<div class="'.esc_attr($responsive_class1).'">
				<div class="mb-box-attr-label">Padding</div>
				<div class="mb-box-attr-option">
					<select class="mbc_#{box}_padding" style="" title="Padding"><option value="0" selected>0px</option><option value="1">1px</option><option value="2">2px</option><option value="3">3px</option><option value="4">4px</option><option value="5">5px</option><option value="6">6px</option><option value="7">7px</option><option value="8">8px</option><option value="9">9px</option><option value="10">10px</option><option value="11">11px</option><option value="12">12px</option><option value="13">13px</option><option value="14">14px</option><option value="15">15px</option><option value="16">16px</option><option value="17">17px</option><option value="18">18px</option><option value="19">19px</option><option value="20">20px</option><option value="30">30px</option><option value="40">40px</option><option value="50">50px</option></select>
				</div>
			</div>
			<div class="'.esc_attr($responsive_class2).' mb-box-attr-bgcolor">
				<div class="mb-box-attr-label">BG Color</div>
				<div class="mb-box-attr-option">
					<div class="mbc-colorpicker2 input-group colorpicker-component" style=""><input type="text" value="" class="mbc_#{box}_background_color" class="form-control" style="padding:5px 3px;" title="Background Color"><span class="input-group-addon radius-0"><i style="width:28px;height:28px;"></i></span></div>
				</div>
			</div>
			<div class="'.esc_attr($responsive_class1).'">
				<div class="mb-box-attr-label">Style</div>
				<div class="mb-box-attr-option">
					<input style="" class="mbc_#{box}_style" value="" type="text" title="CSS Style">
				</div>
			</div>			
			<div class="'.esc_attr($responsive_class2).'">
				<div class="mb-box-attr-label">Class</div>
				<div class="mb-box-attr-option">
					<input style="" class="mbc_#{box}_class" value="" type="text" title="CSS Class">
				</div>
			</div>
			<div class="clear"></div>
		</div>';
	
		if($type=="smart"){
			$style		= 'border:1px solid #b5b5b5;margin-top:-7px !important';
		}else{
			$style		= "";
		}

		$template_start	= '<div class="mb-editor-composer mb-editor-composer-'.esc_attr($editor_id).'" style="display:none;'.esc_attr($style).'">';
			$template_start	.= '<input type="hidden" name="mb-editor-composer-id" value="'.esc_attr($editor_id).'" />';

			$template_start	.= '<div class="mb-editor-attr-box" style="display:none;">';
				$template_start	.= '<div class="mb-editor-composer-tabs">';
					$template_start	.= '<div class="mp-editor-tabs-item mp-editor-tabs-selected" onclick="mb_setEditorTabs(\''.esc_js($editor_id).'\',\'mb-editor-composer-tabs\',1,\'mb-editor-composer-tab1\')"><div class="mp-editor-tabs-item-box"><span class="mb-style3-tabbg-left"></span><span class="mb-style3-tabbg-center">Text Box</span><span class="mb-style3-tabbg-right mp-tab-arrow-right"></span><span class="clear"></span></div></div>';
					$template_start	.= '<div class="mp-editor-tabs-item" onclick="mb_setEditorTabs(\''.esc_js($editor_id).'\',\'mb-editor-composer-tabs\',2,\'mb-editor-composer-tab1\')"><div class="mp-editor-tabs-item-box"><span class="mb-style3-tabbg-left"></span><span class="mb-style3-tabbg-center">Responsive Box</span><span class="mb-style3-tabbg-right mp-tab-arrow-right"></span><span class="clear"></span></div></div>';
					$template_start	.= '<div class="mp-editor-tabs-item" onclick="mb_setEditorTabs(\''.esc_js($editor_id).'\',\'mb-editor-composer-tabs\',3,\'mb-editor-composer-tab1\')"><div class="mp-editor-tabs-item-box"><span class="mb-style3-tabbg-left"></span><span class="mb-style3-tabbg-center">Wrap Box</span><span class="mb-style3-tabbg-right mp-tab-arrow-right"></span><span class="clear"></span></div></div>';
				$template_start	.= '</div>';

				$desc		= __MM("MSG_ECOMPOSER_DOTTED_DESC");
				
				$template_start	.= '<div class="mb-editor-composer-tab1 mb-editor-composer-tab11">';		//Inner box Start
					$template_start	.= '<div class="col-sm-4"><div class="mb-inner-box mb-editor-style-box">'.$box_template.'</div></div>';
					$template_start	.= '<div class="col-sm-8"><div class="mb-editor-composer-tab-title">'.__MM("MSG_ECOMPOSER_INNER_BOX_DESC").'</div><div class="mb-editor-composer-tab-desc">'.$desc.'</div>'.str_replace('#{box}','inner',$attr_template).'</div>';
					$template_start	.= '<div class="clear"></div>';
				$template_start	.= '</div>';	//Inner box End
				$template_start	.= '<div style="display:none;" class="mb-editor-composer-tab1 mb-editor-composer-tab12">';			//Outer box Start
					$template_start	.= '<div class="col-sm-4"><div class="mb-outer-box mb-editor-style-box">'.$box_template.'</div></div>';
					$template_start	.= '<div class="col-sm-8"><div class="mb-editor-composer-tab-title">'.__MM("MSG_ECOMPOSER_OUTER_BOX_DESC").'</div><div class="mb-editor-composer-tab-desc">'.$desc.'</div>'.str_replace('#{box}','outer',$attr_template).'</div>';
					$template_start	.= '<div class="clear"></div>';
				$template_start	.= '</div>';			//Outer box End
				$template_start	.= '<div style="display:none;" class="mb-editor-composer-tab1 mb-editor-composer-tab13">';					//Wrap box Start
					$template_start	.= '<div class="col-sm-4"><div class="mb-wrap-box mb-editor-style-box">'.$box_template.'</div></div>';
					$template_start	.= '<div class="col-sm-8"><div class="mb-editor-composer-tab-title">'.__MM("MSG_ECOMPOSER_WRAP_BOX_DESC").'</div><div class="mb-editor-composer-tab-desc">'.$desc.'</div>'.str_replace('#{box}','wrap',$attr_template).'</div>';
					$template_start	.= '<div class="clear"></div>';
				$template_start	.= '</div>';			//Wrap box End
			$template_start	.= '</div>';

			$template_start	.= '<div class="mb-layout-btn-box">';				

				$template_start	.= '<div class="mb-editor-video-box mb-editor-video-'.esc_attr($editor_id).'">';
					$template_start	.= '<input type="hidden" name="video_input_error1" value="'.__MM("MSG_VIDEO_INSERT_ERROR").'" />';
					$template_start	.= '<div class="mb-editor-video-header">'.__MW("W_VIDEO_INSERT").'</div>';					
					$template_start	.= '<div class="mb-editor-video-body">';
						$template_start	.= '<div  class="mb-editor-video-input-label"><span>'.__MW("W_VIDEO").' URL</span></div>';
						$template_start	.= '<textarea name="mb_editor_video_url" class="mb_editor_video_url" placeholder="'.__MM("MSG_VIDEO_INSERT_ERROR").'"></textarea>';
						$template_start	.= '<div class="btn-box-center">';
							$template_start	.= '<button onclick="mb_insertEditorVideo(\''.esc_js($editor_id).'\');return false;" type="button" class="btn btn-default margin-right-10"><span>'.$mb_words["OK"].'</span></button>';
							$template_start	.= '<button onclick="mb_hideEditorVideoBox(\''.esc_js($editor_id).'\');return false;" type="button" class="btn btn-default"><span>'.$mb_words["Cancel"].'</span></button>';
						$template_start	.= '</div>';
					$template_start	.= '</div>';
				$template_start	.= '</div>';

				$template_start	.= '<div class="mb-layout-option-box">';

					$template_start	.= '<div onclick="checkCSSDisplay(\'.mb-editor-video-'.esc_js($editor_id).'\');return false;" class="mbc_editor_video cursor_pointer" title="'.__MW("W_VIDEO_INSERT").'"><img src="'.MBW_PLUGIN_URL.'plugins/editor_composer/img/icon_editor_video.png" style="width:13px;margin: 3px 0 3px !important;vertical-align: top !important;"> '.__MW("W_VIDEO").'</div>';

					$template_start	.= '<span class="mb-editor-composer-vertical-bar1"></span>';

					$template_start	.= '<select title="'.__MM("MSG_ECOMPOSER_MAX_WIDTH_TITLE").'" class="mbc_editor_max_width" style="min-width:70px !important;"><option value="100%" selected>'.__MW("W_MAX_WIDTH").'</option><option value="100%">100%</option><option value="90%">90%</option><option value="80%">80%</option><option value="70%">70%</option><option value="900px">900px</option><option value="800px">800px</option><option value="700px">700px</option><option value="600px">600px</option><option value="500px">500px</option><option value="400px">400px</option><option value="300px">300px</option><option value="200px">200px</option><option value="100px">100px</option></select>';
					$template_start	.= '<select title="'.__MM("MSG_ECOMPOSER_ROW_COUNT_TITLE").'" class="mbc_editor_line_count" style="min-width:60px !important;"><option value="1" selected>'.__MW("W_ROW_COUNT").'</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="30">30</option><option value="40">40</option><option value="50">50</option><option value="60">60</option><option value="70">70</option><option value="80">80</option><option value="90">90</option><option value="100">100</option><option value="150">150</option><option value="200">200</option><option value="300">300</option><option value="500">500</option></select>';
					$template_start	.= '<select title="'.__MM("MSG_ECOMPOSER_ALIGN_TITLE").'" class="mbc_editor_text_align" style="min-width:62px !important;"><option value="" selected>'.__MW("W_ALIGN").'</option><option value="left">Left</option><option value="center">Center</option><option value="right">Right</option></select>';	
					$template_start	.= '<span class="mb-editor-composer-vertical-bar2"></span>';
				$template_start	.= '</div>';

				$insert_layout		= __MM("MSG_ECOMPOSER_INSERT_LAYOUT")."";


				$template_start	.= '<div onclick="mb_insertEditorLayout(\''.esc_js($editor_id).'\',\'L25-75\');return false;" class="mb-layout-icon" title="Desktop(25%,75%), Tablet(25%,75%), Mobile(100%) '.esc_attr($insert_layout).'"><img src="'.MBW_PLUGIN_URL.'plugins/editor_composer/img/25-75.png"></div>';
				$template_start	.= '<div onclick="mb_insertEditorLayout(\''.esc_js($editor_id).'\',\'L30-70\');return false;" class="mb-layout-icon" title="Desktop(30%,70%), Tablet(30%,70%), Mobile(100%) '.esc_attr($insert_layout).'"><img src="'.MBW_PLUGIN_URL.'plugins/editor_composer/img/30-70.png"></div>';
				$template_start	.= '<div onclick="mb_insertEditorLayout(\''.esc_js($editor_id).'\',\'L33-67\');return false;" class="mb-layout-icon" title="Desktop(33%,67%), Tablet(33%,67%), Mobile(100%) '.esc_attr($insert_layout).'"><img src="'.MBW_PLUGIN_URL.'plugins/editor_composer/img/33-67.png"></div>';
				$template_start	.= '<div onclick="mb_insertEditorLayout(\''.esc_js($editor_id).'\',\'L67-33\');return false;" class="mb-layout-icon" title="Desktop(67%,33%), Tablet(67%,33%), Mobile(100%) '.esc_attr($insert_layout).'"><img src="'.MBW_PLUGIN_URL.'plugins/editor_composer/img/67-33.png"></div>';
				$template_start	.= '<div onclick="mb_insertEditorLayout(\''.esc_js($editor_id).'\',\'L70-30\');return false;" class="mb-layout-icon" title="Desktop(70%,30%), Tablet(70%,30%), Mobile(100%) '.esc_attr($insert_layout).'"><img src="'.MBW_PLUGIN_URL.'plugins/editor_composer/img/70-30.png"></div>';
				$template_start	.= '<div onclick="mb_insertEditorLayout(\''.esc_js($editor_id).'\',\'L75-25\');return false;" class="mb-layout-icon" title="Desktop(75%,25%), Tablet(75%,25%), Mobile(100%) '.esc_attr($insert_layout).'"><img src="'.MBW_PLUGIN_URL.'plugins/editor_composer/img/75-25.png"></div>';

				//$template_start	.= '<div onclick="mb_insertEditorLayout(\''.esc_js($editor_id).'\',\'col-111\');return false;" class="mb-layout-icon" title="Desktop(100%), Tablet(100%), Mobile(100%) '.esc_attr($insert_layout).'"><img src="'.MBW_PLUGIN_URL.'plugins/editor_composer/img/111.png"></div>';
				$template_start	.= '<div onclick="mb_insertEditorLayout(\''.esc_js($editor_id).'\',\'col-221\');return false;" class="mb-layout-icon" title="Desktop(50%,50%), Tablet(50%,50%), Mobile(100%) '.esc_attr($insert_layout).'"><img src="'.MBW_PLUGIN_URL.'plugins/editor_composer/img/221.png"></div>';
				//$template_start	.= '<div onclick="mb_insertEditorLayout(\''.esc_js($editor_id).'\',\'col-222\');return false;" class="mb-layout-icon" title="Desktop(50%,50%), Tablet(50%,50%), Mobile(50%,50%) '.esc_attr($insert_layout).'"><img src="'.MBW_PLUGIN_URL.'plugins/editor_composer/img/222.png"></div>';
				$template_start	.= '<div onclick="mb_insertEditorLayout(\''.esc_js($editor_id).'\',\'col-321\');return false;" class="mb-layout-icon" title="Desktop(33%,33%,33%), Tablet(50%,50%), Mobile(100%) '.esc_attr($insert_layout).'"><img src="'.MBW_PLUGIN_URL.'plugins/editor_composer/img/321.png"></div>';
				$template_start	.= '<div onclick="mb_insertEditorLayout(\''.esc_js($editor_id).'\',\'M331\');return false;" class="mb-layout-icon" title="Desktop(30%,30%,30%), Tablet(30%,30%,30%), Mobile(100%) '.esc_attr($insert_layout).'"><img src="'.MBW_PLUGIN_URL.'plugins/editor_composer/img/m331.png"></div>';
				$template_start	.= '<div onclick="mb_insertEditorLayout(\''.esc_js($editor_id).'\',\'col-421\');return false;" class="mb-layout-icon" title="Desktop(25%,25%,25%,25%), Tablet(50%,50%), Mobile(100%) '.esc_attr($insert_layout).'"><img src="'.MBW_PLUGIN_URL.'plugins/editor_composer/img/421.png"></div>';
				$template_start	.= '<div onclick="mb_insertEditorLayout(\''.esc_js($editor_id).'\',\'col-432\');return false;" class="mb-layout-icon" title="Desktop(25%,25%,25%,25%), Tablet(33%,33%,33%), Mobile(50%,50%) '.esc_attr($insert_layout).'"><img src="'.MBW_PLUGIN_URL.'plugins/editor_composer/img/432.png"></div>';
				$template_start	.= '<div onclick="mb_insertEditorLayout(\''.esc_js($editor_id).'\',\'col-532\');return false;" class="mb-layout-icon" title="Desktop(20%,20%,20%,20%,20%), Tablet(33%,33%,33%), Mobile(50%,50%) '.esc_attr($insert_layout).'"><img src="'.MBW_PLUGIN_URL.'plugins/editor_composer/img/532.png"></div>';
				$template_start	.= '<div onclick="mb_insertEditorLayout(\''.esc_js($editor_id).'\',\'col-543\');return false;" class="mb-layout-icon" title="Desktop(20%,20%,20%,20%,20%), Tablet(25%,25%,25%,25%), Mobile(33%,33%,33%) '.esc_attr($insert_layout).'"><img src="'.MBW_PLUGIN_URL.'plugins/editor_composer/img/543.png"></div>';
				$template_start	.= '<div class="mb-editor-btn-box">';
					$template_start	.= '<a class="mb-editor-hide-btn cursor_pointer" href="javascript:;" onclick="mb_showEditorAttrBox(\''.esc_js($editor_id).'\');return false;" title="'.__MM("MSG_ECOMPOSER_EXPAND_PANELS").'"><img src="'.MBW_PLUGIN_URL.'plugins/editor_composer/img/icon_arrow_down.png" style="width:20px;height:20px;"></a>';
					$template_start	.= '<a class="mb-editor-show-btn cursor_pointer" style="display:none;" href="javascript:;" onclick="mb_hideEditorAttrBox(\''.esc_js($editor_id).'\');return false;" title="'.__MM("MSG_ECOMPOSER_COLLAPSE_ICONS").'"><img src="'.MBW_PLUGIN_URL.'plugins/editor_composer/img/icon_arrow_up.png" style="width:20px;height:20px;"></a>';
				$template_start	.= '</div>';
			$template_start	.= '</div>';
		$template_start	.= '</div>';
		echo  $template_start;
	}
}
?>