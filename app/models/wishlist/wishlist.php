<?php

/**
 * Wishlist Model
 * 
 */
class Wishlist extends AppModel
{
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array('Customer');
	
	/**
	 * Detailed list of hasMany associations.
	 *
	 * @var array
	 * @access public
	 */
	public $hasMany = array('WishlistItem', 'WishlistRecipient');
	
	/**
	 * Users wishlist items.
	 * 
	 * @var array
	 * @access private
	 */
	private $items = array();
	
	/**
	 * List of validation rules.
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(
		'product_id' => array(
			'customer_id' => array('greaterThan', 'customer_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Customer ID missing'
		),
	);
	
	/**
	 * Users wishlist ID.
	 * 
	 * @var int
	 * @access public
	 */
	public $id;
	
	/**
	 * Create wishlist.
	 * 
	 * @return int
	 * @access public
	 */
	public function createCollection()
	{
		if (!empty($this->id))
		{
			return $this->id;
		}
		
		$this->create();
		$this->save(array($this->name => array(
			'customer_id' => $this->customer['id']
		)));

		return $this->id = $this->getInsertID();
		
	}
	
	/**
	 * Get wishlist ID
	 *
	 * @return int
	 * @access public
	 */
	public function getCollectionID()
	{
		if (!empty($this->id))
		{
			return $this->id;
		}
				
		$this->id = $this->field('id', array('Wishlist.customer_id' => $this->customer['id']));
		
		return $this->id;
		
	}
	
	
}


