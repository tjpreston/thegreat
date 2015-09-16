<div class="grid_8">
	<h1>Contact Us</h1>
	<h2><?php echo Configure::read('Site.name'); ?> Ltd</h2>
	<p>	
		17 The Old High St<br />
		Folkestone<br />
		Kent<br />
		CT20 1RL
	</p>

	<p><strong>Tel:</strong><?php echo Configure::read('Site.tel'); ?></p>
	

	<p><strong>Email:</strong> <a href="mailto:<?php echo Configure::read('Site.email'); ?>"><?php echo Configure::read('Site.email'); ?></a></p>
	<p><strong>Press Enquiries:</strong> <a href="mailto:press@thegreatbritishshop.com">press@thegreatbritishshop.com</a></p>
</div>

<div id="enquiries" class="grid_16">

	<?php
	$options = array(
		'between' => '<div class="input-box">',
		'after' => '</div>'
	);
	?>
	
	<?php echo $form->create('Contact', array('class' => 'form', 'url' => '/contact/send')); ?>

	<fieldset>
		
		<div class="inputs">		
		
			<?php
			echo $form->input('name', $options);
			// echo $form->input('company', $options);
			echo $form->input('telephone', $options);
			// echo $form->input('subject', $options);
			echo $form->input('email', $options);
			echo $form->input('enquiry', array('rows' => '5', 'style' => 'width: 400px; border: 1px solid #D7D7D7;') + $options);
			?>

		</div>
	</fieldset>
	
	<?php
	echo $this->Form->button('<span class="face1">Send</span> <span class="face2">Enquiry</span>', array('class' => 'send-enquiry'));
	echo $form->end();
	?>
	
</div>


