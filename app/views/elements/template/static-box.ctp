<?php

// Figure out what group this page is in
$pageGroups = Configure::read('PageGroups');

$url = $this->here;

$page_group = null;
foreach($pageGroups as $group => $pages){
	foreach($pages as $key => $p){
		if(stripos($url, $p['url']) === 0){
			$page_group = $group;
			$page_config = $p;
		}
	}
}

if(!empty($page_group) && !empty($page_config)):

$pages_in_group = Configure::read('PageGroups.' . $page_group);
$pages = array();

foreach($pages_in_group as $k => $v){
	$pages[$v['url']] = $v['title'];
}

?>
<div style="margin-bottom:10px">
	<img class="active" src="/img/static/<?php echo $page_config['image']; ?>" />
</div>
<div id="static-box">
	<ul>
		<?php foreach($pages as $k => $v): ?>
		<li<?php if($page_config['url'] == $k) echo ' class="select"'; ?>><a href="<?php echo $k; ?>"><?php echo $v; ?></a></li>
		<?php endforeach; ?>
	</ul>
	<p><?php echo h($page_config['quote']); ?></p>
</div>
<?php endif; ?>