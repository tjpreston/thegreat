

<div id="leftcol">
	<?php echo $this->element('catalog/featured_and_recent'); ?>
</div>

<div id="content">

	<div class="header">
		<h1>Forgotten Your Password?</h1>
		<p class="intro">Please enter your email address below and we'll send you a link so you can create a new password. </p>
	</div>

	<div class="content-pad">
		<?php

			echo $this->Session->flash();

			echo $form->create('Customer', array('class' => 'horizontal-form', 'action' => 'forgotten_password'));

			echo $form->input('email', array(
				'label' => 'Your email address',
			));

			echo $this->Html->tag('div', $form->button('Continue'), array('class' => 'form-actions'));

			echo $form->end();

		?>
	</div>

</div>
<div class="clear"></div>