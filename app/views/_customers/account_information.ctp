<div id="leftcol">
	<?php echo $this->element('template/account_nav_panel'); ?>
	<?php echo $this->element('catalog/featured_and_recent'); ?>
</div>
<div id="content">
	<div class="header">
		<h1>Account Information</h1>
		<p class="intro">Manage and edit your account information.</p>
	</div>
	<div class="content-pad">
	<?php

		echo $this->Session->flash();

		echo $form->create('Customer', array('class' => 'horizontal-form', 'action' => 'save_account_information'));
		echo $form->input('company_name');
		echo $form->input('first_name');
		echo $form->input('last_name');
		echo $form->input('email');
		echo $form->input('phone');
		echo $form->input('mobile');

		echo $this->Html->tag('div', $form->button('Save'), array('class' => 'form-actions'));

		echo $form->end();

	?>
	</div>

</div>

<div class="clear"></div>