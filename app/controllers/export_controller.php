<?php

/**
 * Export Controller
 *
 */
class ExportController extends AppController
{
	/**
	 * An array containing the class names of models this controller uses.
	 *
	 * @var array
	 * @access public
	 */
	public $uses = array();
	
	/**
	 * An array containing the names of helpers this controller uses. 
	 *
	 * @var mixed A single name as a string or a list of names as an array.
	 * @access public
	 */
	public $helpers = array('Category');

	/**
	 * Admin.
	 * Export home.
	 *
	 * @return void
	 * @access public
	 */
	public function admin_index()
	{
		
	}
	
	/**
	 * Admin.
	 * Create google sitemap frontend.
	 *
	 * @return void
	 * @access public
	 */
	public function admin_google_sitemap()
	{
		$path = '/sitemap.xml';
		
		$output = $this->requestAction('/admin/export/do_google_sitemap');

		$file = new File(WWW_ROOT . $path, true);
		$file->write($output);

		$this->set('path', $path);

		$this->Session->setFlash('Google Sitemap successfully saved to <a href="' . $path . '">' . $path . '</a>', 'default', array('class' => 'success'));
		// $this->redirect('/admin/export');
		$this->setAction('admin_index');

	}
	
	/**
	 * Admin.
	 * Create google sitemap
	 *
	 * @return void
	 * @access public
	 */
	public function admin_do_google_sitemap()
	{
		$this->layout = 'ajax';

		$this->Category->bindName($this->Category, 1, false);
		$this->Category->unbindModel(array('hasAndBelongsToMany' => array('Product')));
		$categories = $this->Category->find('threaded', array('conditions' => array('Category.active' => 1)));
		
		$this->set('categories', $categories);
		
		$this->Category->Product->bindMeta($this->Category->Product, 1, false);
		
		$this->Category->Product->unbindModel(array(
			'belongsTo' => array('Manufacturer', 'StockStatus'),
			'hasOne' => array('ProductName'),
			'hasMany' => array('ProductImage', 'ProductCategory'),
			'hasAndBelongsToMany' => array('Category')
		), false);
		
		$products = $this->Category->Product->find('all');

		$this->set('products', $products);

		$this->render('google_sitemap_xml');

	}




}