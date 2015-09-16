<?php

/**
 * Category Model
 * 
 */
class Category extends AppModel
{
	/**
	 * Detailed list of hasAndBelongsToMany associations.
	 *
	 * @var array
	 * @access public
	 */
	public $hasAndBelongsToMany = array(
		'Product' => array(
			'joinTable' => 'product_categories',
			'with' => 'ProductCategory',
			'unique' => true,
			'foreignKey' => 'category_id',
			'associationForeignKey' => 'product_id'
		)
	);
	
	/**
	 * List of behaviors to load when the model object is initialized.
	 *
	 * @var array
	 * @access public
	 */
	public $actsAs = array('Tree');
	
	/**
	 * The column name(s) and direction(s) to order find results by default.
	 *
	 * @var string
	 * @access public
	 */
	public $order = array(
		'ISNULL(Category.sort_order)',
		'Category.sort_order'
	);
	
	/**
	 * Category list.
	 *
	 * @var array
	 * @access private
	 */
	private $list = array();
		
	/**
	 * Called after each find operation. Can be used to modify any results returned by find().
	 * Return value should be the (modified) results.
	 *
	 * @param mixed $results The results of the find operation
	 * @param boolean $primary Whether this model is being queried directly (vs. being queried as an association)
	 * @return mixed Result of the find operation
	 * @access public
	 */
	public function afterFind($results, $primary)
	{
		if (!empty($primary))
		{	
			foreach ($results as $k => $result)
			{
				if (empty($result['Category']['id']))
				{
					continue;
				}

				$results[$k]['Category']['header_root_path'] = null;
				$results[$k]['Category']['header_web_path'] = null;
				
				$results[$k]['Category']['list_root_path'] = null;
				$results[$k]['Category']['list_web_path'] = null;

				if (!empty($result['Category']['img_header_ext']))
				{
					$file = $result['Category']['id'] . '.' . $result['Category']['img_header_ext'];
					$headerPath = Configure::read('Images.category_header_path') . $file;
					if (file_exists(WWW_ROOT . $headerPath))
					{
						$results[$k]['Category']['header_root_path'] = WWW_ROOT . $headerPath;
						$results[$k]['Category']['header_web_path'] = '/' . $headerPath;
					}
				}
				
				if (!empty($result['Category']['img_list_ext']))
				{
					$file = $result['Category']['id'] . '.' . $result['Category']['img_list_ext'];
					$listPath = Configure::read('Images.category_list_path') . $file;
					if (file_exists(WWW_ROOT . $listPath))
					{
						$results[$k]['Category']['list_root_path'] = WWW_ROOT . $listPath;
						$results[$k]['Category']['list_web_path'] = '/' . $listPath;
					}
				}
	
			}
		}

		return $results;
		
	}
	
	/**
	 * Called before each save operation, after validation. Return a non-true result
	 * to halt the save.
	 *
	 * @return boolean True if the operation should continue, false if it should abort
	 * @access public
	 */
	public function beforeSave($options = array())
	{
		Cache::delete('categories');
		return true;
	}
	
	/**
	 * Called after each successful save operation.
	 *
	 * @param boolean $created True if this save created a new record
	 * @access public
	 */
	public function afterSave()
	{
		$this->saveProductCategorySortOrders();
		
		if (!empty($this->data['ProductDelete']))
		{
			foreach ($this->data['ProductDelete'] as $productID => $delete)
			{
				if (!empty($delete))
				{
					$this->ProductCategory->deleteAll(array(
						'ProductCategory.category_id' => $this->id,
						'ProductCategory.product_id' => $productID
					));
				}
			}
		}
		
		if (!empty($this->data['ProductAdd']))
		{
			foreach ($this->data['ProductAdd'] as $productID => $add)
			{
				if (!empty($add))
				{
					$this->ProductCategory->create();
					$this->ProductCategory->save(array('ProductCategory' => array(
						'category_id' => $this->id,
						'product_id' => $productID
					)));
				}
			}
		}
		
		if (!empty($this->data['CategoryFeaturedProduct']))
		{
			$this->bindFeaturedProducts($this, false);
			$this->CategoryFeaturedProduct->deleteAll(array(
				'CategoryFeaturedProduct.category_id' => $this->id
			));
			foreach ($this->data['CategoryFeaturedProduct'] as $productID => $add)
			{
				if (!empty($add))
				{
					$this->CategoryFeaturedProduct->create();
					$this->CategoryFeaturedProduct->save(array('CategoryFeaturedProduct' => array(
						'category_id' => $this->id,
						'product_id' => $productID
					)));
				}
			}
		}
		
		if (!empty($this->data['CategoryFeaturedManufacturer']))
		{
			$cfm = $this->data['CategoryFeaturedManufacturer'];
			
			if (!empty($cfm['hash']))
			{
				$this->saveFeaturedManufacturerSortOrders($cfm['hash']);
			}
			
			if (!empty($cfm['add_featured_manufacturer']))
			{
				$exists = $this->CategoryFeaturedManufacturer->findByManufacturerId($cfm['add_featured_manufacturer']);
				
				if (empty($exists))
				{
					$this->CategoryFeaturedManufacturer->create();
					$this->CategoryFeaturedManufacturer->save(array('CategoryFeaturedManufacturer' => array(
						'category_id' => $this->id,
						'manufacturer_id' => $cfm['add_featured_manufacturer'],
						'sort' => 999
					)));
				}
			}
		}
		
		if (!empty($this->data['FeaturedManufacturerDelete']))
		{
			foreach ($this->data['FeaturedManufacturerDelete'] as $manID => $delete)
			{
				if (!empty($delete))
				{
					$this->CategoryFeaturedManufacturer->deleteAll(array(
						'CategoryFeaturedManufacturer.category_id' => $this->id,
						'CategoryFeaturedManufacturer.manufacturer_id' => $manID
					));
				}
			}
		}

		$originalID = $this->id;
		$originalData = $this->data;

		$this->recursive = 1;
		$this->bindName($this, null, false);
		$children = $this->children($this->id, true, null, null, null, null, 1);
		foreach($children as $child){
			$this->id = $child[$this->alias]['id'];
			$this->read();
			$this->saveAll();
		}

		$this->id = $originalID;
		$this->data = $originalData;
		
	}
	
	/**
	 * Called after every deletion operation.
	 *
	 * @param boolean $cascade If true records that depend on this record will also be deleted
	 * @return boolean True if the operation should continue, false if it should abort
	 * @access public
	 */
	public function beforeDelete($cascade = true) 
	{
		Cache::delete('categories');
		return true;
	}
	
	
	private function saveFeaturedManufacturerSortOrders($hash)
	{
		$cats = explode(';', $hash);
		if (empty($cats))
		{
			return;
		}
		
		foreach ($cats as $k => $cat)
		{					
			$prods = explode('&', $cat);
			if (!empty($prods))
			{
				foreach ($prods as $sort => $ids)
				{
					$catAndProd = explode('[]=', $ids);
					$catID = substr($catAndProd[0], 8);
					$manID = $catAndProd[1];
					
					$this->CategoryFeaturedManufacturer->updateAll(
						array('CategoryFeaturedManufacturer.sort' => $sort),
						array('CategoryFeaturedManufacturer.category_id' => $catID, 'CategoryFeaturedManufacturer.manufacturer_id' => $manID)
					);
				}
			}
		}

	}
	
	
	/**
	 * Get list of root categories.
	 *
	 * @return void
	 * @access public
	 */
	public function getRootCategories()
	{
		exit('working?');
		
		$this->bindDescription(0);
		$records = $this->find('all', array(
			'conditions' => array('Category.parent_id' => 0)
		));
		
		return Set::combine($records, '{n}.Category.id', '{n}.CategoryDescription.name');
	}
	
	/**
	 * Get list of featured categories.
	 * 
	 */
	public function getFeatured()
	{
		$this->bindDescription($this, 0);
		$this->unbindProducts();
		
		return $this->find('all', array(
			'conditions' => array('Category.featured' => 1)
		));
	}
	

	

	
	
	/**
	 * Bind category name(s) to categories
	 *
	 * @param object $model
	 * @param mixed $languageID
	 * @param bool $reset
	 * @return void
	 * @access public
	 */
	public function bindName($model, $languageID = null, $reset = false)
	{
		$this->bind($model, 'CategoryName', $languageID, $reset);
	}
	
	/**
	 * Bind category description(s) to categories.
	 *
	 * @param object $model
	 * @param mixed $languageID
	 * @param bool $reset
	 * @return void
	 * @access public
	 */
	public function bindDescription($model, $languageID = null, $reset = false)
	{
		$this->bind($model, 'CategoryDescription', $languageID, $reset);
	}
	
	/**
	 * Bind category images(s) to categories.
	 *
	 * @param object $model
	 * @param mixed $languageID
	 * @param bool $reset
	 * @return void
	 * @access public
	 */
	public function bindImage($model, $languageID = null, $reset = false)
	{
		$this->bind($model, 'CategoryImage', $languageID, $reset);
	}
	
	/**
	 * Bind featured category products images(s) to categories.
	 *
	 * @param object $model
	 * @param bool $reset
	 * @return void
	 * @access public
	 */
	public function bindFeaturedProducts($model, $reset = false)
	{
		$bind = array('hasMany' => array());
		$bind['hasMany']['CategoryFeaturedProduct'] = array(
			'className' => 'CategoryFeaturedProduct',
			'foreignKey' => 'category_id'
		);
		$model->bindModel($bind, $reset);
	}
	
	/**
	 * Bind featured manufacturers.
	 *
	 * @param bool $reset
	 * @return void
	 * @access public
	 */
	public function bindFeaturedManufacturers($reset = false)
	{
		$bind = array('hasMany' => array());
		$bind['hasMany']['CategoryFeaturedManufacturer'] = array(
			'className' => 'CategoryFeaturedManufacturer',
			'foreignKey' => 'category_id'
		);
		$this->bindModel($bind, $reset);
	}
	
	/**
	 * Bind products to categories.
	 *
	 * @return void
	 * @access public
	 */
	public function bindProducts()
	{
		$this->bindModel(array('hasAndBelongsToMany' => array(
			'Product' => array(
				'joinTable' => 'product_categories',
				'with' => 'ProductCategory',
				'unique' => true,
				'foreignKey' => 'category_id',
				'associationForeignKey' => 'product_id'
			)
		)));
	}
	
	/**
	 * Unbind products from categories.
	 *
	 * @return void
	 * @access public
	 */
	public function unbindProducts($reset = false)
	{
		$this->unbindModel(array('hasAndBelongsToMany' => array('Product')), $reset);
	}
	
	/**
	 * Remove inactive categories from array of categories.
	 *
	 * @param array $categories
	 * @return void
	 * @access public
	 */	
	public function removeInactiveCategories(&$categories)
	{
		foreach ($categories as $k => $cat)
		{
			if (empty($cat['Category']['active']))
			{
				unset($categories[$k]);
			}
		}
	}
	
	/**
	 * Get list of name => id of all categories in given language.
	 * 
	 * @param int $languageID
	 * @return array
	 * @access public
	 */
	public function getThreadedCategoriesList($languageID = null)
	{
		$this->unbindProducts();
		$this->bindName($this, 0, false);
		
		$categories = $this->find('threaded');
		
		$this->_addCategoriesToList($categories);
		
		return $this->list;
		
	}
	
	/**
	 * Get list of name => id of all categories in given language.
	 * 
	 * @param array $categories
	 * @param array $array [optional]
	 * @return void
	 * @access private
	 */
	private function _addCategoriesToList($categories, &$array = null)
	{	
		if (empty($array))
		{
			$array =& $this->list;
		}
		
		foreach ($categories as $k => $v)
		{	
			$id = $v['Category']['id'];
			$name = $v['CategoryName']['name'];
			
			$array[$name] = array();
			$array[$name]['id'] = $id;
			
			if (!empty($v['children']))
			{
				$this->_addCategoriesToList($v['children'], $array[$name]);
			}
		}
		
	}
	
}

