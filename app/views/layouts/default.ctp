<?php
header('Content-type: text/html; charset=UTF-8');
?><!DOCTYPE html>
<html>
<head>
	<title>
		<?php if (!empty($title_for_layout) && $title_for_layout !== 'Home'): echo h($title_for_layout) . ' - '; endif; ?>
		<?php echo h(Configure::read('Site.title')); ?>
	</title>
	<?php echo $this->Html->charset('utf-8'); ?>
	<link rel="shortcut icon" href="/favicon.ico" />
	<meta name="viewport" content="width=1056" />
	<?php
		$metaKeywords = (!empty($metaKeywords)) ? $metaKeywords : Configure::read('Site.default_meta_keywords');
		if(!empty($metaKeywords)) echo $this->Html->meta('keywords', $metaKeywords);

		$metaDescription = (!empty($metaDescription)) ? $metaDescription : Configure::read('Template.default_meta_description');
		if(!empty($metaDescription)) echo $this->Html->meta('description', $metaDescription);

	echo $this->Html->css(array(
		'//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css',
		'vendors/normalize',
		'grid',
		'listings',
		'basket',
		'checkout',
		'customers',
		'forms',
		'base_styles',
		'greatbritishshop',
	)); ?>
	<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<?php echo $this->Html->script(array(
		'http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js',
		'vendors/jcarousellite_1.0.1.min',
		'vendors/superfish',
		'vendors/jquery.retina',
		'esper',
		'global',
	)); ?>
	<?php echo $scripts_for_layout; ?>
	<?php echo $this->element('template/google_analytics'); ?>
</head>

<body<?php if(!empty($body_id)) echo ' id="' . $body_id . '"'; ?>>

	<header class="container_24">
		<?php
			echo $this->element('template/top_nav');
			echo $this->element('template/header_main');
			echo $this->element('template/nav');
		?>
	</header>

	<section class="container_24" id="content">
		<?php echo $session->flash(); ?>
		<?php echo $content_for_layout; ?>
	</section>

	<footer id="sitefoot">
		<div class="container_24">
			<section class="grid_8 products">
				<h3>Our Products</h3>
				<?php $count = count($categories); ?>
				<?php $half = ceil($count / 2);?>
				<div class="grid_4 alpha">
					<?php
						foreach($categories as $k => $category){
							if($k < $half) {
								if($category['Category']['display_on_footer_nav'] == 0){
									continue;
								}

								$name = $category['CategoryName']['menu_name'];
								if(empty($name)){
									$name = $category['CategoryName']['name'];
								}

								echo $this->Html->link(
									$name,
									$category['CategoryName']['full_url']
								);
								echo '<br/>';
							}

						}
					?>
				</div>
				<div class="grid_4 omega">
					<?php
						foreach($categories as $k => $category){
							if($k >= $half) {
								if($category['Category']['display_on_footer_nav'] == 0){
									continue;
								}

								$name = $category['CategoryName']['menu_name'];
								if(empty($name)){
									$name = $category['CategoryName']['name'];
								}

								echo $this->Html->link(
									$name,
									$category['CategoryName']['full_url']
								);
								echo '<br/>';
							}

						}
					?>
				</div>
			</section>
			<section class="grid_4 about">
				<h3>About Us</h3>
				<ul>
					<li><a href="/pages/who-we-are">Who We Are</a></li>
					<li><a href="/pages/shop">Our Shop</a></li>
				</ul>
				<h3>Find Us On:</h3>
				<a href="https://www.facebook.com/thegreatbritishshopltd"><i class="fa fa-facebook-square fa-2x"></i></a>
				<a href="https://twitter.com/greatbritishop"><i class="fa fa-twitter-square fa-2x"></i></a>
				<!-- <a href="#"><i class="fa fa-pinterest-square fa-2x"></i></a> -->
			</section>
			<section class="grid_5 customer-services">
				<h3>Customer Services</h3>
				<ul>
					<li><a href="/contact">Contact Us</a></li>
					<li><a href="/pages/privacy">Privacy Policy</a></li>
					<li><a href="/pages/terms">Terms &amp; Conditions</a></li>
				</ul>
			</section>
			<section class="grid_7">
				<img src="/img/logo-footer.png" alt="The Great British Shop Ltd" class="retina" />
			</section>
		</div>
	</footer>

	<!-- <?php if(Configure::read('debug') === 0): ?>
	<script type="text/javascript">
	var popcc12_title = 'The Great British Shop Ltd Visitor Experience';
	var popcc12_report = 'greatbritishshop';
	</script>
	<script src="http://popcc12.popcornwebdesign.co.uk/js/popcc12.js"></script>
	<?php endif; ?> -->

	<?php echo $this->element('sql_dump'); ?>
</body>
</html>
