<div id="admin-content">
	
	<div id="side-col">
		<ul>
			<li><a href="/admin/customer_groups" class="icon-link back-link">Back to List</a></li>
		</ul>
	</div>
	
	<?php echo $form->create('CustomerGroup', array('action' => 'save', 'id' => 'product-form', 'type' => 'file')); ?>
	
		<div class="panes">
			
			<?php echo $session->flash(); ?>
			
			<div id="header">
				<?php if (!empty($this->data['CustomerGroup']['name'])): ?>
					<a href="/admin/customer_groups/delete/<?php echo intval($this->data['CustomerGroup']['id']); ?>" class="icon-link delete-link">Delete</a>
					<h1><?php echo h($this->data['CustomerGroup']['name']); ?></h1>
				<?php else: ?>
					<h1>New Customer Group</h1>
				<?php endif; ?>
			</div>
			
			<div id="pane-general" class="pane">
				<div class="fieldset-header"><span>General</span></div>				
				<div class="fieldset-box">
					<fieldset>						
						<?php
						echo $form->input('id');
						echo $form->input('name');
						/*
						echo $form->input('discount_amount', array(
							'after' => '%',
							'class' => 'tiny'
						));
						*/
						?>
					</fieldset>
					<?php echo $form->submit('Save'); ?>
				</div>
				
			</div>
			
		</div>
		
	<?php echo $form->end(); ?>
	
</div>
