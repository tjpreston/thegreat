<?php

$contexts = array(
	'related' => 'RelatedAdd',
	'crosssell' => 'CrossSellAdd',
	'cat' => 'ProductAdd',
	'grouped' => 'Grouped'
);

$key = $contexts[$context];

?>

<?php if (!empty($records)): ?>	
	<?php foreach ($records as $k => $record): ?>
	
		<div>
			<input type="checkbox" name="<?php echo intval($record['Product']['id']); ?>" value="1" />
			<label><?php echo h($record['ProductName']['name']); ?> (<?php echo h($record['Product']['sku']); ?>)</label>
		</div>
		
	<?php endforeach; ?>		
<?php else: ?>

	<p style="padding: 14px; padding-top: 0;">No products found.</p>
	
<?php endif; ?>