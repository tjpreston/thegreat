<?php

/**
 * Shipping Carrier Service Weight Country Price
 *
 */
class ShippingCarrierServiceWeightRangeCountryPrice extends AppModel
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
	public function getPrice($rangeID, $countryID)
	{
		$price = $this->find('first', array(
			'conditions' => array(
				'ShippingCarrierServiceWeightRangeCountryPrice.shipping_zone_id' => $countryID,
				'ShippingCarrierServiceWeightRangeCountryPrice.shipping_carrier_service_weight_range_id' => $rangeID,
				'ShippingCarrierServiceWeightRangeCountryPrice.currency_id' => 1
			)
		));

		return $price;
		
	}

}

