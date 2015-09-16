<?php

/**
 * Wishlist Recipient Model
 * 
 */
class WishlistRecipient extends AppModel
{
	/**
	 * List of validation rules.
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(
		'wishlist_id' => array(
			'rule' => array('greaterThan', 'wishlist_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Wishlist ID missing'
		),
		'name' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Friend\'s name missing'
		),
		'email' => array(
			'rule' => 'email', 
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Friend\'s email address missing'
		)
	);
	
	
	
}
