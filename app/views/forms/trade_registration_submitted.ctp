<?php
	$this->set('body_id', 'register');
	$this->set('title_for_layout', 'Request A Trade Account');
?>

<div id="leftcol">
	<?php echo $this->element('catalog/featured_and_recent'); ?>
</div>
<div id="content">

	<div class="header">
		<h1>Request a Trade Account</h1>
		<p class="intro">Register here for a trade account with Michel Herbelin.</p>
	</div>
	<div class="content-pad">

		<?php echo $this->Session->flash(); ?>

		<p><strong>Thank you.</strong> Your request for a trade account has successfully been submitted, and you will receive an email once your account has been activated.</p>

	</div>

</div>
<div class="clear"></div>