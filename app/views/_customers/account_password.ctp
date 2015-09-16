<div id="leftcol">
	<?php echo $this->element('template/account_nav_panel'); ?>
	<?php echo $this->element('catalog/featured_and_recent'); ?>
</div>
<div id="content">
	<?php $options = array(
		'between' => '<div class="input-box">',
		'after' => '</div>',
		'type' => 'password'
	); ?>

	<div class="header">
		<h1>Account Password</h1>
		<p class="intro">Change your account password.</p>
	</div>

	<div class="content-pad">
	<?php

		echo $this->Session->flash();

		echo $form->create('Customer', array('class' => 'horizontal-form', 'url' => '/customers/save_account_password'));
		echo $form->input('password_current', array('label' => 'Current Password', 'type' => 'password'));
		echo $form->input('password_main', array('label' => 'New Password', 'type' => 'password'));
		echo $form->input('password_confirm', array('label' => 'Confirm Password', 'type' => 'password'));

		echo $this->Html->tag('div', $form->button('Save'), array('class' => 'form-actions'));

		echo $form->end();

	?>
	</div>

</div>
<div class="clear"></div>