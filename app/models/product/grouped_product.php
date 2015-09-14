<?php

/**
 * Grouped Product
 * 
 */
class GroupedProduct extends AppModel
{
	/**
	 * Custom database table name, or null/false if no table association is desired.
	 *
	 * @var string
	 * @access public
	 */
	public $useTable = 'product_grouped_products';

	/**
	 * List of behaviors to load when the model object is initialized.
	 *
	 * @var array
	 * @access public
	 */
	public $actsAs = array('LinkedProduct');
	
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array(
		'FromProduct' => array(
			'className' => 'Product',
			'foreignKey' => 'from_product_id'
		),
		'Product' => array(
			'className' => 'Product',
			'foreignKey' => 'to_product_id'
		)
	);
	
}

