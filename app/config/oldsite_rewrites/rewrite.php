<?php

require 'product_rules.php';

$requestUrl = env('REQUEST_URI');

if(isset($productRules[$requestUrl])){
	// This is a request that needs redirecting
	$Product = ClassRegistry::init('Product');

	$url = $Product->query("SELECT pm.url FROM products AS p, product_metas AS pm WHERE p.active = 1 AND p.id = pm.product_id AND p.id = {$productRules[$requestUrl]}");

	if(!empty($url[0]['pm']['url'])){
		$url = '/' . $url[0]['pm']['url'];
	} else {
		// If the product could not be found, redirect to the homepage
		$url = '/';
	}

	header('HTTP/1.1 301 Moved Permanently');
	header('Location: ' . $url);
	exit;
}

unset($productRules);

require 'page_rules.php';

if(isset($pageRules[$requestUrl])){
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: ' . $pageRules[$requestUrl]);
	exit;
}

unset($pageRules);

unset($requestUrl);