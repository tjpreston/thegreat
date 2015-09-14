<ul class="lang-nav">
	<?php foreach ($languages as $languageID => $languageName): ?>
		<li>
			<a id="lang-<?php echo intval($languageID); ?>" href="#">
				<?php echo h($languageName); ?>
			</a>
		</li>
	<?php endforeach; ?>
</ul>