<div id="admin-content">
	
	<div id="side-col">
		<p><a href="/admin/stockists" class="icon-link back-link">Back to List</a></p>
	</div>
	
	<?php echo $form->create('Stockist', array('url' => $this->here, 'id' => 'product-form')); ?>
	
		<div class="panes">
			
			<?php echo $session->flash(); ?>
			
			<div id="header">
				<?php if (!empty($this->data['Stockist']['name'])): ?>
					<a href="/admin/stockists/delete/<?php echo intval($this->data['Stockist']['id']); ?>" class="icon-link delete-link">Delete</a>
					<h1><?php echo h($this->data['Stockist']['name']); ?></h1>
				<?php else: ?>
					<h1>New Stockist</h1>
				<?php endif; ?>
			</div>
			
			<div id="pane-general" class="pane">
				<div class="fieldset-header"><span>General</span></div>				
				<div class="fieldset-box">
					<fieldset>
						<?php
						echo $form->input('id');
						echo $form->input('name');
						echo $form->input('telephone', array('label' => 'Telephone Number'));
						echo $form->input('email', array('label' => 'Email Address'));
						echo $form->input('website', array('label' => 'Website'));
						
						if(!empty($this->data['Stockist']['id'])): ?>
							<div class="input">
								<?php echo $form->label('Affiliate Link'); ?>
								<div style="padding-top:5px; float: left;">
									<strong><?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/AFF' . $this->data['Stockist']['id']; ?></strong><br/>
									Give this affiliate URL to the stockist for inclusion on their website.
								</div>
							</div>
						<?php
						endif;
						?>
					</fieldset>
				</div>
				<div class="fieldset-header"><span>Postal Address</span></div>				
				<div class="fieldset-box">
					<fieldset>
						<?php
						echo $form->input('address_1', array('label' => 'Line 1'));
						echo $form->input('address_2', array('label' => 'Line 2'));
						echo $form->input('address_3', array('label' => 'Line 3'));
						echo $form->input('town', array('label' => 'Town'));
						echo $form->input('county', array('label' => 'County'));
						echo $form->input('postcode', array('label' => 'Postcode'));
						?>
				</div>
				<div class="fieldset-header"><span>Opening Hours</span></div>				
				<div class="fieldset-box">
					<fieldset>
						<?php
						echo $form->input('monday');
						echo $form->input('tuesday');
						echo $form->input('wednesday');
						echo $form->input('thursday');
						echo $form->input('friday');
						echo $form->input('saturday');
						echo $form->input('sunday');
						?>
						<?php echo $form->submit('Save'); ?>
					</fieldset>
				</div>
			</div>
		</div>
		
	<?php echo $form->end(); ?>
	
</div>