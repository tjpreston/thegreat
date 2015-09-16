<div class="list-item-image">
	<?php if (!empty($category['Category']['list_web_path'])): ?>
		<img src="<?php echo $category['Category']['list_web_path']; ?>" alt="<?php echo h($category['CategoryName']['name']); ?>" class="category-list-image" />
	<?php else: ?>
		<img src="/img/categories/category-no-img.png" class="category-list-image" alt="Awaiting Image" />
	<?php endif; ?>
</div>