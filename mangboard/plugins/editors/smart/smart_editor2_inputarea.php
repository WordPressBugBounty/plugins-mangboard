<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title>Smart Editor&#8482; WYSIWYG Mode</title>
<?php
	mbw_enqueue_style('mb-bootstrap3-grid', MBW_PLUGIN_URL.'assets/css/bootstrap3-grid.css');
	mbw_enqueue_style('mb-assets-style', MBW_PLUGIN_URL.'assets/css/style.css');
	wp_print_styles(array('mb-bootstrap3-grid', 'mb-assets-style'));

	$font_url			= mbw_get_vars("mb-font-url");
	if(!empty($font_url)){
		mbw_enqueue_style('mb-web-font', esc_url_raw($font_url));
		wp_print_styles('mb-web-font');
	}
	if(is_dir(MBW_PLUGIN_PATH."plugins/editor_composer/")){
		mbw_enqueue_style('mb-editor-composer', MBW_PLUGIN_URL.'plugins/editor_composer/css/style.css');
		wp_print_styles('mb-editor-composer');
	}	
	$font_size			= mbw_value_filter(mbw_get_vars("mb-font-size"));
	if(!empty($font_size)){
		echo '<style type="text/css">body,.se2_inputarea{font-size:'.esc_attr($font_size).' !important;}</style>';
	}
?>
</head>

<body class="se2_inputarea mb-<?php echo esc_attr(mbw_get_vars("device_type"));?> mb-editor mb-editor-smart" style="height:0;-webkit-nbsp-mode:normal"></body>
</html>