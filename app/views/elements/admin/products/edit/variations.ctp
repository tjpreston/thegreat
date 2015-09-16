
<script>

$(function() {

	$("#varlinks li").click(function() {
		var id = $(this).attr("id").substring(8);
		$(".varstock-data").hide().filter("#varstock-" + id).show();
		$("#varlinks li").removeClass("on").filter("#varlink-" + id).addClass("on");
	});

	$(".view-var-image").tooltip();

	$(".var-image-edit-link").click(function() {
		$(".var-image-add").hide();
		var id = $(this).attr("id").substring(20);
		var editbox = $("#var-image-edit-" + id);
		var display = (editbox.css("display") == "none") ? "inline" : "none";
		editbox.css("display", display);
	});

	$(".var-image-add-link").click(function() {
		$(".var-image-edit").hide();
		var id = $(this).attr("id").substring(19);
		var addbox = $("#var-image-add-" + id);
		var display = (addbox.css("display") == "none") ? "inline" : "none";
		addbox.css("display", display);
	});

	$('.var-img-sort').disableSelection().sortable({
		handle: '.slot'
	}).find('.slot').css('cursor', 'move');

	$('#product-form').submit(function(){
		$('.var-img-sort').each(function(){
			var sortField = $(this).parent().find('.sort-field');
			var sortOrder = $(this).sortable('toArray');
			sortOrder = sortOrder.join(',');

			sortField.val(sortOrder);
		});

		return true;
	});

	$('#varlink-images').click();

	/*

	update: function() {
		var order = $(this).sortable('toArray');
		order = order.join();

		$.ajax({
			data: {
				order: order
			},
			url: '<?php echo Router::url(array('controller' => 'blocks', 'action' => 'save_order', 'admin' => true)); ?>',
			type: 'post'
		});
	},

	*/

});

</script>



<div id="pane-variations" class="pane">
				
	<?php if (!empty($options)): ?>

		<div class="fieldset-header">
			<span>Variations Matrix</span>
			<ul id="varlinks">
				<li id="varlink-general" class="on"><span>General</span></li>
				<li id="varlink-stock"><span>Stock</span></li>
				<li id="varlink-pricing"><span>Pricing</span></li>
				<li id="varlink-images"><span>Images</span></li>
			</ul>
			</div>
		<div class="fieldset-box">
			<fieldset>
				<?php echo $this->element('admin/products/variations_stock'); ?>
			</fieldset>
		</div>
				
	<?php endif; ?>
				
	<?php echo $form->hidden('ProductOption.hash', array('id' => 'vars-sort-order-data')); ?>
	
	<?php $languageID = 1; ?>
	
	<?php if (!empty($options)): ?>
		<?php foreach ($options as $k => $option): ?>
			
			<?php
			echo $form->hidden('ProductOption.' . $k . '.id', array(
				'value' => $option['ProductOption']['id']
			));
			echo $form->hidden('ProductOption.' . $k . '.product_id', array(
				'value' => $option['ProductOption']['product_id']
			));
			echo $form->hidden('ProductOption.' . $k . '.custom_option_id', array(
				'value' => $option['ProductOption']['custom_option_id']
			));
			?>
			
			<div class="fieldset-header">
				<span><?php echo h($option['CustomOptionName']['name']); ?></span>
				<a href="/admin/products/delete_product_option/<?php echo intval($option['ProductOption']['id']); ?>"><img src="/img/icons/delete.png" style="margin-top: -2px;" /></a>
			</div>
			
			<div class="fieldset-box">
				
				<?php // echo $this->element('admin/language_nav'); ?>
				<div class="fieldsets">
					<?php foreach ($languages as $languageID => $languageName): ?>
						<fieldset>
							<?php
							echo $form->hidden('ProductOption.' . $k . '.ProductOptionName.' . $languageID . '.id', array(
								'value' => $option['ProductOptionName'][$languageID]['id']
							));
							echo $form->hidden('ProductOption.' . $k . '.ProductOptionName.' . $languageID . '.product_option_id', array(
								'value' => $option['ProductOptionName'][$languageID]['product_option_id']
							));
							echo $form->hidden('ProductOption.' . $k . '.ProductOptionName.' . $languageID . '.language_id', array(
								'value' => $option['ProductOptionName'][$languageID]['language_id']
							));
							echo $form->input('ProductOption.' . $k . '.ProductOptionName.' . $languageID . '.name', array(
								'type' => 'text',
								'value' => $option['ProductOptionName'][$languageID]['name']
							));
							?>
						</fieldset>
					<?php endforeach; ?>
				</div>
				
				<?php echo $form->input('ProductOption.' . $k . '.sort', array(
					'type' => 'text',
					'class' => 'tiny',
					'value' => $option['ProductOption']['sort']
				)); ?>
				
				<table class="option-table">
					<tr>
						<td>
							
							<ul class="existing-options-value-list option-value-list">
								<?php $i = 1; ?>
								<?php foreach ($option['ProductOptionValue'] as $kv => $value): ?>
									
									<?php $LIid = ' id="sort_' . intval($option['ProductOption']['id']) . '-' . intval($option['ProductOptionValue'][$kv]['ProductOptionValue']['id']) . '"'; ?>
									
									<li<?php echo $LIid; ?>>
										<?php echo $value['CustomOptionValueName']['name']; ?>
										<a href="/admin/products/delete_product_option_value/<?php echo intval($option['ProductOptionValue'][$kv]['ProductOptionValue']['id']); ?>"><img src="/img/icons/bullet_delete.png" /></a>
									</li>
									
									<?php $i++; ?>
								<?php endforeach; ?>
							</ul>
							
							<ul>
								<?php echo $this->element('admin/products/option_row', array(
									'option' => $option,
									'k' => $k,
									'kv' => 'new',
									'languageID' => $languageID
								)); ?>
							</ul>

						</td>
					</tr>
				</table>
			</div>							
			
		<?php endforeach; ?>
	<?php endif; ?>
	
	
	
	<div id="new-option-link" class="fieldset-box">
		<p><a href="#">Add new option</a></p>
	</div>
		
	<div id="new-option-box" style="display: none;">
		<div class="fieldset-header">
			<span>New Option</span>
			<a id="cancel-new-option-link" href="#">Cancel</a>
		</div>
		<div class="fieldset-box">
			<p style="margin-bottom: 20px;"><img src="/img/icons/exclamation.png" style="vertical-align: middle;" /> Adding a new option will clear the variation matrix.</p>
			<?php // echo $this->element('admin/language_nav'); ?>
			<?php echo $form->hidden('ProductOption.new.product_id', array('value' => $record['Product']['id'])); ?>
			<div class="fieldsets">
				<?php foreach ($languages as $languageID => $languageName): ?>
					<fieldset>
						<?php
						echo $form->hidden('ProductOption.new.ProductOptionName.' . $languageID . '.language_id', array(
							'value' => $languageID
						));
						echo $form->input('ProductOption.new.ProductOptionName.' . $languageID . '.name', array(
							'type' => 'text'
						));
						?>
					</fieldset>
				<?php endforeach; ?>
			</div>
			<?php
			echo $form->input('ProductOption.new.custom_option_id', array(
				'id' => 'new-option',
				'type' => 'select',
				'empty' => array(0 => 'Please Choose ----------'),
				'options' => $customOptions,
				'selected' => 0
			));
			echo $form->input('ProductOption.new.sort', array('type' => 'text', 'class' => 'tiny'));
			
			echo $form->input('ProductOption.new.initial_custom_option_value_id', array(
				'id' => 'new-option-initial-value',
				'type' => 'select',
				'empty' => array(0 => 'Please Choose ----------'),
				'label' => 'Initial Value',
				'options' => array()
			));
			?>
		</div>
	</div>
	
	<script>
	
	var optionValues = [];
	
	<?php foreach ($customOptionValuesList as $customOptionID => $values): ?>
	
		optionValues[<?php echo $customOptionID; ?>] = {		
		<?php foreach ($values as $valueID => $valueName): ?>
			<?php echo $valueID; ?>: "<?php echo h($valueName); ?>",
		<?php endforeach; ?>
		}
	
	<?php endforeach; ?>
	
	$(function() {
		$("#new-option").change(function() {
			var el = $("#new-option-initial-value")[0];
			el.options.length = 0;
			
			var id = $(this).val();
			
			if (id == 0) {
				return;
			}
			var valueItems = optionValues[id];
			
			$.each(valueItems, function (k, v) {
				el.options[el.options.length] = new Option(v, k);
			});
			
		});
			
	});
	
	
	</script>
	
</div>

