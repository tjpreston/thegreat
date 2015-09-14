<?php

/**
 * Category Helper
 * 
 */
class CategoryHelper extends AppHelper
{
	/**
	 * An array containing the names of helpers this controller uses. 
	 *
	 * @var mixed A single name as a string or a list of names as an array.
	 * @access public
	 */
	public $helpers = array('Form', 'Html');
	
	/**
	 * HTML UL representing category tree.
	 * 
	 * @var string
	 * @access private
	 */
	private $tree = '';
	
	/**
	 * Array of categories product is assigned to.
	 * 
	 * @var array
	 * @access private
	 */
	private $productCategories = array();
	
	/**
	 * Iterator var for generating checkbox field names.
	 * 
	 * @var int
	 * @access private
	 */
	private $nodeCount = 0;
	
	/**
	 * Categories to initially check in tree.
	 * 
	 * @var array
	 * @access private
	 */
	private $checkedCats = array();
	
	/**
	 * IDs of categories in path.
	 * 
	 * @var array
	 * @access private
	 */
	private $pathIDs = array();
	
	/**
	 * Current open category ID
	 * 
	 * @var int
	 * @access private
	 */
	private $openCategoryID = 0;
	
	private $topCategoryID = 0;

	public $topCategoryRecord = array();
	
	private $topActiveCategory = 0;
	
	private $options = array();
	
	private $_mainCategoryID = 0;
	
	private $url = array();
	
	public $showHomeLink = true;

	public $showSaleLink = false;

	public $showAllResults = false;

	// Name of model to show checkboxes for..
	public $modelName = 'ProductCategory.ProductCategory';
	
	public function generateSitemapXML($categories, $level = 0)
	{		
		$count = count($categories);
		$i = 0;

		foreach ($categories as $k => $category)
		{
			$myCatUrl = $category['CategoryName']['url'];

			$this->url[$level] = $myCatUrl;
			$url = implode('/', $this->url);

			$this->addUrlElement($url);
			
			if (!empty($category['children']))
			{				
				$this->generateSitemapXML($category['children'], ($level + 1));
			}
			
			if ($count == ($i + 1))
			{
				unset($this->url[$level]);
			}
			
			$i++;
      
		}

	}

	private function addUrlElement($url)
	{
		$this->tree .= "\n\t<url>";
		$this->tree .= "\n\t\t<loc>http://" . Configure::read('Site.domain') . "/" . $url . "</loc>";
		$this->tree .= "\n\t\t<changefreq>weekly</changefreq>";
		$this->tree .= "\n\t\t<priority>0.8</priority>";
		$this->tree .= "\n\t</url>\n";
	}


	/**
	 * Recursively loop through through threaded categories
	 * generating nested HTML UL for sitemap page.
	 * 
	 * @param object $categories
	 * @param int $level
	 * @return void
	 * @access public
	 */
	public function generateSitemap($categories, $level = 0)
	{
		$this->tree .= '<ul class="cats">';

		$count = count($categories);
		$i = 0;

		foreach ($categories as $k => $category)
		{
			$catID = $category['Category']['id'];
			$catName = $category['CategoryName']['name'];
			$myCatUrl = $category['CategoryName']['url'];

			$this->url[$level] = $myCatUrl;

			$classString = (!empty($classes)) ? ' class="' . implode(' ', $classes) . '"' : '';
			
			$this->tree .= "\n<li" . $classString . "><span>";
			
			$url = implode('/', $this->url);
			
			$name = '<a href="/' . $url . '">' . h($catName) . '</a>';
			$this->tree .= "\n\t" . $name;
			
			if (!empty($category['children']))
			{				
				$this->generateSitemap($category['children'], ($level + 1));
			}
			
			$this->tree .= "\n</span></li>";

			if ($count == ($i + 1))
			{
				unset($this->url[$level]);
			}
			
			$i++;
			
		}
		
		$this->tree .= '</ul>';
		
	}

	/**
	 * Recursively loop through through threaded categories
	 * generating nested HTML UL.
	 * 
	 * @param object $categories
	 * @param int $level
	 * @return void
	 * @access public
	 */
	public function generateTopNav($categories, $level = 0)
	{	
		$attrs = (empty($level)) ? ' id="top-nav" class="nav"' : '';
		
		$this->tree .= '<ul' . $attrs . '>';

		if (empty($level) && $this->showHomeLink)
		{
			$this->tree .= '<li id="top-nav-home" class="first"><a href="/">Home</a></li>';
		}

		$count = count($categories);
		$i = 0;

		foreach ($categories as $k => $category)
		{
			$catID = $category['Category']['id'];
			$catName = $category['CategoryName']['name'];
			$myCatUrl = $category['CategoryName']['url'];
			
			$this->url[$level] = $myCatUrl;
			$currentUrl = $this->params['url']['url'];
			$catUrl = implode('/', $this->url);
			
			$classes = array();
			
			if ($currentUrl == $catUrl)
			{
				$classes[] = 'current';
			}
			
			if (empty($level) && ($i == 0))
			{
				$classes[] = 'first';
			}
			
			if (empty($level) && ($count == ($i + 1)) && !$this->showSaleLink)
			{
				$classes[] = 'last';
			}
			
			if (!empty($level) && ($i == 0))
			{
				$classes[] = 'top';
			}
			
			if (!empty($level) && ($count == ($i + 1)))
			{
				$classes[] = 'bottom';
			}
			
			if (!empty($category['children']))
			{
				$classes[] = 'has-children';
			}
			
			$classString = (!empty($classes)) ? ' class="' . implode(' ', $classes) . '"' : '';
			
			$this->tree .= "\n<li" . $classString . " id=\"nav-" . str_replace(' ', '_', strtolower($myCatUrl)) . "\"><span>";
			
			$url = implode('/', $this->url);
			
			$name = '<a href="/' . $url . '">' . h($catName) . '</a>';
			$this->tree .= "\n\t" . $name;
			
			if (!empty($category['children']))
			{				
				$this->generateTopNav($category['children'], ($level + 1));
			}
			
			$this->tree .= "\n</span></li>";

			if ($count == ($i + 1))
			{
				unset($this->url[$level]);
			}
			
			$i++;
			
		}

		if (empty($level) && $this->showSaleLink)
		{
			$this->tree .= '<li id="nav-sale" class="last"><span><a href="/">Sale</a></span></li>';
		}
		
		$this->tree .= '</ul>';
		
	}

	
	/**
	 * Recursively loop through through threaded categories
	 * generating nested HTML UL.
	 * 
	 * @param object $categories
	 * @return void
	 * @access public
	 */
	public function generateNestedTree($categories)
	{
		$this->tree .= '<ul>';

		foreach ($categories as $k => $category)
		{
			$catID = $category['Category']['id'];
			$catName = $category['CategoryName']['name'];
			
			$checked = (in_array($catID, $this->checkedCats)) ? ' class="jstree-checked"' : ' class="jstree-unchecked"';
			$this->tree .= "\n<li id=\"cat-node-" . $catID . "\"" . $checked . ">";
			
			$name = '<a href="/admin/categories/index/' . $catID . '">' . h($catName) . '</a>';
			$this->tree .= "\n\t" . $name . '</a>';
			
			if (!empty($category['children']))
			{
				$this->generateNestedTree($category['children']);
			}
			
			$this->tree .= "\n</li>";			
			
		}
		
		$this->tree .= '</ul>';
		
	}
	
	/**
	 * Recursively loop through through threaded categories
	 * generating nested HTML UL containing checkboxes
	 * 
	 * @param object $categories
	 * @return void
	 * @access public
	 */
	public function generateCategoryCheckboxes($categories)
	{
		$this->tree .= '<ul>';
		
		foreach ($categories as $k => $category)
		{
			$this->nodeCount++;
			
			$catID = $category['Category']['id'];
			$catName = $category['CategoryName']['name'];
			
			$this->tree .= "\n<li>";
			$this->tree .= "\n<div class=\"cat\">";
			
			$disabled = false;
			$checked = (!empty($this->productCategories) && in_array($catID, $this->productCategories)) ? 'checked' : '';
			
			if ($catID == $this->_mainCategoryID)
			{
				$disabled = true;
				$checked = 'checked';				
			}
			
			$this->tree .= $this->Form->checkbox($this->modelName . '.' . $this->nodeCount, array(
				'id' => 'cat-checkbox-' . $catID,
				'value' => $catID,
				'checked' => $checked,
				'disabled' => $disabled
			));	
			
			$this->tree .= "\n\t<label for=\"cat-checkbox-" . $catID . '">' . h($catName) . '</label>';
			
			if (!empty($category['children']))
			{
				$this->generateCategoryCheckboxes($category['children']);
			}
			
			$this->tree .= "\n</li>";
			
		}
				
		$this->tree .= '</ul>';
		
	}
	
	/**
	 * Recursively loop through through threaded categories
	 * generating nested HTML UL for front end category navigation.
	 * 
	 * @param object $categories
	 * @param string $catUrl Parts of url built it in previous iterations
	 * @return void
	 * @access public
	 */
	public function generateCategorySideNav($categories, $catUrl = '', $top = true)
	{
		$class = ($top) ? ' class="filter-style"' : '';
		
		$this->tree .= '<ul' . $class . '>';

		$count = count($categories);
		$i = 0;
		
		foreach ($categories as $k => $category)
		{
			$catID    = $category['Category']['id'];
			$catName  = $category['CategoryName']['name'];
			$myCatUrl = $category['CategoryName']['url'];
			$catCount = $category['Category']['product_counter'];

			// $topLiOpen = ($catID == $this->topActiveCategory) ? ' class="open"' : '';
			// $activeCategory = ($catID == $this->openCategoryID) ? ' class="current"' : '';

			$classes = array();	

			if ($catID == $this->openCategoryID)
			{
				$classes[] = 'selected';
			}
			
			if (!$this->showAllResults && $count == ($i + 1))
			{
				$classes[] = 'last';
			}
				
			$classString = (!empty($classes)) ? ' class="' . implode(' ', $classes) . '"' : '';
			
			$this->tree .= "\n<li" . $classString . ">";
			
			$this->tree .= '<a href="/' . $catUrl . $myCatUrl . '">' . h($catName) . ' <span class="face1">(' . intval($catCount) . ')</span></a>';

			if (in_array($catID, $this->pathIDs) || ($catID == $this->openCategoryID) && !empty($category['children']))
			{
				$passUrl = $myCatUrl . '/';
				$passUrl = (!empty($catUrl)) ? $catUrl . $passUrl : $passUrl;
				$this->generateCategorySideNav($category['children'], $passUrl, false);
			}
			
			$this->tree .= "\n</li>";

			$i++;
			
		}

		if($this->showAllResults && !empty($this->topCategoryRecord) && $top){
			$this->tree .= '<li class="last">';
			$this->tree .= '<a href="' . $this->topCategoryRecord['CategoryName']['full_url'] . '">' . 'All Results <span class="face1">(' . $this->topCategoryRecord['Category']['product_counter'] . ')</span>' . '</a>';
			$this->tree .= '</li>';
		}
		
		$this->tree .= '</ul>';
		
	}
	
	public function generateProductCategoryOptions($categories, $level = 0)
	{
		if (empty($level))
		{
			$this->options = array();
		}	
		
		foreach ($categories as $k => $category)
		{
			if (in_array($category['Category']['id'], $this->productCategories))
			{
				$this->options[$category['Category']['id']] = $category['CategoryName']['name'];
			}
			
			if (!empty($category['children']))
			{
				$this->generateProductCategoryOptions($category['children'], ($level + 1));
			}
		}
	}
	
	public function generateRootCategoryNav($categories)
	{
		$this->tree .= '<ul>';
		
		foreach ($categories as $k => $category)
		{
			$this->tree .= "\n<li>";
			$this->tree .= "\n\t" . '<a href="/' . $category['CategoryName']['url'] . '">' . h($category['CategoryName']['name']) . '</a>';
			$this->tree .= "\n</li>";
		}
		
		$this->tree .= '</ul>';
		
	}
	
	/**
	 * Get options.
	 * 
	 * @return array
	 * @access public
	 */
	public function getOptions()
	{
		return $this->options;
	}
	
	/**
	 * Set main category ID.
	 * 
	 * @param int $mainCategoryID
	 * @return array
	 * @access public
	 */
	public function setMainCategoryID($mainCategoryID)
	{
		$this->_mainCategoryID = intval($mainCategoryID);
	}
	
	/**
	 * Set top category ID.
	 * 
	 * @param int $topCategoryID
	 * @return void
	 * @access public
	 */
	public function setTopCategoryID($topCategoryID)
	{
		$this->topCategoryID = $topCategoryID;
		$this->topActiveCategory = $topCategoryID;
	}
		
	/**
	 * Get nested tree.
	 * 
	 * @return array $this->tree
	 * @access public
	 */
	public function getNestedTree()
	{
		return $this->tree;
	}

		
	/**
	 * Get nested tree.
	 * 
	 * @return array $this->tree
	 * @access public
	 */
	public function clearNestedTree()
	{
		$this->tree = '';
	}
	
	/**
	 * Assign product categories
	 * 
	 * @param array $productCategories
	 * @return void
	 * @access public
	 */
	public function setProductCategories($productCategories)
	{
		$this->productCategories = $productCategories;
	}
	
	/**
	 * Assign checked categories
	 * 
	 * @param array $checkedCats
	 * @return void
	 * @access public
	 */
	public function setCheckedCategories($checkedCats)
	{
		$this->checkedCats = $checkedCats;
	}
	
	/**
	 * Assign IDs of categories in path
	 * 
	 * @param array $pathIDs
	 * @return void
	 * @access public
	 */
	public function setPathIDs($pathIDs)
	{
		$this->pathIDs = $pathIDs;
	}
	
	/**
	 * Assign open category ID
	 * 
	 * @param int $openCategoryID
	 * @return void
	 * @access public
	 */	
	public function setOpenCategoryID($openCategoryID)
	{
		$this->openCategoryID = $openCategoryID;
	}
	
}


