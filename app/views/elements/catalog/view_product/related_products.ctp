<?php if (!empty($record['RelatedProduct'])): ?>
	<div class="related-products">
		<p class="heading">You May Also Like</p>
		<div class="clearfix"></div>


		<div class="related-prev">Previous</div>
		<div class="related-next">Next</div>
		<div class="cycle">
			<ul>
				<?php foreach($record['RelatedProduct'] as $k => $relProd): ?>
				<li>
					<div class="related">
						<a href="<?php echo $this->Catalog->getProductUrl($relProd); ?>">
							<div class="related-image">
								<img src="<?php echo $relProd['Product']['main_thumb_image_path']; ?>" alt="<?php echo h($relProd['ProductName']['name']); ?>">
							</div>
						</a>
						<div class="info">
							<h3 class="border-top-bottom"><?php echo h($relProd['ProductName']['name']); ?></h3>
							<a class="border-top-bottom right" href="<?php echo $this->Catalog->getProductUrl($relProd); ?>">
								<span class="face2">More</span>
							</a>
							<h2 class="first-color"><?php echo $activeCurrencyHTML; ?><?php echo number_format($relProd['ProductPrice']['active_price'], 2); ?></h2>
						</div>
					</div>
				</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
<?php endif; ?>
