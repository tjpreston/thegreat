<?php

/**
 * Product Model
 * 
 */
class Product extends AppModel
{
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array('Manufacturer');
	
	/**
	 * Detailed list of hasMany associations.
	 *
	 * @var array
	 * @access public
	 */
	public $hasMany = array(
		'ProductCategory',
		'ProductImage' => array('exclusive' => true, 'order' => 'ProductImage.sort_order ASC')
	);
	
	/**
	 * List of validation rules. 
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(
		'sku' => array(
			'validSku' => array(
				'rule' =>  array('minLength', 1),
				'required' => true,
				'allowEmpty' => false,
				'message' => 'SKU missing'
			),
			'uniqueProductSku' => array(
				'rule' =>  'isUnique',
				'message' => 'SKU in use'
			),
			'uniqueOptionSku' => array(
				'rule' =>  'isUniqueOptionSku',
				'message' => 'SKU in use'
			)
		),
		'visibility' => array(
			'rule' =>  array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Visibility missing'			
		)
	);
	
	/**
	 * Validation method - check product SKU is not used by any product option.
	 *
	 * @var array $check
	 * @return bool
	 * @access public
	 */
	public function isUniqueOptionSku($check)
	{
		$unqiueOptionSku = ClassRegistry::init('ProductOptionStock')->find('count', array(
			'conditions' => array('ProductOptionStock.sku' => $check['sku'])
		));
		
		return empty($unqiueOptionSku);
		
	}
	
	/**
	 * Add product name (non-existent field) validation rule (for adding products)
	 * 
	 * @return void
	 * @access public
	 */
	public function addProductNameValidation()
	{
		$this->validate['name'] = array(
			'rule' =>  'notEmpty',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter the product name'
		);
	}
	
	/**
	 * Remove product name (non-existent field) validation rule
	 * 
	 * @return void
	 * @access public
	 */
	public function removeProductNameValidation()
	{
		unset($this->validate['name']);
	}
	
	/**
	 * Called before each find operation. Return false if you want to halt the find
	 * call, otherwise return the (modified) query data.
	 *
	 * @param array $queryData Data used to execute this query, i.e. conditions, order, etc.
	 * @return mixed true if the operation should continue, false if it should abort; or, modified $queryData to continue with new $queryData
	 * @access public
	 */
	public function beforeFind($queryData) 
	{
		if (Configure::read('Runtime.mode') == 'front')
		{
			$queryData['conditions']['Product.active'] = 1;
		}
		
		return $queryData;
	}
	
	/**
	 * Called after each find operation. Can be used to modify any results returned by find().
	 * Return value should be the (modified) results.
	 *
	 * @param mixed $results The results of the find operation
	 * @param boolean $primary Whether this model is being queried directly (vs. being queried as an association)
	 * @return mixed Result of the find operation
	 * @access public
	 */
	public function afterFind($results, $primary = false) 
	{
            //xdebug_break();
                if (empty($results) || !isset($results[0]['Product']))
		{
			return $results;
		}
		
		foreach ($results as $k => $result)
		{
			$results[$k]['Product']['main_tiny_image_path'] = Configure::read('Images.placeholder_tiny_path');
			$results[$k]['Product']['main_thumb_image_path'] = Configure::read('Images.placeholder_thumb_path');
			$results[$k]['Product']['main_medium_image_path'] = Configure::read('Images.placeholder_medium_path');
			
			if (!empty($result['ProductImage'][0]))
			{
				if (!empty($result['ProductImage'][0]['tiny_web_path']))
				{
					$results[$k]['Product']['main_tiny_image_path'] = $result['ProductImage'][0]['tiny_web_path'];
				}
				
				if (!empty($result['ProductImage'][0]['thumb_web_path']))
				{
					$results[$k]['Product']['main_thumb_image_path'] = $result['ProductImage'][0]['thumb_web_path'];
				}
				
				if (!empty($result['ProductImage'][0]['medium_web_path']))
				{
					$results[$k]['Product']['main_medium_image_path'] = $result['ProductImage'][0]['medium_web_path'];
				}
			}
			
			// Shipping Services
			if (isset($result['ProductShippingCarrierService']))
			{
				$availableServices = array();
				
				foreach ($result['ProductShippingCarrierService'] as $service)
				{
					$serviceID = $service['shipping_carrier_service_id'];
					$availableServices[$serviceID] = $service['available'];
				}
				
				$results[$k]['ProductShippingCarrierService'] = $availableServices;
				
			}

			// Price Discounts
			
			/*if (!empty($result['ProductPrice']['active_price']))
			{
				$results[$k]['ProductPrice']['trade_price'] = $result['ProductPrice']['active_price'];
				
				// Discounted Price (single qty)
				if (!empty($result['SingleQtyProductPriceDiscount']['discount_amount']) && !empty($result['ProductPrice']['active_price']))
				{
					$activePrice = $result['ProductPrice']['active_price'];
					$discountAmount = str_pad($result['SingleQtyProductPriceDiscount']['discount_amount'], 2, '0', STR_PAD_LEFT);
					$tradePrice = $activePrice - ($activePrice * floatval('0.' . $discountAmount));
					$results[$k]['ProductPrice']['trade_price'] = $tradePrice;
				}
				
				// Discounted Price (all)
				if (!empty($result['ProductPriceDiscount'][0]) && ($result['ProductPriceDiscount'][0]['min_qty'] == 1))
				{
					$activePrice = $result['ProductPrice']['active_price'];
					$discountAmount = str_pad($result['ProductPriceDiscount'][0]['discount_amount'], 2, '0', STR_PAD_LEFT);
					$tradePrice = $activePrice - ($activePrice * floatval('0.' . $discountAmount));
					$results[$k]['ProductPrice']['trade_price'] = $tradePrice;
				}
				
			}*/

			if(Configure::read('Runtime.trade_pricing')){
				if (!empty($result['ProductPrice']['trade_price'])){
					$results[$k]['ProductPrice']['active_price'] = $result['ProductPrice']['trade_price'];
					$results[$k]['ProductPrice']['on_special'] = 0;
				}
			}
			
			
			/*if (!empty($result['ProductPrice']['active_price']) && Configure::read('Customer.id'))
			{
				$results[$k]['ProductPrice']['original_price'] = $result['ProductPrice']['active_price'];
				
				$discountAmount = Configure::read('Customers.discount_percentage');
				$discount = ($discountAmount / 100) * $result['ProductPrice']['active_price'];

				$results[$k]['ProductPrice']['active_price'] = $result['ProductPrice']['active_price'] - $discount;
				$results[$k]['ProductPrice']['member_saving'] = $discount;

				if (!empty($result['ProductPrice']['lowest_price']))
				{
					$discount = ($discountAmount / 100) * $result['ProductPrice']['lowest_price'];
					$results[$k]['ProductPrice']['lowest_price'] = $result['ProductPrice']['lowest_price'] - $discount;
				}

				if (!empty($result['ProductPrice']['highest_price']))
				{
					$discount = ($discountAmount / 100) * $result['ProductPrice']['highest_price'];
					$results[$k]['ProductPrice']['highest_price'] = $result['ProductPrice']['highest_price'] - $discount;
				}


			}*/



			
		}

		return $results;
		
	}
	
	/**
	 * Called before each save operation, after validation. Return a non-true result
	 * to halt the save.
	 *
	 * @return boolean True if the operation should continue, false if it should abort
	 * @access public
	 */
	public function beforeSave()
	{
		if (!empty($this->data['Category']['Category']))
		{
			foreach ($this->data['Category']['Category'] as $k => $categoryID)
			{
				if (empty($categoryID))
				{
					unset($this->data['Category']['Category'][$k]);
				}
			}
		}
		
		// Set defaults
		if (empty($this->data['Product']['weight']))
		{
			$this->data['Product']['weight'] = 0;
		}
		if (empty($this->data['Product']['stock_base_qty']))
		{
			$this->data['Product']['stock_base_qty'] = 0;
		}
		
		return true;
		
	}
		
	/**
	 * Called after each successful save operation.
	 *
	 * @param boolean $created True if this save created a new record
	 * @access public
	 */
	public function afterSave()
	{
		$this->saveProductCategorySortOrders();
		
		foreach ($this->getLinkedModels() as $model)
		{
			$this->saveProductLinks($model);
		}
		
		if (!empty($this->data['ProductOption']))
		{
			$this->saveOptions($this->data);
		}
		
		if (Configure::read('Shipping.products_to_shipping_services') && !empty($this->data['ShippingCarrierDeliveryService']))
		{
			$this->ProductShippingCarrierService->saveAvailability($this->id, $this->data['ShippingCarrierDeliveryService']);
		}
		
		if (isset($this->data['Product']['type']) && ($this->data['Product']['type'] == 'grouped'))
		{
			// Update self
			$this->updateGroupedProductStock($this->id);
		}
		else
		{
			// Update any groups (price and stock) that saved products belongs to
			foreach ($this->inGroups($this->id) as $groupedProductID)
			{
				$this->updateGroupedProductStock($groupedProductID);
			}
		}

		// Assign default product option if not selected
		if (!empty($this->data['ProductOption'][0]) && empty($this->data['Product']['default_product_option_stock_id']))
		{
			$this->data['Product']['default_product_option_stock_id'] = $this->data['ProductOption'][0]['id'];
		}

		$data = $this->data;

		if (empty($data['Product']['attribute_set_id']) && !empty($data['UseAttributes']))
		{
			$data['Product']['attribute_set_id'] = $this->assignAttributeSet($data['Attribute']);
		}
		
		// Save attribute values
		if (!empty($data['Product']['attribute_set_id']) && !empty($data['Attribute']))
		{
			$this->AttributeValuesProduct->saveValues($this->id, $data['Attribute']);
		}
		
		$this->bindOptionStock(false);

		// Update stock
		$this->setInStock();
		
		// Save skus
		$this->saveAllSkus();
		
	}

	private function assignAttributeSet($attributes)
	{
		$ids = array();
		
		foreach ($attributes as $attrID => $attr)
		{
			if (!empty($attr['AttributeValue']))
			{
				$ids[] = $attrID;
			}
		}

		$idsConcat = implode(',', $ids);

		$set = $this->AttributeSet->find('first', array('conditions' => array('AttributeSet.attribute_ids_concat' => $idsConcat)));

		if (!empty($set))
		{
			$this->saveField('attribute_set_id', $set['AttributeSet']['id']);
			return $set['AttributeSet']['id'];
		}

		$this->AttributeSet->Attribute->bindName(1);
		$names = $this->AttributeSet->Attribute->AttributeName->find('list', array(
			'fields' => array('id', 'name'),
			'conditions' => array('language_id' => 1)
		));

		$setName = '';

		foreach ($ids as $id)
		{
			$setName .= $names[$id] . ', ';
		}

		$data = array(
			'AttributeSet' => array(
				'id' => '',
				'attribute_ids_concat' => $idsConcat
			),
			'AttributeSetName' => array(1 => array(
				'language_id' => 1,
				'name' => substr($setName, 0, -2)
			)),
			'Attribute' => array('Attribute' => $ids)
		);

		if ($this->AttributeSet->saveAll($data))
		{
			$this->saveField('attribute_set_id', $this->AttributeSet->getInsertID());
			return $this->AttributeSet->getInsertID();
		}
		
	}
		
	private function setInStock()
	{		
		$record = $this->findById($this->id);
		
		if (!empty($record['ProductOptionStock']))
		{
			$inStock = 0;
			foreach ($record['ProductOptionStock'] as $k => $option)
			{
				if (!empty($option['stock_in_stock']))
				{
					$inStock = 1;
					break;
				}
			}
		}
		else
		{
			$inStock = ($record['Product']['stock_base_qty'] > 0) ? 1 : 0;
		}
		
		$this->save(
			array('Product' => array('stock_in_stock' => $inStock)),
			array('validate' => false, 'callbacks' => false)
		);
		
	}

	private function saveAllSkus()
	{
		$record = $this->findById($this->id);
		
		$skus = $record['Product']['sku'] . ' ';
		
		if (!empty($record['ProductOptionStock']))
		{
			foreach ($record['ProductOptionStock'] as $k => $option)
			{
				$skus .= $option['sku'] . ' ';
			}
		}
		
		$skus = substr($skus, 0, -1);
		
		$this->save(
			array('Product' => array('all_skus' => $skus)),
			array('validate' => false, 'callbacks' => false)
		);	
		
	}
	
	/**
	 * Save product options.
	 *   Called manually after main saveAll as calling in afterSave()
	 *   was causing a rollback and preventing save of Product model
	 *  
	 * @param array $data
	 * @return void
	 * @access public 
	 */
	public function saveOptions($data)
	{
		if (empty($data['ProductOption']))
		{
			return;
		}
		
		$this->bindOptions($this, false);
		$this->ProductOption->bindName($this->ProductOption, null, false);
		
		if (!empty($data['ProductOption']['new']['custom_option_id']))
		{
			$this->ProductOption->saveNewOption($data['ProductOption']['new']);
		}
		else
		{
			// Save option stock records
			ClassRegistry::init('ProductOptionStock')->saveStock($this->data);
		}
		
		if (!empty($data['ProductOption']['hash']))
		{
			$this->ProductOption->saveProductOptionValueSortOrders($data['ProductOption']['hash']);
		}
		
		if (isset($data['ProductOption']['new']))
		{
			unset($data['ProductOption']['new']);
		}
		
		if (isset($data['ProductOption']['hash']))
		{
			unset($data['ProductOption']['hash']);
		}
		
		$this->ProductOption->saveAll($data['ProductOption']);
		
		if (!empty($data['NewProductOptionValues']))
		{
			$this->ProductOption->ProductOptionValue->saveNewValues($this->id, $data['NewProductOptionValues']);
			// ClassRegistry::init('ProductOptionStock')->deleteAll(array('ProductOptionStock.product_id' => $this->id));
			// ClassRegistry::init('ProductOptionStock')->initOptionStock($this->id);
		}
		
		// Update product price record with lowest and highest possible prices based on product options
		$this->ProductPrice->updateLowestHightestPrices($this->id);
		
	}
	
	/**
	 * Overridden method to allow group by in pagination count.
	 * http://cakephp.lighthouseapp.com/projects/42648/tickets/573-paginate-x-virtualfield-x-count-x-groupby#ticket-573-6
	 * 
	 * @param array $conditions [optional]
	 * @param int $recursive [optional]
	 * @param array $extra [optional]
	 * @return int
	 * @access public
	 */
	public function paginateCount($conditions = null, $recursive = 0, $extra = array())
	{
	    
            $parameters = compact('conditions', 'recursive');
	    $count = $this->find('count', array_merge($parameters, $extra));
	    if (isset($extra['group']))
		{
	        $count = $this->getAffectedRows();
	    }
	    return $count;
	}
	
	/**
	 * Get complete product record for displaying product on view page.
	 *
	 * @param int $id
	 * @return void
	 * @access public
	 */
	public function getViewProductData($id)
	{
		$this->unbindModel(array('hasAndBelongsToMany' => array('Category')), false);
		$this->bindDescription($this, 0, false);
		$this->bindOptions($this);

		if (Configure::read('Documents.assigned_to_products'))
		{
			$this->bindDocuments(0);
			$this->Document->order = 'Document.display_name';
		}
		
		if (Configure::read('Catalog.use_product_flags'))
		{
			$this->bindFlags();
		}
		
		if (Configure::read('Shipping.products_to_shipping_services'))
		{
			$this->bindShippingCarrierServices();
		}
		
		if (Configure::read('Catalog.use_tiered_customer_pricing') && Configure::read('Customer.group_id'))
		{
			$this->bindCustomerDiscounts(false, Configure::read('Customer.group_id'));
			$this->ProductPriceDiscount->order = array('ProductPriceDiscount.min_qty ASC');
		}

		$conditions = array(
			'Product.id' => $id,
			'Product.visibility !=' => 'notindividually'
		);
		
		$record = $this->find('first', array('conditions' => $conditions));
		
		if (Configure::read('Catalog.related_enabled'))
		{
			$this->bindRelatedProducts($this);
			$record['RelatedProduct'] = $this->RelatedProduct->getProducts($id);
		}
		
		if (Configure::read('Catalog.crosssells_enabled'))
		{
			$this->bindCrossSells($this);
			$record['CrossSells'] = $this->CrossSell->getProducts($id);
		}
		
		if ($record['Product']['type'] == 'grouped')
		{
			$this->bindGroupedProducts($this);
			$groupedProducts = $this->GroupedProduct->getProducts($id);
			
			foreach ($groupedProducts as $k => $v)
			{
				if (!empty($v['ProductOption']))
				{
					$options = $this->ProductOption->getOptions($v['Product']['id'], 'productonly');		
					$groupedProducts[$k]['ProductOption'] = $this->ProductOption->ProductOptionValue->addValuesToOptions($options, array(
						'currency_id' => Configure::read('Runtime.active_currency'),
						'language_id' => 1,
						'get_prices' => true,
						'get_names' => true		
					));
										
					/* Set an array item for quickly determining that 1 or more products
					   in the parent product group contain 1 or more product options */
					$record['Product']['grouped_products_have_options'] = true;
					
				}
			}
			
			$record['GroupedProducts'] = $groupedProducts;
			
		}
		else if ($record['Product']['type'] == 'simple')
		{
			$options = $this->ProductOption->getOptions($id, 'productonly');
			$record['ProductOption'] = $this->ProductOption->ProductOptionValue->addValuesToOptions($options, array(
				'currency_id' => Configure::read('Runtime.active_currency'),
				'language_id' => 1,
				'get_prices' => true,
				'get_names' => true	
			));
		}
		
		return $record;
		
	}
	
	/**
	 * Get list of grouped products that specified product is in.
	 * 
	 * @param int $id
	 * @return array
	 * $access public
	 */
	public function inGroups($id)
	{
		$type = $this->field('type', array('Product.id' => $id));
		
		if ($type != 'simple')
		{
			return array();
		}
		
		$result = ClassRegistry::init('GroupedProduct')->find('list', array(
			'fields' => array('GroupedProduct.id', 'GroupedProduct.from_product_id'),
			'conditions' => array('GroupedProduct.to_product_id' => $id)
		));
		
		return (!empty($result)) ? $result : array();
		
	}
	
	/**
	 * Update grouped product stock status.
	 * 
	 * @param int $groupedProductID
	 * @return void
	 * @access public
	 */
	public function updateGroupedProductStock($groupedProductID)
	{
		return;

		// Get products in group
		$products = ClassRegistry::init('GroupedProduct')->find('all', array(
			'conditions' => array('GroupedProduct.from_product_id' => $groupedProductID)
		));
		
		// Get stock statuses
		$stockStatuses = $this->StockStatus->find('list', array('fields' => array(
			'StockStatus.id', 'StockStatus.in_stock'
		)));
		
		$inStock = 0;
		
		foreach ($products as $product)
		{
			if (!empty($product['Product']['stock_in_stock']))
			{
				$inStock++;
			}
		}

		if ($inStock == count($products))
		{
			$this->updateAll(
				array('Product.stock_status_id' => Configure::read('Stock.in_stock_status'), 'stock_in_stock' => 1),
				array('Product.id' => $groupedProductID)
			);
		}
		else
		{
			$this->updateAll(
				array('Product.stock_status_id' => Configure::read('Stock.out_of_stock_status'), 'stock_in_stock' => 0),
				array('Product.id' => $groupedProductID)
			);
		}

	}
	
	/**
	 * Get list of models used for linking products.
	 * 
	 * @return array
	 * @access public
	 */
	public function getLinkedModels()
	{
		$assocs = array('GroupedProduct');
		
		if (Configure::read('Catalog.related_enabled'))
		{
			$assocs[] = 'RelatedProduct';
		}
		
		if (Configure::read('Catalog.crosssells_enabled'))
		{
			$assocs[] = 'CrossSell';
		}
		
		return $assocs;
		
	}
	
	private function saveProductLinks($modelName)
	{
		if (empty($this->data['Links'][$modelName]))
		{
			return false;
		}
		
		$split = explode(',', $this->data['Links'][$modelName]);
		
		$model = $this->{$modelName};
		
		// Get existing links
		$links = $model->find('list', array(
			'fields' => array($modelName . '.id', $modelName . '.to_product_id'),
			'conditions' => array($modelName . '.from_product_id' => $this->id)
		));
		
		foreach ($split as $productID)
		{
			if ($this->id == $productID || in_array($productID, $links))
			{
				continue;
			}
		
			$model->create();
			$model->save(array($modelName => array(
				'from_product_id' => $this->id,
				'to_product_id' => $productID
			)));

		}
		
	}
	
	/**
	 * Get featured products, optionally filtered by passed conditions
	 * 
	 * @param mixed $categoryID [optional]
	 * @return array $featuredProducts
	 * @access public
	 */
	public function getFeaturedProducts($categoryID = null)
	{
		$this->ProductCategory->Category->bindFeaturedProducts($this->ProductCategory->Category, false);
		
		$conditions = array();
		
		if (!empty($categoryID))
		{
			$featuredProductIDs = $this->ProductCategory->Category->CategoryFeaturedProduct->find('list', array(
				'fields' => array('CategoryFeaturedProduct.product_id', 'CategoryFeaturedProduct.product_id'),
				'conditions' => array('CategoryFeaturedProduct.category_id' => $categoryID),
				'limit' => 5
			));
			
			$featuredProducts = $this->find('all', array(
				'conditions' => array('Product.id' => $featuredProductIDs)
			));
		}
		else
		{
			$featuredProducts = $this->find('all', array(
				'conditions' => array('Product.featured' => 1, 'Product.visibility <>' => 'notindividually'),
				'order' => 'RAND()',
				'limit' => 5
			));
		}
		
		return $featuredProducts;
		
	}

	/**
	 * Save a new product record.
	 * 
	 * @param array $data
	 * @return mixed
	 * @access public
	 */
	public function addProduct($data)
	{
		$data['ProductMeta'] = array();
		
		foreach ($data['ProductName'] as $k => $name)
		{			
			$data['ProductMeta'][$k]['language_id'] = $name['language_id'];
			$data['ProductMeta'][$k]['product_name'] = $name['name'];
			$data['ProductMeta'][$k]['product_sku'] = $data['Product']['sku'];
		}
		
		$this->bindPrice(null, false);
		$this->bindName($this, null, false);
		$this->bindDescription($this, null, false);
		$this->bindMeta($this, null, false);
		
		$this->ProductPrice->removeProductIDvalidation();
		$this->ProductName->removeProductIDvalidation();
		$this->ProductDescription->removeProductIDvalidation();
		$this->ProductMeta->removeProductIDvalidation();
		
		$this->unbindModel(array('hasMany' => array('ProductCategory')), false);

		if (!empty($data['Product']['type']) && ($data['Product']['type'] == 'simple'))
		{
			$this->ProductPrice->addBasePriceValidation();
		}
		
		$validates = $this->saveAll($data, array('validate' => 'only'));
		
		if (!$validates)
		{
			return false;
		}
		
		if (!$this->save(array('Product' => $data['Product'])))
		{
			return false;
		}
		
		$productID = $this->getInsertID();
		$data['Product']['id'] = $productID;
		
		foreach ($data['ProductPrice'] as $k => $price)
		{
			$data['ProductPrice'][$k]['product_id'] = $productID;
		}
		foreach ($data['ProductName'] as $k => $v)
		{
			$data['ProductName'][$k]['product_id'] = $productID;		
		}
		foreach ($data['ProductDescription'] as $k => $v)
		{
			$data['ProductDescription'][$k]['product_id'] = $productID;
		}
		foreach ($data['ProductMeta'] as $k => $v)
		{
			$data['ProductMeta'][$k]['product_id'] = $productID;
		}
		
		if (!$this->saveAll($data))
		{
			$this->delete($productID);
			return false;
		}
		
		/*
		if (!$this->ProductMeta->saveAll($meta['ProductMeta']))
		{
			$this->delete($productID);
			return false;
		}
		*/
		
		return $productID;
		
	}
	
	/**
	 * Save a new product record which has been duplicated.
	 * 
	 * @param array $data
	 * @return mixed
	 * @access public
	 */
	public function addDuplicatedProduct($data)
	{
		$data['ProductMeta'] = array();
		
		foreach ($data['ProductName'] as $k => $name)
		{			
			$data['ProductMeta'][$k]['language_id'] = $name['language_id'];
			$data['ProductMeta'][$k]['product_name'] = $name['name'];
			$data['ProductMeta'][$k]['product_sku'] = $data['Product']['sku'];
		}
		
		$this->bindPrice(null, false);
		$this->bindName($this, null, false);
		$this->bindDescription($this, null, false);
		$this->bindMeta($this, null, false);

		$this->bindRelatedProducts($this, null, false);
		$this->bindGroupedProducts($this, null, false);
		$this->bindCrossSells($this, null, false);
		$this->bindOptions($this, null, false);
		$this->bindAttributes($this, null, false);
		$this->bindOptionStock($this, null, false);
		
		$this->unbindModel(array('hasMany' => array('ProductCategory')), false);

		if (!empty($data['Product']['type']) && ($data['Product']['type'] == 'simple'))
		{
			$this->ProductPrice->addBasePriceValidation();
		}
		
		/*$validates = $this->saveAll($data, array('validate' => 'only'));
		
		if (!$validates)
		{
			debug($this->validationErrors);
			debug($data);
			return false;
		}*/
		
		if (!$this->save(array('Product' => $data['Product'])))
		{
			return false;
		}
		
		$productID = $this->getInsertID();
		$data['Product']['id'] = $productID;
		
		foreach ($data['ProductPrice'] as $k => $price)
		{
			$data['ProductPrice'][$k]['product_id'] = $productID;
		}
		foreach ($data['ProductName'] as $k => $v)
		{
			$data['ProductName'][$k]['product_id'] = $productID;		
		}
		foreach ($data['ProductDescription'] as $k => $v)
		{
			$data['ProductDescription'][$k]['product_id'] = $productID;
		}
		foreach ($data['ProductCategory'] as $k => $v)
		{
			$data['ProductCategory'][$k]['product_id'] = $productID;
		}
		foreach ($data['ProductMeta'] as $k => $v)
		{
			$data['ProductMeta'][$k]['product_id'] = $productID;
		}

		foreach ($data['RelatedProduct'] as $k => $v)
		{
			$data['RelatedProduct'][$k]['from_product_id'] = $productID;
		}
		foreach ($data['GroupedProduct'] as $k => $v)
		{
			$data['GroupedProduct'][$k]['from_product_id'] = $productID;
		}
		foreach ($data['CrossSell'] as $k => $v)
		{
			$data['CrossSell'][$k]['from_product_id'] = $productID;
		}
		foreach ($data['ProductOption'] as $k => $v)
		{
			$data['ProductOption'][$k]['product_id'] = $productID;
		}
		foreach ($data['ProductOptionStock'] as $k => $v)
		{
			$data['ProductOptionStock'][$k]['product_id'] = $productID;
		}

		if(empty($data['RelatedProduct'])){
			unset($data['RelatedProduct']);
		}
		if(empty($data['GroupedProduct'])){
			unset($data['GroupedProduct']);
		}
		if(empty($data['CrossSell'])){
			unset($data['CrossSell']);
		}

		foreach ($data['ProductImage'] as $k => $v)
		{
			$data['ProductImage'][$k]['product_id'] = $productID;


		}

		foreach ($data['ProductImage'] as $k => $v)
		{
			$data['ProductImage'][$k]['product_id'] = $productID;

			$this->ProductImage->create($data['ProductImage'][$k]);
			$this->ProductImage->save();

			/* Duplicate variation images and change filename references as necessary */
			$types = array('original', 'large', 'medium', 'thumb', 'tiny');
			foreach($types as $type){
				$origFile = $data['ProductImage'][$k][$type . '_root_path'];
				if(empty($origFile)) continue;

				$info = pathinfo($origFile);

				$newFilename = explode('-', $info['filename']);
				array_pop($newFilename);
				$newFilename[] = $this->ProductImage->id;
				$newFilename = implode('-', $newFilename);

				$newFile = $info['dirname'] . DS . $newFilename . '.' . $info['extension'];

				copy($origFile, $newFile);
			}

			$this->ProductImage->set('filename', $newFilename);
			$this->ProductImage->save();
		}

		//debug($data);
		
		/*if (!$this->saveAll($data))
		{
			$this->delete($productID);
			return false;
		}*/
		
		
		if (!$this->ProductMeta->saveAll($data['ProductMeta']))
		{
			$this->delete($productID);
			return false;
		}

		if (!$this->ProductName->saveAll($data['ProductName']))
		{
			$this->delete($productID);
			return false;
		}

		if (!$this->ProductDescription->saveAll($data['ProductDescription']))
		{
			$this->delete($productID);
			return false;
		}

		if (!$this->ProductCategory->saveAll($data['ProductCategory']))
		{
			$this->delete($productID);
			return false;
		}

		if (!$this->ProductPrice->saveAll($data['ProductPrice']))
		{
			$this->delete($productID);
			return false;
		}

		if (!empty($data['RelatedProduct']) && !$this->RelatedProduct->saveAll($data['RelatedProduct']))
		{
			$this->delete($productID);
			return false;
		}

		if (!empty($data['GroupedProduct']) && !$this->GroupedProduct->saveAll($data['GroupedProduct']))
		{
			$this->delete($productID);
			return false;
		}

		if (!empty($data['CrossSell']) && !$this->CrossSell->saveAll($data['CrossSell']))
		{
			$this->delete($productID);
			return false;
		}

		foreach($data['ProductOption'] as $k => $option){
			unset($option['ProductOptionValue'], $option['ProductOptionName']);
			$this->ProductOption->create($option);
			$this->ProductOption->save();
			
			foreach ($data['ProductOption'][$k]['ProductOptionValue'] as $l => $v)
			{
				$data['ProductOption'][$k]['ProductOptionValue'][$l]['product_option_id'] = $this->ProductOption->id;
			}
			$this->ProductOption->ProductOptionValue->saveAll($data['ProductOption'][$k]['ProductOptionValue']);

			foreach ($data['ProductOption'][$k]['ProductOptionName'] as $l => $v)
			{
				$data['ProductOption'][$k]['ProductOptionName'][$l]['product_option_id'] = $this->ProductOption->id;
			}
			$this->ProductOption->ProductOptionName->saveAll($data['ProductOption'][$k]['ProductOptionName']);
		}

		/*if (!$this->ProductOption->saveAll($data['ProductOption']))
		{
			$this->delete($productID);
			return false;
		}*/

		foreach($data['ProductOptionStock'] as $k => $option){
			unset($option['ProductOptionStockImage'], $option['ProductOptionStockPrice']);

			$valueIDs = $this->ProductOption->ProductOptionValue->getValueIDs($productID);
			$origValueIDs = $this->ProductOption->ProductOptionValue->getValueIDs($data['Product']['orig_id']);

			// Replace old 'value_ids' with newly generated 'value_ids'
			$key = array_search(/*strrev(*/$option['value_ids']/*)*/, $origValueIDs);
			$option['value_ids'] = $valueIDs[$key];

			$this->ProductOptionStock->create($option);
			$this->ProductOptionStock->save();
			
			foreach ($data['ProductOptionStock'][$k]['ProductOptionStockImage'] as $l => $v)
			{
				$data['ProductOptionStock'][$k]['ProductOptionStockImage'][$l]['product_option_stock_id'] = $this->ProductOptionStock->id;

				$this->ProductOptionStock->ProductOptionStockImage->create($data['ProductOptionStock'][$k]['ProductOptionStockImage'][$l]);
				$this->ProductOptionStock->ProductOptionStockImage->save();

				/* Duplicate variation images and change filename references as necessary */
				$types = array('large', 'medium', 'small', 'thumb', 'tiny');
				foreach($types as $type){
					$origFile = $data['ProductOptionStock'][$k]['ProductOptionStockImage'][$l][$type . '_root_path'];
					if(empty($origFile)) continue;

					$info = pathinfo($origFile);
					if(!is_numeric($info['filename'])) continue; // If filename isn't numeric, it's probably a missing image - e.g. "no-thumb.png"

					$newFilename = $this->ProductOptionStock->ProductOptionStockImage->id;
					$newFile = $info['dirname'] . DS . $newFilename . '.' . $info['extension'];

					copy($origFile, $newFile);
				}

				$this->ProductOptionStock->ProductOptionStockImage->set('filename', $newFilename);
				$this->ProductOptionStock->ProductOptionStockImage->save();
			}
			//$this->ProductOptionStock->ProductOptionStockImage->saveAll($data['ProductOptionStock'][$k]['ProductOptionStockImage']);

			foreach ($data['ProductOptionStock'][$k]['ProductOptionStockPrice'] as $l => $v)
			{
				$data['ProductOptionStock'][$k]['ProductOptionStockPrice'][$l]['product_option_stock_id'] = $this->ProductOptionStock->id;
			}
			$this->ProductOptionStock->ProductOptionStockPrice->saveAll($data['ProductOptionStock'][$k]['ProductOptionStockPrice']);
		}

		/*if (!$this->ProductOptionStock->saveAll($data['ProductOptionStock']))
		{
			$this->delete($productID);
			return false;
		}*/

		/* Attributes */
		/*foreach($data['AttributeSet']['Attribute'] as $k => $a)
		{
			$extract = Set::extract('/AttributeValue/id', $a);
			$data['Attribute'][$k] = $extract;
		}

		unset($data['AttributeSet']);

		$this->AttributeSet->Attribute->saveAll($data['Attribute']);*/

		/*debug($data);

		$this->delete($productID);
		return false;*/
		
		
		return $productID;
		
	}
	
	/**
	 * Duplicate a product record.
	 * 
	 * @param int $id
	 * @return mixed
	 * @access public
	 */
	public function duplicateProduct($id)
	{
		//exit('way behind the curve');
		//echo "let's give it a go...";
		
		$this->unbindModel(array(
			'belongsTo' => array('MainCategory', 'Manufacturer', 'StockStatus'),
			'hasAndBelongsToMany' => array('Category')
		));
		
		$this->bindPrice(null, false);
		$this->bindName($this, null, false);
		$this->bindDescription($this, null, false);
		$this->bindMeta($this, null, false);

		$this->bindRelatedProducts($this, null, false);
		$this->bindGroupedProducts($this, null, false);
		$this->bindCrossSells($this, null, false);
		$this->bindOptions($this, null, false);
		$this->bindOptionStock($this, null, false);
		$this->ProductOption->bindName($this->ProductOption);
		$this->ProductOptionStock->bindPrice(null, false);
		$this->bindAttributes($this, null, false);

		//$this->recursive = 3;
		$this->contain(array(
			'ProductPrice',
			'ProductName',
			'ProductDescription',
			'ProductMeta',
			'ProductCategory',
			'ProductImage',
			'RelatedProduct',
			'GroupedProduct',
			'CrossSell',
			'ProductOption',
			'ProductOption.ProductOptionName',
			'ProductOption.ProductOptionValue',
			'ProductOptionStock',
			'ProductOptionStock.ProductOptionStockImage',
			'ProductOptionStock.ProductOptionStockPrice',
			'AttributeSet',
			'AttributeSet.Attribute',
			'AttributeSet.Attribute.AttributeValue'
		));
		$record = $this->findById($id);
		
		$record['Product']['orig_id'] = $record['Product']['id'];
		unset($record['Product']['id']);
		$record['Product']['sku'] .= ' [copy]';
		
		foreach ($record['ProductPrice'] as $k => $data)
		{
			unset($record['ProductPrice'][$k]['id']);
		}
		
		foreach ($record['ProductName'] as $k => $data)
		{
			unset($record['ProductName'][$k]['id']);
			
			$record['ProductName'][$k]['name'] .= ' [copy]';
			//$record['ProductName'][$k]['url'] .= '-copy';
			if (!empty($data['sub_name']))
			{
				$record['ProductName'][$k]['sub_name'] .= ' [copy]';
			}
		}

		foreach ($record['ProductMeta'] as $k => $data)
		{
			unset($record['ProductMeta'][$k]['id']);
			
			$record['ProductMeta'][$k]['url'] .= '-copy';
		}

		foreach ($record['RelatedProduct'] as $k => $data)
		{
			unset($record['RelatedProduct'][$k]['id']);
		}

		foreach ($record['GroupedProduct'] as $k => $data)
		{
			unset($record['GroupedProduct'][$k]['id']);
		}

		foreach ($record['CrossSell'] as $k => $data)
		{
			unset($record['CrossSell'][$k]['id']);
		}

		foreach ($record['ProductImage'] as $k => $data)
		{
			unset($record['ProductImage'][$k]['id']);
		}

		foreach ($record['ProductOption'] as $k => $data)
		{
			unset($record['ProductOption'][$k]['id']);
			foreach($record['ProductOption'][$k]['ProductOptionValue'] as $l => $d){
				unset($record['ProductOption'][$k]['ProductOptionValue'][$l]['id']);
				unset($record['ProductOption'][$k]['ProductOptionValue'][$l]['product_option_id']);
			}
			foreach($record['ProductOption'][$k]['ProductOptionName'] as $l => $d){
				unset($record['ProductOption'][$k]['ProductOptionName'][$l]['id']);
				unset($record['ProductOption'][$k]['ProductOptionName'][$l]['product_option_id']);
			}
		}

		foreach ($record['ProductOptionStock'] as $k => $data)
		{
			unset($record['ProductOptionStock'][$k]['id']);
			$record['ProductOptionStock'][$k]['sku'] .= ' [copy]';
			foreach($record['ProductOptionStock'][$k]['ProductOptionStockImage'] as $l => $d)
			{
				unset($record['ProductOptionStock'][$k]['ProductOptionStockImage'][$l]['id']);
				unset($record['ProductOptionStock'][$k]['ProductOptionStockImage'][$l]['product_option_stock_id']);
			}
			foreach($record['ProductOptionStock'][$k]['ProductOptionStockPrice'] as $l => $d)
			{
				unset($record['ProductOptionStock'][$k]['ProductOptionStockPrice'][$l]['id']);
				unset($record['ProductOptionStock'][$k]['ProductOptionStockPrice'][$l]['product_option_stock_id']);
			}
		}

		foreach ($record['ProductPrice'] as $k => $data)
		{
			unset($record['ProductPrice'][$k]['id']);
		}
		
		foreach ($record['ProductDescription'] as $k => $data)
		{
			unset($record['ProductDescription'][$k]['id']);
		}

		foreach ($record['ProductCategory'] as $k => $data)
		{
			unset($record['ProductCategory'][$k]['id']);
		}

		//debug($record); exit;

		return $this->addDuplicatedProduct($record);
		
	}
	
	/**
	 * Get list of products (ie for selects)
	 *
	 * @param array $conditions [optional]
	 * @return array $records
	 * @access public
	 */
	public function getList($conditions = array())
	{
		$this->unbindModel(array(
			'belongsTo' => array('Manufacturer'),
			'hasMany' => array('ProductImage', 'ProductDescription', 'ProductPrice', 'ProductMeta', 'ProductOption', 'RelatedProduct'),
			'hasAndBelongsToMany' => array('Category')
		));
		
		$this->bindName($this, Configure::read('Languages.main_lang_id'), false);
		
		$records = $this->find('all', array(
			'fields' => array('Product.id', 'Product.sku', 'ProductName.name'),
			'conditions' => $conditions
		));
		
		$list = array();
		
		foreach ($records as $k => $record)
		{
			$list[$record['Product']['id']] = $record['Product']['sku'] . ' ' . $record['ProductName']['name'];
		}
		
		return $list;
		
	}
	
	/**
	 * Get featured products
	 *
	 * @return array $records
	 * @access public
	 */
	public function getFeatured()
	{
		return $this->find('all', array('conditions' => array(
			'Product.featured' => 1
		)));
	}
	/**
	 * Get new products
	 *
	 * @return array
	 * @access public
	 */
	public function getNewProducts()
	{
		return $this->find('all', array(
			'conditions' => array(
				'Product.new_product' => 1
			)
		));
	}
	/**
	 * Get best seller products
	 *
	 * @return array
	 * @access public
	 */
	public function getBestSellerProducts()
	{
		return $this->find('all', array(
			'conditions' => array(
				'Product.best_seller' => 1
			)
		));
	}
	/**
	 * Get products with special price
	 *
	 * @return array
	 * @access public
	 */
	public function getSpecialPriceProducts()
	{
		return $this->find('all', array(
			'conditions' => $this->getSpecialPriceProductsConditions()
		));
	}
	
	/**
	 * Get array of conditions used for finding products on special
	 *
	 * @return array
	 * @access public
	 */
	public function getSpecialPriceProductsConditions()
	{
		return array(
			'ProductPrice.special_price >' => 0,
			array(
				array('OR' => array(
					array('ProductPrice.special_price_date_from' => null),
					array('ProductPrice.special_price_date_from' => '0000-00-00'),
					array('ProductPrice.special_price_date_from <=' => date('Y-m-d'))
				)),
				array('OR' => array(
					array('ProductPrice.special_price_date_to' => null),
					array('ProductPrice.special_price_date_to' => '0000-00-00'),
					array('ProductPrice.special_price_date_to >=' => date('Y-m-d'))
				))
			)
		);
	}
	
	/*
	public function isAddableToBasket($id)
	{
		$product = $this->findById($id);
		
		if (empty($product['Product']['active']))
		{
			return false;
		}
		
		return true;
		
	}
	*/
	
	/**
	 * Bind product name(s) to product
	 *
	 * @param object $model
	 * @param mixed $languageID
	 * @param bool $reset
	 * @return void
	 * @access public
	 */
	public function bindName($model, $languageID = null, $reset = false)
	{
		$this->bind($model, 'ProductName', $languageID, $reset);
	}
	
	/**
	 * Bind product description(s) to products
	 *
	 * @param object $model
	 * @param mixed $languageID
	 * @param bool $reset
	 * @return void
	 * @access public
	 */
	public function bindDescription($model, $languageID = null, $reset = false)
	{
		$this->bind($model, 'ProductDescription', $languageID, $reset);
	}
	
	/**
	 * Bind product meta(s) to products
	 *
	 * @param object $model
	 * @param mixed $languageID
	 * @param bool $reset
	 * @return void
	 * @access public
	 */
	public function bindMeta($model, $languageID = null, $reset = false)
	{
		$this->bind($model, 'ProductMeta', $languageID, $reset);
	}
	
	/**
	 * Bind related products to products
	 *
	 * @param object $model
	 * @return void
	 * @access public
	 */
	public function bindRelatedProducts($model)
	{
		$model->bindModel(array(
			'hasMany' => array(
				'RelatedProduct' => array('foreignKey' => 'from_product_id')
			)
		), false);
	}
	
	/**
	 * Bind products for grouped products.
	 *
	 * @param object $model
	 * @return void
	 * @access public
	 */
	public function bindGroupedProducts($model)
	{
		$model->bindModel(array(
			'hasMany' => array(
				'GroupedProduct' => array('foreignKey' => 'from_product_id')
			)
		), false);
	}
	
	/**
	 * Bind cross sells products
	 *
	 * @param object $model
	 * @return void
	 * @access public
	 */
	public function bindCrossSells($model)
	{
		$model->bindModel(array(
			'hasMany' => array(
				'CrossSell' => array('foreignKey' => 'from_product_id')
			)
		), false);
	}
	
	/**
	 * Bind product custom options.
	 *
	 * @param object $model
	 * @param bool $reset
	 * @return void
	 * @access public
	 */
	public function bindOptions($model, $reset = false)
	{
		$model->bindModel(array(
			'hasMany' => array(
				'ProductOption' => array(
					'className' => 'ProductOption', 
					'foreignKey' => 'product_id'
				)
			)
		), $reset);
	}
	
	/**
	 * Bind product attributes.
	 *
	 * @param bool $reset
	 * @return void
	 * @access public
	 */
	public function bindAttributes($reset = false)
	{
		$this->bindModel(array(
			'belongsTo' => array('AttributeSet'),
			'hasAndBelongsToMany' => array('AttributeValue' => array(
				'with' => 'AttributeValuesProduct'
			))
		), $reset);
	}

	/**
	 * Bind product documents.
	 *
	 * @param bool $reset
	 * @return void
	 * @access public
	 */
	public function bindDocuments($reset = false)
	{
		$this->bindModel(array(
			'hasAndBelongsToMany' => array('Document' => array(
				'joinTable' => 'product_documents'
			))
		), $reset);
	}

	/**
	 * Bind delivery services.
	 *
	 * @param bool $reset
	 * @return void
	 * @access public
	 */
	public function bindShippingCarrierServices($reset = false)
	{
		$this->bindModel(array(
			//'hasAndBelongsToMany' => array('ShippingCarrierService' => array('with' => 'ProductShippingCarrierService'))
			'hasMany' => array('ProductShippingCarrierService')
		), $reset);
	}
	
	/**
	 * Bind product price customer discount.
	 *
	 * @param bool $reset
	 * @return void
	 * @access public
	 */
	public function bindCustomerDiscounts($reset = false)
	{
		$this->bindModel(array(
			'hasMany' => array('ProductPriceDiscount' => array(
				'conditions' => array(
					'ProductPriceDiscount.customer_group_id' => Configure::read('Customer.group_id')
				)
			))
		), $reset);
	}
	
	/**
	 * Bind single qty product price customer discount.
	 *
	 * @param bool $reset
	 * @return void
	 * @access public
	 */
	public function bindSingleQtyDiscount($reset = true)
	{
		$this->bindModel(array(
			'hasOne' => array('SingleQtyProductPriceDiscount' => array(
				'className' => 'ProductPriceDiscount',
				'foreignKey' => 'product_id',
				'type' => 'LEFT',
				'fields' => array('SingleQtyProductPriceDiscount.discount_amount'),
				'conditions' => array(
					'SingleQtyProductPriceDiscount.customer_group_id' => Configure::read('Customer.group_id'),
					'SingleQtyProductPriceDiscount.min_qty' => 1
				)
			))
		), $reset);
	}
	
	/**
	 * Bind flags.
	 *
	 * @param bool $reset
	 * @return void
	 * @access public
	 */
	public function bindFlags($reset = false)
	{
		$this->bindModel(array(
			'hasAndBelongsToMany' => array('ProductFlag')
		), $reset);
	}
	
	/**
	 * Bind variations.
	 *
	 * @param bool $reset
	 * @return void
	 * @access public
	 */
	public function bindOptionStock($reset = false)
	{
		$this->bindModel(array(
			'hasMany' => array('ProductOptionStock' => array(
				'foreignKey' => 'product_id'
			))
		), $reset);
	}
	
}


