<?php

$prev = false;
if(!empty($neighbors['prev'])){
	$prev = $neighbors['prev'];
}

$next = false;
if(!empty($neighbors['next'])){
	$next = $neighbors['next'];
}

unset($neighbors);

?>
<div class="product-paging">
<?php if($prev && false): ?>
	<a href="/<?php echo $prev['ProductMeta']['url']; ?>" class="prev">...Prev</a>
<?php //else: ?>
	<a class="prev-disabled">...Prev</a>
<?php endif; ?>

<?php if ($this->Session->check('Site.last_page')): ?>
	<?php $lastPage = $this->Session->read('Site.last_page'); ?>
	<a href="<?php echo key($lastPage); ?>">Back to <?php echo h($lastPage[key($lastPage)]); ?></a>
<?php else: ?>
	<a href="/">Back to Homepage</a>
<?php endif; ?>

<?php if($next && false): ?>
	<a href="/<?php echo $next['ProductMeta']['url']; ?>" class="next">...Next</a>
<?php //else: ?>
	<a class="next-disabled">...Next</a>
<?php endif; ?>
</div>