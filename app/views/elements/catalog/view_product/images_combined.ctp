<?php if (!empty($images[0]['large_web_path'])): ?>
<div id="bigPicLink">
	<div class="contain-image">
		<a href="<?php echo $images[0]['large_web_path']; ?>" class="cloud-zoom" id="zoom1" rel="adjustX: 0, adjustY: 0, position: 'inside'">
			<img src="<?php echo $images[0]['medium_web_path']; ?>" alt="<?php echo $record['ProductName']['name']; ?>" />
		</a>
	</div>
</div>
<?php else: ?>
<div id="bigPicLink">
	<div class="contain-image">
		<img src="/img/products/no-medium.png" alt="Awaiting Image" />
	</div>
</div>
<?php endif; ?>

<?php $imageCount = count($images); ?>
<?php if($imageCount > 1): ?>
<div class="thumbnails">
	<ul>
		<?php

		foreach($images as $i => $image){
			if(!empty($images[$i])){
				$image = $images[$i];

		?>
		<li>
			<a href="<?php echo $image['large_web_path']; ?>" class="cloud-zoom-gallery" rel="useZoom: 'zoom1', smallImage: '<?php echo $image['medium_web_path']; ?>'">
				<img src="<?php echo $image['tiny_web_path']; ?>" alt="<?php echo $record['ProductName']['name']; ?>" />
			</a>
		</li>
		<?php

			}
		}

		?>
	</ul>
</div>
<?php endif; ?>