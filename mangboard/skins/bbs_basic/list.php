<?php
	$model_data			= mbw_get_model("list");			
	$list_model			= mbw_json_decode($model_data);
	$list_data				= mbw_get_list_setup_data($list_model);

	$select_query		= mbw_get_add_query(array("column"=>"*"), "where", "order")." limit ".$list_data["page_start"].", ".$list_data["page_size"];
	$board_items		= mbw_get_board_items_query($select_query);
?>
<script type="text/javascript">
function selectTabMenu(obj,category,name,idx){
	if(typeof(idx)==="undefined" || idx=="") idx	= "1";
	jQuery('.tab-menu-on').removeClass("tab-menu-on").addClass("tab-menu-off");
	jQuery(obj).removeClass("tab-menu-off").addClass("tab-menu-on");
	if(jQuery("input[name=category"+idx+"]")) jQuery("input[name=category"+idx+"]").val(category);
	sendListTemplateData({"board_name":name,"category":category,"idx":idx});
}
function sendSearchData(data){
	var search_url					= mb_urls["search"];	
	var params						= jQuery('#'+mb_options["board_name"]+'_form_board_search').serialize();
	if(typeof(data)!=='undefined') params					= params+"&"+data;		
	if(params.indexOf('category')!=-1){
		if(search_url.indexOf('category')!=-1){
			search_url		= search_url.replace(/(category)(\d{1})=/g,"category_old$2=");
		}
	}
	search_url						= search_url+"&"+params;		
	moveURL(search_url);
}
<?php
if(intval(mbw_get_board_option("fn_use_list_title"))==1 && mbw_get_vars("device_type")!="mobile" || mbw_get_board_option("fn_board_type")=="admin" || intval(mbw_get_board_option("fn_use_list_title"))==2){
?>
jQuery(document).ready(function(){	
	var tbl = jQuery("#tbl_board_list");
	jQuery("th :checkbox", tbl).first().click(function(){		
		if(jQuery(this).is(":checked")){
			jQuery("td :checkbox", tbl).prop("checked", true);
		}else{
			jQuery("td :checkbox", tbl).prop("checked", false);
		}	
		jQuery(":checkbox", tbl).trigger("change");
	});
});
<?php
}
?>
function showDeleteConfirm(){	
	var check_count	= jQuery(".mb-board input[name='check_array[]']").filter(":checked").length;
	if(check_count > 0) {
		showConfirmPopup(check_count+"<?php echo __MM('MSG_MULTI_DELETE_CONFIRM');?>", {"board_action":"multi_delete"}, sendBoardListData);
	}else{
		showAlertPopup({"code":"1000","message":"<?php echo __MM('MSG_DELETE_SELECT_EMPTY');?>"});
	}
}
function showMoveConfirm(type){	
	var check_count	= jQuery(".mb-board input[name='check_array[]']").filter(":checked").length;
	if(check_count > 0) {
		if(type=="multi_copy"){
			showConfirmPopup(check_count+"<?php echo __MM('MSG_MULTI_COPY_CONFIRM');?>", {"board_action":type}, sendBoardListData);
		}else if(type=="multi_move"){
			showConfirmPopup(check_count+"<?php echo __MM('MSG_MULTI_MOVE_CONFIRM');?>", {"board_action":type}, sendBoardListData);
		}		
	}else{
		if(type=="multi_copy"){
			showAlertPopup({"code":"1000","message":"<?php echo __MM('MSG_COPY_SELECT_EMPTY');?>"});
		}else if(type=="multi_move"){
			showAlertPopup({"code":"1000","message":"<?php echo __MM('MSG_MOVE_SELECT_EMPTY');?>"});
		}
	}
}
function sendBoardListData(args){	
	if(args.board_action=="multi_modify"){
		jQuery('#'+mb_options["board_name"]+'_form_board_list input[name=board_pid]').val(args.board_pid);
	}
	jQuery('#'+mb_options["board_name"]+'_form_board_list input[name=board_action]').val(args.board_action);
	sendFormDataRequest(jQuery('#'+mb_options["board_name"]+'_form_board_list'), mb_urls["board_api"], sendBoardListDataHandler);
}
function sendBoardListDataHandler(response, state)
{
	if(response.state == "success"){
		if(typeof(response.message)!=='undefined'){
			 if(response.message!="") alert(response.message);
		}
		moveURL("reload");
	}else{			
		showAlertPopup(response);
	}
}
</script>
<div class="mb-style1 board-list">

	<form name="<?php echo esc_attr($mb_board_name);?>_form_board_search" id="<?php echo esc_attr($mb_board_name);?>_form_board_search" method="post">
	<input type="hidden" name="board_name" value="<?php echo esc_attr($mb_board_name);?>" />
	<?php if(!empty($args["post_id"])){ ?>
	<input type="hidden" name="page_id" value="<?php echo esc_attr($args["post_id"]);?>" />
	<?php }else if(!empty($_REQUEST["page_id"])){ ?>
	<input type="hidden" name="page_id" value="<?php echo esc_attr(mbw_get_param("page_id"));?>" />
	<?php } if(!empty($_REQUEST["order_by"])){ ?> 
	<input type="hidden" name="order_by" value="<?php echo esc_attr(mbw_get_param("order_by"));?>" />
	<?php } if(!empty($_REQUEST["order_type"])){ ?> 
	<input type="hidden" name="order_type" value="<?php echo esc_attr(mbw_get_param("order_type"));?>" />
	<?php } ?>
	<?php do_action('mbw_board_skin_search'); ?>
	<div class="list-head">
	
		<?php
		echo '<div class="mb-category">';			
			echo mbw_get_category_template(mbw_get_board_option("fn_category_type"),mbw_get_board_option("fn_category_data"));
		echo '</div>';	
		?>
	
		<?php if(intval(mbw_get_board_option("fn_use_list_search"))==1){ ?>
		<div class="list-search">
			<select id="search_field" name="search_field" class="search-field margin-right-5" title="<?php echo __MM("W_SEARCH_FIELD");?>"><?php echo $list_data["search"];?></select><input type="text" id="search_text" class="search-text" name="search_text" accesskey="s" title="<?php echo __MM("W_SEARCH_TEXT");?>" value="<?php echo mbw_htmlspecialchars(mbw_get_param("search_text"));?>" onkeypress="checkEnterKey(sendSearchData);"/><input style="display:none !important;" type="text" title="search"/><?php echo mbw_get_btn_template(array("name"=>"Search","onclick"=>"sendSearchData()","class"=>"btn btn-default btn-search margin-left-5")); ?>
		</div>
		<?php } ?>
		<div class="clear"></div>
	</div>
	</form>

	<?php do_action('mbw_board_skin_header'); ?>	
	<form name="<?php echo esc_attr($mb_board_name);?>_form_board_list" id="<?php echo esc_attr($mb_board_name);?>_form_board_list" method="post">	
	<input type="hidden" name="board_name" id="board_name" value="<?php echo esc_attr($mb_board_name);?>" />
	<input type="hidden" name="page_id" id="page_id" value="<?php echo esc_attr(mbw_get_param("page_id"));?>" />
	<input type="hidden" name="list_type" id="list_type" value="<?php echo esc_attr(mbw_get_param("list_type"));?>" />
	<input type="hidden" name="page" id="page" value="<?php echo esc_attr(mbw_get_param("page"));?>" />
	<input type="hidden" name="mode" id="mode" value="list" />
	<input type="hidden" name="board_action" id="board_action" value="" />
	<input type="hidden" name="board_pid" id="board_pid" value="" />
	<?php echo mbw_create_nonce("form"); ?>

	<div class="main-style1" id="<?php echo esc_attr($mb_board_name);?>_board_box">

		<table cellspacing="0" cellpadding="0" border="0" id="tbl_board_list" class="table table-list">
		<caption><?php echo get_the_title();?></caption>
			
			<colgroup><?php echo $list_data["width"];?></colgroup>
			<?php
				if(intval(mbw_get_board_option("fn_use_list_title"))==1 && mbw_get_vars("device_type")!="mobile" || mbw_get_board_option("fn_board_type")=="admin" || intval(mbw_get_board_option("fn_use_list_title"))==2){
					echo "<thead><tr>".$list_data["title"]."</tr></thead>";
				}
			?>

			<tbody id="<?php echo esc_attr($mb_board_name);?>_board_body">
			<?php			
			if($list_data["total_count"] > 0){
				$list_index		= (intval(mbw_get_board_option("fn_page_size"))*(intval(mbw_get_param("board_page")-1)))+1;
				$contentbox	= false;
				if(strpos($model_data,'"onclick":"openContents(this,\'\',')!==false){
					$contentbox			= true;
				}
				if(!empty($board_items)){
					foreach($board_items as $item){
						mbw_set_board_item($item);
						
						if(intval(mbw_get_board_item("fn_is_notice"))==1){
							$item_class		= ' class="mb-notice"';
						}else{
							$item_class		= "";
						}
						echo '<tr id="'.mbw_get_id_prefix()."tr_".esc_attr($list_index).'"'.$item_class.'>';
						foreach($list_model as $data){							
							if(mbw_check_item($data)) mbw_get_list_template($data);
						}
						echo '</tr>';

						if($contentbox){
							echo '<tr class="mb-open-box" style="display: none;"><td colspan="'.esc_attr($list_data["cols"]).'"><div class="mb-open-slide" style="display: none;"><div class="mb-open-content">';
							echo mbw_get_board_item("fn_content");
							echo '</div></div></td></tr>';
						}
						//아래 주석 제거하면 목록에서 콘텐츠 내용도 보이도록 표시함
						//echo '<tr><td colspan="'.esc_attr($list_data["cols"]).'">'.mbw_get_board_item("fn_content").'</td></tr>';
						$list_index++;
					}
				}
			}else{
				echo '<tr><td colspan="'.esc_attr($list_data["cols"]).'" align="center" style="text-align:center;">'.__MM("MSG_LIST_ITEM_EMPTY").'</td></tr>';
			}
			?>
			</tbody>

		</table>
	</div>
	<?php do_action('mbw_board_skin_form'); ?>


	<?php if(mbw_get_option("use_list_button")){ ?>
	<div class="list-btn">
		<div class="btn-box-right" id="<?php echo esc_attr($mb_board_name);?>_btn_box">
			<?php				
				echo '<div class="btn-box-left" style="float:left;">';				
				if(mbw_get_param("search_text")!=""){
					echo mbw_get_btn_template(array("name"=>"Back","href"=>mbw_get_url(array("board_pid"=>"","mode"=>"list","page_size"=>"","search_text"=>"")),"class"=>"btn btn-default btn-list"));
				}
				echo mbw_get_left_button("list");
				echo '</div>';
				if(intval(mbw_get_board_option("fn_delete_level")) <= $mb_user_level)
					echo mbw_get_btn_template(array("name"=>"Delete","onclick"=>"showDeleteConfirm()","class"=>"btn btn-default btn-delete"));

				if(intval(mbw_get_board_option("fn_write_level"))==1 || intval(mbw_get_board_option("fn_write_level")) <= $mb_user_level)
					echo mbw_get_btn_template(array("name"=>"Write","type"=>"button","href"=>mbw_get_url(array("board_pid"=>"","mode"=>"write","board_action"=>"write")),"class"=>"btn btn-default btn-write"));

				echo mbw_get_right_button("list");
				echo '<div class="clear"></div>';
			?>
		</div>
	</div>
	<?php } ?>


	</form>
	<?php do_action('mbw_board_skin_footer'); ?>
	
	<?php
	if(mbw_get_option("use_list_pagination")){
		if($list_data["total_count"] > 0){
			echo '<div id="'.esc_attr($mb_board_name).'_pagination_box" class="pagination-box">'.mbw_get_pagination_template(array("total_count"=>$list_data["total_count"])).'</div>';
			//AJAX 방식으로 URL 이동없이 페이지 전환하려면 아래 코드 사용
			//echo '<div id="'.esc_attr($mb_board_name).'_pagination_box" class="pagination_box">'.mbw_get_pagination_template(array("total_count"=>$list_data["total_count"],"page_type"=>"ajax")).'</div>';
		}			
	}
	?>
</div>