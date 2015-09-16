<?php

$href = $baseUrl . $category['CategoryName']['url'];

$class = array('category-list');

if ((($i) == ($onpage - 1)) || (($i) == ($onpage)))
{
	$class[] = 'bottom-row';
}

if ((($i % ($perrow)) == 0) && ($i > 0))
{
	$class[] = 'end-row';
}

if (!empty($class))
{
	$classHtml = ' class="' . implode(' ', $class) . '"';
}

?>

<div<?php echo $classHtml; ?>>
	
	
	<ul>
		<li><a href="<?php echo $href; ?>"><?php echo h($category['CategoryName']['name']); ?></a></li>
	</ul>
	
</div>


