<html>
	<head>
		<title><?php echo $title_for_layout; ?></title>
		<meta http-equiv="content-type" content="text/html;charset=utf-8">
		<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.8.1/build/reset/reset-min.css" />
		<?php echo $this->Html->css('admin_print.css'); ?>
		<?php echo $scripts_for_layout; ?>
	</head>
	<body>
		<div id="print-master">
			<?php echo $content_for_layout; ?>
		</div>		
	<body>
</html>