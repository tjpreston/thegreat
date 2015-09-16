<?php $html->css(array('vendors/jquery-ui-flick.css'), null, array('inline' => false)); ?>

<div id="admin-content">
	
	<div id="side-col">
		<ul id="admin-links">
			<li><a href="/admin/coupons" class="icon-link back-link">Back to List</a></li>
		</ul>
	</div>
	
	<?php echo $form->create('Coupon', array('action' => 'save', 'id' => 'product-form')); ?>
	
		<div class="panes">
			
			<?php echo $session->flash(); ?>			
			
			<div id="header">
				<?php if (!empty($this->data['Coupon']['code'])): ?>
					<a href="/admin/coupons/delete/<?php echo intval($this->data['Coupon']['id']); ?>" class="icon-link delete-link">Delete</a>
					<h1><?php echo h($this->data['Coupon']['code']); ?> Coupon</h1>
				<?php else: ?>
					<h1>New Coupon</h1>
				<?php endif; ?>
			</div>
			
			<div id="pane-general" class="pane">
				<div class="fieldset-header">Details</div>
				<div class="fieldset-box">
					<fieldset>
						
						<?php
						
						echo $form->input('id');
						echo $form->input('code', array('class' => 'smaller'));
						
						$infiniteUses = $form->input('infinite_uses', array(
							'type' => 'checkbox',
							'label' => 'No limit',
							'div' => 'checkbox',
							'value' => 1,
							'checked' => (isset($this->data['Coupon']['use_limit']) && is_null($this->data['Coupon']['use_limit'])) ? true : false
						));

						echo $form->input('use_limit', array('class' => 'tiny', 'div' => array('id' => 'use-limit'), 'after' => $infiniteUses));
						
						?>

						<div class="input text multiple">
							<p>Available</p>

							<?php

							foreach (array('from', 'to') as $dir)
							{
								echo $this->element('admin/date_input', array(
									'dir' => $dir,
									'value' => isset($this->data['Coupon']['active_' . $dir]) ? $this->data['Coupon']['active_' . $dir] : '',
									'field' => 'active_' . $dir,
									'input' => '[Coupon][active_' . $dir . ']'
								));
							}

							?>

						</div>

						<?php
						
						echo $form->input('notes', array(
							'type' => 'textarea',
							'label' => 'Notes<br /><span>(Internal use only)</span>'
						));
							
						echo $form->input('active');
						
						?>
						
					</fieldset>
					<?php echo $form->submit('Save'); ?>
				</div>
			</div>
		</div>
		
	<?php echo $form->end(); ?>
	
</div>

