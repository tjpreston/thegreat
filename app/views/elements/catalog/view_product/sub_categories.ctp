<?php if(!empty($categoryFamily)): ?>

<div class="panel">
		<div class="interior">

	<?php if(!empty($topCategoryRecord)): ?>
		<p class="heading"><?php
			echo h($topCategoryRecord['CategoryName']['name']) . ' (' . $topCategoryRecord['Category']['product_counter'] . ')';
		?></p>
	<?php else: ?>
		<h4>Filter by category:</h4>
	<?php endif; ?>

	<div class="refine">
		<div class="interior">
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
			<div style="clear:both"></div>
		</div>
		<div class="base"></div>
	</div>

	<div class="base"></div>
	</div>
</div>
	
<?php endif; ?>
