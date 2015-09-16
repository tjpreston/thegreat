<?php

/**
 * Product Option Stock Discount
 *
 */
class ProductOptionStockDiscount extends AppModel
{
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array('ProductOptionStock', 'CustomerGroup');

	/**
	 * The column name(s) and direction(s) to order find results by default.
	 *
	 * @var string
	 * @access public
	 */
	public $order = 'ProductOptionStockDiscount.min_qty ASC';
	
	/**
	 * List of validation rules.
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(
		'product_option_stock_id' => array(
			'rule' => array('greaterThan', 'product_option_stock_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Product Option ID missing'
		),
		'customer_group_id' => array(
			'rule' => array('greaterThan', 'customer_group_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Customer group ID missing'
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
			'ProductOptionStockDiscount.min_qty' => $check['min_qty'],
			'ProductOptionStockDiscount.product_option_stock_id' => $this->data['ProductOptionStockDiscount']['product_option_stock_id'],
			'ProductOptionStockDiscount.customer_group_id' => $this->data['ProductOptionStockDiscount']['customer_group_id'],
		);
		
		if (!empty($this->data['ProductOptionStockDiscount']['id']))
		{
			$conditions['ProductOptionStockDiscount.id !='] = $this->data['ProductOptionStockDiscount']['id'];
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
				if (!empty($v['ProductOptionStockDiscount']) && isset($v['ProductOptionStockDiscount']['discount_amount']))
				{
					$amount = str_pad($v['ProductOptionStockDiscount']['discount_amount'], 2, 0, STR_PAD_LEFT);
					$results[$k]['ProductOptionStockDiscount']['discount_amount'] = $amount;
				}
			}
		}
		
		return $results;
	
	}
	
	public function pruneData(&$data)
	{
		$discounts = $data['ProductOptionStockDiscount'];
		
		$okData = array();
		
		foreach ($discounts as $k => $discount)
		{
			$this->set(array('ProductOptionStockDiscount' => $discount));
			$result = $this->validates();
			
			if ($result)
			{
				$okData[] = $discount;
			}
		
		}
		
		$data['ProductOptionStockDiscount'] = $okData;
	
	}
	
	public function getDiscountAmount($productID, $customerGroupID, $qty)
	{
		$amount = 0;
		
		$record = $this->find('first', array(
			'conditions' => array(
				'ProductOptionStockDiscount.product_option_stock_id' => $productID,
				'ProductOptionStockDiscount.customer_group_id' => $customerGroupID,
				'ProductOptionStockDiscount.min_qty <=' => $qty
			),
			'order' => array('ProductOptionStockDiscount.min_qty DESC'),
			'recursive' => -1
		));
		
		if (!empty($record))
		{
			$amount = $record['ProductOptionStockDiscount']['discount_amount'];
		}
		
		return $amount;
		
	}
	
}


