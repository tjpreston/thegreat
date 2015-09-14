<div class="panel">
	<div class="heading green-block-heading">
		
			<h2>Category</h2>
		
	</div>
	
	<div id="cat-side-nav" class="panel-content">

<?php if (!empty($categoryTree)): ?>

<ul id="manu-filter" class="filter-style">
	
	<?php
	$catCount = count($categoryTree); 
	$i = 1;
	
	?>
	
	<?php foreach ($categoryTree as $id => $name): ?>
	
		<?php
		
		$classes = array();
		if (!empty($catid) && $id == $catid)
		{
			$classes[] = 'use';
		}
		if (empty($selected_category) && empty($noCategory) && ($i == $catCount))
		{
			$classes[] = 'last';
		}
		$class = (!empty($classes)) ? ' class="' . implode(' ', $classes) . '"' : '';
		
		?>
		
		
		<li<?php echo $class; ?>>
			<?php
				$urlName = strtolower(str_replace(' ', '-', trim(str_replace(array('-'), '', $name))));
			?>
			<a href="/<?php echo $base_url; ?>/<?php echo h($id); ?>-<?php echo h($urlName); ?>">
	  			<?php echo h($name); ?>
			</a>
			<?php if ($id == $catid): ?>
				<!-- <a class="icon" href="/<?php echo $base_url; ?>"><img src="/img/app/x.png" /></a> -->
			<?php endif; ?>
		</li>
	
		<?php $i++; ?>

	<?php endforeach; ?>
	

	<?php if (!empty($noManufacturer)): ?>
	
		<?php
		
		$classes = array();
		if (!empty($useManufacturers) && in_array('other', $useManufacturers))
		{
			$classes[] = 'use';
		}
		if (empty($useManufacturers))
		{
			$classes[] = 'last';
		}
		$class = (!empty($classes)) ? ' class="' . implode(' ', $classes) . '"' : '';
		
		?>
		
		<li<?php echo $class; ?>>
			<a href="<?php echo $catalog->getUrl(); ?>maninc[]=other">
				Other <span>(<?php echo intval($noManufacturer); ?>)</span>
			</a>
			<?php if (!empty($selected_manufacturers_filter_values) && in_array('other', $selected_manufacturers_filter_values)): ?>
				<a class="icon" href="<?php echo $catalog->getUrl(); ?>manex[]=other"></a>
			<?php endif; ?>  
		</li>
	
	<?php endif; ?>
	
	<?php if (!empty($selected_manufacturers_filter_values)): ?>
		<li class="remove last"><a href="<?php echo $catalog->getUrl(); ?>remove=manufacturers">Remove filter</a></li>
	<?php endif; ?>

</ul>

<?php endif; ?>


	</div>

</div>
