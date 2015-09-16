<?php $html->script(array('vendors/jstree/jquery.jstree.js'), array('inline' => false)); ?>

<script type="text/javascript">
$(function() {
	$(".reset").click(function() {
		$(".filter-form").find("select").val(0);
		$(".filter-form").find("input:text").val("");
		$(".filter-form").submit();
	});
});
</script>

<?php echo $form->create('Product', array('url' => '/admin/products', 'id' => 'product-filter', 'class' => 'filter-form', 'type' => 'get')); ?>
		
	<hr />
	
	<h2>Filter/Search</h2>

	<script type="text/javascript">
	$(function() {
		jQuery("#cat-tree").jstree({
			plugins : [ "themes", "html_data", "checkbox" ],
			core: {
				animation: 0,
				initially_open: <?php echo $catNodesOpen; ?>,
				selected_parent_close: false
			}
		});	
		$("#product-filter").submit(function() {		
			var checked_ids = [];
			$.jstree._reference('#cat-tree').get_checked().each(function () {
				checked_ids.push(this.id);
			});
			if (checked_ids.length > 0) {
				$("#filtered-cats").val(checked_ids.join(","));
			}
			
			var open = [];
			$.jstree._reference('#cat-tree').get_container().find('li.jstree-open').each(function () {
				open.push('"' + this.id + '"');
			});
			if (open.length > 0) {
				$("#open_nodes").val(open.join(","));
			}
			
			return true;
		});
	});
	</script>

	<h3>Filter by Keyword</h3>
	<?php echo $form->input('keyword', array('label' => false, 'value' => $session->read('Product.keyword'))); ?>

	<h3>Filter by Manufacturer</h3>
	<?php echo $form->input('manufacturer_id', array(
		'label' => false,
		'empty' => array(0 => 'Show All ----------------'),
		'default' => $session->read('Product.manufacturer_id')
	)); ?>
	
	<h3>Filter by Category</h3>
	<?php $category->setCheckedCategories($catIDs); ?>
	<?php $category->generateNestedTree($categories); ?>
	<div id="cat-tree">
		<?php echo $category->getNestedTree(); ?>
	</div>
	<?php 
	echo $form->hidden('filtered_cats', array(
		'id' => 'filtered-cats',
		'value' => ''
	));
	echo $form->hidden('open_nodes', array(
		'id' => 'open_nodes',
		'value' => ''
	));
	?>

	<div class="submit">
		<?php echo $form->submit('Search', array('id' => 'filter-submit', 'div' => false)); ?>
		<?php // echo $form->submit('Reset', array('id' => 'reset-submit', 'div' => false, 'class' => 'reset')); ?>
		<a href="/admin/products">Reset</a>
	</div>

<?php echo $form->end(); ?>

