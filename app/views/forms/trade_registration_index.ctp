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

		<p>To sign up for a trade account please fill in the form below. </p>
		<p><strong>Your account will need to be approved before you are able to log in as a trade user.</strong> </p>

		<?php echo $this->Form->create('Form', array('url' => $this->here, 'class' => 'horizontal-form')); ?>

		<?php
			echo $this->Form->input('company_name');
			echo $this->Form->input('first_name');
			echo $this->Form->input('last_name');
			echo $this->Form->input('email');
			echo $this->Form->input('phone');

			echo $form->input('password_main', array(
				'label' => 'Password',
				'type' => 'password',
				'value' => ''
			));
			echo $form->input('password_confirm', array(
				'label' => 'Confirm Password',
				'type' => 'password',
				'value' => ''
			));

			echo $this->Html->tag('div', $this->Form->button('Register'), array('class' => 'form-actions'));
		?>

		<?php echo $this->Form->end(); ?>

	</div>

</div>
<div class="clear"></div>