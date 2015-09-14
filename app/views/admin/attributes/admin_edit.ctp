<div id="admin-content">
	
	<div id="side-col">
		<ul id="admin-links">
			<li><a href="/admin/attributes" class="icon-link back-link">Back to List</a></li>
		</ul>
	</div>
	
	<?php
	
	$action = (!empty($record)) ? 'save' : 'create';
	echo $form->create('Attribute', array('id' => 'custom-option-form', 'action' => $action));
	
	?>
	
		<?php echo $form->input('id'); ?>
	
		<div class="panes">
			
			<?php echo $session->flash(); ?>
			
			<div id="header">
				<?php if (!empty($this->data['AttributeName'][Configure::read('Languages.main_lang_id')]['name'])): ?>
					<a href="/admin/attributes/delete/<?php echo intval($this->data['Attribute']['id']); ?>" class="icon-link delete-link">Delete</a>
					<h1><?php echo h($this->data['AttributeName'][Configure::read('Languages.main_lang_id')]['name']); ?> Attribute</h1>
				<?php else: ?>
					<h1>New Attribute</h1>
				<?php endif; ?>
			</div>
			
			<div id="pane-desc" class="pane">
			<div class="fieldset-header"><span>General</span></div>
			<div class="fieldset-box">
				<?php if (empty($record)): ?>
					<p style="margin-bottom: 12px;"><img src="/img/icons/information.png" style="vertical-align: middle;" /> Please note you must save this option before you can add values.</p>
				<?php endif; ?>
				<?php // echo $this->element('admin/language_nav'); ?>
				<div class="fieldsets">
						
					<?php foreach ($languages as $languageID => $languageName): ?>
						<fieldset>
							
							<?php
							
							echo $form->hidden('AttributeName.' . $languageID . '.language_id', array('value' => $languageID));		
							
							if (!empty($record['Attribute']['id']))
							{
								echo $form->hidden('AttributeName.' . $languageID . '.attribute_id', array(
									'value' => $record['Attribute']['id']
								));
							}
							
							if (!empty($record['AttributeName'][$languageID]['id']))
							{
								echo $form->input('AttributeName.' . $languageID . '.id', array(
									'value' => $record['AttributeName'][$languageID]['id']
								));
							}
							
							$name = (!empty($this->data['AttributeName'][$languageID]['name'])) ? $this->data['AttributeName'][$languageID]['name'] : '';
							echo $form->input('AttributeName.' . $languageID . '.name', array(
								'value' => $name
							));
							
							$displayname = (!empty($this->data['AttributeName'][$languageID]['display_name'])) ? $this->data['AttributeName'][$languageID]['display_name'] : '';
							echo $form->input('AttributeName.' . $languageID . '.display_name', array(
								'value' => $displayname
							));
							
							?>
							
						</fieldset>
					<?php endforeach; ?>
											
					
					<?php if (!empty($record)): ?>
					
						<p style="margin-top: 6px;"><img src="/img/icons/information.png" style="vertical-align: middle;" /> 
							Click and drag to sort the values and use the checkboxes to delete them.
						</p>
					
						<div id="cat-sorts">
							<ul id="cat-sort-<?php echo intval(1); ?>">
								
								<?php if (!empty($this->data['AttributeValue'])): ?>
									<?php foreach ($this->data['AttributeValue'] as $k => $value): ?>
										<li id="sort_<?php echo intval($value['AttributeValue']['attribute_id']); ?>-<?php echo intval($value['AttributeValue']['id']); ?>">
											<?php
											$name = $value['AttributeValueName'][Configure::read('Languages.main_lang_id')]['name'];
											echo $form->text('void', array(
												'id' => 'value-' . $value['AttributeValue']['id'],
												'class' => 'value-input',
												'style' => 'width: 200px;',
												'value' => $name
											));
											?>
											<input type="checkbox" name="data[ValueDelete][<?php echo intval($value['AttributeValue']['id']); ?>]" value="1" style="float: right; margin-top: 3px;" />
										</li>
									<?php endforeach; ?>
								<?php endif; ?>										
								
								<li id="sort_<?php echo intval($record['Attribute']['id']); ?>-new">
									<?php
									echo $form->text('void', array(
										'id' => 'value-new',
										'class' => 'value-input',
										'style' => 'width: 200px;'								
									));
									?>
									<span style="float: right; margin-top: 3px;">New</span>
								</li>
								
							</ul>
						</div>
						
						<?php echo $form->hidden('Attribute.value_names_json', array('id' => 'value-names-json')); ?>
						<?php echo $form->hidden('Attribute.sort_hash', array('id' => 'sort-order-data')); ?>
												
					<?php endif; ?>
					
					<script type="text/javascript">

					$(function() {
						
						var activeLang = <?php echo Configure::read('Languages.main_lang_id'); ?>;
						var names = <?php echo $json; ?>;
						
						$("ul.lang-nav").tabs("div.fieldsets > fieldset", {
							onClick: function(e) {
								if (e.originalTarget !== undefined) {
									activeLang = e.originalTarget.id.substring(5);
									for (var key in names) {
										$("#value-" + key).val(names[key][activeLang]);
									}
								}
							}
						});
						
						$(".value-input").keyup(function() {
							var id = this.id.substring(6);
							names[id][activeLang] = $(this).val();
						})
						
						$("#cat-sorts ul").sortable({
							axis: "y"
						});
						
						$("#custom-option-form").submit(function() {
							var hash = "";
							$("#cat-sorts ul").each(function() {
								hash += $(this).sortable("serialize") + ";";
							});
							$("#sort-order-data").val(hash.substring(0, hash.length - 1));
							
							$("#value-names-json").val(JSON.stringify(names));			
						})
						
					});
					
					</script>

				</div>
			</div>
			
			<div class="fieldset-box">
				<?php echo $form->submit('Save', array('div' => 'submit single-line')); ?>
			</div>
			
		</div>
		
	<?php echo $form->end(); ?>
	
</div>


