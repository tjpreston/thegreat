<?php

/**
 * Shipping Carrier Service
 * 
 */
class ShippingCarrierService extends AppModel
{
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array('ShippingCarrier');

	/**
	 * Detailed list of hasMany associations.
	 *
	 * @var array
	 * @access public
	 */
	public $hasMany = array(
		'ShippingCarrierServicePerItemPrice',
		'ShippingCarrierServiceSubtotalRange',
		'ShippingCarrierServiceWeightRange'
	);
	
	/**
	 * Get available shipping services for a given basket.
	 *
	 * @param int $countryID
	 * @return array $services
	 * @access public
	 */
	public function getAvailableServices($countryID, $value = null)
	{
		switch (Configure::read('Shipping.mode'))
		{
			case 'peritem':
				$services = $this->ShippingCarrierServicePerItemPrice->getAvailableServices($countryID);
				break;
			case 'subtotal':
				$services = array();
				break;
			case 'weight':
				$services = $this->ShippingCarrierServiceWeightRange->getAvailableServices($countryID, $value);
				break;
		}
		return $services;
		
	}
	
	/**
	 * Get [carrier => service] list of services from full services array
	 * 
	 * @param array $services
	 * @return array
	 * @access public
	 */
	public function getList($services)
	{
		$list = array();
		
		if (!empty($services))
		{
			foreach ($services as $k => $service)
			{
				$id = $service['ShippingCarrierService']['id'];
				$name = $service['ShippingCarrier']['name'] . ' ' . $service['ShippingCarrierService']['name'];
				$list[$id] = $name;
			}			
		}
		
		return $list;
		
	}
	
	/**
	 * Get full shipping info.
	 *
	 * @param int $serviceID
	 * @param int $shippingInfo
	 * @return array
	 * @access public
	 */
	public function getShippingInfo($serviceID, $countryID)
	{
		$conditions = array(
			'Product.free_shipping' => 0,
			'Product.virtual_product' => 0
		);

		switch (Configure::read('Shipping.mode'))
		{
			case 'peritem':
				$shippingInfo = $this->ShippingCarrierServicePerItemPrice->get();
				break;
			case 'subtotal':
				$shippingInfo = array();
				break;
			case 'weight':
				$shippingInfo = $this->ShippingCarrierServiceWeightRange->getService($serviceID, $countryID, $conditions);
				break;
		}
		
		return $shippingInfo;
		
	}
	
}



