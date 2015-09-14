<?php

/**
 * Wishlist Item Option Value Model.
 * 
 */
class WishlistItemOptionValue extends AppModel
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
