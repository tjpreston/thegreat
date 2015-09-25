<?php

/**
 * Home Controller
 * 
 */
class HomeController extends AppController
{
	/**
	 * An array containing the class names of models this controller uses.
	 *
         * In this case we use the Staticpage model as we need to get the
         * homepage description and images from the staticpages table.
         * 
	 * @var array
	 * @access public
	 */
	public $uses = array('Staticpage'); 
	
	/**
	 * An array containing the names of helpers this controller uses.
	 *
	 * @var mixed A single name as a string or a list of names as an array.
	 * @access public
	 */
	public $helpers = array('Text');
	
	/**
	 * Called before the controller action.
	 *
	 * @return void
	 * @access public
	 */
	function beforeFilter() 
	{
		$this->Auth->allow('index');
		parent::beforeFilter();
	}
	
	/**
	 * Show the homepage.
	 * 
	 * @return void
	 * @access public
	 */
	public function index()
	{
		$this->setLastPage();
		
		$this->Category->Product->unbindModel(array('hasAndBelongsToMany' => array('Category')), false);
		$this->Category->Product->bindDescription($this->Category->Product, 0, false);
		
		$featuredCats  = $this->Category->getFeatured();
		$featuredProds = $this->Category->Product->getFeatured();

		
		
		$this->set('featuredCategories', $featuredCats);
		$this->set('featuredProducts', $featuredProds);
		$this->set('specialOffers', $this->Category->Product->getSpecialPriceProducts());
		$this->set('newProducts', $this->Category->Product->getNewProducts());
		$this->set('bestSellers', $this->Category->Product->getbestSellerProducts());
		
		

		
		$manufacturers = $this->Category->Product->Manufacturer->getAll();
		$this->set('allManufacturers', $manufacturers);
		
		$this->set('featuredManufacturers', $this->Category->Product->Manufacturer->getFeatured());
		
		$this->set('noSidenav', true);

		$this->set('inCatalog', true);
		//$this->set('pagedata', $this->Staticpage->find('all'));
                
                $this->set('pagedata', $this->Staticpage->find('first', 
                array('conditions' => array('Staticpage.name' => 'Homepage'))));
                
                // $tmp = $this->Staticpage->find('first', 
                // array('conditions' => array('Staticpage.name' => 'who_we_are')));
		

                // $meta = $this->Config->ConfigHomepage->find('first');
		// $this->set('title_for_layout', $meta['ConfigHomepage']['title']);
		// $this->set('metaKeywords', $meta['ConfigHomepage']['meta_keywords']);
		// $this->set('metaDescription', $meta['ConfigHomepage']['meta_description']);
		
	}	
	
}


