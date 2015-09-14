<?php if ($paginator->hasPage(2)): ?>
	<div id="pagination">
		<ul>
			<?php echo $paginator->prev('< Previous',  array('tag' => 'li'), null); ?>
			<?php echo $paginator->numbers(array('tag' => 'li',	'separator' => '')); ?>
			<?php echo $paginator->next('Next >', array('escape' => false, 'tag' => 'li'), null); ?>
		</ul>
	</div>
<?php endif; ?>