<div id="leftcol">
	<?php echo $this->element('catalog/featured_and_recent'); ?>&nbsp;
</div>

<div id="rightcol">

	<?php echo $this->element('catalog/category_header', array(
		'name' => 'Latest News From Hytek',
		'intro' => 'Keep up to date with the latest Hytek and industry news here. Just select a news item below to view the full article.'
	)); ?>
	
	<div id="news-list">
	
		<?php foreach ($records as $record): ?>
		
			<div>
				<h2><a href="/news/<?php echo $record['Article']['slug']; ?>"><?php echo h($record['Article']['name']); ?></a></h2>
				<p class="news-list-date"><?php echo $this->Time->format('d.m.Y', $record['Article']['published']); ?></p>
				<p><?php echo h($record['Article']['blurb']); ?> <a href="/news/<?php echo $record['Article']['slug']; ?>">Read More</a></p>
			</div>
		
		<?php endforeach; ?>
		
	</div>

</div>
