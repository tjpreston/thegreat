<?php
	$this->set('body_id', 'error404');
?>

<div id="leftcol">
	<?php echo $this->element('template/customer_services'); ?>
	<?php echo $this->element('catalog/featured_and_recent'); ?>
</div>

<div id="content">

	<h1>Page Not Found</h1>
	<?php echo $this->element('template/breadcrumbs'); ?>
	<div class="product-paging"></div>

	<div id="static-text" style="width: 90%">
		<p class="headline">It would appear the page that you're looking for no longer exists.</p>
		<p>We apologise for this inconvenience, and suggest that you might instead like to <a href="/">visit our homepage</a> for our latest promotions, or use the search box above to find what you're looking for.</p>
	</div>

</div>

<div class="clear"></div>
