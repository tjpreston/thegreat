<nav class="grid_24 breadcrumb">
	<ol>
		<li><a href="/">Home</a></li>
		<?php if (!empty($breadcrumbs)): ?>
			<?php $totalCrumbs = count($breadcrumbs); ?>
			<?php $i = 0; ?>
			<?php foreach ($breadcrumbs as $k => $crumb): ?>
					<li<?php echo (($i + 1) == $totalCrumbs) ? ' class="last"' : ''; ?>><?php

						if (($i + 1) < $totalCrumbs){
							$open = '<a href="' . $crumb['url'] . '">';
							$close = '</a>';
						} else {
							$open = '<h1>';
							$close = '</h1>';
						}

						echo $open . h($crumb['link']) . $close;
						
					?></li>
				<?php $i++; ?>
			<?php endforeach; ?>
		<?php endif; ?>
	</ol>
</nav>