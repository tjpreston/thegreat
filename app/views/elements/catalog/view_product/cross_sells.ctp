<?php if (!empty($record['CrossSells'])): ?>

<div id="cross-sells">
	
	<h2>We also recommend</h2>
	
	<?php $i = 1; ?>
		
	<?php foreach ($record['CrossSells'] as $prod): ?>
	
		<div class="cross-sell-prod">
			<img src="<?php echo $prod['Product']['main_tiny_image_path']; ?>" />
			<?php
			//debug($prod);
			$price = '&pound;' . number_format($prod['ProductPrice']['active_price'], 2);

			echo $form->hidden('Basket.' . $i . '.product_id', array('value' => $prod['Product']['id']));
			echo $form->input('Basket.' . $i . '.qty', array(
				'type' => 'checkbox',
				'label' => ' <span class="underline">' .$prod['ProductName']['name'] . ' </span> <span class="cross-sell-price orange">' . $price . '</span>',
				'value' => 1,
				'div' => 'input checkbox cross-sell-label'
			));
			?>
			
		</div>
		
		<?php $i++; ?>
	<div style="clear:right;"></div>
	<?php endforeach; ?>
		
</div>

<?php endif; ?>


