<?php
header('Content-type: text/html; charset=utf-8');
?><!DOCTYPE html><html>
<head>
	<title></title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=1056px" />
	<link rel="stylesheet" href="/css/vendors/normalize.css" />
	<link rel="stylesheet" href="/css/grid.css" />
	<link rel="stylesheet" href="/css/greatbritishshop.css" />
	<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<body>
	<header class="container_24">
		<div class="top-nav clearfix">
			<nav id="account-nav">
				<ul>
					<li><a href="#">Register</a></li>
					<li><a href="#">Login</a></li>
				</ul>
			</nav>
			<div id="mini-basket">
				Your Basket 10 Items £943.00
			</div>
		</div>
		<div class="grid_24 header-main">
			<div class="grid_13 alpha">
				<a href="/" id="logo"><img src="/img/logo.png" alt="The Great British Shop Ltd" /></a>
			</div>
			<div class="grid_7 prefix_1 search">
				<form id="search" action="/" method="get">
					<input type="text" placeholder="Search Site" />
					<button type="submit"><img src="/img/search-submit.png" alt="Search" /></button>
				</form>
			</div>
			<div class="grid_3 omega gift-finder">
				<a href="#" class="dual">
					<span class="face1">Gift</span> 
					<span class="face2">Finder</span>
				</a>
			</div>
		</div>
		<div class="grid_24 header-nav">
			<nav id="sitenav">
				<ul class="clearfix">
					<li><a href="#">Home</a></li>
					<li>
						<a href="#">Kitchen</a>
						<ul>
							<li><a href="#">Item One</a></li>
							<li><a href="#">Item Two</a></li>
							<li><a href="#">Item Three</a></li>
							<li><a href="#">Item Four</a></li>
							<li><a href="#">Item Five</a></li>
						</ul>
					</li>
					<li><a href="#">Baby/Child</a></li>
					<li><a href="#">Bath/Body</a></li>
					<li><a href="#">Jewellery</a></li>
					<li><a href="#">Accessories &amp; Leather</a></li>
					<li><a href="#">Recycled</a></li>
					<li><a href="#">Gifts</a></li>
					<li><a href="#">Stationary &amp; Games</a></li>
					<li><a href="#">Pets &amp; Garden</a></li>
					<li><a href="#">Ceramics</a></li>
				</ul>
			</nav>
		</div>
	</header>
	<section class="container_24" id="content" style="min-height: 600px">
		<div class="grid_24">
			Banners
		</div>
		<div class="grid_12">
			Amazing British Quality
		</div>
		<div class="grid_12">
			Gift Finder
		</div>
	</section>
	<footer id="sitefoot">
		<div class="container_24">
			<section class="grid_8 products">
				<h3>Our Products</h3>
				<div class="grid_4 alpha">
					<ul>
						<li><a href="#">Home</a></li>
						<li><a href="#">Kitchen</a></li>
						<li><a href="#">Baby/Child</a></li>
						<li><a href="#">Bath/Body</a></li>
						<li><a href="#">Jewellery</a></li>
						<li><a href="#">Accessories</a></li>
					</ul>
				</div>
				<div class="grid_4 omega">
					<ul>
						<li><a href="#">Leather</a></li>
						<li><a href="#">Recycled</a></li>
						<li><a href="#">Gifts</a></li>
						<li><a href="#">Stationary/Games</a></li>
						<li><a href="#">Pets &amp; Garden</a></li>
						<li><a href="#">Ceramics</a></li>
					</ul>
				</div>
			</section>
			<section class="grid_4 about">
				<h3>About Us</h3>
				<ul>
					<li><a href="#">Who Are We</a></li>
					<li><a href="#">How We Do It</a></li>
					<li><a href="#">Our Products</a></li>
					<li><a href="#">Latest News</a></li>
				</ul>
			</section>
			<section class="grid_5 customer-services">
				<h3>Customer Services</h3>
				<ul>
					<li><a href="#">Delivery</a></li>
					<li><a href="#">Returns</a></li>
					<li><a href="#">Privacy Policy</a></li>
					<li><a href="#">Payment Methods</a></li>
					<li><a href="#">Terms &amp; Conditions</a></li>
					<li><a href="#">Contact Us</a></li>
				</ul>
			</section>
			<section class="grid_7">
				<img src="/img/logo-footer.png" alt="The Great British Shop Ltd" />
			</section>
		</div>
	</footer>
</body>
</html>