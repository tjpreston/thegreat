/*
MySQL Data Transfer
Source Host: mother
Source Database: michelherbelin-esper
Target Host: mother
Target Database: michelherbelin-esper
Date: 06/11/2012 09:45:30
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for attribute_names
-- ----------------------------
CREATE TABLE `attribute_names` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `language_id` tinyint(3) unsigned NOT NULL,
  `attribute_id` smallint(5) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `display_name` varchar(50) NOT NULL,
  `url` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_lang_attr` (`language_id`,`attribute_id`),
  KEY `fk_attribute_name_attribute` (`attribute_id`),
  CONSTRAINT `fk_attribute_name_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_attribute_name_language` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for attribute_set_names
-- ----------------------------
CREATE TABLE `attribute_set_names` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `language_id` tinyint(3) unsigned NOT NULL,
  `attribute_set_id` tinyint(3) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_lang_set` (`language_id`,`attribute_set_id`),
  KEY `fk_attribute_set_name_set` (`attribute_set_id`),
  CONSTRAINT `fk_attribute_set_lang` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_attribute_set_name_set` FOREIGN KEY (`attribute_set_id`) REFERENCES `attribute_sets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for attribute_sets
-- ----------------------------
CREATE TABLE `attribute_sets` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `attribute_ids_concat` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for attribute_sets_attributes
-- ----------------------------
CREATE TABLE `attribute_sets_attributes` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `attribute_set_id` tinyint(3) unsigned NOT NULL,
  `attribute_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_attribute_x_set_attribute_set` (`attribute_set_id`),
  KEY `fk_attribute_x_set_attribute` (`attribute_id`),
  CONSTRAINT `fk_attribute_x_set_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_attribute_x_set_attribute_set` FOREIGN KEY (`attribute_set_id`) REFERENCES `attribute_sets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for attribute_value_names
-- ----------------------------
CREATE TABLE `attribute_value_names` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `language_id` tinyint(3) unsigned NOT NULL,
  `attribute_value_id` smallint(5) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `url` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_lang_value` (`language_id`,`attribute_value_id`),
  UNIQUE KEY `idx_lang_name` (`attribute_value_id`,`name`),
  CONSTRAINT `fk_attribute_value` FOREIGN KEY (`attribute_value_id`) REFERENCES `attribute_values` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_attribute_value_name_lang` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for attribute_values
-- ----------------------------
CREATE TABLE `attribute_values` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `attribute_id` smallint(5) unsigned NOT NULL,
  `sort` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_attribute_value_attribute` (`attribute_id`),
  CONSTRAINT `fk_attribute_value_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for attribute_values_products
-- ----------------------------
CREATE TABLE `attribute_values_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` smallint(5) unsigned NOT NULL,
  `attribute_value_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_avp_product` (`product_id`),
  KEY `fk_avp_av` (`attribute_value_id`),
  CONSTRAINT `fk_avp_av` FOREIGN KEY (`attribute_value_id`) REFERENCES `attribute_values` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_avp_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for attributes
-- ----------------------------
CREATE TABLE `attributes` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for basket_applied_discounts
-- ----------------------------
CREATE TABLE `basket_applied_discounts` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `basket_id` mediumint(8) unsigned NOT NULL,
  `basket_discount_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for basket_discount_prices
-- ----------------------------
CREATE TABLE `basket_discount_prices` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `basket_discount_id` smallint(5) unsigned NOT NULL,
  `currency_id` tinyint(3) unsigned NOT NULL,
  `modifier_value` decimal(7,2) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_basket_discount_price_basket_discount` (`basket_discount_id`),
  KEY `fk_basket_discount_price_currency` (`currency_id`),
  CONSTRAINT `fk_basket_discount_price_basket_discount` FOREIGN KEY (`basket_discount_id`) REFERENCES `basket_discounts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_basket_discount_price_currency` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for basket_discounts
-- ----------------------------
CREATE TABLE `basket_discounts` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `coupon_code` varchar(50) NOT NULL,
  `use_limit` smallint(5) unsigned DEFAULT NULL,
  `modifier` enum('fixed','percentage') NOT NULL,
  `modifier_percentage_value` tinyint(10) unsigned DEFAULT NULL,
  `notes` varchar(255) NOT NULL,
  `active_from` date NOT NULL,
  `active_to` date NOT NULL,
  `active` tinyint(1) unsigned NOT NULL,
  `sort` smallint(5) unsigned NOT NULL,
  `uses` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for basket_items
-- ----------------------------
CREATE TABLE `basket_items` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `basket_id` int(10) unsigned NOT NULL,
  `product_id` smallint(5) unsigned NOT NULL,
  `product_option_stock_id` smallint(5) unsigned NOT NULL,
  `qty` tinyint(3) unsigned NOT NULL,
  `taxable` tinyint(1) unsigned NOT NULL,
  `giftwrap_product_id` smallint(5) unsigned NOT NULL,
  `parent_product_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `basket_item_basket` (`basket_id`),
  KEY `basket_item_product` (`product_id`),
  CONSTRAINT `basket_item_basket` FOREIGN KEY (`basket_id`) REFERENCES `baskets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `basket_item_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for baskets
-- ----------------------------
CREATE TABLE `baskets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `hash` varchar(50) NOT NULL,
  `last_calculated_total_items` smallint(5) unsigned NOT NULL,
  `last_calculated_subtotal` decimal(7,2) unsigned NOT NULL,
  `customer_id` mediumint(50) unsigned NOT NULL,
  `customer_billing_address_id` mediumint(8) unsigned NOT NULL,
  `ship_to_billing_address` tinyint(1) unsigned NOT NULL,
  `customer_shipping_address_id` mediumint(8) unsigned NOT NULL,
  `shipping_zone_id` tinyint(3) unsigned NOT NULL,
  `shipping_country_id` smallint(5) unsigned NOT NULL,
  `shipping_carrier_service_id` smallint(5) unsigned NOT NULL DEFAULT '1',
  `free_shipping` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `last_calculated_grand_total` decimal(7,2) unsigned NOT NULL,
  `last_calculated_subtotal_tax` decimal(7,2) unsigned DEFAULT NULL,
  `last_calculated_shipping_tax` decimal(7,2) unsigned DEFAULT NULL,
  `last_calculated_discount_total` decimal(7,2) unsigned NOT NULL,
  `basket_discount_id` smallint(5) unsigned NOT NULL,
  `tax_rate` decimal(7,2) unsigned DEFAULT NULL,
  `coupon_code` varchar(50) NOT NULL,
  `delivery_date` date DEFAULT NULL,
  `delivery_am` tinyint(1) DEFAULT '0',
  `shipping_price` decimal(7,2) DEFAULT NULL,
  `order_note` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_unique_session_hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for categories
-- ----------------------------
CREATE TABLE `categories` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` smallint(5) unsigned NOT NULL,
  `lft` smallint(5) unsigned NOT NULL,
  `rght` smallint(5) unsigned NOT NULL,
  `sort_order` mediumint(8) unsigned DEFAULT NULL,
  `img_header_ext` varchar(5) NOT NULL,
  `img_list_ext` varchar(5) NOT NULL,
  `active` tinyint(1) unsigned NOT NULL,
  `featured` tinyint(1) unsigned NOT NULL,
  `display_as_landing` tinyint(1) unsigned NOT NULL,
  `product_counter` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for category_descriptions
-- ----------------------------
CREATE TABLE `category_descriptions` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `category_id` smallint(5) unsigned NOT NULL,
  `language_id` tinyint(3) unsigned NOT NULL,
  `short_description` text NOT NULL,
  `description` text NOT NULL,
  `meta_keywords` text NOT NULL,
  `meta_description` text NOT NULL,
  `page_title` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_unique_cat_lang_desc` (`category_id`,`language_id`) USING BTREE,
  KEY `category_desc_language` (`language_id`),
  CONSTRAINT `category_desc_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `category_desc_language` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for category_featured_products
-- ----------------------------
CREATE TABLE `category_featured_products` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` smallint(5) unsigned NOT NULL,
  `product_id` smallint(5) unsigned NOT NULL,
  `sort` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_cfp_category` (`category_id`),
  KEY `fk_cfp_product` (`product_id`),
  CONSTRAINT `fk_cfp_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_cfp_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for category_names
-- ----------------------------
CREATE TABLE `category_names` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `category_id` smallint(5) unsigned NOT NULL,
  `language_id` tinyint(3) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `url` varchar(100) NOT NULL,
  `full_url` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_unique_cat_lang_name` (`category_id`,`language_id`) USING BTREE,
  KEY `category_name_language` (`language_id`),
  CONSTRAINT `category_name_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `category_name_language` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for countries
-- ----------------------------
CREATE TABLE `countries` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `iso` varchar(5) NOT NULL,
  `upper_name` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `iso3` varchar(5) DEFAULT NULL,
  `numcode` smallint(5) unsigned DEFAULT NULL,
  `sort` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=247 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for currencies
-- ----------------------------
CREATE TABLE `currencies` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `code` varchar(5) NOT NULL,
  `symbol` varchar(5) NOT NULL,
  `html` varchar(10) NOT NULL,
  `exchange_rate` double NOT NULL,
  `exchange_rate_from` tinyint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for custom_option_names
-- ----------------------------
CREATE TABLE `custom_option_names` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `custom_option_id` mediumint(8) unsigned NOT NULL,
  `language_id` tinyint(3) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `FK_custom_option_id` (`custom_option_id`,`language_id`,`name`) USING BTREE,
  KEY `FK_custom_option_name_lang` (`language_id`),
  CONSTRAINT `FK_custom_option_id` FOREIGN KEY (`custom_option_id`) REFERENCES `custom_options` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_custom_option_name_lang` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for custom_option_value_names
-- ----------------------------
CREATE TABLE `custom_option_value_names` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `custom_option_value_id` mediumint(8) unsigned NOT NULL,
  `language_id` tinyint(3) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `FK_custom_option_value` (`custom_option_value_id`,`language_id`,`name`) USING BTREE,
  KEY `FK_custom_option_value_name_lang` (`language_id`),
  CONSTRAINT `FK_custom_option_value` FOREIGN KEY (`custom_option_value_id`) REFERENCES `custom_option_values` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_custom_option_value_name_lang` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for custom_option_values
-- ----------------------------
CREATE TABLE `custom_option_values` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `custom_option_id` mediumint(8) unsigned NOT NULL,
  `sort` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_custom_option` (`custom_option_id`),
  CONSTRAINT `FK_custom_option` FOREIGN KEY (`custom_option_id`) REFERENCES `custom_options` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for custom_options
-- ----------------------------
CREATE TABLE `custom_options` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for customer_addresses
-- ----------------------------
CREATE TABLE `customer_addresses` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `customer_id` mediumint(8) unsigned NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `address_1` varchar(100) NOT NULL,
  `address_2` varchar(100) NOT NULL,
  `town` varchar(50) NOT NULL,
  `country_id` smallint(5) unsigned NOT NULL,
  `county` varchar(50) NOT NULL,
  `postcode` varchar(20) NOT NULL,
  `first_use` tinyint(1) unsigned NOT NULL,
  `basket_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_customer_address_customer` (`customer_id`),
  CONSTRAINT `FK_customer_address_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for customer_groups
-- ----------------------------
CREATE TABLE `customer_groups` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `discount_amount` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for customers
-- ----------------------------
CREATE TABLE `customers` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `customer_group_id` tinyint(3) unsigned NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `mobile` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `guest` tinyint(1) unsigned NOT NULL,
  `allow_payment_by_account` tinyint(1) unsigned NOT NULL,
  `pending` tinyint(1) unsigned NOT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_group_id` (`customer_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for documents
-- ----------------------------
CREATE TABLE `documents` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `display_name` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `filename` varchar(200) NOT NULL,
  `filesize` int(10) unsigned NOT NULL,
  `filetype` varchar(50) NOT NULL,
  `ext` varchar(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for forgotten_passwords
-- ----------------------------
CREATE TABLE `forgotten_passwords` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `customer_id` mediumint(10) unsigned NOT NULL,
  `hash` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_forgotten_password_customer` (`customer_id`),
  CONSTRAINT `fk_forgotten_password_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for giftwrap_product_names
-- ----------------------------
CREATE TABLE `giftwrap_product_names` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `giftwrap_product_id` tinyint(3) unsigned NOT NULL,
  `language_id` tinyint(3) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_giftwrap_product_name_giftwrap_product` (`giftwrap_product_id`),
  KEY `fk_giftwrap_product_name_language` (`language_id`),
  CONSTRAINT `fk_giftwrap_product_name_giftwrap_product` FOREIGN KEY (`giftwrap_product_id`) REFERENCES `giftwrap_products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_giftwrap_product_name_language` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for giftwrap_products
-- ----------------------------
CREATE TABLE `giftwrap_products` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `available` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for instant_payment_notifications
-- ----------------------------
CREATE TABLE `instant_payment_notifications` (
  `id` varchar(36) NOT NULL,
  `notify_version` varchar(64) DEFAULT NULL,
  `verify_sign` varchar(127) DEFAULT NULL,
  `test_ipn` int(11) DEFAULT NULL,
  `address_city` varchar(40) DEFAULT NULL,
  `address_country` varchar(64) DEFAULT NULL,
  `address_country_code` varchar(2) DEFAULT NULL,
  `address_name` varchar(128) DEFAULT NULL,
  `address_state` varchar(40) DEFAULT NULL,
  `address_status` varchar(20) DEFAULT NULL,
  `address_street` varchar(200) DEFAULT NULL,
  `address_zip` varchar(20) DEFAULT NULL,
  `first_name` varchar(64) DEFAULT NULL,
  `last_name` varchar(64) DEFAULT NULL,
  `payer_business_name` varchar(127) DEFAULT NULL,
  `payer_email` varchar(127) DEFAULT NULL,
  `payer_id` varchar(13) DEFAULT NULL,
  `payer_status` varchar(20) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `residence_country` varchar(2) DEFAULT NULL,
  `business` varchar(127) DEFAULT NULL,
  `item_name` varchar(127) DEFAULT NULL,
  `item_number` varchar(127) DEFAULT NULL,
  `quantity` varchar(127) DEFAULT NULL,
  `receiver_email` varchar(127) DEFAULT NULL,
  `receiver_id` varchar(13) DEFAULT NULL,
  `custom` varchar(255) DEFAULT NULL,
  `invoice` varchar(127) DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  `option_name1` varchar(64) DEFAULT NULL,
  `option_name2` varchar(64) DEFAULT NULL,
  `option_selection1` varchar(200) DEFAULT NULL,
  `option_selection2` varchar(200) DEFAULT NULL,
  `tax` float(10,2) DEFAULT NULL,
  `auth_id` varchar(19) DEFAULT NULL,
  `auth_exp` varchar(28) DEFAULT NULL,
  `auth_amount` int(11) DEFAULT NULL,
  `auth_status` varchar(20) DEFAULT NULL,
  `num_cart_items` int(11) DEFAULT NULL,
  `parent_txn_id` varchar(19) DEFAULT NULL,
  `payment_date` varchar(28) DEFAULT NULL,
  `payment_status` varchar(20) DEFAULT NULL,
  `payment_type` varchar(10) DEFAULT NULL,
  `pending_reason` varchar(20) DEFAULT NULL,
  `reason_code` varchar(20) DEFAULT NULL,
  `remaining_settle` int(11) DEFAULT NULL,
  `shipping_method` varchar(64) DEFAULT NULL,
  `shipping` float(10,2) DEFAULT NULL,
  `transaction_entity` varchar(20) DEFAULT NULL,
  `txn_id` varchar(19) DEFAULT NULL,
  `txn_type` varchar(20) DEFAULT NULL,
  `exchange_rate` float(10,2) DEFAULT NULL,
  `mc_currency` varchar(3) DEFAULT NULL,
  `mc_fee` float(10,2) DEFAULT NULL,
  `mc_gross` float(10,2) DEFAULT NULL,
  `mc_handling` float(10,2) DEFAULT NULL,
  `mc_shipping` float(10,2) DEFAULT NULL,
  `payment_fee` float(10,2) DEFAULT NULL,
  `payment_gross` float(10,2) DEFAULT NULL,
  `settle_amount` float(10,2) DEFAULT NULL,
  `settle_currency` varchar(3) DEFAULT NULL,
  `auction_buyer_id` varchar(64) DEFAULT NULL,
  `auction_closing_date` varchar(28) DEFAULT NULL,
  `auction_multi_item` int(11) DEFAULT NULL,
  `for_auction` varchar(10) DEFAULT NULL,
  `subscr_date` varchar(28) DEFAULT NULL,
  `subscr_effective` varchar(28) DEFAULT NULL,
  `period1` varchar(10) DEFAULT NULL,
  `period2` varchar(10) DEFAULT NULL,
  `period3` varchar(10) DEFAULT NULL,
  `amount1` float(10,2) DEFAULT NULL,
  `amount2` float(10,2) DEFAULT NULL,
  `amount3` float(10,2) DEFAULT NULL,
  `mc_amount1` float(10,2) DEFAULT NULL,
  `mc_amount2` float(10,2) DEFAULT NULL,
  `mc_amount3` float(10,2) DEFAULT NULL,
  `recurring` varchar(1) DEFAULT NULL,
  `reattempt` varchar(1) DEFAULT NULL,
  `retry_at` varchar(28) DEFAULT NULL,
  `recur_times` int(11) DEFAULT NULL,
  `username` varchar(64) DEFAULT NULL,
  `password` varchar(24) DEFAULT NULL,
  `subscr_id` varchar(19) DEFAULT NULL,
  `case_id` varchar(28) DEFAULT NULL,
  `case_type` varchar(28) DEFAULT NULL,
  `case_creation_date` varchar(28) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for languages
-- ----------------------------
CREATE TABLE `languages` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for manufacturers
-- ----------------------------
CREATE TABLE `manufacturers` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `url` varchar(100) NOT NULL,
  `featured` tinyint(1) unsigned NOT NULL,
  `img_ext` varchar(5) NOT NULL,
  `sort` smallint(5) unsigned NOT NULL,
  `in_nav` tinyint(1) NOT NULL DEFAULT '0',
  `in_footer` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for newsletter_recipients
-- ----------------------------
CREATE TABLE `newsletter_recipients` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(150) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for newsletters
-- ----------------------------
CREATE TABLE `newsletters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for order_items
-- ----------------------------
CREATE TABLE `order_items` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` mediumint(8) unsigned NOT NULL,
  `product_id` smallint(5) unsigned NOT NULL,
  `price` decimal(7,2) unsigned NOT NULL,
  `qty` smallint(5) unsigned NOT NULL,
  `product_sku` varchar(50) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_details` varchar(150) NOT NULL,
  `giftwrap_product_name` varchar(50) NOT NULL,
  `giftwrap_price` decimal(7,2) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_order_item_order` (`order_id`),
  KEY `fk_order_item_product` (`product_id`),
  CONSTRAINT `fk_order_item_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_order_item_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for order_notes
-- ----------------------------
CREATE TABLE `order_notes` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `order_id` mediumint(8) unsigned NOT NULL,
  `icon` tinyint(3) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `customer_notified` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_order_note_order` (`order_id`),
  CONSTRAINT `fk_order_note_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for order_statuses
-- ----------------------------
CREATE TABLE `order_statuses` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for orders
-- ----------------------------
CREATE TABLE `orders` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `ref` varchar(50) NOT NULL,
  `success` tinyint(1) unsigned NOT NULL,
  `order_status_id` tinyint(3) unsigned NOT NULL,
  `customer_id` mediumint(8) unsigned NOT NULL,
  `currency_id` tinyint(3) unsigned NOT NULL,
  `processor` varchar(15) NOT NULL,
  `subtotal` decimal(7,2) unsigned NOT NULL,
  `shipping_carrier_service_name` varchar(50) NOT NULL,
  `shipping_cost` decimal(7,2) unsigned NOT NULL,
  `free_shipping` tinyint(1) unsigned NOT NULL,
  `tax_rate` decimal(7,2) unsigned NOT NULL,
  `subtotal_tax` decimal(7,2) unsigned NOT NULL,
  `shipping_tax` decimal(7,2) unsigned DEFAULT NULL,
  `grand_total` decimal(7,2) unsigned NOT NULL,
  `placed_from_ip` varchar(20) NOT NULL,
  `customer_last_name` varchar(100) NOT NULL,
  `customer_first_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `billing_address_1` varchar(100) NOT NULL,
  `billing_address_2` varchar(100) NOT NULL,
  `billing_town` varchar(50) NOT NULL,
  `billing_country_id` smallint(5) unsigned NOT NULL,
  `billing_county` varchar(50) NOT NULL,
  `billing_postcode` varchar(20) NOT NULL,
  `shipping_first_name` varchar(100) NOT NULL,
  `shipping_last_name` varchar(100) NOT NULL,
  `shipping_address_1` varchar(100) NOT NULL,
  `shipping_address_2` varchar(100) NOT NULL,
  `shipping_town` varchar(100) NOT NULL,
  `shipping_country_id` smallint(5) unsigned NOT NULL,
  `shipping_county` varchar(50) NOT NULL,
  `shipping_postcode` varchar(10) NOT NULL,
  `shipped` tinyint(1) unsigned NOT NULL,
  `error` varchar(200) NOT NULL,
  `coupon_code` varchar(50) NOT NULL,
  `discount_total` decimal(7,2) unsigned NOT NULL,
  `shipping_price` decimal(7,2) DEFAULT NULL,
  `order_note` text,
  PRIMARY KEY (`id`),
  KEY `fk_order_customer` (`customer_id`),
  KEY `fk_order_currency` (`currency_id`),
  CONSTRAINT `fk_order_currency` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_order_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for paypal_items
-- ----------------------------
CREATE TABLE `paypal_items` (
  `id` varchar(36) NOT NULL,
  `instant_payment_notification_id` varchar(36) NOT NULL,
  `item_name` varchar(127) DEFAULT NULL,
  `item_number` varchar(127) DEFAULT NULL,
  `quantity` varchar(127) DEFAULT NULL,
  `mc_gross` float(10,2) DEFAULT NULL,
  `mc_shipping` float(10,2) DEFAULT NULL,
  `mc_handling` float(10,2) DEFAULT NULL,
  `tax` float(10,2) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for product_categories
-- ----------------------------
CREATE TABLE `product_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` smallint(5) unsigned NOT NULL,
  `category_id` smallint(5) unsigned NOT NULL,
  `primary` tinyint(1) unsigned NOT NULL,
  `sort` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_unique_cat_prod` (`category_id`,`product_id`),
  KEY `product_category_product` (`product_id`) USING BTREE,
  CONSTRAINT `product_category_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_category_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for product_cross_sells
-- ----------------------------
CREATE TABLE `product_cross_sells` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from_product_id` smallint(5) unsigned NOT NULL,
  `to_product_id` smallint(5) unsigned NOT NULL,
  `sort` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_cross_sell_from_product` (`from_product_id`,`to_product_id`) USING BTREE,
  KEY `product_cross_sell_to_product` (`to_product_id`),
  CONSTRAINT `product_cross_sell_from_product` FOREIGN KEY (`from_product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_cross_sell_to_product` FOREIGN KEY (`to_product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for product_descriptions
-- ----------------------------
CREATE TABLE `product_descriptions` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` smallint(5) unsigned NOT NULL,
  `language_id` tinyint(3) unsigned NOT NULL,
  `short_description` text NOT NULL,
  `long_description` text NOT NULL,
  `spec_as_key_value` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `specification` text NOT NULL,
  `keywords` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_unique_prod_lang_desc` (`product_id`,`language_id`),
  KEY `product_description_language` (`language_id`),
  CONSTRAINT `product_description_language` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `product_description_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for product_documents
-- ----------------------------
CREATE TABLE `product_documents` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` smallint(5) unsigned NOT NULL,
  `document_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_product_document_document` (`document_id`),
  KEY `fk_product_document_product` (`product_id`),
  CONSTRAINT `fk_product_document_document` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_product_document_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for product_flags
-- ----------------------------
CREATE TABLE `product_flags` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `ext` varchar(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for product_flags_products
-- ----------------------------
CREATE TABLE `product_flags_products` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` smallint(5) unsigned NOT NULL,
  `product_flag_id` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fk_product_flag_product` (`product_id`,`product_flag_id`) USING BTREE,
  KEY `fk_product_flag_flag` (`product_flag_id`),
  CONSTRAINT `fk_product_flag_flag` FOREIGN KEY (`product_flag_id`) REFERENCES `product_flags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_product_flag_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for product_grouped_products
-- ----------------------------
CREATE TABLE `product_grouped_products` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `from_product_id` smallint(5) unsigned NOT NULL,
  `to_product_id` smallint(5) unsigned NOT NULL,
  `sort` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `grp_product_from` (`from_product_id`,`to_product_id`),
  KEY `grp_product_to` (`to_product_id`),
  CONSTRAINT `fk_grp_product_from` FOREIGN KEY (`from_product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_grp_product_to` FOREIGN KEY (`to_product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for product_images
-- ----------------------------
CREATE TABLE `product_images` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `product_id` smallint(5) unsigned NOT NULL,
  `ext` varchar(5) NOT NULL,
  `filename` varchar(100) NOT NULL,
  `label` varchar(200) NOT NULL,
  `sort_order` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_image_product` (`product_id`),
  CONSTRAINT `product_image_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for product_metas
-- ----------------------------
CREATE TABLE `product_metas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` smallint(10) unsigned NOT NULL,
  `language_id` tinyint(3) unsigned NOT NULL,
  `page_title` varchar(100) NOT NULL,
  `keywords` text NOT NULL,
  `description` text NOT NULL,
  `url` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_meta_product` (`product_id`),
  KEY `fk_product_meta_language` (`language_id`),
  CONSTRAINT `fk_product_meta_language` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_meta_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for product_names
-- ----------------------------
CREATE TABLE `product_names` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` smallint(5) unsigned NOT NULL,
  `language_id` tinyint(3) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `sub_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_name_product_language_unique` (`product_id`,`language_id`),
  KEY `product_name_language` (`language_id`),
  KEY `product_name_product` (`product_id`),
  CONSTRAINT `product_name_language` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `product_name_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for product_option_names
-- ----------------------------
CREATE TABLE `product_option_names` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_option_id` int(10) unsigned NOT NULL,
  `language_id` tinyint(3) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_product_option_name_product_option` (`product_option_id`),
  KEY `fk_product_option_name_lang` (`language_id`),
  CONSTRAINT `fk_product_option_name_lang` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_product_option_name_product_option` FOREIGN KEY (`product_option_id`) REFERENCES `product_options` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for product_option_stock
-- ----------------------------
CREATE TABLE `product_option_stock` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` smallint(5) unsigned NOT NULL,
  `value_ids` varchar(20) NOT NULL,
  `option_ids` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `available` tinyint(1) unsigned NOT NULL,
  `sku` varchar(255) NOT NULL,
  `stock_in_stock` tinyint(1) unsigned NOT NULL,
  `stock_base_qty` smallint(5) unsigned NOT NULL,
  `stock_allow_backorders` tinyint(1) unsigned NOT NULL,
  `stock_lead_time` varchar(50) NOT NULL,
  `stock_subtract_qty` tinyint(1) unsigned NOT NULL,
  `sort` smallint(6) NOT NULL,
  `modifier` enum('fixed','add','subtract') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_product_values` (`product_id`,`value_ids`),
  CONSTRAINT `option_stock_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for product_option_stock_discounts
-- ----------------------------
CREATE TABLE `product_option_stock_discounts` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `product_option_stock_id` mediumint(8) unsigned NOT NULL,
  `customer_group_id` tinyint(3) unsigned NOT NULL,
  `min_qty` smallint(5) unsigned NOT NULL,
  `discount_amount` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fk_option_stock_discount_stock` (`product_option_stock_id`,`customer_group_id`,`min_qty`) USING BTREE,
  KEY `fk_option_stock_discount_customer_group` (`customer_group_id`),
  CONSTRAINT `fk_option_stock_discount_customer_group` FOREIGN KEY (`customer_group_id`) REFERENCES `customer_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_option_stock_discount_stock` FOREIGN KEY (`product_option_stock_id`) REFERENCES `product_option_stock` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for product_option_stock_images
-- ----------------------------
CREATE TABLE `product_option_stock_images` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `product_option_stock_id` mediumint(8) unsigned NOT NULL,
  `label` varchar(50) NOT NULL,
  `ext` varchar(5) NOT NULL,
  `filename` varchar(50) NOT NULL,
  `sort_order` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for product_option_stock_prices
-- ----------------------------
CREATE TABLE `product_option_stock_prices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_option_stock_id` mediumint(10) unsigned NOT NULL,
  `currency_id` tinyint(3) unsigned NOT NULL,
  `modifier_value` decimal(7,2) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `FK_product_option_value_price_product_option_value` (`product_option_stock_id`,`currency_id`) USING BTREE,
  KEY `FK_product_option_value_price_currency` (`currency_id`),
  CONSTRAINT `FK_product_option_value_price_currency` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_product_opton_stock_price_stock` FOREIGN KEY (`product_option_stock_id`) REFERENCES `product_option_stock` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for product_option_values
-- ----------------------------
CREATE TABLE `product_option_values` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_option_id` int(10) unsigned NOT NULL,
  `custom_option_value_id` mediumint(5) unsigned NOT NULL,
  `sort` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_product_option_value_product_option` (`product_option_id`),
  KEY `fk_product_option_custom_option_value` (`custom_option_value_id`),
  CONSTRAINT `fk_product_option_custom_option_value` FOREIGN KEY (`custom_option_value_id`) REFERENCES `custom_option_values` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_product_option_value_product_option` FOREIGN KEY (`product_option_id`) REFERENCES `product_options` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for product_options
-- ----------------------------
CREATE TABLE `product_options` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` smallint(5) unsigned NOT NULL,
  `custom_option_id` mediumint(8) unsigned NOT NULL,
  `sort` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_product_option_product` (`product_id`),
  KEY `FK_procuct_option_custom_option` (`custom_option_id`),
  CONSTRAINT `FK_procuct_option_custom_option` FOREIGN KEY (`custom_option_id`) REFERENCES `custom_options` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_product_option_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for product_price_discounts
-- ----------------------------
CREATE TABLE `product_price_discounts` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` smallint(5) unsigned NOT NULL,
  `customer_group_id` tinyint(3) unsigned NOT NULL,
  `min_qty` smallint(5) unsigned NOT NULL,
  `discount_amount` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_unqiue_discount` (`product_id`,`customer_group_id`,`min_qty`),
  KEY `fk_discount_customer_group` (`customer_group_id`),
  CONSTRAINT `fk_discount_customer_group` FOREIGN KEY (`customer_group_id`) REFERENCES `customer_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_discount_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for product_prices
-- ----------------------------
CREATE TABLE `product_prices` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` smallint(5) unsigned NOT NULL,
  `currency_id` tinyint(3) unsigned NOT NULL,
  `base_price` decimal(7,2) DEFAULT NULL,
  `base_rrp` decimal(7,2) unsigned DEFAULT NULL,
  `wholesale` decimal(7,2) DEFAULT NULL,
  `special_price` decimal(7,2) unsigned DEFAULT NULL,
  `special_price_date_from` date DEFAULT NULL,
  `special_price_date_to` date DEFAULT NULL,
  `on_special` tinyint(1) unsigned DEFAULT NULL,
  `active_price` decimal(7,2) unsigned DEFAULT NULL,
  `lowest_price` decimal(7,2) unsigned DEFAULT NULL,
  `highest_price` decimal(7,2) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_unqiue_prod_currency` (`product_id`,`currency_id`),
  KEY `product_price_currency` (`currency_id`),
  CONSTRAINT `product_price_currency` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_price_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for product_related_products
-- ----------------------------
CREATE TABLE `product_related_products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from_product_id` smallint(5) unsigned NOT NULL,
  `to_product_id` smallint(5) unsigned NOT NULL,
  `sort` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_related_products_unique` (`from_product_id`,`to_product_id`),
  KEY `related_product_to_product` (`to_product_id`),
  CONSTRAINT `related_product_from_product` FOREIGN KEY (`from_product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `related_product_to_product` FOREIGN KEY (`to_product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for product_shipping_carrier_services
-- ----------------------------
CREATE TABLE `product_shipping_carrier_services` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` smallint(5) unsigned NOT NULL,
  `shipping_carrier_service_id` tinyint(3) unsigned NOT NULL,
  `available` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_product_shipping_carrier_service_product` (`product_id`),
  KEY `fk_product_shipping_carrier_service_service` (`shipping_carrier_service_id`),
  CONSTRAINT `fk_product_shipping_carrier_service_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for products
-- ----------------------------
CREATE TABLE `products` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `type` enum('simple','grouped') NOT NULL,
  `sku` varchar(20) NOT NULL,
  `sku_group` varchar(20) NOT NULL,
  `manufacturer_id` smallint(5) unsigned NOT NULL,
  `weight` float unsigned NOT NULL DEFAULT '0',
  `active` tinyint(1) unsigned NOT NULL,
  `visibility` enum('notindividually','catalogsearch','search','catalog') NOT NULL DEFAULT 'catalogsearch',
  `taxable` tinyint(1) unsigned NOT NULL,
  `free_shipping` tinyint(1) unsigned NOT NULL,
  `featured` tinyint(1) unsigned NOT NULL,
  `new_product` tinyint(1) unsigned NOT NULL,
  `best_seller` tinyint(1) unsigned NOT NULL,
  `virtual_product` tinyint(1) unsigned NOT NULL,
  `stock_in_stock` tinyint(1) unsigned NOT NULL,
  `stock_base_qty` mediumint(8) unsigned NOT NULL,
  `stock_allow_backorders` tinyint(1) unsigned NOT NULL,
  `stock_lead_time` varchar(50) NOT NULL,
  `stock_subtract_qty` tinyint(1) unsigned NOT NULL,
  `attribute_set_id` smallint(5) unsigned NOT NULL,
  `default_product_option_stock_id` mediumint(8) unsigned NOT NULL,
  `all_skus` varchar(200) NOT NULL,
  `deliverable` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `in_stock` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 11264 kB; (`main_category_id`) REFER `rsvp-stor';

-- ----------------------------
-- Table structure for referrals
-- ----------------------------
CREATE TABLE `referrals` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` smallint(5) unsigned NOT NULL,
  `sender_name` varchar(150) NOT NULL,
  `sender_email` varchar(150) NOT NULL,
  `sender_ip` varchar(20) NOT NULL,
  `customer_id` mediumint(8) unsigned NOT NULL,
  `recipient_name` varchar(150) NOT NULL,
  `recipient_email` varchar(150) NOT NULL,
  `message` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for sagepay_form_orders
-- ----------------------------
CREATE TABLE `sagepay_form_orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` mediumint(8) unsigned NOT NULL,
  `status` varchar(20) NOT NULL,
  `status_detail` varchar(255) NOT NULL,
  `vendor_tx_code` varchar(40) NOT NULL,
  `vsp_tx_id` varchar(38) NOT NULL,
  `tx_auth_no` int(11) NOT NULL,
  `amount` decimal(7,2) unsigned NOT NULL,
  `avs_cv2` varchar(50) NOT NULL,
  `address_result` varchar(20) NOT NULL,
  `postcode_result` varchar(20) NOT NULL,
  `cv2_result` varchar(20) NOT NULL,
  `3d_secure_status` varchar(50) NOT NULL,
  `cavv` varchar(32) NOT NULL,
  `payer_status` varchar(20) NOT NULL,
  `card_type` varchar(15) NOT NULL,
  `last_4_digits` varchar(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_sagepay_order` (`order_id`),
  CONSTRAINT `fk_sagepay_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for shipment_items
-- ----------------------------
CREATE TABLE `shipment_items` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `shipment_id` mediumint(8) unsigned NOT NULL,
  `order_item_id` mediumint(8) unsigned NOT NULL,
  `qty_shipped` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_shipment_item_shipment` (`shipment_id`),
  KEY `fk_shipment_item_order_item` (`order_item_id`),
  CONSTRAINT `fk_shipment_item_order_item` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_shipment_item_shipment` FOREIGN KEY (`shipment_id`) REFERENCES `shipments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for shipments
-- ----------------------------
CREATE TABLE `shipments` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `order_id` mediumint(8) unsigned NOT NULL,
  `shipping_carrier_service_name` varchar(50) NOT NULL,
  `total_weight` decimal(7,2) unsigned NOT NULL,
  `tracking_ref` varchar(50) NOT NULL,
  `shipping_first_name` varchar(50) NOT NULL,
  `shipping_last_name` varchar(50) NOT NULL,
  `shipping_address_1` varchar(100) NOT NULL,
  `shipping_address_2` varchar(100) NOT NULL,
  `shipping_town` varchar(50) NOT NULL,
  `shipping_country_id` smallint(5) unsigned NOT NULL,
  `shipping_county` varchar(50) NOT NULL,
  `shipping_postcode` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_shipmen_order` (`order_id`),
  CONSTRAINT `fk_shipmen_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for shipping_carrier_service_countries
-- ----------------------------
CREATE TABLE `shipping_carrier_service_countries` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `shipping_carrier_service_id` tinyint(3) unsigned NOT NULL,
  `shipping_zone_id` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_shipping_carrier_service_country_service` (`shipping_carrier_service_id`),
  KEY `fk_shipping_carrier_service_country_country` (`shipping_zone_id`),
  CONSTRAINT `fk_shipping_carrier_service_country_service` FOREIGN KEY (`shipping_carrier_service_id`) REFERENCES `shipping_carrier_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_shipping_carrier_service_country_zone` FOREIGN KEY (`shipping_zone_id`) REFERENCES `shipping_zones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for shipping_carrier_service_countries_per_item_prices
-- ----------------------------
CREATE TABLE `shipping_carrier_service_countries_per_item_prices` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `currency_id` tinyint(3) unsigned NOT NULL,
  `shipping_zone_id` smallint(5) unsigned NOT NULL,
  `shipping_carrier_service_id` tinyint(3) unsigned NOT NULL,
  `first_item_price` decimal(5,2) unsigned DEFAULT NULL,
  `additional_item_price` double(5,2) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_per_item_price` (`currency_id`,`shipping_carrier_service_id`,`shipping_zone_id`),
  KEY `fk_peritem_service` (`shipping_carrier_service_id`),
  KEY `fk_peritemprice_country` (`shipping_zone_id`),
  CONSTRAINT `fk_peritemprice_currency` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_peritem_service` FOREIGN KEY (`shipping_carrier_service_id`) REFERENCES `shipping_carrier_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for shipping_carrier_service_subtotal_range_country_prices
-- ----------------------------
CREATE TABLE `shipping_carrier_service_subtotal_range_country_prices` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `shipping_zone_id` tinyint(3) unsigned NOT NULL,
  `currency_id` tinyint(3) unsigned NOT NULL,
  `shipping_carrier_service_subtotal_range_id` smallint(5) unsigned NOT NULL,
  `price` double(7,2) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_service_subtotal_range_country_price_currency` (`currency_id`),
  KEY `fk_service_subtotal_range_country_price_country` (`shipping_zone_id`),
  KEY `fk_service_subtotal_range_country_price_range` (`shipping_carrier_service_subtotal_range_id`),
  CONSTRAINT `fk_service_subtotal_range_country_price_currency` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_subtotal_range_country_price_range` FOREIGN KEY (`shipping_carrier_service_subtotal_range_id`) REFERENCES `shipping_carrier_service_subtotal_ranges` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_subtotal_range_country_price_zone` FOREIGN KEY (`shipping_zone_id`) REFERENCES `shipping_zones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for shipping_carrier_service_subtotal_ranges
-- ----------------------------
CREATE TABLE `shipping_carrier_service_subtotal_ranges` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `shipping_carrier_service_id` tinyint(3) unsigned NOT NULL,
  `from` tinyint(3) unsigned NOT NULL,
  `to` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_shipping_carrier_service_subtotal_range_servce` (`shipping_carrier_service_id`),
  CONSTRAINT `fk_shipping_carrier_service_subtotal_range_servce` FOREIGN KEY (`shipping_carrier_service_id`) REFERENCES `shipping_carrier_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for shipping_carrier_services
-- ----------------------------
CREATE TABLE `shipping_carrier_services` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `shipping_carrier_id` tinyint(3) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `delivery_time` varchar(50) NOT NULL,
  `charge_tax` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_shipping_carrier_service_carrier` (`shipping_carrier_id`),
  CONSTRAINT `fk_shipping_carrier_service_carrier` FOREIGN KEY (`shipping_carrier_id`) REFERENCES `shipping_carriers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for shipping_carriers
-- ----------------------------
CREATE TABLE `shipping_carriers` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `tracking_url` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for shipping_zone_countries
-- ----------------------------
CREATE TABLE `shipping_zone_countries` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `shipping_zone_id` tinyint(3) unsigned NOT NULL,
  `country_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_shipping_zone_country_zone` (`shipping_zone_id`),
  KEY `fk_shipping_zone_country_country` (`country_id`),
  CONSTRAINT `fk_shipping_zone_country_country` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_shipping_zone_country_zone` FOREIGN KEY (`shipping_zone_id`) REFERENCES `shipping_zones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for shipping_zones
-- ----------------------------
CREATE TABLE `shipping_zones` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `sort` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tax_rates
-- ----------------------------
CREATE TABLE `tax_rates` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `country_id` smallint(5) unsigned NOT NULL,
  `rate` decimal(4,2) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for wishlist_item_option_values
-- ----------------------------
CREATE TABLE `wishlist_item_option_values` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `wishlist_item_id` mediumint(8) unsigned NOT NULL,
  `product_option_id` mediumint(8) unsigned NOT NULL,
  `product_option_value_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wishlist_items
-- ----------------------------
CREATE TABLE `wishlist_items` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `wishlist_id` mediumint(8) unsigned NOT NULL,
  `product_id` smallint(8) unsigned NOT NULL,
  `qty` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_wishlist_item_wishlist` (`wishlist_id`),
  KEY `FK_wishlist_item_product` (`product_id`),
  CONSTRAINT `FK_wishlist_item_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_wishlist_item_wishlist` FOREIGN KEY (`wishlist_id`) REFERENCES `wishlists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wishlist_recipients
-- ----------------------------
CREATE TABLE `wishlist_recipients` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `wishlist_id` mediumint(8) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `to_customer` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wishlists
-- ----------------------------
CREATE TABLE `wishlists` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `customer_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_wishlist_customer` (`customer_id`),
  CONSTRAINT `FK_wishlist_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records 
-- ----------------------------
INSERT INTO `countries` VALUES ('8', 'AF', 'AFGHANISTAN', 'Afghanistan', 'AFG', '4', '10');
INSERT INTO `countries` VALUES ('9', 'AL', 'ALBANIA', 'Albania', 'ALB', '8', '10');
INSERT INTO `countries` VALUES ('10', 'DZ', 'ALGERIA', 'Algeria', 'DZA', '12', '10');
INSERT INTO `countries` VALUES ('11', 'AS', 'AMERICAN SAMOA', 'American Samoa', 'ASM', '16', '10');
INSERT INTO `countries` VALUES ('12', 'AD', 'ANDORRA', 'Andorra', 'AND', '20', '10');
INSERT INTO `countries` VALUES ('13', 'AO', 'ANGOLA', 'Angola', 'AGO', '24', '10');
INSERT INTO `countries` VALUES ('14', 'AI', 'ANGUILLA', 'Anguilla', 'AIA', '660', '10');
INSERT INTO `countries` VALUES ('15', 'AQ', 'ANTARCTICA', 'Antarctica', null, null, '10');
INSERT INTO `countries` VALUES ('16', 'AG', 'ANTIGUA AND BARBUDA', 'Antigua and Barbuda', 'ATG', '28', '10');
INSERT INTO `countries` VALUES ('17', 'AR', 'ARGENTINA', 'Argentina', 'ARG', '32', '10');
INSERT INTO `countries` VALUES ('18', 'AM', 'ARMENIA', 'Armenia', 'ARM', '51', '10');
INSERT INTO `countries` VALUES ('19', 'AW', 'ARUBA', 'Aruba', 'ABW', '533', '10');
INSERT INTO `countries` VALUES ('20', 'AU', 'AUSTRALIA', 'Australia', 'AUS', '36', '10');
INSERT INTO `countries` VALUES ('21', 'AT', 'AUSTRIA', 'Austria', 'AUT', '40', '10');
INSERT INTO `countries` VALUES ('22', 'AZ', 'AZERBAIJAN', 'Azerbaijan', 'AZE', '31', '10');
INSERT INTO `countries` VALUES ('23', 'BS', 'BAHAMAS', 'Bahamas', 'BHS', '44', '10');
INSERT INTO `countries` VALUES ('24', 'BH', 'BAHRAIN', 'Bahrain', 'BHR', '48', '10');
INSERT INTO `countries` VALUES ('25', 'BD', 'BANGLADESH', 'Bangladesh', 'BGD', '50', '10');
INSERT INTO `countries` VALUES ('26', 'BB', 'BARBADOS', 'Barbados', 'BRB', '52', '10');
INSERT INTO `countries` VALUES ('27', 'BY', 'BELARUS', 'Belarus', 'BLR', '112', '10');
INSERT INTO `countries` VALUES ('28', 'BE', 'BELGIUM', 'Belgium', 'BEL', '56', '10');
INSERT INTO `countries` VALUES ('29', 'BZ', 'BELIZE', 'Belize', 'BLZ', '84', '10');
INSERT INTO `countries` VALUES ('30', 'BJ', 'BENIN', 'Benin', 'BEN', '204', '10');
INSERT INTO `countries` VALUES ('31', 'BM', 'BERMUDA', 'Bermuda', 'BMU', '60', '10');
INSERT INTO `countries` VALUES ('32', 'BT', 'BHUTAN', 'Bhutan', 'BTN', '64', '10');
INSERT INTO `countries` VALUES ('33', 'BO', 'BOLIVIA', 'Bolivia', 'BOL', '68', '10');
INSERT INTO `countries` VALUES ('34', 'BA', 'BOSNIA AND HERZEGOVINA', 'Bosnia and Herzegovina', 'BIH', '70', '10');
INSERT INTO `countries` VALUES ('35', 'BW', 'BOTSWANA', 'Botswana', 'BWA', '72', '10');
INSERT INTO `countries` VALUES ('36', 'BV', 'BOUVET ISLAND', 'Bouvet Island', null, null, '10');
INSERT INTO `countries` VALUES ('37', 'BR', 'BRAZIL', 'Brazil', 'BRA', '76', '10');
INSERT INTO `countries` VALUES ('38', 'IO', 'BRITISH INDIAN OCEAN TERRITORY', 'British Indian Ocean Territory', null, null, '10');
INSERT INTO `countries` VALUES ('39', 'BN', 'BRUNEI DARUSSALAM', 'Brunei Darussalam', 'BRN', '96', '10');
INSERT INTO `countries` VALUES ('40', 'BG', 'BULGARIA', 'Bulgaria', 'BGR', '100', '10');
INSERT INTO `countries` VALUES ('41', 'BF', 'BURKINA FASO', 'Burkina Faso', 'BFA', '854', '10');
INSERT INTO `countries` VALUES ('42', 'BI', 'BURUNDI', 'Burundi', 'BDI', '108', '10');
INSERT INTO `countries` VALUES ('43', 'KH', 'CAMBODIA', 'Cambodia', 'KHM', '116', '10');
INSERT INTO `countries` VALUES ('44', 'CM', 'CAMEROON', 'Cameroon', 'CMR', '120', '10');
INSERT INTO `countries` VALUES ('45', 'CA', 'CANADA', 'Canada', 'CAN', '124', '10');
INSERT INTO `countries` VALUES ('46', 'CV', 'CAPE VERDE', 'Cape Verde', 'CPV', '132', '10');
INSERT INTO `countries` VALUES ('47', 'KY', 'CAYMAN ISLANDS', 'Cayman Islands', 'CYM', '136', '10');
INSERT INTO `countries` VALUES ('48', 'CF', 'CENTRAL AFRICAN REPUBLIC', 'Central African Republic', 'CAF', '140', '10');
INSERT INTO `countries` VALUES ('49', 'TD', 'CHAD', 'Chad', 'TCD', '148', '10');
INSERT INTO `countries` VALUES ('50', 'CL', 'CHILE', 'Chile', 'CHL', '152', '10');
INSERT INTO `countries` VALUES ('51', 'CN', 'CHINA', 'China', 'CHN', '156', '10');
INSERT INTO `countries` VALUES ('52', 'CX', 'CHRISTMAS ISLAND', 'Christmas Island', null, null, '10');
INSERT INTO `countries` VALUES ('53', 'CC', 'COCOS (KEELING) ISLANDS', 'Cocos (Keeling) Islands', null, null, '10');
INSERT INTO `countries` VALUES ('54', 'CO', 'COLOMBIA', 'Colombia', 'COL', '170', '10');
INSERT INTO `countries` VALUES ('55', 'KM', 'COMOROS', 'Comoros', 'COM', '174', '10');
INSERT INTO `countries` VALUES ('56', 'CG', 'CONGO', 'Congo', 'COG', '178', '10');
INSERT INTO `countries` VALUES ('57', 'CD', 'CONGO, THE DEMOCRATIC REPUBLIC OF THE', 'Congo, the Democratic Republic of the', 'COD', '180', '10');
INSERT INTO `countries` VALUES ('58', 'CK', 'COOK ISLANDS', 'Cook Islands', 'COK', '184', '10');
INSERT INTO `countries` VALUES ('59', 'CR', 'COSTA RICA', 'Costa Rica', 'CRI', '188', '10');
INSERT INTO `countries` VALUES ('60', 'CI', 'COTE D\'IVOIRE', 'Cote D\'Ivoire', 'CIV', '384', '10');
INSERT INTO `countries` VALUES ('61', 'HR', 'CROATIA', 'Croatia', 'HRV', '191', '10');
INSERT INTO `countries` VALUES ('62', 'CU', 'CUBA', 'Cuba', 'CUB', '192', '10');
INSERT INTO `countries` VALUES ('63', 'CY', 'CYPRUS', 'Cyprus', 'CYP', '196', '10');
INSERT INTO `countries` VALUES ('64', 'CZ', 'CZECH REPUBLIC', 'Czech Republic', 'CZE', '203', '10');
INSERT INTO `countries` VALUES ('65', 'DK', 'DENMARK', 'Denmark', 'DNK', '208', '10');
INSERT INTO `countries` VALUES ('66', 'DJ', 'DJIBOUTI', 'Djibouti', 'DJI', '262', '10');
INSERT INTO `countries` VALUES ('67', 'DM', 'DOMINICA', 'Dominica', 'DMA', '212', '10');
INSERT INTO `countries` VALUES ('68', 'DO', 'DOMINICAN REPUBLIC', 'Dominican Republic', 'DOM', '214', '10');
INSERT INTO `countries` VALUES ('69', 'EC', 'ECUADOR', 'Ecuador', 'ECU', '218', '10');
INSERT INTO `countries` VALUES ('70', 'EG', 'EGYPT', 'Egypt', 'EGY', '818', '10');
INSERT INTO `countries` VALUES ('71', 'SV', 'EL SALVADOR', 'El Salvador', 'SLV', '222', '10');
INSERT INTO `countries` VALUES ('72', 'GQ', 'EQUATORIAL GUINEA', 'Equatorial Guinea', 'GNQ', '226', '10');
INSERT INTO `countries` VALUES ('73', 'ER', 'ERITREA', 'Eritrea', 'ERI', '232', '10');
INSERT INTO `countries` VALUES ('74', 'EE', 'ESTONIA', 'Estonia', 'EST', '233', '10');
INSERT INTO `countries` VALUES ('75', 'ET', 'ETHIOPIA', 'Ethiopia', 'ETH', '231', '10');
INSERT INTO `countries` VALUES ('76', 'FK', 'FALKLAND ISLANDS (MALVINAS)', 'Falkland Islands (Malvinas)', 'FLK', '238', '10');
INSERT INTO `countries` VALUES ('77', 'FO', 'FAROE ISLANDS', 'Faroe Islands', 'FRO', '234', '10');
INSERT INTO `countries` VALUES ('78', 'FJ', 'FIJI', 'Fiji', 'FJI', '242', '10');
INSERT INTO `countries` VALUES ('79', 'FI', 'FINLAND', 'Finland', 'FIN', '246', '10');
INSERT INTO `countries` VALUES ('80', 'FR', 'FRANCE', 'France', 'FRA', '250', '10');
INSERT INTO `countries` VALUES ('81', 'GF', 'FRENCH GUIANA', 'French Guiana', 'GUF', '254', '10');
INSERT INTO `countries` VALUES ('82', 'PF', 'FRENCH POLYNESIA', 'French Polynesia', 'PYF', '258', '10');
INSERT INTO `countries` VALUES ('83', 'TF', 'FRENCH SOUTHERN TERRITORIES', 'French Southern Territories', null, null, '10');
INSERT INTO `countries` VALUES ('84', 'GA', 'GABON', 'Gabon', 'GAB', '266', '10');
INSERT INTO `countries` VALUES ('85', 'GM', 'GAMBIA', 'Gambia', 'GMB', '270', '10');
INSERT INTO `countries` VALUES ('86', 'GE', 'GEORGIA', 'Georgia', 'GEO', '268', '10');
INSERT INTO `countries` VALUES ('87', 'DE', 'GERMANY', 'Germany', 'DEU', '276', '10');
INSERT INTO `countries` VALUES ('88', 'GH', 'GHANA', 'Ghana', 'GHA', '288', '10');
INSERT INTO `countries` VALUES ('89', 'GI', 'GIBRALTAR', 'Gibraltar', 'GIB', '292', '10');
INSERT INTO `countries` VALUES ('90', 'GR', 'GREECE', 'Greece', 'GRC', '300', '10');
INSERT INTO `countries` VALUES ('91', 'GL', 'GREENLAND', 'Greenland', 'GRL', '304', '10');
INSERT INTO `countries` VALUES ('92', 'GD', 'GRENADA', 'Grenada', 'GRD', '308', '10');
INSERT INTO `countries` VALUES ('93', 'GP', 'GUADELOUPE', 'Guadeloupe', 'GLP', '312', '10');
INSERT INTO `countries` VALUES ('94', 'GU', 'GUAM', 'Guam', 'GUM', '316', '10');
INSERT INTO `countries` VALUES ('95', 'GT', 'GUATEMALA', 'Guatemala', 'GTM', '320', '10');
INSERT INTO `countries` VALUES ('96', 'GN', 'GUINEA', 'Guinea', 'GIN', '324', '10');
INSERT INTO `countries` VALUES ('97', 'GW', 'GUINEA-BISSAU', 'Guinea-Bissau', 'GNB', '624', '10');
INSERT INTO `countries` VALUES ('98', 'GY', 'GUYANA', 'Guyana', 'GUY', '328', '10');
INSERT INTO `countries` VALUES ('99', 'HT', 'HAITI', 'Haiti', 'HTI', '332', '10');
INSERT INTO `countries` VALUES ('100', 'HM', 'HEARD ISLAND AND MCDONALD ISLANDS', 'Heard Island and Mcdonald Islands', null, null, '10');
INSERT INTO `countries` VALUES ('101', 'VA', 'HOLY SEE (VATICAN CITY STATE)', 'Holy See (Vatican City State)', 'VAT', '336', '10');
INSERT INTO `countries` VALUES ('102', 'HN', 'HONDURAS', 'Honduras', 'HND', '340', '10');
INSERT INTO `countries` VALUES ('103', 'HK', 'HONG KONG', 'Hong Kong', 'HKG', '344', '10');
INSERT INTO `countries` VALUES ('104', 'HU', 'HUNGARY', 'Hungary', 'HUN', '348', '10');
INSERT INTO `countries` VALUES ('105', 'IS', 'ICELAND', 'Iceland', 'ISL', '352', '10');
INSERT INTO `countries` VALUES ('106', 'IN', 'INDIA', 'India', 'IND', '356', '10');
INSERT INTO `countries` VALUES ('107', 'ID', 'INDONESIA', 'Indonesia', 'IDN', '360', '10');
INSERT INTO `countries` VALUES ('108', 'IR', 'IRAN, ISLAMIC REPUBLIC OF', 'Iran, Islamic Republic of', 'IRN', '364', '10');
INSERT INTO `countries` VALUES ('109', 'IQ', 'IRAQ', 'Iraq', 'IRQ', '368', '10');
INSERT INTO `countries` VALUES ('110', 'IE', 'IRELAND', 'Ireland', 'IRL', '372', '10');
INSERT INTO `countries` VALUES ('111', 'IL', 'ISRAEL', 'Israel', 'ISR', '376', '10');
INSERT INTO `countries` VALUES ('112', 'IT', 'ITALY', 'Italy', 'ITA', '380', '10');
INSERT INTO `countries` VALUES ('113', 'JM', 'JAMAICA', 'Jamaica', 'JAM', '388', '10');
INSERT INTO `countries` VALUES ('114', 'JP', 'JAPAN', 'Japan', 'JPN', '392', '10');
INSERT INTO `countries` VALUES ('115', 'JO', 'JORDAN', 'Jordan', 'JOR', '400', '10');
INSERT INTO `countries` VALUES ('116', 'KZ', 'KAZAKHSTAN', 'Kazakhstan', 'KAZ', '398', '10');
INSERT INTO `countries` VALUES ('117', 'KE', 'KENYA', 'Kenya', 'KEN', '404', '10');
INSERT INTO `countries` VALUES ('118', 'KI', 'KIRIBATI', 'Kiribati', 'KIR', '296', '10');
INSERT INTO `countries` VALUES ('119', 'KP', 'KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF', 'Korea, Democratic People\'s Republic of', 'PRK', '408', '10');
INSERT INTO `countries` VALUES ('120', 'KR', 'KOREA, REPUBLIC OF', 'Korea, Republic of', 'KOR', '410', '10');
INSERT INTO `countries` VALUES ('121', 'KW', 'KUWAIT', 'Kuwait', 'KWT', '414', '10');
INSERT INTO `countries` VALUES ('122', 'KG', 'KYRGYZSTAN', 'Kyrgyzstan', 'KGZ', '417', '10');
INSERT INTO `countries` VALUES ('123', 'LA', 'LAO PEOPLE\'S DEMOCRATIC REPUBLIC', 'Lao People\'s Democratic Republic', 'LAO', '418', '10');
INSERT INTO `countries` VALUES ('124', 'LV', 'LATVIA', 'Latvia', 'LVA', '428', '10');
INSERT INTO `countries` VALUES ('125', 'LB', 'LEBANON', 'Lebanon', 'LBN', '422', '10');
INSERT INTO `countries` VALUES ('126', 'LS', 'LESOTHO', 'Lesotho', 'LSO', '426', '10');
INSERT INTO `countries` VALUES ('127', 'LR', 'LIBERIA', 'Liberia', 'LBR', '430', '10');
INSERT INTO `countries` VALUES ('128', 'LY', 'LIBYAN ARAB JAMAHIRIYA', 'Libyan Arab Jamahiriya', 'LBY', '434', '10');
INSERT INTO `countries` VALUES ('129', 'LI', 'LIECHTENSTEIN', 'Liechtenstein', 'LIE', '438', '10');
INSERT INTO `countries` VALUES ('130', 'LT', 'LITHUANIA', 'Lithuania', 'LTU', '440', '10');
INSERT INTO `countries` VALUES ('131', 'LU', 'LUXEMBOURG', 'Luxembourg', 'LUX', '442', '10');
INSERT INTO `countries` VALUES ('132', 'MO', 'MACAO', 'Macao', 'MAC', '446', '10');
INSERT INTO `countries` VALUES ('133', 'MK', 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF', 'Macedonia, the Former Yugoslav Republic of', 'MKD', '807', '10');
INSERT INTO `countries` VALUES ('134', 'MG', 'MADAGASCAR', 'Madagascar', 'MDG', '450', '10');
INSERT INTO `countries` VALUES ('135', 'MW', 'MALAWI', 'Malawi', 'MWI', '454', '10');
INSERT INTO `countries` VALUES ('136', 'MY', 'MALAYSIA', 'Malaysia', 'MYS', '458', '10');
INSERT INTO `countries` VALUES ('137', 'MV', 'MALDIVES', 'Maldives', 'MDV', '462', '10');
INSERT INTO `countries` VALUES ('138', 'ML', 'MALI', 'Mali', 'MLI', '466', '10');
INSERT INTO `countries` VALUES ('139', 'MT', 'MALTA', 'Malta', 'MLT', '470', '10');
INSERT INTO `countries` VALUES ('140', 'MH', 'MARSHALL ISLANDS', 'Marshall Islands', 'MHL', '584', '10');
INSERT INTO `countries` VALUES ('141', 'MQ', 'MARTINIQUE', 'Martinique', 'MTQ', '474', '10');
INSERT INTO `countries` VALUES ('142', 'MR', 'MAURITANIA', 'Mauritania', 'MRT', '478', '10');
INSERT INTO `countries` VALUES ('143', 'MU', 'MAURITIUS', 'Mauritius', 'MUS', '480', '10');
INSERT INTO `countries` VALUES ('144', 'YT', 'MAYOTTE', 'Mayotte', null, null, '10');
INSERT INTO `countries` VALUES ('145', 'MX', 'MEXICO', 'Mexico', 'MEX', '484', '10');
INSERT INTO `countries` VALUES ('146', 'FM', 'MICRONESIA, FEDERATED STATES OF', 'Micronesia, Federated States of', 'FSM', '583', '10');
INSERT INTO `countries` VALUES ('147', 'MD', 'MOLDOVA, REPUBLIC OF', 'Moldova, Republic of', 'MDA', '498', '10');
INSERT INTO `countries` VALUES ('148', 'MC', 'MONACO', 'Monaco', 'MCO', '492', '10');
INSERT INTO `countries` VALUES ('149', 'MN', 'MONGOLIA', 'Mongolia', 'MNG', '496', '10');
INSERT INTO `countries` VALUES ('150', 'MS', 'MONTSERRAT', 'Montserrat', 'MSR', '500', '10');
INSERT INTO `countries` VALUES ('151', 'MA', 'MOROCCO', 'Morocco', 'MAR', '504', '10');
INSERT INTO `countries` VALUES ('152', 'MZ', 'MOZAMBIQUE', 'Mozambique', 'MOZ', '508', '10');
INSERT INTO `countries` VALUES ('153', 'MM', 'MYANMAR', 'Myanmar', 'MMR', '104', '10');
INSERT INTO `countries` VALUES ('154', 'NA', 'NAMIBIA', 'Namibia', 'NAM', '516', '10');
INSERT INTO `countries` VALUES ('155', 'NR', 'NAURU', 'Nauru', 'NRU', '520', '10');
INSERT INTO `countries` VALUES ('156', 'NP', 'NEPAL', 'Nepal', 'NPL', '524', '10');
INSERT INTO `countries` VALUES ('157', 'NL', 'NETHERLANDS', 'Netherlands', 'NLD', '528', '10');
INSERT INTO `countries` VALUES ('158', 'AN', 'NETHERLANDS ANTILLES', 'Netherlands Antilles', 'ANT', '530', '10');
INSERT INTO `countries` VALUES ('159', 'NC', 'NEW CALEDONIA', 'New Caledonia', 'NCL', '540', '10');
INSERT INTO `countries` VALUES ('160', 'NZ', 'NEW ZEALAND', 'New Zealand', 'NZL', '554', '10');
INSERT INTO `countries` VALUES ('161', 'NI', 'NICARAGUA', 'Nicaragua', 'NIC', '558', '10');
INSERT INTO `countries` VALUES ('162', 'NE', 'NIGER', 'Niger', 'NER', '562', '10');
INSERT INTO `countries` VALUES ('163', 'NG', 'NIGERIA', 'Nigeria', 'NGA', '566', '10');
INSERT INTO `countries` VALUES ('164', 'NU', 'NIUE', 'Niue', 'NIU', '570', '10');
INSERT INTO `countries` VALUES ('165', 'NF', 'NORFOLK ISLAND', 'Norfolk Island', 'NFK', '574', '10');
INSERT INTO `countries` VALUES ('166', 'MP', 'NORTHERN MARIANA ISLANDS', 'Northern Mariana Islands', 'MNP', '580', '10');
INSERT INTO `countries` VALUES ('167', 'NO', 'NORWAY', 'Norway', 'NOR', '578', '10');
INSERT INTO `countries` VALUES ('168', 'OM', 'OMAN', 'Oman', 'OMN', '512', '10');
INSERT INTO `countries` VALUES ('169', 'PK', 'PAKISTAN', 'Pakistan', 'PAK', '586', '10');
INSERT INTO `countries` VALUES ('170', 'PW', 'PALAU', 'Palau', 'PLW', '585', '10');
INSERT INTO `countries` VALUES ('171', 'PS', 'PALESTINIAN TERRITORY, OCCUPIED', 'Palestinian Territory, Occupied', null, null, '10');
INSERT INTO `countries` VALUES ('172', 'PA', 'PANAMA', 'Panama', 'PAN', '591', '10');
INSERT INTO `countries` VALUES ('173', 'PG', 'PAPUA NEW GUINEA', 'Papua New Guinea', 'PNG', '598', '10');
INSERT INTO `countries` VALUES ('174', 'PY', 'PARAGUAY', 'Paraguay', 'PRY', '600', '10');
INSERT INTO `countries` VALUES ('175', 'PE', 'PERU', 'Peru', 'PER', '604', '10');
INSERT INTO `countries` VALUES ('176', 'PH', 'PHILIPPINES', 'Philippines', 'PHL', '608', '10');
INSERT INTO `countries` VALUES ('177', 'PN', 'PITCAIRN', 'Pitcairn', 'PCN', '612', '10');
INSERT INTO `countries` VALUES ('178', 'PL', 'POLAND', 'Poland', 'POL', '616', '10');
INSERT INTO `countries` VALUES ('179', 'PT', 'PORTUGAL', 'Portugal', 'PRT', '620', '10');
INSERT INTO `countries` VALUES ('180', 'PR', 'PUERTO RICO', 'Puerto Rico', 'PRI', '630', '10');
INSERT INTO `countries` VALUES ('181', 'QA', 'QATAR', 'Qatar', 'QAT', '634', '10');
INSERT INTO `countries` VALUES ('182', 'RE', 'REUNION', 'Reunion', 'REU', '638', '10');
INSERT INTO `countries` VALUES ('183', 'RO', 'ROMANIA', 'Romania', 'ROM', '642', '10');
INSERT INTO `countries` VALUES ('184', 'RU', 'RUSSIAN FEDERATION', 'Russian Federation', 'RUS', '643', '10');
INSERT INTO `countries` VALUES ('185', 'RW', 'RWANDA', 'Rwanda', 'RWA', '646', '10');
INSERT INTO `countries` VALUES ('186', 'SH', 'SAINT HELENA', 'Saint Helena', 'SHN', '654', '10');
INSERT INTO `countries` VALUES ('187', 'KN', 'SAINT KITTS AND NEVIS', 'Saint Kitts and Nevis', 'KNA', '659', '10');
INSERT INTO `countries` VALUES ('188', 'LC', 'SAINT LUCIA', 'Saint Lucia', 'LCA', '662', '10');
INSERT INTO `countries` VALUES ('189', 'PM', 'SAINT PIERRE AND MIQUELON', 'Saint Pierre and Miquelon', 'SPM', '666', '10');
INSERT INTO `countries` VALUES ('190', 'VC', 'SAINT VINCENT AND THE GRENADINES', 'Saint Vincent and the Grenadines', 'VCT', '670', '10');
INSERT INTO `countries` VALUES ('191', 'WS', 'SAMOA', 'Samoa', 'WSM', '882', '10');
INSERT INTO `countries` VALUES ('192', 'SM', 'SAN MARINO', 'San Marino', 'SMR', '674', '10');
INSERT INTO `countries` VALUES ('193', 'ST', 'SAO TOME AND PRINCIPE', 'Sao Tome and Principe', 'STP', '678', '10');
INSERT INTO `countries` VALUES ('194', 'SA', 'SAUDI ARABIA', 'Saudi Arabia', 'SAU', '682', '10');
INSERT INTO `countries` VALUES ('195', 'SN', 'SENEGAL', 'Senegal', 'SEN', '686', '10');
INSERT INTO `countries` VALUES ('196', 'CS', 'SERBIA AND MONTENEGRO', 'Serbia and Montenegro', null, null, '10');
INSERT INTO `countries` VALUES ('197', 'SC', 'SEYCHELLES', 'Seychelles', 'SYC', '690', '10');
INSERT INTO `countries` VALUES ('198', 'SL', 'SIERRA LEONE', 'Sierra Leone', 'SLE', '694', '10');
INSERT INTO `countries` VALUES ('199', 'SG', 'SINGAPORE', 'Singapore', 'SGP', '702', '10');
INSERT INTO `countries` VALUES ('200', 'SK', 'SLOVAKIA', 'Slovakia', 'SVK', '703', '10');
INSERT INTO `countries` VALUES ('201', 'SI', 'SLOVENIA', 'Slovenia', 'SVN', '705', '10');
INSERT INTO `countries` VALUES ('202', 'SB', 'SOLOMON ISLANDS', 'Solomon Islands', 'SLB', '90', '10');
INSERT INTO `countries` VALUES ('203', 'SO', 'SOMALIA', 'Somalia', 'SOM', '706', '10');
INSERT INTO `countries` VALUES ('204', 'ZA', 'SOUTH AFRICA', 'South Africa', 'ZAF', '710', '10');
INSERT INTO `countries` VALUES ('205', 'GS', 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS', 'South Georgia and the South Sandwich Islands', null, null, '10');
INSERT INTO `countries` VALUES ('206', 'ES', 'SPAIN', 'Spain', 'ESP', '724', '10');
INSERT INTO `countries` VALUES ('207', 'LK', 'SRI LANKA', 'Sri Lanka', 'LKA', '144', '10');
INSERT INTO `countries` VALUES ('208', 'SD', 'SUDAN', 'Sudan', 'SDN', '736', '10');
INSERT INTO `countries` VALUES ('209', 'SR', 'SURINAME', 'Suriname', 'SUR', '740', '10');
INSERT INTO `countries` VALUES ('210', 'SJ', 'SVALBARD AND JAN MAYEN', 'Svalbard and Jan Mayen', 'SJM', '744', '10');
INSERT INTO `countries` VALUES ('211', 'SZ', 'SWAZILAND', 'Swaziland', 'SWZ', '748', '10');
INSERT INTO `countries` VALUES ('212', 'SE', 'SWEDEN', 'Sweden', 'SWE', '752', '10');
INSERT INTO `countries` VALUES ('213', 'CH', 'SWITZERLAND', 'Switzerland', 'CHE', '756', '10');
INSERT INTO `countries` VALUES ('214', 'SY', 'SYRIAN ARAB REPUBLIC', 'Syrian Arab Republic', 'SYR', '760', '10');
INSERT INTO `countries` VALUES ('215', 'TW', 'TAIWAN, PROVINCE OF CHINA', 'Taiwan, Province of China', 'TWN', '158', '10');
INSERT INTO `countries` VALUES ('216', 'TJ', 'TAJIKISTAN', 'Tajikistan', 'TJK', '762', '10');
INSERT INTO `countries` VALUES ('217', 'TZ', 'TANZANIA, UNITED REPUBLIC OF', 'Tanzania, United Republic of', 'TZA', '834', '10');
INSERT INTO `countries` VALUES ('218', 'TH', 'THAILAND', 'Thailand', 'THA', '764', '10');
INSERT INTO `countries` VALUES ('219', 'TL', 'TIMOR-LESTE', 'Timor-Leste', null, null, '10');
INSERT INTO `countries` VALUES ('220', 'TG', 'TOGO', 'Togo', 'TGO', '768', '10');
INSERT INTO `countries` VALUES ('221', 'TK', 'TOKELAU', 'Tokelau', 'TKL', '772', '10');
INSERT INTO `countries` VALUES ('222', 'TO', 'TONGA', 'Tonga', 'TON', '776', '10');
INSERT INTO `countries` VALUES ('223', 'TT', 'TRINIDAD AND TOBAGO', 'Trinidad and Tobago', 'TTO', '780', '10');
INSERT INTO `countries` VALUES ('224', 'TN', 'TUNISIA', 'Tunisia', 'TUN', '788', '10');
INSERT INTO `countries` VALUES ('225', 'TR', 'TURKEY', 'Turkey', 'TUR', '792', '10');
INSERT INTO `countries` VALUES ('226', 'TM', 'TURKMENISTAN', 'Turkmenistan', 'TKM', '795', '10');
INSERT INTO `countries` VALUES ('227', 'TC', 'TURKS AND CAICOS ISLANDS', 'Turks and Caicos Islands', 'TCA', '796', '10');
INSERT INTO `countries` VALUES ('228', 'TV', 'TUVALU', 'Tuvalu', 'TUV', '798', '10');
INSERT INTO `countries` VALUES ('229', 'UG', 'UGANDA', 'Uganda', 'UGA', '800', '10');
INSERT INTO `countries` VALUES ('230', 'UA', 'UKRAINE', 'Ukraine', 'UKR', '804', '10');
INSERT INTO `countries` VALUES ('231', 'AE', 'UNITED ARAB EMIRATES', 'United Arab Emirates', 'ARE', '784', '10');
INSERT INTO `countries` VALUES ('232', 'GB', 'UNITED KINGDOM', 'United Kingdom', 'GBR', '826', '1');
INSERT INTO `countries` VALUES ('233', 'US', 'UNITED STATES', 'United States', 'USA', '840', '10');
INSERT INTO `countries` VALUES ('234', 'UM', 'UNITED STATES MINOR OUTLYING ISLANDS', 'United States Minor Outlying Islands', null, null, '10');
INSERT INTO `countries` VALUES ('235', 'UY', 'URUGUAY', 'Uruguay', 'URY', '858', '10');
INSERT INTO `countries` VALUES ('236', 'UZ', 'UZBEKISTAN', 'Uzbekistan', 'UZB', '860', '10');
INSERT INTO `countries` VALUES ('237', 'VU', 'VANUATU', 'Vanuatu', 'VUT', '548', '10');
INSERT INTO `countries` VALUES ('238', 'VE', 'VENEZUELA', 'Venezuela', 'VEN', '862', '10');
INSERT INTO `countries` VALUES ('239', 'VN', 'VIET NAM', 'Viet Nam', 'VNM', '704', '10');
INSERT INTO `countries` VALUES ('240', 'VG', 'VIRGIN ISLANDS, BRITISH', 'Virgin Islands, British', 'VGB', '92', '10');
INSERT INTO `countries` VALUES ('241', 'VI', 'VIRGIN ISLANDS, U.S.', 'Virgin Islands, U.s.', 'VIR', '850', '10');
INSERT INTO `countries` VALUES ('242', 'WF', 'WALLIS AND FUTUNA', 'Wallis and Futuna', 'WLF', '876', '10');
INSERT INTO `countries` VALUES ('243', 'EH', 'WESTERN SAHARA', 'Western Sahara', 'ESH', '732', '10');
INSERT INTO `countries` VALUES ('244', 'YE', 'YEMEN', 'Yemen', 'YEM', '887', '10');
INSERT INTO `countries` VALUES ('245', 'ZM', 'ZAMBIA', 'Zambia', 'ZMB', '894', '10');
INSERT INTO `countries` VALUES ('246', 'ZW', 'ZIMBABWE', 'Zimbabwe', 'ZWE', '716', '10');
INSERT INTO `currencies` VALUES ('1', 'GBP', 'GBP', '£', '&pound;', '1', '0');
INSERT INTO `giftwrap_product_names` VALUES ('1', '1', '1', 'Red');
INSERT INTO `giftwrap_product_names` VALUES ('2', '2', '1', 'Blue');
INSERT INTO `giftwrap_product_names` VALUES ('3', '3', '1', 'White');
INSERT INTO `giftwrap_product_names` VALUES ('4', '4', '1', 'Silver');
INSERT INTO `giftwrap_products` VALUES ('1', '1');
INSERT INTO `giftwrap_products` VALUES ('2', '1');
INSERT INTO `giftwrap_products` VALUES ('3', '1');
INSERT INTO `giftwrap_products` VALUES ('4', '1');
INSERT INTO `instant_payment_notifications` VALUES ('4ef3465c-8950-44f0-95e3-40af5412c393', '2.4', 'A--8MSCLabuvN8L.-MHjxC9uypBtAxL6Eaw7a8Pg8x13TYWWHkCTff5W', '1', null, null, null, null, null, null, null, null, 'John', 'Smith', null, 'buyer@paypalsandbox.com', 'TESTBUYERID01', 'verified', null, 'US', 'seller@paypalsandbox.com', null, null, null, 'seller@paypalsandbox.com', 'TESTSELLERID1', 'xyz123', 'abc1234', null, null, null, null, null, '2.02', null, null, null, null, null, null, '07:01:03 Dec 22, 2011 PST', 'Completed', 'instant', null, null, null, null, null, null, '31222151', 'cart', null, 'GBP', '0.44', '15.34', '2.06', '3.02', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '2011-12-22 15:01:48', '2011-12-22 15:01:48');
INSERT INTO `instant_payment_notifications` VALUES ('4ef3469a-5f3c-4cbe-aa78-41705412c393', '2.4', 'Axgjnq7fpfDWLy6JM.i82J.p73TcAz1Q-UG5DJGgetovMW.0T06T1Gyi', '1', null, null, null, null, null, null, null, null, 'John', 'Smith', null, 'buyer@paypalsandbox.com', 'TESTBUYERID01', 'verified', null, 'US', 'seller@paypalsandbox.com', null, null, null, 'seller@paypalsandbox.com', 'TESTSELLERID1', 'xyz123', 'abc1234', null, null, null, null, null, '2.02', null, null, null, null, null, null, '07:01:03 Dec 22, 2011 PST', 'Completed', 'instant', null, null, null, null, null, null, '31222151', 'cart', null, 'GBP', '0.44', '15.34', '2.06', '3.02', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '2011-12-22 15:02:50', '2011-12-22 15:02:50');
INSERT INTO `languages` VALUES ('1', 'English');
INSERT INTO `order_statuses` VALUES ('2', 'Failed');
INSERT INTO `order_statuses` VALUES ('1', 'Processing');
INSERT INTO `order_statuses` VALUES ('3', 'Held');
INSERT INTO `order_statuses` VALUES ('4', 'Complete');
INSERT INTO `shipping_carrier_service_countries` VALUES ('1', '1', '1');
INSERT INTO `shipping_carrier_service_countries_per_item_prices` VALUES ('4', '1', '1', '1', '0.00', '0.00');
INSERT INTO `shipping_carrier_service_subtotal_range_country_prices` VALUES ('1', '1', '1', '1', '0.00');
INSERT INTO `shipping_carrier_service_subtotal_ranges` VALUES ('1', '1', '1', '4');
INSERT INTO `shipping_carrier_services` VALUES ('1', '1', 'Standard', '', '0');
INSERT INTO `shipping_carriers` VALUES ('1', 'Royal Mail', '');
INSERT INTO `shipping_zone_countries` VALUES ('1', '1', '232');
INSERT INTO `shipping_zones` VALUES ('1', 'UK Mainland', '0');
INSERT INTO `tax_rates` VALUES ('4', 'Standard VAT', '232', '20.00');
