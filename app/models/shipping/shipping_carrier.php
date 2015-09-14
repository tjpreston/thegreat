<?php

/**
 * Shipping Carrier
 * 
 */
class ShippingCarrier extends AppModel
{
	/**
	 * Detailed list of hasMany associations.
	 *
	 * @var array
	 * @access public
	 */
	public $hasMany = array('ShippingCarrierService');	
	
}
