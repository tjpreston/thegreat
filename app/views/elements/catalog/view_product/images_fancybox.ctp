<?php

$html->script(
	array('vendors/jquery.fancybox-1.3.4.pack.js'),
	array('inline' => false)
);
$html->css('vendors/jquery.fancybox-1.3.4.css', null, array('inline' => false));

?>

<script type="text/javascript">
$(function() {
	$("#image-links").tabs("#product-images > .image-view", {
		tabs: "li.image-link"
	});
	$(".image-view a").fancybox({
		titleShow: false,
		type: 'image'
	});
});
</script>

<div id="product-images">
			
	<?php if (!empty($record['ProductImage'])): ?>
		
		<?php foreach ($record['ProductImage'] as $k => $image): ?>
		
			<div class="image-view">			
				<?php if (!empty($image['large_web_path'])): ?>
			        <a href="<?php echo $image['large_web_path']; ?>" rel="images">
						<img src="<?php echo $image['medium_web_path']; ?>" alt="" />
					</a>
					<!-- <img src="/img/icons/magnifier.png" class="zoom" alt="zoom" /> -->				
				<?php else: ?>
					<img src="<?php echo $image['medium_web_path']; ?>" alt="" />
				<?php endif; ?>
			</div>
			
		<?php endforeach; ?>
		
	<?php else: ?>
		
		<div class="image-view">
			<img src="<?php echo $record['Product']['main_medium_image_path']; ?>" alt="" />
		</div>
		
	<?php endif; ?>
	
</div>

<div id="product-image-nav">
	
	<?php $imageCount = count($record['ProductImage']); ?>	
	
	<div class="product-details-nav">		
		<ul id="image-links">
			
			<?php for ($i = 0; $i <= Configure::read('Images.product_max') - 1; $i++): ?>
			<?php // foreach ($record['ProductImage'] as $k => $image): ?>
				<li<?php echo (!empty($record['ProductImage'][$i])) ? ' class="image-link"' : ''; ?>>
					
						<?php // echo $k + 1; ?>
					
					<?php if (!empty($record['ProductImage'][$i])): ?>
						<img src="<?php echo $record['ProductImage'][$i]['tiny_web_path']; ?>" alt="" />
					<?php endif; ?>
					
				</li>
			<?php // endforeach; ?>
			<?php endfor; ?>
			
		</ul>		
	</div>
	
</div>



