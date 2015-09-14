<?php

/**
 * Shipping Carrier Service Weight Range
 *
 */
class ShippingCarrierServiceWeightRange extends AppModel
{
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array('ShippingCarrierService');

	/**
	 * Detailed list of hasMany associations.
	 *
	 * @var array
	 * @access public
	 */
	public $hasMany = array('ShippingCarrierServiceWeightRangeCountryPrice');

	/**
	 * Called after each find operation. Can be used to modify any results returned by find().
	 * Return value should be the (modified) results.
	 *
	 * @param mixed $results The results of the find operation
	 * @param boolean $primary Whether this model is being queried directly (vs. being queried as an association)
	 * @return mixed Result of the find operation
	 * @access public
	 */
	public function afterFind($results)
	{
		if (!empty($results))
		{
			foreach ($results as $k => $result)
			{
				if (!empty($result['ShippingCarrierServiceWeightRangeCountryPrice']))
				{
					$results[$k]['Price'] = $result['ShippingCarrierServiceWeightRangeCountryPrice'];
				}
			}
		}

		return $results;

	}
	
	/**
	 * Get shipping service weight range.
	 *
	 * @param int $shippingServiceID
	 * @param float $subtotal
	 * @return array $range
	 * @access public
	 */
	public function getRange($shippingServiceID, $weight)
	{
		$ranges = $this->find('all', array(
			'conditions' => array('ShippingCarrierServiceWeightRange.shipping_carrier_service_id' => $shippingServiceID),
			'recursive' => -1,
			'order' => 'ShippingCarrierServiceWeightRange.from DESC'
		));
		
		foreach ($ranges as $r => $range)
		{
			if ($this->weightInRange($weight, $range['ShippingCarrierServiceWeightRange']))
			{
				return $range;
			}
		}
	}

	/**
	 * Check if subtotal in given range.
	 *
	 * @param float $value
	 * @param array $range
	 * @return bool
	 * @access public
	 */
	public function weightInRange($value, $range)
	{
		if (($range['from'] > $value) || ($range['to'] < $value))
		{
			return false;
		}
		
		if ($value < $range['from'])
		{
			return false;
		}
		
		if ($value > $range['to'])
		{
			return false;
		}
		
		return true;
		
	}

	/**
	 * Get available services based on passed country and weight.
	 *
	 * @param int $countryID
	 * @param float $weight
	 * @return array
	 * @access public
	 */
	public function getAvailableServices($countryID, $weight)
	{
		$services = $this->find('all', array(
			'fields' => array(
				'ShippingCarrierService.*', 'ShippingCarrier.*',
				'ShippingCarrierServiceWeightRange.*', 'ShippingCarrierServiceWeightRangeCountryPrice.*',
				'ShippingCarrierServiceCountry.*'
			),
			'joins' => array(
				array(
					'table' => 'shipping_carrier_services',
					'alias' => 'ShippingCarrierService',
					'type' => 'LEFT',
					'conditions' => array('ShippingCarrierService.id = ShippingCarrierServiceWeightRange.shipping_carrier_service_id')
				),
				array(
					'table' => 'shipping_carriers',
					'alias' => 'ShippingCarrier',
					'type' => 'LEFT',
					'conditions' => array('ShippingCarrier.id = ShippingCarrierService.shipping_carrier_id')
				),
				array(
					'table' => 'shipping_carrier_service_countries',
		            'alias' => 'ShippingCarrierServiceCountry',
		            'type' => 'INNER',
		            'conditions'=> array('ShippingCarrierServiceCountry.shipping_carrier_service_id = ShippingCarrierService.id')
				),
				array(
					'table' => 'shipping_carrier_service_weight_range_country_prices',
		            'alias' => 'ShippingCarrierServiceWeightRangeCountryPrice',
					'type' => 'LEFT',
					'conditions' => array(
						'ShippingCarrierServiceWeightRangeCountryPrice.shipping_carrier_service_weight_range_id = ShippingCarrierServiceWeightRange.id',
						'ShippingCarrierServiceWeightRangeCountryPrice.currency_id' => Configure::read('Runtime.active_currency'),
						'ShippingCarrierServiceWeightRangeCountryPrice.shipping_zone_id' => $countryID
					)
				)
			),
			'conditions' => array(
				'ShippingCarrierServiceCountry.shipping_zone_id' => $countryID,
				'ShippingCarrierServiceWeightRange.from <=' => $weight,
				'ShippingCarrierServiceWeightRange.to >=' => $weight
			),
			'recursive' => -1
		));

		$Basket = ClassRegistry::init("Basket");
		$subtotal = $Basket->getSubtotal();
		
		foreach ($services as $k => $record)
		{
			$freeThreshold = floatval($record['ShippingCarrierServiceCountry']['free_threshold_subtotal']);
			
			if (!empty($freeThreshold) && ($subtotal >= $freeThreshold))
			{
				$services[$k]['Price']['price'] = 0.0;
			}
		}
		
		return $services;
		
	}
	
	/**
	 * Get full service info.
	 *
	 * @param int $serviceID
	 * @param int $countryID
	 * @param array $conditions
	 * @return array
	 * @access public
	 */
	public function getService($serviceID, $countryID, $conditions = array())
	{
		$joins = array(array(
			'table' => 'shipping_carrier_service_countries',
			'alias' => 'ShippingCarrierServiceCountry',
			'type' => 'INNER',
			'conditions'=> array(
				'ShippingCarrierServiceCountry.shipping_carrier_service_id = ShippingCarrierService.id',
				'ShippingCarrierServiceCountry.shipping_zone_id' => $countryID
			)
		));
		
		$record = $this->ShippingCarrierService->find('first', array(
			'fields' => array('ShippingCarrierService.*', 'ShippingCarrier.*', 'ShippingCarrierServiceCountry.*'),
			'joins' => $joins,
			'conditions' => array('ShippingCarrierService.id' => $serviceID)
		));
		
		if (empty($record))
		{
			return array();
		}
		
		$Basket = ClassRegistry::init("Basket");
		
		$weight = $Basket->getWeight($conditions);
		$subtotal = $Basket->getSubtotal();

		$freeThreshold = floatval($record['ShippingCarrierServiceCountry']['free_threshold_subtotal']);

		if (!empty($freeThreshold) && ($subtotal >= $freeThreshold))
		{
			$record['Price'] = array(
				'shipping_zone_id' => $countryID,
				'currency_id' => 1,
				'price' => 0.0
			);

			return $record;

		}

		$useRangeID = 0;

		foreach ($record['ShippingCarrierServiceWeightRange'] as $k => $range)
		{
			if ($this->weightInRange($weight, $range))
			{
				$useRangeID = $range['id'];
				break;
			}
		}
		
		$price = $this->ShippingCarrierServiceWeightRangeCountryPrice->getPrice($useRangeID, $countryID);
		$record['Price'] = $price['ShippingCarrierServiceWeightRangeCountryPrice'];

		return $record;
	
	}

}


