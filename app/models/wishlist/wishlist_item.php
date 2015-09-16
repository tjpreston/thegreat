<?php

/**
 * Wishlist Item Model
 * 
 */
class WishlistItem extends AppModel
{
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array(
		'Wishlist' => array('type' => 'INNER'),
		'Product' => array('type' => 'INNER')
	);
	
	/**
	 * Detailed list of hasMany associations.
	 *
	 * @var array
	 * @access public
	 */
	public $hasMany = array('WishlistItemOptionValue');
	
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
		'product_id' => array(
			'rule' => array('validateProduct'),
			'message' => 'This product is not currently available for adding to your wishlist'
		)
	);
	
	/**
	 * Validate incoming product.
	 * Check against availability and stock control.
	 * 
	 * @return bool
	 * @access public
	 */
	public function validateProduct()
	{
		$product = $this->Product->find('first', array('conditions' => array(
			'Product.id' => $this->data[$this->name]['product_id'],
		)));
				
		if (empty($product))
		{
			return false;
		}
		
		return true;
		
	}
	
	/**
	 * Get item array of wishlist item for adding to basket.
	 * 
	 * @param array $id
	 * @return mixed
	 * @access public
	 */
	public function getBasketItemArray($id)
	{
		
		$item = $this->findById($id);
		
		if (empty($item))
		{
			return false;
		}
		
		$newBasketItem = array(
			'product_id' => $item['WishlistItem']['product_id'],
			'qty' => $item['WishlistItem']['qty']
		);
		
		if (!empty($item['WishlistItemOptionValue']))
		{
			$newBasketItem['ProductOption'] = array();
			foreach ($item['WishlistItemOptionValue'] as $optionValue)
			{
				$key = 'productoption-' . $optionValue['product_option_id'];
				$value = $optionValue['product_option_value_id'];
				$newBasketItem['ProductOption'][$key] = $value;
			}
		}
		
		return $newBasketItem;
		
	}
	
	
}
