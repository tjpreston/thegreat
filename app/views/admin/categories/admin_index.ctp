<?php 

$html->script(
	array('vendors/jstree/jquery.jstree.js', 'vendors/tiny_mce/tiny_mce.js'),
	array('inline' => false)
); 

?>

<script type="text/javascript">

tinyMCE.init({
    theme : "advanced",
    mode : "textareas",
    convert_urls : "specific_textareas ",
	editor_selector: "tinymce",
	width: 460,
	height: 120,
	plugins: "paste",
	theme_advanced_buttons1: "bold,italic,underline,strikethrough,separator,separator,pastetext,pasteword,separator,link,unlink",
	theme_advanced_buttons2: "",
	theme_advanced_buttons3: ""
});

</script>

<div id="admin-content">
		
	<div id="side-col">
		<ul id="admin-links">
			<li><a href="/admin/categories/index/new" class="icon-link add-link">Add New Category</a></li>
		</ul>
		<hr />
		<h2>Select Category</h2>
		<script type="text/javascript">
		$(function() {
			jQuery("#cat-tree").jstree({
				plugins: ["themes", "html_data"],
				core: { animation: 0 }
		    });
		});
		</script>
		<?php $category->generateNestedTree($categories); ?>
		<div id="cat-tree">
			<?php echo $category->getNestedTree(); ?>
		</div>
	</div>
	
	<div class="panes">
		
		<?php echo $session->flash(); ?>
		
		<div id="header">
			<?php if (!empty($this->data['Category']['id'])): ?>
				<a href="/admin/categories/delete/<?php echo intval($this->data['Category']['id']); ?>" class="icon-link delete-link">Delete</a>
			<?php endif; ?>
			<h1>
				<?php if (!empty($newRecord)): ?>
					Add New Category
				<?php elseif (!empty($record)): ?>
					<?php echo $record['CategoryName'][Configure::read('Languages.main_lang_id')]['name']; ?>
				<?php else: ?>
					Manage Categories
				<?php endif; ?>
			</h1>
		</div>
						
		<?php if (!empty($record) || !empty($newRecord)): ?>		
		
			<script type="text/javascript">	
			$(function() {
				$("ul.lang-nav").tabs("div.fieldsets > fieldset");
			});
			</script>
			
			<?php echo $form->create('Category', array('url' => '/admin/categories/save', 'type' => 'file', 'id' => 'category-form')); ?>

				<div class="pane">
					
					<div class="fieldset-header"><span>Name and Meta</span></div>					
					<div class="fieldset-box">

						<fieldset>
	
							<?php // echo $this->element('admin/language_nav'); ?>
				
							<div class="fieldsets">
								<?php foreach ($languages as $languageID => $languageName): ?>
									
											
									<fieldset>
										
										<?php 			
																
										echo $form->hidden('CategoryName.' . $languageID . '.language_id', array('value' => $languageID));
										
										$nameID = (!empty($this->data['CategoryName'][$languageID]['id'])) ? $this->data['CategoryName'][$languageID]['id'] : '';
										echo $form->input('CategoryName.' . $languageID . '.id', array('value' => $nameID));											
										
										$urlError = $form->error('CategoryName.' . $languageID . '.url');
										if (!empty($urlError))
										{
											echo '<div class="input error">';
											echo '<label></label>';
											echo $urlError;
											echo '</div>';
										}
										
										$nameName = (!empty($this->data['CategoryName'][$languageID]['name'])) ? $this->data['CategoryName'][$languageID]['name'] : '';
										echo $form->input('CategoryName.' . $languageID . '.name', array('value' => $nameName));
										
										echo $form->hidden('CategoryDescription.' . $languageID . '.language_id', array('value' => $languageID));
										
										$fields = array(
											'id' => array(),
											'short_description' => array(),
											'description' => array('class' => 'tinymce'),
											'page_title' => array(),
											'meta_keywords' => array(),
											'meta_description' => array()
										);
										
										?>
										
										<?php foreach ($fields as $field => $attrs): ?>
											<?php
											$languageData[$field] = (!empty($this->data['CategoryDescription'][$languageID][$field])) ? $this->data['CategoryDescription'][$languageID][$field] : '';
											$fieldAttrs = array('value' => $languageData[$field]);
											if (!empty($attrs))
											{
												$fieldAttrs = array_merge($fieldAttrs, $attrs);
											}
											echo $form->input('CategoryDescription.' . $languageID . '.' . $field, $fieldAttrs);
											?>
										<?php endforeach; ?>
										
									</fieldset>
								<?php endforeach; ?>
							</div>

						</fieldset>
					</div>

					<div class="fieldset-header"><span>General</span></div>					
					<div class="fieldset-box">
						
						<fieldset>
							
							<?php
							echo $form->input('id');
							echo $form->input('parent_id', array(
								'options' => $treeList,
								'empty' => array(0 => 'Root Category -----------')
							));
							echo $form->input('sort_order', array('class' => 'tiny'));
							echo $form->input('active');
							//echo $form->input('featured');
							echo $form->input('display_as_landing', array('label' => 'Landing page'));
							?>

						</fieldset>
					</div>

					<div class="fieldset-header"><span>Site Navigation</span></div>
					<div class="fieldset-box">
						
						<fieldset>
							
							<?php
							// echo $this->Form->input('CategoryName.' . $languageID . '.menu_name');
							echo $this->Form->input('Category.display_on_main_nav');
							echo $this->Form->input('Category.enable_subcategory_dropdown');
							echo $this->Form->input('Category.display_on_footer_nav');
							?>

						</fieldset>
					</div>

					<script>
					$(function() {
						$("a.edit-image-popup").live('click', function() {
							var id = $(this).attr("id").substr(10);
							var width = 800;
							var height = 700;
							window.open(this, "edit-cat-image", "width=" + width + ",height=" + height + ",status=0,toolbar=0,menubar=0");
							return false;
						});
					});
					</script>
			
					<div class="fieldset-header"><span>Images</span></div>					
					<div class="fieldset-box">

						<?php $id = (!empty($this->data['Category']['id'])) ? $this->data['Category']['id'] : 0; ?>
						
						<fieldset>
							
							<?php
							if (!empty($this->data['Category']['header_web_path']))
							{
								// $headerImage = '<img src="/categories/get_thumb/' . intval($this->data['Category']['id']) . '/header/admin" alt="" /><br />';
								$headerImage = '<img src="' . $this->data['Category']['header_web_path'] . '" alt="" /><br />';
							}
							?>
							<div class="input">
								<label for="void">Header Image</label>
								<div class="image-preview">									
									<?php if (!empty($headerImage)): ?>
										<div><?php echo $headerImage; ?></div>
										<div>
											<!--
											<a id="cat-image-<?php echo $id ?>" href="/admin/categories/edit_image/header/<?php echo $id ?>" class="edit-image-popup">Edit</a> | 
											-->
											<a href="/admin/categories/delete_image/<?php echo $id ?>/header">Delete Image</a>
										</div>
									<?php else: ?>
										<div>No header image uploaded.</div>
									<?php endif; ?>
								</div>
							</div>															
							<?php
							echo $form->input('header_image', array(
								'type' => 'file',
								'label' => 'Upload Header Image'
							));
							?>
							
							<?php
							if (!empty($this->data['Category']['list_web_path']))
							{
								// $listImage = '<img src="/categories/get_thumb/' . intval($this->data['Category']['id']) . '/list/admin" alt="" /><br />';
								$listImage = '<img src="' . $this->data['Category']['list_web_path'] . '" alt="" /><br />';
							}
							?>
							<div class="input">
								<label for="void">List Image</label>
								<div class="image-preview">									
									<?php if (!empty($listImage)): ?>
										<div><?php echo $listImage; ?></div>
										<div>
											<!--
											<a id="cat-image-<?php echo $id ?>" href="/admin/categories/edit_image/list/<?php echo $id ?>" class="edit-image-popup">Edit</a> | 
											-->
											<a href="/admin/categories/delete_image/<?php echo $id ?>/list">Delete Image</a>							
										</div>
									<?php else: ?>
										<div>No list image uploaded.</div>
									<?php endif; ?>
								</div>
							</div>

							<?php echo $form->input('list_image', array(
								'type' => 'file',
								'label' => 'Upload List Image'
							));	?>
										
						</fieldset>
								
					</div>
				</div>
				
				
				<?php if (!empty($categoryProducts)): ?>
				
					<div class="pane">
						
						<script type="text/javascript">
						$(function() {
							
							$("#cat-sorts ul").sortable({
								axis: "y"
							});
							$("#cat-sorts ul").disableSelection();
							
							$("#category-form").submit(function() {
								var hash = "";
								$("#cat-sorts ul").each(function() {
									// var catID = $(this).attr("id").substring(9);
									// hash += catID + ":" + $(this).sortable("serialize") + ";";
									hash += $(this).sortable("serialize") + ";";
								});
								$("#sort-order-data").val(hash.substring(0, hash.length - 1));
							})
						});
						</script>
					
						<?php echo $form->hidden('ProductCategorySort.hash', array('id' => 'sort-order-data')); ?>
						
						<div class="fieldset-header"><span>Default Product Sort Order</span></div>					
						<div class="fieldset-box">
							<p>Click and drag the product to amend the default sort order position.<br />Tick products to remove from the category.</p>
							<div id="cat-sorts">
								<ul id="cat-sort-<?php echo intval($record['Category']['id']); ?>">
									<?php foreach ($categoryProducts as $k => $product): ?>
										
										<li id="sort_<?php echo intval($record['Category']['id']); ?>-<?php echo intval($product['Product']['id']); ?>">
											[<?php echo h($product['Product']['sku']); ?>]
											<?php echo h($product['ProductName']['name']); ?>

											<span>	
												<img src="/img/icons/delete.png" alt="Delete from category" title="Delete from category" />
												<input type="checkbox" name="data[ProductDelete][<?php echo intval($product['Product']['id']); ?>]" value="1" />
											</span>
										
											<span>
												<img src="/img/icons/flag_green.png" alt="Featured in category" title="Featured in category" />
												<input type="checkbox" 
													name="data[CategoryFeaturedProduct][<?php echo intval($product['Product']['id']); ?>]" 
													value="1" 
													<?php echo (in_array($product['Product']['id'], $record['CategoryFeaturedProduct'])) ? ' checked="checked"' : ''; ?>
												/>
											</span>
											
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						</div>					
						
					</div>
				
				<?php endif; ?>
				
				
				
				<div class="fieldset-box">
					<?php echo $form->submit('Save', array('div' => 'submit single-line')); ?>
				</div>
				
			<?php echo $form->end(); ?>
				
		<?php else: ?>
			<p>Please select a category from the list.</p>
		<?php endif; ?>
		
	</div>
	
</div>







