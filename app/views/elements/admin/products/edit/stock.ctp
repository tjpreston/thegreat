<div id="pane-stock" class="pane">

	<?php if (!empty($options)): ?>
	
		<div class="info-message">Stock control managed by variations.</div>
	
	<?php else: ?>
		
		<div class="fieldset-header"><span>Stock Control</span></div>
		<div class="fieldset-box">
			<fieldset>
				<?php
				echo $form->input('stock_base_qty', array('class' => 'tiny', 'label' => 'Current Qty'));
				echo $form->input('stock_lead_time', array('class' => 'smaller'));
				//echo $form->input('stock_min_order_qty', array('class' => 'tiny', 'label' => 'Min Order Qty'));
				//echo $form->input('stock_max_order_qty', array('class' => 'tiny', 'label' => 'Max Order Qty'));
				
				//echo $form->input('stock_allow_backorders', array(
				//	'label' => 'Allow Backorders',
				//));

				?>
			</fieldset>
		</div>
	
	<?php endif; ?>
	
</div>

