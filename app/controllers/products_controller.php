<?php

/**
 * Products Controller
 * 
 */
class ProductsController extends AppController
{
	/**
	 * Array containing the names of components this controller uses.
	 *
	 * @var array
	 * @access public
	 */
	public $components = array('ImageNew');
	
	/**
	 * Admin
	 * List products. 
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_index()
	{
		$conditions = array();
		
		$this->set('catIDs', array());
		$this->set('catNodesOpen', '""');
		
		if (!empty($this->params['url']['keyword']))
		{
			$this->Session->write('Product.keyword', $this->params['url']['keyword']);
			$conditions[]['OR'] = array(
				'Product.sku LIKE' => '%' . $this->params['url']['keyword'] . '%',
				'ProductName.name LIKE' => '%' . $this->params['url']['keyword'] . '%'
			);
		}
		else
		{
			$this->Session->delete('Product.keyword');
		}

		if (!empty($this->params['url']['manufacturer_id']))
		{
			if (!empty($this->params['url']['manufacturer_id']))
			{
				$this->Session->write('Product.manufacturer_id', $this->params['url']['manufacturer_id']);
				$conditions['Product.manufacturer_id'] = $this->params['url']['manufacturer_id'];
			}
			else
			{
				$this->Session->delete('Product.manufacturer_id');
			}
		}
		
		if (!empty($this->params['url']['filtered_cats']))
		{
			$nodeIDs = explode(',', $this->params['url']['filtered_cats']);
			
			$catIDs = array();
			$openNodeIDs = array();
			foreach ($nodeIDs as $nofdeID)
			{
				$catID = substr($nodeID, 9);
				$catIDs[] = $catID;
				$openNodeIDs[] = '"cat-node-' . $catID . '"';
			}
			$this->set('catIDs', $catIDs);
			
			$products = $this->Product->ProductCategory->find('list', array(
				'fields' => array('ProductCategory.product_id', 'ProductCategory.product_id'),
				'conditions' => array('ProductCategory.category_id' => $catIDs)
			));
			
			$conditions[] = array('Product.id' => $products);
			
		}
		
		if (!empty($this->params['url']['open_nodes']))
		{			$this->set('catNodesOpen', '[' . $this->params['url']['open_nodes'] . ']');
		}
		
		$this->Product->bindName($this->Product, 0, false);
		// $this->Product->unbindModel(array('hasAndBelongsToMany' => array('Category')), false);
		$this->Product->unbindModel(array('hasMany' => array('ProductImage')), false);
		
		// $this->Product->MainCategory->bindName($this->Product->MainCategory, 0, false);
		
		$joins = array(
			array(
				'table' => 'product_categories',
		        'alias' => 'ProductCategory',
		        'type' => 'LEFT',
		        'conditions'=> array(
					'ProductCategory.product_id = Product.id',
					'ProductCategory.primary = 1'
				)			
			),
			array(
				'table' => 'category_names',
		        'alias' => 'CategoryName',
		        'type' => 'LEFT',
		        'conditions'=> array(
					'ProductCategory.category_id = CategoryName.category_id',
					'CategoryName.language_id' => 1
				)			
			)
		);
		
		$this->paginate['fields'] = array('Product.*', 'ProductName.*', 'CategoryName.*', 'Manufacturer.*');
		$this->paginate['joins'] = $joins;
		$this->paginate['limit'] = 20;
		$this->paginate['conditions'] = $conditions;
		
		$records = $this->paginate();
		$this->set('records', $records);
		
		$this->Category->bindName($this->Category, 0, false);
		$this->Category->unbindModel(array('hasAndBelongsToMany' => array('Product')), false);
		$this->set('categories', $this->Category->find('threaded'));
		
		$this->set('manufacturers', $this->Product->Manufacturer->find('list'));

		$this->Session->write('Product.last_index_url', $_SERVER['REQUEST_URI']);
		
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
	
	/**
	 * Admin
	 * Save existing product.
	 * 
	 * @return void
	 * @access public
	 */	
	public function admin_save()
	{
		if (empty($this->data['Product']['id']))
		{
			$this->redirect('/admin/products');
		}
		
		$id = $this->data['Product']['id'];
		
		$record = $this->Product->findById($id);
		
		if (isset($this->params['form']['last_pane']))
		{
			$this->Session->write('Admin.products.last_tab', $this->params['form']['last_pane']);
		}
		
		$this->Product->bindName($this->Product, null, false);
		$this->Product->bindDescription($this->Product, null, false);
		$this->Product->bindPrice(null, false);
		$this->Product->bindMeta($this->Product, null, false);
		$this->Product->bindRelatedProducts($this->Product);
		$this->Product->bindCrossSells($this->Product);
		$this->Product->bindGroupedProducts($this->Product);
		$this->Product->bindAttributes(false);
		$this->Product->bindShippingCarrierServices();
		
		if (Configure::read('Catalog.use_tiered_customer_pricing'))
		{
			$this->Product->bindCustomerDiscounts(false);
			$this->Product->ProductPriceDiscount->pruneData($this->data);
		}
		
		if (Configure::read('Catalog.use_product_flags'))
		{
			$this->Product->bindFlags(false);	
		}
		
		if (Configure::read('Documents.assigned_to_products'))
		{
			$this->Product->bindDocuments(false);
		}

		// Add product type to data for callbacks
		$this->data['Product']['type'] = $record['Product']['type'];
		
		if ($record['Product']['type'] == 'simple')
		{
			$this->Product->ProductPrice->addBasePriceValidation();
		}
		
		foreach ($this->data['ProductName'] as $languageID => $name)
		{
			$this->data['ProductMeta'][$languageID]['product_name'] = $name['name'];
			$this->data['ProductMeta'][$languageID]['product_sku'] = $this->data['Product']['sku'];
		}
		
		$this->Product->unbindModel(array('hasMany' => array('ProductCategory')), false);
		
		$validates = $this->Product->saveAll($this->data, array('validate' => 'only'));

		if ($validates)
		{
			$this->Product->saveAll($this->data);
			
			$this->Product->ProductCategory->assignProductToCategories($id, $this->data);
			
			$image = $this->data['NewProductImage']['image'];
			$sku = $this->data['Product']['sku'];
			
			$this->uploadImage($id, $image, $sku);
			$this->uploadVarImages();
			
			$this->Session->setFlash('Product saved.', 'default', array('class' => 'success'));
		}
		else
		{
			$errorsOnTabs = array();
			$validationErrors = array();
			
			if (!empty($this->Product->validationErrors['sku']))
			{
				$errorsOnTabs[] = 'general';
			}
			if (!empty($this->Product->validationErrors['main_category_id']))
			{
				$errorsOnTabs[] = 'cats';
			}
			
			if ($this->hasValidationErrors($this->Product->ProductName))
			{
				$errorsOnTabs[] = 'desc';
			}
			if ($this->hasValidationErrors($this->Product->ProductPrice))
			{
				$errorsOnTabs[] = 'pricing';
			}
			if ($this->hasValidationErrors($this->Product->ProductDescription))
			{
				$errorsOnTabs[] = 'desc';
				$validationErrors['ProductDescription'] = $this->Product->ProductDescription->validationErrors;
			}
			if ($this->hasValidationErrors($this->Product->ProductMeta))
			{	
				$errorsOnTabs[] = 'meta';
				$validationErrors['ProductMeta'] = $this->Product->ProductMeta->validationErrors;
			}
			if ($this->hasValidationErrors($this->Product->CrossSell))
			{	
				$errorsOnTabs[] = 'cross';
				$validationErrors['CrossSell'] = $this->Product->CrossSell->validationErrors;
			}
			
			$this->set('errorsOnTabs', $errorsOnTabs);
			$this->set('validationErrors', $validationErrors);

			//debug($validationErrors);
			
			$this->Session->setFlash('Product could not be saved. Please check the form for errors.', 'default', array('class' => 'failure'));
			return $this->setAction('admin_edit', $id);
			
		}
		
		$this->redirect('/admin/products/edit/' . $id);
		
	}
	
	/**
	 * Admin
	 * Show add new product form.
	 * 
	 * @return void
	 * @access public
	 */	
	public function admin_new()
	{
		$this->Product->bindPrice(null, false);
		$this->Product->bindName($this->Product, null, true);
		$this->Product->bindDescription($this->Product, null, false);
		$this->Product->bindMeta($this->Product, null, false);
		
		$this->Category->bindName($this->Category, 0, false);
		
		$languages = $this->Language->find('list');
		$currencies = $this->Product->ProductPrice->Currency->find('all');
		
		$this->Category->unbindProducts();
		$treeList = $this->Category->generatetreelist(null, '{n}.Category.id', '{n}.CategoryName.name', ' -- ', 1);
		
		$manufacturers = $this->Product->Manufacturer->find('list');
		
		if (!empty($this->data['Product']['type']) && ($this->data['Product']['type'] == 'simple'))
		{
			$this->Product->ProductPrice->addBasePriceValidation();
		}
		
		$this->set('catIDs', array());
		$this->set('catNodesOpen', '""');
		
		if (!empty($this->data['Product']['filtered_cats']))
		{
			$nodeIDs = explode(',', $this->data['Product']['filtered_cats']);
			
			$catIDs = array();
			$openNodeIDs = array();
			foreach ($nodeIDs as $nodeID)
			{
				$catID = substr($nodeID, 9);
				$catIDs[] = $catID;
				$openNodeIDs[] = '"cat-node-' . $catID . '"';
			}
			$this->set('catIDs', $catIDs);
			
		}
		
		if (!empty($this->data['Product']['open_nodes']))
		{
			$this->set('catNodesOpen', '[' . $this->data['Product']['open_nodes'] . ']');
		}
		
		$this->Category->bindName($this->Category, 0, false);
		$this->Category->unbindModel(array('hasAndBelongsToMany' => array('Product')), false);
		$this->set('categories', $this->Category->find('threaded'));

		$this->set(compact('languages', 'currencies', 'treeList', 'manufacturers'));
		
	}
	
	/**
	 * Admin
	 * Add a new product.
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_add()
	{
		if (empty($this->data))
		{
			$this->redirect('/admin/products');
		}
		
		/*
		foreach ($this->data['ProductName'] as $languageID => $name)
		{
			$this->data['ProductMeta'][$languageID]['product_name'] = $name['name'];
			$this->data['ProductMeta'][$languageID]['product_sku'] = $this->data['Product']['sku'];
		}
		*/
		
		$id = $this->Product->addProduct($this->data);
		
		if (!empty($id))
		{
			$this->Product->ProductCategory->assignProductToCategories($id, $this->data);
			
			$this->Session->setFlash('Product added. You may now complete the product record.', 'default', array('class' => 'success'));
			$this->redirect('/admin/products/edit/' . $id);
		}

		debug($this->Product->validationErrors);
		
		$this->Session->setFlash('Product could not be saved. Please check the form for errors.', 'default', array('class' => 'failure'));
		return $this->setAction('admin_new');
		
	}
	
	/**
	 * Admin.
	 * Delete a product record.
	 * 
	 * @param int $id
	 * @access public
	 * @return void
	 */
	public function admin_delete($id)
	{
		if ($this->Product->delete($id))
		{
			$this->Session->setFlash('Product deleted.', 'default', array('class' => 'success'));
			$this->redirect('/admin/products');
		}
		
		$this->Session->setFlash('Product not deleted.', 'default', array('class' => 'failure'));
		$this->redirect('/admin/products/edit/' . $id);	
		
	}	
	
	/**
	 * Admin
	 * Duplicate a product record.
	 * 
	 * @param int $id
	 * @return void
	 * @access public
	 */
	public function admin_duplicate($id)
	{
		$duplicateID = $this->Product->duplicateProduct($id);
		
		if (!empty($duplicateID))
		{
			$this->Session->setFlash('Product duplicated. Now editing duplicated product.', 'default', array('class' => 'success'));
			$this->redirect('/admin/products/edit/' . $duplicateID);
		}
		
		$this->Session->setFlash('Product not duplicated.', 'default', array('class' => 'failure'));
		$this->redirect('/admin/products/edit/' . $id);
		
	}
	
	/**
	 * Admin
	 * Delete a product option.
	 * 
	 * @param int $productOptionID
	 * @return void
	 * @access public
	 */
	public function admin_delete_product_option($productOptionID)
	{
		$this->Session->write('Admin.products.last_tab', 'variations');
		
		$this->Product->bindOptions($this->Product, false);
		
		$productID = $this->Product->ProductOption->field('product_id', array(
			'ProductOption.id' => $productOptionID
		));
		
		$this->Product->ProductOption->delete($productOptionID);
		
		// Re-init stock
		ClassRegistry::init('ProductOptionStock')->initOptionStock($productID);

		$this->Product->bindPrice(null, false);
		
		// Update product price record with lowest and highest possible prices based on product options
		$this->Product->ProductPrice->updateLowestHightestPrices($productID);
		
		$this->Session->setFlash('Product option deleted.', 'default', array('class' => 'success'));
		$this->redirect('/admin/products/edit/' . $productID);
		
	}
	
	/**
	 * Admin
	 * Duplicate a product record.
	 * 
	 * @param int $id
	 * @return void
	 * @access public
	 */
	public function admin_delete_product_option_value($productOptionValueID)
	{
		$this->Session->write('Admin.products.last_tab', 'variations');
		
		$this->Product->bindOptions($this->Product, false);
		
		$productOptionID = $this->Product->ProductOption->ProductOptionValue->field('product_option_id', array(
			'ProductOptionValue.id' => $productOptionValueID
		));
		
		$productID = $this->Product->ProductOption->field('product_id', array(
			'ProductOption.id' => $productOptionID
		));
		
		$valueCount = $this->Product->ProductOption->ProductOptionValue->find('count', array('conditions' => array(
			'ProductOptionValue.product_option_id' => $productOptionID
		)));
		
		if ($valueCount == 1)
		{
			$this->Session->setFlash('At least one value is required.', 'default', array('class' => 'failure'));
			$this->redirect('/admin/products/edit/' . $productID);
		}
		
		$this->Product->ProductOption->ProductOptionValue->delete($productOptionValueID);
		
		ClassRegistry::init('ProductOptionStock')->removeStockByValue($productID, $productOptionValueID);
		
		$this->Session->setFlash('Product option value deleted.', 'default', array('class' => 'success'));
		$this->redirect('/admin/products/edit/' . $productID);
		
	}
	
	/**
	 * Admin
	 * Delete a customer group discount tier.
	 * 
	 * @param int $tierID
	 * @return void
	 * @access public
	 */
	public function admin_delete_customer_tier($tierID)
	{
		$this->Session->write('Admin.products.last_tab', 'pricing');
		
		$this->Product->bindCustomerDiscounts();
		
		$record = $this->Product->ProductPriceDiscount->find('first', array(
			'conditions' => array('ProductPriceDiscount.id' => $tierID)
		));
		
		if (empty($record))
		{
			$this->redirect('/admin/products');
		}
		
		$productID = $record['ProductPriceDiscount']['product_id'];
		
		$result = $this->Product->ProductPriceDiscount->delete($tierID);
		
		if ($result)
		{
			$this->Session->setFlash('Discount tier deleted.', 'default', array('class' => 'success'));
			$this->redirect('/admin/products/edit/' . $productID);
		}
		
		$this->Session->setFlash('Discount tier not deleted.', 'default', array('class' => 'failure'));
		$this->redirect('/admin/products/edit/' . $productID);
	
	}
	
	/**
	 * Admin [ajax]
	 * Get list of products for ajax lookup.
	 * 
	 * @param string $context
	 * @param int $catID
	 * @param string $query [optional]
	 * @return void
	 * @access public
	 */
	public function admin_getlist($context, $catID, $query = null)
	{	
		Configure::write('debug', 0);
	
		$this->layout = 'ajax';
		
		if (empty($query))
		{
			$this->render(false);
			return 'Please enter a search term';
		}
		
		$conditions = array('OR' => array(
			'ProductName.name LIKE' => '%' . $query . '%',
			'Product.sku LIKE' => '%' . $query . '%'
		));
		
		if (!empty($catID))
		{
			$categoryProducts = $this->Product->ProductCategory->find('list', array(
				'fields' => array('ProductCategory.id', 'ProductCategory.product_id'),
				'conditions' => array('ProductCategory.category_id' => $catID)
			));
				
			$conditions['NOT'] = array('Product.id' => $categoryProducts);			
		}
		
		$this->Product->unbindModel(array(
			'belongsTo' => array('Manufacturer'),
			'hasMany' => array('ProductImage', 'ProductDescription', 'ProductPrice'),
			'hasAndBelongsToMany' => array('Category')
		));
		
		$this->Product->bindName($this->Product, Configure::read('Languages.main_lang_id'), false);
		
		$records = $this->Product->find('all', array(
			'fields' => array('Product.id', 'Product.sku', 'ProductName.name'),
			'conditions' => $conditions
		));
		
		$this->set('records', $records);
		$this->set('context', $context);
		
	}
	
	/**
	 * Upload product image.
	 * 
	 * @param int $productID
	 * @param array $file
	 * @param string $sku
	 * @return bool
	 * @access private
	 */
	private function uploadImage($productID, $file, $sku)
	{
		if (empty($file) || !empty($file['error']) || !is_uploaded_file($file['tmp_name']))
		{
			return false;
		}

		$this->Product->ProductImage->create();
		$this->Product->ProductImage->save(array('ProductImage' => array(
			'product_id' => $productID
		)));
		
		$imageID = $this->Product->ProductImage->getInsertID();
		
		if (empty($imageID))
		{
			return false;
		}
		
		$pathInfo = pathinfo($file["name"]);
		$filenameNoExt = Inflector::slug(strtolower($sku)) . '-' . $imageID;
		$filename = $filenameNoExt . '.' .  $pathInfo['extension'];

		$pathToOriginal = WWW_ROOT . 'img/products/original/' . $filename;
		
		move_uploaded_file($file['tmp_name'], $pathToOriginal);
		
		//App::import('Vendor', 'CI_Image_lib', array('file' => 'Image_lib.php'));
		
		// Upload large image
		/*$largePath = $this->uploadLargeProductImage($pathToOriginal, Configure::read('Images.product_large_path'), $filename);
		
		$out = $largePath;
		foreach (array('medium', 'thumb', 'tiny') as $size)
		{
			$dest = WWW_ROOT . 'img/products/' . $size . '/' . $filename;
			$width = Configure::read('Images.product_' . $size . '_width');
			$height = Configure::read('Images.product_' . $size . '_height');

			$out = $this->ImageNew->resize($pathToOriginal, $dest, $width, $height, 'auto');
			$this->ImageNew->expandToFit($out, $width, $height);
		}*/

		App::import('Lib', 'EsperImage');
		$options = array( 'saveFilename' => $filename );
		$esperImage = new EsperImage($pathToOriginal, $options);
		$esperImage->resizeAll();
		
		$this->Product->ProductImage->id = $imageID;
		$this->Product->ProductImage->save(array('ProductImage' => array(
			'filename'  => $filenameNoExt,
			'ext'	 	=> $pathInfo['extension']
		)));

		return true;
		
	}

	/**
	 * Upload product variation images.
	 * 
	 * @return bool
	 * @access private
	 */
	private function uploadVarImages()
	{ 	
		if (empty($this->data['ProductOptionStockImage']))
		{
			return false;
		}

		$this->loadModel('ProductOptionStockImage');
		
		foreach ($this->data['ProductOptionStockImage'] as $stockID => $images)
		{
			foreach ($images as $k => $file)
			{
				if ($k == 'sort') {
					$this->saveVarImageSort($file);
				} else {
					$this->uploadVarImage($stockID, $k, $file);
				}
			}
		}

		return true;
		
	}

	private function saveVarImageSort($sortIDs){
		$sortIDs = explode(',', $sortIDs);

		foreach($sortIDs as $i => $sortID){
			$sortID = substr($sortID, 1);
			$sortID = trim($sortID);

			if (empty($sortID)) {
				continue;
			}

			$this->ProductOptionStockImage->id = $sortID;

			$this->ProductOptionStockImage->save(array(
				'sort_order' => $i,
			), false);
		}
	}

	/**
	 * Upload product variation images.
	 * 
	 * @param int $stockID
	 * @param mixed $imageID
	 * @param array $file
	 * @return bool
	 * @access private
	 */
	/*private function uploadVarImage($stockID, $imageID, $file)
	{	
		if (!empty($file['error']) || !is_uploaded_file($file['tmp_name']))
		{
			return false;
		}
		
		if ($imageID == 'new')
		{
			$this->ProductOptionStockImage->create();
			$this->ProductOptionStockImage->save(array('ProductOptionStockImage' => array(
				'product_option_stock_id' => $stockID
			)));
			
			$imageID = $this->ProductOptionStockImage->getInsertID();

			if (empty($imageID))
			{
				return false;
			}
		}

		$pathInfo = pathinfo($file["name"]);
		$filename = $imageID . '.' .  $pathInfo['extension'];

		App::import('Vendor', 'CI_Image_lib', array('file' => 'Image_lib.php'));

		// Upload large image
		$largePath = $this->uploadLargeProductImage($file['tmp_name'], Configure::read('Images.var_large_path'), $filename);

		$out = $largePath;
		foreach (array('medium', 'thumb', 'tiny') as $size)
		{
			$dest = WWW_ROOT . 'img/vars/' . $size . '/' . $filename;
			$width = Configure::read('Images.var_' . $size . '_width');
			$height = Configure::read('Images.var_' . $size . '_height');

			$out = $this->ImageNew->resize($out, $dest, $width, $height, 'auto');
			$this->ImageNew->expandToFit($out, $width, $height);
		}
		
		$this->ProductOptionStockImage->id = $imageID;
		$this->ProductOptionStockImage->save(array('ProductOptionStockImage' => array(
			'filename'  => $imageID,
			'ext'	 	=> $pathInfo['extension']
		)));

		return true;

	}*/
	private function uploadVarImage($stockID, $imageID, $file)
	{	
		if (!empty($file['error']) || !is_uploaded_file($file['tmp_name']))
		{
			return false;
		}
		
		if ($imageID == 'new')
		{
			$this->ProductOptionStockImage->create();
			$this->ProductOptionStockImage->save(array('ProductOptionStockImage' => array(
				'product_option_stock_id' => $stockID
			)));
			
			$imageID = $this->ProductOptionStockImage->getInsertID();

			if (empty($imageID))
			{
				return false;
			}
		}

		$pathInfo = pathinfo($file["name"]);
		$filename = $imageID . '.' .  $pathInfo['extension'];

		/*App::import('Vendor', 'CI_Image_lib', array('file' => 'Image_lib.php'));

		// Upload large image
		$largePath = $this->uploadLargeProductImage($file['tmp_name'], Configure::read('Images.var_large_path'), $filename);

		$out = $largePath;
		foreach (array('medium', 'thumb', 'tiny') as $size)
		{
			$dest = WWW_ROOT . 'img/vars/' . $size . '/' . $filename;
			$width = Configure::read('Images.var_' . $size . '_width');
			$height = Configure::read('Images.var_' . $size . '_height');

			$out = $this->ImageNew->resize($out, $dest, $width, $height, 'auto');
			$this->ImageNew->expandToFit($out, $width, $height);
		}*/

		$pathToOriginal = $file['tmp_name'];

		App::import('Lib', 'EsperImage');
		$options = array( 'saveFilename' => $filename, 'type' => 'var' );
		$esperImage = new EsperImage($pathToOriginal, $options);
		$esperImage->resizeAll();
		
		$this->ProductOptionStockImage->id = $imageID;
		$this->ProductOptionStockImage->save(array('ProductOptionStockImage' => array(
			'filename'  => $imageID,
			'ext'	 	=> $pathInfo['extension']
		)));

		return true;

	}

}


