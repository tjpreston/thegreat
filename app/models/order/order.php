<?php

/**
 * Order
 * 
 */
class Order extends AppModel
{
	/**
	 * The column name(s) and direction(s) to order find results by default.
	 *
	 * @var string
	 * @access public
	 */
	public $order = 'Order.id DESC';
	
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array(
		'Customer',
		'OrderStatus',
		'BillingCountry' => array(
			'className' => 'Country',
			'foreignKey' => 'billing_country_id'
		),
		'ShippingCountry' => array(
			'className' => 'Country',
			'foreignKey' => 'shipping_country_id'
		),
		'Currency'
	);
	
	/**
	 * Detailed list of hasMany associations.
	 *
	 * @var array
	 * @access public
	 */
	public $hasMany = array('OrderItem', 'Shipment', 'OrderNote');

	/**
	 * Detailed list of hasOne associations.
	 *
	 * @var array
	 * @access public
	 */
	/*public $hasOne = array(
		'StockistCommission',
	);*/
	
	/**
	 * List of validation rules. 
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(
		'customer_id' => array(		
			'rule' => array('greaterThan', 'customer_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Customer ID missing'
		),
		'currency_id' => array(
			'rule' => array('greaterThan', 'currency_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Currency ID missing'
		),
		'subtotal' => array(
			'rule' => 'numeric',
			'required' => true,
			'allowEmpty' => true,
			'message' => 'Subtotal missing'
		),
		'grand_total' => array(
			'rule' => 'numeric',
			'required' => true,
			'allowEmpty' => true,
			'message' => 'Grand total missing'
		),
		'customer_first_name' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Customers\'s first name missing'
		),
		'customer_last_name' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Customers\'s last name missing'
		),
		'customer_email' => array(
			'rule' => 'email',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Customers\'s email address missing'
		),
		'billing_address_1' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Billing address 1 missing'
		),
		'billing_town' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Billing town missing'
		),
		'billing_country_id' => array(
			'rule' => array('greaterThan', 'billing_country_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Billing country missing'
		),
		'billing_postcode' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Billing postcode missing'
		)
		//	,
		// 'shipping_carrier_service_name' => array(
		// 	'rule' => array('minLength', 1),
		// 	'required' => true,
		// 	'allowEmpty' => false,
		// 	'message' => 'Shipping info missing'
		// )
	);
	
	/**
	 * Constructor.
	 * Assign order details hasOne association.
	 * 
	 * @return void
	 * @access public
	 */
	public function __construct()
	{
		$this->hasOne[] = Configure::read('Payments.processor') . 'Order';
		parent::__construct();
	}
	
	/**
	 * Get order.
	 * 
	 * @param int $id
	 * @return array
	 * @access public
	 */
	public function getOrder($id)
	{
		$this->Behaviors->attach('Containable');
		
		$contain = array('Customer', 'OrderStatus',	'Shipment',	'ShippingCountry', 'BillingCountry');
		$contain[] = Configure::read('Payments.processor') . 'Order';
		
		return $this->find('first', array(
			'conditions' => array('Order.id' => $id),
			'contain' => $contain
		));
		
	}
	
	/**
	 * Get order items.
	 * 
	 * @param int $id
	 * @return array $items
	 * @access public
	 */
	public function getItems($id)
	{
		$languageID = 1;
		
		$this->OrderItem->Product->unbindModel(array(
			'hasMany' => array('ProductImage'),
			'hasAndBelongsToMany' => array('Category')
		));
		
		$items = $this->OrderItem->find('all', array(
			'fields' => array(
				'OrderItem.*', 'Product.*', 'ProductName.*'
			),
			'conditions' => array('OrderItem.order_id' => intval($id)),
			'recursive' => -1,
			'joins' => array(
				array(
					'table' => 'products',
		            'alias' => 'Product',
		            'type' => 'LEFT',
		            'conditions'=> array(
						'Product.id = OrderItem.product_id'
					)
				),
				array(
					'table' => 'product_names',
		            'alias' => 'ProductName',
		            'type' => 'LEFT',
		            'conditions'=> array(
						'ProductName.product_id = Product.id',
						'ProductName.language_id' => intval($languageID)
					)
				)
			)
		));
		
		return $items;
		
	}
	
	/**
	 * Determine if entire order has shipped.
	 * 
	 * @param int $id
	 * @return bool
	 * @access public 
	 */
	public function hasShipped($id)
	{
		$this->unbindModel(array('belongsTo' => array(
			'DeliveryAddress', 'Customer', 'ShippingCarrierService', 'OrderStatus'
		)));
		
		$order = $this->findById($id);
		
		if (!empty($order['OrderItem']))
		{
			foreach ($order['OrderItem'] as $k => $item)
			{
				if ($item['qty_shipped'] < $item['qty'])
				{
					return false;
				}
			}
		}
		
		return true;		
		
	}
	
	/**
	 * Save order shipped field.
	 * 
	 * @param int $id
	 * @param bool $orderShipped
	 * @return bool
	 * @access public 
	 */
	public function updateShippedStatus($id, $orderShipped)
	{
		$value = ($orderShipped) ? 1 : 0;
		
		$this->Shipment->Order->id = $id;
		$this->saveField('shipped', $value, false);
		
	}

	/**
	 * Get order items, then re-index array by order item ID.
	 * 
	 * @param int $id
	 * @return array
	 * @access public 
	 */
	public function getItemsIndexedById($id)
	{
		$items = $this->getItems($id);
		
		$result = array();
		
		foreach ($items as $k => $item)
		{
			$itemID = $item['OrderItem']['id'];
			$result[$itemID] = $item;
		}
		
		return $result;
		
	}
	
	/**
	 * Get transaction reference.
	 *
	 * @param int $basketID
	 * @return string
	 * @access public
	 */
	public function generateTxRef($basketID)
	{
		return mt_rand(1000000, 9999999) . Configure::read('Orders.ref_sep') . $basketID;
	}
	
	
}


