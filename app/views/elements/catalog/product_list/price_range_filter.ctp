<?php if (!empty($range)): ?>

<dl class="filter">

	<?php

	$displayRangeCount = 0;
	foreach ($range as $k => $v)
	{
		$displayRangeCount = (!empty($rangeCount[$k])) ? $displayRangeCount + 1 : $displayRangeCount;
	}
	
	?>

	<dt>Price</dt>
		
	<?php $i = 1; ?>
  
	<?php foreach ($range as $k => $v): ?>
		 
		<?php if (!empty($rangeCount[$k])): ?>
			
			<?php
			
			$from = intval(key($v));
			$to = intval($v[key($v)]);
			$string = $from . '-' . $to;
			
			$classes = array();
			if (!empty($selected_price_ranges_filter_values) && in_array($string, $selected_price_ranges_filter_values))
			{
				$classes[] = 'selected';
			}
			if (empty($selected_price_ranges_filter_values) && empty($aboveRange) && ($i == $displayRangeCount) && empty($onSpecial))
			{
				$classes[] = 'last';
			}
			$class = (!empty($classes)) ? ' class="' . implode(' ', $classes) . '"' : '';
			
			?>

		  	<dd<?php echo $class; ?>>
				<?php
					$string = $from . '-' . $to;

					if(empty($selected_price_ranges_filter_values) || !in_array($string, $selected_price_ranges_filter_values)){
						$url = $catalog->getUrl() . 'priceinc[]=' . $string;
					} else {
						$url = $catalog->getUrl() . 'priceex[]=' . $string;
					}
				?>
				<a href="<?php echo $url; ?>" rel="nofollow">
		  			<?php echo $activeCurrencyHTML; ?><?php echo number_format($from, 2); ?> - <?php echo $activeCurrencyHTML; ?><?php echo number_format($to, 2); ?> 
					<span class="count face1">(<?php echo intval($rangeCount[$k]); ?>)</span>
					<div class="icon"></div>
				</a>
			</dd>
			
			<?php $i++; ?>
			
		<?php endif; ?>
		
	<?php endforeach; ?>
  
	<?php if (!empty($aboveRange)): ?>
	
		<?php
		
		$classes = array();
		if (!empty($selected_price_ranges_filter_values) && in_array($to . '+', $selected_price_ranges_filter_values))
		{
			$classes[] = 'selected';
		}
		if (empty($selected_price_ranges_filter_values) && empty($onSpecial))
		{
			$classes[] = 'last';
		}
		$class = (!empty($classes)) ? ' class="' . implode(' ', $classes) . '"' : '';
		
		?>
	
		<dd<?php echo $class; ?>>
			<a href="<?php echo $catalog->getUrl(); ?>priceinc[]=<?php echo intval($to); ?><?php echo rawurlencode('+'); ?>" rel="nofollow">
				<?php echo $activeCurrencyHTML; ?><?php echo number_format($to, 2); ?> + <span class="count face1">(<?php echo intval($aboveRange); ?>)</span>
				<div class="icon"></div>
			</a>
		</dd>
		
	<?php endif; ?>
	
	<?php if ($body_id != 'specials'): ?>
		<?php if (!empty($onSpecial)): ?>
		
			<?php
			
			$classes = array('last');
			if (!empty($selected_price_ranges_filter_values) && in_array('special', $selected_price_ranges_filter_values))
			{
				$classes[] = 'selected';
			}
			$class = (!empty($classes)) ? ' class="' . implode(' ', $classes) . '"' : '';
			
			?>
		
			<dd<?php echo $class; ?>>
				<?php
					$string = 'special';

					if(empty($selected_price_ranges_filter_values) || !in_array($string, $selected_price_ranges_filter_values)){
						$url = $catalog->getUrl() . 'priceinc[]=' . $string;
					} else {
						$url = $catalog->getUrl() . 'priceex[]=' . $string;
					}
				?>
				<a href="<?php echo $url; ?>" rel="nofollow">
		  			On Offer <span class="count face1">(<?php echo intval($onSpecial); ?>)</span>
		  			<div class="icon"></div>
				</a>
			</dd>
		
		<?php endif; ?>
	<?php endif; ?>

</dl>

<?php endif; ?>