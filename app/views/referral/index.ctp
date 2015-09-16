<?php
	$this->set('body_id', 'referral');
	$this->set('title_for_layout', 'Send to a Friend');
?>




	
<div id="left-col">
	<?php echo $this->element('catalog/featured_and_recent'); ?>
</div>
<div id="right-content">

	<section id="referral" class="content">
		

		<h1>Send to a Friend</h1>
			
		<p class="intro">Email this product to your friends by completing their details:</p>
		<?php echo $session->flash(); ?>
		
		<div id="referral-product-box">
			<?php echo $this->element('catalog/product_item_tiny', array('product' => $record)); ?>
			<div class="clear"></div>
		</div>
		
		<?php echo $form->create('Referral', array('class' => 'form', 'url' => '/referral/send', 'id' => 'main-form')); ?>
		
			<?php echo $form->hidden('product_id'); ?>
			
			<?php $options = array(
				'between' => '<div class="input-box">',
				'after' => '</div>'
			); ?>
		
		<fieldset>
			<div class="heading">Your Details</div>
			<div class="inputs">
				<?php
				echo $form->input('sender_name', $options + array('label' => 'Name'));
				echo $form->input('sender_email', $options + array('label' => 'Email'));
				?>
			</div>
		</fieldset>
		
		<fieldset>
			<div class="heading">Your Friend's Details</div>
			<div class="inputs">
				<?php
				echo $form->input('recipient_name', $options + array('label' => 'Name'));
				echo $form->input('recipient_email', $options + array('label' => 'Email'));
				?>
			</div>
		</fieldset>

		<div class="divide"></div>
		
		<fieldset>
			<div class="inputs">
				<?php echo $form->input('message', array(
					'type' => 'textarea',
					'label' => 'Message <span class="smaller" style="font-size: 80%;">(optional)</span>'
				) + $options); ?>
			</div>
		</fieldset>

		<br/>
		
		<div class="right">
			<?php echo $form->submit('Send Now', array('class' => 'send-button')); ?>
		</div>
		<?php echo $form->end(); ?>
		
	</section>


</div>
<div class="clear"></div>