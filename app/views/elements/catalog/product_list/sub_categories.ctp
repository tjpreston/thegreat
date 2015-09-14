<?php if(!empty($categoryFamily)): ?>
	<?php if(!empty($topCategoryRecord)): ?>
		<p class="heading"><span class="face2">Categories</span></p>
	<?php else: ?>
		<p class="heading"><span class="face1">Filter</span> <span class="face2">Category</span></p>
	<?php endif; ?>

	<div class="refine">
		<?php

		$category->clearNestedTree();
		
		if (!empty($categoryPathIDs))
		{
			$category->setPathIDs($categoryPathIDs);
		}
		if (!empty($categoryID))
		{
			$category->setOpenCategoryID($categoryID);
		}
		if (!empty($topCategoryID))
		{
			$category->setTopCategoryID($topCategoryID);
			$category->topCategoryRecord = $topCategoryRecord;
			$this->Category->showAllResults = true;
		}

		$this->Category->generateCategorySideNav($categoryFamily, $rootCatUrl . '/');

		echo $this->Category->getNestedTree();
		
		?>
	</div>
	
<?php endif; ?>