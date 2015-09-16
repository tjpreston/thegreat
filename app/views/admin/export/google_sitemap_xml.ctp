<?php echo '<'; ?><?php echo '?'; ?>xml version="1.0" encoding="UTF-8"?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

	<url>
		<loc>http://www.thewrightbuy.co.uk/</loc>
		<changefreq>daily</changefreq>
		<priority>1</priority>
	</url>

	<url>
		<loc>http://www.thewrightbuy.co.uk/pages/contact</loc>
		<changefreq>never</changefreq>
		<priority>0.5</priority>
	</url>

	<url>
		<loc>http://www.thewrightbuy.co.uk/pages/terms</loc>
		<changefreq>never</changefreq>
		<priority>0.5</priority>
	</url>

	<url>
		<loc>http://www.thewrightbuy.co.uk/pages/sitemap</loc>
		<changefreq>never</changefreq>
		<priority>0.5</priority>
	</url>

	<url>
		<loc>http://www.thewrightbuy.co.uk/faqs</loc>
		<changefreq>never</changefreq>
		<priority>0.5</priority>
	</url>

	<url>
		<loc>http://www.thewrightbuy.co.uk/sitemap</loc>
		<changefreq>daily</changefreq>
		<priority>1</priority>
	</url>


	<!-- Categories -->

	<?php
	
	// Receives $categories, $topCategoryID
	$this->Category->clearNestedTree();
	$this->Category->generateSitemapXML($categories);
	echo $this->Category->getNestedTree();
	
	?>


	<!-- Products -->

<?php foreach ($products as $product): ?>

	<url>
		<loc>http://www.thewrightbuy.co.uk/<?php echo $product['ProductMeta']['url']; ?></loc>
		<changefreq>daily</changefreq>
		<priority>1</priority>
	</url>

<?php endforeach; ?>

</urlset>