<?php

class SitemapController extends AppController {
	public $uses = array('Product', 'Category');

	public $helpers = array('XmlSitemap');

	public function beforeFilter(){
		$this->Auth->allow();
	}

	public function xml_sitemap(){
		$this->layout = false;

		$this->Product->bindMeta($this->Product, 1, false);
		$products = $this->Product->find('all', array(
			'contain' => 'ProductMeta',
			'conditions' => array('Product.active' => 1),
			'fields' => array('ProductMeta.url'),
		));

		$this->Category->bindName($this->Category, 1, false);
		$categories = $this->Category->find('all', array(
			'contain' => 'CategoryName',
			'conditions' => array('Category.active' => 1),
			'fields' => array('CategoryName.full_url'),
		));

		$pages = Configure::read('Static.pages');

		$this->set(compact('products', 'categories', 'pages'));
	}
}