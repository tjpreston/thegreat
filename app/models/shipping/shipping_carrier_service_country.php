<?php

/**
 * Shipping Product <> Country Join
 * 
 */
class ShippingCarrierServiceCountry extends AppModel
{
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array(
		// 'DeliveryCountry', 
		'ShippingCarrierService'
	);
	
	
	
	
}
