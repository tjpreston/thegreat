<?php

/**
 * Import Controller
 * 
 */
class ImportController extends AppController
{
	/**
	 * An array containing the class names of models this controller uses.
	 *
	 * @var array
	 * @access public
	 */
	public $uses = array('Product', 'Category', 'CustomOption', 'ProductOptionStock');
	
	/**
	 * Array containing the names of components this controller uses.
	 *
	 * @var array
	 * @access public
	 */
	public $components = array('Image');	
	
	/**
	 * Array containing the codes of sub products already inserted
	 * and the last sort order inserted.
	 *
	 * @var array
	 * @access private
	 */
	private $_optionSorts = array();
	
	/**
	 * Current iteration of product from feed.
	 *
	 * @var array
	 * @access public
	 */
	private $_product;
	
	private $_languageID;
	
	private $_currencyID;
	
	private $_productID;
	
	
	public function admin_correct_prices()
	{
		exit;
		
		$this->Product->bindPrice($this->Product, null, false);
		
		$path = WWW_ROOT . 'data/complete.csv';
		if (($handle = fopen($path, "r")) === FALSE)
		{
			exit;
		}
		
		$productsFixed = array();
		
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
		{
			$sort = $data[0];  // A
			$fullSKU = $data[1]; // B
			$mainSKU = $data[2]; // C
			$optionSKU = $data[3]; // D
			
			$ourPrice = $data[17]; // R
			
			if (empty($optionSKU))
			{
				continue;
			}
			
			$productID = $this->Product->field('id', array(
				'Product.sku' => $mainSKU
			));
			
			if (in_array($productID, $productsFixed))
			{
				continue;
			}
			
			$this->Product->ProductPrice->updateAll(
				array('ProductPrice.base_price' => strval($ourPrice), 'ProductPrice.active_price' => strval($ourPrice)),
				array('ProductPrice.product_id' => $productID)			
			);
			
			$productsFixed[] = $productID;
			
		}
		
		echo 'a';
		
		exit;
		
	}
	
	

	public function admin_set_lowest_highest_prices()
	{
		exit;
		
		$this->Product->bindPrice($this->Product, null, false);
		$this->Product->bindOptions($this->Product);
		
		$this->Product->ProductPrice->updateLowestHightestPrices();
		
		exit;
		
	}
	
	/**
	 * Admin.
	 * Import a product feed from 1on1dropship.
	 * 
	 * @access public
	 * @return void
	 */
	public function admin_import_1on1dropship_products()
	{
		$topCatMap = array(
			'Toys For Her'  => 'For Her',
			'Toys For Him'  => 'For Him',
			'Anal Toys'	    => 'Anal',
			'Sexy Lingerie' => 'Clothing',
			'Special Occasions' => 'Occasions',
			'Clothing'      => 'Dress Up',
			'Essentials'    => 'Sex Aids',
			'Fun and Games' => 'Sex Aids',
			'Enhancers'     => 'Sex Aids',
			'Batteries'     => 'Sex Aids',
			// 'Weekly Offers' => '',
			'Valentine`s Day' => 'Occasions',
			'Christmas'     => 'Occasions'
		);
		
		$subCatMap = array(
			'Butt Plugs' => 'Anal',
			'Penis Pumps' => 'For Him',
			'Anal Dildos' => 'Anal',
			'Party Plan' => 'Occasions',
			'Clit Teasers' => 'For Her',
			'Anal Sundries' => 'Anal',
			'Finger Fun' => 'For Her',
			'Sleeves & Rings' => 'For Him',
			'Eggs & Rings' => 'For Her',
			'Anal Beads' => 'Anal',
			'Nipple Play' => 'Occasions',
			'Valentine\'s Day' => 'Anal',
			'Easter' => 'Occasions',
			'Masturbators' => 'For Him',
			'Glass Plugs' => 'Anal',
			'Realistic Vaginas' => 'For Him',
			'Christmas' => 'Occasions',
			'Clothing For Him' => 'Clothing',
			'Bras and Knickers' => 'Clothing',
			'World Cup' => 'Occasions',
			'Fetish Fun' => 'Clothing',
			'Clothing For Her' => 'Clothing',			
			'Lesbian' => 'Groups',
			'Costumes' => 'Clothing',
			'Easter' => 'Occasions',
			'Lingerie Sets' => 'Clothing',
			'Corsets and Basques' => 'Clothing',
			'Bridal' => 'Clothing',
			'Babydolls' => 'Clothing',
			'Nightwear' => 'Clothing',
			'Lelo' => 'Designer',
			'Tenga' => 'Designer',
			'Pink' => 'Designer',
			'Sin Five' => 'Designer',
			'Lubricants' => 'Sex Aids',
			'Gifts' => 'Sex Aids',
			'Aphrodisiacs' => 'Sex Aids',
			'Games' => 'Sex Aids',
			'Creams and Sprays' => 'Sex Aids',
			'Batteries' => 'Sex Aids',
			'Sundries' => 'Sex Aids',
			'Massage' => 'Sex Aids',
			'Accessories' => 'Clothing',
			'Valentine`s For Her' => 'Occasions',
			'Condoms' => 'Sex Aids',
			'Christmas Stocking Fillers' => 'Occasions',
			'Aromas' => 'Sex Aids',
			'Valentine`s For Him' => 'Occasions',
			'Valentine`s For Couples' => 'Occasions',
			'Christmas For Couples' => 'Occasions',
			'Valentine`s Romance' => 'Occasions',
			'Christmas For Her' => 'Occasions',
			'Eat Me' => 'Sex Aids',
			'Christmas For Him' => 'Occasions',
			'Sexual Health' => 'Sex Aids',
			'Hen and Stag' => 'Sex Aids'
		);
		
		$options = $this->_getOptions();
		
		$this->_languageID = 1;
		$this->_currencyID = 1;
		
		$this->Product->bindPrice(null, false);
		$this->Product->bindName($this->Product, null, false);
		$this->Product->bindDescription($this->Product, null, false);
		$this->Product->bindMeta($this->Product, null, false);
		$this->Product->bindCrossSells($this->Product, null, false);
		
		$this->Product->bindOptions($this->Product, false);
		$this->Product->ProductOption->bindName($this->Product->ProductOption, null, false);
		// $this->Product->ProductOption->ProductOptionValue->bindPrice($this->Product->ProductOption->ProductOptionValue, null, false);
		
		// Get all cats
		$categories = $this->Category->getThreadedCategoriesList();
		
		// Get all stock statuses
		$stockStatuses = $this->Product->StockStatus->find('list', array('fields' => array(
			'name', 'id'
		)));
		
		// Get all manufacturers
		$manufacturers = $this->Product->Manufacturer->find('list', array('fields' => array(
			'name', 'id'
		)));
		
		App::import('Vendor', 'CI_Image_lib', array('file' => 'Image_lib.php'));
		
		$path = WWW_ROOT . 'data/complete.csv';
		if (($handle = fopen($path, "r")) === FALSE)
		{
			exit;
		}
		
		$i = 0;
		
		$crossells = array();
		
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
		{
			if ($i == 0)
			{
				//$i++;
				//continue;
			}
			
			$i++;
			
			if ($i > 50)
			{
				break;
			}
			
			/*
			$sort = $data[0];  // A
			$fullSKU = $data[1]; // B
			$mainSKU = $data[2]; // C
			$optionSKU = $data[3]; // D
			$fullName = htmlentities($data[4]); // E
			$customOptionID = $data[5]; // F
			$description = htmlentities($data[6]); // G
			$materials = $data[7]; // H
			$sizeImperial = $data[8]; // I
			$sizeMetric = $data[9]; // J
			$power = $data[10]; // K
			
			$crossellProducts = $data[11]; // L
			
			$tradePrice = $data[12]; // M
			$tradePricePlusTen = $data[13]; // N
			$margin = $data[14]; // O
			$rrp = $data[15]; // P
			$borkedPrice = $data[16]; // Q
			$ourPrice = $data[17]; // R
			$borkedPriceTwo = $data[18]; // S
			
			$subCategory = $data[19]; // T
			$topCategory = $data[20]; // U
			
			$imageName = $data[21]; // V
			$thumbURL = $data[22]; // W
			$viewURL = $data[23]; // X
			$largeURL = $data[24]; // Y
			
			$stock = $data[25]; // Z
			$stockLevel = $data[26]; // AA
			
			$mpn = $data[27]; // AB
			$manufacturer = $data[28]; // AC
			*/

			$fullSKU = $data[0];					// A
			$mainSKU = $data[1];					// B
			$optionSKU = $data[2];					// C
			$fullName = htmlentities($data[3]);		// D
			$description = htmlentities($data[4]);	// E
			$materials = $data[5];					// F
			$sizeImperial = $data[6];				// G
			$sizeMetric = $data[7];					// H
			$power = $data[8];						// I
			
			$tradePrice = $data[9];					// J
			$rrp = $data[10];						// K
			
			$subCategory = $data[11];				// L
			$topCategory = $data[12];				// M
			
			$imageName = $data[13];					// N
			$thumbURL = $data[14];					// O
			$viewURL = $data[15];					// P
			$largeURL = $data[16];					// Q
			
			$stock = $data[17];						// R
			$stockLevel = $data[18];				// S
			
			$mpn = $data[19];						// T
			$manufacturer = $data[20];				// U
			
			$stockID = $stockStatuses[$stock];
			$stockLevel = intval($stockLevel);
			
			if (array_key_exists($topCategory, $topCatMap))
			{
				$topCategory = $topCatMap[$topCategory];
			}
			
			if (!empty($optionSKU))
			{
				$productName = substr($fullName, 0, strrpos($fullName, '-'));
				$productSku  = strval($mainSKU);
				$optionName  = ucwords(substr(strrchr($fullName, '-'), 2));
				
				$existingProduct = $this->Product->find('first', array('conditions' => array('Product.sku' => $mainSKU)));
				
				if (!empty($existingProduct))
				{
					$this->_productID = $existingProduct['Product']['id'];
					$productOptionID = $this->_insertOption($customOptionID);
					$this->_insertOptionValue($productOptionID, $options, $customOptionID, $optionName, $optionSKU, $ourPrice, $stockID, $stockLevel);
					continue;
				}
				
			}
			else
			{
				$productName = strval($fullName);
				$productSku  = strval($mainSKU);
				$optionName  = null;
			}

			
			// Init product data
			$productData = array('Product' => array(
				'sku' => $productSku,
				'stock_status_id' => $stockID,
				'stock_base_qty' => $stockLevel,
				'active' => 1,
				'materials' => $materials,
				'size_imperial' => $sizeImperial,
				'size_metric' => $sizeMetric,
				'power' => $power,
				'taxable' => 1,
			));
			
			if (!empty($manufacturers[$manufacturer]))
			{
				$productData['Product']['manufacturer_id'] = $manufacturers[$manufacturer];
			}
			
			if (array_key_exists($topCategory, $categories))
			{
				$topCatID = $categories[$topCategory]['id'];
				
				if (array_key_exists($subCategory, $categories[$topCategory]))
				{
					$subCatID = $categories[$topCategory][$subCategory]['id'];
				}
				else
				{
					CakeLog::write('1to1', $productSku . ": No sub cat. - " . $subCategory . " Aborting line.");
					continue;
				}
			}
			else
			{
				CakeLog::write('1to1', $productSku . ": No top cat - " . $topCategory . ". Aborting line.");
				continue;
			}
			
			$productData['Product']['main_category_id'] = $subCatID;
			
			$this->Product->create();
			if ($this->Product->save($productData))
			{
				CakeLog::write('1to1', $productSku . ": Product record insert OK.");
			}
			else
			{
				CakeLog::write('1to1', $productSku . ": FAIL: Product record insert failed. Aborting line.");
				continue;
			}
			
			$this->_productID = $this->Product->getInsertID();
			
			// Crossells			
			$temp = array();
			if (!empty($crossellProducts))
			{
				$temp = explode(',', $crossellProducts);
				$crossells[$this->_productID] = $temp;
				
			}
			
			$this->Product->ProductCategory->create();
			$this->Product->ProductCategory->save(array('ProductCategory' => array(
				'product_id' => $this->_productID,
				'category_id' => $topCatID
			)));
			
			$this->Product->ProductCategory->create();
			$this->Product->ProductCategory->save(array('ProductCategory' => array(
				'product_id' => $this->_productID,
				'category_id' => $subCatID
			)));
			
			
			// Add product name record
			$this->Product->ProductName->create();
			$productNameInsert = $this->Product->ProductName->save(array('ProductName' => array(
				'product_id' => $this->_productID,
				'language_id' => $this->_languageID,
				'name' => $productName
			)));
			
			if ($productNameInsert)
			{
				CakeLog::write('1to1', $productSku . ": Product name insert OK.");
			}
			else
			{
				CakeLog::write('1to1', $productSku . ": FAIL: Product name insert failed. Aborting line.");
				echo $productSku . ": Product name insert failed. Aborting line." . '<br>';
				continue;
			}
			
			// Add product description record
			$this->Product->ProductDescription->create();
			$productDescriptionInsert = $this->Product->ProductDescription->save(array('ProductDescription' => array(
				'product_id' => $this->_productID,
				'language_id' => $this->_languageID,
				'short_description' => $description,
				'long_description' => $description
			)));
			
			if ($productDescriptionInsert)
			{
				CakeLog::write('1to1', $productSku . ": Product desc insert OK.");
			}
			else
			{
				CakeLog::write('1to1', $productSku . ": FAIL: Product description insert failed. Aborting line.");				
				echo $productSku . ": Product description insert failed. Aborting line." . '<br>';
				continue;
			}
			
			// Add product meta record
			$this->Product->ProductMeta->create();
			$productMetaInsert = $this->Product->ProductMeta->save(array('ProductMeta' => array(
				'product_id' => $this->_productID,
				'language_id' => $this->_languageID,
				'url' => $productName
			)));
			
			if ($productMetaInsert)
			{
				CakeLog::write('1to1', $productSku . ": Product meta insert OK.");
			}
			else
			{
				CakeLog::write('1to1', $productSku . ": FAIL: Product meta insert failed. (" . $productName . ") Aborting line.");
				echo $productSku . ": Product meta insert failed. Aborting line." . '<br>';
				continue;
			}
			
			// ------------------------
			
			$price = (!empty($optionSKU)) ? $rrp : $ourPrice;
			
			// Add GBP product price record
			$this->Product->ProductPrice->create();
			$productPriceInsert = $this->Product->ProductPrice->save(array('ProductPrice' => array(
				'product_id' => $this->_productID,
				'currency_id' => $this->_currencyID,
				'base_rrp' => strval($rrp),
				'base_price' => strval($ourPrice),
				'wholesale' => strval($tradePricePlusTen)
			)));
			
			if ($productPriceInsert)
			{
				CakeLog::write('1to1', $productSku . ": Product price insert OK.");
			}
			else
			{
				CakeLog::write('1to1', $mainSKU . ": FAIL: GBP price insert failed. Aborting line.");				
				echo $mainSKU . ": GBP price insert failed. Aborting line." . '<br>';
				continue;
			}
			
			/*
			$usd = 1.5652;
			$usdPrice = $rrp * $usd;
			
			// Add USD product price record
			$this->Product->ProductPrice->create();
			$productPriceInsert = $this->Product->ProductPrice->save(array('ProductPrice' => array(
				'product_id' => $this->_productID,
				'currency_id' => 2,
				'base_rrp' => $usdPrice,
				'base_price' => $usdPrice
			)));
			
			if (!$productPriceInsert)
			{
				CakeLog::write('1to1', $mainSKU . ": USD price insert failed. Aborting line.");				
				echo $mainSKU . ": USD price insert failed. Aborting line." . '<br>';
				continue;
			}
			
			$euro = 1.18692652;
			$euroPrice = $rrp * $euro;
			
			// Add EUROS product price record
			$this->Product->ProductPrice->create();
			$productPriceInsert = $this->Product->ProductPrice->save(array('ProductPrice' => array(
				'product_id' => $this->_productID,
				'currency_id' => 3,
				'base_rrp' => $euroPrice,
				'base_price' => $euroPrice
			)));
			
			if (!$productPriceInsert)
			{
				CakeLog::write('1to1', $mainSKU . ": EURO price insert failed. Aborting line.");				
				echo $mainSKU . ": EURO price insert failed. Aborting line." . '<br>';
				continue;
			}
			*/
			
			// ------------------------
			
			
			if (!empty($optionSKU))
			{
				$productOptionID = $this->_insertOption($customOptionID);
				$this->_insertOptionValue($productOptionID, $options, $customOptionID, $optionName, $optionSKU, $ourPrice, $stockID, $stockLevel);
			}
			
			
		
			// Add product image record
			$this->Product->ProductImage->create();
			$productImageInsert = $this->Product->ProductImage->save(array('ProductImage' => array(
				'product_id' => $this->_productID,
				'ext' => 'jpg',
				'filename' => $imageName
			)));				
		
			
			
			
		}
		
		$products = $this->Product->find('list', array('fields' => array('Product.sku', 'Product.id')));
		
		foreach ($crossells as $prodID => $crossSkus)
		{
			$sort = 0;
			foreach ($crossSkus as $sku)
			{
				$crossProdID = $this->Product->field('id', array('Product.sku' => $sku));				
				if (!empty($crossProdID))
				{
					$this->Product->ProductCrossSell->create();
					$this->Product->ProductCrossSell->save(array('ProductCrossSell' => array(
						'from_product_id' => $prodID,
						'to_product_id' => $crossProdID,
						'sort' => $sort
					)));
					$sort++;
				}
			}
		}
		
	
	}
	
	
	
	private function _insertOption($customOptionID)
	{
		$exists = $this->Product->ProductOption->find('first', array('conditions' => array(
			'ProductOption.product_id' => $this->_productID,
			'ProductOption.custom_option_id' => $customOptionID
		)));
		
		if ($exists)
		{
			return $exists['ProductOption']['id'];
		}
				
		$this->Product->ProductOption->create();
		$result = $this->Product->ProductOption->save(array('ProductOption' => array(
			'product_id' => $this->_productID,
			'custom_option_id' => $customOptionID,
			'sort' => 1
		)));
		
		if (!$result)
		{
			CakeLog::write('1to1', $this->_productID . ": FAIL: Product option insert failed (" . $customOptionID . "). Aborting line.");
			return;
		}
		
		
		$id = $this->Product->ProductOption->getInsertID();
		
		$this->Product->ProductOption->ProductOptionName->create();
		$result = $this->Product->ProductOption->ProductOptionName->save(array('ProductOptionName' => array(
			'product_option_id' => $id,
			'language_id' => 1,
			'name' => 'name'
		)));
		
		if (!$result)
		{
			CakeLog::write('1to1', $this->_productID . ": FAIL: Product option name insert failed (" . $id . "). Aborting line.");
			return;
		}
		
		CakeLog::write('1to1', $this->_productID . ' ' . $customOptionID . ": Product option insert OK.");
		
		return $id;
		
	}
	
	
	private function _insertOptionValue($productOptionID, $options, $customOptionID, $optionName, $optionSKU, $ourPrice, $stockID, $stockLevel)
	{	
		if (empty($options[$customOptionID][strtolower($optionName)]))
		{
			CakeLog::write('1to1', ": FAIL" . 'missing: ' . $optionSKU . ' - ' . strtolower($optionName));	
			return;
		}
	
		$customOptionValueID = $options[$customOptionID][strtolower($optionName)];
		
		
		$this->Product->ProductOption->ProductOptionValue->create();
		$result = $this->Product->ProductOption->ProductOptionValue->save(array('ProductOptionValue' => array(
			'product_option_id' => $productOptionID,
			'custom_option_value_id' => $customOptionValueID,
			'sku' => $optionSKU,
			'sort' => 1,
			'modifier' => 'fixed'
		)));
		
		if (!$result)
		{
			CakeLog::write('1to1', $this->_productID . ": FAIL: Product option value insert failed (" . $productOptionID . ", " . $customOptionValueID . ", " . $optionSKU . "). Aborting line.");
			return;
		}
		
		$valueID = $this->Product->ProductOption->ProductOptionValue->getInsertID();
		
				
		$this->Product->ProductOption->ProductOptionValue->ProductOptionValuePrice->create();
		$result = $this->Product->ProductOption->ProductOptionValue->ProductOptionValuePrice->save(array('ProductOptionValuePrice' => array(
			'product_option_value_id' => $valueID,
			'currency_id' => 1,
			'modifier_value' => $ourPrice
		)));
		
		if (!$result)
		{
			CakeLog::write('1to1', $this->_productID . ": FAIL: Product option value price insert failed (" . $productOptionID . ", " . $valueID . ", " . $rrp . "). Aborting line.");
			return;
		}
		
		
		$this->ProductOptionStock->create();
		$result = $this->ProductOptionStock->save(array('ProductOptionStock' => array(
			'product_id' => $this->_productID,
			'value_ids' => $valueID,
			'stock_status_id' => $stockID,
			'stock_lead_time' => '',
			'stock_base_qty' => $stockLevel
		)));
		
		if (!$result)
		{
			CakeLog::write('1to1', $this->_productID . ": FAIL: Product option value stock insert failed (" . $valueID . ", " . $stockID . ", " . $stockLevel . "). Aborting line.");
			return;
		}
		
		
		CakeLog::write('1to1', $this->_productID . ' ' . $optionSKU . ": Product option value insert OK.");
		
				
		
	}
	
	
	
	private function _getOptions()
	{
		$out = array();
		
		$this->CustomOption->CustomOptionValue->bindName($this->CustomOption->CustomOptionValue, 1, false);
		
		$this->CustomOption->Behaviors->attach('Containable');
		$options = $this->CustomOption->find('all', array(
			'contain' => array('CustomOptionValue' => array('CustomOptionValueName'))
		));
		
		foreach ($options as $k => $option)
		{
			$cID = $option['CustomOption']['id'];
			$out[$cID] = array();
			
			foreach ($option['CustomOptionValue'] as $k2 => $value)
			{
				$vID = $value['id'];
				$vName = strtolower($value['CustomOptionValueName']['name']);
				$out[$cID][$vName] = $vID;
			}
			
		}
		
		return $out;
		
	}

	
	
}
