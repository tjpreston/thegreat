<div id="admin-content">
	
	<div id="side-col">
		<p><a href="/admin/tax_rates" class="icon-link back-link">Back to List</a></p>
	</div>
	
	<?php echo $form->create('TaxRate', array('action' => 'save', 'id' => 'product-form')); ?>
	
		<div class="panes">
			
			<?php echo $session->flash(); ?>
			
			<div id="header">
				<?php if (!empty($this->data['TaxRate']['name'])): ?>
					<a href="/admin/tax_rates/delete/<?php echo intval($this->data['TaxRate']['id']); ?>" class="icon-link delete-link">Delete</a>
					<h1><?php echo h($this->data['TaxRate']['name']); ?></h1>
				<?php else: ?>
					<h1>New Tax Rate</h1>
				<?php endif; ?>
			</div>
			
			<div id="pane-general" class="pane">
				<div class="fieldset-header">General</div>				
				<div class="fieldset-box">
					<fieldset>
						<?php
						echo $form->input('id');
						echo $form->input('country_id', array(
							'empty' => array(0 => 'Please Select -------')
						));
						echo $form->input('name');
						echo $form->input('rate', array(
							'class' => 'smallest',
							'after' => '%'
						));
						?>
					</fieldset>
				</div>
				<?php echo $form->submit('Save'); ?>
			</div>
			
		</div>
		
	<?php echo $form->end(); ?>
	
</div>