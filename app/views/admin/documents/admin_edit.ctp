<div id="admin-content">
	
	<div id="side-col">
		<ul>
			<li><a href="/admin/documents" class="icon-link back-link">Back to List</a></li>
		</ul>
	</div>
	
	<?php echo $form->create('Document', array('action' => 'save', 'id' => 'product-form', 'type' => 'file')); ?>
	
		<div class="panes">
			
			<?php echo $session->flash(); ?>
			
			<div id="header">
				<?php if (!empty($this->data['Document']['name'])): ?>
					<a href="/admin/documents/delete/<?php echo intval($this->data['Document']['id']); ?>" class="icon-link delete-link">Delete</a>
					<h1><?php echo h($this->data['Document']['name']); ?></h1>
				<?php else: ?>
					<h1>New Document</h1>
				<?php endif; ?>
			</div>
			
			<div id="pane-general" class="pane">
				<div class="fieldset-header"><span>General</span></div>				
				<div class="fieldset-box">
					<fieldset>
						
						<?php
						echo $form->input('id');
						echo $form->input('name');
						echo $form->input('display_name');
						echo $form->input('description');
						?>
						
						<?php
						if (!empty($this->data['Document']['web_path']))
						{
							$file = '<a href="' . $this->data['Document']['web_path'] . '" class="icon-link ' . $this->data['Document']['ext'] . '-link">' . $this->data['Document']['filename'] . '</a>';
						}
						?>

						<?php if (!empty($this->data['Document']['id'])): ?>
							
							<div class="input">
								<label for="void">File</label>
								<div class="image-preview">									
									<?php if (!empty($file)): ?>
										<div><?php echo $file; ?></div>
									<?php else: ?>
										<div>No file uploaded.</div>
									<?php endif; ?>
								</div>
							</div>

						<?php endif; ?>

						<?php
						$action = (!empty($this->data['Document']['id'])) ? 'Replace' : 'Upload';
						echo $form->input('file', array(
							'type' => 'file',
							'label' => $action . ' File'
						));
						?>

					</fieldset>
					<?php echo $form->submit('Save'); ?>
				</div>
				
			</div>
			
		</div>
		
	<?php echo $form->end(); ?>
	
</div>


