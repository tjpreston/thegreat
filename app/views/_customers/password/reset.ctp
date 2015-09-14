

<div id="leftcol">
	<?php echo $this->element('catalog/featured_and_recent'); ?>
</div>
<div id="content">

	<div class="header">
		<h1>Reset Your Password</h1>
		<p class="intro">Please enter a new password for your account.</p>
	</div>

	<div class="content-pad">

		<?php

			echo $form->create('Customer', array('class' => 'horizontal-form', 'action' => 'reset_password'));

			echo $form->hidden('hash', array('value' => $hash));
			
			echo $form->input('Customer.password_main', array(
				'label' => 'New Password', 
				'type' => 'password', 
				'value' => '',
				'between' => '<div class="input-box">',
				'after' => '</div>'
			));
			
			echo $form->input('Customer.password_confirm', array(
				'label' => 'Confirm Password', 
				'type' => 'password', 
				'value' => '',
				'between' => '<div class="input-box">',
				'after' => '</div>'
			));

			echo $this->Html->tag('div', $form->button('Continue'), array('class' => 'form-actions'));

			echo $form->end();

		?>


	</div>

</div>


	<div class="clear"></div>


	
	



	
