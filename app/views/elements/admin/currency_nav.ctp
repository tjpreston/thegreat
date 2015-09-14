<ul class="currency-nav">
	<?php foreach ($currencies as $c => $currency): ?>
		<?php $cID = $currency['Currency']['id']; ?>
		<?php $cName = $currency['Currency']['name']; ?>
		<li><a href="#" class="currency-<?php echo $cID; ?>"><?php echo h($cName); ?></a></li>
	<?php endforeach; ?>
</ul>