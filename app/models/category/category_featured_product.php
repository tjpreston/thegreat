<?php

/**
 * Category Featured Product
 * 
 */
class CategoryFeaturedProduct extends AppModel
{
	/**
	 * List of validation rules.
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(
		'category_id' => array(
			'rule' => array('greaterThan', 'category_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Category ID assoc. missing'
		),
		'product_id' => array(
			'rule' => array('greaterThan', 'product_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Product ID assoc. missing'
		)
	);
	
}
