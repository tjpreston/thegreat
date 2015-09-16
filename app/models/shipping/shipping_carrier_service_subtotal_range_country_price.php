<?php

/**
 * Shipping Carrier Service Subtotal Range Country Price
 * 
 */
class ShippingCarrierServiceSubtotalRangeCountryPrice extends AppModel
{
	/**
	 * Get delivery price
	 * 
	 * @TODO currency_id
	 * @param int $rangeID
	 * @param int $countryID
	 * @return array
	 * @access public
	 */
	public function getPrice($rangeID, $zoneID)
	{
		$price = $this->find('first', array(
			'conditions' => array(
				'ShippingCarrierServiceSubtotalRangeCountryPrice.shipping_zone_id' => $zoneID,
				'ShippingCarrierServiceSubtotalRangeCountryPrice.shipping_carrier_service_subtotal_range_id' => $rangeID,
				'ShippingCarrierServiceSubtotalRangeCountryPrice.currency_id' => 1
			)
		));

		return $price;
		
	}
	
	
	
	
	
	
	
	
	
}
