<?php

/**
 * Catalog Controller
 * 
 */
class CatalogController extends AppController
{
	/**
	 * An array containing the class names of models this controller uses.
	 *
	 * @var array
	 * @access public
	 */
	public $uses = array('Product');
	
	/**
	 * An array containing the names of helpers this controller uses.
	 *
	 * @var mixed A single name as a string or a list of names as an array.
	 * @access public
	 */
	public $helpers = array('Text');
	
	/**
	 * Percentage of record set that must be met for price range filter to be used.
	 *
	 * @var int
	 * @access private
	 */
	private $requiredPricesInFilterRange = 80;
	
	/**
	 * An array containing prices ranges for the filter.
	 * Which one is used depends on the prices in the record set.
	 *
	 * @var array
	 * @access private
	 */
	private $priceFilterRanges = array(
		1 => array(
			array(0  => 5), 
			array(5  => 10),
			array(10 => 15),
			array(15 => 20),
			array(20 => 25)
		),
		2 => array(
			array(0  => 10),
			array(10 => 20),
			array(20 => 30),
			array(30 => 40),
			array(40 => 50)
		),
		3 => array(
			array(0  => 10),
			array(10 => 20),
			array(20 => 30),
			array(30 => 40),
			array(40 => 50),
			array(50 => 60),
			array(60 => 70),
			array(70 => 80),
			array(80 => 90),
			array(90 => 100)
		),
		4 => array(
			array(0   => 20),
			array(20  => 40),
			array(40  => 60),
			array(60  => 80),
			array(80  => 100),
			array(100 => 120),
			array(120 => 140),
			array(140 => 160),
			array(160 => 180),
			array(180 => 200)
		),
		5 => array(
			array(0   => 50),
			array(50  => 100),
			array(100 => 150),
			array(150 => 200),
			array(200 => 250),
			array(250 => 300),
			array(300 => 350),
			array(350 => 400),
			array(400 => 450),
			array(450 => 500)	
		),
		6 => array(
			array(0   => 100),
			array(100 => 200),
			array(200 => 300),
			array(300 => 400),
			array(400 => 500),
			array(500 => 600),
			array(600 => 700),
			array(700 => 800),
			array(800 => 900),
			array(900 => 1000)
		),
		7 => array(
			array(0    => 200),
			array(200  => 400),
			array(400  => 600),
			array(600  => 800),
			array(800  => 1000),
			array(1000 => 1200),
			array(1200 => 1400),
			array(1400 => 1600),
			array(1600 => 1800),
			array(1800 => 2000)
		),
		8 => array(
			array(0    => 500),
			array(500  => 1000),
			array(1000 => 1500),
			array(1500 => 2000),
			array(2000 => 2500),
			array(2500 => 3000),
			array(3000 => 3500),
			array(3500 => 4000),
			array(4000 => 4500),
			array(4500 => 5000)
		)
	);
	
	/**
	 * Cached attributes.
	 * 
	 * @var array
	 * @access private
	 */
	private $attributes = array();
	
	private $selectedFilters = array();
	
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
		$this->Security->disabled = true;

		$this->set('inCatalog', true);
	}
	
	/**
	 * Default action for parsing url and determining correct action.
	 *
	 * @return void
	 * @access public
	 */
	public function index()
	{
                $this->set('phpExecutionTime1',  microtime(true));
                $this->Product->bindMeta($this->Product, 1, false);
		
		$language = 1;
		
		$urlSplit = explode('/', $this->params['url']['url']);
		
		// Remove any named parameters
		foreach ($urlSplit as $key => $urlPart)
		{
			if (strpos($urlPart, ':') !== false)
			{
				unset($urlSplit[$key]);
			}
		}
		
		$urlCount = count($urlSplit);
		
		$firstKey = $urlSplit[0];
		$lastKey = $urlSplit[$urlCount - 1];
		
		// Look for top category		
		$this->Category->bindName($this->Category, 0, false);			
		
		$parentID = 0;
		
		$this->Category->unbindModel(array('hasAndBelongsToMany' => array('Product')), false);
		
		foreach ($urlSplit as $k => $part)
		{
			$category = $this->Category->find('first', array('conditions' => array(
				'CategoryName.language_id' => $language,
				'CategoryName.url' => $part,
				'Category.parent_id' => $parentID
			)));
			
			$categoryID = $parentID = $category['Category']['id'];
			
		}
		
		if (!empty($categoryID))
		{
			return $this->setAction('view_category', $categoryID);
		}
		
		// Looking at brand page
		if (($urlCount == 2) && ($firstKey == 'brands') && !empty($lastKey))
		{
			return $this->setAction('view_manufacturer', $lastKey);
		}

		// Looking at brand page
		if (($urlCount == 1) && ($firstKey == 'brands'))
		{
			return $this->setAction('display_manufacturers');
		}

		// Looking at product
		if (($urlCount == 1) || ($urlCount == 2))
		{	
			// First look for product url by language
			$productID = $this->Product->ProductMeta->field('product_id', array(
				'ProductMeta.language_id' => $language,
				'ProductMeta.url' => $lastKey
			));
			
			if (!empty($productID))
			{
				return $this->setAction('view_product', $productID);
			}
		}
		
		$this->cakeError('error404');
		
	}
	
	/**
	 * View manufacturer page.
	 * 
	 * @return void
	 * @access public 
	 */
	public function view_manufacturer($manuKey)
	{
		$record = $this->Product->Manufacturer->findByUrl($manuKey);
		
		$conditions = array(
			'Product.manufacturer_id' => $record['Manufacturer']['id'],
			'OR' => array(
				array('Product.visibility' => 'catalog'),
				array('Product.visibility' => 'catalogsearch')
			)
		);
		$this->_listProducts($conditions, false);
		
		$this->set('record', $record);
		
		$pageName = 'Products by ' . $record['Manufacturer']['name'];
		$this->set('title_for_layout', $pageName);
		$this->addCrumb('', $pageName);
		
		$this->setLastPage($record['Manufacturer']['name']);
		
	}
	
	/**
	 * Show product search page
	 * 
	 * @param string $keyword
	 * @return void
	 * @access public 
	 */
	public function search()
	{
		$keyword = $this->getSearchKeyword();
		
		if (empty($keyword))
		{
			$this->redirect('/');
		}
		
		
		$this->Product->bindDescription($this->Product, 1, false);
		$this->Product->unbindModel(array('hasAndBelongsToMany' => array('Category')), false);

		$conditions = array(
			'ProductName.name LIKE' => '%' . $keyword . '%',
			'Product.all_skus LIKE' => '%' . $keyword . '%',
			'ProductDescription.keywords LIKE' => '%' . $keyword . '%'
		);
		
		if (Configure::read('Catalog.search_manufacturers'))
		{
			$manuCondition = array('Manufacturer.name LIKE' => '%' . $keyword . '%');
			$conditions = array('OR' => array_merge($conditions, $manuCondition));
		}

		$conditions = array($conditions, array('OR' => array(
			array('Product.visibility' => 'search'),
			array('Product.visibility' => 'catalogsearch')
		)));

		$this->_listProducts($conditions, false);
		
		$this->set('keyword', $keyword);
		$this->set('title_for_layout', 'Search results for "' . $keyword . '"');
		
		$this->addCrumb('/search?search=' . $keyword, 'Search results for "' . $keyword . '"');
		
		$this->setLastPage();
		
	}
	
	/**
	 * View products on special offer.
	 * 
	 * @return void
	 * @access public
	 */
	public function specials($catid = 0)
	{

		$catid = intval($catid);

		$categoryTree = $this->Category->generatetreelist(null, '{n}.Category.id', '{n}.CategoryName.name', ' - ', 1);

		$this->set(compact('catid', 'categoryTree'));

		$this->Product->unbindModel(array('hasAndBelongsToMany' => array('Category')), false);
		
		$conditions = $this->Product->getSpecialPriceProductsConditions();

		if($catid !== 0){
			$conditions['ProductCategory.category_id'] = $catid;
		}
		
		$this->_listProducts($conditions, false);
		
		$this->set('title_for_layout', 'Special Offers');
		
		$this->setLastPage();
		
	}
	
	/**
	 * View category page
	 * Shows category information and 
	 * - child categories if $id contains children categories, or...
	 * - products in category in not
	 * 
	 * @param int $id
	 * @return void
	 * @access public 
	 */
	public function view_category($categoryID = null)
	{
             
            $this->notEmptyOr404($categoryID);
		
		$this->Category->bindDescription($this->Category, 0, false);
		$this->Category->unbindModel(array('hasAndBelongsToMany' => array('Product')), false);
		
		$record = $this->Category->find('first', array('conditions' => array(
			'Category.id' => $categoryID,
			'Category.active' => 1
		)));
                $this->notEmptyOr404($record);
		
		$path = $this->Category->getPath($categoryID, null, 0);

		
                
		$pathMinusSelf = $path;
		array_pop($pathMinusSelf);
		
		foreach ($pathMinusSelf as $p)
		{
			if (empty($p['Category']['active']))
			{
				$this->cakeError('error404');
			}
		}
		
		$this->_getCategoryCrumbs($path);
		
		$this->Cookie->write('Catalog.last_viewed_category', $categoryID);
		
		
		$childCategoryCount = $this->Category->childCount($categoryID, true);
		$this->set('childCategoryCount', $childCategoryCount);
		
		
		$this->setLastPage($record['CategoryName']['name']);
		
		$this->set('topCategoryRecord', $path[0]);
		$this->set('topCategoryID', $path[0]['Category']['id']);
		//$this->set('record', $record);
		$this->set('title_for_layout', $this->get_category_page_title($record, $path));
		$this->set('categoryPath', $path);
		$this->set('categoryPathIDs', Set::extract('/Category/id', $pathMinusSelf));
		$this->set('categoryID', $categoryID);
		$this->set('metaKeywords', $record['CategoryDescription']['meta_keywords']);
		$this->set('metaDescription', $record['CategoryDescription']['meta_description']);
		
		$this->Product->unbindModel(array('hasAndBelongsToMany' => array('Category')), false);
		$this->Product->bindModel(array('hasOne' => array('ProductCategory')), false);
		
                
                  
                //  Uncomment to fix real number products per category
                
                 
                $pathIDs = Set::extract('{n}.Category.id', $path); //stole from popcorn
                  
                // Category.product_counter is incorrect so another kludge fix - TJP 23/10/15
                foreach ($this->_categories as $k => $cat)
		{
			if ($cat['Category']['id'] == $pathIDs[0])
			{
				$family = $cat['children'];
				break;
			}
		}
                foreach ($family as $k => $cat)  
                {
                    $catID = $cat['Category']['id']; 
                    $catsToUse[] = $catID; 
                    $catCountAll[$catID] = 0;

                    $catCountAll[$catID] = $catCountAll[$catID] + $this->Product->find('count', array('conditions' => array(
                                'ProductCategory.category_id' => $catID,
                                'Product.active' => 1
                        )));

                }
		
                $actualNumProducts = array_sum($catCountAll);
                $record['Category']['product_counter'] = $actualNumProducts;
                 // add ending block comment tag above if commenting                

                
                $this->set('record', $record);
                
		$conditions = array(
			'ProductCategory.category_id' => $categoryID,
			'OR' => array(
				array('Product.visibility' => 'catalog'),
				array('Product.visibility' => 'catalogsearch')
			)
		);
				
		$this->selectedFilters = array(
			'price' => $this->getSelectedFilterValues('price', 'price_ranges'),
			'manufacturer' => $this->getSelectedFilterValues('man', 'manufacturers'),
			'attribute' => $this->getSelectedFilterValues('attr', 'attributes')
		);
		
		$showLanding = Configure::read('Catalog.show_manu_landing_page');
		
		if ($showLanding && empty($this->selectedFilters['price']) && empty($this->selectedFilters['manufacturer']) && empty($this->selectedFilters['attribute']))
		{
			$this->display_manufacturer_landing_page($categoryID, $conditions);		
		}
		else if (!empty($childCategoryCount) && !empty($record['Category']['display_as_landing']))
		{
			$this->display_category_landing_page($categoryID, $path);
		}
		else
		{
			$this->display_product_list($path, $pathMinusSelf, $conditions);
		}
		
	}
	
	private function get_category_page_title($record, $path)
	{
		if (empty($record['CategoryDescription']['page_title']))
		{
			$pageTitle = '';
			foreach ($path as $cat)
			{
				$catTitle = (!empty($cat['CategoryDescription']['page_title'])) ? $cat['CategoryDescription']['page_title'] : $cat['CategoryName']['name'];
				$pageTitle .= $catTitle . ' > ';
			}
			$pageTitle = substr($pageTitle, 0, -3);
		}
		else
		{			
			$pageTitle = $record['CategoryDescription']['page_title'];
		}

		return $pageTitle;

	}
	
	private function display_manufacturer_landing_page($categoryID, $conditions)
	{
		$this->Product->bindAttributes($this->Product, false);
			
		$preFilterProducts = $this->getProductsForAttributeLookup($conditions);
		$allManufacturers = $this->Product->Manufacturer->find('all', array(
			'conditions' => array('Manufacturer.id' => array_unique(Set::extract('{n}.Product.manufacturer_id', $preFilterProducts)))
		));
		
		$this->Category->bindFeaturedManufacturers(false);
		$this->Category->CategoryFeaturedManufacturer->bindModel(array('belongsTo' => array('Manufacturer')));
		$featuredManufacturers = $this->Category->CategoryFeaturedManufacturer->find('all', array(
			'conditions' => array('CategoryFeaturedManufacturer.category_id' => $categoryID),
			'sort' => array('CategoryFeaturedManufacturer.sort')
		));
		
		// Get filter values for display in product list panels
		$this->getAvailablePriceFilterValues($conditions, array(), array());
		$this->getAvailableManufacturerFilterValues($conditions, array());

		$allAttributes = $this->getAvailableAttributes($preFilterProducts);		
		$availableAttributes = $this->getAttributesForView($allAttributes, $conditions, array(), array());

		$this->set(compact('allManufacturers', 'featuredManufacturers', 'allAttributes', 'availableAttributes'));
	
		$this->render('cat_landing');

	}

	public function display_manufacturers()
	{
		$this->Product->bindAttributes($this->Product, false);
			
		$allManufacturers = $this->Product->Manufacturer->find('all');

		$this->set(compact('allManufacturers'));

	}

	private function display_category_landing_page($categoryID, $path)
	{
		$categoryFeaturedProducts = $this->Product->getFeaturedProducts($categoryID);

		$pathMinusSelf = $path;
		array_pop($pathMinusSelf);

		$childCategories = $this->get_category_family($path, $pathMinusSelf);

		//debug($childCategories);
			
		//$childCategories = $this->Category->children($categoryID, true, null, null, null, null, 0);
		$childCategories = $this->Category->find('all', array(
			'conditions' => array(
				'Category.parent_id' => $categoryID,
				'Category.active' => 1,
			),
		));
		//$this->Category->removeInactiveCategories($childCategories);
		$this->set('childCategories', $childCategories);

		$baseUrl = '/';
		
		foreach ($path as $cat)
		{
			$baseUrl .= $cat['CategoryName']['url'] . '/';
		}
		
		$this->set(compact('categoryFeaturedProducts', 'baseUrl'));
		$this->render('view_category');
		
	}

	private function display_product_list($path, $pathMinusSelf, $conditions)
	{
           
                $rootCatUrl = $path[0]['CategoryName']['url'];
		$this->set('rootCatUrl', $rootCatUrl);

		$pathIDs = Set::extract('{n}.Category.id', $path);
		$this->set('pathIDs', $pathIDs);
		
		// Get all categories from top level
                // popcorn had this wrong it was doing all cats
		foreach ($this->_categories as $k => $cat)
		{
			if ($cat['Category']['id'] == $pathIDs[0])
			{
				$family = $cat['children'];
				break;
			}
		}

		$baseUrl = '/';
		
		foreach ($pathMinusSelf as $cat)
		{
			$baseUrl .= $cat['CategoryName']['url'] . '/';
		}
		
		$this->set('baseUrl', $baseUrl);
		$this->set('categoryFamily', $family);
		
		$this->_listProducts($conditions);

                $this->set('phpExecutionTime2',  microtime(true));
		
		$this->render('view_subcategory');

	}

	private function get_category_family($path, $pathMinusSelf){
		$rootCatUrl = $path[0]['CategoryName']['url'];
		$this->set('rootCatUrl', $rootCatUrl);

		$pathIDs = Set::extract('{n}.Category.id', $path);
		$this->set('pathIDs', $pathIDs);
		
		// Get all categories from top level
		foreach ($this->_categories as $k => $cat)
		{
			if ($cat['Category']['id'] == $pathIDs[0])
			{
				$family = $cat['children'];
				break;
			}
		}

		$baseUrl = '/';
		
		foreach ($pathMinusSelf as $cat)
		{
			$baseUrl .= $cat['CategoryName']['url'] . '/';
		}
		
		$this->set('baseUrl', $baseUrl);
		$this->set('categoryFamily', $family);
	}

	
	/**
	 * View product.
	 * 
	 * @param int $id
	 * @return void
	 * @access public
	 */
	public function view_product($id)
	{
            
            $record = $this->Product->getViewProductData($id);
		//debug($record['RelatedProduct']);

		//$neighbors = $this->Product->find('neighbors', array(
		//	'field' => 'id',
		//	'value' => $record['Product']['id'],
		//	'contain' => array('ProductMeta'),
		//));

		//$this->set(compact('neighbors'));
		
		$this->notEmptyOr404($record);
		
		$this->_addToRecentlyViewed($id);
		if (!empty($record['MainCategory']['id']))
		{
			$categoryID = $record['MainCategory']['id'];
		}
		
		$lastViewedCategory = $this->Cookie->read('Catalog.last_viewed_category');
		if (!empty($lastViewedCategory))
		{
			$inCategories = Set::extract('ProductCategory.{n}.category_id', $record);
			if (in_array($lastViewedCategory, $inCategories))
			{
				$categoryID = $lastViewedCategory;
			}
		}
		
		if (!empty($categoryID))
		{
			$this->Category->unbindModel(array('hasAndBelongsToMany' => array('Product')));
			$category = $this->Category->findById($categoryID);
			$path = $this->Category->getPath($categoryID, null, 0);
			
			$pathsCopy = $path;
			array_pop($pathsCopy);
			
			$this->_getCategoryCrumbs($path);
			
			$this->set('topCategoryID', $path[0]['Category']['id']);
			$this->set('topCategoryRecord', $path[0]);
			$this->set('categoryPath', $path);
			$this->set('categoryPathIDs', Set::extract('/Category/id', $pathsCopy));
			$this->set('categoryID', $categoryID);

			$this->get_category_family($path, $pathsCopy);
		}
		
		$this->addCrumb('#', $record['ProductName']['name']);
		
		$this->loadModel('ProductOptionStock');
		$allVars = $this->ProductOptionStock->bindPrice(0, false)->getAvailableStockByProduct($id, true);
		
		
		
		if (!empty($allVars))
		{
			$varImages = false;

			foreach ($allVars as $v)
			{
				if (!empty($v['ProductOptionStockImage'][0]))
				{
					$varImages = true;
				}
				
				if (!empty($this->params['url']['var']) && ($this->params['url']['var'] == $v['ProductOptionStock']['id']))
				{
					$preSelectedVariation = $v;
				}
				if ($record['Product']['default_product_option_stock_id'] == $v['ProductOptionStock']['id'])
				{
					$chosenDefaultStock = $v;
				}
			}
			
			if (!empty($preSelectedVariation))
			{
				$stock = $preSelectedVariation;
			}
			else if (!empty($chosenDefaultStock))
			{
				$stock = $chosenDefaultStock;
			}
			else
			{
				$stock = $allVars[0];
			}
			
			if ($varImages)
			{
				$defaultVarValues = explode('-', $stock['ProductOptionStock']['value_ids']);
				$this->set('loadDefaultVar', true);
				$this->set('defaultVarValues', $defaultVarValues);
				
				$this->overwriteBaseProductData($record, $stock);
			}
			
			/*
			 * Get available values of first option
			 *
			 */
			$availableFirstValueIDs = array();
			$allValueIDs = Set::extract('{n}.ProductOptionStock.value_ids', $allVars);
			foreach ($allValueIDs as $valueIDs)
			{
				$t = explode('-', $valueIDs);
				if (!in_array($t[0], $availableFirstValueIDs))
				{
					$availableFirstValueIDs[] = $t[0];
				}
			}


			$firstOptionValues = array();

			foreach ($record['ProductOption'][0]['ProductOptionValue'] as $k => $value)
			{
				if (in_array($value['ProductOptionValue']['id'], $availableFirstValueIDs))
				{
					$firstOptionValues[] = $value;
				}
			}

			$record['ProductOption'][0]['ProductOptionValue'] = $firstOptionValues;
			
			
			/*if (count($record['ProductOption']) == 1)
			{
				$stockRekeyed = array();
				
				foreach ($allVars as $var)
				{
					$vid = $var['ProductOptionStock']['value_ids'];
					$stockRekeyed[$vid] = $var;
				}
				
				foreach ($record['ProductOption'][0]['ProductOptionValue'] as $k => $value)
				{
					$vid = $value['ProductOptionValue']['id'];
					$price = $stockRekeyed[$vid]['ProductOptionStockPrice']['active_price'];
					$record['ProductOption'][0]['ProductOptionValue'][$k]['CustomOptionValueName']['name'] .= ' &pound;' . $price;
				}			
			}*/

		}
		
		

		
		$this->set('optionsStock', $allVars);
		$this->set('metaKeywords', $record['ProductMeta']['keywords']);
		$this->set('metaDescription', $record['ProductMeta']['description']);
		$this->set('record', $record);

		$this->setViewProductPageTitle($record);
		
	}
	
	private function overwriteBaseProductData(&$record, $stock)
	{
		$record['Product']['stock_lead_time'] = $stock['ProductOptionStock']['stock_lead_time'];
		$record['Product']['stock_base_qty'] = $stock['ProductOptionStock']['stock_base_qty'];
		
		$record['ProductOptionStock'] = $stock['ProductOptionStock'];
		$record['ProductImage'] = $stock['ProductOptionStockImage'];
		
		$record['ProductPrice']['base_price'] = $stock['ProductOptionStockPrice']['base_price'];
		$record['ProductPrice']['active_price'] = $stock['ProductOptionStockPrice']['active_price'];
		
		if (isset($stock['ProductOptionStockPrice']['trade_price']))
		{
			$record['ProductPrice']['trade_price'] = $stock['ProductOptionStockPrice']['trade_price'];
		}
		
	}



	public function ajax_get_available_vars($productID, $optionID, $valueIDs)
	{
		$this->loadModel('ProductOptionStock');

		$ids = $this->ProductOptionStock->find('all', array(
			'fields' => array('option_ids', 'value_ids'),
			'conditions' => array(
				'ProductOptionStock.product_id' => $productID,
				'ProductOptionStock.available' => 1,
				'ProductOptionStock.value_ids LIKE' => $valueIDs . '-%'
			)
		));

		$options = Set::extract('{n}.ProductOptionStock.option_ids', $ids);
		$options = explode('-', $options[0]);

		$updatedKey = array_search($optionID, $options);
		$nextKey = $updatedKey + 1;
		$nextOptionID = $options[$nextKey];
	
		$values = Set::extract('{n}.ProductOptionStock.value_ids', $ids);

		/*
		 * Get individual values
		 *
		 */
		$availValues = array();
		foreach ($values as $valuesStr)
		{
			$t = explode('-', $valuesStr);
			$vID = $t[$nextKey];
			if (!in_array($vID, $availValues))
			{
				$availValues[] = $vID;
			}
		}
		
		$this->Product->bindOptions($this->Product);
		$nextValues = $this->Product->ProductOption->ProductOptionValue->getOptionValues($nextOptionID, array(
			'currency_id' => Configure::read('Runtime.active_currency'),
			'get_prices' => false,
			'get_names' => true,
			'value_id' => $availValues
		));

		$nextValueIDs = Set::combine($nextValues, '{n}.ProductOptionValue.id', '{n}.CustomOptionValueName.name');

		echo json_encode($nextValueIDs);

		exit;
		
	}

	
	/**
	 * AJAX.
	 * Take product ID and selected option values,
	 * respond with stock status and pricing of selected values.
	 * 
	 * @param int $id
	 * @return void
	 * @access public
	 */	
	public function ajax_get_price_and_stock($id)
	{
		if (empty($this->params['url']['data']['Basket'][0]))
		{
			exit('missing basket data');
		}
		
		$this->Product->unbindModel(array(
			'belongsTo' => array('Manufacturer', 'MainCategory'),
			'hasOne' => array('ProductName', 'ProductMeta'),
			'hasMany' => array('ProductImage'),
			'hasAndBelongsToMany' => array('Category')
		), false);
		
		$record = $this->Product->find('first', array('conditions' => array('Product.id' => $id)));
		
		$this->Product->bindOptions($this->Product);
		
		if ($record['Product']['type'] == 'grouped')
		{
			$this->Product->bindGroupedProducts($this->Product);
			$groupedProductIDs = $this->Product->GroupedProduct->getProductList($id);
		}
		
		$checkID = (!empty($groupedProductIDs)) ? $groupedProductIDs : $id;
		
		$postedOptions = $this->Product->getPostedProductOptions($this->params['url']['data']['Basket'][0]);
		
		if (!$this->Product->ProductOption->allRequiredValuesPosted($checkID, $postedOptions))
		{
			return $this->render('/elements/catalog/view_product/please_select_options');
		}
		
		$values = array();
		foreach ($postedOptions as $optionID => $valueID)
		{
			$values[] = $this->Product->ProductOption->ProductOptionValue->getOptionValues($optionID, array(
				'currency_id' => Configure::read('Runtime.active_currency'),
				'get_prices' => true,
				'value_id' => $valueID
			));
		}
		
		$ids = implode('-', Set::extract('/ProductOptionValue/id', $values));
		
		$this->loadModel('ProductOptionStock');
		$this->ProductOptionStock->bindPrice(0);
		
		if (Configure::read('Catalog.use_tiered_customer_pricing') && Configure::read('Customer.group_id'))
		{
			$this->ProductOptionStock->bindCustomerDiscounts(false, Configure::read('Customer.group_id'));
		}
		
		$stock = $this->ProductOptionStock->getStockByProductAndValues($id, $ids);
		
		if (!empty($stock))
		{
			$stockID = $stock['ProductOptionStock']['id'];
			$record['Product']['stock_in_stock'] = $stock['ProductOptionStock']['stock_in_stock'];
			$record['Product']['stock_lead_time'] = $stock['ProductOptionStock']['stock_lead_time'];
			$record['Product']['stock_base_qty'] = $stock['ProductOptionStock']['stock_base_qty'];
			
			$record['ProductPrice']['base_price'] = $stock['ProductOptionStockPrice']['base_price'];
			$record['ProductPrice']['active_price'] = $stock['ProductOptionStockPrice']['active_price'];
			
			if (!empty($stock['ProductOptionStockDiscount']))
			{
				$record['ProductPriceDiscount'] = $stock['ProductOptionStockDiscount'];
			}
			
			$record['ProductOptionStock'] = $stock['ProductOptionStock'];
			$record['ProductOptionStockPrice'] = $stock['ProductOptionStockPrice'];
			
			$this->set('optionSku', $stock['ProductOptionStock']['sku']);
			
		}
		else
		{
			exit('Error: Variation not found.');
		}
		
		// $record['StockStatus'] = $stock['StockStatus'];
		// $record['Product']['stock_status_id'] = $stock['Product']['stock_status_id'];
		
		//$record['ProductPrice']['base_price'] = $stock['ProductOptionStockPrice']['base_price'];
		//$record['ProductPrice']['active_price'] = $stock['ProductOptionStockPrice']['active_price'];
		
		/*
		if (isset($stock['ProductOptionStockPrice']['trade_price']))
		{
			$record['ProductPrice']['trade_price'] = $stock['ProductOptionStockPrice']['trade_price'];
		}
		else
		{
			$record['ProductPrice']['trade_price'] = $stock['ProductOptionStockPrice']['active_price'];
		}
		*/

		$this->set('record', $record);
		$this->render('/elements/catalog/view_product/price_and_stock');
		
	}
	
	/**
	 * AJAX.
	 * Get image box (view and thumb nav) for selected stock ID.
	 *
	 * @param int $stockID
	 * @return void
	 * @access public
	 */
	public function ajax_get_var_images_box($stockID)
	{
		$this->loadModel('ProductOptionStock');
		
		$this->ProductOptionStock->bindPrice(0);
		$stock = $this->ProductOptionStock->getByID($stockID, true);

		if (!empty($stock['ProductOptionStockImage']))
		{
			$this->set('images', $stock['ProductOptionStockImage']);
		}
		else
		{
			$record = $this->Product->find('first', array('conditions' => array('Product.id' => $stock['Product']['id'])));
			$this->set('images', $record['ProductImage']);
		}		
		
		$this->render('/elements/catalog/view_product/images');

	}
	
	/**
	 * Common method for listing products.
	 * 
	 * @param array $conditions
	 * @return void
	 * @access private
	 */
	private function _listProducts($conditions, $inCategory = true)
	{
		$this->Product->unbindModel(array('hasAndBelongsToMany' => array('Category')), false);		
		$this->Product->bindModel(array('hasOne' => array('ProductCategory')), false);
		$this->Product->bindAttributes($this->Product, false);
		
                //xdebug_break();
		if (isset($this->params['url']['removesort']))
		{
			$this->Cookie->write('Catalog.sortby', '');
			$this->Cookie->write('Catalog.orderby', '');
		}
		
		if (!empty($this->params['url']['sortby']) && (strpos($this->params['url']['sortby'], '-') !== false))
		{
			$sortSplit = explode('-', $this->params['url']['sortby']);
			$this->params['url']['sortby'] = $sortSplit[0];
			$this->params['url']['orderby'] = $sortSplit[1];
		}
		
		$this->_getUrlParamAndSetCookie('sortby');
		$this->_getUrlParamAndSetCookie('orderby');
		
                // Returns how to sort which can be 'default', 'name' or 'price' - TJP 16/10/15
                $sortBy = $this->_getSortBy($inCategory);
		
                $orderByCookie = $this->Cookie->read('Catalog.orderby');
		$orderBy = (!empty($orderByCookie) && in_array($orderByCookie, array('asc', 'desc'))) ? $orderByCookie : 'asc';
		
		$this->paginate['fields'] = array(
			'Product.*', 'ProductName.*', 'ProductPrice.*', 'ProductMeta.*',
			'Manufacturer.*', 'AttributeSet.*'
		);
		
		if ($inCategory)
		{
			$this->paginate['fields'][] = 'ProductCategory.*';
		}
		
		$this->paginate['group'] = array('Product.id');
		
		if (Configure::read('Catalog.show_product_short_description_on_list'))
		{
			$this->Product->bindDescription($this->Product, 0, false);
			$this->paginate['fields'][] = 'ProductDescription.*';
		}
		
		//$this->_getLimit();
		$this->paginate['order'] = array($sortBy['field'] => strtoupper($orderBy));
		
		// $selectedManufacturers = $this->getSelectedFilterValues('man', 'manufacturers');
		// $selectedPrices = $this->getSelectedFilterValues('price', 'price_ranges');
		// $selectedAttributes = $this->getSelectedFilterValues('attr', 'attributes');
		
		if (empty($this->selectedFilters))
		{
			$this->selectedFilters = array(
				'price' => $this->getSelectedFilterValues('price', 'price_ranges'),
				'manufacturer' => $this->getSelectedFilterValues('man', 'manufacturers'),
				'attribute' => $this->getSelectedFilterValues('attr', 'attributes')
			);
		}
		
		$this->set('selectedManufacturers', $this->selectedFilters['manufacturer']);
		
		// Get price and manufacturer filter conditions
		$filterConditions = array();
		$filterConditions['price'] = $this->_getPriceRangeFilterConditions($this->selectedFilters['price']);
		$filterConditions['manufacturer'] = $this->_getManufacturerFilterConditions($this->selectedFilters['manufacturer']);
		
		// Get selected attribute values and index into array
		$selectedAttributeValues = $this->indexAttributeFilterValues($this->selectedFilters['attribute']);
		
		// Get conditions for all attribute filter values
		$filterConditions['attribute'] = $this->_getAttributeFilterConditions($selectedAttributeValues);
		
		// Get filter values for display in product list panels
		$this->getAvailablePriceFilterValues($conditions, getAllExcluding($filterConditions, 'price'), $this->selectedFilters['price']);
		$this->getAvailableManufacturerFilterValues($conditions, getAllExcluding($filterConditions, 'manufacturer'));
		
                
                // The product attribute system is totally fucked so remove for launch then fix later. TJP
		//$preFilterProducts = $this->getProductsForAttributeLookup($conditions);		
		//$attributes = $this->getAvailableAttributes($preFilterProducts);
		//$attributesToView = $this->getAttributesForView($attributes, $conditions, $filterConditions, $selectedAttributeValues);
                $attributes = []; // Feel the hack *flowing* through you - Ben Kenobi
                $attributesToView = [];    
                
		// If there are qty 1+ customer group discount tiers we can display them on the list
		$this->Product->bindSingleQtyDiscount(false);
		$this->paginate['fields'][] = 'SingleQtyProductPriceDiscount.discount_amount';
		
                $origCatID = $conditions['ProductCategory.category_id'];
                 $conditions = array(
                                        'ProductCategory.category_id' => $origCatID,
                                        'AND' => array(
                                               // array('Product.visibility' => 'catalog'), // is active?
                                               // array('Product.visibility' => 'catalogsearch'),
                                                array('Product.active' => 1)
                                            )
                                               );
            
                // For Pete's sake avert your eyes - TJP 19/10/15
		if(empty($this->viewVars['record']['Category']['parent_id']) && $inCategory)
                {
                    //xdebug_break();
                     //this really is insanity
                    $this->_getLimit();
                    $junk = $this->paginate('Product', $this->getFinalConditions($conditions, $filterConditions));
                   
                   // $this->loadModel('ProductOptionStock');
                   // $allRecords = $this->ProductOptionStock->addVarsToProducts($allRecords, 'singleqty');
                    
                    
                    $catCountAll = [];
                    $catsToUse = [];
                    
                    // Get accurate count of products in parent category
                    foreach ($this->viewVars['categoryFamily'] as $k => $cat)                           
                    {
                        $catID = $cat['Category']['id']; 
                        $catsToUse[] = $catID; 
                        $catCountAll[$catID] = 0;
                        
                        $catCountAll[$catID] = $catCountAll[$catID] + $this->Product->find('count', array('conditions' => array(
                                    'ProductCategory.category_id' => $catID,
                                    'Product.active' => 1
                            )));
                        
                    }
                    
                    // Uncomment to fix real number products per category
                    
                    $actualNumProducts = array_sum($catCountAll);
                    // Check total product count is correct as paginate version is broken
                    if($this->params['paging']['Product']['count'] != $actualNumProducts)
                    {
                        //xdebug_break ();
                        $this->params['paging']['Product']['count'] = array_sum($catCountAll);
                        
                        $currentPage = $this->params['paging']['Product']['page'];
                        $numRecordsOnPage = ($actualNumProducts - ($currentPage - 1) * $this->paginate['limit']);
                        if($numRecordsOnPage > $this->paginate['limit']) 
                        {
                        $numRecordsOnPage = $this->paginate['limit'];
                            
                        }
                        
                    } else 
                    {
                        $currentPage = $this->params['paging']['Product']['page'];
                        $numRecordsOnPage = $this->params['paging']['Product']['current'];
           
                    }   
                    
                    // add ending block comment tag above if commenting
                    
                  
                    //save paging params
                    $allCatPaging = $this->params['paging'];
                    $paramsUrl = $this->params['url'];
             
                    if(isset($this->passedArgs['page']))
                    {
                        $passedArgsPage = $this->passedArgs['page'];
                    }
                    
                    //continue
                    
                    $allRecordsIndexStart = ($currentPage - 1) * $this->paginate['limit'];
                    $allRecordsIndexEnd = $currentPage * $this->paginate['limit'] - 1;

                                       
                    $numCats = count($catsToUse);
                    
                    
                    // xdebug_break();
                    $numProducts = 0;
                    $startCat = [];
                    foreach ($catsToUse as $k => $catID)                           
                    {
                        $numProducts = $numProducts + $catCountAll[$catID];
                        if($numProducts > $allRecordsIndexStart)
                        {
                            $startCat = $catID;
                            break;
                        }
                        
                    }
                    
        
                      $pOffset = $catCountAll[$startCat] - ($numProducts -  ($this->paginate['limit']) * ($currentPage-1));
                    
                    for($i =0; $i <= $numCats; $i++)
                    {
                        if($catsToUse[0] == $startCat)
                        {
                            break;
                        }
                        $tmp1 = array_shift($catsToUse);
                        $tmp2 = array_shift($catCountAll);
                    }
                     
                    $idx = 0;
                    $catCountTotal = array_sum($catCountAll);
                    while ($catCountTotal > $allRecordsIndexEnd)
                    {
                        if(count($catsToUse) == 0)
                        {
                            break;
                        }
                        $tmp1 = array_pop($catsToUse);
                        $tmp2 = array_pop($catCountAll);
                        $catCountTotal = array_sum($catCountAll);
                        $idx = $idx + 1;
                    }
                        
                    array_push($catsToUse, $tmp1);
                    array_push($catCountAll, $tmp2);
                   
                   
                    $allRecords = []; 
                   //xdebug_break();
                    $this->paginate['offset'] = $pOffset;
                    // loop over subcategories
                    foreach ($catsToUse as $k => $catID)                           
                    {
                        $subConditions = array(
                                        'ProductCategory.category_id' => $catID,
                                        'OR' => array(
                                                array('Product.visibility' => 'catalog'), // is active?
                                                array('Product.visibility' => 'catalogsearch')
                                                     )
                                               );

                        unset($this->params['paging']);
                        $this->params['url'] = [];
                        $this->passedArgs['page'] = '1';
                        
                        $subCatRecords = $this->paginate('Product', $this->getFinalConditions($subConditions, $filterConditions));
                        $allRecords = array_merge($allRecords, $subCatRecords);
                        unset($this->paginate['offset']);    
                    }
                    
                    
                    //pop
                    $this->params['paging'] = $allCatPaging;
                    $this->params['url'] = $paramsUrl;
                    if(isset($passedArgsPage))
                    {
                       $this->passedArgs['page'] = $passedArgsPage;
                    }
                    
                    
                    
                    $junk = $this->paginate('Product', $this->getFinalConditions($conditions, $filterConditions));
                    $this->loadModel('ProductOptionStock');
                    $allRecords = $this->ProductOptionStock->addVarsToProducts($allRecords, 'singleqty');
               
                    $records = array_slice ($allRecords, 0 , $numRecordsOnPage);
                    
                    // Uncomment to fix real number products per category
                    $this->params['paging']['Product']['count'] = $actualNumProducts; 
                 }
                 else 
                 {
                    //This returns the array of products to displayed and their order -( commented) TJP 16/10/15
                    $this->_getLimit();
                    $records = $this->paginate('Product', $this->getFinalConditions($conditions, $filterConditions));
                    $this->loadModel('ProductOptionStock');
                    $records = $this->ProductOptionStock->addVarsToProducts($records, 'singleqty');
		
                 }
                 
                
		
		$this->set('allAttributes', $attributes);
		$this->set('availableAttributes', $attributesToView);
		$this->set('products', $records);
		$this->set('sortBy', $sortBy['key']);
		$this->set('orderBy', $orderBy);
		$this->set('sortByOrderByCombined', $sortBy['key'] . '-' . $orderBy);
		
	}


	// Attribute Filter -------------------------------------------

	/**
	 * Get available attributes for current product record set.
	 * 
	 * @param array $products
	 * @return array
	 * @access private
	 */
	private function getAvailableAttributes($products)
	{
		if (empty($products))
		{
			return array();
		}

		$ids = $this->Product->AttributeSet->AttributeSetsAttribute->find('list', array(
			'fields' => array('AttributeSetsAttribute.id', 'AttributeSetsAttribute.attribute_id'),
			'conditions' => array('AttributeSetsAttribute.attribute_set_id' => array_unique(Set::extract('{n}.Product.attribute_set_id', $products)))
		));
		
		$preFilterProductAttributeValues = array();
		
		// Determine all attributes used by pre filtered product set so we only ever display these.
		foreach ($products as $k => $product)
		{
			foreach ($product['AttributeValue'] as $k => $value)
			{
				$preFilterProductAttributeValues[] = $value['id'];
			}
		}

		$attributes = $this->Product->AttributeSet->Attribute->getAttributes($ids);
		$this->attributes = $this->Product->AttributeSet->Attribute->addValuesToAttributes($attributes, $preFilterProductAttributeValues);
		
		return $this->attributes;
		
	}
	
	private function getAttributesForView($attributes, $conditions, $filterConditions, $selectedAttributeValues)
	{
		$attributeIDs = Set::extract('{n}.Attribute.id', $attributes);
		
		$c2 = getAllExcluding($filterConditions, 'attribute');
		
		$attributesToView = array();
		
		foreach ($attributeIDs as $attributeID)
		{
			$c = $conditions;
			$s = $selectedAttributeValues;
			unset($s[$attributeID]);
			
			$c3 = $this->_getAttributeFilterConditions($s);
			
			if (!empty($c2))
			{
				$c[] = $c2;
			}
			
			if (!empty($c3))
			{
				$c[] = $c3;
			}
			
			$v = Set::extract('{n}.AttributeValue.{n}.id', $this->getProductsForAttributeLookup($c));
			
			$attributesToView[$attributeID] = $this->getAvailableAttributesFilterValuesForView($attributeID, $v);
			
		}
		
		return $attributesToView;
	
	}
	
	private function getProductsForAttributeLookup($conditions)
	{
		$this->Product->unbindModel(array(
			//'hasOne' => array('ProductDescription', 'ProductMeta', 'Category'),
			'hasOne' => array('ProductMeta', 'Category'),
			'hasMany' => array('ProductImage')
		));
		
		$products = $this->Product->find('all', array(
			'fields' => array('Product.id', 'Product.manufacturer_id', 'Product.attribute_set_id'),
			'conditions' => $conditions,
			'group' => array('Product.id')
		));
		
		return $products;
		
	}
		
	/**
	 * Get available attribute filter values for displaying on product list panel.
	 * 
	 * @param array $attributeIDs
	 * @return void
	 * @access private
	 */
	private function getAvailableAttributesFilterValuesForView($attributeID, $valueIDs)
	{
		$valueCount = array();
		
		foreach ($valueIDs as $values)
		{
			foreach ($values as $value)
			{
				if (empty($valueCount[$value]))
				{
					$valueCount[$value] = 0;
				}
				$valueCount[$value]++;
			}
		}
		
		$attribute = $this->attributes[$attributeID];
		
		foreach ($attribute['AttributeValue'] as $k2 => $value)
		{
			$attribute['AttributeValue'][$k2]['AttributeValue']['count'] = 0;
			if (!empty($valueCount[$value['AttributeValue']['id']]))
			{
				$attribute['AttributeValue'][$k2]['AttributeValue']['count'] = $valueCount[$value['AttributeValue']['id']];
			}
			else
			{
				unset($attribute['AttributeValue'][$k2]);
			}
		}
			
		return $attribute;
		
	}

	/**
	 * Get attribute filter conditions.
	 * 
	 * @param array $attributes
	 * @return mixed
	 * @access private
	 */
	private function _getAttributeFilterConditions($attributes)
	{
		$conditions = array();
		$filterAttributes = array();
		
		foreach ($attributes as $attributeID => $values)
		{
			if (empty($attributes[$attributeID]))
			{
				$filterAttributes[$attributeID] = array();
			}
			
			foreach ($values as $valueID)
			{
				$filterAttributes[$attributeID][] = $valueID;
			}
		}
		
		$this->loadModel('AttributeValuesProduct');
		
		$productIDs = array();
		
		foreach ($filterAttributes as $attributeID => $valueIDs)
		{
			$productIDs[] = $this->AttributeValuesProduct->find('list', array(
				'fields' => array('AttributeValuesProduct.id', 'AttributeValuesProduct.product_id'),
				'conditions' => array('AttributeValuesProduct.attribute_value_id' => $valueIDs),
				'group' => array('AttributeValuesProduct.product_id')
			));
		}
		
		if (empty($productIDs))
		{
			return false;
		}
		
		if (count($productIDs) > 1)
		{
			$p = call_user_func_array('array_intersect', array_values($productIDs));
			return array('Product.id' => $p);
		}
		
		return array('Product.id' => $productIDs[0]);
		
	}

	/**
	 * Using selected attribute filter values passed in GET or cookie,
	 * create a well-formed array representing value IDs inside attribute IDs.
	 * 
	 * @param array $values
	 * @return $attributes array
	 * @access private
	 */
	private function indexAttributeFilterValues($values)
	{
		// Init array
		$attributes = array();
		
		foreach ($values as $k => $value)
		{
			// Split attribute filter value string into component parts
			$temp = explode(':', $value);
			
			// Component parts
			$valueName = $temp[0];
			$attributeID = intval($temp[1]);
			$valueID = intval($temp[2]);
			
			// If attribute ID or value ID missing, ignore
			if (empty($attributeID) || empty($valueID))
			{
				continue;
			}
			
			// Create attribute ID array key if not already in array
			if (empty($attributes[$attributeID]))
			{
				$attributes[$attributeID] = array();
			}
			
			// Add value
			$attributes[$attributeID][] = $valueID;
			
		}
		
		return $attributes;
		
	}
	

	// Price Filter ----------------------------------------------------------------

	/**
	 * Get price range filter conditions.
	 * 
	 * @param array $ranges
	 * @return $priceConditions
	 * @access private
	 */	
	private function _getPriceRangeFilterConditions($ranges)
	{
		$priceConditions = array();
		
		foreach ($ranges as $k => $range)
		{
			if (strpos($range, '+') !== false)
			{
				$from = substr($range, 0, -1);
				
				if (!is_numeric($from))
				{
					continue;
				}
				
				$priceConditions[] = array('ProductPrice.active_price >=' => $from);
				
			}
			else if (strpos($range, '-') !== false)
			{	
				$split = explode('-', $range);
				$from = intval($split[0]);
				$to = intval($split[1]);
				
				if (!is_numeric($from) || !is_numeric($to))
				{
					continue;
				}
				
				$priceConditions[] = array('AND' => array(
					'ProductPrice.active_price >=' => $from,
					'ProductPrice.active_price <=' => $to,
				));
				
			}
			else if ($range == 'special')
			{
				$priceConditions[] = array('ProductPrice.on_special' => 1);
			}
		}
		
		if (!empty($priceConditions))
		{
			$priceConditions = array('OR' => $priceConditions);
		}
		
		return $priceConditions;
		
	}

	/**
	 * Get available price filter values for displaying on product list panel.
	 * 
	 * @param array $mainConditions
	 * @param array $otherFilterConditions
	 * @return void
	 * @access private
	 */
	private function getAvailablePriceFilterValues($mainConditions, $otherFilterConditions, $selectedPrices)
	{	
		$ranges = $this->priceFilterRanges;
		
		$this->Product->unbindModel(array(
			//'hasOne' => array('ProductDescription', 'ProductMeta'),
			'hasOne' => array('ProductMeta'),
			'hasMany' => array('ProductImage')
		));
		
		$conditions = $mainConditions;
		if (!empty($otherFilterConditions))
		{
			$conditions[] = $otherFilterConditions;
		}
		
		$records = $this->Product->find('all', array(
			'fields' => array('ProductPrice.active_price', 'ProductPrice.on_special'),
			'conditions' => $conditions,
			'group' => array('Product.id')
		));
		
		if (empty($records))
		{
			return false;
		}
		
		// Get all product prices
		$prices = array();
		foreach ($records as $k => $v)
		{
			$prices[] = $v['ProductPrice']['active_price'];
		}
		
		sort($prices);
		
		$pricesInRanges = array_pad(array(), count($ranges) + 1, 0);
		unset($pricesInRanges[0]);
		
		// Get prices per range
		foreach ($ranges as $k => $range)
		{
			$inRange = count($range) - 1;
			$rangeEnd = array_values($range[$inRange]);
			$rangeEnd = $rangeEnd[0];
			
			foreach ($prices as $price)
			{
				if ($price <= $rangeEnd)
				{
					$pricesInRanges[$k]++;
				}
			}
		}
		
		$totalPrices = count($prices);
		
		$highestPercentage = 0;
		$highestRange = 0;
		
		$useRange = $this->getUseRangeFromCookie();
		
		// If we don't already have a range to use (from cookie) get it
		if (empty($useRange))
		{	
			foreach ($pricesInRanges as $k => $inRange)
			{
				$percentInRange = (($inRange / $totalPrices) * 100);
				if ($percentInRange >= $this->requiredPricesInFilterRange)
				{
					$useRange = $k;
					break;
				}
				
				if ($percentInRange > $highestPercentage)
				{
					$highestPercentage = $percentInRange;
					$highestRange = $k;
				}
				
			}
		}
		
		if (!empty($selectedPrices))
		{
			$this->Cookie->write('Catalog.use_filter_price_range', serialize(array(
				'pass' => $this->params['pass'],
				'useRange' => $useRange
			)));
		}
		
		$range = (!empty($useRange)) ? $ranges[$useRange] : $ranges[$highestRange];
		
		$rangeCount = array_pad(array(), count($range), 0);
		$rangeMatches = 0;
		
		// Get prices per XX - YY in range set
		foreach ($prices as $price)
		{
			foreach ($range as $k => $v)
			{
				$from = key($v);
				$to = $v[key($v)];
				
				if (($price >= $from) && ($price <= $to))
				{
					$rangeCount[$k]++;
					$rangeMatches++;
					continue;
				}
			}
		}
		
		$aboveRange = 0;
		
		if ($rangeMatches < $totalPrices)
		{
			$aboveRange = $totalPrices % $rangeMatches;
		}
		
		// Any products on special offer?
		//$onSpecial = 0;
		//foreach ($records as $k => $v)
		//{
		//	if (!empty($v['ProductPrice']['on_special']))
		//	{
		//		$onSpecial++;
		//		break;
		//	}
		//}

		$onSpecial = $this->Product->find('count', array(
			'fields' => 'DISTINCT ProductPrice.product_id',
			'conditions' => array(
				'AND' => array(
					array('ProductPrice.on_special' => 1),
					array($conditions))
				)));
		
		$this->set(compact('range', 'rangeCount', 'aboveRange', 'onSpecial'));
		
	}
	
	/**
	 * Get filter price range set to use, if cookies set.
	 * 
	 * @return bool
	 * @access private
	 */
	private function getUseRangeFromCookie()
	{
		$usePriceRangeCookie = $this->Cookie->read('Catalog.use_filter_price_range');
		
		if (!empty($usePriceRangeCookie))
		{
			$usePriceRangeCookie = unserialize($usePriceRangeCookie);
		}
		
		if (empty($usePriceRangeCookie['pass']) || ($usePriceRangeCookie['pass'] != $this->params['pass']) || empty($usePriceRangeCookie['useRange']))
		{
			return null;
		}
		
		return $usePriceRangeCookie['useRange'];
		
		/*
		$usePriceRangeCookie = $this->Cookie->read('Catalog.use_filter_price_range');
		$priceRangeFilteredByCookie = $this->Cookie->read('Catalog.price_ranges_filter');
		
		if (!empty($usePriceRangeCookie))
		{
			$usePriceRangeCookie = unserialize($usePriceRangeCookie);
		}
		
		if (!empty($priceRangeFilteredByCookie))
		{
			$priceRangeFilteredByCookie = unserialize($priceRangeFilteredByCookie);
		}
		
		if (empty($usePriceRangeCookie) || empty($priceRangeFilteredByCookie))
		{
			return null;
		}
			
		if (empty($usePriceRangeCookie['pass']) || ($usePriceRangeCookie['pass'] != $this->params['pass']) || empty($usePriceRangeCookie['useRange']))
		{
			return null;
		}
		
		if (empty($priceRangeFilteredByCookie['pass']) || ($priceRangeFilteredByCookie['pass'] != $this->params['pass']) || empty($priceRangeFilteredByCookie['priceinc']))
		{
			return null;
		}
		
		return $usePriceRangeCookie['useRange'];
		*/
		
	}


	// Manufacturer Filter --------------------------------------------------------------------------------------------------------------------------
	// ----------------------------------------------------------------------------------------------------------------------------------------------

	/**
	 * Get manufacturers to filter products by.
	 * 
	 * @param array $conditions
	 * @return array $useManufacturers
	 * @access private
	 */
	private function _getManufacturerFilterConditions($manufacturers)
	{
		$manufacturerConditions = array();
		
		foreach ($manufacturers as $k => $man)
		{
			if ($man == 'other')
			{
				$manufacturerConditions[] = array('Manufacturer.id' => NULL);
			}
			else
			{
				$manufacturerConditions[] = array('Manufacturer.url' => $man);
			}			
		}
		
		if (!empty($manufacturerConditions))
		{
			$manufacturerConditions = array('OR' => $manufacturerConditions);
		}
		
		return $manufacturerConditions;
		
	}
	
	/**
	 * Get available manufacturer filter values for displaying on product list panel.
	 * 
	 * @param array $mainConditions
	 * @param array $otherFilterConditions
	 * @return void
	 * @access private
	 */
	private function getAvailableManufacturerFilterValues($mainConditions, $otherFilterConditions)
	{
		$this->Product->unbindModel(array(
			//'hasOne' => array('ProductDescription', 'ProductMeta'),
			'hasOne' => array('ProductMeta'),
			'hasMany' => array('ProductImage')
		));
		
		$conditions = $mainConditions;
		if (!empty($otherFilterConditions))
		{
			$conditions[] = $otherFilterConditions;
		}
		
		$records = $this->Product->find('all', array(
			'fields' => array('Manufacturer.id', 'Manufacturer.name', 'Manufacturer.url'),
			'conditions' => $conditions,
			'group' => array('Product.id')
		));
		
		$manufacturers = array();
		$noManufacturer = 0;
		
		if (!empty($records))
		{
			foreach ($records as $k => $v)
			{
				if (!empty($v['Manufacturer']['name']))
				{
					$id  = $v['Manufacturer']['id'];
					
					if (empty($manufacturers[$id]))
					{
						$manufacturers[$id] = array('url' => '', 'name' => '', 'count' => 0);
					}
					
					$manufacturers[$id]['url'] = $v['Manufacturer']['url'];
					$manufacturers[$id]['name'] = $v['Manufacturer']['name'];				
					$manufacturers[$id]['count']++;
					
				}
				else
				{
					$noManufacturer++;
				}
			}
		}
		
		usort($manufacturers, array("CatalogController", "sortManufacturers"));
		
		$this->set(compact('manufacturers', 'noManufacturer'));
		
	}



	// Misc Filter ----------------------------------------------------------------------------------------------------------------------------------
	// ----------------------------------------------------------------------------------------------------------------------------------------------

	/**
	 * Add any filter conditions to main conditions and return final conditions.
	 * 
	 * @param array $conditions
	 * @param array $filterConditions
	 * @return array
	 * @access private
	 */
	private function getFinalConditions($conditions, $filterConditions)
	{
		if (!empty($filterConditions['price']))
		{
			$conditions[] = $filterConditions['price'];
		}
		
		if (!empty($filterConditions['manufacturer']))
		{
			$conditions[] = $filterConditions['manufacturer'];
		}
		
		if (!empty($filterConditions['attribute']))
		{
			$conditions[] = $filterConditions['attribute'];
		}

		return $conditions;
		
	}
	
	/**
	 * Get selected filter values of a given filter from url and cookie.
	 * Write values back to cookie.
	 * 
	 * @param string $key
	 * @param string $name
	 * @return array
	 * @access private
	 */
	private function getSelectedFilterValues($key, $name)
	{
		if (!empty($this->params['url']['remove']) && ($this->params['url']['remove'] == $name))
		{
			$this->Cookie->delete('Catalog.' . $name . '_filter');
			$this->set('selected_' . $name . '_filter_values', array());
			return array();
		}
		
		$selectedValues = array();
		$exValues = array();

		if (!empty($this->params['url'][$key . 'ex']))
		{
			foreach ($this->params['url'][$key . 'ex'] as $k => $v)
			{
				$exValues[] = $v;
			}
		}
		
		$cookie = $this->Cookie->read('Catalog.' . $name . '_filter');
		
		if (!empty($cookie))
		{
			$cookieValues = unserialize($cookie);
			
			if (!empty($cookieValues['pass']) && ($cookieValues['pass'] == $this->params['pass']) && !empty($cookieValues[$key . 'inc']))
			{
				foreach ($cookieValues[$key . 'inc'] as $k => $v)
				{
					if (!in_array($v, $exValues))
					{
						$selectedValues[] = $v;
					}
				}
			}
		}
		
		if (!empty($this->params['url'][$key . 'inc']))
		{
			foreach ($this->params['url'][$key . 'inc'] as $k => $v)
			{
				if (!in_array($v, $exValues))
				{
					$selectedValues[] = $v;
				}
			}
		}
		
		$selectedValues = array_unique($selectedValues);
		
		// Limit manufacturer to 1
		if ($key == 'man' && (count($selectedValues) > 1))
		{
			$selectedValues = array(end($selectedValues));
		}
		
		$this->Cookie->write('Catalog.' . $name . '_filter', serialize(array(
			'pass' => $this->params['pass'],
			$key . 'inc' => $selectedValues
		)));
		
		$this->set('selected_' . $name . '_filter_values', $selectedValues);
		
		return $selectedValues;
		
	}
	
	/**
	 * Set view product page title
	 * 
	 * @param array $record
	 * @return void
	 * @access private
	 */
	private function setViewProductPageTitle($record)
	{
		if (!empty($record['ProductMeta']['page_title']))
		{
			$pageTitle = $record['ProductMeta']['page_title'];
		}
		else
		{
			$pageTitle = $record['ProductName']['name'];
                        // Edit by Will to remove Manufacturer Name from Page Title.
//			if (!empty($record['Manufacturer']['name']))
//			{
//				$pageTitle .= ' by ' . $record['Manufacturer']['name'];
//			}
		}
		
		$this->set('title_for_layout', $pageTitle);
		
	}
	
	/**
	 * Overridden last page setter.
	 * 
	 * @param $string [optional]
	 * @return void
	 * @access protected
	 */
	protected function setLastPage($name = null)
	{
		$catalogActions = array(
			'view_category' => $name,
			'search' => 'your search results',
			'specials' => 'the special offers',
			'view_manufacturer' => $name
		);
		
		if ($this->name == 'Catalog' && in_array($this->action, array_keys($catalogActions)))
		{
			$url = $this->params['url']['url'];
			
			if ($this->action == 'search')
			{
				$url .= '?search=' . $this->getSearchKeyword();
			}
			
			$this->Session->write('Site.last_page', array(
				$url => $catalogActions[$this->action]
			));
			
			return;			
		}
		
	}
	
	/**
	 * Get search string from url either as get param or named param.
	 * 
	 * @return string
	 * @access private
	 */
	private function getSearchKeyword()
	{
		$keyword = '';
		
		if (!empty($this->params['url']['search']))
		{
			$keyword = trim($this->params['url']['search']);
		}
		else if (!empty($this->params['named']['search']))
		{
			$keyword = trim($this->params['named']['search']);
		}
		
		return $keyword;
	}
	
	/**
	 * Add viewing product to recentl viewed cookie.
	 * 
	 * @param int $id
	 * @return void
	 * @access private
	 */
	private function _addToRecentlyViewed($id)
	{
		$recentlyViewed = unserialize($this->Cookie->read('Catalog.recently_viewed'));
				
		if (empty($recentlyViewed))
		{
			$recentlyViewed = array($id);
		}
		else
		{
			$unique = array_unique($recentlyViewed);
			if (count($unique) >= Configure::read('Catalog.recently_viewed_limit'))
			{
				$split = array_chunk($unique, Configure::read('Catalog.recently_viewed_limit') - 1);
				$recentlyViewed = $split[0];
			}
			array_unshift($recentlyViewed, $id);
		}
		
		$recentlyViewed = array_unique($recentlyViewed);
		$this->Cookie->write('Catalog.recently_viewed', serialize($recentlyViewed));
		
	}
		
	/**
	 * Static method for sorting manufacturer filter array.
	 * 
	 * @param array $a
	 * @param array $b
	 * @return int
	 * @access static
	 */
	public static function sortManufacturers($a, $b)
	{
		if ($a['name'] == $b['name'])
		{
            return 0;
        }
        return ($a['name'] > $b['name']) ? +1 : -1;
	}

	/**
	 * Get/set per page limit.
	 * 
	 * @return void
	 * @access private 
	 */
	private function _getLimit()
	{
        
                $productsPerPage = Configure::read('Catalog.default_products_per_page');
		$productsPerPageLabel = $productsPerPage;
		
		if (!empty($this->params['url']['display']))
		{
			if ($this->params['url']['display'] == 'all')
			{
				$productsPerPage = 999;
				$productsPerPageLabel = 'All';
			}
			else if (in_array($this->params['url']['display'], Configure::read('Catalog.products_per_page_options')))
			{
				$productsPerPage = $this->params['url']['display'];
				$productsPerPageLabel = $productsPerPage;
				
				$this->Cookie->write('Catalog.products_per_page', $productsPerPage);
				$this->Cookie->write('Catalog.products_per_page_label', $productsPerPageLabel);
				
			}
		}
		else
		{
			$cookieProductsPerPage = $this->Cookie->read('Catalog.products_per_page');
			$cookieProductsPerPageLabel = $this->Cookie->read('Catalog.products_per_page_label');
			
			if (!empty($cookieProductsPerPage) && !empty($cookieProductsPerPageLabel))
			{
				$productsPerPage = $this->Cookie->read('Catalog.products_per_page');
				$productsPerPageLabel = $this->Cookie->read('Catalog.products_per_page_label');
			}
		}
		
		$this->paginate['limit'] = $productsPerPage;
		
		$this->set('showingPerPage', $productsPerPageLabel);
		
	}
	
	/**
	 * Get sort by field for product list.
	 * 
	 * @param bool $inCategory
	 * @return string
	 * @access private
	 */
	private function _getSortBy($inCategory)
	{
		$sortByCookie  = $this->Cookie->read('Catalog.sortby');	
		
		$validSorts = array(
			0 => array('key' => 'name',  'field' => 'ProductName.name'),
			1 => array('key' => 'price', 'field' => 'ProductPrice.active_price')
		);
		
		if ($inCategory)
		{
			array_unshift($validSorts, array('key' => 'default', 'field' => 'ProductCategory.sort, ProductName.name'));
		}
		
		$this->set('validSorts', $validSorts);
		
		$validSortKeys = Set::extract('{n}.key', $validSorts);
		$sortByKey = (!empty($sortByCookie) && in_array($sortByCookie, $validSortKeys)) ? $sortByCookie : $validSortKeys[0];
		
		foreach ($validSorts as $k => $sort)
		{
			if ($sort['key'] == $sortByKey)
			{
				return $sort;
			}
		}
	}
	
	/**
	 * Set cookies based on url params.
	 * 
	 * @param array $keys [optional]
	 * @return void
	 * @access private
	 */
	private function _getUrlParamAndSetCookie($key)
	{		
		if (isset($this->params['url'][$key]))
		{
			if (!empty($this->params['url'][$key]))
			{
				$this->Cookie->write('Catalog.' . $key, $this->params['url'][$key]);
			}
			else
			{
				$this->Cookie->delete('Catalog.' . $key);
			}
		}
	}
	
	/**
	 * Get category breadcrumbs.
	 * 
	 * @param array $path
	 * @return void
	 * @access private
	 */
	private function _getCategoryCrumbs($path)
	{
		$url = '';
		foreach ($path as $k => $cat)
		{
			$url .= '/' . $cat['CategoryName']['url'];
			$this->addCrumb($url, $cat['CategoryName']['name']);
		}
	}
        
        private function addConstantToVector($v1, $valToAdd)
        {
            
            $vOut = array();
            $acc = 0;
            
            //for ($i = 0; $i < count($v1); $i++) {
           //     $vOut[$i] = $v1[$i] + $v2[$i];
            //}            
            foreach($v1 as $k => $arrayValue)
            {
               
                    $vOut[$k] = $arrayValue + $valToAdd;
                
            }
            return $vOut;
        }        
	
}



