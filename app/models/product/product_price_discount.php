<?php

/**
 * Product Price Discount
 *
 */
class ProductPriceDiscount extends AppModel
{
	/**
	 * The column name(s) and direction(s) to order find results by default.
	 *
	 * @var string
	 * @access public
	 */
	public $order = 'ProductPriceDiscount.min_qty ASC';
	
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
		'customer_group_id' => array(
			'rule' => array('greaterThan', 'customer_group_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Customer grouup ID missing'
		),
		'min_qty' => array(
			'present' => array(
				'rule' => array('greaterThan', 'min_qty', 0),
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Min qty missing'
			),
			'valid' => array(
				'rule' => array('validMinQty'),
				'message' => 'Product ID missing'
			)
		),
		'discount_amount' => array(
			'rule' => array('range', 0, 100),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Discount amount missing'
		)
	);
	
	public function validMinQty($check) 
	{
		$conditions = array(
			'ProductPriceDiscount.min_qty' => $check['min_qty'],
			'ProductPriceDiscount.product_id' => $this->data['ProductPriceDiscount']['product_id'],
			'ProductPriceDiscount.customer_group_id' => $this->data['ProductPriceDiscount']['customer_group_id'],
		);
		
		if (!empty($this->data['ProductPriceDiscount']['id']))
		{
			$conditions['ProductPriceDiscount.id !='] = $this->data['ProductPriceDiscount']['id'];
		}
		
		$duplicate = $this->find('count', array(
			'conditions' => $conditions
		));
		
		return (empty($duplicate));
		
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
		if (!empty($results))
		{
			foreach ($results as $k => $v)
			{
				if (!empty($v['ProductPriceDiscount']))
				{
					$amount = str_pad($v['ProductPriceDiscount']['discount_amount'], 2, 0, STR_PAD_LEFT);
					$results[$k]['ProductPriceDiscount']['discount_amount'] = $amount;
				}
			}
		}
		
		return $results;
	
	}
	
	public function pruneData(&$data)
	{
		$discounts = $data['ProductPriceDiscount'];
		
		$okData = array();
		
		foreach ($discounts as $k => $discount)
		{
			$this->set(array('ProductPriceDiscount' => $discount));
			$result = $this->validates();
			
			if ($result)
			{
				$okData[] = $discount;
			}
		
		}
		
		if (!empty($okData))
		{
			$data['ProductPriceDiscount'] = $okData;
		}
		else
		{
			unset($data['ProductPriceDiscount']);
		}
	
	}
	
	public function getDiscountAmount($productID, $customerGroupID, $qty)
	{
		$amount = 0;
		
		$record = $this->find('first', array(
			'conditions' => array(
				'ProductPriceDiscount.product_id' => $productID,
				'ProductPriceDiscount.customer_group_id' => $customerGroupID,
				'ProductPriceDiscount.min_qty <=' => $qty
			),
			'order' => array('ProductPriceDiscount.min_qty DESC')
		));
		
		if (!empty($record))
		{
			$amount = $record['ProductPriceDiscount']['discount_amount'];
		}
		
		return $amount;
	
	}
	
	
	
	

}

