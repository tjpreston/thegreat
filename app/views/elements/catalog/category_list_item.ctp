<?php

$href = $baseUrl . $category['CategoryName']['url'];

$class = array('category-list-item');

if ((($i) == ($onpage - 1)) || (($i) == ($onpage)))
{
	$class[] = 'bottom-row';
}

if ((($i % ($perrow)) == 0) && ($i > 0))
{
	$class[] = 'omega';
}

if ((($i % ($perrow)) == 1))
{
	$class[] = 'alpha';
}

$class[] = 'grid_6';
$class[] = 'list-item';

if (!empty($class))
{
	$classHtml = ' class="' . implode(' ', $class) . '"';
}

?>

<div<?php echo $classHtml; ?>>
	<a href="<?php echo $href; ?>">
		<?php echo $this->element('catalog/category_list_item_image', array('category' => $category)); ?>
	</a>

	<div class="list-item-details">
		<h2 class="border-top-bottom">
			<a href="<?php echo $href; ?>"><?php echo h($category['CategoryName']['name']); ?></a>
		</h2>
	</div>
</div>
