<?php

/**
 * Shipping Carrier Service Per Item Price
 *
 */
class ShippingCarrierServicePerItemPrice extends AppModel
{
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array('ShippingCarrierService');
	
	/**
	 * Custom database table name, or null/false if no table association is desired.
	 *
	 * @var string
	 * @access public
	 */
	public $useTable = 'shipping_carrier_service_countries_per_item_prices';
	
	public function getAvailableServices($countryID)
	{
		$services = $this->find('all', array('conditions' => array(
			'ShippingCarrierServiceCountriesPerItemPrice.delivery_country_id' => $countryID,
			'ShippingCarrierServiceCountriesPerItemPrice.currency_id' => Configure::read('Runtime.active_currency'),
		)));
		
		return $services;
		
	}
	
	public function getPerItemBasedShippingInfo($serviceID, $countryID, $conditions)
	{
		$shippingInfo = $this->findById($serviceID);
		
		if (empty($shippingInfo))
		{
			return array();
		}
		
		$items = ClassRegistry::init("BasketItem")->getCollectionTotalQuantities();
		
		$firstItemPrice = $shippingInfo['ShippingCarrierServiceCountriesPerItemPrice']['first_item_price'];
		$additionalItemPrice = $shippingInfo['ShippingCarrierServiceCountriesPerItemPrice']['additional_item_price'];
		
		$price = 0;
	
		if (($items > 1) && !empty($firstItemPrice))
		{
			$price += $firstItemPrice;
			$items--;
		}
		
		$price += ($items * $additionalItemPrice);
		
		$shippingInfo['Price'] = array(
			'currency_id' => Configure::read('Runtime.active_currency'),
			'price' => $price
		);
		
		return $shippingInfo;
	
	}

}

