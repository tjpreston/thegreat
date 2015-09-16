<?php

/**
 * Basket Item Option Value Model.
 * 
 */
class BasketItemOptionValue extends AppModel
{
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array(
		'ProductOption',
		'ProductOptionValue'
	);
	

	
}
