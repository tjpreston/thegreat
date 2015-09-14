<div id="pane-cats" class="pane">
	<div class="fieldset-header"><span>Categories</span></div>
	<div class="fieldset-box">
		<?php 
		echo $form->input('ProductCategory.ProductCategory.primary_category_id', array(
			'options' => $treeList,
			'selected' => $primaryCategory['Category']['id'],
			'type' => 'select'
		));
		?>
		<?php 
		$category->setProductCategories($productCategories);
		// $category->setMainCategoryID($this->data['Product']['main_category_id']);
		$category->generateCategoryCheckboxes($categories);
		?>
		<div id="product-cats" style="padding: 10px;">
			<?php echo $category->getNestedTree(); ?>
		</div>
	</div>
</div>