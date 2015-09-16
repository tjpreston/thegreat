			<div id="pane-sort" class="pane">
				
				<?php echo $form->hidden('ProductCategorySort.hash', array('id' => 'sort-order-data')); ?>
				
				<div class="fieldset-header">Sort Order</div>
				<div class="fieldset-box">
					<div>
						<?php
						$category->generateProductCategoryOptions($categories);
						echo $form->input('sort_in_category', array(
							'id' => 'sort-in-category',
							'type' => 'select',
							'options' => $category->getOptions(),
							'label' => false
						));
						?>
					</div>
					<p>Click and drag the product to amend the default sort order position.</p>
					<div id="cat-sorts">
						<?php foreach ($productCategories as $xID => $catID): ?>
							<ul id="cat-sort-<?php echo intval($catID); ?>" style="display: none;">
								<?php foreach ($productsInCategory[$catID] as $k => $product): ?>		
									<?php $prodIsViewing = ($product['Product']['id'] == $this->data['Product']['id']) ? ' class="viewing"' : ''; ?>
									<li id="sort_<?php echo intval($catID); ?>-<?php echo intval($product['Product']['id']); ?>"<?php echo $prodIsViewing; ?>>
										<?php echo h($product['ProductName']['name']); ?>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endforeach; ?>
					</div>
				</div>
				<?php echo $form->submit('Save'); ?>
			</div>