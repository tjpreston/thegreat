<?php

/**
 * Shipping Product
 * 
 */
class ShippingProduct extends AppModel
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
		'ShippingProductSubtotalRange',
		'ShippingProductWeightRange'
	);	
	
	/**
	 * Detailed list of hasAndBelongsToMany associations.
	 *
	 * @var array
	 * @access public
	 */
	public $hasAndBelongsToMany = array(
		'ShippingCountry' => array(
			'joinTable' => 'shipping_product_countries',
			'with' => 'ShippingProductCountry',
			'unique' => true
		)
	);
	
	
	public function getAvailableProductsList($basket)
	{
		$basketCountry  = $basket['delivery_country_id'];
		$basketSubtotal = $basket['last_calculated_total_price'];
		
		$carriers = $this->ShippingCarrierService->ShippingCarrier->find('list');

		$availableProducts = array();
		
		$products = $this->find('all', array());
		
		foreach ($products as $k => $product)
		{
			$countryOK = false;
			$subtotalOK = false;
			
			foreach ($product['ShippingCountry'] as $country)
			{
				if ($country['id'] == $basketCountry)
				{
					$countryOK = true;
					continue;
				}				
			}
			
			if ($countryOK)
			{
				if ($product['ShippingProduct']['type'] == 'free')
				{
					$availableProducts[$product['ShippingProduct']['id']] = 'Free Shipping';
				}
				else
				{
					$availableProducts[$product['ShippingProduct']['id']] = $carriers[$product['ShippingCarrierService']['shipping_carrier_id']] . ' ' . $product['ShippingCarrierService']['name'];
				}
			}
			
		}
		
		return $availableProducts;
		
	}	
	
}



