<?php
error_reporting(0);
@ini_set('display_errors',0);

if(!empty($_REQUEST['type'])){
	$type		= $_REQUEST['type'];
}else{
	$type		= "";
}
if(!empty($_REQUEST['encoding'])){
	$encoding		= $_REQUEST['encoding'];
}else{
	$encoding		= "UTF-8";
}
if(!empty($_REQUEST['target'])){
	$target		= $_REQUEST['target'];
}else{
	$target		= "";
}

if(function_exists('mb_internal_encoding')) mb_internal_encoding($encoding);


if(defined('MBW_UPLOAD_PATH')){
	$upload_path		= MBW_UPLOAD_PATH;
}else{
	$upload_path		= "../../../uploads/mangboard/";
}

function checkImagePath($path){
	global $upload_path;
	$size		= $_REQUEST['size'];
	if($size=="small"){
		$small_path		= substr($path,0,strrpos($path, "."))."_small".substr($path,strrpos($path, "."));
		if(is_file($upload_path.$small_path)) $path		= $small_path;
	}else if($size=="middle"){
		$middle_path		= substr($path,0,strrpos($path, "."))."_middle".substr($path,strrpos($path, "."));
		if(is_file($upload_path.$middle_path)) $path		= $middle_path;
	}
	return $path;
}

if(!empty($_REQUEST['path'])){
	$path				= $_REQUEST['path'];
	if($type=="download") $path				= base64_decode($path);
	$path				= trim($path);
	$path				= strip_tags($path);
	$path				= str_replace("..","",$path);
	$path				= str_replace("\\","",$path);	
	$path				= str_replace("#","＃",$path);
	$path				= str_replace("%","％",$path);

	$file_name		= basename($path);
	$file_array		= explode('.',$file_name);
	$file_ext			= array_pop($file_array);
	$file_ext			= strtolower($file_ext);

	$path				= str_replace($file_name,"",$path);	
	$path				= str_replace(".","",$path).$file_name;	
	$file_path		= $upload_path.$path;	

	global $mb_image_upload_files,$mb_board_upload_files;

	if(is_file($file_path)){		
		if(empty($mb_board_upload_files)) 	require("mb-config.php");
		if($type=="download"){	
			$check_ext		= $mb_board_upload_files;
		}else{
			$check_ext		= $mb_image_upload_files;
		}
		if(empty($check_ext)) $check_ext		= array("jpg","jpeg","png","gif","bmp");

		if(in_array($file_ext, $check_ext)){
			if($file_ext=="php") exit;
			if(strpos($file_name, 'F')===0 && strpos($file_name, '_')!==false){
				$file_name		= substr($file_name,strpos($file_name, '_')+1);
			}
			$file_name				= str_replace(",","",$file_name);

			if($type=="download"){
				$file_size			= filesize($file_path);
				header('Expires: 0');
				header('Pragma: public');
				header('Content-Length: ' . $file_size);
				header('Cache-Control: must-revalidate');
				header('Content-Description: File Transfer');
				header("Content-type: application/octet-stream");
				if(preg_match('/(MSIE|Trident)/i', $_SERVER['HTTP_USER_AGENT'])){
					header("Content-Disposition: attachment; filename=\"".rawurlencode($file_name)."\"");
				}else{
					header("Content-Disposition: attachment; filename=\"".rawurlencode($file_name)."\"");
				}
				@set_time_limit(0);
				if($file_size<52428800){
					ob_clean();
					flush();
					@readfile($file_path);
				}else{
					ob_flush();
					flush();
					$handle	= @fopen($file_path, "r");
					if($handle){
						$rate	= 1024*1024;
						while(!feof($handle)){
							echo fread($handle, $rate);
							ob_flush();
							flush();
						}
					}
				}
			}else{
				header("Content-type: image/".$file_ext);
				if(preg_match('/(MSIE|Trident)/i', $_SERVER['HTTP_USER_AGENT'])){
					header("Content-Disposition: inline; filename=\"".rawurlencode($file_name)."\"");
				}else{
					header("Content-Disposition: inline; filename=\"".rawurlencode($file_name)."\"");
				}
				if(!empty($_REQUEST['size'])) $path		= checkImagePath($path);
				if(defined('MBW_CONTENT_URL')){
					$upload_url			= MBW_CONTENT_URL."/uploads/mangboard/";
					if(strpos($upload_url, 'http') !== 0){
						if(strpos($upload_url, '/wp-content') === 0){
							$upload_url		= MBW_SITE_URL.$upload_url;
						}
					}
					header('Location: '. $upload_url.$path);
				}else{
					header('Location: '. $upload_path.$path);
				}
			}
		}
	}
}
exit;
?>