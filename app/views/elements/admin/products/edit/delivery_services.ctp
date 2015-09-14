<div id="pane-delivery" class="pane">
	<div class="fieldset-header">Available Delivery Services</div>
	<div class="fieldset-box">
		
		<?php if (!empty($services)): ?>
			<?php foreach ($services as $service): ?>
				
				<?php echo $form->input('ShippingCarrierDeliveryService.' . $service['ShippingCarrierService']['id'], array(
					'type' => 'checkbox',
					'label' => $service['ShippingCarrierService']['name'],
					'value' => 1,
					'checked' => !empty($availableServices[$service['ShippingCarrierService']['id']])
				)); ?>

			<?php endforeach; ?>
		<?php endif; ?>
		<?php echo $form->submit('Save', array('div' => 'submit stay-left')); ?>
	</div>
</div>