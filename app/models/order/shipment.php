<?php

/**
 * Shipment
 * 
 */
class Shipment extends AppModel
{
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array('Order');
	
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
		// 'shipping_carrier_service_name' => array(
		// 	'rule' => array('minLength', 1),
		// 	'required' => true,
		// 	'allowEmpty' => false,
		// 	'message' => 'Shipping service missing'
		// ),
		'shipping_first_name' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Delivery first name missing'
		),
		'shipping_last_name' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Delivery last name missing'
		),
		'shipping_address_1' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Delivery address missing'
		),
		'shipping_town' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Delivery town missing'
		),
		'shipping_postcode' => array(
			'rule' =>  'notEmpty',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Delivery postcode missing'
		),
		'shipping_country_id' => array(
			'rule' => array('greaterThan', 'shipping_country_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Delivery country missing'
		)
	);
	
	/**
	 * Get shipments by order ID
	 * 
	 * @param int $orderID
	 * @return array
	 * @access public
	 */
	public function getShipments($orderID)
	{
		$this->Behaviors->attach('Containable');
		
		return $this->find('all', array(
			'contain' => array('ShipmentItem' => array('OrderItem')),
			'conditions' => array('Shipment.order_id' => $orderID)
		));

	}
	public function getShipment($id)
	{
		$this->Behaviors->attach('Containable');

		$record = $this->find('first', array(
			'contain' => array('Order' => array('ShippingCountry', 'BillingCountry'), 'ShipmentItem' => array('OrderItem')),
			'conditions' => array('Shipment.id' => $id)
		));

		return $record;

	}
	
	
	
}
