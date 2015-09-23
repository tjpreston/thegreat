<?php

/**
 * StaticPageController
 * 
 */
class StaticpagesController extends AppController
{
    /**
     * An array containing the class names of models this controller uses.
     *
     * @var array
     * @access public
     */
    // public $uses = array('StaticPage');
    
    
    public function admin_index()
       
    {
          
        $this->set('pagedata', $this->Staticpage->find('all'));
       // $tmpdata = $this->Staticpage->find('all');
      //  $this->set('pagedata','$tmpdata');
        /* 
        $id = '2';
             
            if (is_null($id))
		{
			$this->redirect('/admin/products');
		}

		$this->Product->bindName($this->Product, 0, true);
		$this->Product->unbindModel(array(
			'belongsTo' => array('Manufacturer'),
			'hasMany' => array('ProductImage', 'ProductCategory')
		));
		
		$allProductsList = $this->Product->find('all', array(
			'fields' => array('Product.id', 'Product.sku', 'ProductName.name'),
			'order' => array('Product.sku')
		));
		
		$productList = array();	
		foreach ($allProductsList as $product)
		{
			$productList[$product['Product']['id']] = $product['Product']['sku'] . ' ' . $product['ProductName']['name'];
		}
		
		$this->Product->unbindModel(array('hasOne' => array('ProductName')));
		
		$this->Product->bindName($this->Product, null, true);
		$this->Product->bindDescription($this->Product, null, false);
		$this->Product->bindPrice(null, false);
		$this->Product->bindMeta($this->Product, null, false);
		$this->Product->bindOptions($this->Product);
		$this->Product->ProductOption->bindName($this->Product->ProductOption);
		$this->Product->bindAttributes(false);
		$this->Product->AttributeSet->bindName(0, false);
		
		if (Configure::read('Catalog.use_product_flags'))
		{
			$this->Product->bindFlags(false);	
		}

		if (Configure::read('Documents.assigned_to_products'))
		{
			$this->Product->bindDocuments(false);
		}
		
		$this->Category->bindName($this->Category, 0, false);
		
		$record = $this->Product->find('first', array(
			'conditions' => array('Product.id' => $id),
			'recursive' => -1
		));

		if (empty($record))
		{
			$this->redirect('/admin/products');
		}
		
		if ($record['Product']['type'] == 'simple')
		{
			$this->Product->ProductPrice->addBasePriceValidation();
		}
		
		$record['ProductName'] = $this->Product->ProductName->getNames('product_id', $id);
		$record['ProductDescription'] = $this->Product->ProductDescription->getDescriptions($id);
		$record['ProductPrice'] = $this->Product->ProductPrice->getPrices($id);
		$record['ProductMeta'] = $this->Product->ProductMeta->getMetas($id);
		
		$images = $this->Product->ProductImage->getImages($id);
		$productCategories = $this->Product->ProductCategory->getCategoriesList($id);
		$primaryCategory = $this->Product->ProductCategory->getPrimaryCategory($id);
		
		if (Configure::read('Catalog.related_enabled'))
		{
			$this->Product->bindRelatedProducts($this->Product);
			$relatedProducts = $this->Product->RelatedProduct->getProducts($id);
			$this->set('relatedProducts', $relatedProducts);
		}
		
		if (Configure::read('Catalog.crosssells_enabled'))
		{
			$this->Product->bindCrossSells($this->Product);
			$crossSells = $this->Product->CrossSell->getProducts($id);
			$this->set('crossSells', $crossSells);
		}
		
		if ($record['Product']['type'] == 'grouped')
		{
			$this->Product->bindGroupedProducts($this->Product);
			$groupedProducts = $this->Product->GroupedProduct->getProducts($id);
			$this->set('groupedProducts', $groupedProducts);
		}
		
		if (Configure::read('Shipping.products_to_shipping_services'))
		{
			$this->loadModel('ShippingCarrierService');
			$this->Product->bindShippingCarrierServices();
			$availableServices = $this->Product->ProductShippingCarrierService->find('list', array(
				'fields' => array('ProductShippingCarrierService.shipping_carrier_service_id', 'ProductShippingCarrierService.available'),
				'conditions' => array('ProductShippingCarrierService.product_id' => $id)
			));
			$services = $this->ShippingCarrierService->find('all', array('recursive' => -1));
			$this->set(compact('availableServices', 'services'));
		}

		if (Configure::read('Site.name') == 'Hytek (GB) Limited')
		{
			$attributeSet = $this->Product->AttributeSet->findById($record['Product']['attribute_set_id']);

			$attributes = $this->Product->AttributeSet->Attribute->getAttributes();
			$attributes = $this->Product->AttributeSet->Attribute->addValuesToAttributes($attributes);

			// Get attributes in set
			$productAttributes = $this->Product->AttributeSet->AttributeSetsAttribute->find('list', array(
				'fields' => array('id', 'attribute_id'),
				'conditions' => array('attribute_set_id' => $record['Product']['attribute_set_id'])
			));

			$this->set('productAttributes', $productAttributes);

			// Get product attribute values
			$productAttributeValues = $this->Product->AttributeValuesProduct->find('list', array(
				'fields' => array('AttributeValuesProduct.id', 'AttributeValuesProduct.attribute_value_id'),
				'conditions' => array('AttributeValuesProduct.product_id' => $id)
			));
			
		}		
		else if (!empty($record['Product']['attribute_set_id']))
		{
			// Get attribute set record
			$attributeSet = $this->Product->AttributeSet->findById($record['Product']['attribute_set_id']);
			
			// Get attributes in set
			$attributes = $this->Product->AttributeSet->Attribute->getAttributesBySet($record['Product']['attribute_set_id']);
			
			// Add all values to each attribute
			$attributes = $this->Product->AttributeSet->Attribute->addValuesToAttributes($attributes);
			
			// Get product attribute values
			$productAttributeValues = $this->Product->AttributeValuesProduct->find('list', array(
				'fields' => array('AttributeValuesProduct.id', 'AttributeValuesProduct.attribute_value_id'),
				'conditions' => array('AttributeValuesProduct.product_id' => $id)
			));
			
		}
		
		if (empty($this->data))
		{
			$this->data = $record;
		}
		
		if ($this->Session->check('Admin.' . $this->params['controller'] . '.last_tab'))
		{
			$this->set('initTab', $this->Session->read('Admin.' . $this->params['controller'] . '.last_tab'));
			$this->Session->delete('Admin.' . $this->params['controller'] . '.last_tab');
		}
		
		$customOptions = $this->Product->ProductOption->CustomOption->getList();
		$customOptionValuesList = $this->Product->ProductOption->CustomOption->CustomOptionValue->getValuesList();
		
		$options = $this->Product->ProductOption->getOptions($id, 'both', null, true);
		$options = $this->Product->ProductOption->ProductOptionValue->addValuesToOptions($options, array(
			'get_prices' => true,
			'get_names' => true,
			'rekey' => true
		));
		
		if (!empty($options))
		{
			// Get the custom options used by product so only select values these options
			// $usedOptions = Set::extract('/ProductOption/custom_option_id', $options);
			
			$ProductOptionStock = ClassRegistry::init('ProductOptionStock');
			$ProductOptionStock->unbindModel(array('hasOne' => array('ProductOptionStockPrice')), false);
			$stock = $ProductOptionStock->bindPrice(null, false)->getStockByProduct($id, true);
			
			// Has images?
			$varImages = false;
			foreach ($stock as $k => $v)
			{
				if (!empty($v['ProductOptionStockImage']))
				{
					$varImages = true;
					break;
				}
			}
			$this->set('varImages', $varImages);
			
			// Re-key stock
			$rekeyedStock = array();
			foreach ($stock as $k => $v)
			{
				$string = $v['ProductOptionStock']['value_ids'];
				$rekeyedStock[$string] = $v;
			}
			$this->set('rekeyedOptionsStock', $rekeyedStock);
			

			// Translate product options values to custom option value names
			$valueNames = array();
			foreach ($options as $k => $v)
			{
				foreach ($v['ProductOptionValue'] as $k2 => $v2)
				{
					$valueNames[$v2['ProductOptionValue']['id']] = $v2['CustomOptionValueName']['name'];
				}
			}
			$this->set('valueNames', $valueNames);
			
		}
		
		$this->Category->unbindProducts();
		$this->Category->recursive = 1;
		$categories = $this->Category->find('threaded');
		
		$treeList = $this->Category->generatetreelist(null, '{n}.Category.id', '{n}.CategoryName.name', '  -- ', 1);

		// Select list records
		$currencies = $this->Product->ProductPrice->Currency->find('all');
		$currenciesList = $this->Product->ProductPrice->Currency->find('list');
		$manufacturers = $this->Product->Manufacturer->find('list');
		$attributeSets = $this->Product->AttributeSet->getAttributeSetList();
		
		if (Configure::read('Documents.assigned_to_products'))
		{
			$this->Product->Document->order = 'Document.name';
			$documents = $this->Product->Document->find('list');
			
			$assocDocs = $this->Product->ProductDocument->find('list', array(
				'fields' => array('ProductDocument.id', 'ProductDocument.document_id'),
				'conditions' => array('ProductDocument.product_id' => $id)
			));
			
			$this->set(compact('documents', 'assocDocs'));
		
		}
		
		if (Configure::read('Catalog.use_tiered_customer_pricing'))
		{
			$CustomerGroup = ClassRegistry::init('CustomerGroup');
			$CustomerGroup->bindModel(array('hasMany' => array(
				'ProductPriceDiscount' => array(
					'conditions' => array('ProductPriceDiscount.product_id' => $id),
					'order' => array('ProductPriceDiscount.min_qty ASC')
				)
			)));
		
			$groups = $CustomerGroup->find('all');
			$this->set('groups', $groups);
		}
		
		if (Configure::read('Catalog.use_product_flags'))
		{
			$productFlags = $this->Product->ProductFlag->find('list');
			$assocFlags = $this->Product->ProductFlagsProduct->find('list', array(
				'fields' => array('ProductFlagsProduct.id', 'ProductFlagsProduct.product_flag_id'),
				'conditions' => array('ProductFlagsProduct.product_id' => $id)
			));
			$this->set(compact('productFlags', 'assocFlags'));
		}
		
		$this->set(compact(
			'record', 'descriptions', 'options', 'customOptions','images', 'productCategories',
			'manufacturers', 'currencies', 'categories', 'treeList', 'currenciesList', 'customOptionValuesList',
			'attributeSets', 'attributes', 'attributeSet', 'productAttributeValues', 'primaryCategory', 'productList'
		));
         * 
         */
        }  
        
        /**
	 * Admin
	 * View existing product for editing.
	 * 
	 * @param int $id
	 * @return void
	 * @access public
	 */
	public function admin_edit($id = null)
	{
		if (is_null($id))
		{
			$this->redirect('/admin/products');
		}

		$this->Product->bindName($this->Product, 0, true);
		$this->Product->unbindModel(array(
			'belongsTo' => array('Manufacturer'),
			'hasMany' => array('ProductImage', 'ProductCategory')
		));
		
		$allProductsList = $this->Product->find('all', array(
			'fields' => array('Product.id', 'Product.sku', 'ProductName.name'),
			'order' => array('Product.sku')
		));
		
		$productList = array();	
		foreach ($allProductsList as $product)
		{
			$productList[$product['Product']['id']] = $product['Product']['sku'] . ' ' . $product['ProductName']['name'];
		}
		
		$this->Product->unbindModel(array('hasOne' => array('ProductName')));
		
		$this->Product->bindName($this->Product, null, true);
		$this->Product->bindDescription($this->Product, null, false);
		$this->Product->bindPrice(null, false);
		$this->Product->bindMeta($this->Product, null, false);
		$this->Product->bindOptions($this->Product);
		$this->Product->ProductOption->bindName($this->Product->ProductOption);
		$this->Product->bindAttributes(false);
		$this->Product->AttributeSet->bindName(0, false);
		
		if (Configure::read('Catalog.use_product_flags'))
		{
			$this->Product->bindFlags(false);	
		}

		if (Configure::read('Documents.assigned_to_products'))
		{
			$this->Product->bindDocuments(false);
		}
		
		$this->Category->bindName($this->Category, 0, false);
		
		$record = $this->Product->find('first', array(
			'conditions' => array('Product.id' => $id),
			'recursive' => -1
		));

		if (empty($record))
		{
			$this->redirect('/admin/products');
		}
		
		if ($record['Product']['type'] == 'simple')
		{
			$this->Product->ProductPrice->addBasePriceValidation();
		}
		
		$record['ProductName'] = $this->Product->ProductName->getNames('product_id', $id);
		$record['ProductDescription'] = $this->Product->ProductDescription->getDescriptions($id);
		$record['ProductPrice'] = $this->Product->ProductPrice->getPrices($id);
		$record['ProductMeta'] = $this->Product->ProductMeta->getMetas($id);
		
		$images = $this->Product->ProductImage->getImages($id);
		$productCategories = $this->Product->ProductCategory->getCategoriesList($id);
		$primaryCategory = $this->Product->ProductCategory->getPrimaryCategory($id);
		
		if (Configure::read('Catalog.related_enabled'))
		{
			$this->Product->bindRelatedProducts($this->Product);
			$relatedProducts = $this->Product->RelatedProduct->getProducts($id);
			$this->set('relatedProducts', $relatedProducts);
		}
		
		if (Configure::read('Catalog.crosssells_enabled'))
		{
			$this->Product->bindCrossSells($this->Product);
			$crossSells = $this->Product->CrossSell->getProducts($id);
			$this->set('crossSells', $crossSells);
		}
		
		if ($record['Product']['type'] == 'grouped')
		{
			$this->Product->bindGroupedProducts($this->Product);
			$groupedProducts = $this->Product->GroupedProduct->getProducts($id);
			$this->set('groupedProducts', $groupedProducts);
		}
		
		if (Configure::read('Shipping.products_to_shipping_services'))
		{
			$this->loadModel('ShippingCarrierService');
			$this->Product->bindShippingCarrierServices();
			$availableServices = $this->Product->ProductShippingCarrierService->find('list', array(
				'fields' => array('ProductShippingCarrierService.shipping_carrier_service_id', 'ProductShippingCarrierService.available'),
				'conditions' => array('ProductShippingCarrierService.product_id' => $id)
			));
			$services = $this->ShippingCarrierService->find('all', array('recursive' => -1));
			$this->set(compact('availableServices', 'services'));
		}

		if (Configure::read('Site.name') == 'Hytek (GB) Limited')
		{
			$attributeSet = $this->Product->AttributeSet->findById($record['Product']['attribute_set_id']);

			$attributes = $this->Product->AttributeSet->Attribute->getAttributes();
			$attributes = $this->Product->AttributeSet->Attribute->addValuesToAttributes($attributes);

			// Get attributes in set
			$productAttributes = $this->Product->AttributeSet->AttributeSetsAttribute->find('list', array(
				'fields' => array('id', 'attribute_id'),
				'conditions' => array('attribute_set_id' => $record['Product']['attribute_set_id'])
			));

			$this->set('productAttributes', $productAttributes);

			// Get product attribute values
			$productAttributeValues = $this->Product->AttributeValuesProduct->find('list', array(
				'fields' => array('AttributeValuesProduct.id', 'AttributeValuesProduct.attribute_value_id'),
				'conditions' => array('AttributeValuesProduct.product_id' => $id)
			));
			
		}		
		else if (!empty($record['Product']['attribute_set_id']))
		{
			// Get attribute set record
			$attributeSet = $this->Product->AttributeSet->findById($record['Product']['attribute_set_id']);
			
			// Get attributes in set
			$attributes = $this->Product->AttributeSet->Attribute->getAttributesBySet($record['Product']['attribute_set_id']);
			
			// Add all values to each attribute
			$attributes = $this->Product->AttributeSet->Attribute->addValuesToAttributes($attributes);
			
			// Get product attribute values
			$productAttributeValues = $this->Product->AttributeValuesProduct->find('list', array(
				'fields' => array('AttributeValuesProduct.id', 'AttributeValuesProduct.attribute_value_id'),
				'conditions' => array('AttributeValuesProduct.product_id' => $id)
			));
			
		}
		
		if (empty($this->data))
		{
			$this->data = $record;
		}
		
		if ($this->Session->check('Admin.' . $this->params['controller'] . '.last_tab'))
		{
			$this->set('initTab', $this->Session->read('Admin.' . $this->params['controller'] . '.last_tab'));
			$this->Session->delete('Admin.' . $this->params['controller'] . '.last_tab');
		}
		
		$customOptions = $this->Product->ProductOption->CustomOption->getList();
		$customOptionValuesList = $this->Product->ProductOption->CustomOption->CustomOptionValue->getValuesList();
		
		$options = $this->Product->ProductOption->getOptions($id, 'both', null, true);
		$options = $this->Product->ProductOption->ProductOptionValue->addValuesToOptions($options, array(
			'get_prices' => true,
			'get_names' => true,
			'rekey' => true
		));
		
		if (!empty($options))
		{
			// Get the custom options used by product so only select values these options
			// $usedOptions = Set::extract('/ProductOption/custom_option_id', $options);
			
			$ProductOptionStock = ClassRegistry::init('ProductOptionStock');
			$ProductOptionStock->unbindModel(array('hasOne' => array('ProductOptionStockPrice')), false);
			$stock = $ProductOptionStock->bindPrice(null, false)->getStockByProduct($id, true);
			
			// Has images?
			$varImages = false;
			foreach ($stock as $k => $v)
			{
				if (!empty($v['ProductOptionStockImage']))
				{
					$varImages = true;
					break;
				}
			}
			$this->set('varImages', $varImages);
			
			// Re-key stock
			$rekeyedStock = array();
			foreach ($stock as $k => $v)
			{
				$string = $v['ProductOptionStock']['value_ids'];
				$rekeyedStock[$string] = $v;
			}
			$this->set('rekeyedOptionsStock', $rekeyedStock);
			

			// Translate product options values to custom option value names
			$valueNames = array();
			foreach ($options as $k => $v)
			{
				foreach ($v['ProductOptionValue'] as $k2 => $v2)
				{
					$valueNames[$v2['ProductOptionValue']['id']] = $v2['CustomOptionValueName']['name'];
				}
			}
			$this->set('valueNames', $valueNames);
			
		}
		
		$this->Category->unbindProducts();
		$this->Category->recursive = 1;
		$categories = $this->Category->find('threaded');
		
		$treeList = $this->Category->generatetreelist(null, '{n}.Category.id', '{n}.CategoryName.name', '  -- ', 1);

		// Select list records
		$currencies = $this->Product->ProductPrice->Currency->find('all');
		$currenciesList = $this->Product->ProductPrice->Currency->find('list');
		$manufacturers = $this->Product->Manufacturer->find('list');
		$attributeSets = $this->Product->AttributeSet->getAttributeSetList();
		
		if (Configure::read('Documents.assigned_to_products'))
		{
			$this->Product->Document->order = 'Document.name';
			$documents = $this->Product->Document->find('list');
			
			$assocDocs = $this->Product->ProductDocument->find('list', array(
				'fields' => array('ProductDocument.id', 'ProductDocument.document_id'),
				'conditions' => array('ProductDocument.product_id' => $id)
			));
			
			$this->set(compact('documents', 'assocDocs'));
		
		}
		
		if (Configure::read('Catalog.use_tiered_customer_pricing'))
		{
			$CustomerGroup = ClassRegistry::init('CustomerGroup');
			$CustomerGroup->bindModel(array('hasMany' => array(
				'ProductPriceDiscount' => array(
					'conditions' => array('ProductPriceDiscount.product_id' => $id),
					'order' => array('ProductPriceDiscount.min_qty ASC')
				)
			)));
		
			$groups = $CustomerGroup->find('all');
			$this->set('groups', $groups);
		}
		
		if (Configure::read('Catalog.use_product_flags'))
		{
			$productFlags = $this->Product->ProductFlag->find('list');
			$assocFlags = $this->Product->ProductFlagsProduct->find('list', array(
				'fields' => array('ProductFlagsProduct.id', 'ProductFlagsProduct.product_flag_id'),
				'conditions' => array('ProductFlagsProduct.product_id' => $id)
			));
			$this->set(compact('productFlags', 'assocFlags'));
		}
		
		$this->set(compact(
			'record', 'descriptions', 'options', 'customOptions','images', 'productCategories',
			'manufacturers', 'currencies', 'categories', 'treeList', 'currenciesList', 'customOptionValuesList',
			'attributeSets', 'attributes', 'attributeSet', 'productAttributeValues', 'primaryCategory', 'productList'
		));
		
	}

}