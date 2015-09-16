<?php

/**
 * Shipping Carrier Service Subtotal Range
 * 
 */
class ShippingCarrierServiceSubtotalRange extends AppModel
{
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array(
		'ShippingCarrierService'
	);
	
	/**
	 * Detailed list of hasMany associations.
	 *
	 * @var array
	 * @access public
	 */
	public $hasMany = array(
		'ShippingCarrierServiceSubtotalRangeCountryPrice'
	);	
	
	/**
	 * Get shipping service subtotal range.
	 *
	 * @param int $shippingServiceID
	 * @param float $subtotal
	 * @return array $range
	 * @access public
	 */
	public function getRange($shippingServiceID, $subtotal)
	{
		$ranges = $this->find('all', array(
			'conditions' => array('ShippingCarrierServiceSubtotalRange.shipping_carrier_service_id' => $shippingServiceID),
			'recursive' => -1,
			'order' => 'ShippingCarrierServiceSubtotalRange.from DESC'
		));
		
		foreach ($ranges as $r => $range)
		{
			if (!$this->subtotalInRange($subtotal, $range['ShippingCarrierServiceSubtotalRange']))
			{
				continue;
			}
			
			return $range;

		}
		
	}
	
	/**
	 * Check if subtotal in given range.
	 *
	 * @param float $subtotal
	 * @param array $range
	 * @return bool
	 * @access public
	 */
	public function subtotalInRange($subtotal, $range)
	{
		if (($range['from'] > $subtotal) || ($range['to'] < $subtotal))
		{
			return false;
		}
		
		if ($subtotal < $range['from'])
		{
			return false;
		}
		
		if ($subtotal > $range['to'])
		{
			return false;
		}
		
		return true;
		
	}
	
	
}



