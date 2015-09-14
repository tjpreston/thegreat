
<div class="basket-desc">
	
	<a href="/<?php echo $item['ProductMeta']['url']; ?>">
		<img class="tiny-basket-image" src="<?php echo $item['Product']['main_tiny_image_path']; ?>" />
	</a>
	
	<div class="basket-prod-details">
		<div class="basket-prod-name">
			<a href="/<?php echo $item['ProductMeta']['url']; ?>">
				<?php echo h($item['ProductName']['name']); ?><?php
					if(!empty($item['ProductOptionStock'])){
						echo ': ' . h($item['ProductOptionStock']['name']);
					}
				?>
			</a>
		</div>
		<?php if (Configure::read('Giftwrapping.enabled') && count($giftwrapProducts) === 1): ?>
			<div class="giftwrap-details">
				<?php $gp = $giftwrapProducts[0]; ?>
				<?php if (!empty($item['BasketItem']['giftwrap_product_id'])): ?>
					<a href="/basket/remove_giftwrap/<?php echo intval($item['BasketItem']['id']); ?>" class="giftwrap-link remove"><?php echo Configure::read('Giftwrapping.rem_msg'); ?></a>
					


				<?php else: ?>
					<a href="/basket/add_giftwrap/<?php echo intval($item['BasketItem']['id']); ?>/<?php echo intval($gp['GiftwrapProduct']['id']); ?>" class="giftwrap-link add"><?php echo Configure::read('Giftwrapping.add_msg'); ?></a>
				<?php endif; ?>
			</div>	
		<?php endif; ?>
	</div>
	
	<?php if (!empty($item['GroupedProducts'])): ?>
		<div>
			<?php foreach ($item['GroupedProducts'] as $grpItem): ?>
				<p>
					<?php echo h($grpItem['ProductName']['name']); ?>
				</p>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>



	<?php if (!empty($item['BasketItem']['giftwrap_product_id'])): ?>
		<div class="custom-text-container">
			<?php $text = !empty($item['BasketItem']['custom_text']) ? $item['BasketItem']['custom_text'] : ''; ?>
			<?php if (empty($item['BasketItem']['custom_text'])): ?>
				<a href="#" class="show-hide-custom-text">Add a gift message (free of charge)</a>
			<?php else:?>
				<?php echo h($text);?><br />
				<a href="#" class="show-hide-custom-text">Edit gift message</a>
			<?php endif; ?>
			<div class="custom-text-input">
				<?php echo $form->input($k . '.custom_text',array(
					'id' => 'custom-text',
					'value' => $text,
					'rows' => ($item['BasketItem']['qty'] +1),
					'label' => 'Gift Card Text (optional)'
				)); ?>
				<button class="send-button dual" type="submit"><span class="face1">Save</span></button>
			</div> 
		</div>
	<?php endif; ?>
	
</div>
	

<div class="basket-price">
	<p>
		<?php echo $activeCurrencyHTML; ?> <?php echo number_format(floatval($item[$key]['price']), 2); ?>
		<?php if (!empty($item['BasketItem']['giftwrap_product_id'])): ?>
			<br/>
			<span class="giftwrap-details giftwrap-price"><?php echo $activeCurrencyHTML; ?> <?php echo number_format(Configure::read('Giftwrapping.price'), 2); ?></span>
		<?php endif; ?>
	</p>
</div>


<div class="basket-qty">

	<?php if ($caller == 'basket'): ?>

		<script type="text/javascript">
		$(function() {
			var qtyBox = $("#qty-input-<?php echo intval($item[$key]['id']); ?>");
			$("#decrease-qty-<?php echo intval($item[$key]['id']); ?>").click(function() {
				if (qtyBox.val() > 1) {
					qtyBox.val(parseInt(qtyBox.val()) - 1);
				}
			});
			$("#increase-qty-<?php echo intval($item[$key]['id']); ?>").click(function() { 
				qtyBox.val(parseInt(qtyBox.val()) + 1);
			});
		});
		</script>
		
		<?php echo $form->input($k . '.id', array('value' => $item[$key]['id'])); ?>
		<div id="decrease-qty-<?php echo intval($item[$key]['id']); ?>" class="qty-button qty-button-decrease">-</div>
		<?php echo $form->input($k . '.qty', array(
			'id' => 'qty-input-' . intval($item[$key]['id']),
			'label' => false,
			'class' => 'qty-input',
			'div' => false,
			'value' => intval($item[$key]['qty'])
		)); ?>
		<div id="increase-qty-<?php echo intval($item[$key]['id']); ?>" class="qty-button qty-button-increase">+</div>
		
		<?php if (Configure::read('Basket.show_update_link_under_qty')): ?>
			<p><a href="#">update</a></p>
		<?php endif; ?>

	<?php else: ?>
	
		<p><?php echo intval($item[$key]['qty']); ?></p>

	<?php endif; ?>

</div>
