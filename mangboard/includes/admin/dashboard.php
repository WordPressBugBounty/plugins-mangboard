<?php 
if(!defined('_MB_')) exit();
do_action('mbw_dashboard_init');
$dashboard_data		= array();
$mb_version			= mbw_get_option("mb_version");

function mbw_dashboard_plugin_update($version){
	if(empty($version)) return false;	
	$update_file		= download_url("http://demo.mangboard.com/update/mangboard.".$version.".zip");

	if(is_wp_error($update_file)){echo '<script>alert("MangBoard '.esc_js($version).' download failed");moveURL("'.admin_url("admin.php?page=mbw_dashboard").'");</script>';exit;}

	global $wp_filesystem;
	$path			= trailingslashit($wp_filesystem->find_folder(WP_CONTENT_DIR.'/plugins'));
	$unzipfile		= @unzip_file($update_file, $path);
   
	if($unzipfile){
		if(mbw_get_option("mb_version")!=$version) mbw_update_option('mb_version',$version);
		echo '<script>alert("MangBoard '.esc_js($version).' Update Completed");moveURL("'.admin_url("admin.php?page=mbw_dashboard").'");</script>';
	}else{
		echo '<script>alert("MangBoard '.esc_js($version).' Update Failed");moveURL("'.admin_url("admin.php?page=mbw_dashboard").'");</script>';exit;
	}  
	return true;
}
if(!empty($_REQUEST["site_lang"]) && mbw_is_admin()){
	if(wp_verify_nonce(mbw_get_param('mbw-language-nonce'), 'mbw-language-key')){
		$mb_lang			= mbw_value_filter($_REQUEST["site_lang"]);
		update_option("mb_locale",$mb_lang);
	}
}
if(!empty($_REQUEST["lang"]) && mbw_is_admin()){
	if(wp_verify_nonce(mbw_get_param('mbw-language-nonce'), 'mbw-language-key')){
		$mb_admin_lang			= mbw_value_filter($_REQUEST["lang"]);
		update_option("mb_admin_locale",$mb_admin_lang);
	}
}
if(!empty($_REQUEST["update_version"])){
	if(!current_user_can('activate_plugins') || !wp_verify_nonce(mbw_get_param('mbw-update-nonce'), 'mbw-update-key')){
		echo '<script>alert("'.__MM('MSG_UPDATE_PERMISSION_ERROR').'");moveURL("'.admin_url("admin.php?page=mbw_dashboard").'");</script>';
	}else{
		$update_version		= mbw_value_filter($_REQUEST["update_version"]);
		$url					= wp_nonce_url(admin_url("admin.php?page=mbw_dashboard&update_version=".$update_version), 'mangboard-'.$update_version);
		if(($creds=request_filesystem_credentials($url))===false) return;
		if(!WP_Filesystem($creds)){request_filesystem_credentials($url);return;}
		if(version_compare($mb_version, $update_version, '<')){
			mbw_dashboard_plugin_update($update_version);
		}
	}
}
$mb_admin_lang		= get_option("mb_admin_locale");
$mb_locale				= get_option("mb_locale");
if(empty($mb_locale)){ $mb_locale	= mbw_get_option("locale"); }
$feed_url					= "https://demo.mangboard.com/dashboard_data.php?lang=".$mb_locale;
$feed_item				= mbw_fetch_feed($feed_url);
$latest_version			= '1.0.0';
$dashboard_title			= "";

if(!empty($feed_item)){	
	if(!empty($feed_item["notice"])){
		$dashboard_desc		= $feed_item["notice"];
	}
	if(!empty($feed_item["version"])){
		$latest_version			= $feed_item["version"];
	}
	if(!empty($feed_item["rss"])){
		$dashboard_data		= $feed_item["rss"];
	}	
}
$today_date	= date("Y-m-d", mbw_get_timestamp());
$items			= $mdb->get_results($mdb->prepare("select * from `".$mb_admin_tables["analytics"]."` where ".$mb_fields["analytics"]["fn_date"]."<=%s order by ".$mb_fields["analytics"]["fn_pid"]." desc limit 0,2;",$today_date),ARRAY_A);
if(count($items)==0) $items[]	= array("today_page_view"=>0,"today_write"=>0,"today_comment"=>0,"today_join"=>0,"today_upload"=>0,"today_visit"=>0,"total_visit"=>0,"today_reply"=>0);
if(count($items)==1) $items[]	= array("today_page_view"=>0,"today_write"=>0,"today_comment"=>0,"today_join"=>0,"today_upload"=>0,"today_visit"=>0,"total_visit"=>0,"today_reply"=>0);
?>
<style type="text/css">
.mb-dash {margin-top:10px;}
.mb-dash table {border-collapse: collapse;table-layout: fixed;word-break: break-all;min-height:209px;}
.mb-dash table td{padding:1px 2px;border:1px solid #eee;font-size:13px;}
.mb-dash table th{padding:5px 2px;background-color:#F6F6F6;border:1px solid #eee;}
.mb-dash .text-left{text-align:left;padding-left:8px !important;}
.mb-dash .mb-dash-title{font-size: 21px;margin: 0;line-height: 1.2;display: block;font-weight: 600;float:left;padding:2px 30px 0 0;}
.mb-dash .mb-dash-panel-title{font-size: 16px;padding: 10px 0 0;margin: 0;line-height: 2.1;display: block;font-weight: 600;}
.mb-dash .mb-dash-box-title{font-size: 14px;padding: 8px 12px;margin: 0;line-height: 1.4;border-bottom: 1px solid #eee;display: block;font-weight: 600;}

.mb-dash .mb-welcome-panel{margin:0px 0 0px !important;padding: 12px 0px 0 !important;box-shadow:none !important;position: relative;overflow: auto;border: 1px solid #c3c4c7;box-shadow: 0 1px 1px rgb(0 0 0 / 4%);background: #fff;font-size: 13px;line-height: 1.7; }
.mb-dash .about-description{display:none !important;}
.mb-dash .mb-welcome-panel .mb-welcome-panel-column:first-child {width: 36%;}
.mb-dash .mb-welcome-panel .mb-welcome-panel-column {width: 32%;min-width: 200px;float: left;}
body.mobile .mb-dash .mb-welcome-panel-column, body.mobile .mb-dash .mb-welcome-panel .mb-welcome-panel-column:first-child {display: block;float: none;width: 100%;}
.mb-dash .mb-welcome-panel td{line-height:2.3 !important;}
.mb-mobile.mb-dash .mb-welcome-panel td{line-height:1.3 !important;padding:7px 2px 7px;}
.mb-dash .mb-welcome-panel-content{margin-left:13px;max-width:1500px;}
.mb-dash .mb-dash-box{border: 1px solid #e5e5e5;border-top:none;box-shadow: 0 1px 1px rgba(0,0,0,.04);background: #fff;}
.mb-dash a{text-decoration: none !important;}
.mb-dash .button{min-height:30px !important;line-height:2}
.mb-dash select,.mb-board input{min-height:28px;line-height:normal;font-size:13px;}
.mb-dash .handlediv{display:none;}
<?php
if(!defined( 'ELEMENTOR_VERSION' )){ 
	echo '.mb-dash .mb-dash-elementor{display:none;}';
}
if(intval($items[0]["total_visit"])>2000){
	echo '.mb-dash .mb-dash-tip, .mb-dash .mb-dash-elementor{display:none;}';
}
$mb_version2	= $mb_version;
if(function_exists('get_plugin_data')){
	$plugin_data		= get_plugin_data(MBW_PLUGIN_PATH.'mangboard.php',false,false);
	if(!empty($plugin_data['Version'])){
		$mb_version2	= $plugin_data['Version'];
	}
}
if(version_compare($mb_version2, '1.9.0', '>=')){
	echo '.mb-dash .mb-dash-update,.mb-dash .mb-dash-update2{display:none;}';
}
?>
</style>
<script type="text/javascript">
function mbw_update_confirm(){		
	if(confirm("<?php echo __MM('MSG_UPDATE_CONFIRM'); ?>")){
		document.forms['mb_dashboard_update'].submit();
		//moveURL("<?php echo admin_url('admin.php?page=mbw_dashboard&update_version='.$latest_version);?>");
	}
}
function mbw_send_update_form(){		
	if(document.getElementById('mb_latest_version').value){
		document.forms['mb_dashboard_update'].submit();
	}
}
function mbw_send_language_form(){
	/*
	var objSelect1	= document.getElementById("mb_site_locale");
	var lang1			= objSelect1.options[objSelect1.selectedIndex].value;
	var objSelect2	= document.getElementById("mb_admin_locale");
	var lang2			= objSelect2.options[objSelect2.selectedIndex].value;	
	moveURL("<?php echo admin_url('admin.php?page=mbw_dashboard&site_lang=');?>"+lang1+"&lang="+lang2);
	*/
	if(document.getElementById('mb_site_locale').value!="" && document.getElementById('mb_admin_locale').value!=""){
		document.forms['mb_dashboard_language'].submit();
	}
}
<?php
if(version_compare($mb_version2, '1.9.0', '<') && empty($_REQUEST["update_version"])){
	if($mb_locale=="ko_KR"){		
		echo 'jQuery( document ).ready(function() {   if(confirm("'.__MM('MSG_UPDATE_LATEST_VERSION',$latest_version).'.\n'.__MM('MSG_UPDATE_CONFIRM2').'")){ mbw_send_update_form(); } });';
	}
}
?>
</script>
<?php do_action('mbw_dashboard_header'); ?>

<?php if(!empty($dashboard_title)) echo "<div>".$dashboard_title."</div>"; ?>		
<div id="wpbody" role="main" class="mb-dash mb-<?php echo mbw_get_vars("device_type");?>">
<div id="wpbody-content" aria-label="Main Contents" tabindex="0" style="overflow: hidden;">

	<div id="mb-welcome-panel" class="mb-welcome-panel">
	<div class="mb-welcome-panel-content">
		
		<div>
			<div class="mb-dash-title"><?php echo __MW('W_MANGBOARD')." ".__MW('W_DASHBOARD');?></div>
			<div style="float:left;">
				<div class="button"><a href="https://www.mangboard.com/manual/?lang=<?php echo esc_attr($mb_locale);?>" target="_blank"><?php echo __MW('W_MANUAL'); ?></a></div>
				<div class="button"><a href="https://www.mangboard.com/tech_support/?lang=<?php echo esc_attr($mb_locale);?>" target="_blank"><?php echo __MW('W_TECH_SUPPORT'); ?></a></div>				
			</div>
			<div class="clear"></div>
		</div>
		
		
		<p class="about-description"></p>
		<div class="mb-welcome-panel-column-container">
			<div class="mb-welcome-panel-column">
				<div class="mb-dash-panel-title"><?php echo __MW('W_SUMMARY_STATISTICS'); ?></div>			
				<div style="text-align:center;width:90%;">
					<?php
					echo '<table cellspacing="0" cellpadding="0" style="width:100%;">';
					echo '<colgroup><col style="40%"><col style="width:30%"><col style="width:30%"></colgroup>';
					echo '<thead><tr><th scope="col">'.__MW('W_TYPE').'</th><th scope="col">'.__MW('W_YESTERDAY').'</th><th scope="col">'.__MW('W_TODAY').'</th></tr></thead>';
					echo '<tbody>';
					echo '<tr><td>'.__MW('W_TODAY_PAGE_VIEW').'</td><td>'.number_format($items[1]["today_page_view"]).'</td><td>'.number_format($items[0]["today_page_view"]).'</td></tr>';
					echo '<tr><td>'.__MW('W_TODAY_WRITE').'</td><td>'.number_format(intval($items[1]["today_write"])+intval($items[1]["today_reply"])).'</td><td>'.number_format(intval($items[0]["today_write"])+intval($items[0]["today_reply"])).'</td></tr>';
					echo '<tr><td>'.__MW('W_TODAY_COMMENT').'</td><td>'.number_format($items[1]["today_comment"]).'</td><td>'.number_format($items[0]["today_comment"]).'</td></tr>';
					echo '<tr><td>'.__MW('W_TODAY_JOIN').'</td><td>'.number_format($items[1]["today_join"]).'</td><td>'.number_format($items[0]["today_join"]).'</td></tr>';
					echo '<tr><td>'.__MW('W_TODAY_UPLOAD').'</td><td>'.number_format($items[1]["today_upload"]).'</td><td>'.number_format($items[0]["today_upload"]).'</td></tr>';
					echo '<tr><td>'.__MW('W_TODAY_VISIT').'</td><td>'.number_format($items[1]["today_visit"]).'</td><td>'.number_format($items[0]["today_visit"]).'</td></tr>';
					echo '</tbody></table>';
					?>
				</div>
				<div style="padding:6px 0px;"></div>
			</div>
			<div class="mb-welcome-panel-column mb-welcome-panel-last">
				<div class="mb-dash-panel-title"><?php echo __MW('W_REFERER_LATESET'); ?><span style="font-size:12px;color:#999;line-height:1.2;"> (<?php echo __MW('W_ONE_WEEK'); ?>)</span></div>				
				<div style="text-align:center;width:90%;">
					<?php
					$search_date	= date("Y-m-d H:i:s", (mbw_get_timestamp()-(60*60*24*7)));
					$field			= mbw_value_filter($mb_fields["referers"]["fn_referer_host"],"name");
					$url_items	= $mdb->get_results($mdb->prepare("select ".$field.", count(".$field.") as count  from ".$mb_admin_tables["referers"]." where ".$mb_fields["referers"]["fn_reg_date"].">%s and ".$field."!='' group by ".$field." order by count desc limit 6;",$search_date),ARRAY_A);
					echo '<table cellspacing="0" cellpadding="0" style="width:100%;">';
					echo '<colgroup><col style="15%"><col style="width:65%"><col style="width:20%"></colgroup>';
					echo '<thead><tr><th scope="col">'.__MW('W_RANK').'</th><th scope="col">URL</th><th scope="col">'.__MW('W_SESSION').'</th></tr></thead>';
					echo '<tbody>';				
					$rank		= 1;
					foreach($url_items as $item){
						echo '<tr><td>'.$rank.'</td><td class="text-left">'.$item[$field].'</td><td>'.$item["count"].'</td></tr>';
						$rank++;
					}					
					echo '</tbody></table>';
					?>
				</div>
				<div style="padding:6px 0px;"></div>
			</div>
			<div class="mb-welcome-panel-column">
				<div class="mb-dash-panel-title"><?php echo __MW('W_MANGBOARD')." ".__MW('W_CURRENT_STATE');?></div>
				<div style="text-align:center;width:90%;">
					<?php					
					echo '<table cellspacing="0" cellpadding="0" style="width:100%;">';
					echo '<colgroup><col style="38%"><col style="width:62%"></colgroup>';
					echo '<thead><tr><th scope="col">'.__MW('W_TYPE').'</th><th scope="col">'.__MW('W_VALUE').'</th></tr></thead>';
					echo '<tbody>';				
					
					$update_button		= "";
					if(version_compare($mb_version, $latest_version, '<')){
						$update_button		= '=&gt;<div class="button"><a href="javascript:;" onclick="mbw_update_confirm();return false;">'.$latest_version." ".__MW('W_UPDATE').'</a></div>';
						echo '<form id="mb_dashboard_update" name="mb_dashboard_update" action="'.admin_url('admin.php?page=mbw_dashboard').'" method="post">';
							echo '<input type="hidden" id="mb_latest_version" name="update_version" value="'.esc_attr($latest_version).'">';
							echo wp_nonce_field('mbw-update-key', 'mbw-update-nonce');
						echo '</form>';
					}else{
						$update_button		= '(DB:'.mbw_get_option("db_version").') ';
					}
					echo '<tr><td>'.__MW('W_MANGBOARD_VERSION').'</td><td>'.$mb_version." ".$update_button.'</td></tr>';

					echo '<tr><td>'.__MW('W_PHP_VERSION').'</td><td>'.phpversion()." (Upload Max: ".ini_get("upload_max_filesize").", Post: ".ini_get("post_max_size").')</td></tr>';
					
					include(MBW_PLUGIN_PATH."templates/mtpl.input.php");
					$lang_html		= '<form id="mb_dashboard_language" name="mb_dashboard_language" action="'.admin_url('admin.php?page=mbw_dashboard').'" method="post">';
						$data					= array("type"=>"select","ext"=>"","style"=>"");
						$data["value"]		= $mb_locale;
						$data["item_name"]		= "site_lang";
						
						if($data["value"]!='ko_KR' && $data["value"]!='ja' && $data["value"]!='zh_CN') $data["value"]		= 'en_US';
						$data["data"]		= "en_US,ko_KR,zh_CN,ja";
						$data["label"]		= "English,Korean,Chinese,Japanese";
						$data["item_id"]	= "mb_site_locale";
						$data["ext"]		= " title='Site Language' onchange=\"mbw_send_language_form();\"";
						$lang_html		.= mbw_get_input_template("admin",$data);

						if(!empty($mb_admin_lang)){
							$data["value"]		= $mb_admin_lang;
							if($data["value"]!='ko_KR' && $data["value"]!='ja' && $data["value"]!='zh_CN') $data["value"]		= 'en_US';
						}
						$data["item_name"]	= "lang";
						$data["item_id"]	= "mb_admin_locale";
						$data["ext"]		= " title='Admin Language' onchange=\"mbw_send_language_form();\"";
						$lang_html		.= mbw_get_input_template("admin",$data);					
						$lang_html		.= wp_nonce_field('mbw-language-key', 'mbw-language-nonce');
					$lang_html		.= '</form>';

					echo '<tr><td>'.__MW('W_SITE_LOCALE').'<span class="max-width-m100">(Home/Admin)</span></td><td>'.$lang_html.'</td></tr>';

					echo '<tr><td>'.__MW('W_TOTAL_USER').'</td><td>'.number_format($mdb->get_var($mdb->prepare("SELECT count(*) FROM %1s;",$mb_admin_tables["users"]))).'</td></tr>';
					echo '<tr><td>'.__MW('W_TOTAL_FILE').'</td><td>'.number_format($mdb->get_var($mdb->prepare("SELECT count(*) FROM %1s;",$mb_admin_tables["files"]))).'</td></tr>';
					echo '<tr><td>'.__MW('W_TOTAL_VISIT').'</td><td>'.number_format($items[0]["total_visit"]).'</td></tr>';
					echo '</tbody></table>';
					?>
				</div>
				<div style="padding:6px 0px;"></div>
			</div>
		</div>
		<?php
		if(version_compare(PHP_VERSION, '5.4.0', '<')){
			echo '<div style="padding:0 0 10px;">';
				echo '<div style="color:#ed1c2e">[경고] PHP 5.4 미만의 버전에서 망보드 사용시 일부 기능이 정상적으로 동작하지 않을 수 있습니다. (PHP 설치버전이 5.4 이상인지 확인해 주세요)</div>';
			echo '</div>';
		}
		?>
	</div>

	</div>
	<?php
		if(!empty($dashboard_desc)) echo $dashboard_desc;
		$index			= 1; 
		$maxlength	= 50;
	?>
	<div id="dashboard-widgets-wrap"><div id="dashboard-widgets" class="metabox-holder">
		
		<!-- 최근 글 리스트 -->
		<div id="postbox-container-<?php echo esc_attr($index);?>" class="postbox-container">
		<div id="box<?php echo esc_attr($index);?>-sortables" class="meta-box-sortables ui-sortable"><div id="dashboard_primary" class="postbox ">
			<div class="handlediv" title=""><br></div>
			<div class="mb-dash-box-title  ui-sortable-handle"><span><?php echo __MW('W_BOARD_LATESET'); ?></span></div>
			<div class="inside">				
				<div class="rss-widget">
					<ul>
					<?php 
					$option_name				= "mb_latest_board_data";			
					$latest_data					= get_option($option_name);
					if(!empty($latest_data) && is_array($latest_data)) $latest_data		= array_reverse($latest_data);
					$is_admin_page			= mbw_is_admin_page();

					if(!empty($latest_data)){
						$list_size						= 10;
						$list_index					= 0;
						foreach($latest_data as $data){						
							$item		= array_merge( array("title"=>"","post_id"=>"","parent_pid"=>"","pid"=>"","table"=>"","name"=>"","time"=>""), $data);
							if($is_admin_page){
								$url		= "admin.php?page=mbw_board_options&board_name=".$item["name"]."&vid=".$item["pid"];
							}else {
								$latest_permalink		= get_permalink($item["post_id"]);
								if(strpos($latest_permalink, '?') === false)	$latest_permalink		= $latest_permalink."?";
								else 	$latest_permalink		= $latest_permalink."&";
								$url		= $latest_permalink."vid=".$item["pid"];
							}
							if(!mbw_is_admin_table($item["table"])){
								$row		= $mdb->get_row($mdb->prepare("SELECT * FROM ".$item["table"]." where ".$mb_fields["board"]["fn_pid"]."=%d limit 1",$item["pid"]),ARRAY_A);
								if(empty($row['title'])) continue;
								$title		= $row['title'];
								if(mb_strlen($title)>$maxlength) $title		= mb_substr($title, 0,$maxlength)."...";
								if(!empty($title)){ 
									echo '<li><a class="rsswidget" href="'.esc_url($url).'">'.esc_html($title).'</a> <span class="rss-date">'.date("Y-m-d H:i:s", $item["time"]).'</span></li>';
									$list_index++;
								}
							}							
							if($list_index>=$list_size) break;
						}
					}
					?>
					</ul>
				</div>
			</div>
		</div></div>
		</div>

		<?php $index++;	?>
		<!-- 최근 댓글 리스트 -->
		<div id="postbox-container-<?php echo esc_attr($index);?>" class="postbox-container">
		<div id="box<?php echo esc_attr($index);?>-sortables" class="meta-box-sortables ui-sortable"><div id="dashboard_primary" class="postbox ">
			<div class="handlediv" title=""><br></div>
			<div class="mb-dash-box-title ui-sortable-handle"><span><?php echo __MW('W_COMMENT_LATESET'); ?></span></div>
			<div class="inside">				
				<div class="rss-widget">
					<ul>
					<?php 
					$option_name				= "mb_latest_comment_data";			
					$latest_data					= get_option($option_name);
					if(!empty($latest_data) && is_array($latest_data)) $latest_data		= array_reverse($latest_data);
					$is_admin_page			= mbw_is_admin_page();
					if(!empty($latest_data)){
						$list_size						= 10;
						$list_index					= 0;
						foreach($latest_data as $data){						
							$item		= array_merge( array("title"=>"","post_id"=>"","parent_pid"=>"","pid"=>"","table"=>"","name"=>"","time"=>""), $data);
							if($is_admin_page){
								$url		= "admin.php?page=mbw_board_options&board_name=".$item["name"]."&vid=".$item["parent_pid"];
							}else {							
								$latest_permalink		= get_permalink($item["post_id"]);
								if(strpos($latest_permalink, '?') === false)	$latest_permalink		= $latest_permalink."?";
								else 	$latest_permalink		= $latest_permalink."&";
								$url		= $latest_permalink."vid=".$item["parent_pid"];
							}
							$title		= $mdb->get_var($mdb->prepare("SELECT ".$mb_fields["comment"]["fn_content"]." FROM ".$item["table"]." where ".$mb_fields["comment"]["fn_pid"]."=%d limit 1",$item["pid"]));		
							if(!empty($title)){
								if(mb_strlen($title)>$maxlength) $title		= mb_substr($title, 0,$maxlength)."...";
								if(!empty($title)){
									echo '<li><a class="rsswidget" href="'.esc_url($url).'">'.esc_html($title).'</a> <span class="rss-date">'.date("Y-m-d H:i:s", $item["time"]).'</span></li>';
									$list_index++;
								}
							}
							if($list_index>=$list_size) break;
						}
					}
					?>
					</ul>
				</div>
			</div>
		</div></div>
		</div>

	<!-- 망보드 RSS -->	
	<?php if(!empty($dashboard_data)){ $index++;foreach($dashboard_data  as $data){	?>
		<!-- widget1 start -->
		<div id="postbox-container-<?php echo esc_attr($index);?>" class="postbox-container">
		<div id="box<?php echo esc_attr($index);?>-sortables" class="meta-box-sortables ui-sortable"><div id="dashboard_primary" class="postbox ">
			<div class="handlediv" title=""><br></div>
			<div class="mb-dash-box-title ui-sortable-handle"><span><?php echo $data["title"];?></span></div>
			<div class="inside">				
				<div class="rss-widget">
					<?php $default_args = array( 'show_author' => 0, 'show_date' => 1, 'show_summary' => 0 ); wp_widget_rss_output(trim($data["link"]),$default_args); ?>
				</div>
			</div>
		</div></div>
		</div>
		<!-- widget1 end -->
	<?php $index++; }}?>

	<?php do_action('mbw_dashboard_widget'); ?>


	</div></div><!-- dashboard-widgets-wrap -->

<div class="clear"></div></div><!-- wpbody-content -->
<div class="clear"></div></div>

<?php do_action('mbw_dashboard_footer'); ?>