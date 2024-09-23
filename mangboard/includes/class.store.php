<?php
Class MStore
{
	private $db;
	public $params					= array("page"=>1);

	public $board_items;
	public $comment_items;	
	public $board_item;
	public $board_json;
	public $comment_item;
	public $comment_fields;

	public $board_options;
	public $board_models			= array();
	public $board_type				= array();
	public $board_fields;
	public $board_option_fields;	
	public $board_files;	

	public $board_name			= "";
	public $model_key				= "";
	public $list_type					= "list";	
	public $options					= array();
	public $formats					= array();
	public $editors					= array();	
	public $messages				= array();

	public $cookies					= array();
	public $filter_items				= array();
	public $pattern_items			= array();
	public $button_left_items		= array();
	public $button_right_items		= array();
	public $category_fields			= array();

	public $current_time;
	public $templates;

	public $where_data				= array();
	public $where_query_data		= array();
	public $where_unset_data		= array();
	public $order_data				= array();
	public $order_query			= "";
	public $board_pid				= 0;
	public $file_index				= 0;
	public $item_index				= 0;
	public $template_index			= 0;	
	
	public $user						= array("user_id"=>"guest","user_name"=>"Guest");	
	public $user_fields;
	public $user_login				= false;
	public $auth_secure			= false;
	
	public $result_data				= array("state"=>"ready","code"=>"0","message"=>"","mode"=>"","board_action"=>"","html"=>"","script"=>"","data"=>"","count"=>"","total_count"=>"","target_name"=>"");
	
	public function __construct($db=NULL,$options=array()){		
		global $mb_fields;
		global $mb_options;

		if(!empty($db)){
			$this->db = $db;
		}
		$this->set_user_fields($mb_fields["users"]);
		$this->set_board_option_fields($mb_fields["board_options"]);	
		$this->set_options($options);
		$this->set_db_options("setup");
		$this->set_auth_secure();
	}
	
	public function set_board_name($name){
		$name	= mbw_value_filter($name,"name");
		$this->board_name			= $name;
	}
	public function get_board_name(){
		return $this->board_name;
	}
	public function init_board($name,$is_reset=false){
		$name	= mbw_value_filter($name,"name");
		$this->set_board_name($name);
		$this->set_board_options($name,$is_reset);
	}
	public function init_board_panel(){
		$this->file_index		= 0;
	}

	public function set_list_type($type){
		$this->list_type			= $type;
	}
	public function get_list_type(){
		return $this->list_type;
	}

	private function set_auth_secure() {
		if ( isset($_SERVER['HTTPS']) ) {
			if ( 'on' == strtolower($_SERVER['HTTPS'])) {
				$this->auth_secure		= true;return;
			}
			if ( '1' == $_SERVER['HTTPS'] ){
				$this->auth_secure		= true;return;
			}
		} else if ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
			$this->auth_secure		= true;
			return;
		}
		$this->auth_secure		= false;
	}
	public function get_auth_secure(){
		return $this->auth_secure;
	}
	public function check_get_param(){
		if(is_array($this->params)){
			$preg_exception		= array("search_text","stag","search_add_text1","search_add_text2","search_add_text3","redirect_to","category1","category2","category3","stype1","stype2","stype3","se_text1","se_text2","se_text3","se_text4","se_text5","search_name");
			foreach($this->params  as $key => $value){
				if(in_array($key, $preg_exception)===false){
					$this->params[$key]			= mbw_value_filter($value);
				}else{
					//$this->params[$key]			= strip_tags($value);
				}
			}
		}
	}
	public function check_post_param(){
		if(is_array($this->params)){
			$check_param	= array("board_name","board_pid","page","board_page","mode","board_action","order_by","order_type","user_pid","parent_pid","parent_user_pid","page_size","lang","mb_locale","list_type","view_type","write_type","comment_type","page_id","start_price","end_price","template","template_name","idx","step","search_field","se_field1","se_field2","se_field3","se_field4","se_field5","search_add_field1","search_add_field2","search_add_field3","date_field");

			foreach($check_param as $key){
				if(!empty($this->params[$key])){
					$this->params[$key]			= mbw_value_filter($this->params[$key]);
				}
			}			
			$text_param		= array("search_text","stag","search_add_text1","search_add_text2","search_add_text3","redirect_to","category1","category2","category3","stype1","stype2","stype3","se_text1","se_text2","se_text3","se_text4","se_text5","search_name");
			foreach($text_param as $key){
				if(!empty($this->params[$key])){
					if(is_array($this->params[$key])){
						$this->params[$key]		= implode(",",$this->params[$key]);
					}
					$this->params[$key]			= strip_tags($this->params[$key]);
				}
			}
			$date_param		= array("calendar_date","reg_date","modify_date","search_date","start_date","end_date","search_year","search_month","search_day");
			foreach($date_param  as $key){
				if(!empty($this->params[$key])){
					$this->params[$key]			= mbw_value_filter($this->params[$key],"date1");
				}
			}
		}
	}
	public function set_model($key,$value,$device="desktop"){
		$this->board_models[$device][$key]			= $value;		
	}
	public function set_models($params){
		$this->board_models		= array_merge($this->board_models,$params);
	}

	public function get_model($key){
		$model					= "";
		$device_type			= mbw_get_vars("device_type");
		$this->model_key	= $key;

		if(mbw_is_admin_page() && !empty($this->board_models[$device_type][$key."_admin"])){
			$model	= $this->board_models[$device_type][$key."_admin"];
		}else{
			if(!empty($this->board_models[$device_type][$key])){
				$model	= $this->board_models[$device_type][$key];
			}else if(!empty($this->board_models["desktop"][$key])){
				$model	= $this->board_models["desktop"][$key];
			}
		}
		if(has_filter('mf_board_model')) $model			= apply_filters("mf_board_model",$model);
		return $model;
	}
	public function get_model_key(){
		return $this->model_key;
	}
	public function add_left_button($key,$value){
		if(empty($this->button_left_items[$key]))
			$this->button_left_items[$key]			= $value;
		else
			$this->button_left_items[$key]			= $this->button_left_items[$key].$value;
	}
	public function add_left_buttons($params){
		$this->button_left_items		= array_merge($this->button_left_items,$params);
	}

	public function get_left_button($key){
		if(!empty($this->button_left_items[$key])){
			return $this->button_left_items[$key];
		}else{
			return "";
		}
	}

	public function add_right_button($key,$value){
		if(empty($this->button_right_items[$key]))
			$this->button_right_items[$key]			= $value;
		else
			$this->button_right_items[$key]			= $this->button_right_items[$key].$value;
	}
	public function add_right_buttons($params){
		$this->button_right_items		= array_merge($this->button_right_items,$params);
	}

	public function get_right_button($key){
		if(!empty($this->button_right_items[$key])){
			return $this->button_right_items[$key];
		}else{
			return "";
		}
	}

	public function set_category_field($key,$value){
		$this->category_fields[$key]			= $value;
	}
	public function set_category_fields($params){
		$this->category_fields		= array_merge($this->category_fields,$params);
	}
	public function get_category_fields(){
		if(!empty($this->category_fields)){
			return $this->category_fields;
		}else{
			global $mb_fields;			
			if(!empty($mb_fields["select_board"])){
				$select_fields		= $mb_fields["select_board"];
			}else{
				$select_fields		= $mb_fields["board"];
			}
			$fields				= array();
			if(!empty($select_fields["fn_category1"])){
				$fields[]		= "fn_category1";
			}
			if(!empty($select_fields["fn_category2"])){
				$fields[]		= "fn_category2";
			}
			if(!empty($select_fields["fn_category3"])){
				$fields[]		= "fn_category3";
			}
			return $fields;
		}
	}


	public function set_format($key,$value){
		$this->formats[$key]			= $value;
	}
	public function set_formats($params){
		$this->formats		= array_merge($this->formats,$params);
	}

	public function get_format($key){
		if(!empty($this->formats[$key])){
			return $this->formats[$key];
		}else{
			return "";
		}
	}

	public function set_editor($key,$value){
		$this->editors[$key]			= $value;
	}
	public function set_editors($params){
		$this->editors		= array_merge($this->editors,$params);
	}
	public function get_editors(){
		return $this->editors;
	}
	public function get_editor($key){
		if(!empty($this->editors[$key])){
			return $this->editors[$key];
		}else{
			return "";
		}
	}
	public function set_params($params){
		$this->params		= array_merge($this->params,$params);
	}
	public function set_param($key,$value){
		$this->params[$key]			= $value;
	}

	public function get_param($key){
		if(isset($this->params[$key])){
			return $this->params[$key];
		}else{
			return "";
		}
	}

	public function set_pattern($key,$value){
		$this->pattern_items[$key]			= $value;
	}

	public function get_pattern($key){
		if(!empty($this->pattern_items[$key])){
			return $this->pattern_items[$key];
		}else{
			return $key;
		}
	}

	public function set_filter($key,$value){
		$this->filter_items[$key]			= $value;
	}

	public function get_filter($key){
		if(!empty($this->filter_items[$key])){
			return $this->filter_items[$key];
		}else{
			return $key;
		}
	}

	public function set_template($key,$value){
		$this->templates[$key]			= $value;
	}
	public function get_template($key){
		if(!empty($this->templates[$key])){
			return $this->templates[$key];
		}else{
			return $key;
		}
	}

	public function get_templates(){
		return $this->templates;
	}


	public function get_add_query($sData,$wData=null,$oData=null){
		global $mstore;
		global $mb_fields;
		global $mb_board_table_name,$mb_comment_table_name,$mb_admin_tables;

		$add_query1	= "";
		$add_query2	= "";
		$add_query3	= "";
		$user_query	= "";
		$join_query		= "";
		$select_data	= array();
		$where_data	= array();
		$order_data		= array();

		if(!empty($sData)){
			if (is_array($sData) ){
				$select_data		= $sData;
			}else if($sData=="where"){
				$wData		= $sData;
			}else if($sData=="where_join"){
				$wData		= $sData;
				if(mbw_is_user_join()) $join_query		= "b.";
			}else if($sData=="order"){
				$oData		= $sData;
			}else if($sData=="order_join"){
				$oData		= $sData;
				if(mbw_is_user_join()) $join_query		= "b.";
			}
			if(!empty($select_data)){
				if($this->get_option("show_user_picture")){
					$user_query		= $user_query.",u.".$mb_fields["users"]["fn_user_picture"];
				}

				if($this->get_option("show_user_level")){
					$user_query		= $user_query.",u.".$mb_fields["users"]["fn_user_level"];
				}

				if(has_filter('mf_user_join_field')) $user_query		= apply_filters("mf_user_join_field",$user_query);

				if(empty($sData["command"])) $sData["command"]		= "SELECT";
				if(empty($sData["column"])) $sData["column"]				= "*";	
				if(empty($sData["join"])) $sData["join"]						= "";
				if(empty($sData["user_pid"])) $sData["user_pid"]			= "user_pid";
				if(empty($sData["table"])){
					$sData["table"]						= $mb_board_table_name;	
					//기본 테이블에 user_pid가 없으면 join 쿼리를 사용하지 않도록 설정
					if(empty($mb_fields["select_board"]["fn_user_pid"])) $user_query		= "";
				}

				if(strpos($sData["column"],"(")!==false) $user_query		= "";
				else if($this->is_admin_table($sData["table"])) $user_query		= "";
				else if($sData["join"]=="none") $user_query		= "";
				
				if(!empty($user_query)){
					if(mbw_is_user_join() && $sData["command"]=="SELECT")
						$join_query		= "b.";

					$user_query				= $user_query.",u.".$mb_fields["users"]["fn_pid"]." as uid";

					$sData["column"]		= str_replace( " ", "", $sData["column"]);
					$sData["column"]		= $join_query.str_replace( ",", ",".$join_query, $sData["column"]);

					$add_query1		= "SELECT ".$sData["column"].$user_query." from ".$sData["table"]." b LEFT OUTER JOIN ".$mb_admin_tables["users"]." u ON b.".$sData["user_pid"]."=u.pid";
				}else{
					$add_query1		= "SELECT ".$sData["column"]." from ".$sData["table"];
				}
			}
		}

		if(!empty($wData)){
			if (is_array($wData) ){
				$where_data		= $wData;				
				if(!empty($this->where_data) && !empty($this->board_name) && $this->board_name!='commerce_order_result' && strpos($this->board_name, 'commerce_product')!==0 && strpos($this->board_name, 'commerce_lecture')!==0){
					if(!empty($sData["table"]) && $mb_board_table_name==$sData["table"]){
						$temp_data		= array_merge($where_data,$this->where_data);
						$where_data	= array();
						$field_array		= array();
						foreach($temp_data as $item){
							if(!in_array($item['field'], $field_array)){
								$field_array[]		= $item['field'];
								$where_data[]		= $item;
							}
						}
					}
				}
				
				if(count($wData)==1 && isset($wData[0]['field']) && $wData[0]['field']=='fn_pid'){
					//글보기 모드에서 불필요한 파라미터 삭제
					if(function_exists('mbw_get_param') && mbw_get_param('mode')=='view'){
						$remove_data		= array('fn_category1','fn_category2','fn_category3');
						$search_data			= array('search_field','search_add_field1','search_add_field2','search_add_field3','se_field1');
						foreach($search_data as $key=>$value){
							if(mbw_get_param($value)!="" && strpos(mbw_get_param($value), 'pid')===false){
								$remove_data[]	= mbw_get_param($value);
							}
						}
						foreach($where_data as $key=>$data){
							if(isset($data['field'])){							
								if(in_array($data['field'], $remove_data)){
									unset($where_data[$key]);
								}
							}
						}
					}
				}
			}else if($wData=="where"){
				$where_data	= $this->where_data;
			}else if($wData=="where_join"){
				if(mbw_is_user_join()) $join_query		= "b.";
				$where_data	= $this->where_data;
			}else if($wData=="order"){
				$oData		= $wData;
			}else if($wData=="order_join"){
				if(mbw_is_user_join()) $join_query		= "b.";
				$oData		= $wData;				
			}
			if(!empty($where_data)){			
				$add_data		= array();
				$index			= 0;
				$count			= count($where_data)-1;
				
				foreach ( $where_data  as $data ) {
					if(empty($data["prefix"])) $data["prefix"]	= "";
					if(empty($data["suffix"])) $data["suffix"]	= "";
					if(empty($data["multi"])) $data["multi"]	= "0";
					if(empty($data["sign"])) $data["sign"]		= "=";
					if(empty($data["operator"])) $data["operator"]		= "AND";

					if(!empty($data["field"]) && isset($data["value"])){
						if(strpos($data["field"], 'fn_')===0){
							$field		= $this->get_board_field($data["field"]);
						}else{
							$field		= mbw_value_filter($data["field"],"name");
						}

						if($data["sign"]=="in"){
							$where_query		= $data["prefix"].$join_query.$field." ".$data["sign"]." ".$data["value"]." ".$data["suffix"];		//여기는 prepare 처리하면 안됨, 여러 문자열 데이터가 쉼표로 구분되서 넘어옴
						}else{
							if((strpos($field, 'category') === 0) && strlen($field)==9){
								$data["multi"]		= '1';
							}							
							if($data["multi"]=='1' && strpos($data["value"], ',') !== false){
								$category_array		= explode(',',$data["value"]);
								$filter_array			= array();
								foreach($category_array as $item){
									$filter_array[]		= $this->db->prepare($data["prefix"].$join_query.$field." ".$data["sign"]." %s ".$data["suffix"], $item );
								}
								$where_query		= " (".implode( ' OR ', $filter_array).")";
							}else{
								$where_query		= $this->db->prepare($data["prefix"].$join_query.$field." ".$data["sign"]." %s ".$data["suffix"],$data["value"]);
							}							
						}
						if($index<$count) $where_query		= $where_query." ".$data["operator"]." ";
						$add_data[]			= $where_query;
					}
					$index++;
				}
				$add_query2		= " WHERE ".implode( "", $add_data );
			}
		
			if(!empty($this->where_query_data)){
				$where_add_query		= "";
				$index						= 0;
				foreach ( $this->where_query_data as $data ) {
					if($index==0 && empty($add_query2))  $data["operator"]		= "WHERE";
					else if(empty($data["operator"])) $data["operator"]		= "AND";
					$where_add_query		.= " ".$data["operator"]." (".$data["query"].")";
					$index++;
				}
				$add_query2		.= $where_add_query;
			}
			if(empty($join_query) && strpos($add_query2, 'b.')!==false){
				$add_query2		= str_replace("(b.", "(", $add_query2);
				$add_query2		= str_replace(" b.", " ", $add_query2);
			}
		}

		if(!empty($oData)){
			if(is_array($oData)){
				$order_data		= $oData;
			}else if($oData=="order"){
				$order_data	= $this->order_data;
			}else if($oData=="order_join"){
				if(mbw_is_user_join()) $join_query		= "b.";
				$order_data	= $this->order_data;
			}
			$add_data		= array();

			if(!empty($this->order_query)){
				$add_query3		= " ".$this->order_query;
			}else if(!empty($order_data)){
				foreach ( $order_data  as $key => $value ) {
					if(strpos($key, 'fn_')===0){
						$field		= $this->get_board_field($key);
					}else $field		= $key;
					$field				= mbw_value_filter($field,"name");
					$value				= trim(strtolower(mbw_value_filter($value)));
					if($value=="desc"){
						$value		= "desc";
					}else{
						$value		= "asc";
					}
					$add_data[] = $join_query.$field." ".$value;
				}
				$add_query3		= " ORDER BY ".implode( ",", $add_data );
			}
			if(empty($join_query) && strpos($add_query3, 'b.')!==false){
				$add_query3		= str_replace("(b.", "(", $add_query3);
				$add_query3		= str_replace(" b.", " ", $add_query3);
			}
		}
		return $add_query1.$add_query2.$add_query3;
	}
	public function init_board_where(){
		$this->where_data			= array();
	}
	public function set_board_where($data){
		$exist		= false;
		if(isset($data["field"])){
			if(isset($data["mode"])){
				if($data["mode"]=='delete'){
					$this->where_unset_data[]		= $data["field"];
					if(!empty($this->where_data)){
						foreach ( $this->where_data as $key=>$item ) {
							if($item["field"]==$data["field"]){
								unset($this->where_data[$key]);
								break;
							}
						}
					}
				}
			}else if(isset($data["value"])){
				if(!empty($this->where_unset_data)){
					if(in_array($data["field"], $this->where_unset_data)){
						return;
					}
				}
				if(!empty($this->where_data) && !isset($data["sign"])){
					foreach ( $this->where_data as $key=>$item ) {
						if($item["field"]==$data["field"]){
							$this->where_data[$key]			= $data;
							$exist		= true;
							break;
						}
					}
				}
				if(!$exist) $this->where_data[]			= $data;
			}			
		}else if(!empty($data["query"])){
			if(!empty($this->where_query_data)){
				foreach ( $this->where_query_data as $key=>$item ) {
					if($item["query"]==$data["query"]){
						$exist		= true;
						break;
					}
				}
			}
			if(!$exist) $this->where_query_data[]			= $data;
		}
	}


	public function init_board_order(){
		$this->order_data			= array();
	}
	public function set_board_order($data){
		if(is_array($data)){
			$this->order_data			= array_merge($this->order_data,$data);
		}else if(is_string($data)){
			$this->order_query		= $data;
		}		
	}
	public function set_result_data($data){
		$this->result_data			= array_merge($this->result_data,$data);
	}
	public function get_result_data($key){
		if(isset($this->result_data[$key])) return $this->result_data[$key];
		else return "";
	}
	public function get_result_array($data=NULL){
		if(isset($data)) $this->result_data			= array_merge($this->result_data,$data);
		return $this->result_data;
	}
	public function get_result_datas($data=NULL){
		if(isset($data)) $this->result_data			= array_merge($this->result_data,$data);
		return $this->result_data;
	}

	public function table_exists($name){
		global $mb_table_prefix;		
		$table_name			= ($this->db->get_var($this->db->prepare("SHOW TABLES LIKE %s", $name)));

		if(!empty($table_name)) return true;
		else return false;		
	}


	public function set_options($data){		
		$this->options					= array_merge($this->options,$data);
	}

	public function get_option($key,$filter=true){
		if(isset($this->options[$key])){
			if($filter){
				return mbw_variable_type($this->options[$key]);
			}else{
				return $this->options[$key];
			}
		}else return "";
	}
	public function set_option($key,$value){
		$this->options[$key]		= $value;
	}

	public function set_messages($data){
		$this->messages				= array_merge($this->messages,$data);
	}

	public function get_message($key){
		if(isset($this->messages[$key])){
			return $this->messages[$key];
		}else return $key;
	}
	public function set_message($key,$value){
		$this->messages[$key]			= $value;
	}

	public function is_admin_table($name){
		global $mb_admin_tables, $mb_table_prefix;
		$board_type		= "";
		
		if(!empty($mb_admin_tables) && (in_array($mb_table_prefix.$name, $mb_admin_tables)!==false || in_array($name, $mb_admin_tables)!==false)){
			return true;
		}else{
			return false;
		}
	}

	public function get_board_type($name){
		global $mb_admin_tables, $mb_fields;		
		if(!empty($this->board_type[$name])) return $this->board_type[$name];

		if($this->is_admin_table($name)) $board_type	= "admin";
		else{
			$board_type	= $this->db->get_var($this->db->prepare("select ".$mb_fields["board_options"]["fn_board_type"]." from `".$mb_admin_tables["board_options"]."` where ".$mb_fields["board_options"]["fn_board_name2"]."=%s;", $name));
			if(empty($board_type)) $board_type		= "custom";
		}
		
		$this->board_type[$name]		= $board_type;
		return $board_type;
	}

	public function get_board_meta($w_data,$column="*",$limit=1){
		global $mb_fields,$mstore,$mdb,$mb_admin_tables;
		$fields				= $mb_fields["meta"];
		$o_data				= array($fields["fn_pid"]=>"DESC");
		$select_query		= mbw_get_add_query(array("column"=>$column,"join"=>"none","table"=>$mb_admin_tables["meta"]),$w_data,$o_data)." limit ".$limit;
		$result				= $mdb->get_results($select_query, ARRAY_A);
		if($limit==1){
			if(!empty($result[0])) return $result[0];
			else return array();
		}else{
			return $result;
		}
	}

	public function set_board_meta($command, $s_data,$w_data){
		global $mb_fields,$mstore,$mb_admin_tables;
		$this->db->db_query($command,$mb_admin_tables["meta"], $s_data, $w_data);
	}

	public function set_db_options($key="",$category=""){		
		global $mb_admin_tables,$mb_fields;
		global $mb_vars,$mstore;

		if(mbw_get_trace("mbw_db_options_meta")=="" && !empty($this->db)){
			$options					= array();
			$where_query			= "";

			if(!empty($key)){
				$where_query	= $this->db->prepare(" where ".$mb_fields["options"]["fn_option_load"]."=%s", $key);
				if(!empty($category)){
					$where_query	= $this->db->prepare($where_query." and ".$mb_fields["options"]["fn_option_category"]."=%s", $category);
				}
			}			

			$select_query		= $this->get_add_query(array("column"=>$mb_fields["meta"]["fn_meta_value"],"table"=>$mb_admin_tables["meta"]), array(array("field"=>$mb_fields["meta"]["fn_meta_table"],"value"=>'options'),array("field"=>$mb_fields["meta"]["fn_meta_key"],"value"=>'db_options')));
			$options_meta		= $this->db->get_var($select_query." ORDER BY ".$mb_fields["meta"]["fn_pid"]." DESC limit 1");

			if(!empty($options_meta)){	
				mbw_add_trace("mbw_db_options_meta");
				$option_data	= maybe_unserialize($options_meta);
				if(!is_array($option_data)) $option_data	= $this->db->get_results("select ".$mb_fields["options"]["fn_option_name"].",".$mb_fields["options"]["fn_option_value"]." from ".$mb_admin_tables["options"], ARRAY_A);
			}else{
				$option_data	= $this->db->get_results("select ".$mb_fields["options"]["fn_option_name"].",".$mb_fields["options"]["fn_option_value"]." from ".$mb_admin_tables["options"].$where_query, ARRAY_A);
			}
			
			foreach($option_data  as $value){
				$this->set_option($value[$mb_fields["options"]["fn_option_name"]], $value[$mb_fields["options"]["fn_option_value"]]);
			}
		}
	}

	public function set_board_options($board_name,$is_reset=false){
		global $mb_admin_tables,$mb_fields,$mb_api_urls;
		global $mb_vars,$mb_request_mode,$mdb;
		global $mb_languages,$mb_words;		

		if(empty($this->board_options) || $is_reset){
			if(empty($board_name)){
				return;
			}
			$board_name	= mbw_value_filter($board_name,"name");
			mbw_add_trace("mstore->set_board_options");
			$this->board_options		= $this->db->get_row($this->db->prepare("select * from ".$mb_admin_tables["board_options"]." where `".$mb_fields["board_options"]["fn_board_name2"]."`=%s limit 1", $board_name),ARRAY_A);

			//게시판 설정이 존재 하는지 확인
			if(!isset($this->board_options)){
				mbw_error_message("MSG_EXIST_ERROR2", array($board_name,$mb_languages["W_SETUP"]),"1301");
				return;
			}
			$this->set_board_option("fn_board_name",$board_name);

			//쿠키 정보가 있으면 회원정보 세팅
			if(mbw_validate_auth_cookie()){	
				mbw_set_wp_user_data();
			}
			if(mbw_get_param("action")=="mb_uploader" && mbw_get_param("file_type")==""){
				return;
			}

			$skin_name		= $this->get_board_option("fn_skin_name");
			//모바일 스킨이 설정되어 있을 경우 모바일 스킨으로 적용
			if(mbw_get_vars("device_type")=="mobile" && $this->get_board_option("fn_mobile_skin_name")!="") $skin_name		= $this->get_board_option("fn_mobile_skin_name");
			if(has_filter('mf_board_skin_name')) $skin_name			= apply_filters("mf_board_skin_name",$skin_name);
			if(empty($skin_name)){
				return;
			}
			$skin_path		= MBW_PLUGIN_PATH;
			$skin_url			= MBW_PLUGIN_URL;
			//테마에 망보드 스킨 폴더에 스킨이 존재하면 테마에 있는 스킨 적용			
			if(is_dir(get_stylesheet_directory()."/".MBW_PLUGIN_DIR."/skins/".$skin_name."/")){
				$skin_path		= get_stylesheet_directory()."/".MBW_PLUGIN_DIR."/";
				$skin_url			= get_stylesheet_directory_uri()."/".MBW_PLUGIN_DIR."/";
			}
			$skin_path		= $skin_path."skins/".$skin_name."/";
			if(!defined("MBW_SKIN_PATH")) define("MBW_SKIN_PATH", $skin_path);
			if(!defined("MBW_SKIN_URL")) define("MBW_SKIN_URL", $skin_url."skins/".$skin_name."/");

			$this->set_board_languages();

			//스킨 템플릿 파일 읽어오기
			if(is_file($skin_path."includes/skin-template.php")) require($skin_path."includes/skin-template.php");

			$require_files		= array();
			//테마 템플릿 폴더의 파일 읽어오기
			$require_files		= mbw_get_require_path("templates/","theme_file");
			//망보드 템플릿 폴더의 파일 읽어오기
			$require_files		= array_merge($require_files,mbw_get_require_path("templates/","file"));
			if(!empty($require_files)){
				foreach($require_files  as $value){
					require($value);
				}
			}
			if(is_file($skin_path."includes/skin-filters.php")) require($skin_path."includes/skin-filters.php");
			else require(MBW_PLUGIN_PATH."includes/skin-filters.php");
			
			$mb_fields["select_board"]			= $mb_fields["board"];
			$mb_fields["select_comment"]	= $mb_fields["comment"];

			//Model 설정이 되어 있지 않으면 스킨의 디폴트 파일(skin-model.php)을 불러옴
			$model_name		= mbw_get_board_option("fn_model_name");
			if(has_filter('mf_board_model_name')) $model_name			= apply_filters("mf_board_model_name",$model_name);

			if(!empty($model_name)) $model_path		= MBW_PLUGIN_PATH."models/".$model_name.".php";
			else $model_path		= $skin_path."includes/skin-model.php";
			if(has_filter('mf_model_path')) $model_path	= apply_filters("mf_model_path",$model_path);

			if(is_file($model_path)){  //모델 파일이 존재하는지 체크
				require($model_path);

				$board_model["desktop"]			= $desktop_model;			

				if(!empty($mobile_model)) $board_model["mobile"]		= $mobile_model;
				else $board_model["mobile"]		= $desktop_model;

				if(!empty($tablet_model)) $board_model["tablet"]		= $tablet_model;
				else $board_model["tablet"]		= $desktop_model;

				if(!empty($desktop_large_model)) $board_model["desktop_large"]		= $desktop_large_model;
				else $board_model["desktop_large"]		= $desktop_model;

				$this->set_models($board_model);
			}else{
				//모델 파일이 없으면 다른 모델 불러오거나 에러 출력
				mbw_error_message("MSG_EXIST_ERROR2", array($model_path,"File"),"1501");
			}		

			$this->set_board_fields($mb_fields["select_board"]);
			$this->set_comment_fields($mb_fields["select_comment"]);

			if(mbw_get_request_mode()=="Frontend"){
				if(!empty($skin_path) && is_dir($skin_path)){  //스킨 존재하는지 체크
					$skin_settings_path		= $skin_path."includes/skin-settings.php";
					if(has_filter('mf_skin_settings_path')) $skin_settings_path	= apply_filters("mf_skin_settings_path",$skin_settings_path);
					if(is_file($skin_settings_path)) require($skin_settings_path);	//스킨 설정 파일
					$api_type		= $this->get_board_option("fn_api_type");
					if(empty($api_type))	$api_type	= "mb";
					foreach($mb_api_urls as $key => $value){
						$file_name					= str_replace("_api", ".php", $key);
						$api_url						= MBW_PLUGIN_URL;
						$mb_api_urls[$key]		= $api_type."_".str_replace("_api", "", $key);
						if(is_file($skin_path."api/".$api_type."-".$file_name)){
							$mb_api_urls[$key]	= "skin_".$mb_api_urls[$key];
						}
					}
				}else{
					mbw_error_message("MSG_EXIST_ERROR2", array($skin_name,$mb_languages["W_SKIN"]),"1501");
					return;
				}
			}
		}
	}
	public function set_board_languages($lang=""){
		global $mb_languages,$mb_words;
		$locale		= $this->get_option("locale");

		if(!empty($lang) && $lang!=$locale){
			$locale		= $lang;
			mbw_set_option("locale",$locale);
			if(is_file(MBW_PLUGIN_PATH."includes/languages/mb-languages-".$locale.".php")){
				require(MBW_PLUGIN_PATH."includes/languages/mb-languages-".$locale.".php");
				$this->set_messages($mb_languages);
			}
		}
		if(is_file(MBW_SKIN_PATH."includes/languages/skin-languages-".$locale.".php")){
			require(MBW_SKIN_PATH."includes/languages/skin-languages-".$locale.".php");
			$this->set_messages($mb_languages);
		}else if(is_file(MBW_SKIN_PATH."includes/languages/skin-languages.php")){
			require(MBW_SKIN_PATH."includes/languages/skin-languages.php");
			$this->set_messages($mb_languages);
		}
	}

	public function set_board_files($pid=0,$table_name="",$is_download="1"){
		global $mb_admin_tables;
		global $mb_fields,$mb_board_table_name;
		if(!empty($pid) && (!isset($this->board_files) || $this->board_pid!=$pid)) {
			if(empty($table_name))		$table_name		= $mb_board_table_name;

			$this->board_pid		= $pid;
			$this->board_files	= $this->db->get_results($this->db->prepare("select * from ".$mb_admin_tables["files"]." where `".$mb_fields["files"]["fn_table_name"]."`=%s and ".$mb_fields["files"]["fn_board_pid"]."=%d and ".$mb_fields["files"]["fn_is_download"]."=%d order by ".$mb_fields["files"]["fn_file_sequence"]." asc", $table_name,$pid,$is_download),ARRAY_A);
		}
	}
	public function get_board_files($pid=0,$table_name="",$is_download="1"){		
		if(!empty($pid) && (!isset($this->board_files) || $this->board_pid!=$pid)) $this->set_board_files($pid,$table_name,$is_download);
		
		if(!empty($this->board_files))
			return $this->board_files;
		else return array();
	}
	public function get_board_file($pid=0,$table_name="",$is_download="1"){
		if(!empty($pid) && !isset($this->board_files)) $this->set_board_files($pid,$table_name,$is_download);
		
		if(!empty($this->board_files[$this->file_index])) {
			$data		= $this->board_files[$this->file_index];
			$this->file_index		= $this->file_index+1;
			return $data;
		}else return array();
	}

	public function get_editor_files($pid=0,$table_name="",$is_download="0"){
		global $mb_admin_tables;
		global $mb_fields,$mb_board_table_name;
		if(!empty($pid)) {			
			if(empty($table_name))		$table_name		= $mb_board_table_name;

			return $this->db->get_results($this->db->prepare("select * from ".$mb_admin_tables["files"]." where `".$mb_fields["files"]["fn_table_name"]."`=%s and ".$mb_fields["files"]["fn_board_pid"]."=%d and ".$mb_fields["files"]["fn_is_download"]."=%d order by ".$mb_fields["files"]["fn_file_sequence"]." asc", $table_name,$pid,$is_download),ARRAY_A);
		}else return array();
	}
	public function add_board_file($file){
		if(!empty($file)){
			$this->board_files[]		= $file;
		}
	}

	public function is_login(){
		if(mbw_get_user("fn_pid")==0) return false;			
		else if($this->user_login) return true;
		return $this->is_login_cookie();
	}

	public function is_login_cookie(){
		if($this->get_login_cookie()==""){
			return false;
		}else{
			return true;
		}
	}
	public function is_admin(){
		if($this->user_login && mbw_get_user("fn_user_level")>=mbw_get_option("admin_level")) return true;
		else return false;
	}

	//워드프레스 is_admin 함수가 ajax 모드에서 항상 true 를 리턴하기 때문에 별도로 구현
	public function is_admin_page(){	
		//Front-end
		if($this->is_admin()){
			if(is_admin() && mbw_get_request_mode()=="Frontend") return true;
			//back-end (admin-ajax)
			if(mbw_get_param("action")!="" && mbw_get_param("admin_page")=="true") return true; 
		}
		return false;
	}

	public function get_login_cookie(){
		if(!empty($this->cookies[$this->get_auth_cookie_name()])) return $this->cookies[$this->get_auth_cookie_name()];
		else if(!empty($_COOKIE[$this->get_auth_cookie_name()]))  return $_COOKIE[$this->get_auth_cookie_name()];
		else return "";
	}

	public function set_cookie($key,$value){
		$this->cookies[$key]	= $value;
	}

	public function get_cookie($key){
		if(!empty($this->cookies[$key])) return $this->cookies[$key];
		else if(!empty($_COOKIE[$key])){
			$this->cookies[$key]	= $_COOKIE[$key];
			return $_COOKIE[$key];
		} else return "";
	}
	public function clear_cookie(){
		$this->cookies			= array();
	}

	public function get_current_time(){
		if(empty($this->current_time))
			$this->current_time		= date($this->get_option("date_format")." ".$this->get_option("time_format"), mbw_get_timestamp());
		return $this->current_time;
	}
	public function get_current_date(){
		$current_time		= $this->get_current_time();
		$current_date		= explode(" ",$current_time);
		return $current_date[0];
	}

	public function set_user_data($user_id){		
		global $mb_admin_tables;
		$this->user_login		= true;
		$this->user				= $this->db->get_row($this->db->prepare("select * from ".$mb_admin_tables["users"]." where `".$this->user_fields["fn_user_id"]."`=%s limit 1",$user_id),ARRAY_A);		
	}
	public function is_user_pid($mode="equal"){		  
		global $mdb,$mb_board_table_name;
		global $mb_admin_tables,$mb_table_prefix,$mb_fields;
		$mb_user_pid		= $this->get_user("fn_pid");
		
		if($mb_table_prefix.$this->get_param("board_name")==$mb_admin_tables["users"] || $mb_table_prefix.mbw_get_board_option("fn_table_link")==$mb_admin_tables["users"]){
			if($this->get_param("board_pid")!=""){
				$item_user_pid		= intval($this->get_param("board_pid"));
			}else{
				$item_user_pid		= -1;
			}
		}else $item_user_pid		= -1;

		if($mode=="user"){
			if($this->is_login() && $item_user_pid==-1)
				$item_user_pid		= intval($this->get_board_item("fn_user_pid"));			
		}else if($mode=="guest"){
			if(intval($this->get_board_item("fn_user_pid"))==0 && $mb_user_pid==0) return true;
		}else if($mode=="equal"){
			if($item_user_pid==-1)
				$item_user_pid		= intval($this->get_board_item("fn_user_pid"));
		
		}else if($mode=="permission" && !empty($mb_fields["select_board"]["fn_user_pid"])){
			if($this->is_login() && $item_user_pid==-1 && $this->get_param("board_pid")!=""){
				if(empty($mb_board_table_name)){
					if(!empty($this->board_name)){
						$mb_board_table_name		= mbw_get_board_table_name($this->board_name);
					}else if(!empty($this->board_options) && !empty($this->board_options["board_name"])){
						$mb_board_table_name		= mbw_get_board_table_name($this->board_options["board_name"]);
					}
				}
				$item_user_pid		= intval($mdb->get_var($mdb->prepare("select ".$mb_fields["select_board"]["fn_user_pid"]." from `%1s` where %1s=%d;",$mb_board_table_name,$mb_fields["select_board"]["fn_pid"],$this->get_param("board_pid"))));
			}			
		}
		if($mb_user_pid == $item_user_pid) return true;
		else return false;
	}	

	public function get_current_user_level(){
		if($this->user_login){
			$user_mode		= strtoupper($this->get_option("user_mode"));
			if($user_mode=="MB"){
				return intval($this->user[$this->user_fields["fn_user_level"]]);
			}else if($user_mode=="WP"){
				return intval($this->user[$this->user_fields["fn_user_level"]]);
			} 
		}
		return 0;		
	}
	public function get_user($field){
		if(!empty($field)){
			if(isset($this->user_fields[$field])){
				if($field=="fn_user_level"){					
					return $this->get_current_user_level();
				}else{
					$key		= $this->user_fields[$field];
					if(isset($this->user[$key])){
						return $this->user[$key];
					}else{
						if($field=="fn_pid") return 0;
						else return "";
					}
				}
			}else if(isset($this->user[$field])) return $this->user[$field];
			else return "";
		}else{
			return $this->user;
		}
	}

	
	public function get_board_option($field){
		if(isset($this->board_option_fields[$field])){
			$key		= $this->board_option_fields[$field];
			if(isset($this->board_options[$key])){
				$value		= $this->board_options[$key];
				if(has_filter('mf_board_option')) $value			= apply_filters("mf_board_option", array("value"=>$value,"field"=>$field,"key"=>$key));
				return mbw_variable_type($value);
			}else return "";
		}else return "";
	}
	public function set_board_option($field,$value){
		if(isset($this->board_option_fields[$field])){
			$key		= $this->board_option_fields[$field];
			$this->board_options[$key]			= $value;
			return true;
		}else return false;		
	}

	public function set_board_fields($data){
		$this->board_fields				= $data;
	}
	public function set_board_option_fields($data){
		$this->board_option_fields			= $data;
	}

	public function set_comment_fields($data){
		$this->comment_fields			= $data;
	}
	public function set_user_fields($data){
		$this->user_fields			= $data;
	}
	
	public function set_board_field($key,$value){
		$this->board_fields[$key]			= $value;
	}
	public function set_comment_field($key,$value){
		$this->comment_fields[$key]			= $value;
	}
	public function get_board_fields(){
		return $this->board_fields;
	}
	public function get_comment_fields(){
		return $this->comment_fields;
	}

	public function get_board_select_fields($data,$board_name=""){
		global $mb_table_prefix,$mb_vars;
		global $mb_admin_tables,$mb_fields;
		if(empty($board_name)){
			$table_name		= mbw_get_board_table_name(mbw_get_board_name());
			if(!empty($table_name)){
				$board_name		= substr($table_name,strlen($mb_table_prefix));
			}
		}
		if(!empty($board_name) && ($this->board_name!=$board_name && $board_name!=mbw_get_board_option("fn_table_link"))){
			if($this->is_admin_table($board_name)){
				$fields		= $mb_fields[$board_name];
			}else{				
				$fields		= mbw_get_model_field($board_name);				
			}
		}else{
			$fields		= $this->board_fields;
		}		
		$select_fields		= array();
		foreach($data as $key){
			if(!empty($fields[$key])){
				$select_fields[$key]	= $fields[$key];
			}
		}
		return $select_fields;
	}
	public function get_comment_select_fields($data,$board_name=""){
		global $mb_table_prefix,$mb_vars,$mb_admin_tables;
		global $mb_fields;
		if(empty($board_name)){
			$table_name		= mbw_get_board_table_name(mbw_get_board_name());
			if(!empty($table_name)){
				$board_name		= substr($table_name,strlen($mb_table_prefix));
			}
		}
		if(!empty($board_name) && ($this->board_name!=$board_name && $board_name!=mbw_get_board_option("fn_table_link"))){
			$fields		= mbw_get_model_field($board_name,"comment");
		}else{
			$fields		= $this->comment_fields;
		}		

		$select_fields		= array();
		foreach($data as $key){
			if(!empty($fields[$key])){
				$select_fields[$key]	= $fields[$key];
			}
		}
		return $select_fields;
	}

	public function get_board_field($key,$fields=null){
		if(empty($fields)) $fields		= $this->board_fields;
		if(isset($fields[$key])){
			return $fields[$key];
		}else return "";
	}
	public function get_comment_field($key,$fields=null){
		if(empty($fields)) $fields		= $this->comment_fields;
		if(isset($fields[$key])){
			return $fields[$key];
		}else return "";
	}
	
	public function get_board_items(){
		if(!empty($this->board_items)){
			return $this->board_items;
		}else return array();
	}

	public function set_board_items($items){
		if(!empty($items)){
			$this->board_items		= $items;
		}
	}

	public function get_board_item($field="",$filter=true){
		if(!empty($field)){
			if(isset($this->board_fields[$field])){
				$key		= $this->board_fields[$field];
				if(isset($this->board_item[$key])){
					$value			= $this->board_item[$key];
					if(has_filter('mf_board_sitem')){
						$filter_item	= apply_filters("mf_board_sitem", array("value"=>$value,"field"=>$field,"type"=>"board"), $this->board_item);
						$value			= $filter_item["value"];
					}
					if($filter){						
						$filter_item	= apply_filters("mf_board_item", array("value"=>$value,"field"=>$field,"type"=>"board"), $this->board_item);
						return  $filter_item["value"];
					}else{
						return  $value;
					}
				}else return "";
			}else if(isset($this->board_item[$field])){
				$value			= $this->board_item[$field];
				if(has_filter('mf_board_sitem')){
					$filter_item		= apply_filters("mf_board_sitem", array("value"=>$value,"field"=>$field,"type"=>"board"), $this->board_item);
					$value				= $filter_item["value"];
				}
				if($filter){
					$filter_item		= apply_filters("mf_board_item", array("value"=>$value,"field"=>$field,"type"=>"board"), $this->board_item);
					return  $filter_item["value"];
				}else{
					return  $value;
				}
			}else return "";
		}else if(!empty($this->board_item)){
			return $this->board_item;
		}else return array();
	}
	public function set_board_item($field,$value="",$index=1){		
		if(!empty($field)){
			if(is_string($field)){
				if(isset($this->board_fields[$field])){
					$key		= $this->board_fields[$field];
					$this->board_item[$key]			= $value;
					return true;
				}
			}else if(is_array($field)){
				$this->item_index		= $this->item_index+$index;
				$this->board_item		= $field;
				return true;
			}
		}
		return false;
	}
	public function get_item_index(){
		return $this->item_index;
	}
	public function set_item_index($value){
		$this->item_index	= $value;
		return true;
	}
	public function get_template_index(){
		return $this->template_index;
	}
	public function set_template_index($value){
		$this->template_index	= $value;
		return true;
	}
	public function get_board_json($field){
		if(isset($this->board_json[$key])){
			return $this->board_json[$key];
		}else return "";
	}
	public function set_board_json($data){
		$this->board_json			= ($data);
		return true;
	}

	public function get_comment_item($field,$filter=true){
		if(!empty($field)){
			if(isset($this->comment_fields[$field])){
				$key		= $this->comment_fields[$field];
				if(isset($this->comment_item[$key])){
					$value			= $this->comment_item[$key];
					if(has_filter('mf_board_sitem')){
						$filter_item	= apply_filters("mf_board_sitem", array("value"=>$value,"field"=>$field,"type"=>"comment"), $this->comment_item);
						$value			= $filter_item["value"];
					}
					if($filter){
						$filter_item		= apply_filters("mf_board_item", array("value"=>$value,"field"=>$field,"type"=>"comment"), $this->comment_item);
						return $filter_item["value"];
					}else{
						return $value;
					}
				}else return "";
			} else return "";
		}else if(!empty($this->comment_item)){
			return $this->comment_item;
		}else return "";
	}
	public function get_comment_items(){
		return $this->comment_items;
	}
	public function set_comment_items($items){
		if(!empty($items)){
			$this->comment_items		= $items;
		}
	}
	public function set_comment_item($field,$value=""){		
		if(!empty($field)){
			if(is_string($field)){
				if(isset($this->comment_fields[$field])){
					$key		= $this->comment_fields[$field];
					$this->comment_item[$key]			= $value;			
				}else $this->comment_item[$field]			= $value;
				return true;
			}else if(is_array($field)){				
				$this->comment_item		= $field;
				return true;
			}
		}
		return false;
	}
	public function get_auth_cookie_name(){
		if($this->get_auth_secure()){
			//return MBW_SECURE_AUTH_COOKIE;
			return MBW_AUTH_COOKIE;
		}else{
			return MBW_AUTH_COOKIE;
		}		
	}
}
?>