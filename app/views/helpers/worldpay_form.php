<?php

/**
 * Sagepay Form Helper
 * 
 */
class WorldpayFormHelper extends AppHelper
{
	/**
	 * Sagepay Form URLs.
	 *
	 * @var array
	 * @access private
	 */
	private $_urls = array(
		'simulator' => 'https://test.sagepay.com/simulator/vspformgateway.asp',
		'test' 		=> 'https://test.sagepay.com/gateway/service/vspform-register.vsp',
		'live' 		=> 'https://live.sagepay.com/gateway/service/vspform-register.vsp'
	);
	
	/**
	 * Customers basket items
	 *
	 * @var array
	 * @access private
	 */
	private $_basketItems = array();
	
	/**
	 * Customers basket and customer details.
	 *
	 * @var array
	 * @access private
	 */
	private $_basket = array();
	
	/**
	 * Transaction ref
	 *
	 * @var string
	 * @access private
	 */
	private $_txRef;
	
	/**
	 * Get Sagepay Form URL.
	 *
	 * @return string
	 * @access public
	 */
	public function getUrl()
	{
		return $this->_urls[Configure::read('SagepayForm.mode')];
	}
	
	/**
	 * Get Sagepay vendor name.
	 *
	 * @return string
	 * @access public
	 */
	public function getVendorName()
	{
		$vendors = Configure::read('SagepayForm.vendors');
		return $vendors[Configure::read('SagepayForm.mode')];
	}
	
	/**
	 * Get Sagepay POST data.
	 *
	 * @return string
	 * @access public
	 */
	public function getPostData()
	{
		$post = 'VendorTxCode=' . $this->_txRef;
		
		$post .= "&Amount=" . number_format($this->_basket['Basket']['last_calculated_grand_total'], 2);
		$post .= "&Currency=GBP";
		
		$post .= "&Description=" . Configure::read('SagepayForm.tx_desc');
		$post .= "&SuccessURL=http://" . $_SERVER['HTTP_HOST'] . "/checkout/callback/success";
		$post .= "&FailureURL=http://" . $_SERVER['HTTP_HOST'] . "/checkout/callback/failure";
		
		$post .= "&SendEMail=" . Configure::read('SagepayForm.send_email');
		$post .= "&CustomerName=" . $this->_basket['Customer']['first_name'] . ' ' . $this->_basket['Customer']['last_name'];
		$post .= "&CustomerEMail=" . $this->_basket['Customer']['email'];
		$post .= "&VendorEMail=" . $this->_getVendorEmail();
		$post .= "&eMailMessage=" . Configure::read('SagepayForm.email_message');
		
		$post .= "&BillingFirstnames=" . $this->_basket['Customer']['first_name'];
		$post .= "&BillingSurname=" . $this->_basket['Customer']['last_name'];
		
		$b = $this->_basket['CustomerBillingAddress'];
		$bCountry = $this->_basket['CustomerBillingAddressCountry'];
		
		if (!empty($this->_basket['CustomerShippingAddress']['id']))
		{
			$d = $this->_basket['CustomerShippingAddress'];
			$dCountry = $this->_basket['CustomerShippingAddressCountry'];
		}
		else
		{
			$d = $b;
			$dCountry = $bCountry;
		}
		
		$post .= "&BillingAddress1=" . $b['address_1'];
		if (strlen($b['address_2']) > 0)
		{
			$post .= "&BillingAddress2=" . $b['address_2'];
		}
		$post .= "&BillingCity=" . $b['town'];
		$post .= "&BillingPostCode=" . $b['postcode'];
		$post .= "&BillingCountry=" . $bCountry['iso'];
		
		$deliveryFirstName = (!empty($d['first_name'])) ? $d['first_name'] : $this->_basket['Customer']['first_name'];
		$deliveryLastName  = (!empty($d['last_name'])) ? $d['last_name'] : $this->_basket['Customer']['last_name'];
		
		$post .= "&DeliveryFirstnames=" . $deliveryFirstName;
		$post .= "&DeliverySurname=" . $deliveryLastName;
		$post .= "&DeliveryAddress1=" . $d['address_1'];
		if (strlen($d['address_2']) > 0)
		{
			$post .= "&DeliveryAddress2=" . $d['address_2'];
		}
		$post .= "&DeliveryCity=" . $d['town'];
		$post .= "&DeliveryPostCode=" . $d['postcode'];
		$post .= "&DeliveryCountry=" . $dCountry['iso'];
		
		$post .= "&Basket=" . $this->_getBasket();
		
		$post .= "&AllowGiftAid=0";

		return $post;
		
	}

	/**
	 * Encrypt SagePay POST data.
	 *
	 * @return string
	 * @access public
	 */
	public function encrypt($input)
	{
		$passwords = Configure::read('SagepayForm.passwords');
		$password = $passwords[Configure::read('SagepayForm.mode')];

		return base64Encode(simpleXor($input, $password));
	}
	
	/**
	 * Set basket items.
	 *
	 * @param array $basketItems
	 * @return void
	 * @access public
	 */
	public function setBasketItems($basketItems)
	{
		$this->_basketItems = $basketItems;
	}
	
	/**
	 * Set basket.
	 *
	 * @param array $basket
	 * @return void
	 * @access public
	 */
	public function setBasket($basket)
	{
		$this->_basket = $basket;
	}
	
	/**
	 * Set tx ref.
	 *
	 * @param string $txRef
	 * @return void
	 * @access public
	 */
	public function setTxRef($txRef)
	{
		$this->_txRef = $txRef;
	}
	
	/**
	 * Get vendor email address.
	 *
	 * @return string
	 * @access private
	 */
	private function _getVendorEmail()
	{
		$vendorEmails = Configure::read('SagepayForm.emails');
		return $vendorEmails[Configure::read('SagepayForm.mode')];
	}
	
	/**
	 * Get basket items POST data.
	 *
	 * @return string
	 * @access private
	 */
	private function _getBasket()
	{
		$basket = '';
		
		foreach ($this->_basketItems as $k => $item)
		{
			$itemName = trim($item['ProductName']['name']);
			
			if (!empty($item['ProductOptionStock']))
			{
				$itemName .= ' (';
				$itemName .= $item['ProductOptionStock']['name'];
				$itemName .= ')';
			}
			
			$basket .= $itemName . ':'; 								// name
			$basket .= $item['BasketItem']['qty'] . ':'; 				// qty
			$basket .= $item['ProductPrice']['active_price'] . ':'; 	// price
			$basket .= ':'; 											// tax
			$basket .= ':'; 											// item total
			$basket .= ($item['ProductPrice']['active_price'] * $item['BasketItem']['qty']) . ':';		// line total
		}
		
		$basket = count($this->_basketItems) . ':' . substr($basket, 0, -1);
	
		return $basket;
		
	}
	
}




