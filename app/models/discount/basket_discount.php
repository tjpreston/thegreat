<?php

/**
 * Basket Discount Model
 * 
 */
class BasketDiscount extends AppModel
{
	/**
	 * Detailed list of hasMany associations.
	 *
	 * @var array
	 * @access public
	 */
	public $hasMany = array('BasketAppliedDiscount');

	/**
	 * Used to skip beforeSave and afterSave callback methods.
	 * Useful when saving just 1 field (i.e. incrementing 'uses' field in checkout callback)
	 *
	 * @var boolean
	 * @access public
	 */
	public $skipSaveCallbacks = false;
	
	/**
	 * List of validation rules. 
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(
		'name' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter a discount name'
		),
		'modifier' => array(
			'rule' => array('inList', array('fixed', 'percentage')),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please specify a valid modifier'
		),
		'coupon_code' => array(
			array(
				'rule' => 'notEmpty',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Please enter a valid coupon code'
			),
			array(
				'rule' => 'isUnique',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This coupon code has already been used'
			)
		)
	);

	public $hasAndBelongsToMany = array(
		'Product',
		'Category',
	);

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
			$queryData['conditions']['BasketDiscount.active'] = 1;
		}
		
		return $queryData;
	}

	/**
	 * Called before each save operation, after validation. Return a non-true result
	 * to halt the save.
	 *
	 * @return boolean True if the operation should continue, false if it should abort
	 * @access public
	 */
	public function beforeSave($options = array()) 
	{
		if($this->skipSaveCallbacks){ return true; }

		// Convert date formatting so it's MySQL-compatible
		foreach (array('from', 'to') as $dir)
		{
			if (!empty($this->data['BasketDiscount']['active_' . $dir]))
			{
				$temp = explode('/', $this->data['BasketDiscount']['active_' . $dir]);
				$this->data['BasketDiscount']['active_' . $dir] = $temp[2] . '-' . $temp[1] . '-' . $temp[0];
			}
		}

		/*$today = date('Y-m-d');

		$from = $this->data['BasketDiscount']['active_from'];		
		if (!empty($from) && ($from > $today))
		{
			$this->data['BasketDiscount']['active'] = 0;
		}
		
		$to = $this->data['BasketDiscount']['active_to'];
		if (!empty($to) && ($to < $today))
		{
			$this->data['BasketDiscount']['active'] = 0;
		}

		if (!empty($this->data['BasketDiscount']['infinite_uses']))
		{
			$this->data['BasketDiscount']['use_limit'] = null;
		}*/
		
		if ($this->data['BasketDiscount']['modifier'] == 'fixed')
		{
			$currencies = $this->BasketDiscountPrice->Currency->find('list');

			foreach ($currencies as $cid => $cname)
			{
				if (empty($this->data['BasketDiscountPrice'][$cid]['modifier_value']))
				{
					$this->BasketDiscountPrice->invalidate('modifier_value');
					return false;
				}
			}

		}
		
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
		if($this->skipSaveCallbacks){ return true; }
		
		if ($this->data['BasketDiscount']['modifier'] == 'fixed')
		{
			$this->bindPrice(null, false);

			foreach ($this->data['BasketDiscountPrice'] as $price)
			{
				if (empty($price['id']))
				{
					$this->BasketDiscountPrice->create();
				}
				
				$this->BasketDiscountPrice->save(array('BasketDiscountPrice' => array(
					'id' => $price['id'],
					'basket_discount_id' => $this->id,
					'currency_id' => $price['currency_id'],
					'modifier_value' => $price['modifier_value']
				)));
				
			}
			
		}

	}
	

	public function getByCouponCode($code, $availableOnly = false)
	{
		$this->bindPrice(0);

		//$record = $this->find('first', array(
		//	'conditions' => array('BasketDiscount.coupon_code' => $code)
		//));

		$record = $this->find('first', array(
			'conditions' => array(
				'BasketDiscount.coupon_code' => $code,
				'BasketDiscount.active' => 1,
				'BasketDiscount.active_from <= CURDATE()',
				'BasketDiscount.active_to >= CURDATE()',
				'OR' => array(
					'BasketDiscount.use_limit > BasketDiscount.uses',
					'BasketDiscount.use_limit' => 0,
				),
			),
			'contain' => array(
				'BasketDiscountPrice',
				//'Product',
				//'Category',
			),
		));
		
		if ($availableOnly && !$this->isAvailable($record))
		{
			return array();
		}
		
		return $record;
		
	}
	
	/**
	 * Determine if discount code is available by checking discount code record and orders.
	 * 
	 * @param mixed $record
	 * @return bool
	 * @access public
	 */
	public function isAvailable($record)
	{
		if (is_int($record))
		{
			$record = $this->findById($id);
		}
		
		// false if inactive
		if ($record['BasketDiscount']['active'] == false)
		{
			return false;
		}

		// Check start & end dates
		$today = date('Y-m-d');

		$from = $record['BasketDiscount']['active_from'];		
		if ($from != '0000-00-00' && ($from > $today))
		{
			return false;
		}
		
		$to = $record['BasketDiscount']['active_to'];
		if ($to != '0000-00-00' && ($to < $today))
		{
			return false;
		}
		
		// Check usage limits
		if (!empty($record['BasketDiscount']['use_limit']) && $record['BasketDiscount']['uses'] >= $record['BasketDiscount']['use_limit'])
		{
			return false;
		}

		// Check value of basket
		$subtotal = $this->BasketAppliedDiscount->Basket->getSubtotal();
		if(!empty($record['BasketDiscount']['min_basket_subtotal']) && $record['BasketDiscount']['min_basket_subtotal'] > $subtotal){
			return false;
		}

		// Check number of items in basket
		$itemCount = $this->BasketAppliedDiscount->Basket->BasketItem->getCollectionTotalQuantities();
		if(!empty($record['BasketDiscount']['min_basket_items_count']) && $record['BasketDiscount']['min_basket_items_count'] > $itemCount){
			return false;
		}

		// Check if this applies to our basket
		switch ($record['BasketDiscount']['applies_to']) {
			case 'products':
				return $this->isAvailableForProducts($record);
				break;

			case 'categories':
				return $this->isAvailableForCategories($record);
				break;
			
			default:
				# code...
				break;
		}

		return true;
		
	}

	public function isAvailableForProducts($record){
		$basketID = $this->BasketAppliedDiscount->Basket->getCollectionID();

		$validProductIDs = $this->BasketDiscountsProduct->find('all', array(
			'conditions' => array('basket_discount_id' => $record['BasketDiscount']['id']),
			'fields' => array('product_id'),
			'contain' => false,
		));

		$validProductIDs = Set::extract('{n}.BasketDiscountsProduct.product_id', $validProductIDs);

		$validBasketItems = $this->BasketAppliedDiscount->Basket->BasketItem->find('count', array(
			'conditions' => array(
				'basket_id' => $basketID,
				'product_id' => $validProductIDs,
			),
			'contain' => false,
		));

		return ($validBasketItems > 0);
	}

	public function isAvailableForCategories($record){
		$basketID = intval($this->BasketAppliedDiscount->Basket->getCollectionID());

		$validCategoryIDs = $this->BasketDiscountsCategory->find('all', array(
			'conditions' => array('basket_discount_id' => $record['BasketDiscount']['id']),
			'fields' => array('category_id'),
			'contain' => false,
		));

		$validCategoryIDs = Set::extract('{n}.BasketDiscountsCategory.category_id', $validCategoryIDs);

		if(count($validCategoryIDs) < 1){
			return false;
		}

		$validCategoryIDs = implode(',', $validCategoryIDs);

		$validBasketItems = "SELECT COUNT(*) as count
							FROM basket_items, products, product_categories
							WHERE basket_items.product_id = products.id
								AND products.id = product_categories.product_id
								AND basket_items.basket_id = $basketID
								AND product_categories.category_id IN($validCategoryIDs)";

		$validBasketItems = $this->query($validBasketItems);
		$validBasketItems = $validBasketItems[0][0]['count'];

		return ($validBasketItems > 0);
	}
	
	/**
	 * Get discount amount 
	 *
	 * @param array $discount
	 * @param float $subtotal
	 * @return float
	 * @access public
	 */
	public function getDiscountAmount($discount, $subtotal)
	{
		$discountAmount = 0.0;

		if ($discount['BasketDiscount']['modifier'] == 'percentage')
		{
			$discountAmount += (($subtotal / 100) * $discount['BasketDiscount']['modifier_percentage_value']);
		}
		else
		{
			$discountAmount += $discount['BasketDiscountPrice']['modifier_value'];
		}

		return $discountAmount;

	}

	
}


