<?php

/**
 * Customer Billing Address
 * 
 */
class CustomerBillingAddress extends CustomerAddress
{
	/**
	 * Custom database table name, or null/false if no table association is desired.
	 *
	 * @var string
	 * @access public
	 */
	public $useTable = 'customer_addresses';
	
	/**
	 * List of validation rules.
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(
		'first_name' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter your first name as it appears on your card'
		),
		'last_name' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter your last name as it appears on your card'
		),
		'address_1' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter the first line of your billing address'
		),
		'town' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter your billing town'
		),
		'postcode' => array(
			'rule' =>  'notEmpty',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter your billing postcode'
		),
		'country_id' => array(
			'rule' => array('greaterThan', 'country_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter your billing country'
		),
		'customer_id' => array(
			'rule' => array('greaterThan', 'customer_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Customer ID missing'
		)
	);
	
}



