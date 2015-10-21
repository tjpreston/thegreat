<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.app.config
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

require(APP . 'config' . DS . 'oldsite_rewrites' . DS . 'rewrite.php');

/*
 * Defaults
 * 
 */
Router::connect('/', array('controller' => 'home', 'action' => 'index'));
Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

Router::connect('/sitemap.xml', array('controller' => 'sitemap', 'action' => 'xml_sitemap'));

/*
 * Regular
 * 
 */
$controllers = array(
	'catalog', 'basket', 'checkout', 'customers', 'wishlist', 
	'currencies', 'orders', 'customer_addresses', 'newsletter',
	'newsletter_recipients','contact', /*'stockists', */'quick_order',
);

foreach ($controllers as $controller)
{
	Router::connect('/' . $controller . '/:action/*', array('controller' => $controller, 'action' => ':action'));
	Router::connect('/' . $controller, array('controller' => $controller, 'action' => 'index'));
}
 
// Router::connect('/products/:action/*', array('controller' => 'products', 'action' => ':action'));
// Router::connect('/categories/:action/*', array('controller' => 'categories', 'action' => ':action'));
// Router::connect('/product_images/:action/*', array('controller' => 'product_images', 'action' => ':action'));


Router::connect('/specials/*', array('controller' => 'catalog', 'action' => 'specials'));
Router::connect('/search/*', array('controller' => 'catalog', 'action' => 'search'));
Router::connect('/referral/send', array('controller' => 'referral', 'action' => 'send'));
Router::connect('/referral/*', array('controller' => 'referral', 'action' => 'index'));

Router::connect('/catalogue/*', array('controller' => 'forms', 'action' => 'index', 'catalogue'));
Router::connect('/trade_registration', array('controller' => 'forms', 'action' => 'index', 'trade_registration'));

/*
 * Ajax
 */
Router::connect('/ajax/catalog/get_price_and_stock/*', array('controller' => 'catalog', 'action' => 'get_price_and_stock', 'ajax' => true));
Router::connect('/ajax/catalog/get_var_images_box/*', array('controller' => 'catalog', 'action' => 'get_var_images_box', 'ajax' => true));
Router::connect('/ajax/catalog/get_available_vars/*', array('controller' => 'catalog', 'action' => 'get_available_vars', 'ajax' => true));

Router::connect('/ajax/quick_order/find_product/*', array('controller' => 'quick_order', 'action' => 'find_product', 'ajax' => true));
Router::connect('/ajax/quick_order/get_product/*', array('controller' => 'quick_order', 'action' => 'get_product', 'ajax' => true));



/*
 * Admin
 * 
 */
$controllers = array(
	'products', 'categories', 'customers', 'customer_groups', 'customer_addresses', 'orders', 'import',
	'product_images', 'related_products', 'shipments', 'order_notes', 'manufacturers',
	'config', 'custom_options', 'tax_rates', 'currencies', 'product_cross_sells',
	'grouped_products', 'coupons', 'basket_discounts', 'product_option_stock_images',
	'attributes', 'attribute_sets','newsletter_recipients',
	/*'stockists', 'stockist_commissions', */'product_price_discounts','staticpages','staticpages_images'
);

foreach ($controllers as $controller)
{
	Router::connect('/admin/' . $controller, array('controller' => $controller, 'action' => 'index', 'admin' => true));
	Router::connect('/admin/' . $controller . '/:action/*', array('controller' => $controller, 'action' => ':action', 'admin' => true));
}

Router::connect('/admin', array('controller' => 'products', 'action' => 'index', 'admin' => true));


Router::connect('/asset_compress/:css_files/:action/*', array('plugin' => 'asset_compress', 'controller' => 'css_files', 'action' => ':action'));

/* Paypal IPN plugin */
  Router::connect('/paypal_ipn/process', array('plugin' => 'paypal_ipn', 'controller' => 'instant_payment_notifications', 'action' => 'process'));
  /* Optional Route, but nice for administration */
  //Router::connect('/paypal_ipn/:action/*', array('admin' => 'true', 'plugin' => 'paypal_ipn', 'controller' => 'instant_payment_notifications', 'action' => 'index'));
  /* End Paypal IPN plugin */


/*
 * Catch-all for SEO-friendly categories & products
 * 
 */
Router::connect('/*', array('controller' => 'catalog', 'action' => 'index'));

