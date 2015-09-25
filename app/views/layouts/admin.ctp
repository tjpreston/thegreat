<?php header('Content-type: text/html; charset=UTF-8') ;?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<title><?php echo $title_for_layout; ?></title>
		<?php echo $html->charset('utf-8'); ?>
		<?php echo $html->css(array(
			'http://yui.yahooapis.com/2.8.1/build/reset/reset-min.css',
			'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/themes/redmond/jquery-ui.css',
			'admin.css', 'utils.css', 'vendors/admin_superfish'
		)); ?>
		<?php echo $html->script(array(
			'https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js',
			'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/jquery-ui.min.js',
			'vendors/hoverIntent',
			'vendors/superfish-admin',
			'http://cdn.jquerytools.org/1.2.5/all/jquery.tools.min.js',
			'admin.js'
		)); ?>
		<?php echo $scripts_for_layout; ?>
		<link href="http://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet" type="text/css">
	</head>
	<body>
		
		<div id="master">
			<div id="main">
				<div id="admin-header">
					<p><?php echo h(Configure::read('Site.name')); ?> <span>Store Administration</span></p>
					<div id="nav">
						<a id="home" href="/admin">Home</a>
                                                <a id="staticpage" href="/admin/staticpages">Edit Static Pages</a> 
						<ul class="sf-menu">
							<li id="nav-catalog">
								<span>Catalog</span>
								<ul>
									<li><a href="/admin/products">Manage Products</a></li>
									<li><a href="/admin/categories">Manage Categories</a></li>
									<!-- <li><a href="/admin/product_flags">Manage Product Flags</a></li> -->
									<li><a href="/admin/manufacturers">Manufacturers</a></li>
									<li><a href="/admin/custom_options">Product Options</a></li>
									<li><a href="/admin/attribute_sets">Attribute Sets</a></li>
									<li><a href="/admin/attributes">Attributes</a></li>
								</ul>
							</li>
							<?php /*<li id="nav-stockists">
								<span>Stockists</span>
								<ul>
									<li><a href="/admin/stockists">Manage Stockists</a></li>
									<li><a href="/admin/stockist_commissions">Commission Report</a></li>
								</ul>
							</li>*/ ?>
							<li id="nav-customers">
								<span>Customers</span>
								<ul>
									<li><a href="/admin/customers">Customers</a></li>
									<!-- <li><a href="/admin/customer_groups">Customer Groups</a></li> -->
									<!-- <li><a href="/admin/newsletter_recipients">Newsletter Customers</a></li> -->
								</ul>
							</li>
							<li id="nav-orders"><a href="/admin/orders">Orders</a></li>
							<li id="nav-discount"><a href="/admin/basket_discounts">Basket Discounts</a></li>
							<!-- <li id="nav-cms">
								<span>CMS</span>
								<ul>
									<li><a href="/admin/documents">Documents</a></li>
									<li><a href="/admin/news">News</a></li>
								</ul>
							</li> -->
						</ul>
						<div class="clear-both"></div>
					</div>
				</div>
				<div id="content">
					<div id="body">
						<?php echo $content_for_layout; ?>
					</div>
				</div>
			</div>
		</div>
		
		<div id="footer">
			<div>
				<!--<p style="width: 50%; float: right; text-align: right;">Copyright &copy; 2010-2011 Popcorn Web Design Ltd</p>-->
			</div>
		</div>
		
		<?php //echo $this->element('sql_dump'); ?>
		
	</body>
</html>
