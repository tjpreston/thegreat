<?php echo $this->Html->script('vendors/jquery.tinycarousel.js', array('inline' => false)); ?>

<div id="var-images">

	<h3>Also Available In</h3>
	
	<div class="scrollable">
		
		<a class="go-left buttons prev">
			<img src="/img/app/bn-small-arrow-left.gif" />
		</a>
		
		<div class="viewport">
			<ul class="overview">
				
				<?php foreach ($optionsStock as $k => $line): ?>
					
					<?php
					$path = $line['ProductOptionStock']['main_small_image_path'];
					$class = (!empty($record['ProductOptionStock']) && ($record['ProductOptionStock']['id'] == $line['ProductOptionStock']['id'])) ? ' class="selected"' : '';
					?>
					
					<li id="var-<?php echo intval($line['ProductOptionStock']['id']); ?>"<?php echo $class; ?>>
						<img src="<?php echo $path; ?>" />
					</li>
					
				<?php endforeach; ?>
				
			</ul>
		</div>
		
		<a class="go-right buttons next">
			<img src="/img/app/bn-small-arrow-right.gif" />
		</a>

	</div>

</div>



