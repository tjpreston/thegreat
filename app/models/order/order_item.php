<?php

/**
 * Order Item Model
 * 
 */
class OrderItem extends AppModel
{
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array(
		'Order' => array('type' => 'INNER'),
		'Product' => array('type' => 'LEFT')
	);
	
	/**
	 * Detailed list of hasMany associations.
	 *
	 * @var array
	 * @access public
	 */
	public $hasMany = array('ShipmentItem');
	
	/**
	 * List of validation rules. 
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(
		'order_id' => array(
			'rule' => array('greaterThan', 'order_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Order ID missing'
		),
		'product_id' => array(
			'valid' => array(
				'rule' => array('greaterThan', 'product_id', 0),
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Product ID missing'			
			)
		)
	);
	
	/**
	 * Called after each find operation. Can be used to modify any results returned by find().
	 * Return value should be the (modified) results.
	 *
	 * @param mixed $results The results of the find operation
	 * @param boolean $primary Whether this model is being queried directly (vs. being queried as an association)
	 * @return mixed Result of the find operation
	 * @access public
	 */
	function afterFind($results, $primary)
	{
		if (!empty($results) && !empty($results[0]))
		{
			foreach ($results as $k => $result)
			{
				if (!empty($result['OrderItem']['id']))
				{
					$records = $this->ShipmentItem->find('list', array(
						'fields' => array('ShipmentItem.id', 'ShipmentItem.qty_shipped'),
						'conditions' => array('ShipmentItem.order_item_id' => $result['OrderItem']['id'])
					));

					$qtyShipped = array_sum($records);
					
					$results[$k]['OrderItem']['qty_shipped'] = $qtyShipped;
					$results[$k]['OrderItem']['qty_to_ship'] = $result['OrderItem']['qty'] - $qtyShipped;					
					
				}
			}
		}

		return $results;
		
	}
	
	/**
	 * Write basket items to order items.
	 * 
	 * @array array $basketItems
	 * @param int $orderID
	 * @return void
	 * @access public
	 */
	public function transferBasketItemsToOrderItems($basketItems, $orderID)
	{
		if(Configure::read('Giftwrapping.enabled')){
			$this->GiftwrapProduct = ClassRegistry::init('GiftwrapProduct');
			$this->GiftwrapProduct->bindName(0);
		}

		foreach ($basketItems as $k => $item)
		{
			$itemName = trim($item['ProductName']['name']);
			
			if (!empty($item['ProductOptionStock']['name']))
			{
				$itemName .= ' (' . $item['ProductOptionStock']['name'];

				if(!empty($item['BasketItem']['additional_strap_name'])) {
					$itemName .= ', ' . $item['BasketItem']['additional_strap_name'];
				}

				$itemName .= ')';
			}
			
			$orderItem = array('OrderItem' => array(
				'order_id' 		=> $orderID,
				'product_id' 	=> $item['ProductPrice']['product_id'],
				'price' 		=> $item['BasketItem']['price'],
				'qty'			=> $item['BasketItem']['qty'],
				'product_sku'	=> $item['Product']['sku'],				
				'product_name'	=> $itemName,
			));

			if(!empty($item['BasketItem']['giftwrap_product_id'])) {
				$giftwrapProduct = $this->GiftwrapProduct->find('first', array(
					'conditions' => array('GiftwrapProduct.id' => $item['BasketItem']['giftwrap_product_id'])
				));

				if(!empty($giftwrapProduct)){
					$orderItem['OrderItem']['giftwrap_product_name'] = $giftwrapProduct['GiftwrapProductName']['name'];
					$orderItem['OrderItem']['giftwrap_price'] = (Configure::read('Giftwrapping.price') * $item['BasketItem']['qty']);
				}
				if(!empty($item['BasketItem']['custom_text'])){
					$orderItem['OrderItem']['custom_text'] = $item['BasketItem']['custom_text'];
				}
			}
			
			$this->create();
			$result = $this->save($orderItem);
			
			/*
			if ($result && Configure::read('Stock.use_stock_control'))
			{
				$currentStockQty = $this->Product->field('stock_base_qty');
				$newStockQty = ($currentStockQty - $item['BasketItem']['qty']);
				
				if ($newStockQty < 0)
				{
					$newStockQty = 0;
				}
				
				$this->Product->id = $item['Product']['id'];
				$this->Product->saveField('stock_base_qty', $newStockQty);

			}
			*/
			
		}
		
	}
	
	/**
	 * Decrease stock numbers of products / options.
	 * 
	 * @array array $basketItems
	 * @return void
	 * @access public
	 */
	public function decrementStock($basketItems)
	{
		$this->Product->bindOptionStock();
		
		foreach ($basketItems as $k => $item)
		{
			if (!empty($item['ProductOptionStock']['id']))
			{
				$this->Product->ProductOptionStock->id = $item['ProductOptionStock']['id'];
				$currentyQty = $this->Product->ProductOptionStock->field('stock_base_qty');

				$newStockQty = ($currentyQty - $item['BasketItem']['qty']);
				
				if ($newStockQty < 0)
				{
					$newStockQty = 0;
				}

				$this->Product->ProductOptionStock->saveField('stock_base_qty', $newStockQty);

			}
			else
			{
				$this->Product->id = $item['Product']['id'];
				$currentyQty = $this->Product->field('stock_base_qty');

				$newStockQty = ($currentyQty - $item['BasketItem']['qty']);
				
				if ($newStockQty < 0)
				{
					$newStockQty = 0;
				}

				$this->Product->saveField('stock_base_qty', $newStockQty);
				$this->Product->saveField('stock_in_stock', ($newStockQty > 0 ? 1 : 0));

			}
		}	
	
	}
	
}


