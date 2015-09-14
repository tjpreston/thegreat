<?php

/**
 * Referral Model
 * 
 */
class Referral extends AppModel
{
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
		'sender_name' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter your name'
		),
		'sender_email' => array(
			'rule' => 'email',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter your email address'
		),
		'recipient_name' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter your friend\'s name'
		),
		'recipient_email' => array(
			'rule' => 'email',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter your friend\'s email address'
		)
	);	
	
}

