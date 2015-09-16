<?php if (!empty($categories)): ?>
	
	<div id="left-nav-cap-top">Choose a Category...</div>
	<div id="left-nav">
		
		<?php
		
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
		}
		
		$category->generateCategorySideNav($categories);
		echo $category->getNestedTree();
		
		?>		
		
		<ul>
			<?php $specialsOpen = (isset($showingSpecials)) ? ' class="open"' : ''; ?>
			<li<?php echo $specialsOpen; ?>><a href="/specials">Special Offers</a></li>
		</ul>

	</div>
	<div id="left-nav-cap-bottom"></div>

<?php endif; ?>


