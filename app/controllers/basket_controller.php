<?php

/**
 * Basket Controller
 *
 */
class BasketController extends AppController
{
	/**
	 * Array containing the names of components this controller uses.
	 *
	 * @var array
	 * @access public
	 */
	public $components = array('Security');
	
	/**
	 * Called before the controller action.
	 *
	 * @access public
	 */
	public function beforeFilter()
	{
		if (!Configure::read('Basket.members_only'))
		{
			$this->Auth->allow('*');
		}
		
		parent::beforeFilter();
		
		$this->Security->requirePost('add', 'update');
		$this->set('title_for_layout', 'Your Shopping Basket');
		
		if ($this->action == 'add' || $this->action == 'update')
		{
			$this->Security->validatePost = false;
		}
	
		$this->Basket->bindFullDetails();		

	}
	
	/**
	 * Show basket. Includes:
	 * Items, Shipping Countries, Shipping Services, Shipping Info, Coupon, Giftwrapping
	 *
	 * @access public
	 * @return void
	 */
	public function index()
	{
		if (!Configure::read('Template.products_in_minibasket'))
		{
			$this->_basketItems = $this->Basket->BasketItem->getCollectionItems();
			$this->set('basketItems', $this->_basketItems);
		}

		$this->loadModel('ShippingZone');
		$availableShippingZones = $this->ShippingZone->getList();
		/*
		if (count($availableShippingCountries) == 1)
		{
			$keys = array_keys($availableShippingCountries);
			$this->_basket['Basket']['shipping_country_id'] = $keys[0];
		}
		*/
		//$deliveryDate = $this->_basket['Basket']['delivery_date'];
		
	
	



		$zoneID = $this->_basket['Basket']['shipping_zone_id'];
		$serviceID = $this->_basket['Basket']['shipping_carrier_service_id'];
		
		$availableShippingServices = array();
		
		if (!empty($zoneID))
		{
			$rangeValue = (Configure::read('Shipping.mode') == 'peritem') ? null : $this->Basket->getRangeValueForShippingCalc();
			$availableShippingServices = $this->Basket->ShippingCarrierService->getAvailableServices($zoneID, $rangeValue);
		}
		
		$shippingInfo = $this->Basket->ShippingCarrierService->getShippingInfo($serviceID, $zoneID);
		
		// $coupon = $this->Basket->Coupon->findById($this->_basket['Basket']['coupon_id']);
		// $basketDiscounts = $this->Basket->BasketDiscountsBasket->getDiscounts($this->_basket['Basket']['id']);

		// $this->set('eligibleForFreeShipping', $this->Basket->eligibleForFreeShipping());
		$this->set(compact('availableShippingZones', 'availableShippingServices', 'shippingInfo', 'deliveryDate'));
		$this->addCrumb('/basket', 'My Bag');
		
		if (Configure::read('Giftwrapping.enabled'))
		{
			$this->loadModel('GiftwrapProduct');
			$this->GiftwrapProduct->bindName(0);

			$gwp = $this->GiftwrapProduct->find('all', array('conditions' => array(
				'GiftwrapProduct.available' => 1
			)));

			$this->set('giftwrapProducts', $gwp);

		}

		
	
	
	}
	
	/**
	 * Add item to basket.
	 *
	 * @return void
	 * @access public
	 */
	public function add()
	{
		if (!empty($this->data['Basket']['redirect_to']))
		{
			$redirectUrl = $this->data['Basket']['redirect_to'];
			unset($this->data['Basket']['redirect_to']);
		}
		else
		{
			$redirectUrl = (Configure::read('Basket.redirect_to') == 'product') ? $this->referer() : '/basket';
		}
		
		$ids = Set::extract('{n}.product_id', $this->data['Basket']);

		$productidfound = false;

		foreach ($ids as $id)
		{
			if (notEmpty(intval($id)))
			{
				$productidfound = true;
				break;
			}
		}

		if (empty($productidfound))
		{
			$this->cakeError('error404');
		}

				
		if (empty($this->data['Basket']))
		{
			$this->redirect($redirectUrl);
		}

		$this->loadModel('GroupedProduct');
		foreach($this->data['Basket'] as $k => $item){
			if($item['qty'] > 0){
				$group = $this->GroupedProduct->findByToProductId($item['product_id'], array('recursive' => false));
				if(!empty($group)){
					$this->data['Basket'][$k]['parent_product_id'] = $group['GroupedProduct']['from_product_id'];
				}
			}
		}
		
		$result = $this->Basket->BasketItem->addItemsToCollection($this->data['Basket']);
		if (!empty($result))
		{
			if (is_bool($result) && ($result === true))
			{
				$this->Basket->saveTotals();
				
				$plural = ($result <> 1) ? 's' : ''; 
				$this->Session->setFlash($result . ' item' . $plural . ' added to your bag', 'collection', array('class' => 'success'), 'collection');
				$this->redirect($redirectUrl);
			}
			else
			{
				$flash = '';
				foreach ($result as $product => $msg)
				{
					$flash .= $product . ': ' . $msg . '<br />';
				}
				$flash = substr($flash, 0, -6);

				$this->Session->setFlash($flash, 'default', array('class' => 'failure'));

			}
			
		}
		
		$this->redirect($redirectUrl);
		
	}
	
	/**
	 * Remove item.
	 *
	 * @return void
	 * @access public
	 */
	public function remove($id)
	{
		$itemCountBeforeDelete = $this->Basket->BasketItem->getCollectionItemCount();
		$result = $this->Basket->BasketItem->removeItemFromCollection($id);
		$itemCountAfterDelete = $this->Basket->BasketItem->getCollectionItemCount();
		
		if ($itemCountAfterDelete < $itemCountBeforeDelete)
		{
			$this->Session->setFlash('Item removed from your shopping basket', 'default', array('class' => 'success'));
		}
		
		$this->Basket->saveTotals();

		$this->Basket->updateDiscount();

		$this->redirect('/basket');
	
	}

	/**
	 * empty cart.
	 *
	 * @return void
	 * @access public
	 */
	public function removeall()
	{

		$id = $this->Basket->getCollectionID();
		$this->Basket->delete($id);

		$this->Session->setFlash('shopping basket has been emptied', 'default', array('class' => 'success'));
		
		
		$this->Basket->saveTotals();

		$this->redirect($this->referer());
	}
	
	/**
	 * Update basket (Quantities, Discounts, Shipping)
	 *
	 * @return void
	 * @access public
	 */
	public function update()
	{
		
		//Custom Text
		foreach ($this->data['BasketItem'] as $basketItem) {
			if(!empty($basketItem['custom_text'])){

				$this->Basket->BasketItem->id = $basketItem['id'];
				$this->Basket->BasketItem->saveField('custom_text', $basketItem['custom_text']);
			}
		}


		$discountAdded = false;
		// Coupon code
		if (!empty($this->data['Coupon']['code']) && strtolower($this->data['Coupon']['code']) !== 'enter code here')
		{
			$code = $this->data['Coupon']['code'];
			$discount = $this->Basket->BasketAppliedDiscount->BasketDiscount->getByCouponCode($code);

			//debug($discount); exit;
			
			if (empty($discount))
			{
				$this->Session->setFlash('Discount code \'' . $code . '\' could not be found', 'default', array('class' => 'failure'));
				$this->redirect('/basket');
			}

			if (!$this->Basket->applyDiscount($discount))
			{
				$this->Session->setFlash('Discount code \'' . $code . '\' could not be applied to your basket', 'default', array('class' => 'failure'));
				$this->redirect('/basket');
			}
			
			$this->Session->setFlash('Discount code \'' . $code . '\' has been applied to your basket', 'default', array('class' => 'success'));

			$discountAdded = true;

		}
		
		// Qtys
		$preUpdateBasket = $this->Basket->BasketItem->getCollectionTotalQuantities();
		$this->Basket->BasketItem->updateCollectionItemQuantities($this->data['BasketItem']);
		$postUpdateBasket = $this->Basket->BasketItem->getCollectionTotalQuantities();
		
		if ($preUpdateBasket <> $postUpdateBasket)
		{
			$this->Session->setFlash('Shopping basket quantities updated', 'default', array('class' => 'success'));
		}
		
		$shippingUpdated = $this->Basket->updateShipping($this->data);

		$this->Basket->updateAdditionalOptions($this->data);
		
		// //postcode

		// if(!empty($this->data['Shipping']['postcode']) && strtolower($this->data['Shipping']['postcode']) !== 'enter your postcode')
		// {
		// 	$postcode = $this->data['Shipping']['postcode'];
		// 	$postcodeLength = strlen($postcode);
			
		// 	if ($postcodeLength < 5)
		// 	{
		// 		$this->Session->setFlash ('Your postcode is too short');
		// 		$this->redirect('/basket');
		// 	}

			
		// 	$freddy = array('lat' => 51.875992, 'lon' => 0.277471);

		// 	$prevPostcode = $this->Session->read('Shipping.Location.postcode');
		// 	$this->Session->write('Shipping.Location.postcode', $this->data['Shipping']['postcode']);
		// 	if($prevPostcode !== $this->data['Shipping']['postcode']){
		// 		$shopperLatLon = $this->Basket->postcode2LatLon($this->data['Shipping']['postcode']);
		// 	} else {
		// 		$shopperLatLon = $this->Session->read('Shipping.Location.latLon');
		// 	}
		// 	$this->Session->write('Shipping.Location.latLon', $shopperLatLon);

		// 	$distance = $this->Basket->calculateDistance($freddy['lat'], $freddy['lon'], $shopperLatLon['lat'], $shopperLatLon['lon'], 'm');
		// 	$newDistance = floor(substr($distance, 0, 6));
			
		// 	$this->Session->write('Shipping.Location.distance', $newDistance);
			
		// 	//$this->Session->setFlash('Delivery postcode: '. $postcode);
		// }
		// if ($shippingUpdated)
		// {
		// 	$this->Session->setFlash('Postcode updated', 'default', array('class' => 'success'));
		// }
		

		
		
		
		if (!empty($this->data['Basket']['order_note']))
		{
			$order_note = $this->data['Basket']['order_note'];
			$this->Basket->savefield('order_note', $order_note);
		}
		
		$this->Basket->saveTotals();

		if(!$discountAdded){
			$this->Basket->updateDiscount();
		}

		$this->redirect('/basket');
		
	}
	
	/**
	 * Add gift wrapping to basket item.
	 *
	 * @param int $basketItemID
	 * @param int $giftwrapProductID
	 * @return void
	 * @access public
	 */
	public function add_giftwrap($basketItemID, $giftwrapProductID)
	{
		$result = $this->Basket->BasketItem->giftwrapItem($basketItemID, $giftwrapProductID);

		if ($result)
		{
			$this->loadModel('GiftwrapProduct');
			$this->GiftwrapProduct->bindName(0);
			$wrap = $this->GiftwrapProduct->findById($giftwrapProductID);
			$item = $this->Basket->BasketItem->getCollectionItems($basketItemID);
			
			$msg = 'Gift wrapping will be added to \'' . $item[0]['ProductName']['name'] . '\'';
			$this->Session->setFlash($msg, 'default', array('class' => 'success'));
		}
		else
		{
			$this->Session->setFlash('Gift wrapping could not be applied', 'default', array('class' => 'failure'));
		}

		$this->Basket->saveTotals();
		
		$this->redirect('/basket');

	}
	
	/**
	 * Remove gift wrapping from basket item.
	 *
	 * @param int $basketItemID
	 * @return void
	 * @access public
	 */
	public function remove_giftwrap($basketItemID)
	{
		$result = $this->Basket->BasketItem->removeGiftwrapFromItem($basketItemID);

		if ($result)
		{
			$this->Session->setFlash('Gift wrapping removed', 'default', array('class' => 'success'));
		}
		else
		{
			$this->Session->setFlash('Gift wrapping could not be removed', 'default', array('class' => 'failure'));
		}
		
		$this->Basket->saveTotals();

		$this->redirect('/basket');

	}

	/**
	 * Remove discount code from basket.
	 *
	 * @return void
	 * @access public
	 */
	public function removediscountcode()
	{
		$this->Basket->removeDiscountCode();

		$this->Basket->saveTotals();

		$this->Session->setFlash('Discount code removed from your basket', 'default', array('class' => 'success'));
		$this->redirect('/basket');

	}
		
}





