<?php
if(!function_exists('mbw_get_calendar_template')){
	function mbw_get_calendar_template($mode,$date,$field="fn_calendar_date"){ 
		global $mdb,$mstore;
		global $mb_board_table_name,$mb_fields;

		$date_ym		= mbw_value_filter($date,"date1");
		$box_size		= 7;
		if(mbw_get_vars("device_type")=="mobile") $box_size		= 2;
		
		if(empty($date_ym)) $date_ym	= date("Y-m",mbw_get_timestamp());
		if(strlen($date_ym)>7) $date_ym		= substr($date_ym,0,7);

		$date_field				= $mb_fields["select_board"][$field];
		$where_query			= $mdb->prepare(" (".$date_field.">=%s AND ".$date_field."<DATE_ADD(%s, INTERVAL 1 MONTH))",$date_ym."-01",$date_ym."-01");
		$add_where_query		= mbw_get_add_query("where");

		if(!empty($add_where_query)){
			$where_query			= $add_where_query." and".$where_query;
		}else $where_query		= " WHERE".$where_query;

		$order_query				= mbw_get_add_query("order");
		$order_query				= str_replace('ORDER BY ','ORDER BY '.$date_field." asc,",$order_query);
		//$order_query				= " order by ".$date_field." asc,".$mb_fields["select_board"]["fn_gid"]." asc,".$mb_fields["select_board"]["fn_reply"]." asc";
		mbw_set_board_items_query("select * from ".$mb_board_table_name.$where_query.$order_query);
		
		$calendar_data							= array();
		$select_date								= explode("-",$date_ym);
		$today_date								= date("Y-m-d",mbw_get_timestamp());

		$calendar_data["year"]					= $select_date[0];
		$calendar_data["month"]				= $select_date[1];
		$calendar_data["prev_year"]			= date("Y-m",mktime(0,0,0,$calendar_data["month"],1,intval($calendar_data["year"]-1)));
		$calendar_data["prev_month"]		= date("Y-m",mktime(0,0,0,intval($calendar_data["month"])-1,1,$calendar_data["year"]));
		$calendar_data["next_year"]			= date("Y-m",mktime(0,0,0,$calendar_data["month"],1,intval($calendar_data["year"]+1)));
		$calendar_data["next_month"]		= date("Y-m",mktime(0,0,0,intval($calendar_data["month"])+1,1,$calendar_data["year"]));

		$select_t				= date("t",mktime(0,0,0,$calendar_data["month"],1,$calendar_data["year"]));
		$select_n				= date("N",mktime(0,0,0,$calendar_data["month"],1,$calendar_data["year"]));
		
		$template_start		= "";
		$empty_check		= $select_n%7;
		$mb_locale			= mbw_get_option("locale");
		if($mb_locale=='ko_KR'){
			$week_array		= array("토","일","월","화","수","목","금");
		}else{
			$week_array		= array("Sat","Sun","Mon","Tue","Wed","Thu","Fri");
		}
		if($box_size==7){
			$empty_count			= $select_n%7;			
			$week_size				= ceil(($select_t+$empty_count)/7)-1;
			$template_start	.= '<colgroup><col style="width:15%"><col style="width:14%"><col style="width:14%"><col style="width:14%"><col style="width:14%"><col style="width:14%"><col style="width:15%"></colgroup>';
			$template_start	.= '<thead><tr>';
			$template_start	.= '<th class="sunday" scope="col">'.esc_html($week_array[1]).'</th><th scope="col">'.esc_html($week_array[2]).'</th><th scope="col">'.esc_html($week_array[3]).'</th><th scope="col">'.esc_html($week_array[4]).'</th><th scope="col">'.esc_html($week_array[5]).'</th><th scope="col">'.esc_html($week_array[6]).'</th><th class="saturday" scope="col">'.esc_html($week_array[0]).'</th>';
			$template_start	.= '</tr></thead>';
		}else{
			$empty_count			= 0;
			$week_size				= ceil($select_t/$box_size);
		}		

		$template_calendar	= '<tbody>';
		$write_url			= mbw_get_url(array("board_pid"=>"","mode"=>"write","board_action"=>"write","calendar_date"=>""));

		//캘린더 모델 설정
		$list_model	= mbw_get_model("list_calendar");
		if(empty($list_model)) $list_model		= '{"field":"fn_title","name":"Title","width":"","type":"title","maxlength":"8","maxtext":"..","td_class":"text-left"}';
		$list_data			= mbw_json_decode($list_model);

		$write_level			= intval(mbw_get_board_option("fn_write_level"));
		$user_level			= intval(mbw_get_user("fn_user_level"));

		for($i=0;$i<=$week_size;$i++){
			$template_calendar	.= '<tr>';
				for($j=1;$j<=$box_size;$j++){
					$item_date					= ($box_size*$i+$j)-$empty_count;

					if($item_date<=0 || $item_date>$select_t){ 
						if($box_size==7) $template_calendar	.= '<td><div></div></td>';
					}else{
						if(intval($item_date)<10)
							$date_ymd					= $date_ym."-0".$item_date;
						else
							$date_ymd					= $date_ym."-".$item_date;

						if($today_date==$date_ymd){
							$template_calendar	.= '<td class="i-today"><div>';
						}else{
							$template_calendar	.= '<td><div>';
						}

						$week_index		= ($empty_check+$item_date)%7;
						if($week_index==1){
							$box_class		= ' class="i-date sunday"';
						}else if($week_index==0){
							$box_class		= ' class="i-date saturday"';
						}else{
							$box_class		= ' class="i-date"';
						}
						$date_text		= $item_date;
						$add_style		= "";
						if($box_size!=7){
							$add_style	= ' style="width:auto !important;float:left;"';
							$date_text	.= ' <span>('.esc_html($week_array[$week_index]).')</span>';
						}
						if($write_level<=$user_level){
							$date_text	= '<a href="'.esc_url($write_url.'&calendar_date='.$date_ymd).'">'.($date_text).'</a>';
						}
						$template_calendar	.= '<div'.$box_class.$add_style.'>';
							$template_calendar	.= $date_text;
						$template_calendar	.= '</div><div class="clear"></div>';

						$board_items				= mbw_get_board_items();
						if(!empty($board_items)){
							$item_check		= true;
							mbw_set_vars("calendar_date_ymd",$date_ymd);
							foreach($board_items as $item){
								mbw_set_board_item($item);
								if(strpos($item[$date_field], $date_ymd) !== false){
									$item_check		= false;
									$template_calendar	.= '<div class="i-view">';
									foreach($list_data as $data){
										if(mbw_check_item($data)){
											$list_item							= mbw_get_list_template($data,array("type"=>"span"),false);
											$template_calendar				= $template_calendar.$list_item;
										}
									}
									$template_calendar	.= '</div>';
								}
							}
							if($item_check) $template_calendar	.= '<div class="i-empty"></div>';
						}
						$template_calendar	.= '</div></td>';
					}					
				}
			$template_calendar	.= '</tr>';			
		}
		$template_calendar	.= '</tbody>';

		$calendar_data["start"]				= '<table cellspacing="0" cellpadding="0" border="0" class="table table-list">';
		$calendar_data["thead"]			= $template_start;
		$calendar_data["tbody"]			= $template_calendar;
		$calendar_data["end"]				= '</table>';
		return $calendar_data;
	}
}
?>