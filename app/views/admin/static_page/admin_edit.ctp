<?php

$html->script(array(
	'vendors/tiny_mce/tiny_mce.js',
	'vendors/jquery.fancybox-1.3.4.pack.js'
), array('inline' => false));

$html->css(array(
	'vendors/jquery.fancybox-1.3.4.css',
	'vendors/jquery-ui-flick.css'
), null, array('inline' => false));

$tabs = array();
$k = 0;

$tabs['desc'] = array($k++, 'Name &amp; Descriptions');
$tabs['general'] = array($k++, 'General');
$tabs['meta'] = array($k++, 'Meta Information');

if (Configure::read('Catalog.use_product_flags'))
{
	$tabs['flags'] = array($k++, 'Badges');
}

if (Configure::read('Shipping.products_to_shipping_services'))
{
	$tabs['delivery'] = array($k++, 'Delivery');
}

if ($record['Product']['type'] == 'simple')
{
	$tabs['pricing'] = array($k++, 'Pricing');
	$tabs['variations'] = array($k++, 'Variations');
	
	if (!empty($record['Product']['attribute_set_id']))
	{
		$tabs['attributes'] = array($k++, 'Attributes');
	}
	
	if (Configure::read('Stock.use_stock_control'))
	{
		$tabs['stock'] = array($k++, 'Stock Control');
	}
}
else
{
	$tabs['grouped'] = array($k++, 'Grouped Products');
}

$tabs['images'] = array($k++, 'Images');
$tabs['cats'] = array($k++, 'Categories');
// $tabs['sort'] = array($k++, 'Sort Order');

if (Configure::read('Catalog.related_enabled'))
{
	$tabs['related'] = array($k++, 'Related Products');
}

if (Configure::read('Catalog.crosssells_enabled'))
{
	$tabs['cross'] = array($k++, 'Cross-sells');
}

if (Configure::read('Documents.assigned_to_products'))
{
	$tabs['documents'] = array($k++, 'Documents');
}

if (Configure::read('Catalog.youtube_videos'))
{
	$tabs['videos'] = array($k++, 'Videos');
}


$tabIndex = 0;
if (!empty($this->params['named']['tab']) && !empty($tabs[$this->params['named']['tab']]))
{
	$tabIndex = $tabs[$this->params['named']['tab']][0];
}
else if (!empty($initTab))
{
	$tabIndex = $tabs[$initTab][0];
}

$returnUrl = $this->Session->read('Product.last_index_url');
if(empty($returnUrl)) $returnUrl = '/admin/products';

?>

<div id="admin-content">
	
	<div id="side-col">
		<p><a href="<?php echo $returnUrl; ?>" class="icon-link back-link">Back to List</a></p>
		<!-- <h3 id="product-type" class="<?php echo $record['Product']['type']; ?>-type"><?php echo ucwords($record['Product']['type']); ?> Product</h3> -->
		<ul id="product-nav">
			<?php $i = 0; ?>
			<?php foreach ($tabs as $tab => $tabData): ?>
				<?php $error = (!empty($errorsOnTabs) && in_array($tab, $errorsOnTabs)) ? ' class="tab-error"' : ''; ?>
				<li<?php echo $error; ?>><a href="#"><span><?php echo $tabData[1]; ?></span></a></li>
				<?php $i++; ?>
			<?php endforeach; ?>
		</ul>
	</div>
	
	<?php echo $form->create('Product', array('action' => 'save', 'id' => 'product-form', 'type' => 'file')); ?>
		
		<input type="hidden" name="last_pane" id="last-pane" value="desc" />
		<input type="hidden" name="data[Links][RelatedProduct]" id="RelatedProduct-data" value="" />
		<input type="hidden" name="data[Links][CrossSell]" id="CrossSell-data" value="" />
		<input type="hidden" name="data[Links][GroupedProduct]" id="GroupedProduct-data" value="" />
		
		<div class="panes">
			
			<?php echo $session->flash(); ?>
			
			<?php
			$productName = (!empty($record['ProductName'][Configure::read('Languages.main_lang_id')]['name'])) ?
				$record['ProductName'][Configure::read('Languages.main_lang_id')]['name'] : 'Untitled Product';
			?>
			
			<div id="header">
				<a href="/admin/products/delete/<?php echo intval($this->data['Product']['id']); ?>" class="icon-link delete-link"></a>
				<!-- <a href="/admin/products/duplicate/<?php echo intval($this->data['Product']['id']); ?>" class="icon-link duplicate-link"></a> -->
				<a href="/<?php echo $this->data['ProductMeta'][Configure::read('Languages.main_lang_id')]['url']; ?>" class="icon-link preview-link" target="_blank"></a>
				<a href="/admin/products/edit/<?php echo intval($this->data['Product']['id']); ?>" class="icon-link reload-link"></a>
				<h1><?php echo h($productName); ?></h1>
			</div>
			
			<?php echo $this->element('admin/products/edit/names'); ?>
			<?php echo $this->element('admin/products/edit/general'); ?>
			<?php echo $this->element('admin/products/edit/meta'); ?>
			
			<?php if (Configure::read('Catalog.use_product_flags')): ?>
				<?php echo $this->element('admin/products/edit/flags'); ?>
			<?php endif; ?>
		
			<?php if (Configure::read('Shipping.products_to_shipping_services')): ?>
				<?php echo $this->element('admin/products/edit/delivery_services'); ?>
			<?php endif; ?>
			
			<?php if ($record['Product']['type'] == 'simple'): ?>
			
				<?php echo $this->element('admin/products/edit/pricing'); ?>
				<?php echo $this->element('admin/products/edit/variations'); ?>
				
				<?php if (!empty($record['Product']['attribute_set_id'])): ?>
					<?php echo $this->element('admin/products/edit/attributes'); ?>
				<?php endif; ?>
			
				<?php if (Configure::read('Stock.use_stock_control')): ?>
					<?php echo $this->element('admin/products/edit/stock'); ?>
				<?php endif; ?>
			
			<?php else: ?>
			
				<?php echo $this->element('admin/products/edit/linked', array(
					'linkedProducts' => $groupedProducts,
					'link' => 'grouped_products'
				)); ?>
			
			<?php endif; ?>
			
			<?php echo $this->element('admin/products/edit/images'); ?>
			<?php echo $this->element('admin/products/edit/categories'); ?>
			
			<?php if (Configure::read('Catalog.related_enabled')): ?>
				<?php echo $this->element('admin/products/edit/linked', array(
					'linkedProducts' => $relatedProducts,
					'link' => 'related_products'
				)); ?>
			<?php endif; ?>
			
			<?php if (Configure::read('Catalog.crosssells_enabled')): ?>
				<?php echo $this->element('admin/products/edit/linked', array(
					'linkedProducts' => $crossSells,
					'link' => 'cross_sells'
				)); ?>
			<?php endif; ?>

			<?php if (Configure::read('Documents.assigned_to_products')): ?>
				<?php echo $this->element('admin/products/edit/documents'); ?>
			<?php endif; ?>
			
			<?php if (Configure::read('Catalog.youtube_videos')): ?>
				<?php echo $this->element('admin/products/edit/videos'); ?>
			<?php endif; ?>

			<div class="fieldset-box">
				<?php echo $form->submit('Save', array('div' => 'submit single-line')); ?>
			</div>
			
		</div>
		
	<?php echo $form->end(); ?>
	
	<div style="clear: both;"></div>
	
</div>

<script>
	
tinyMCE.init({
    theme : "advanced",
    mode : "textareas",
    convert_urls : "specific_textareas",
	editor_selector: "tinymce",
	width: 520,
	height: 280,
	plugins : "paste",
	paste_auto_cleanup_on_paste : true,
	theme_advanced_buttons3_add : "|,pastetext,pasteword,selectall",
	theme_advanced_toolbar_align : "left"
});

$(function() {
	
	$("ul#product-nav").tabs("div.panes > div.pane", {
		initialIndex: <?php echo intval($tabIndex); ?> 
	});
	
	var api = $("ul#product-nav").data("tabs");
	
	$("#product-form").submit(function() {
		var pane = $(api.getCurrentPane())
		var id = pane.attr("id").substring(5);
		$("#last-pane").val(id);
		return true;
	});
	
	$("ul.lang-nav").tabs("div.fieldsets > fieldset");
	$("ul.currency-nav").tabs("div.fieldsets > fieldset");
	
	var productID = <?php echo intval($this->data['Product']['id']); ?>;
	var uploadWidth = <?php echo intval(Configure::read('Images.product_max_upload_width')); ?>;
	var uploadHeight = <?php echo intval(Configure::read('Images.product_max_upload_height')); ?>;
	var maxUploadFilesize = "<?php echo intval(Configure::read('Images.max_filesize')); ?>mb";
	var uploadQuality = <?php echo intval(Configure::read('Images.upload_quality')); ?>;
	
	$("a.edit-image-popup").live('click', function() {
		var id = $(this).attr("id").substr(14);
		var width = parseInt($("#image_" + id + "_width").val()) + 50;
		var height = parseInt($("#image_" + id + "_height").val()) + 150;
		window.open(this, "edit-product-image", "width=" + width + ",height=" + height + ",status=0,toolbar=0,menubar=0");
		return false;
	});
	
	$("#sort-in-category").change(showCategorySort);
	showCategorySort();
	
	function showCategorySort() {
		var catID = $("#sort-in-category").val();
		$("#cat-sorts ul").hide();
		$("#cat-sort-" + catID).show();
	}
	
	$("#cat-sorts ul").sortable({
		axis: "y",
	});
	
	$(".existing-options-value-list").sortable({
		axis: "y"
	});
	
	$("#product-form").submit(function() {
		
		var hash = "";
		$("#cat-sorts ul").each(function() {
			// var catID = $(this).attr("id").substring(9);
			// hash += catID + ":" + $(this).sortable("serialize") + ";";
			hash += $(this).sortable("serialize") + ";";
		});
		$("#sort-order-data").val(hash.substring(0, hash.length - 1));
		
		var varHash = "";
		$(".existing-options-value-list").each(function() {
			varHash += $(this).sortable("serialize") + ";";
		});
		$("#vars-sort-order-data").val(varHash.substring(0, varHash.length - 1));
		
	});
	
	$("#new-option-link a").click(function() {
		$("#new-option-box").show();
		$("#new-option-link").hide();
		return false;
	});
	
	$("#cancel-new-option-link").click(function() {
		$("#new-option-link").show();
		$("#new-option-box").hide();
		return false;
	});
	
});

</script>


