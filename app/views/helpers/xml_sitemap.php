<?php

/**
 * Used to help generate XML Sitemaps
 */

class XmlSitemapHelper extends AppHelper {
	public function start(){
		return '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' . "\n" .
			   '<urlset ' . 
			   'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ' .
			   'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ' .
			   'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 ' .
			   'http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n";
	}

	public function end(){
		return '</urlset>';
	}

	public function url($url, $priority = 0.5){
		$priority = number_format($priority, 4, '.', '');
		if(stripos(substr($url, 0, 8), '://') === FALSE){
			if(substr($url, 0, 1) != '/') $url = '/' . $url;
			$url = 'http://' . env('SERVER_NAME') . $url;
		}

		return '	<url>' . "\n" . 
			   '		<loc>' . $url . '</loc>' . "\n" .
			   '		<priority>' . $priority . '</priority>' . "\n" .
			   '	</url>' . "\n";
	}
}