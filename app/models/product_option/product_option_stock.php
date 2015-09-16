<?php

/**
 * Product Option Stock
 * 
 */
class ProductOptionStock extends AppModel
{
	/**
	 * Custom database table name, or null/false if no table association is desired.
	 *
	 * @var string
	 * @access public
	 */
	public $useTable = 'product_option_stock';
	
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array('Product');
	
	/**
	 * Detailed list of hasMany associations.
	 *
	 * @var array
	 * @access public
	 */
	public $hasMany = array('ProductOptionStockImage' => array(
		'order' => array(
			'ISNULL(ProductOptionStockImage.sort_order)' => 'ASC',
			'ProductOptionStockImage.sort_order' => 'ASC'
		)
	));

	/**
	 * The column name(s) and direction(s) to order find results by default.
	 *
	 * @var string
	 * @access public
	 */
	public $order = 'ProductOptionStock.value_ids ASC';
	
	/**
	 * List of validation rules. 
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(
		'product_id' => array(
			'rule' => array('greaterThan', 'product_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Product ID missing'
		),
		'option_ids' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Option IDs missing'
		),
		'value_ids' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Value IDs missing'
		),
		'name' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Option name missing'
		),
		'sku' => array(
			'validSku' => array(
				'rule' =>  array('minLength', 1),
				'required' => true,
				'allowEmpty' => false,
				'message' => 'SKU missing'			
			),
			'uniqueOptionSku' => array(
				'rule' =>  'isUnique',
				'message' => 'SKU in use'
			),
			'uniqueProductSku' => array(
				'rule' =>  'isUniqueProductSku',
				'message' => 'SKU in use'
			)			
		)
	);
	
	public function isUniqueProductSku($check)
	{
		$unqiueProductSku = $this->Product->find('count', array(
			'conditions' => array('Product.sku' => $check['sku'])
		));
		
		return empty($unqiueProductSku);
		
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
		if (empty($results) || !isset($results[0]['ProductOptionStock']))
		{
			return $results;
		}
		
		foreach ($results as $k => $result)
		{
			if (!empty($result['ProductOptionStockPrice']['id']) && !empty($result['ProductPrice']['id']))
			{	
				$productPrice = $result['ProductPrice']['active_price'];
				$modifier = $result['ProductOptionStock']['modifier'];
				$modValue = $result['ProductOptionStockPrice']['modifier_value'];
				
				$varPrice = $this->Product->ProductPrice->getVarPrice($productPrice, $modifier, $modValue);
				
				$results[$k]['ProductOptionStockPrice']['base_price'] = $result['ProductPrice']['base_price'];
				$results[$k]['ProductOptionStockPrice']['active_price'] = $varPrice;



				/*if (!empty($varPrice) && Configure::read('Customer.id'))
				{
					$discountAmount = Configure::read('Customers.discount_percentage');
					$discount = ($discountAmount / 100) * $varPrice;

					$results[$k]['ProductOptionStockPrice']['active_price'] = $varPrice - $discount;
					$results[$k]['ProductOptionStockPrice']['member_saving'] = $discount;

				}*/
				

				/*
				// Discounted Price (single qty)
				if (!empty($result['SingleQtyProductOptionStockDiscount']['discount_amount']))
				{
					$discountAmount = str_pad($result['SingleQtyProductOptionStockDiscount']['discount_amount'], 2, '0', STR_PAD_LEFT);
					$tradePrice = number_format($varPrice - ($varPrice * floatval('0.' . $discountAmount)), 2);
					$results[$k]['ProductOptionStockPrice']['trade_price'] = $tradePrice;
				}

				// Discounted Price (all)
				if (!empty($result['ProductOptionStockDiscount'][0]) && ($result['ProductOptionStockDiscount'][0]['min_qty'] == 1))
				{
					$discountAmount = str_pad($result['ProductOptionStockDiscount'][0]['discount_amount'], 2, '0', STR_PAD_LEFT);
					$tradePrice = number_format($varPrice - ($varPrice * floatval('0.' . $discountAmount)), 2);
					$results[$k]['ProductOptionStockPrice']['trade_price'] = $tradePrice;
				}
				*/

				if(Configure::read('Runtime.trade_pricing')){
					if (!empty($result['ProductOptionStockPrice']['trade_modifier_value'])){
						$results[$k]['ProductOptionStockPrice']['active_price'] = $result['ProductOptionStockPrice']['trade_modifier_value'];
						$results[$k]['ProductOptionStockPrice']['on_special'] = 0;
					}
				}


				
			}
			
			$results[$k]['ProductOptionStock']['main_tiny_image_path']   = '/img/vars/no-tiny.png';
			$results[$k]['ProductOptionStock']['main_small_image_path']  = '/img/vars/no-small.png';
			$results[$k]['ProductOptionStock']['main_thumb_image_path']  = '/img/vars/no-thumb.png';
			$results[$k]['ProductOptionStock']['main_medium_image_path'] = '/img/vars/no-medium.png';
			$results[$k]['ProductOptionStock']['main_large_image_path']  = '/img/vars/no-large.png';

			if (!empty($result['ProductOptionStockImage'][0]))
			{
				$results[$k]['ProductOptionStock']['main_tiny_image_path']   = $result['ProductOptionStockImage'][0]['tiny_web_path'];
				$results[$k]['ProductOptionStock']['main_small_image_path']  = $result['ProductOptionStockImage'][0]['small_web_path'];
				$results[$k]['ProductOptionStock']['main_thumb_image_path']  = $result['ProductOptionStockImage'][0]['thumb_web_path'];
				$results[$k]['ProductOptionStock']['main_medium_image_path'] = $result['ProductOptionStockImage'][0]['medium_web_path'];
				$results[$k]['ProductOptionStock']['main_large_image_path']  = $result['ProductOptionStockImage'][0]['large_web_path'];
			}

		}
		
		return $results;

	}
	
	/**
	 * Called during save operations, before validation. Please note that custom
	 * validation rules can be defined in $validate.
	 *
	 * @return boolean True if validate operation should continue, false to abort
	 * @param $options array Options passed from model::save(), see $options of model::save().
	 * @access public
	 */
	public function beforeValidate($options = array())
	{
		if (!empty($options['skipBeforeValidate']))
		{
			return true;
		}

		$product = $this->Product->findById($this->data['ProductOptionStock']['product_id']);
		$baseSku = $product['Product']['sku'];
		
		$ProductOptionValue = ClassRegistry::init('ProductOptionValue');
		$ProductOptionValue->bindModel(array('belongsTo' => array('CustomOptionValue')));
		$ProductOptionValue->CustomOptionValue->bindName($ProductOptionValue->CustomOptionValue, 0);
		
		$values = explode('-', $this->data['ProductOptionStock']['value_ids']);
		
		$name = '';
		$optionIDs = array();
		
		foreach ($values as $id)
		{
			$value = $ProductOptionValue->findById($id);
			$customOptionValueID = $value['ProductOptionValue']['custom_option_value_id'];
			
			$optionIDs[] = $value['ProductOptionValue']['product_option_id'];
			
			$record = $ProductOptionValue->CustomOptionValue->findById($customOptionValueID);
			
			$name .= $record['CustomOptionValueName']['name'] . ', ';
			
		}
		
		$optionIDs = implode('-', $optionIDs);
		
		$this->data['ProductOptionStock']['option_ids'] = $optionIDs;
		
		if (empty($this->data['ProductOptionStock']['name']))
		{
			$this->data['ProductOptionStock']['name'] = substr($name, 0, -2);
		}
		
		if (empty($this->data['ProductOptionStock']['sku']))
		{
			$this->data['ProductOptionStock']['sku'] = $baseSku . '.' . $this->data['ProductOptionStock']['value_ids']; // $optionIDs
		}
	
		return true;
		
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
		$this->data['ProductOptionStock']['stock_in_stock'] = (!empty($this->data['ProductOptionStock']['stock_base_qty'])) ? 1 : 0;
		
		return true;
	}
	
	/**
	 * Called after each successful save operation.
	 *
	 * @param boolean $created True if this save created a new record
	 * @access public
	 */
	public function afterSave($created)
	{
		if (!empty($this->data['ProductOptionStock']['ProductOptionStockPrice']))
		{
			$this->bindPrice(null);
			
			foreach ($this->data['ProductOptionStock']['ProductOptionStockPrice'] as $k => $v)
			{
				$this->data['ProductOptionStock']['ProductOptionStockPrice'][$k]['product_option_stock_id'] = $this->id;
			}
			
			$this->ProductOptionStockPrice->saveAll($this->data['ProductOptionStock']['ProductOptionStockPrice']);
		}
		
	}
	
	
	public function get($conditions, $rekey = false)
	{
		$this->unbindModel(array('belongsTo' => array('Product')));
		
		/*
		if (Configure::read('Catalog.use_tiered_customer_pricing') && Configure::read('Customer.group_id'))
		{
			$this->bindCustomerDiscounts(false, Configure::read('Customer.group_id'));
			$this->bindSingleQtyDiscount(false);
		}
		*/
		
		$type = 'all';
		
		$singleRecordConditions = array(
			'ProductOptionStock.id',
			'ProductOptionStock.value_ids',
			'ProductOptionStock.sku'
		);
		
		foreach ($conditions as $k => $v)
		{
			if (in_array($k, $singleRecordConditions))
			{
				$type = 'first';
				break;
			}
		}
		
		$fields = array('ProductOptionStock.*', 'Product.*', 'ProductName.*', 'ProductPrice.*');
		
		if (!empty($this->hasOne['ProductOptionStockPrice']))
		{
			$fields[] = 'ProductOptionStockPrice.*';
		}
		
		if (!empty($this->hasOne['SingleQtyProductOptionStockDiscount']))
		{
			$fields[] = 'SingleQtyProductOptionStockDiscount.*';
		}
		
		$stock = $this->find($type, array(
			'fields' => $fields,
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'LEFT',
					'conditions'=> array(
						'Product.id = ProductOptionStock.product_id'
					)
				),
				array(
					'table' => 'product_names',
					'alias' => 'ProductName',
					'type' => 'LEFT',
					'conditions'=> array(
						'Product.id = ProductName.product_id',
						'ProductName.language_id = 1'
					)
				),
				array(
					'table' => 'product_prices',
					'alias' => 'ProductPrice',
					'type' => 'LEFT',
					'conditions'=> array(
						'Product.id = ProductPrice.product_id',
						'ProductPrice.currency_id = ' . Configure::read('Runtime.active_currency')
					)
				)
			)
		));
		
		if (!empty($rekey) && !empty($this->hasMany['ProductOptionStockPrice']))
		{
			$stock = $this->rekey($stock);
		}
		
		return $stock;
	
	}
	
	
	public function getByID($stockID, $rekey = false)
	{
		$conditions = array('ProductOptionStock.id' => $stockID);
		return $this->get($conditions, $rekey);
	}

	public function getAvailableByID($stockID, $rekey = false)
	{
		$conditions = array(
			'ProductOptionStock.id' => $stockID,
			'ProductOptionStock.available' => 1
		);
		return $this->get($conditions, $rekey);
	}
	
	public function getStockByProduct($productID, $rekey = false)
	{
		$conditions = array('ProductOptionStock.product_id' => $productID);
		return $this->get($conditions, $rekey);
	}
	
	public function getStockBySku($sku, $rekey = false)
	{
		$conditions = array('ProductOptionStock.sku' => $sku);
		return $this->get($conditions, $rekey);
	}

	public function getStockByProductAndValues($productId, $values, $rekey = false)
	{
		$conditions = array(
			'ProductOptionStock.product_id' => $productId,
			'ProductOptionStock.value_ids' => $values
		);
		
		return $this->get($conditions, $rekey);		
	}

	public function getStockByValues($values, $rekey = false)
	{
		$conditions = array('ProductOptionStock.value_ids' => $values);
		return $this->get($conditions, $rekey);
	}



	public function getAvailableStockByProduct($productID, $rekey = false)
	{
		$conditions = array(
			'ProductOptionStock.product_id' => $productID,
			'ProductOptionStock.available' => 1
		);
		return $this->get($conditions, $rekey);
	}



	
	private function rekey($stock)
	{
		foreach ($stock as $k => $v)
		{
			$stock[$k]['ProductOptionStockPrice'] = array();
			foreach ($v['ProductOptionStockPrice'] as $price)
			{
				$currencyID = $price['currency_id'];
				$stock[$k]['ProductOptionStockPrice'][$currencyID] = $price;
			}
		}
		
		return $stock;
		
	}	
		
	/**
	 * Save stock.
	 * 
	 * @param array $data
	 * @return void
	 * @access public
	 */
	public function saveStock($data)
	{	
		if (empty($data['ProductOptionStock']))
		{
			return false;
		}
		
		$this->saveAll($data['ProductOptionStock']);
		
	}
	
	
	public function updateVariationStock($productID, $newProductOptionValueID)
	{
		$ProductOptionValue = ClassRegistry::init('ProductOptionValue');
		$ProductOptionValue->bindModel(array('belongsTo' => array('ProductOption')), false);
		
		$options = $ProductOptionValue->ProductOption->getOptionCount($productID);
		
		// If only one product option, insert and return
		if ($options === 1)
		{
			$data = $this->getNewStockRecordData($productID, $newProductOptionValueID);
			
			$this->create();
			$this->save($data);
			
			return;
		}
		
		$stock = $this->find('list', array(
			'fields' => array('ProductOptionStock.id', 'ProductOptionStock.value_ids'),
			'conditions' => array('ProductOptionStock.product_id' => $productID),
			'order' => 'ProductOptionStock.sort DESC'
		));
		
		$valueIDs = $ProductOptionValue->getValueIDs($productID);
		
		foreach ($valueIDs as $k => $valueID)
		{
			if (in_array($valueID, $stock))
			{
				continue;
			}
			
			$data = $this->getNewStockRecordData($productID, $valueID);
			$this->create();
			$this->save($data);
		
		}
		
	}
	
	public function initOptionStock($productID)
	{
		$this->deleteAll(array('ProductOptionStock.product_id' => $productID));
		
		$valueIDs = ClassRegistry::init('ProductOptionValue')->getValueIDs($productID);
		
		foreach ($valueIDs as $k => $valueID)
		{
			$data = $this->getNewStockRecordData($productID, $valueID);
			$this->create();
			$this->save($data);
		}
		
	}
	
	public function getNewStockRecordData($productID, $valueIDs)
	{
		$data = array('ProductOptionStock' => array(
			'product_id' => $productID,
			'value_ids' => $valueIDs,
			'available' => 1,
			// 'stock_status_id' => Configure::read('Stock.default_stock_status')
		));
		
		$currencies = ClassRegistry::init('Currency')->find('list');
		
		$i = 0;
		
		foreach ($currencies as $currencyID => $name)
		{
			$data['ProductOptionStock']['ProductOptionStockPrice'][$i] = array(
				'currency_id' => $currencyID
			);
			
			$i++;
		}
		
		return $data;
	
	}
	
	public function removeStockByValue($productID, $productOptionValueID)
	{
		$stock = $this->getStockByProduct($productID);
		
		foreach ($stock as $k => $v)
		{
			$ids = explode('-', $v['ProductOptionStock']['value_ids']);
			
			if (in_array($productOptionValueID, $ids))
			{
				$this->delete($v['ProductOptionStock']['id']);
			}
		}
	
	}
	
	
	public function addVarsToProducts($products, $customerDiscounts = 'all')
	{
		if (empty($products))
		{
			return $products;
		}
		
		foreach ($products as $k => $product)
		{
			$this->bindPrice(0, true);
		
			if (Configure::read('Catalog.use_tiered_customer_pricing') && Configure::read('Customer.group_id'))
			{
				if ($customerDiscounts == 'all')
				{
					$this->bindCustomerDiscounts(true, Configure::read('Customer.group_id'));
				}
				else if ($customerDiscounts == 'singleqty')
				{
					$this->bindSingleQtyDiscount(false);
				}
			}

			$vars = $this->getStockByProduct($product['Product']['id'], false);
			
			if (empty($vars))
			{
				continue;
			}
			
			$products[$k]['ProductOptionStock'] = $vars;
			
			foreach ($vars as $var)
			{
				if (!empty($var['ProductOptionStockImage'][0]))
				{
					$products[$k]['Product']['has_var_images'] = true;
					
					if ($product['Product']['default_product_option_stock_id'] == $var['ProductOptionStock']['id'])
					{
						$products[$k]['ProductImage'] = $var['ProductOptionStockImage'];
						break;
					}
				}
			}
			
			$tradePrices = array_filter(Set::extract('{n}.ProductOptionStockPrice.trade_price', $vars), 'notEmpty');
			
			if (!empty($tradePrices))
			{
				$products[$k]['ProductPrice']['lowest_trade_price'] = min($tradePrices);
			}
			
		}
		
		return $products;
		
	}
		
	
	/**
	 * Bind product option price customer discount.
	 *
	 * @param bool $reset
	 * @return void
	 * @access public
	 */
	public function bindCustomerDiscounts($reset = false, $customerGroupID = null)
	{
		$conditions = array();
		$order = array('ProductOptionStockDiscount.min_qty ASC');
		
		if (!empty($customerGroupID))
		{
			$conditions['ProductOptionStockDiscount.customer_group_id'] = $customerGroupID;
		}
		
		$this->bindModel(array('hasMany' => array('ProductOptionStockDiscount' => array(
			'conditions' => $conditions, 
			'order' => $order
		))), $reset);
		
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
			'hasOne' => array('SingleQtyProductOptionStockDiscount' => array(
				'className' => 'ProductOptionStockDiscount',
				'foreignKey' => 'product_option_stock_id',
				'type' => 'LEFT',
				'fields' => array('SingleQtyProductOptionStockDiscount.discount_amount'),
				'conditions' => array(
					'SingleQtyProductOptionStockDiscount.customer_group_id' => Configure::read('Customer.group_id'),
					'SingleQtyProductOptionStockDiscount.min_qty' => 1
				)
			))
		), $reset);
	}
	
}






