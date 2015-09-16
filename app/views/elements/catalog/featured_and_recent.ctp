
<div id="featured-and-recently-viewed">
	<?php echo $this->element('catalog/featured_products'); ?>
	<?php echo $this->element('catalog/recently_viewed_products'); ?>
</div>

<?php if ((!empty($categoryFeaturedProducts) || !empty($rootFeauredProducts)) && !empty($recentlyViewed)): ?>

<?php endif; ?>

