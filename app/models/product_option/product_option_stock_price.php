<?php

/**
 * Product Option Stock Price Model
 *  
 */
class ProductOptionStockPrice extends AppModel
{	
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array('ProductOptionStock');
	
	/**
	 * List of validation rules. 
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(
		'currency_id' => array(
			'rule' => array('greaterThan', 'currency_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Currency ID missing'
		),
		'product_option_stock_id' => array(
			'rule' => array('greaterThan', 'product_option_stock_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Product Option Stock ID missing'
		)
	);
	
}

