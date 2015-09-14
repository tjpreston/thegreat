<?php

/**
 * Product Category Model
 * 
 */
class ProductCategory extends AppModel
{
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array('Product', 'Category');
	
	/**
	 * The column name(s) and direction(s) to order find results by default.
	 *
	 * @var string
	 * @access public
	 */
	public $order = 'ProductCategory.sort';
	
	public function assignProductToCategories($productID, $data) 
	{
		$data = $data['ProductCategory']['ProductCategory'];

		$primaryCat = $data['primary_category_id'];
		unset($data['primary_category_id']);
		
		// Get product categories prior to save
		$preCategories = $this->find('list', array(
			'fields' => array('ProductCategory.id', 'ProductCategory.category_id'),
			'conditions' => array('ProductCategory.product_id' => $productID)
		));
		
		// Delete all product cats
		$this->deleteAll(array('ProductCategory.product_id' => $productID));
				
		// Add the primary cat
		$this->create();
		$this->save(array('ProductCategory' => array(
			'product_id' => $productID,
			'category_id' => $primaryCat,
			'primary' => 1
		)), array('callbacks' => false));

		// Init new categories array
		$postCategories = array($primaryCat);

		foreach ($data as $id)
		{
			if ($id && ($id !== $primaryCat))
			{
				$this->create();
				$this->save(array('ProductCategory' => array(
					'product_id' => $productID,
					'category_id' => $id
				)), array('callbacks' => false));
				
				$postCategories[] = $id;

			}
		}

		$affectedCategories = array_unique(array_merge($preCategories, $postCategories));

		$this->updateCategoryProductCount($affectedCategories);
		
	}

	public function updateCategoryProductCount($cats)
	{
		foreach ($cats as $catID)
		{
			$count = $this->find('count', array('conditions' => array(
				'ProductCategory.category_id' => $catID,
				'Product.active' => 1
			)));
			
			$this->Category->id = $catID;
			$this->Category->saveField('product_counter', $count);

		}
	}


	
	/**
	 * Get categories.
	 * 
	 * @param int $productID
	 * @return array
	 * @access public
	 */
	public function getCategories($productID)
	{	
		return $this->find('all', array(
			'conditions' => array('ProductCategory.product_id' => $productID),
			'recursive' => 2
		));
	}
	
	/**
	 * Get list of categories.
	 * 
	 * @param int $productID
	 * @return array
	 * @access public
	 */
	public function getCategoriesList($productID)
	{	
		return $this->find('list', array(
			'fields' => array('ProductCategory.id', 'ProductCategory.category_id'),
			'conditions' => array('ProductCategory.product_id' => $productID),
			'recursive' => -1
		));
	}
	
	/**
	 * Get all products in category.
	 * 
	 * @param int $categoryID
	 * @return array $records
	 * @access public
	 */
	public function getProductsInCategory($categoryID)
	{	
		/*
		$this->Category->Product->ProductCategory->Product->bindName($this->Category->Product, 0, true);
		$this->Category->Product->ProductCategory->Behaviors->attach('Containable');
		$categoryProducts = $this->Category->Product->ProductCategory->find('all', array(
			'conditions' => array('ProductCategory.category_id' => $id),
			'contain' => array('Product' => array('ProductName'))
		));
		*/
		
		$this->Product->bindName($this->Product, 0, false);
		
		$this->Product->unbindModel(array(
			'hasMany' => array('ProductImage', 'ProductDescription', 'ProductPrice', 'ProductMeta', 'RelatedProduct', 'ProductOption', 'CrossSell'),
			'hasAndBelongsToMany' => array('Category')
		), false);
		
		$records = $this->Product->find('all', array(
			'fields' => array('Product.id', 'Product.sku', 'ProductName.name'),
			'conditions' => array('OR' => array('ProductCategory.category_id' => $categoryID)),
			'order' => array('ProductCategory.sort'),
			'joins' => array(
				array(
					'table' => 'product_categories',
			        'alias' => 'ProductCategory',
			        'type' => 'LEFT',
			        'conditions'=> array('ProductCategory.product_id = Product.id')
				)
			)
		));
		
		return $records;
		
	}

	/**
	 * Get products primary category.
	 *
	 * @param int $productID
	 * @return array $record
	 * @access public
	 */
	public function getPrimaryCategory($productID)
	{
		$this->Category->unbindProducts();
		$this->Category->bindName($this->Category, 0, false);
		
		$record = $this->Category->find('first', array(
			'joins' => array(
				array(
					'table' => 'product_categories',
			        'alias' => 'ProductCategory',
			        'type' => 'INNER',
			        'conditions'=> array('ProductCategory.category_id = Category.id')
				)
			),
			'conditions' => array(
				'ProductCategory.product_id' => $productID, 
				'ProductCategory.primary' => 1
			)
		));
		
		return $record;
		
	}
	
}


