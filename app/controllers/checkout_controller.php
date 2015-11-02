<?php

App::import('Sanitize');

/**
 * Checkout Controller
 * 
 */
class CheckoutController extends AppController
{
	/**
	 * An array containing the class names of models this controller uses.
	 *
	 * @var array
	 * @access public
	 */
	public $uses = array(
		'Basket', 'Customer', 'CustomerBillingAddress',	'CustomerShippingAddress', 'Order'
	);

	public $helpers = array('Html', 'Form', 'PaypalIpn.Paypal');
	
	/**
	 * Array containing the names of components this controller uses.
	 *
	 * @var array
	 * @access public
	 */
	public $components = array('Email');
	
	/**
	 * Order ID.
	 * 
	 * @var int
	 * @access public
	 */
	public $orderID;
	
	/**
	 * Context of user request 
	 * (checking out as guest, checking out as registrant, editing self)
	 *
	 * @var string
	 * @access private
	 */
	private $_context;
	
        
        
        
        public  $clickCollectAddress = array('id' => '','first_name' => '','last_name' => '','address_1' => 'The Great British Shop','address_2' => '17 The Old High St','town' => 'Folkestone','country_id' => '232','county' => 'Kent','postcode' => 'CT20 1RL','company_name' => 'The Great British Shop','phone' => '01303 243366', 'new_address' => '0');
        
        
	/**
	 * Constuctor.
	 * 
	 * @return void
	 * @access public
	 */
	public function __construct()
	{
		parent::__construct();
		
		$class = Configure::read('Payments.processor');
		$this->components[] = $class;
		$this->helpers[] = $class;
	}
	
	/**
	 * Called before the controller action.
	 *
	 * @return void
	 * @access public
	 */
	function beforeFilter() 
	{
		$this->Auth->allow('*');
		parent::beforeFilter();
		
		$this->Security->validatePost = false;

		$this->Basket->bindFullDetails();
		
	}
	
	/**
	 * Show billing and delivery address input form.
	 * Also ask for password if registering.
	 *
	 * @return void
	 * @access public
	 */
	public function index($newBillingAddress = false, $newShippingAddress = false)
	{
		$this->Basket->postcode = $this->Session->read('Shipping.Location.postcode');
		$this->Basket->BasketItem->updateShippingForRestrictedProducts();
		if (!$this->Basket->isReadyToCheckout())
		{
			$this->Session->setFlash('Please complete the required fields on the basket before checking out.', 'default', array('class' => 'failure'));
			// debug($this->Basket->invalidFields());
			$this->redirect('/basket');
		}
		
		$this->_initCheckout();
		
		$this->addCrumb('/checkout', 'Checkout');
		
		$record = $this->Basket->getBasketAndCustomer();

		if (empty($this->data))
		{
			$this->data = $record;
		}
		
		$this->set('record', $record);
		
		$showShipping = (isset($record['Basket']['ship_to_billing_address']) && empty($record['Basket']['ship_to_billing_address'])) ? true : false;		
		$this->set('showShippingAddress', $showShipping);
		
		if ($this->customerIsLoggedIn())
		{
			$this->set('customer', $this->Customer->getCustomer($this->Auth->user('id')));
			$this->set('addresses', $this->Customer->CustomerAddress->getSavedAddresses($this->Auth->user('id')));
		}
		
		$this->set('countries', $this->Customer->CustomerAddress->Country->find('list'));
		
		if (Configure::read('Orders.phone_required'))
		{
			$this->Customer->addPhoneValidation();
		}
		
		// Include counties if we're in the Channel Islands or ROI.
		if($record['Basket']['shipping_zone_id'] > 1){
			$this->loadModel('StockistCounty');
			$stockistCounties = $this->StockistCounty->find('list', array(
				'conditions' => array('location_id' => $record['Basket']['shipping_zone_id']),
				'contain' => false,
			));
			$this->set(compact('stockistCounties'));
		}
	}
	
	
	/**
	 * Validate posted checkout form data.
	 * Save basket and customer record.
	 * Redirect to confirmation() is all OK. Else return to index().
	 * 
	 * @return void
	 * @access public
	 */
	public function save()
	{
            
            //xdebug_break();
            if(isset($this->data['CustomerShippingAddress']['click_collect']))
            {
                $this->data['CustomerShippingAddress'] = $this->clickCollectAddress;
                $this->data['CustomerShippingAddress']['id'] = '8';
                $this->data['CustomerShippingAddress']['first_name'] = '';
                $this->data['CustomerShippingAddress']['last_name'] = '';
                //$this->data['Basket'] = $this->viewVars['basket']['Basket'];
            }
            
            
            $this->CustomerShippingAddress->postcode = $this->Session->read('Shipping.Location.postcode');

		$this->_initCheckout();

		// Non logged in users will need to enter customer details
		if (!$this->customerIsLoggedIn() && empty($this->data['Customer']))
		{
			$this->cakeError('error404');
		}

		// Everyone needs to enter address details (logged in uses can supply ids)
		if (empty($this->data['CustomerBillingAddress']) || empty($this->data['CustomerShippingAddress']))
		{
			$this->cakeError('error404');
		}
		
		$this->Basket->id = $this->_basket['Basket']['id'];
		

		// Everyone needs to enter valid address details
		// Do this now so if we have to return early due to Customer error, address validation is displayed
		$validBillingAddress = $this->validateBillingAddressAndAppendData();
		$validShippingAddress = $this->validateShippingAddressAndAppendData();
		
		if (!empty($this->data['CustomerBillingAddress']['new_address']))
		{
			$this->Basket->saveField('customer_billing_address_id', 0);
			$this->set('newBillingAddress', true);
		}
		
		if (!empty($this->data['CustomerShippingAddress']['new_address']))
		{
			$this->Basket->saveField('customer_shipping_address_id', 0);
			$this->set('newShippingAddress', true);
		}
		
		
		
		if ($this->customerIsLoggedIn())
		{	
			$this->Basket->saveField('customer_id', $this->Auth->user('id'));
			$this->Customer->id = $this->Auth->user('id');
		}
		else
		{
			if (!empty($this->_basket['Basket']['customer_id']))
			{
				$this->Customer->id = $this->_basket['Basket']['customer_id'];
			}
			
			if ($this->_context == 'register')
			{
				$this->data['Customer']['password'] = $this->Auth->password($this->data['Customer']['password_main']);
			}
			else
			{
				$this->data['Customer']['guest'] = 1;
			}

			$customer = array('Customer' => $this->data['Customer']);
			$this->Customer->set($customer);
			
			if (!$this->Customer->validates() || !$this->Customer->save($customer))
			{
				$this->Session->setFlash('There were errors. Please check the form below.', 'default', array('class' => 'failure'));
				return $this->setAction('index');
			}

			if (empty($this->Customer->id))
			{
				$this->Customer->id = $this->Customer->getInsertID();
			}
			
			$this->Basket->saveField('customer_id', $this->Customer->id, false);

		}

		// Save a few custom basket fields
		$fields = array('newsletter_signup', 'wrist_size', 'gift_message');
		foreach($fields as $field){
			if(isset($this->data['Basket'][$field])){
				$this->Basket->saveField($field, $this->data['Basket'][$field], false);
			}
		}


		$billingOK = ($validBillingAddress && $this->saveBillingAddress());

		if (!empty($this->data['Basket']['ship_to_billing_address']))
		{
			$this->Basket->saveField('ship_to_billing_address', 1, false);
			$this->Basket->saveField('customer_shipping_address_id', 0, false);
			$this->set('shipToBillingAddress', true);
			$shippingOK = true;
		}
		else
		{
			$this->Basket->saveField('ship_to_billing_address', 0, false);
			$shippingOK = ($validShippingAddress && $this->saveShippingAddress());
		}

		if ($billingOK && $shippingOK)
		{
			$this->redirect('/checkout/support');
		}

		
		$this->set(compact('validBillingAddress', 'validShippingAddress'));
		
		$this->Session->setFlash('There were errors. Please check the form below.', 'default', array('class' => 'failure'));
		return $this->setAction('index');
		
	}

	/**
	 * Choose the stockist who will provide support
	 *
	 * @return void
	 * @access public
	 */
	public function support()
	{
		if($this->Auth->user('trade') == 1){
			$this->Basket->saveField('stockist', null);
			$this->redirect('/checkout/confirmation');
			exit;
		}


		$useCounty = false;

		$this->addCrumb('/checkout/support', 'Support');

		$origCountyID = $this->Session->read('Basket.stockist_county_id');

		if(!empty($this->data['Basket']['stockist']))
		{
			$this->Basket->saveField('stockist', $this->data['Basket']['stockist']);
			$this->redirect('/checkout/confirmation');
		} elseif(!empty($this->data) && ( empty($this->data['Basket']['stockist_county_id']) || $origCountyID == $this->data['Basket']['stockist_county_id'] )){
			$error = 'Please choose a stockist for aftersales support before continuing.';
			$this->set(compact('error'));
		}
		
		//$this->Session->delete('affiliate');
		
		// If user arrived via an affiliate link, use them as the support stockist.
		$affiliate = $this->Session->read('affiliate');
		if(!empty($affiliate))
		{
			$this->Basket->saveField('stockist', $affiliate);
			$this->redirect('/checkout/confirmation');
		}
		else 
		{
			$bc = $this->Basket->getBasketAndCustomer();
			$shippingZone = $bc['Basket']['shipping_zone_id'];

			$this->loadModel('Stockist');

			if($shippingZone == 1){
				if($bc['Basket']['ship_to_billing_address'])
				{
					$postcode = $bc['CustomerBillingAddress']['postcode'];
				}
				else 
				{
					$postcode = $bc['CustomerShippingAddress']['postcode'];
				}
				$stockists = $this->Stockist->getByPostcode($postcode);

				$this->set(compact('stockists', 'postcode'));
			} else {
				$useCounty = true;

				if(!empty($this->data['Basket']['stockist_county_id'])){
					$this->Session->write('Basket.stockist_county_id', $this->data['Basket']['stockist_county_id']);
				}

				$countyID = $this->Session->read('Basket.stockist_county_id');

				if(!empty($countyID)){
					$alias = $this->Stockist->hasAndBelongsToMany['StockistCounty']['with'];
					$dbo = $this->Stockist->getDataSource();
					$subQuery = $dbo->buildStatement(array(
						'fields' => array('`' . $alias . '`.`stockist_id`'),
						'table' => $this->Stockist->hasAndBelongsToMany['StockistCounty']['joinTable'],
						'alias' => $alias,
						'limit' => null,
						'offset' => null,
						'joins' => array(),
						'conditions' => array('`' . $alias . '`.`stockist_county_id`' => $countyID),
						'order' => null,
						'group' => null,
					), $this->Stockist);
					$subQuery = $dbo->expression(' `Stockist`.`id` IN(' . $subQuery . ') ');

					$stockists = $this->Stockist->find('all', array(
						'conditions' => array($subQuery),
					));
					
					$this->set(compact('stockists'));
				} else {
					$chooseCounty = true;
					$this->set(compact('chooseCounty'));
				}

				$counties = $this->Stockist->StockistCounty->find('list', array(
					'conditions' => array('location_id' => $shippingZone),
				));
				$this->set(compact('counties'));
			}
			
			if(empty($stockists) && !$useCounty){ // If no stockists were found... 
				$this->Basket->saveField('stockist', '0');
				$this->redirect('/checkout/confirmation');
			}

			$this->set(compact('useCounty'));
			
		}
	}
	
	/**
	 * Show order confirmation.
	 *
	 * @return void
	 * @access public
	 */
	public function confirmation()
	{
		$this->Basket->postcode = $this->Session->read('Shipping.Location.postcode');
		if (!$this->Basket->isReadyToCheckout())
		{
			$this->redirect('/basket');
		}
		
		if (!$this->Basket->isReadyToConfirmCheckout())
		{
			$this->redirect('/checkout');
		}

		$shippingInfo = $this->Basket->ShippingCarrierService->getShippingInfo(
			$this->_basket['Basket']['shipping_carrier_service_id'],
			$this->_basket['Basket']['shipping_zone_id']
		);

		$this->set('shippingInfo', $shippingInfo);
		
		$txRef = $this->Order->generateTxRef($this->_basket['Basket']['id']);
		$this->Session->write('Checkout.Order.ref', $txRef);
		$this->Session->write('Checkout.Basket.id', $this->_basket['Basket']['id']);
		$this->set('txRef', $txRef);
		$this->set('basket', $this->Basket->getBasketAndCustomer());
		$this->set('basketItems', $this->Basket->BasketItem->getCollectionItems());
		
		$this->addCrumb('/checkout/confirmation', 'Confirm Your Order');

		if (Configure::read('Giftwrapping.enabled'))
		{
			$this->loadModel('GiftwrapProduct');
			$this->GiftwrapProduct->bindName(0);
			$records = $this->GiftwrapProduct->find('all', array('conditions' => array(
				'GiftwrapProduct.available' => 1
			)));
			
			$gwp = array();

			foreach ($records as $record)
			{
				$gwid = $record['GiftwrapProduct']['id'];
				$gwp[$gwid] = $record;
			}

			$this->set('giftwrapProducts', $gwp);

		}

		$this->set('referer', $this->referer(array('action' => 'index')));
		
	}
	
	/**
	 * Submit order via pay on account method.
	 *
	 * @return void
	 * @access public
	 */
	public function pay_on_account()
	{
		$this->Basket->postcode = $this->Session->read('Shipping.Location.postcode');
		
		if (!$this->Basket->isReadyToCheckout())
		{
			$this->redirect('/basket');
		}
		
		if (!$this->Basket->isReadyToConfirmCheckout())
		{
			$this->redirect('/checkout');
		}
		
		if (!$this->Auth->user('allow_payment_by_account'))
		{
			$this->redirect('/checkout/confirmation');
		}
		
		$basketAndCustomer = $this->Basket->getBasketAndCustomer();
		
		$this->saveOrder($basketAndCustomer);
		
		$orderRef = $this->Order->generateTxRef($this->_basket['Basket']['id']);
		
		$basketItems = $this->Basket->BasketItem->getCollectionItems();
		$this->Order->OrderItem->transferBasketItemsToOrderItems($basketItems, $this->orderID);
		
		$this->_sendConfirmationEmail($orderRef, $basketAndCustomer);
		
		$this->Order->saveField('ref', $orderRef);
		$this->Order->saveField('success', 1);
		$this->Order->saveField('order_status_id', Configure::read('OrderStatuses.processing'));
		
		$this->Basket->delete($this->_basket['Basket']['id']);
		
		$this->redirect('/checkout/complete/' . $orderRef);
	
	}
		
	/**
	 * Process order callback from payment provider.
	 * 
	 * @param string $result [optional]
	 * @return void
	 * @access public
	 */
	public function callback($result = null)
	{
		if (empty($this->_basket))
		{
			$this->redirect('/');
		}
		
		$component = Configure::read('Payments.processor');
		
		if (!$this->{$component}->initCallback())
		{
			$url = Router::url(array(
				'controller' => 'checkout',
				'action' => 'complete',
				'plugin' => false,
				'admin' => false,
			), true);

			$this->set(compact('url'));
			$this->render('worldpay_meta_redirect');
			// echo 'failed'; exit;
		}
		$this->Basket->id = $this->{$component}->getBasketId();
		
		$basketAndCustomer = $this->Basket->getBasketAndCustomer();	
		
		$this->saveOrder($basketAndCustomer);
		
		$basketItems = $this->Basket->BasketItem->getCollectionItems();
		$this->Order->OrderItem->transferBasketItemsToOrderItems($basketItems, $this->orderID);
		$this->Order->OrderItem->decrementStock($basketItems);
		
		$this->{$component}->processCallback();
		
		$orderRef = $this->Order->field('ref');
		$this->Session->write('Checkout.Order.ref', $orderRef);
		// debug($this->Session->read('Checkout.Order.ref'));
		// debug($this->{$component}->transOK);
		// die;
		if ($this->{$component}->transOK) {
			$discountAmount = $this->Basket->getTotalBasketDiscountAmount();
			$this->_sendConfirmationEmail($orderRef, $basketAndCustomer, $discountAmount);
			
			$this->Order->saveField('processor', Configure::read('Payments.processor'));
			$this->Order->saveField('success', 1);
			
			$this->Basket->delete($this->Basket->id);
		}
		$url = Router::url(array(
			'controller' => 'checkout',
			'action' => 'complete',
			'plugin' => false,
			'admin' => false,
			$orderRef
		), true);

		$this->set(compact('url'));
		$this->render('worldpay_meta_redirect');
		// echo 'done'; exit;
		
	}

	/**
	 * Process order callback from PayPal payment provider.
	 * 
	 * @param string $ipn_id [optional] - if not present, assume this is a PDT callback, not IPN.
	 * @return void
	 * @access public
	 */
	public function paypal_callback($ipn_id = null)
	{
		if(!empty($ipn_id))
		{
			// This callback will be coming from IPN
			$this->loadModel('InstantPaymentNotification');
			$ipn = $this->InstantPaymentNotification->findById($ipn_id);
			$txRef = $ipn['InstantPaymentNotification']['invoice'];

			$this->Basket->id = $ipn['InstantPaymentNotification']['custom'];
			
			$transOK = ($ipn['InstantPaymentNotification']['payment_status'] == 'Completed') ? true : false;
		}
		elseif (!empty($_POST))
		{
			// This callback will be coming from PDT
			$txRef = $_POST['invoice'];
			$transOK = ($_POST['payment_status'] == 'Completed') ? true : false;

			$this->Basket->id = $_POST['custom'];
		}
		else
		{
			// This callback looks invalid
			$this->redirect('/'); // Users shouldn't be here.
		}

		$basketAndCustomer = $this->Basket->getBasketAndCustomer();	
		
		$this->saveOrder($basketAndCustomer);
		
		$basketItems = $this->Basket->BasketItem->getCollectionItems();
		$this->Order->OrderItem->transferBasketItemsToOrderItems($basketItems, $this->orderID);
		$this->Order->OrderItem->decrementStock($basketItems);
		
		//$this->{$component}->processCallback();


		$this->Order->save(array('Order' => array(
			'ref' => $txRef,
			'order_status_id' => ($transOK) ? Configure::read('OrderStatuses.processing') : Configure::read('OrderStatuses.failed')
		)), array('validate' => false));
		
		$orderRef = $this->Order->field('ref');
		
		if ($transOK)
		{
			$discountAmount = $this->Basket->getTotalBasketDiscountAmount();
			$this->_sendConfirmationEmail($orderRef, $basketAndCustomer, $discountAmount);
			
			$this->Order->saveField('processor', 'PayPal');
			$this->Order->saveField('success', 1);
			
			$this->Basket->delete($this->_basket['Basket']['id']);
		}
		
		$this->redirect('/checkout/complete/' . $orderRef);
	}
	
	/**
	 * Show order complete page. Relevant payment component will handle procssing.
	 * 
	 * @param string $result
	 * @return void
	 * @access public
	 */
	public function complete($orderRef)
	{
		$basketID = $this->Session->read('Checkout.Basket.id');
		$order = $this->Order->find('first', array(
			'conditions' => array('Order.ref' => $orderRef),
		));
		if (empty($orderRef) || empty($basketID) || empty($order) || $order['WorldpayFormOrder']['cartId'] != $basketID)
		{
			$this->redirect('/');
		}

		$this->Session->delete('Checkout.Order.ref');
		$this->Session->delete('Checkout.Basket.id');
		
		$this->Order->contain(array(
			'BillingCountry',
			'ShippingCountry',
			'Customer',
			'OrderStatus',
			'Currency',
			'WorldpayFormOrder',
			'OrderItem',
			'Shipment',
			'OrderNote',
		));
		$order = $this->Order->findByRef($orderRef);
		
		if (empty($order))
		{
			$this->redirect('/');
		}
		
		$this->set('order', $order);
		
		$this->addCrumb('/checkout/complete/' . $orderRef, 'Order Complete');
		
	}
	
	/**
	 * Save order record.
	 * 
	 * @param array $basketAndCustomer
	 * @return void
	 * @access private
	 */
	private function saveOrder($basketAndCustomer)
	{		
		$b  = $basketAndCustomer['Basket'];
		$c  = $basketAndCustomer['Customer'];
		$ba = $basketAndCustomer['CustomerBillingAddress'];
		$sa = $basketAndCustomer['CustomerShippingAddress'];
		$sa = (!empty($sa['id'])) ? $sa : $ba;
		
		if (!empty($b['shipping_carrier_service_id']))
		{
			$s = $this->Basket->ShippingCarrierService->getShippingInfo($b['shipping_carrier_service_id'], $b['shipping_zone_id']);
		}
				
		$discountAmount = $this->Basket->getTotalBasketDiscountAmount();
		
		$this->Order->create();
		
		//$taxRate = Configure::read('Tax.rate');

		$order = array('Order' => array(
			'customer_id' 					=> $b['customer_id'],
			'currency_id' 					=> Configure::read('Runtime.active_currency'),
			'subtotal' 						=> $b['last_calculated_subtotal'],			
			'tax_rate'						=> $b['tax_rate'],
			'subtotal_tax'					=> $b['last_calculated_subtotal_tax'],
			'shipping_tax'					=> $b['last_calculated_shipping_tax'],
			'grand_total' 					=> $b['last_calculated_grand_total'],
			'placed_from_ip' 				=> $_SERVER['REMOTE_ADDR'],
			'customer_first_name' 			=> $c['first_name'],
			'customer_last_name' 			=> $c['last_name'],
			'customer_email' 				=> $c['email'],
			'billing_address_1' 			=> $ba['address_1'],
			'billing_address_2' 			=> $ba['address_2'],
			'billing_town' 					=> $ba['town'],
			'billing_country_id' 			=> $ba['country_id'],
			'billing_county' 				=> $ba['county'],
			'billing_postcode'	 			=> $ba['postcode'],
			'shipping_first_name' 			=> $sa['first_name'],
			'shipping_last_name' 			=> $sa['last_name'],
			'shipping_address_1' 			=> $sa['address_1'],
			'shipping_address_2' 			=> $sa['address_2'],
			'shipping_town' 				=> $sa['town'],
			'shipping_country_id' 			=> $sa['country_id'],
			'shipping_county' 				=> $sa['county'],
			'shipping_postcode'	 			=> $sa['postcode'],
			'shipped'						=> 0,
			'coupon_code'					=> $b['coupon_code'],
			'discount_total'				=> $discountAmount,
			'delivery_date'					=> $b['delivery_date'],
			'delivery_am'					=> $b['delivery_am'],
			'shipping_price'				=> $b['shipping_price'],
			'order_note'					=> $b['order_note'],
			'gift_wrap'						=> $b['gift_wrap'],
			'gift_message'					=> $b['gift_message'],
			'watch_sizing'					=> $b['watch_sizing'],
			'wrist_size'					=> $b['wrist_size'],
		));

		//$shippingName = Configure::read('Site.name');
		if (!empty($s))
		{
			$order['Order']['shipping_cost'] = $s['Price']['price'];
			$order['Order']['shipping_carrier_service_name'] = $s['ShippingCarrier']['name'] . ' ' . $s['ShippingCarrierService']['name'];
		}
		else if (!empty($b['free_shipping']))
		{
			$order['Order']['free_shipping'] = 1;
			$order['Order']['shipping_cost'] = 0;
			$order['Order']['shipping_carrier_service_name'] = 'Free Shipping';
		}
		
		$this->Order->create();
		
		if (!$this->Order->save($order))
		{
			pr($this->Order->validationErrors);
			exit('Error saving order.');
		}

		// Increment voucher usage
		if (!empty($b['coupon_code']))
		{
			$this->Basket->BasketAppliedDiscount->BasketDiscount->query(
				"UPDATE basket_discounts SET uses = uses + 1 WHERE (coupon_code = '" . Sanitize::escape($b['coupon_code']) . "');"
			);
		}

		/* Save stockist commission details */
		/*$affiliate = $this->Session->read('affiliate');
		$type = 'basket';
		if(!empty($affiliate)) $type = 'referral';
		
		// If a stockist has been chosen, save their commission record
		if(!empty($b['stockist']) && $b['stockist'] > 0){
			$commission = array(
				'order_id' => $this->Order->id,
				'stockist_id' => $b['stockist'],
				'type' => $type
			);
			$this->Order->StockistCommission->save($commission);
		}*/

		// Subscribe user to newsletter
		if($b['newsletter_signup']){
			$this->loadModel('Newsletter');
			$this->Newsletter->subscribeCampaignMonitor($c['email']);
		}

		// Unset billing address first use
		if (!empty($ba['id']))
		{
			$this->CustomerBillingAddress->id = $ba['id'];
			$this->CustomerBillingAddress->saveField('first_use', 0);
		}
		
		// Unset shipping address first use		
		if (!empty($sa['id']))
		{
			$this->CustomerShippingAddress->id = $sa['id'];
			$this->CustomerShippingAddress->saveField('first_use', 0);
		}
		
		$this->orderID = $this->Order->getInsertID();
		
		return true;
		
	}
	
	
	/**
	 * Checkout init.
	 * 
	 * @return void
	 * @access public 
	 */
	private function _initCheckout()
	{
		$this->set('mode', null);

		/* // Force "Checkout as Guest" for all purchases
		$this->_context = 'guest';
		$this->Session->write('Checkout.mode', $this->_context);
		$this->set('mode', $this->_context);

		return true; */
		
		if ($this->customerIsLoggedIn())
		{
			return;
		}
		
		if ($this->_checkingOutAsGuest())
		{
			$this->_context = 'guest';
			$this->Session->write('Checkout.mode', $this->_context);
			$this->set('mode', $this->_context);
		}
		else if ($this->_checkingOutAsRegistrant())
		{
			$this->Customer->addUniqueRegisteredEmailValidation();
			$this->Customer->addPasswordValidation();
			
			$this->_context = 'register';
			$this->Session->write('Checkout.mode', $this->_context);
			$this->set('mode', $this->_context);
		}
		else
		{
			$this->redirect('/customers/login?ref=checkout');
		}
		
	}
	

	/**
	 * Validate billing address (either saved or inputted).
	 * Also append to $this->data as necessary.
	 * 
	 * @return bool
	 * @access private
	 */
	private function validateBillingAddressAndAppendData()
	{
		$validBillingAddress = false;
		
		if ($this->customerIsLoggedIn() && !empty($this->data['CustomerBillingAddress']['id']))
		{
			$address = $this->CustomerBillingAddress->find('first', array('conditions' => array(
				'CustomerBillingAddress.customer_id' => $this->Auth->user('id'),
				'CustomerBillingAddress.id' => $this->data['CustomerBillingAddress']['id']
			)));
			
			$validBillingAddress = !empty($address);
			
		}
		else if ($this->customerIsLoggedIn() && !empty($this->data['CustomerBillingAddress']['new_address']))
		{
			$customer = $this->Customer->findById($this->Auth->user('id'));

			$this->data['CustomerBillingAddress']['customer_id'] = $customer['Customer']['id'];
			$this->data['CustomerBillingAddress']['first_name'] = $customer['Customer']['first_name'];
			$this->data['CustomerBillingAddress']['last_name'] = $customer['Customer']['last_name'];
			
			$this->CustomerBillingAddress->set($this->data);

			$this->CustomerBillingAddress->removeCustomerIDValidation();
			$validBillingAddress = $this->CustomerBillingAddress->validates();
			$this->CustomerBillingAddress->addCustomerIDValidation();
			
		}
		else if (!$this->customerIsLoggedIn())
		{
			// $this->data['CustomerBillingAddress']['customer_id'] = $this->Customer->id;

			if (!empty($this->data['Customer']['first_name']))
			{
				$this->data['CustomerBillingAddress']['first_name'] = $this->data['Customer']['first_name'];
			}
			
			if (!empty($this->data['Customer']['last_name']))
			{
				$this->data['CustomerBillingAddress']['last_name'] = $this->data['Customer']['last_name'];
			}
			
			$this->CustomerBillingAddress->set($this->data);

			$this->CustomerBillingAddress->removeCustomerIDValidation();
			$validBillingAddress = $this->CustomerBillingAddress->validates();
			$this->CustomerBillingAddress->addCustomerIDValidation();
			
		}
		
		return $validBillingAddress;
		
	}

	/**
	 * Save billing address
	 *
	 * @return bool
	 * @access private
	 */
	private function saveBillingAddress()
	{
		if (!empty($this->data['CustomerBillingAddress']['id']))
		{
			$this->CustomerBillingAddress->id = $this->data['CustomerBillingAddress']['id'];
			$this->Basket->saveField('customer_billing_address_id', $this->CustomerBillingAddress->id, false);
			return true;
		}
		else if (!empty($this->data['CustomerBillingAddress']['new_address']))
		{
			$this->data['CustomerBillingAddress']['customer_id'] = $this->Customer->id;
			$this->data['CustomerBillingAddress']['first_use'] = 1;
			
			// $this->data['CustomerBillingAddress']['basket_id'] = $this->Basket->id;
			
			if (!$this->CustomerBillingAddress->save($this->data))
			{
				// pr($this->CustomerBillingAddress->validationErrors);
				// exit;
				return false;
			}
			
			$this->Basket->saveField('customer_billing_address_id', $this->CustomerBillingAddress->id, false);

			return true;

		}
		
		/*
		if (!empty($this->_basket['Basket']['customer_billing_address_id']))
		{
			$this->CustomerBillingAddress->id = $this->_basket['Basket']['customer_billing_address_id'];
		}
		*/
		
		// $this->CustomerBillingAddress->addCustomerIDValidation();
	
	}	



	
	/**
	 * Validate shipping address (either saved or inputted).
	 * Also append to $this->data as necessary.
	 * 
	 * @return bool 
	 * @access private
	 */
	private function validateShippingAddressAndAppendData()
	{
           
                if (!empty($this->data['Basket']['ship_to_billing_address']))
		{
			return true;
		}
		
		$validShippingAddress = false;
		
		if ($this->customerIsLoggedIn() && !empty($this->data['CustomerShippingAddress']['id']))
		{
			$address = $this->CustomerShippingAddress->find('first', array('conditions' => array(
				'CustomerShippingAddress.customer_id' => $this->Auth->user('id'),
				'CustomerShippingAddress.id' => $this->data['CustomerShippingAddress']['id']
			)));
			
			$validShippingAddress = !empty($address);
			
		}
		else if (($this->customerIsLoggedIn() && !empty($this->data['CustomerShippingAddress']['new_address'])) || !$this->customerIsLoggedIn())
		{
			$this->data['CustomerShippingAddress']['customer_id'] = $this->Customer->id;
			
			$this->CustomerShippingAddress->set($this->data);

			$this->CustomerShippingAddress->removeCustomerIDValidation();
			$validShippingAddress = $this->CustomerShippingAddress->validates();
			$this->CustomerShippingAddress->addCustomerIDValidation();
			
		}
		
		return $validShippingAddress;
		
	}
	
	/**
	 * Save shipping address
	 *
	 * @return bool
	 * @access private
	 */
	private function saveShippingAddress()
	{
		if (!empty($this->data['CustomerShippingAddress']['id']))
		{
			$this->CustomerShippingAddress->id = $this->data['CustomerShippingAddress']['id'];
			$this->Basket->saveField('customer_shipping_address_id', $this->CustomerShippingAddress->id, false);
			return true;
		}
		else if (!empty($this->data['CustomerShippingAddress']['new_address']))
		{
			$this->data['CustomerShippingAddress']['first_use'] = 1;
			$this->data['CustomerBillingAddress']['customer_id'] = $this->Customer->id;
			$this->data['CustomerShippingAddress']['customer_id'] = $this->Customer->id;
			
			if (!$this->CustomerShippingAddress->save($this->data))
			{
				return false;
			}
			
			$this->Basket->saveField('customer_shipping_address_id', $this->CustomerShippingAddress->id, false);

			return true;

		}
		
		/*
		if (!empty($this->_basket['Basket']['customer_shipping_address_id']))
		{
			$this->CustomerShippingAddress->id = $this->_basket['Basket']['customer_shipping_address_id'];
		}
		*/
		
		// $this->CustomerShippingAddress->addCustomerIDValidation();
	
	}
	
	/**
	 * Is request a checkout as guest?
	 *
	 * @return void
	 * @access private
	 */
	private function _checkingOutAsGuest()
	{
		return (
			(isset($this->params['url']['edit']) && ($this->Session->read('Checkout.mode') == 'guest')) || 
			(isset($this->params['url']['guest']) || (!empty($this->data['Customer']['mode']) && ($this->data['Customer']['mode'] == 'guest')))
		);
	}
	
	/**
	 * Is request a register and checkout?
	 *
	 * @return void
	 * @access private
	 */
	private function _checkingOutAsRegistrant()
	{
		return (
			(isset($this->params['url']['edit']) && ($this->Session->read('Checkout.mode') == 'register')) || 
			(isset($this->params['url']['register']) || (!empty($this->data['Customer']['mode']) && $this->data['Customer']['mode'] == 'register'))
		);
	}
	
	/**
	 * Send order confirmation to customer.
	 * 
	 * @param array $orderRef
	 * @return void
	 * @access private
	 */
	private function _sendConfirmationEmail($orderRef, $basketAndCustomer, $discountAmount = null)
	{
		$basket = $this->_basket;
		$customer = $basketAndCustomer['Customer'];
		$basketItems = $this->Basket->BasketItem->getCollectionItems();
		
		$shipping = $this->Basket->ShippingCarrierService->getShippingInfo(
			$basket['Basket']['shipping_carrier_service_id'],
			$basket['Basket']['shipping_zone_id']
		);

		if (Configure::read('Giftwrapping.enabled'))
		{
			$this->loadModel('GiftwrapProduct');
			$this->GiftwrapProduct->bindName(0);
			$records = $this->GiftwrapProduct->find('all', array('conditions' => array(
				'GiftwrapProduct.available' => 1
			)));
			
			$gwp = array();

			foreach ($records as $record)
			{
				$gwid = $record['GiftwrapProduct']['id'];
				$gwp[$gwid] = $record;
			}

			$this->set('giftwrapProducts', $gwp);

		}
		
		$this->initDefaultEmailSettings();
		
		$this->Email->subject = Configure::read('Site.name') . ' - Order Confirmation';
		$this->Email->template = 'customers/order_notifications/order_success';
		
		$this->set('recipient', $customer['first_name']);
		$this->set('orderRef', $orderRef);
		$this->set('basket', $basket);
		$this->set('basketAndCustomer', $basketAndCustomer);
		$this->set('shipping', $shipping);
		$this->set('basketItems', $basketItems);
		$this->set('discountAmount', $discountAmount);
		
		$this->Email->to = $customer['first_name'] . '<' . $customer['email'] . '>';
		$this->Email->send();
		
		if (Configure::read('Checkout.confirmation_to_vendor'))
		{
			$this->Email->to = Configure::read('Site.name') . '<' . Configure::read('Checkout.confirmation_to_vendor') . '>';
			$this->Email->send();
		}
		
	}

	
}


