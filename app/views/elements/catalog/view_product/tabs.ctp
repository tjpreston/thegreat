<script>
$(function() {
	$("#pane-nav").tabs("div#other-panes > div");
})
</script>

<div id="pane-nav-box">
	<ul id="pane-nav">
		
		<li class="first"><a href="/">Description</a></li>
		
		<?php if (!empty($record['ProductDescription']['specification_array'])): ?>
			<li><span><a href="/">Tech Specs</a></span></li>
		<?php endif; ?>
		
		<!--<?php if (!empty($record['Product']['youtube_1'])): ?>
			<li><span><a href="/">Videos</a></span></li>
		<?php endif; ?>
		
		<?php if (!empty($record['Document'])): ?>
			<li><span><a href="/">Documents</a></span></li>
		<?php endif; ?>-->
		
		<?php if (!empty($record['RelatedProduct'])): ?>
			<li class="last"><span><a href="/">You Might Also Be Interested In</a></span></li>
		<?php endif; ?>
		
	</ul>
</div>
<div class="clear-left"></div>		
<div id="other-panes">
	
	<div id="long-desc">
		<?php echo nl2br($this->Catalog->cleanSmartQuotes($record['ProductDescription']['long_description'])); ?>
	</div>
	
	<?php if (!empty($record['ProductDescription']['specification_array'])): ?>
		<div id="spec">
			<?php echo $this->Catalog->formatSpec($record['ProductDescription']['specification_array']); ?>
		</div>
	<?php endif; ?>
	
	<?php if (!empty($record['Product']['youtube_1'])): ?>
		<div id="video">
			<?php echo $record['Product']['youtube_1']; ?>
			<?php if (!empty($record['Product']['youtube_2'])): ?>
				<?php echo $record['Product']['youtube_2']; ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	
	<?php if (!empty($record['Document'])): ?>
		<div id="docs">
			<ul>
				<?php foreach ($record['Document'] as $doc): ?>
					<?php $path = Configure::read('Documents.path') . $doc['filename']; ?>
					<?php if (file_exists(WWW_ROOT . $path)): ?>
						<li style="background-image: url(/img/icons/file_extension_<?php echo h($doc['ext']); ?>.png);">
							<a href="/<?php echo $path; ?>" class="doc">
								<?php echo (!empty($doc['display_name'])) ? h($doc['display_name']) : h($doc['name']); ?>
							</a>
							<?php if (!empty($doc['description'])): ?>
								<p><?php echo h($doc['description']); ?></p>
							<?php endif; ?>
						</li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
	
	<?php if (!empty($record['RelatedProduct'])): ?>
		<div>
			<?php echo $this->element('catalog/view_product/related_products'); ?>
		</div>
	<?php endif; ?>
	

</div>
<?php if (!empty($record['Document'])): ?>
	<script>
	$("#docs a").click(function() {
		window.open(this.href);
		return false;
	});
	</script>
<?php endif; ?>

		
		
		