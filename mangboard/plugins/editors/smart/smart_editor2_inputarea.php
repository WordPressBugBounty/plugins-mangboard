<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title>Smart Editor&#8482; WYSIWYG Mode</title>
<link href="<?php echo MBW_PLUGIN_URL;?>assets/css/bootstrap3-grid.css" rel="stylesheet" type="text/css">
<link href="<?php echo MBW_PLUGIN_URL;?>assets/css/style.css" rel="stylesheet" type="text/css">
<?php
	$font_url			= mbw_get_vars("mb-font-url");
	if(!empty($font_url)){
		echo '<link href="'.$font_url.'" rel="stylesheet" type="text/css">';
	}
	if(is_dir(MBW_PLUGIN_PATH."plugins/editor_composer/")){
		echo '<link href="'.MBW_PLUGIN_URL.'plugins/editor_composer/css/style.css" rel="stylesheet" type="text/css">';
	}
	$font_size			= mbw_value_filter(mbw_get_vars("mb-font-size"));
	if(!empty($font_size)){
		echo '<style type="text/css">body,.se2_inputarea{font-size:'.esc_attr($font_size).' !important;}</style>';
	}
?>
</head>
<body class="se2_inputarea mb-<?php echo esc_attr(mbw_get_vars("device_type"));?> mb-editor mb-editor-smart" style="height:0;-webkit-nbsp-mode:normal"></body>
</html>