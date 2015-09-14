<div id="product-nav-<?php echo $pos; ?>" class="product-nav clearfix border-top-bottom">
	
	<div class="showing">Showing <?php echo $paginator->counter(array('format' => 'range', 'separator' => ' of ')); ?> products</div>
	
	<?php if ($paginator->hasPage(2)): ?>
		<div class="pagination">
			<span>Page:</span>
				<?php echo $paginator->prev('Previous',  array('tag' => 'span'), null); ?>
				<?php echo $paginator->numbers(array('tag' => 'span',	'separator' => ' ')); ?>
				<?php echo $paginator->next('Next', array('escape' => false, 'tag' => 'span'), null); ?>
		</div>
	<?php endif; ?>
	
	<?php if (Configure::read('Catalog.product_sorts_in_selects')): ?>
	
		<script type="text/javascript">
		$(function() {
			$("#show-select-<?php echo $pos; ?>, #sort-select-<?php echo $pos; ?>").change(function() {
				$("#sort-form-<?php echo $pos; ?>").submit();
			});
		});
		</script>
		
		<form id="sort-form-<?php echo $pos; ?>" method="get" action="<?php echo $catalog->getUrl(); ?>" class="sort-form">
		
			<div id="sort">
				<label for="<?php echo 'show-select-' . $pos; ?>">Show</label>
				<?php
				$shows = Configure::read('Catalog.products_per_page_options');
				$options = array_combine($shows, $shows);
				echo $form->select('display', $options, $showingPerPage, array(
					'id' => 'show-select-' . $pos,
					'class' => 'per-page-select', 
					'name' => 'display',
					'empty' => false
				));
				?>
				<label for="<?php echo 'sort-select-' . $pos; ?>">Sort by</label>
				<?php
				$sorts = array(
					'default' => 'No sort',
					'name-asc' => 'Name A - Z',
					'name-desc' => 'Name Z - A',
					'price-desc' => 'Price High - Low',
					'price-asc' => 'Price Low - High',
				);
				echo $form->select('sortby', $sorts, $sortByOrderByCombined, array(
					'id' => 'sort-select-' . $pos, 
					'name' => 'sortby', 
					'empty' => false
				));
				?>
			</div>
			
		</form>
		
	<?php else: ?>
	
		<div id="sort">
			<ul>
				<li<?php echo ($sortBy == 'name') ? ' class="current"' : ''; ?>>
					<a href="<?php echo $catalog->getUrl(); ?>; ?>sortby=name&orderby=asc">Sort by name</a>
					<?php if (($sortBy == 'name') && ($validSorts[0]['key'] == 'default')): ?>
						<a href="<?php echo $catalog->getUrl(); ?>; ?>removesort"><img src="/img/icons/cross.png" alt="Remove" /></a>
					<?php endif; ?>
				</li>
			</ul>
			<span>Sort by price:</span>
			<ul>
				<li<?php echo (($sortBy == 'price') && ($orderBy == 'desc')) ? ' class="current"' : ''; ?>>
					<a href="<?php echo $catalog->getUrl(); ?>; ?>sortby=price&orderby=desc">High</a>
					<?php if (($sortBy == 'price') && ($orderBy == 'desc')): ?>
						<a href="<?php echo $catalog->getUrl(); ?>; ?>removesort"><img src="/img/icons/cross.png" alt="Remove" /></a>
					<?php endif; ?>
				</li>
				<li<?php echo (($sortBy == 'price') && ($orderBy == 'asc')) ? ' class="current"' : ''; ?>>
					<a href="<?php echo $catalog->getUrl(); ?>; ?>sortby=price&orderby=asc">Low</a>
					<?php if (($sortBy == 'price') && ($orderBy == 'asc')): ?>
						<a href="<?php echo $catalog->getUrl(); ?>; ?>removesort"><img src="/img/icons/cross.png" alt="Remove" /></a>
					<?php endif; ?>
				</li>
			</ul>
		</div>
		
		<?php if ($paginator->hasPage(2)): ?>
			<div id="show-all">
				<?php $showAllUrl = (!empty($keyword)) ? '/catalog/search?search=' . $keyword . '&display=all' : $catalog->getPaginationUrl() . '?display=all'; ?>
				<a href="<?php echo $showAllUrl; ?>">Show All</a>
			</div>
		<?php endif; ?>
	
	<?php endif; ?>
	
</div>


