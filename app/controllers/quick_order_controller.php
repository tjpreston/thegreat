<?php

/**
 * Quick Order Controller
 *
 */
class QuickOrderController extends AppController
{
	/**
	 * An array containing the class names of models this controller uses.
	 *
	 * @var array
	 * @access public
	 */
	public $uses = array('Product');
	
	/**
	 * Called before the controller action.
	 *
	 * @return void
	 * @access public
	 */
	function beforeFilter() 
	{
		parent::beforeFilter();
		$this->Security->disabled = true;
	}

	/**
	 * Show quick order page.
	 *
	 * @return void
	 * @access public
	 */
	public function index() {}
	
	public function ajax_find_product()
	{
		$sku = func_get_args();
		$sku = implode('/', $sku);

		if (empty($sku))
		{
			return '';
		}
		
		$this->Product->bindOptionStock(false);
		$this->Product->ProductOptionStock->bindPrice(0);
		
		$record = $this->Product->find('first', array(
			'conditions' => array('Product.sku' => $sku)
		));
		
		// If SKU of product with variations entered, exit now - base SKU no use on it's own
		if (!empty($record['ProductOptionStock']))
		{
			exit;
		}
		
		if (empty($record))
		{
			$record = $this->Product->ProductOptionStock->getStockBySku($sku);
		}

		// If no basic product or var found exit
		if (empty($record))
		{
			exit;
		}

		$this->set('product', $record);
		
	}

	public function ajax_get_product($productID, $optionID = null, $qty = 1)
	{
		$this->Product->bindOptionStock(false);
		$this->Product->ProductOptionStock->bindPrice(0);

		if (!empty($optionID))
		{
			$record = $this->Product->ProductOptionStock->getByID($optionID);

			$images = $this->Product->ProductImage->find('all', array(
				'recursive' => -1,
				'conditions' => array('ProductImage.product_id' => $productID),
				'order' => 'ProductImage.sort_order ASC',
				'limit' => 1
			));

			foreach ($images as $k => $image)
			{
				$record['ProductImage'][$k] = $image['ProductImage'];
			}

		}
		else
		{
			$record = $this->Product->findById($productID);
		}
		
		
		$customerDiscountPercentage = 0;

		$itemPrice = $record['ProductPrice']['active_price'];

		if (!empty($record['ProductOptionStock']))
		{
			$ProductOptionStockDiscount = ClassRegistry::init('ProductOptionStockDiscount');
			$itemPrice = $record['ProductOptionStockPrice']['active_price'];

			if (Configure::read('Catalog.use_tiered_customer_pricing') && Configure::read('Customer.group_id'))
			{
				$customerDiscountPercentage += $ProductOptionStockDiscount->getDiscountAmount(
					$record['ProductOptionStock']['id'], Configure::read('Customer.group_id'), $qty
				);
			}
		}
		else
		{
			if (Configure::read('Catalog.use_tiered_customer_pricing') && Configure::read('Customer.group_id'))
			{
				$ProductPriceDiscount = ClassRegistry::init('ProductPriceDiscount');
				
				$customerDiscountPercentage = $ProductPriceDiscount->getDiscountAmount(
					$record['Product']['id'], Configure::read('Customer.group_id'), $qty
				);
			}
		}
		
		if (!empty($customerDiscountPercentage))
		{
			$itemPrice = number_format($itemPrice - ($itemPrice * floatval('0.' . $customerDiscountPercentage)), 2);
		}

		$this->set('product', $record);
		$this->set('price', $itemPrice);

		$this->render('ajax_find_product');;

	}




}

