<?php
	$this->set('body_id', 'product');

	$description = $record['ProductMeta']['description'];
	if(empty($description)){
		$description = $this->Text->truncate(str_replace("\n", ' ', strip_tags($record['ProductDescription']['long_description'])), 250, array('exact' => false));
	}
	$this->set('metaDescription', $description);
?>
<script type="text/javascript">
var productID = <?php echo intval($record['Product']['id']); ?>;
</script>
<?php
	$html->script(
		array('vendors/cloud-zoom.1.0.2.min.js'),
		array('inline' => false)
	);
	$html->css('vendors/cloud-zoom.css', null, array('inline' => false));
?>

<?php echo $this->element('template/breadcrumbs'); ?>

<?php echo $session->flash(); ?>
<div class="grid_24 product-details">

        <!-- start here to fix z-index mess in element below -->
	<div class="grid_12 alpha">
		<?php echo $this->element('catalog/view_product/images'); ?>
	</div>

	<div class="grid_12 omega">
		<h1><?php echo h($record['ProductName']['name']); ?></h1>
		<p class="sku face1 second-color">SKU <?php echo h($record['Product']['sku']); ?></p>

		<?php //echo $this->element('catalog/view_product/social_icons'); ?>

		<div class="long-desc">
			<?php echo $record['ProductDescription']['long_description']; ?>
		</div>

		<?php echo $this->element('catalog/view_product/specs'); ?>

		<?php echo $this->Form->create('Basket', array('url' => '/basket/add', 'class' => 'purchasing', 'id' => 'product-form')); ?>

		<?php echo $this->element('catalog/view_product/custom_options'); ?>

		<div id="price-and-stock">
			<?php if (!empty($record['ProductOption']) && empty($loadDefaultVar)): ?>
				<?php if(!empty($record['ProductPrice']['lowest_price'])) : ?>
					<div class="price">
						<span class="active first-color">from <?php echo $activeCurrencyHTML; ?><?php echo number_format($record['ProductPrice']['lowest_price'], 2); ?></span>
					</div>
				<?php endif;?>
				<?php echo $this->element('catalog/view_product/please_select_options'); ?>
			<?php else: ?>
				<?php echo $this->element('catalog/view_product/price_and_stock'); ?>
			<?php endif; ?>
		</div>

		<?php echo $this->Form->end(); ?>

	</div>
	<div class="grid_24 alpha omega">
		<?php echo $this->element('catalog/view_product/related_products'); ?>
	</div>
</div>