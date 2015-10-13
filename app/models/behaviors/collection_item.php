<?php

/**
 * Collection Item Behavior
 * 
 */
class CollectionItemBehavior extends ModelBehavior
{
	/**
	 * Model Instance.
	 * 
	 * @var object
	 * @access public
	 */
	public $model = null;
	
	/**
	 * Product Instance.
	 * 
	 * @var object
	 * @access public
	 */
	public $Product;
	
	/**
	 * Setup behaviour.
	 * 
	 * @param object $Model
	 * @param array $settings
	 * @return void
	 * @access public
	 */
	public function setup(&$Model, $settings)
	{
		$this->model = $Model;
		$this->Product = $Model->Product;
	}
	
	/**
	 * Get all items in a collection
	 * 
	 * @param array $extraConditions
	 * @return array $items
	 * @access public
	 */
	public function getCollectionItems(&$Model, $extraConditions = null)
	{	
           // xdebug_break();
                extract($this->getCollectionVars());
		
		// Causes issues when changing currencies
		if (!empty($this->items))
		{
			return $this->items;
		}
		
		$id = $collection->getCollectionID();
		
		if (empty($id))
		{
			return array();
		}
		
		$languageID = 1;
		$currencyID = Configure::read('Runtime.active_currency');
		
//		$this->Product->unbindModel(array(
//			'hasMany' => array('ProductImage'),
//			'hasAndBelongsToMany' => array('Category')
//		));
		$this->Product->unbindModel(array(
			'hasMany' => array('ProductImage'),
			'hasAndBelongsToMany' => array('Category')
		));

                
		$fields = array(
			$modelName . '.id',	$modelName . '.qty',
			$modelName . '.id',	$modelName . '.product_option_stock_id',
			'ProductName.name',	'ProductMeta.url', 'ProductPrice.*',
			'Product.id', 'Product.sku', 'Product.free_shipping', 'Product.virtual_product', 'Product.taxable', 'Product.type', 'Product.weight', 'Product.courier_shipping_only'
		);
		
		if ($modelName == 'BasketItem')
		{
			$fields[] = 'BasketItem.giftwrap_product_id';
			$fields[] = 'BasketItem.custom_text';
		}
		
		$conditions = array(
			$modelName . '.' . $fieldName => intval($id),
			'ProductName.language_id' => intval($languageID),
			'ProductPrice.currency_id' => intval($currencyID)
		);
		
		if (is_int($extraConditions) && !empty($extraConditions))
		{
			$conditions = $conditions + array('BasketItem.id' => $extraConditions);
		}
		else if (is_array($extraConditions) && !empty($extraConditions))
		{
			$conditions = $conditions + $extraConditions;
		}

		$this->Product->bindOptionStock(false);
		$this->Product->ProductOptionStock->bindPrice(0);
		
		$items = $this->Product->find('all', array(
			'fields' => $fields,
			'joins' => array(
				array(
					'table' => $tableName,
					'alias' => $modelName,
					'type'  => 'INNER',
					'conditions' => array('Product.id = ' . $modelName . '.product_id')
				)
			),
			'conditions' => $conditions
		));
		
		$GroupedProduct = ClassRegistry::init('GroupedProduct');
		
		foreach ($items as $k => $item)
		{
			if ($item['Product']['type'] == 'grouped')
			{
				$groupItems = $GroupedProduct->getProducts($item['Product']['id']);
				// $groupItems = $this->getItemOptions($groupItems);
				$items[$k]['GroupedProducts'] = $groupItems;
			}
		}
		
                
                // xdebug_break();
                // this adds item variations i.e. ProductOptionStock nonsense
                $items = $this->addItemOptions($items);
                
                 
                // Again a huge hack but I'm just copying what popcorn did in
                // public function afterFind($results, $primary = false) in
                // the product.php model - TJP 6/10/15
                $images = array();
                
                foreach ($items as $k => $item)
		{                
                    if(empty($items[$k]['ProductOptionStock'])) // No variations
                    {
                        $images[$k] = $this->Product->ProductImage->getImagesForBasket($item['Product']['id']);
                        $items[$k]['Product']['main_tiny_image_path'] = $images[$k]['tiny_web_path'];
                        $items[$k]['Product']['main_thumb_image_path'] = $images[$k]['thumb_web_path'];
                        $items[$k]['Product']['main_medium_image_path'] = $images[$k]['medium_web_path'];
                        
                    }  else { // We get the right image variation for free due to the call to addItemOptions($items) above
                        $items[$k]['Product']['main_tiny_image_path'] = $items[$k]['ProductOptionStock']['main_tiny_image_path'];
                        $items[$k]['Product']['main_thumb_image_path'] = $items[$k]['ProductOptionStock']['main_thumb_image_path'];
                        $items[$k]['Product']['main_medium_image_path'] = $items[$k]['ProductOptionStock']['main_medium_image_path'];
  
                       
                    }  
                    
                  
                   
                }
                
                
                
		$items = $this->addItemPrices($items);
		
		$this->items = $items;
				
		return $items;		
		
	}

	/**
	 * Determine if basket contains any items with options.
	 *
	 * @param array $items
	 * @return bool
	 * @access private
	 */
	private function hasOptions($items)
	{
		foreach ($items as $item)
		{
			if (!empty($item['BasketItem']['product_option_stock_id']))
			{
				return true;
			}
		}
				
		return false;
		
	}
	
	/**
	 * Add item option and option price to each basket item.
	 * 
	 * @param array $items
	 * @return array
	 * @access private
	 */
	private function addItemOptions($items)
	{
		foreach ($items as $k => $item)
		{
			if (empty($item['BasketItem']['product_option_stock_id']))
			{
				continue;
			}
			
			$option = $this->Product->ProductOptionStock->getByID($item['BasketItem']['product_option_stock_id']);
			
			$items[$k]['ProductOptionStock'] = $option['ProductOptionStock'];
			$items[$k]['ProductOptionStockPrice'] = $option['ProductOptionStockPrice'];
			
		}
		
		return $items;
	
	}
	
	/**
	 * Add price to each basket item.
	 * 
	 * @param array $items
	 * @return array
	 * @access private
	 */
	private function addItemPrices($items)
	{
		extract($this->getCollectionVars());
		
		$hasOptions = $this->hasOptions($items);

		if (Configure::read('Catalog.use_tiered_customer_pricing') && Configure::read('Customer.group_id'))
		{
			$ProductPriceDiscount = ClassRegistry::init('ProductPriceDiscount');
			if ($hasOptions)
			{
				$ProductOptionStockDiscount = ClassRegistry::init('ProductOptionStockDiscount');
			}
		}
		
		foreach ($items as $k => $item)
		{
			$customerDiscountPercentage = 0;

			$itemPrice = $item['ProductPrice']['active_price'];
			
			if (!empty($item['ProductOptionStock']))
			{
				$itemPrice = $item['ProductOptionStockPrice']['active_price'];

				if (Configure::read('Catalog.use_tiered_customer_pricing') && Configure::read('Customer.group_id'))
				{
					$customerDiscountPercentage += $ProductOptionStockDiscount->getDiscountAmount(
						$item['ProductOptionStock']['id'], Configure::read('Customer.group_id'), $item['BasketItem']['qty']
					);
				}
			}
			else
			{
				if (Configure::read('Catalog.use_tiered_customer_pricing') && Configure::read('Customer.group_id'))
				{
					$customerDiscountPercentage = $ProductPriceDiscount->getDiscountAmount(
						$item['Product']['id'], Configure::read('Customer.group_id'), $item['BasketItem']['qty']
					);
				}
			}

			if (!empty($customerDiscountPercentage))
			{
				$itemPrice = number_format($itemPrice - ($itemPrice * floatval('0.' . $customerDiscountPercentage)), 2);
			}
			
			$items[$k][$modelName]['price'] = $itemPrice;
			
		}
		
		return $items;
		
	}
	
	/**
	 * Get total of unique products in collection.
	 *
	 * @TODO do we need to query the db again if we already have the baskt items?
	 * @return int
	 * @access public
	 */
	public function getCollectionItemCount(&$Model)
	{
		extract($this->getCollectionVars());
		return $Model->find('count', array('conditions' => array(
			$modelName . '.' . $fieldName => $collection->getCollectionID()
		)));
	}
	
	/**
	 * Get sum of all item quantities.
	 * 
	 * @TODO do we need to query the db again if we already have the baskt items?
	 * @return array
	 * @access public
	 */
	public function getCollectionTotalQuantities(&$Model)
	{
		extract($this->getCollectionVars());
		
		$items = $Model->find('list', array(
			'fields' => array($modelName . '.id', $modelName . '.qty'),
			'conditions' => array($modelName . '.' . $fieldName => $collection->getCollectionID())
		));
		
		return array_sum($items);
		
	}
	
	/**
	 * If item added to basket is already in basket, 
	 * this method is called to increment the qty by passed $qty.
	 * 
	 * @param int $itemID
	 * @param int $qty
	 * @return bool true
	 * @access private
	 */
	private function incrementItemQty($itemID, $qty)
	{
		extract($this->getCollectionVars());
		
		$this->model->updateAll(
			array(
				$this->model->name . '.qty' => $this->model->name . '.qty + ' . intval($qty)
			),
			array(
				$this->model->name . '.id' => intval($itemID),
				$this->model->name . '.' . $fieldName => intval($collection->getCollectionID())
			)
		);
		
		return true;
		
	}
	
	/**
	 * Front end method for adding products.
	 * Accepts array of products and passes each product to Basket::addItem()
	 * 
	 * @param array $data
	 * @return bool
	 * @access public
	 */	
	public function addItemsToCollection(&$Model, $data)
	{
		if (empty($data[0]))
		{
			return false;
		}
		
		$primaryItemResult = $this->addOneItemToCollection($data[0]);
		
		if ($primaryItemResult !== true)
		{
			return $primaryItemResult;
		}
		
		unset($data[0]);
		
		foreach ($data as $item)
		{
			$this->addOneItemToCollection($item);
		}
		
		return true;
		
	}
	
	/**
	 * Remove item from collection.
	 *
	 * @param int $itemID
	 * @param bool $dontUpdateTotals
	 * @return void
	 * @access public
	 */
	public function removeItemFromCollection(&$Model, $itemID, $dontUpdateTotals = false)
	{
		extract($this->getCollectionVars());
		
		$result = $Model->deleteAll(array(
			$Model->name . '.' . $fieldName => $collection->getCollectionID(),
			$Model->name . '.id' => $itemID
		));

	}
	
	/**
	 * If item is in collection, return it, else return false.
	 * 
	 * @param array $item
	 * @return array
	 * @access public
	 */
	private function getItemInCollection($item)
	{
		extract($this->getCollectionVars());
		
		$conditions = array(
			$this->model->name . '.' . $fieldName => $collection->getCollectionID(),
			$this->model->name . '.product_id' => $item['product_id']
		);
		
		if (!empty($item['product_option_stock_id']))
		{
			$conditions[$this->model->name . '.product_option_stock_id'] = $item['product_option_stock_id'];
		}
		
		$collectionItem = $this->model->find('first', array('conditions' => $conditions));
		
		return $collectionItem ;
		
	}
	
	/**
	 * Add one item to basket.
	 * Before adding item, check basket exists, create if not.
	 * Then check item not already in basket. If it is, increment quantity.
	 *
	 * @param array $item Item array
	 * @return bool $result Result of add operation 
	 * @access private
	 */
	private function addOneItemToCollection($data)
	{
		extract($this->getCollectionVars());
		
		$collection->createCollection();
		
		$newItem = array($this->model->name => $data);
		$newItem[$this->model->name][$fieldName] = $collection->getCollectionID();
		$newItem[$this->model->name]['product_option_stock_id'] = (!empty($data['product_option_stock_id'])) ? $data['product_option_stock_id'] : 0;
		
		$this->model->create();
		$this->model->set($newItem);
		
		if (!$this->model->validates())
		{
			return false;
		}
		
		$itemAlreadyInCollection = $this->getItemInCollection($data);
		if (!empty($itemAlreadyInCollection))
		{
			return $this->incrementItemQty($itemAlreadyInCollection[$this->model->name]['id'], $data['qty']);
		}
		
		$addItemResult = $this->model->save($newItem, array(
			'fieldList' => array($fieldName, 'product_id', 'product_option_stock_id', 'qty')
		));
		
		if (!$addItemResult)
		{
			return false;
		}
		
		$itemID = $this->model->getInsertID();
		
		return true;
		
	}
	
	/**
	 * Return model vars used by collection item methods.
	 * 
	 * @return array
	 * @access private
	 */
	private function getCollectionVars()
	{
		$vars = array();
		
		$vars['modelName'] = $this->model->name;
		$vars['tableName'] = $this->model->table;
				
		if ($vars['modelName'] == 'BasketItem') 
		{
			$vars['collection'] = $this->model->Basket;
			$vars['fieldName'] = 'basket_id';
		 	$vars['joinFieldName'] = 'basket_item_id';
		}
		else
		{
			$vars['collection'] = $this->Wishlist;
			$vars['fieldName'] = 'wishlist_id';
		 	$vars['joinFieldName'] = 'wishlist_item_id';
		}
		
		return $vars;
		
	}

	/**
	 * Update collection item quantities.
	 *
	 * @param array $data
	 * @return void
	 * @access public
	 */
	public function updateCollectionItemQuantities(&$Model, $data)
	{
		extract($this->getCollectionVars());
		
		foreach ($data as $k => $item)
		{
			$qty = intval($item['qty']);
			
			if ($qty < 1)
			{
				$this->removeItemFromCollection($Model, $item['id'], true);
				continue;
			}
			
			$Model->updateAll(
				array($Model->name . '.qty' => $qty),
				array($Model->name . '.id' => $item['id'], $Model->name . '.' . $fieldName => $collection->getCollectionID())
			);
		}
	}


}


