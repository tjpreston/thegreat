<?php

/**
 * Product Shipping Carrier Service
 * 
 */
class ProductShippingCarrierService extends AppModel
{
	/**
	 * Save product shipping service availability.
	 * 
	 * @param int $productID
	 * @param array $data
	 * @return void
	 * @access public
	 */
	public function saveAvailability($productID, $data)
	{
		$this->deleteAll(array('product_id' => $productID));
		
		foreach ($data as $serviceID => $available)
		{
			$this->create();
			$this->save(array('ProductShippingCarrierService' => array(
				'product_id' => $productID,
				'shipping_carrier_service_id' => $serviceID,
				'available' => $available
			)));
		}		
	}	
	
	
}

