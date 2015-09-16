<?php

$html->script(
	array('vendors/jquery.jqzoom-core.js'),
	array('inline' => false)
);
$html->css('vendors/jquery.jqzoom.css', null, array('inline' => false));

?>

<script type="text/javascript">	
$(function() { initZoom(); });
//$(window).load(function(){ initZoom(); });
</script>

<div id="product-images">
	
	<div class="image-view">
		<?php if (!empty($images[0]['large_web_path'])): ?>
			<a href="<?php echo $images[0]['large_web_path']; ?>" class="jqzoom" rel="gal1">
				<img src="<?php echo $images[0]['medium_web_path']; ?>" alt="" />
			</a>
		<?php endif; ?>
	</div>

	<?php if (Configure::read('Catalog.show_special_offer_overlay') && !empty($record['ProductPrice']['on_special'])): ?>
		<div class="special-offer-product">This product is on special offer</div>
	<?php endif; ?>

</div>

<div id="product-image-nav">
	
	<?php $imageCount = count($images); ?>
	
	<div class="product-details-nav">
		<ul id="image-links">

			<?php for ($i = 0; $i <= Configure::read('Images.product_max') - 1; $i++): ?>
			
				<li<?php echo ($i == (Configure::read('Images.product_max') - 1)) ? ' class="last-image"' : ''; ?>>
			
					<?php if (!empty($images[$i])): ?>					
						<?php $class = ($i === 0) ? ' class="zoomThumbActive"' : ''; ?>
						<a href="" rel="{gallery: 'gal1', smallimage: '<?php echo $images[$i]['medium_web_path']; ?>', largeimage: '<?php echo $images[$i]['large_web_path']; ?>'}"<?php echo $class; ?>>
							<img src="<?php echo $images[$i]['tiny_web_path']; ?>" alt="" />
						</a>
					<?php endif; ?>

				</li>
				
			<?php endfor; ?>
			
		</ul>		
	</div>
	
</div>

