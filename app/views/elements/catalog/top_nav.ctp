<div id="top-nav-box">

	<?php
	
	// Receives $categories, $topCategoryID
	
	$this->Category->clearNestedTree();
	$this->Category->generateTopNav($categories);
	echo $this->Category->getNestedTree();
	
	?>
	
	<div class="nav-cap"></div>

	<div style="clear: both;"></div>
	
</div>
