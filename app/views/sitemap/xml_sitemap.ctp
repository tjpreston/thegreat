<?php

header('Content-Type: text/xml; charset=utf-8');

echo $this->XmlSitemap->start();

echo $this->XmlSitemap->url('/', 1);

foreach($products as $product){
	echo $this->XmlSitemap->url($product['ProductMeta']['url'], 0.6);
}

foreach($categories as $category){
	echo $this->XmlSitemap->url($category['CategoryName']['full_url'], 0.5);
}

foreach($pages as $i => $page){
	$url = '/pages/' . $i;
	echo $this->XmlSitemap->url($url, 0.4);
}

echo $this->XmlSitemap->end();

?>