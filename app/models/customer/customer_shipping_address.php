<?php

/**
 * Customer Shipping Address
 * 
 */
class CustomerShippingAddress extends CustomerAddress
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
			'message' => 'Please enter the delivery recipient\'s first name'
		),
		'last_name' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter the delivery recipient\'s last name'
		),
		'address_1' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter the first line of your delivery address'
		),
		'town' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter your delivery town'
		),
		'postcode' => array(
			'rule' =>  'notEmpty',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter your delivery postcode'
		),
		'country_id' => array(
			'rule' => array('greaterThan', 'country_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter your delivery country'
		),
		/*
		'telephone' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter a delivery location telephone number'
		),
		*/
		'customer_id' => array(
			'rule' => array('greaterThan', 'customer_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Customer ID missing'
		)

	);		
	
	
}

