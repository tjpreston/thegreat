<?php

/**
 * Application
 * 
 */
$config['Site']['name']  = 'The Great British Shop Ltd';
$config['Site']['title'] = 'Great British Shop';
$config['Site']['tel'] = '+44 (0)1303 243366';
$config['Site']['fax'] = '';
$config['Site']['email'] = 'info@thegreatbritishshop.com';
$config['Site']['vat_no'] = '168638678';
$config['Site']['company_no'] = '08304747';

$config['Template']['products_in_minibasket']   = false;
$config['Template']['default_meta_keywords']    = '';
$config['Template']['default_meta_description'] = '';
$config['Template']['show_manufacturer_select'] = false;

$config['Languages']['main_lang_id'] = 1;
$config['Currencies']['main_currency_id'] = 1;

$config['Email']['from_name']   = 'Great British Shop';
$config['Email']['from_email']  = 'info@thegreatbritishshop.com';
$config['Email']['text_footer'] = "Kind Regards\n\nGreat British Shop";

$config['Email']['Forms']['default']['to'] = 'info@thegreatbritishshop.com';
$config['Email']['Forms']['trade_registration']['to'] = 'info@thegreatbritishshop.com';

$config['Static']['pages'] = array(
    'about' => 'About Us',
    'privacy' => 'Privacy',
    'terms' => 'Terms',
    'who-we-are' => 'Who We Are',
    'shop' => 'Shop',
);

/**
 * Admin
 *
 */
$config['Admin']['users'] = array(
	'thegreat' => 'o3f4p58TMa'
);

$config['Admin']['pricing']['show_special'] = true;


/**
 * Basket
 * 
 */
$config['Basket']['redirect_to'] = 'product'; // basket / product

/**
 * Giftwrapping
 *
 */
$config['Giftwrapping']['enabled'] = true;
$config['Giftwrapping']['add_msg'] = '<strong>+ add gift wrap</strong> (additional £2.50 per item)';
$config['Giftwrapping']['rem_msg'] = '<strong>- remove gift wrap</strong>';
$config['Giftwrapping']['price'] = 2.50; // Price per item (will be multiplied by quantity of items in basket)

/**
 * Catalog
 *
 */
$config['Catalog']['reserved_url_words'] = array(
	'specials', 'catalog', 'pages', 'basket', 'customers', 
	'checkout', 'search', 'referral', 'brand'
);

$config['Catalog']['max_related_products'] = 8;
$config['Catalog']['search_manufacturers'] = true;
$config['Catalog']['max_category_levels'] = 2;
$config['Catalog']['recently_viewed_limit'] = 3; // Don't set to 0! It causes errors on product pages.

$config['Catalog']['grouped_products_enabled'] = false;

$config['Catalog']['crosssells_enabled'] = false;
$config['Catalog']['related_enabled'] = true;
$config['Catalog']['recently_viewed_enabled'] = false;
$config['Catalog']['featured_products_enabled'] = true;

$config['Catalog']['manufacturer_name_in_product_urls'] = false;
$config['Catalog']['prefix_product_name_with_sku'] = false;
$config['Catalog']['prefix_product_list_name_with_manufacturer'] = false;

$config['Catalog']['manufacturer_list_in_breadcrumbs'] = false;


$config['Catalog']['visibilities'] = array(
	'catalogsearch' => 'Catalog & Search Results',
	'catalog' => 'Catalog Only',
	'search' => 'Search Results Only',
	'notindividually' => 'Not Individually',
);



$config['Customers']['login_after_register'] = true;


/**
 * Product List
 * 
 */
$config['Catalog']['default_products_per_page'] = 9;
$config['Catalog']['allow_per_page_customization'] = false;
$config['Catalog']['products_per_page_options'] = array(9, 18, 27); // All
$config['Catalog']['allow_product_sorting'] = false; 
$config['Catalog']['show_product_short_description_on_list'] = true;
$config['Catalog']['encode_product_list_name'] = false;
$config['Catalog']['product_sorts_in_selects'] = true;
$config['Catalog']['display_show_all_link'] = true;
$config['Catalog']['prefix_product_list_name_with_manufacturer'] = false;
$config['Catalog']['show_stock_on_list'] = false;
$config['Catalog']['show_new_overlay'] = false;
$config['Catalog']['show_featured_overlay'] = false;
$config['Catalog']['show_best_seller_overlay'] = false;
$config['Catalog']['show_special_offer_overlay'] = true;

/**
 * Stock Control
 * 
 */
$config['Stock']['use_stock_control'] = true; // todo
$config['Stock']['default_stock_status'] = 1;
$config['Stock']['in_stock_status'] = 1;
$config['Stock']['out_of_stock_status'] = 2;

/**
 * Product View
 * 
 */
$config['Catalog']['show_twitter'] = true;
$config['Catalog']['show_facebook'] = true;

/**
 * Checkout
 * 
 */
$config['Checkout']['confirmation_to_vendor'] = 'info@thegreatbritishshop.com';


/**
 * Images
 * 
 */
// Globals
$config['Images']['max_filesize'] = '5mb';

$config['Images']['upload_quality'] = 90;
$config['Images']['pad_colour'] = array(255, 255, 255); // RGB values for padding background colour

// Uploads
// $config['Images']['product_max_upload_width'] = 2000;
// $config['Images']['product_max_upload_height'] = 2000;
$config['Images']['product_upload_resize_width'] = 700;
$config['Images']['product_upload_resize_height'] = 700;

$config['Images']['product_list_background_color'] = 'FFFFFF';

// Paths
$config['Images']['product_original_path'] = 'img/products/original/';
$config['Images']['product_edited_path']   = 'img/products/edited/';
$config['Images']['product_large_path']    = 'img/products/large/';
$config['Images']['product_medium_path']   = 'img/products/medium/';
$config['Images']['product_thumb_path']    = 'img/products/thumb/';
$config['Images']['product_tiny_path']     = 'img/products/tiny/';
$config['Images']['category_header_path']  = 'img/categories/header/';
$config['Images']['category_list_path']    = 'img/categories/list/';

// Vars Paths
$config['Images']['var_large_path']    = 'img/vars/large/';
$config['Images']['var_medium_path']   = 'img/vars/medium/';
$config['Images']['var_thumb_path']    = 'img/vars/thumb/';
$config['Images']['var_tiny_path']     = 'img/vars/tiny/';

$config['Images']['category_header_path']  = 'img/categories/header/';
$config['Images']['category_list_path']    = 'img/categories/list/';

$config['Images']['manufacturer_path']     = 'img/manufacturers/';


// Category dimensions
$config['Images']['category_header_width']      = 227;
$config['Images']['category_header_height']     = 180;
$config['Images']['category_header_master_dim'] = 'height';
$config['Images']['category_list_width']        = 198;
$config['Images']['category_list_height']       = 123;
$config['Images']['category_list_master_dim']   = 'width';

// Manufacturer images
$config['Images']['manufacturer_width']     = 198;
$config['Images']['manufacturer_height']     = 123;
 
// Product
$config['Images']['product_tiny_width'] = 80; // check ratio
$config['Images']['product_tiny_height'] = 80;
$config['Images']['product_thumb_width'] = 198; // check ratio
$config['Images']['product_thumb_height'] = 198;
$config['Images']['product_medium_width'] = 459; // check ratio
$config['Images']['product_medium_height'] = 459;
$config['Images']['product_max_large_width'] = 1000;
$config['Images']['product_max_large_height'] = 1000;

$config['Images']['var_tiny_width'] = 80;
$config['Images']['var_tiny_height'] = 80;
$config['Images']['var_thumb_width'] = 198;
//$config['Images']['var_thumb_height'] = 123;
$config['Images']['var_thumb_height'] = 198; //Edit by Will to fix poor image uploading on variations
$config['Images']['var_medium_width'] = 459;
//$config['Images']['var_medium_height'] = 362;
$config['Images']['var_medium_height'] = 459; //Edit by Will to fix poor image uploading on variations

$config['Images']['product_max'] = 5;

// Admin thumbnail dimensions
$config['Images']['admin_product_thumb_width'] = 60;
$config['Images']['admin_product_thumb_height'] = 60;
$config['Images']['admin_category_thumb_width'] = 80;
$config['Images']['admin_category_thumb_height'] = 80;

// Placeholder images
$config['Images']['placeholder_tiny_path'] = '/img/products/no-tiny.png';
$config['Images']['placeholder_thumb_path'] = '/img/products/no-thumb.png';
$config['Images']['placeholder_medium_path'] = '/img/products/no-medium.png';


$config['Orders']['all_orders_to_country'] = false;
$config['Orders']['ref_sep'] = '-';

$config['OrderLogIcons']['1'] = 'book_edit';
$config['OrderLogIcons']['2'] = 'stop';
$config['OrderLogIcons']['3'] = 'note';
$config['OrderLogIcons']['4'] = 'lorry';
$config['OrderLogIcons']['5'] = 'tick';

$config['OrderStatuses']['failed'] = 2;
$config['OrderStatuses']['processing'] = 1;
$config['OrderStatuses']['held'] = 3;
$config['OrderStatuses']['complete'] = 4;



$config['Payments']['processor'] = 'WorldpayForm';

/**
 * Sagepay Form
 * 
 */
// $config['SagepayForm']['mode'] = 'simulator';

// $config['SagepayForm']['vendors'] = array(
// 	'test' => 'michelherbelinu',
// 	'live' => 'michelherbelinu',
// 	'simulator' => 'popwebdesign'
// );
// $config['SagepayForm']['passwords'] = array(
// 	'test' => 'RHgw52Ek2wmbXpNH',
// 	'live' => 'RHgw52Ek2wmbXpNH',
// 	'simulator' => 'McX3h2EzeXNFtMWC'
// );
// $config['SagepayForm']['emails'] = array(
// 	'test' => 'yvette@popcornwebdesign.co.uk',
// 	'live' => 'yvette@popcornwebdesign.co.uk',
// 	'simulator' => 'dev@popcornwebdesign.co.uk'
// );

//$config['SagepayForm']['tx_type'] = 'DEFERRED';
$config['SagepayForm']['tx_type'] = 'PAYMENT';
$config['SagepayForm']['tx_desc'] = 'Great British Shop Online Purchase';
$config['SagepayForm']['send_email'] = 1;
$config['SagepayForm']['email_message'] = 'Thank you very much for your order.';

/**
 * Worldpay
 *
 */
$config['Worldpay']['test_mode'] = false;
if(isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'timdev.thegreatbritishshop.com')
{
$config['Worldpay']['test_mode'] = true;
}
$config['Worldpay']['installation_id'] = '307494';

/**
 * Shipping
 * 
 */
$config['Shipping']['mode'] = 'weight'; // peritem / weight
$config['Shipping']['international_postage_weight_limit'] = 2.00;

$config['Shipping']['use_ranges'] = 'subtotal';

$config['Shipping']['free_shipping_zone_id'] = 1; // Only elegible in this shipping zone
$config['Shipping']['free_shipping_subtotal_threshold'] = 50;
$config['Shipping']['free_shipping_delivery_time'] = 'Within 3 days';

// id used for forcing courier on match products
$config['Shipping']['courier_shipping_carrier_service_id'] = 2;

//$config['Shipping']['max_items'] = 3; // Baskets containing more items will get a "contact us for a shipping cost" message.

/**
 * Tax
 * 
 */
$config['Tax']['catalog_prices_include_tax'] = true;
$config['Tax']['shipping_prices_include_tax'] = true;
$config['Tax']['show_tax_total_on_basket'] = false;
$config['Tax']['rate'] = 20;

/**
 * Wishlist
 * 
 */
$config['Wishlist']['enabled'] = false;


/**
 * Google Analytics
 *
 */
$config['GoogleAnalytics']['enabled'] = false;
$config['GoogleAnalytics']['accounts'] = array('dev' => 'UA-69525753-1', 'tgbs_all' => 'UA-69525753-2');
$config['GoogleAnalytics']['account_id'] = '';

/**
 * Stockist commission rates
 *
 */
$config['StockistCommission']['Basket']['rate'] = 20 / 100; // 20% of order total
$config['StockistCommission']['Affiliate']['trade_price_rate'] = ( 1 / 2.15 ); // Order total divided by 2.15
$config['StockistCommission']['Affiliate']['admin_fee_rate'] = 10 / 100; // 10% of order total

/**
 * Newsletter stuff
 */
// This is used when automatically subscribing users to the mailing list following a successful order.
$config['Newsletter']['CampaignMonitor']['api_key'] = 'df5910c84d1fdcdfb5075625694c6b7e';
$config['Newsletter']['CampaignMonitor']['list_id'] = 'c43673f425544e96263b9c2e42ab0802';

/**
 * LinkShare integration (for Michel Herbelin)
 */
$config['LinkShare']['merchant_id'] = '37226';

$config['Catalog']['use_tiered_customer_pricing'] = false;
