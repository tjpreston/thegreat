<?php if (!empty($manufacturers)): ?>

	<?php

	$class = (empty($selected_manufacturers_filter_values)) ? 'closed' : 'open';

	?>

	<dl id="manu-filter" class="<?php echo $class; ?>">
		
		<dt>
			<a href="#">Manufacturer</a>
		</dt>
		
		<?php
		
		$manCount = count($manufacturers); 
		$i = 1;
		
		?>
		
		<?php foreach ($manufacturers as $k => $man): ?>
		
			<?php
			
			$classes = array();
			if (!empty($selected_manufacturers_filter_values) && in_array($man['url'], $selected_manufacturers_filter_values))
			{
				$classes[] = 'use';
			}
			if (empty($selected_manufacturers_filter_values) && empty($noManufacturer) && ($i == $manCount))
			{
				$classes[] = 'last';
			}
			$class = (!empty($classes)) ? ' class="' . implode(' ', $classes) . '"' : '';
			
			?>
			
			<dd<?php echo $class; ?>>
				<a href="<?php echo $catalog->getUrl(); ?>maninc[]=<?php echo h($man['url']); ?>">
					<?php echo h($man['name']); ?>
					<span>(<?php echo intval($man['count']); ?>)</span>
				</a>
				<?php if (!empty($selected_manufacturers_filter_values) && in_array($man['url'], $selected_manufacturers_filter_values)): ?>
					<a class="icon" href="<?php echo $catalog->getUrl(); ?>manex[]=<?php echo h($man['url']); ?>"><img src="/img/app/x.gif" /></a>
				<?php endif; ?>
			</dd>
		
			<?php $i++; ?>

		<?php endforeach; ?>
		

		<?php if (!empty($noManufacturer)): ?>
		
			<?php
			
			$classes = array();
			if (!empty($selected_manufacturers_filter_values) && in_array('other', $selected_manufacturers_filter_values))
			{
				$classes[] = 'use';
			}
			if (empty($selected_manufacturers_filter_values))
			{
				$classes[] = 'last';
			}
			$class = (!empty($classes)) ? ' class="' . implode(' ', $classes) . '"' : '';
			
			?>
			
			<dd<?php echo $class; ?>>
				<a href="<?php echo $catalog->getUrl(); ?>maninc[]=other">
					Other <span>(<?php echo intval($noManufacturer); ?>)</span>
				</a>
				<?php if (!empty($selected_manufacturers_filter_values) && in_array('other', $selected_manufacturers_filter_values)): ?>
					<a class="icon" href="<?php echo $catalog->getUrl(); ?>manex[]=other"><img src="/img/app/x.gif" /></a>
				<?php endif; ?>  
			</dd>
		
		<?php endif; ?>
		
		<?php if (!empty($selected_manufacturers_filter_values)): ?>
			<dd class="remove last"><a href="<?php echo $catalog->getUrl(); ?>remove=manufacturers">Remove filter</a></dd>
		<?php endif; ?>

	</dl>

<?php endif; ?>


