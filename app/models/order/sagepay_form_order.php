<?php

/**
 * Sagepay Form Order
 * 
 */
class SagepayFormOrder extends AppModel
{
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array('Order');
	
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
		'status' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Status missing'
		),
		'vendor_tx_code' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Vendor TX code missing'
		),
		'amount' => array(
			'rule' => array('greaterThan', 'amount', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Order ID missing'
		)
	);
	
}

