<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<title><?php echo $title_for_layout; ?></title>
		<meta http-equiv="content-type" content="text/html;charset=utf-8">
		<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.8.1/build/reset/reset-min.css" />
		<?php echo $html->css(array('admin', 'admin_popup', 'utils')); ?>
		<?php echo $html->script(array(
			'https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js',
			'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/jquery-ui.min.js',
			'vendors/jquery.tools.min.js', 'admin.js'
		)); ?>
		<?php echo $scripts_for_layout; ?>
	</head>
	<body>
		<div id="popup-master">
			<?php echo $content_for_layout; ?>
		</div>
		<?php echo $this->element('sql_dump'); ?>
	<body>
</html>