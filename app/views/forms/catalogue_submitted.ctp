<?php

$this->set('title_for_layout', 'Request a Catalogue');

// Don't show the "Free Delivery" banner on these pages
$this->set('show_delivery_banner', false);

?>
<div id="content" class="wide">
	<?php echo $this->element('template/static-box'); ?>

	<div id="static-text">

		<h1>Request a Catalogue</h1>
		<img src="/img/static-content-divider.gif" alt="" style="margin-top:10px" />
		
		<p><strong>Thank you.</strong> We have received your request for a catalogue, and you should receive one in the post shortly.</p>
		<p>In the meantime, why not browse our online catalogue? Simply use the navigation links at the top of this page, or type in the search box.</p>

	</div>
</div>
