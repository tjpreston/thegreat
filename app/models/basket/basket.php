<?php

/**
 * Basket Model
 * 
 */
class Basket extends AppModel
{
	/**
	 * List of behaviors to load when the model object is initialized.
	 *
	 * @var array
	 * @access public
	 */
	public $actsAs = array('Collection');
	
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	/*
	public $belongsTo = array(
		'Customer',
		'CustomerBillingAddress',
		'CustomerShippingAddress',
		'ShippingCarrierService',
		'DeliveryCountry' => array('foreignKey' => 'shipping_country_id'),
	);
	*/
	
	/**
	 * Detailed list of hasMany associations.
	 *
	 * @var array
	 * @access public
	 */
	public $hasMany = array('BasketItem', 'BasketAppliedDiscount');
	
	/**
	 * Users basket ID.
	 * 
	 * @var int
	 * @access private
	 */
	private $basketID;
	
	/**
	 * Users basket items.
	 * 
	 * @var array
	 * @access private
	 */
	private $items = array();
	
	/**
	 * List of validation rules.
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(
		'hash' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Session hash missing'
		)
	);
	
	/**
	 * Users basket ID.
	 * 
	 * @var int
	 * @access public
	 */
	public $id;
	
	/**
	 * Validate basket to ensure is ready to checkout.
	 * 
	 * @return bool
	 * @access public 
	 */
	public function isReadyToCheckout()
	{
		$originalValidationRules = $this->validate;
		
		$this->validate['last_calculated_total_items'] = array(
			'rule' => array('greaterThan', 'last_calculated_total_items', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'No items'
		);
		$this->validate['last_calculated_subtotal'] = array(
			'rule' => array('greaterThan', 'last_calculated_subtotal', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'No subtotal'
		);
		
		/*
		$this->validate['delivery_date'] = array(
			'rule' => array('date','ymd'),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'No date'
		);
		$this->validate['delivery_am'] = array(
			'rule' => array('boolean'),
			'message' => 'Invalid valid for checkbox'
		);
		$this->validate['shipping_country_id'] = array(
			'rule' => array('greaterThan', 'shipping_country_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'No shipping country'
		);
		*/
		$this->validate['shipping_zone_id'] = array(
			'rule' => array('greaterThan', 'shipping_zone_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'No shipping zone'
		);
		$this->validate['shipping_carrier_service_id'] = array(
			'rule' => array('greaterThan', 'shipping_carrier_service_id', 0),
			'message' => 'No Shipping Carrier Service ID'
		);
		$this->validate['last_calculated_grand_total'] = array(
			'rule' => array('greaterThan', 'last_calculated_grand_total', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'No grand total'
		);

		$this->set($this->getCollection());

		$this->set('postcode', $this->postcode);

		$validates = $this->validates();
		
		$this->validate = $originalValidationRules;
		
		return $validates;
		
	}
	
	/**
	 * Validate basket to ensure is ready for checkout confirmation
	 * 
	 * @return bool
	 * @access public
	 */
	public function isReadyToConfirmCheckout()
	{
		$originalValidationRules = $this->validate;
		
		$this->validate['customer_id'] = array(
			'rule' => array('greaterThan', 'customer_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'No customer'
		);
		$this->validate['customer_billing_address_id'] = array(
			'rule' => array('greaterThan', 'customer_billing_address_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'No customer billing address'
		);
		$this->validate['customer_shipping_address_id'] = array(
			'rule' => array('validateCustomerShippingAddress'),
			'message' => 'No customer shipping address'
		);
		
		$this->set($this->getCollection());
		$validates = $this->validates();
		
		$this->validate = $originalValidationRules;
		
		return $validates;
		
	}
	
	/**
	 * Validate customer shipping address
	 * 
	 * @return bool
	 * @access public
	 */
	public function validateCustomerShippingAddress()
	{
		return (!empty($this->data['Basket']['customer_shipping_address_id']) || !empty($this->data['Basket']['ship_to_billing_address']));
	}
	
	/**
	 * Validate basket shipping choice
	 * 
	 * @return bool
	 * @access public
	 */
	public function validateShippingChoice()
	{
		return (!empty($this->data['Basket']['shipping_carrier_service_id']) || !empty($this->data['Basket']['free_shipping']));
	}

	/**
	 * Bind full basket details.
	 *
	 * @return void
	 * @access public
	 */
	public function bindFullDetails()
	{
		$this->bindModel(array(
			'belongsTo' => array(
				'Customer',
				'CustomerBillingAddress',
				'CustomerShippingAddress',
				'ShippingCarrierService',
				'ShippingZone',
				'ShippingCountry' => array(
					'className' => 'Country',
					'foreignKey' => 'shipping_country_id'
				)
			)
			/*
			'hasAndBelongsToMany' => array(
				'BasketDiscount' => array('with' => 'BasketDiscountsBasket')
			)
			*/
		), false);

		// $this->BasketDiscount->bindPrice(0, false);

	}

	/**
	 * If basket doesn't have a billing and/or shipping address assign the default
	 * 
	 * @return void
	 * @access public
	 */
	public function assignDefaultCustomerAddressesToBasketIfNecessary()
	{
		$basket = $this->getCollection();
		
		if (empty($basket['CustomerBillingAddress']['id']) && !empty($basket['Customer']['default_billing_address_id']))
		{
			$defaultBillingAddress = $this->CustomerBillingAddress->findById($basket['Customer']['default_billing_address_id']);

			$basket['CustomerBillingAddress'] = $defaultBillingAddress['CustomerBillingAddress'];
			$basket['CustomerBillingAddressCountry'] = $defaultBillingAddress['Country'];
			
			$this->id = $basket['Basket']['id'];
			$this->saveField('customer_billing_address_id', $basket['CustomerBillingAddress']['id'], false);
		}
		
		if (empty($basket['CustomerShippingAddress']['id']) && !empty($basket['Customer']['default_shipping_address_id']))
		{
			$defaultShippingAddress = $this->CustomerShippingAddress->findById($basket['Customer']['default_shipping_address_id']);
			
			$basket['CustomerShippingAddress'] = $defaultShippingAddress['CustomerShippingAddress'];				
			$basket['CustomerShippingAddressCountry'] = $defaultShippingAddress['Country'];
			
			$this->id = $basket['Basket']['id'];
			$this->saveField('customer_shipping_address_id', $basket['CustomerShippingAddress']['id'], false);
		}
		
	}

	/**
	 * Create a new basket.
	 *
	 * @return void
	 * @access public
	 */
	public function createCollection()
	{
		if (!empty($this->id))
		{
			return $this->id;
		}
		
		$this->create();
		$this->save(array('Basket' => array(
			'hash' => Configure::read('Runtime.session_id')
		)));

		return $this->id = $this->getInsertID();
		
	}
	
	/**
	 * Get basket ID
	 *
	 * @return int
	 * @access public
	 */
	public function getCollectionID()
	{
		if (!empty($this->id))
		{
			return $this->id;
		}
		
		$this->id = $this->field('id', array('Basket.hash' => Configure::read('Runtime.session_id')));
		
		if (empty($this->id))
		{
			$this->id = $this->createCollection();
		}
		
		return $this->id;
		
	}

	/**
	 * Get basket, basket items and shipping info only (no customer).
	 *
	 * @return array
	 * @access public
	 */
	public function getCollection()
	{
		$basketID = $this->getCollectionID();
		
		$this->unbindModel(array('belongsTo' => array(
			'ShippingZone', 'ShippingCountry', 'Customer', 'CustomerBillingAddress', 'CustomerShippingAddress'
		)));
		
		$fields = array('Basket.*', 'ShippingZone.*', 'TaxRate.*');
		$joins = array(
			array(
				'table' => 'shipping_zones',
				'alias' => 'ShippingZone',
				'type' => 'LEFT',
				'conditions'=> array('ShippingZone.id = Basket.shipping_zone_id')
			),
			array(
				'table' => 'countries',
				'alias' => 'ShippingCountry',
				'type' => 'LEFT',
				'conditions'=> array('ShippingCountry.id = Basket.shipping_country_id')
			),
			array(
				'table' => 'tax_rates',
				'alias' => 'TaxRate',
				'type' => 'LEFT',
				'conditions'=> array('ShippingCountry.id = TaxRate.country_id')
			)
		);
		
		if (isset($this->ShippingCarrierService))
		{
			$extraFields[] = 'ShippingCarrierService.*';
		}
		
		/*
		if (isset($this->BasketAppliedDiscount))
		{			
			$extraFields[] = 'BasketDiscountPrice.*';
			$joins[] = array(
				'table' => 'basket_discount_prices',
				'alias' => 'BasketDiscountPrice',
				'type' => 'LEFT',
				'conditions' => array('Basket.basket_discount_id = BasketDiscountPrice.basket_discount_id')
			);
		}
		*/
		
		$basket = $this->find('first', array(
			'fields' => $fields,
			'joins' => $joins,
			'conditions' => array('Basket.id' => $basketID)
		));
		
		return $basket;
		
	}
	
	/**
	 * Get basket with full customer details.
	 *
	 * @return array
	 * @access public
	 */
	public function getBasketAndCustomer()
	{
		return $this->find('first', array(
			'fields' => array(
				'Basket.*', 'Customer.*',
				'CustomerBillingAddress.*', 'CustomerBillingAddressCountry.*',
				'CustomerShippingAddress.*', 'CustomerShippingAddressCountry.*'
			),
			'conditions' => array('Basket.id' => $this->getCollectionID()),
			'recursive' => -1,
			'joins' => array(
				array(
					'table' => 'customers',
		            'alias' => 'Customer',
		            'type' => 'LEFT',
		            'conditions'=> array('Customer.id = Basket.customer_id')
				),
				array(
					'table' => 'customer_addresses',
		            'alias' => 'CustomerBillingAddress',
		            'type' => 'LEFT',
		            'conditions'=> array('CustomerBillingAddress.id = Basket.customer_billing_address_id')
				),
				array(
					'table' => 'countries',
		            'alias' => 'CustomerBillingAddressCountry',
		            'type' => 'LEFT',
		            'conditions'=> array('CustomerBillingAddress.country_id = CustomerBillingAddressCountry.id')
				),
				array(
					'table' => 'customer_addresses',
		            'alias' => 'CustomerShippingAddress',
		            'type' => 'LEFT',
					'conditions'=> array('CustomerShippingAddress.id = Basket.customer_shipping_address_id')
				),
				array(
					'table' => 'countries',
		            'alias' => 'CustomerShippingAddressCountry',
		            'type' => 'LEFT',
		            'conditions'=> array('CustomerShippingAddress.country_id = CustomerShippingAddressCountry.id')
				)
			)
		));
	}
	
	/**
	 * Get basket subtotal.
	 * 
	 * @return float
	 * @access public
	 */
	public function getSubtotal($conditions = array())
	{
		$this->id = $this->getCollectionID();
		
		$items = $this->BasketItem->getCollectionItems($conditions);
		$subtotal = 0;
		
		foreach ($items as $k => $item)
		{
			$subtotal += ($item['BasketItem']['price'] * $item['BasketItem']['qty']);

			if (!empty($item['BasketItem']['giftwrap_product_id']))
			{
				$subtotal += ( Configure::read('Giftwrapping.price') * $item['BasketItem']['qty'] );
			}

		}
		
		return $subtotal;
		
	}

	/**
	 * Get basket subtotal tax total.
	 * 
	 * @param array $items [optional]
	 * @param float $rate [optional]
	 * @return float
	 * @access public
	 */
	public function getSubtotalTax($subtotal, $rate = 0.0)
	{
		ClassRegistry::init('TaxRate');
		
		$subtotalTax = (Configure::read('Tax.catalog_prices_include_tax')) ?
			 TaxRate::getTaxFromInclusiveAmount($subtotal, $rate) : 
			 TaxRate::getTaxFromExclusiveAmount($subtotal, $rate);
		
		return $subtotalTax;
		
	}
	
	/**
	 * Get basket shipping tax total.
	 * 
	 * @param array $shipping [optional]
	 * @param float $rate [optional]
	 * @return float
	 * @access public
	 */	
	public function getShippingTax($shipping, $rate)
	{
		$shippingTax = 0;
		
		if (!empty($shipping['ShippingCarrierService']['charge_tax']))
		{
			ClassRegistry::init('TaxRate');
			
			$shippingTax = (Configure::read('Tax.shipping_prices_include_tax')) ? 
				TaxRate::getTaxFromInclusiveAmount($shipping['Price']['price'], $rate) : 
				TaxRate::getTaxFromExclusiveAmount($shipping['Price']['price'], $rate);			
		}
		
		return $shippingTax;
		
	}

	/**
	 * Update basket shipping choices.
	 * 
	 * @TODO Add validation
	 * @param array $data
	 * @return bool
	 * @access public
	 */
	public function updateShipping($data)
	{
		$this->id = $this->getCollectionID();
		$collection = $this->getCollection();
		
		$current = array(
			'zoneID'    => $collection['Basket']['shipping_zone_id'],
			'countryID' => $collection['Basket']['shipping_country_id'],
			'serviceID' => $collection['Basket']['shipping_carrier_service_id']
		);
		
		$new = array(
			'zoneID'    => (isset($data['Basket']['shipping_zone_id'])) ? $data['Basket']['shipping_zone_id'] : 0,
			'countryID' => (isset($data['Basket']['shipping_country_id'])) ? $data['Basket']['shipping_country_id'] : 0,
			'serviceID' => (isset($data['Basket']['shipping_carrier_service_id'])) ? $data['Basket']['shipping_carrier_service_id'] : 0
		);
		
		if (!empty($zoneID) && empty($countryID))
		{
			$ShippingZone = ClassRegistry::init('ShippingZone');
			$zone = $ShippingZone->findById($zoneID);
			
			if (!empty($zone) && !empty($zone['Country']) && (count($zone['Country'] == 1)))
			{
				$new['countryID'] = $zone['Country'][0]['id'];
			}
		}
		
		if ($current == $new)
		{
			return false;
		}
		
		extract($new);

		if(empty($data['Basket']['shipping_carrier_service_id'])){
			$basketItemsCount = $this->BasketItem->getCollectionTotalQuantities();
			$subtotal = (Configure::read('Shipping.mode') == 'peritem') ? null : $this->getSubtotal();
			$availableShippingServices = $this->ShippingCarrierService->getAvailableServices($zoneID, $subtotal, $basketItemsCount);

			if(count($availableShippingServices) === 1){
				$serviceID = $availableShippingServices[0]['ShippingCarrierService']['id'];
			}
		}
		
		$this->saveField('shipping_zone_id', 0);
		$this->saveField('shipping_country_id', 0);
		$this->saveField('shipping_carrier_service_id', 0);
		
		if (!empty($zoneID))
		{
			$this->saveField('shipping_zone_id', $zoneID, false);
		}
		
		if (!empty($zoneID) && !empty($countryID))
		{
			$this->saveField('shipping_country_id', $countryID, false);
		}
		
		/*if ($current['zoneID'] <> $new['zoneID'])
		{
			$this->saveField('shipping_carrier_service_id', 0);
		}
		else if (!empty($zoneID) && !empty($serviceID))
		{	*/
			$this->saveField('shipping_carrier_service_id', $serviceID, false);
		//}
		
		return true;
		
	}

	/**
	 * Update additional options.
	 * 
	 * @TODO Add validation
	 * @param array $data
	 * @return void
	 * @access public
	 */
	public function updateAdditionalOptions($data)
	{
		$this->id = $this->getCollectionID();
		
		if (isset($data['Basket']['gift_wrap']))
		{
			$this->saveField('gift_wrap', $data['Basket']['gift_wrap'], false);

			if(!$data['Basket']['gift_wrap']){
				$this->saveField('gift_message', null, false);
			}
		}
		
		if (isset($data['Basket']['watch_sizing']))
		{
			$this->saveField('watch_sizing', $data['Basket']['watch_sizing'], false);

			if(!$data['Basket']['watch_sizing']){
				$this->saveField('wrist_size', null, false);
			}
		}

		
	}
	
	/**
	 * Save basket totals to basket record
	 *
	 * @param array $totals
	 * @return void
	 * @access private
	 */	
	public function saveTotals()
	{
		$this->id = $this->getCollectionID();
		
		$basket = $this->getCollection();
		
		$subtotal = $this->getSubtotal();
		$items = $this->BasketItem->getCollectionItems();
						
		$this->save(array('Basket' => array(
			'last_calculated_total_items' => count($items),
			'last_calculated_subtotal' => $subtotal
		)), false);

		$discount = $this->getTotalBasketDiscountAmount();

		if ($subtotal <= $discount)
		{
			$this->removeDiscountCode(false);
		}
		else
		{
			$this->saveField('last_calculated_discount_total', $discount);
		}
		
		$taxRate = $basket['TaxRate']['rate'];
		
		$subtotalTax = $this->getSubtotalTax($subtotal, $taxRate);
		$grandTotal = $subtotal - $discount;
		
		if (!Configure::read('Tax.catalog_prices_include_tax'))
		{
			$grandTotal += $subtotalTax;
		}
		
		$shipping = $this->ShippingCarrierService->getShippingInfo(
			$basket['Basket']['shipping_carrier_service_id'],
			$basket['Basket']['shipping_zone_id']
		);
		
		$shippingPrice = $basket['Basket']['shipping_price'];

		$ShippingZone = ClassRegistry::init('ShippingZone');
		$zone = $ShippingZone->findById($basket['Basket']['shipping_zone_id']);

		if($zone['ShippingZone']['deduct_tax_from_total'] == 1){
			$taxRate = Configure::read('Tax.rate');
			$taxRate = (100 + $taxRate) / 100;

			$grandTotal = $grandTotal / $taxRate;
			$this->saveField('tax_rate', 0, false);
		} else {
			$this->saveField('tax_rate', Configure::read('Tax.rate'), false);
		}

		$grandTotal += $shippingPrice;


		if (!empty($shipping['Price']['price']))
		{	
			$grandTotal += $shipping['Price']['price'];
			$shippingTax = $this->getShippingTax($shipping, $taxRate);
			
			if (!Configure::read('Tax.shipping_prices_include_tax'))
			{
				$grandTotal += $shippingTax;
			}
			
			$this->saveField('last_calculated_shipping_tax', $shippingTax, false);
			
		}
		
		//$grandTotal -= $discount;
		
		$this->save(array('Basket' => array(
			'last_calculated_grand_total'  => $grandTotal,
			'last_calculated_subtotal_tax' => $subtotalTax
		)), false);
		
	}
	
	/**
	 * Update basket with basket discount.
	 * 
	 * @param string $code
	 * @return mixed 
	 *   String (containing message) if code added/removed OK. 
	 *   False if code failed.
	 *   Null if no code provided.
	 * @access public
	 */
	public function applyDiscount($discount)
	{
		//debug($discount); exit;
		$subtotal = $this->getSubtotal();

		$isAvailable = $this->BasketAppliedDiscount->BasketDiscount->isAvailable($discount);

		if(!$isAvailable){
			return false;
		}
		
		$discountAmount = $this->BasketAppliedDiscount->BasketDiscount->getDiscountAmount($discount, $subtotal);

		if ($subtotal < $discountAmount)
		{
			return false;
		}
		
		$this->saveField('coupon_code', $discount['BasketDiscount']['coupon_code']);
		
		// Remove any existing discount.
		$this->BasketAppliedDiscount->deleteAll(array('BasketAppliedDiscount.basket_id' => $this->getCollectionID()));
		
		// Save basket <-> discount bridge records.
		$this->BasketAppliedDiscount->saveDiscounts($this->id, array($discount));
		
		return true;
		
	}

	/**
	 * Remove applied coupon and discounts.
	 *
	 * @return void
	 * @acccess public
	 */	
	public function removeDiscountCode($saveTotals = true)
	{
		$this->saveField('coupon_code', '');
		$this->saveField('last_calculated_discount_total', '0');

		$this->BasketAppliedDiscount->deleteAll(array('BasketAppliedDiscount.basket_id' => $this->getCollectionID()));

		if ($saveTotals)
		{
			$this->saveTotals();
		}
		
		
	}

	/**
	 * Verify that the discount is still valid in this basket,
	 * since some items may have been removed
	 */
	public function updateDiscount(){
		$code = $this->field('coupon_code');
		$discount = $this->BasketAppliedDiscount->BasketDiscount->getByCouponCode($code);
		$available = $this->BasketAppliedDiscount->BasketDiscount->isAvailable($discount);

		// Remove the discount voucher if it's no longer valid
		if(!$available){
			$this->removeDiscountCode();
		}
	}
	
	/**
	 * Update basket with total discount amount.
	 *
	 * @return void
	 * @access public
	 */
	public function getTotalBasketDiscountAmount()
	{
		$subtotal = $this->getSubtotal();
		$discounts = $this->BasketAppliedDiscount->getDiscounts($this->id);		
		//debug($discounts);
		
		$discountAmount = 0.00;
		
		foreach ($discounts as $discount)
		{
			$discountAmount += $this->BasketAppliedDiscount->BasketDiscount->getDiscountAmount($discount, $subtotal);
		}

		return $discountAmount;
		
	}
	
	
	


	/**
	 * Update basket gift wrapping choices.
	 *
	 * @param array $data
	 * @return mixed True if added, False is removed, Null if unaltered
	 * @access public
	 */
	/*
	public function updateGiftWrapping($data)
	{
		$this->id = $this->getCollectionID();
		
		$preUpdateGiftWrappedItemsCount = $this->BasketItem->find('count', array('conditions' => array(
			'BasketItem.giftwrap_product_id >' => 0
		)));
		
		foreach ($data['BasketItem'] as $k => $item)
		{
			$prodID = (!empty($item['giftwrap_product_id'])) ? $item['giftwrap_product_id'] : 0;
			
			$this->BasketItem->id = $item['id'];
			$this->BasketItem->saveField('giftwrap_product_id', $prodID);
		
		}
		
		$postUpdateGiftWrappedItemsCount = $this->BasketItem->find('count', array('conditions' => array(
			'BasketItem.giftwrap_product_id >' => 0
		)));

		if ($preUpdateGiftWrappedItemsCount == $postUpdateGiftWrappedItemsCount)
		{
			return null;
		}
		
		return ($preUpdateGiftWrappedItemsCount > $postUpdateGiftWrappedItemsCount);
		
	}
	*/

	public function postcode2LatLon($postcode)
	{
		$client = new SoapClient("http://www.postcoderwebsoap.co.uk/websoap/websoap.php?wsdl");
		$result = $client->getGrids($postcode, 'FDFlowersBasket', 'Flowers', 'Flowers123');
		
		if(empty($result->latitude_etrs89) || empty($result->longitude_etrs89)){
			return false;
		}
		
		$lat = $result->latitude_etrs89;
		$lon = $result->longitude_etrs89;
		
		$latlon = array('lat' => $lat, 'lon' => $lon);

		/*$latlon = array('lat' => 51.9039525, 'lon' => 0.1926641);*/
		
		return $latlon;
	}

	public function calculateDistance($lat1, $lon1, $lat2, $lon2, $unit) { 

		$theta = $lon1 - $lon2; 
		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 
		$dist = acos($dist); 
		$dist = rad2deg($dist); 
		$miles = $dist * 60 * 1.1515;
		$unit = strtoupper($unit);

		if ($unit == "K") {
			return ($miles * 1.609344); 
		} else if ($unit == "N") {
			return ($miles * 0.8684);
		} else {
			return $miles;
		}
	}

	/**
	 * Get total basket weight.
	 * 
     * @param array $conditions
	 * @return float
	 * @access public
	 */
	public function getWeight($conditions = array())
	{
		$this->id = $this->getCollectionID();
		
		$items = $this->BasketItem->getCollectionItems($conditions);
		$weight = 0.0;
		
		foreach ($items as $k => $item)
		{
			$weight += ($item['Product']['weight'] * $item['BasketItem']['qty']);
		}
		
		return $weight;

	}
	
	/**
	 * Get total subtotal / weight for finding shipping price based on saved ranges.
	 * 
	 * @return float
	 * @access public
	 */
	public function getRangeValueForShippingCalc()
	{
		return (Configure::read('Shipping.mode') == 'weight') ? $this->getWeight() : $this->getSubtotal();
	}

}



