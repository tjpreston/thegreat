<div id="leftcol">
	<?php echo $this->element('catalog/featured_and_recent'); ?>&nbsp;
</div>

<div id="rightcol">

	<h1><?php echo h($record['Article']['name']); ?></h1>
	
	<p id="news-date">
		<?php echo $this->Time->format('d.m.Y', $record['Article']['published']); ?>
	</p>
	
	<p id="news-blurb"><?php echo h($record['Article']['blurb']); ?></p>
	
	<div id="news-content">
		
		<?php if (!empty($record['Article']['web_path'])): ?>
			<img id="article-img" src="<?php echo $record['Article']['web_path']; ?>" alt="" />
		<?php endif; ?>
		
		<?php echo $record['Article']['content']; ?>
		
		<p id="more-news"><a href="/news">&lt; More Hytek News</a></p>
		
	</div>
	
</div>