<?php

/**
 * Basket Item Model
 * 
 */
class BasketItem extends AppModel
{
	/**
	 * List of behaviors to load when the model object is initialized.
	 *
	 * @var array
	 * @access public
	 */
	public $actsAs = array('CollectionItem');
	
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array(
		'Basket'  => array('type' => 'INNER'),
		'Product' => array('type' => 'INNER'),
		'ProductOptionStock' => array('type' => 'LEFT'),
		'GiftwrapProduct' => array('type' => 'LEFT')
	);
	
	/**
	 * Detailed list of hasMany associations.
	 *
	 * @var array
	 * @access public
	 */
	// public $hasMany = array('BasketItemOptionValue');
	
	/**
	 * List of validation rules. 
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(		
		'basket_id' => array(
			'rule' => array('greaterThan', 'basket_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Basket ID missing.'
		),
		'product_id' => array(
			'validProduct' => array(
				'required' => true,
				'allowEmpty' => false,
				'rule' => array('validProduct'),
				'message' => 'Product not available.'
			),
			'productInStock' => array(
				'rule' => array('productInStock'),
				'message' => 'This item is not currently in stock.'
			),
			'productDeliverable' => array(
				'rule' => array('productDeliverable'),
				'message' => 'Please contact us for availability of this item. Email: <a href="mailto:customerservices@lottiemutton.co.uk">customerservices@lottiemutton.co.uk</a>'
			)
		),
		'qty' => array(
			'present' => array(
				'rule' => array('greaterThan', 'qty', 0),
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Please enter the quantity you would like to add to your basket.'
			),
			'sufficientStock' => array(
				'rule' => array('sufficientStock'),
				'message' => 'You selected a qty which is greater than than our current stock level.'
			)
		),
		'product_option_stock_id' => array(
			'rule' => array('optionSpecifiedIfRequired'),
			'message' => 'Please select your desired product options.'
		)
	);
	
	/**
	 * Check all required options and values were posted and valid.
	 * 
	 * @return bool
	 * @access public 
	 */
	public function optionSpecifiedIfRequired()
	{
		$productID = $this->data['BasketItem']['product_id'];
		$stockID = $this->data['BasketItem']['product_option_stock_id'];
		
		$this->Product->bindOptionStock(true);
		$product = $this->Product->findById($productID);
		
		return (empty($stockID) && !empty($product['ProductOptionStock'])) ? false : true;
		
	}
	
	/**
	 * Validation method
	 *
	 * @return bool
	 */
	public function validProduct()
	{
		$product = $this->Product->findById($this->data['BasketItem']['product_id']);
		return (empty($product)) ? false : true;
	}
	
	/**
	 * Validation method
	 *
	 * @return bool
	 */
	public function productInStock()
	{
		if (!Configure::read('Stock.use_stock_control'))
		{
			return true;
		}

		$product = $this->Product->find('first', array('conditions' => array(
			'Product.id' => $this->data['BasketItem']['product_id'],
			'Product.stock_in_stock' => 1
		)));
		
		return (empty($product)) ? false : true;
		
	}
	
	/**
	 * Validation method
	 *
	 * @return bool
	 */
	public function sufficientStock()
	{
		if (!Configure::read('Stock.use_stock_control'))
		{
			return true;
		}
		
		$itemConditions = $basketConditions = array(
			'BasketItem.basket_id' => $this->Basket->getCollectionID(),
			'BasketItem.product_id' => $this->data['BasketItem']['product_id']
		);

		if (!empty($this->data['BasketItem']['product_option_stock_id']))
		{
			$stockid = $this->data['BasketItem']['product_option_stock_id'];
			
			$this->Product->bindOptionStock(true);
			$var = $this->Product->ProductOptionStock->getAvailableByID($stockid);

			$qtyAvailable = $var['ProductOptionStock']['stock_base_qty'];

			$itemConditions['BasketItem.product_option_stock_id'] = $stockid;

		}
		else
		{
			$product = $this->Product->find('first', array('conditions' => array(
				'Product.id' => $this->data['BasketItem']['product_id']
			)));
			
			$qtyAvailable = $product['Product']['stock_base_qty'];

		}

		$qtyRequired = $this->data['BasketItem']['qty'];

		if ($qtyRequired > $qtyAvailable)
		{
			$msg  = 'Your required quantity of ' . $qtyRequired . ' exceeds our current stock level of ' . $qtyAvailable;
			// $msg .= '<br />Please contact us to discuss availabilty.';
			return $msg;
		}

		// If NOT updating qtys from basket
		if (empty($this->data['BasketItem']['update_qtys']))
		{
			$item = $this->find('first', array(
				'conditions' => $itemConditions
			));

			$basketQty = 0;

			if (!empty($item))
			{
				$basketQty = $item['BasketItem']['qty'];
			}


			if (($basketQty + $this->data['BasketItem']['qty']) > $qtyAvailable)
			{
				$msg  = 'You already have ' . $basketQty . ' of this item in your basket. We currently only have ' . $qtyAvailable . ' in stock';
				// $msg .= '<br />Please contact us to discuss availabilty.';
				return $msg;
			}
		}
		
		return true;
		
	}
	
	/**
	 * Validation method
	 *
	 * @return bool
	 */
	public function productDeliverable()
	{
		$product = $this->Product->find('first', array('conditions' => array(
			'Product.id' => $this->data['BasketItem']['product_id'],
			'Product.deliverable' => 1
		)));

		return (empty($product)) ? false : true;

	}
	
	/**
	 * Add gift wrapping to item.
	 *
	 * @param int $basketItemID
	 * @param int $giftwrapProductID
	 * @return bool
	 * @access public
	 */
	public function giftwrapItem($basketItemID, $giftwrapProductID)
	{
		$conditions = array(
			'Basket.id' => $this->Basket->id,
			'BasketItem.id' => $basketItemID
		);

		$this->updateAll(array('BasketItem.giftwrap_product_id' => $giftwrapProductID), $conditions);
		
		$hasGiftwrap = $this->find('count', array(
			'conditions' => $conditions + array('BasketItem.giftwrap_product_id >' => 0)
		));

		return !empty($hasGiftwrap);

	}

	/**
	 * Remove gift wrapping from item.
	 *
	 * @param int $basketItemID
	 * @return bool
	 * @access public
	 */
	public function removeGiftwrapFromItem($basketItemID)
	{
		$conditions = array(
			'Basket.id' => $this->Basket->id,
			'BasketItem.id' => $basketItemID
		);

		$this->updateAll(array('BasketItem.giftwrap_product_id' => 0), $conditions);
		
		$hasGiftwrap = $this->find('count', array(
			'conditions' => $conditions + array('BasketItem.giftwrap_product_id >' => 0)
		));

		return empty($hasGiftwrap);

	}
	public function updateShippingForRestrictedProducts() {
		$basketItems = $this->getCollectionItems();
		$basketAndCustomer = $this->Basket->getBasketAndCustomer();
		// if the basket is already using a courier we dont need to do anything
		if($basketAndCustomer['Basket']['shipping_carrier_service_id'] == Configure::read('Shipping.courier_shipping_carrier_service_id')) {
			return true;
		}
		foreach ($basketItems as $item) {
			if($item['Product']['courier_shipping_only']) {
				$courierOnly = true;
				$this->removeCurrentShippingOption();
			}
		}
		return true;
	}
	public function removeCurrentShippingOption() {
		$this->Basket->saveField('shipping_carrier_service_id', 0);
		return true;
	}

}


