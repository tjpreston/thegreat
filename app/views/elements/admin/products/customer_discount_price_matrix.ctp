<?php if (!empty($options)): ?>
	<div class="info-message">These customer discounts will not be used as variations have been saved.</div>
<?php endif; ?>

<div id="discount-matrix"<?php echo (!empty($options)) ? ' class="void"' : ''; ?>>

	<div class="fieldset-header">
		<span>Customer Discounts</span>
	</div>
		
	<div class="fieldset-box">
		<div id="customer-pricing" class="fieldsets">

	<?php $j = 0; ?>

	<?php foreach ($groups as $k => $group): ?>

		<div>
		
			<h3><?php echo h($group['CustomerGroup']['name']); ?></h3>
			
			<?php 
			$prevQty = 0; 
			$i = 0;
			?>
			
			<?php foreach ($group[$model] as $d => $discount): ?>
			
				<?php
				
				echo $form->hidden($model . '.' . $j . '.id', array(
					'value' => $discount['id']
				));
				echo $form->hidden($model . '.' . $j . '.' . $fk, array(
					'value' => $fkID
				));
				echo $form->hidden($model . '.' . $j . '.customer_group_id', array(
					'value' => $group['CustomerGroup']['id']
				));
				echo $form->hidden($model . '.' . $j . '.min_qty', array(
					'value' => $discount['min_qty']
				));
				
				$label  = $discount['min_qty'];			
				$label .= (!empty($group[$model][$i + 1])) ? ' - ' . ($group[$model][$i + 1]['min_qty'] - 1) : '+';
				
				echo $form->input($model . '.' . $j . '.discount_amount', array(
					'label' => $label,
					'class' => 'tiny',
					'value' => $discount['discount_amount'],
					'after' => '% <a href="/admin/' . Inflector::underscore($model) . 's/delete_customer_tier/' . $discount['id'] . '"><img src="/img/icons/bullet_delete.png" alt="Remove" /></a>'
				));
				
				$prevQty = $discount['min_qty'];
				
				$i++;
				$j++;
				
				?>
				
			<?php endforeach; ?>
			
			<div class="add-tier">
				<p><a href="#">Add tier</a></p>
				<div style="display: none;">
					<?php
					echo $form->hidden($model . '.' . $j . '.' . $fk, array(
						'value' => $fkID
					));
					echo $form->hidden($model . '.' . $j . '.customer_group_id', array(
						'value' => $group['CustomerGroup']['id']
					));
					echo $form->text($model . '.' . $j . '.min_qty', array(
						'label' => false,
						'class' => 'new-qty smallest',
						'value' => 'Qty'
					));
					echo $form->text($model . '.' . $j . '.discount_amount', array(
						'label' => false,
						'class' => 'tiny'
					)); 
					$j++;
					?>% <a class="cancel-new-tier" href="#"><img src="/img/icons/bullet_delete.png" alt="Remove" /></a>
				</div>
			</div>
			
		</div>


	<?php endforeach; ?>

		</div>
	</div>

</div>



			