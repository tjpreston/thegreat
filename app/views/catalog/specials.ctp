<?php
	$this->Catalog->setSpecialsUrls();
	$this->set('body_id', 'specials');
?>



<div id="leftcol">
	<div class="panel">
		<div class="interior">
			<?php echo $this->element('catalog/product_list/product_filter', array(
					//'options' => array('category_list' => true),
					'base_url' => 'specials'
				)); ?>
			<div class="base"></div>
		</div>
	</div>
</div>
<div id="content">
	
	<h1>Special Offers</h1>
	
	<?php echo $this->element('catalog/product_list/product_list', array(
		'message' => 'No products found in this category.'
	)); ?>

</div>
<div class="clear"></div>