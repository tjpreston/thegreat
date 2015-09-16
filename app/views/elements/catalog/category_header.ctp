<?php /*

<?php if (!empty($record) && empty($name)): ?>
	<h1><?php echo h($record['CategoryName']['name']); ?></h1>
<?php else: ?>
	<h1><?php echo h($name); ?></h1>
<?php endif; ?>

*/ ?>

<?php if(!empty($record['CategoryDescription']['description'])): ?>
	<h1><?php echo $record['CategoryName']['name'];?></h2>
	<div class="description dropcap">
		<?php echo $record['CategoryDescription']['description']; ?>
	</div>
<?php endif; ?>