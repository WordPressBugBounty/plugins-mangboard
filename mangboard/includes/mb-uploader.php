<?php
define("MBW_REQUEST_MODE", "API");
if(!defined('_MB_')) exit();

do_action('mbw_uploader_api_init');
$mode			= "";
if(isset($_REQUEST["mode"])) $mode		= $_REQUEST["mode"];

$file_name			= "";
$file_url				= "";
$file_ext				= "";

$uploadPath		= MBW_UPLOAD_PATH;
$error_check		= false;
$datePath			= date("Y/m/d/",mbw_get_timestamp());
$file_pid				= "";
do_action('mbw_uploader_api_header');

if($mode=="html5"){
	$sFileInfo = "";
	$headers = array();	 
	foreach($_SERVER as $k => $v) {
		if(substr($k, 0, 9) == "HTTP_FILE") {
			$k = substr(strtolower($k), 5);
			$headers[$k] = $v;
		} 
	}
	if(!empty($headers)){
		$upload_data				= mbw_file_upload(array("board_name"=>"N","table_name"=>"N","board_pid"=>"0","type"=>"editor"),$headers);
		$file_name					= $upload_data["name"];

		if(!empty($upload_data["path"])){			
			$sFileInfo .= "&sFileName=".rawurlencode($file_name);
			$sFileInfo .= "&sFileURL=".rawurlencode(mbw_get_image_url("path",$upload_data["path"]));
			$sFileInfo .= "&bNewLine=true";
			echo $sFileInfo;
		}else{
			echo "NOTALLOW_".$file_name;
		}
	}
}else if($mode=="flash"){
	if(!empty($_FILES["Filedata"]) && mbw_check_api_file("editor")) {
		$upload_data	= mbw_file_upload(array("board_name"=>"N","table_name"=>"N","board_pid"=>"0","type"=>"editor"));
		$file_name		= $upload_data["name"];

		if($mstore->get_result_data("state")=="error"){
			echo mbw_data_encode($mstore->result_data);
		}else if(!empty($upload_data["path"])){
			$file_data					= array();
			$file_data["name"]		= $file_name;
			$file_data["url"]			= mbw_get_image_url("url",$upload_data["path"]);

			$mstore->set_result_data(array("data"=>$file_data));
			echo mbw_data_encode($mstore->get_result_array(array("state"=>"success")));	
		}else echo mbw_data_encode($mstore->result_data);
	}else if($mstore->get_result_data("state")=="error"){
		echo mbw_data_encode($mstore->result_data);
	}
}else if($mode=="plugin"){
	do_action('mbw_uploader_plugin');
}else{
	if((!empty($_FILES["Filedata"]) || !empty($_FILES["upload"]))&& mbw_check_api_file("editor")) { 		
		$upload_data	= mbw_file_upload(array("board_name"=>"N","table_name"=>"N","board_pid"=>"0","type"=>"editor"));
		$file_name		= $upload_data["name"];		
		if(!empty($upload_data["path"])){
			if(!empty($_FILES["upload"]) && !empty($_REQUEST['CKEditorFuncNum'])){
				$funcNum	= mbw_get_param('CKEditorFuncNum');
				$url			= mbw_get_image_url("path",$upload_data["path"]);
				if($funcNum=='json'){
					echo '{"filename":"'.esc_js($file_name).'","uploaded":1,"url":"'.esc_url($url).'"}';
				}else{
					$message		= "";
					echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction(".esc_js($funcNum).", '".esc_url($url)."', '".esc_js($message)."');</script>";
				}
			}else{
				$url = $_REQUEST["callback"].'&callback_func='.$_REQUEST["callback_func"];				
				$url .= "&sFileName=".rawurlencode($file_name);
				$url .= "&sFileURL=".rawurlencode(mbw_get_image_url("path",$upload_data["path"]));
				$url .= "&bNewLine=true";
				header('Location: '. $url);
			}
		}else echo $mstore->get_result_data("message");
	}else if($mstore->get_result_data("state")=="error"){
		echo $mstore->get_result_data("message");
	}	
}
do_action('mbw_uploader_api_footer');
exit;
?>