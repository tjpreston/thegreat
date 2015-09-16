<div id="admin-content">
	
	<div id="side-col">
		<p><a href="/admin/manufacturers" class="icon-link back-link">Back to List</a></p>
	</div>
	
	<?php echo $form->create('Manufacturer', array('action' => 'save', 'id' => 'product-form', 'type' => 'file')); ?>
	
		<div class="panes">
			
			<?php echo $session->flash(); ?>
			
			<div id="header">
				<?php if (!empty($this->data['Manufacturer']['name'])): ?>
					<a href="/admin/manufacturers/delete/<?php echo intval($this->data['Manufacturer']['id']); ?>" class="icon-link delete-link">Delete</a>
					<h1><?php echo h($this->data['Manufacturer']['name']); ?></h1>
				<?php else: ?>
					<h1>New Manufacturer</h1>
				<?php endif; ?>
			</div>
			
			<div id="pane-general" class="pane">
				<div class="fieldset-header"><span>General</span></div>				
				<div class="fieldset-box">
					<fieldset>
						
						<?php
						echo $form->input('id');
						echo $form->input('name');
						echo $form->input('url');
						echo $form->input('featured');
						echo $form->input('in_nav', array('label' => 'Show in navigation'));
						echo $form->input('in_footer', array('label' => 'Show in footer'));
						echo $form->input('sort', array('class' => 'tiny'));
						?>
						
						<?php
						if (!empty($this->data['Manufacturer']['web_path']))
						{
							$image = '<img src="' . $this->data['Manufacturer']['web_path'] . '" alt="" /><br />';
						}
						?>
						<div class="input">
							<label for="void">Image</label>
							<div class="image-preview">									
								<?php if (!empty($image)): ?>
									<div><?php echo $image; ?></div>
									<div><a href="/admin/manufacturers/delete_image/<?php echo $this->data['Manufacturer']['id']; ?>/list">Delete Image</a></div>
								<?php else: ?>
									<div>No image uploaded.</div>
								<?php endif; ?>
							</div>
						</div>
						<?php
						echo $form->input('image', array(
							'type' => 'file',
							'label' => 'Upload Image'
						));
						?>
					</fieldset>
				</div>
			</div>
			
			<div class="fieldset-box">
				<?php echo $form->submit('Save', array('div' => 'submit single-line')); ?>
			</div>
			
		</div>
		
	<?php echo $form->end(); ?>
	
</div>