<div class="<?php echo $class; ?> home-feature"<?php if(!empty($style)) echo ' style="' . $style . '"'; ?>>
	<a href="javascript: return false;" class="arrow-prev">Previous</a>
	<a href="javascript: return false;" class="arrow-next">Next</a>

	<div class="cycle">
	<?php foreach($records as $record): ?>
	<div class="loadin">
		<a href="/<?php echo $record['ProductMeta']['url']; ?>" title="View Product: <?php echo h($record['ProductName']['name']); ?>">
			<img src="<?php echo $record['Product']['main_thumb_image_path']; ?>" alt="<?php echo h($record['ProductName']['name']); ?>" />
		</a>
		<div class="info">
			<h3><?php echo h($record['ProductName']['name']); ?></h3>
			<h2><?php
				if($record['ProductPrice']['on_special']){
					echo '<span>' . $activeCurrencyHTML . $record['ProductPrice']['base_price'] . '</span> ' . $activeCurrencyHTML . $record['ProductPrice']['active_price'];
				} else {
					echo $activeCurrencyHTML . h($record['ProductPrice']['active_price']);
				}
			?></h2>
		</div>
	</div>
	<?php endforeach; ?>
	</div>
</div>