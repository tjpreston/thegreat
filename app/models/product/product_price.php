<?php

/**
 * Product Price Model
 * 
 */
class ProductPrice extends AppModel
{
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array('Product', 'Currency');
	
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
		'currency_id' => array(
			'rule' => array('greaterThan', 'currency_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Currency ID missing'
		)
	);
	
	/**
	 * Called after each successful save operation.
	 *
	 * @param boolean $created True if this save created a new record
	 * @access public
	 */
	public function afterSave()
	{
		$productID = $this->data['ProductPrice']['product_id'];

		// Update product price record with active price (taking into account active special offers)
		$this->updateActivePrices($productID);
		
		if ($this->Product->field('type') == 'grouped')
		{
			// Update self
			$this->updateGroupedProductPrice($productID);
		}
		else
		{
			// Update any groups (price and stock) that saved products belongs to
			foreach ($this->Product->inGroups($this->id) as $groupedProductID)
			{
				$this->updateGroupedProductPrice($groupedProductID);
			}
		}
		
	}

	public function afterFind($results, $primary){
		$vatRate = Configure::read('Pricing.vat');

		foreach($results as $k => $result){
			if(empty($result[$this->name])) continue;

			$fields = array('active_price', 'lowest_price');
			foreach($fields as $field){
				if(!empty($result[$this->name][$field]) && is_numeric($result[$this->name][$field])){
					$price = $result[$this->name][$field];
					
				}
			}
		}

		return $results;
	}
	
	/**
	 * Add base price to validation rules.
	 * 
	 * @return void
	 * @access public
	 */
	public function addBasePriceValidation()
	{
		$this->validate['base_price'] = array(
			'rule' => array('greaterThan', 'base_price', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter a price for the product'
		);
	}
	
	/**
	 * Get product prices
	 * 
	 * @param int $productID
	 * @return array $prices
	 * @access public
	 */
	public function getPrices($productID)
	{
		$records = $this->find('all', array(
			'conditions' => array('ProductPrice.product_id' => $productID),
			'recursive' => -1
		));

		$prices = array();
		
		foreach ($records as $k => $record)
		{
			$currencyID = $record['ProductPrice']['currency_id'];
			$prices[$currencyID] = $record['ProductPrice'];
		}

		return $prices;
		
	}
	
	/**
	 * Update product active prices.
	 * 
	 * @param int $productID [optional]
	 * @return void
	 * @access public
	 */
	public function updateActivePrices($productID = null)
	{
		$sql = 'UPDATE product_prices AS ProductPrice SET 
				ProductPrice.active_price = IF (
					(ProductPrice.special_price > 0) AND 
					((ProductPrice.special_price_date_from IS NULL) OR (ProductPrice.special_price_date_from = "0000-00-00") OR (ProductPrice.special_price_date_from <= NOW())) AND 
					((ProductPrice.special_price_date_to IS NULL) OR (ProductPrice.special_price_date_to = "0000-00-00") OR (ProductPrice.special_price_date_to >= NOW())), 
						ProductPrice.special_price, ProductPrice.base_price
				)
		';

		if (!empty($productID))
		{
			$sql .= " WHERE (product_id = '" . intval($productID) . "')";
		}
		
		$result = $this->query($sql);
		
		$sql = 'UPDATE product_prices AS ProductPrice SET 
				ProductPrice.on_special = IF ((ProductPrice.active_price < ProductPrice.base_price), 1, 0)
		';
		
		if (!empty($productID))
		{
			$sql .= " WHERE (product_id = '" . intval($productID) . "')";
		}
		
		$result = $this->query($sql);
			
	}
	
	/**
	 * Update product price lowest/highest amount from product options.
	 * 
	 * @param int $productID
	 * @return void
	 * @access public
	 */	
	public function updateLowestHightestPrices($productID)
	{
		foreach ($this->Currency->find('list') as $currencyID => $code)
		{
			$productActivePrice = $this->field('active_price', array(
				'ProductPrice.currency_id' => $currencyID,
				'ProductPrice.product_id' => $productID
			));
			
			$ProductOptionStock = ClassRegistry::init('ProductOptionStock');
			$ProductOptionStock->bindPrice(null);
			
			$stocks = $ProductOptionStock->find('all', array('conditions' => array(
				'ProductOptionStock.product_id' => $productID
			)));
			
			if (empty($stocks))
			{
				return;
			}
			
			$lowest = null;
			$highest = null;
			
			foreach ($stocks as $k => $stock)
			{
				$modifier = $stock['ProductOptionStock']['modifier'];
				
				foreach ($stock['ProductOptionStockPrice'] as $k => $price)
				{
					$varPrice = $this->getVarPrice($productActivePrice, $modifier, $price['modifier_value']);
					
					if (is_null($lowest) || ($varPrice < $lowest))
					{
						$lowest = $varPrice;
					}
					
					if (is_null($highest) || ($varPrice > $highest))
					{
						$highest = $varPrice;
					}
					
				}
			}
			
			$this->Product->ProductPrice->updateAll(
				array('ProductPrice.lowest_price' => $lowest, 'ProductPrice.highest_price' => $highest),
				array('ProductPrice.currency_id' => $currencyID, 'ProductPrice.product_id' => $productID)
			);
		
		}
		
	}
	
	public function updateGroupedProductPrice($groupedProductID)
	{
		$groupedProducts = ClassRegistry::init('GroupedProduct')->find('list', array(
			'fields' => array('GroupedProduct.id', 'GroupedProduct.to_product_id'),
			'conditions' => array('GroupedProduct.from_product_id' => $groupedProductID)
		));

		$currencies = $this->Currency->find('list');
		
		foreach ($currencies as $currencyID => $code)
		{
			$active  = 0.00;
			$lowest  = 0.00;
			$highest = 0.00;
			
			foreach ($groupedProducts as $id)
			{
				$record = $this->Product->ProductPrice->find('first', array('conditions' => array(
					'ProductPrice.product_id' => $id, 
					'ProductPrice.currency_id' => $currencyID
				)));
				
				if (($record['ProductPrice']['lowest_price'] != '0.00') && ($record['ProductPrice']['lowest_price'] <> $record['ProductPrice']['highest_price']))
				{
					$lowest  += $record['ProductPrice']['lowest_price'];
					$highest += $record['ProductPrice']['highest_price'];
				}
				else
				{
					$lowest  += $record['ProductPrice']['active_price'];
					$highest += $record['ProductPrice']['active_price'];
				}	
			}
			
			if ($lowest == $highest)
			{
				$this->updateAll(
					array('ProductPrice.base_price' => $lowest, 'ProductPrice.active_price' => $lowest),
					array('ProductPrice.product_id' => $groupedProductID, 'ProductPrice.currency_id' => $currencyID)
				);	
			}
			else
			{
				$this->updateAll(
					array('ProductPrice.base_price' => $active, 'ProductPrice.lowest_price' => $lowest, 'ProductPrice.highest_price' => $highest),
					array('ProductPrice.product_id' => $groupedProductID, 'ProductPrice.currency_id' => $currencyID)
				);
			}
			
		}
		
	}
	
	/**
	 * Get price of variation from product active price, var modifier and mod value.
	 * 
	 * @param float $active
	 * @param string $modifier
	 * @param float $modValue
	 * @return float
	 * @access public
	 */
	public function getVarPrice($active, $modifier, $modValue)
	{
		if (empty($modValue))
		{
			return $active;
		}
		
		switch ($modifier)
		{
			case 'fixed':
				$price = $modValue;
				break;
			case 'add':
				$price = $active + $modValue;
				break;
			case 'subtract':
				$price = $active - $modValue;
				break;
			default:
				$price = $active;
				break;
		}
		
		return $price;
		
	}
	
	
}


