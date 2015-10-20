<?php

/**
 * Application Controller
 * 
 */
class AppController extends Controller
{
	/**
	 * An array containing the class names of models this controller uses.
	 *
	 * @var array
	 * @access public
	 */
	public $uses = array('Currency', 'Language', 'Category', 'Basket');
	
	/**
	 * Array containing the names of components this controller uses.
	 *
	 * @var array
	 * @access public
	 */
	public $components = array(/*'DebugKit.Toolbar', */'Auth', 'Session', 'Security', 'Cookie', 'RequestHandler');
	
	/**
	 * An array containing the names of helpers this controller uses. 
	 *
	 * @var mixed A single name as a string or a list of names as an array.
	 * @access public
	 */
	public $helpers = array('Session', 'Category', 'Esper', 'Javascript', 'Time','Text');
	
	/**
	 * Site mode.
	 * 
	 * @var string
	 * @access protected
	 */
	protected $mode;
	
	/**
	 * An array containing the breadcrumbs for site header
	 *
	 * @var mixed Array containing url => linkName pairs
	 * @access protected
	 */
	protected $breadcrumbs = array();
	
	/**
	 * Current request currency ID.
	 *
	 * @var int
	 * @access protected
	 */
	protected $currencyID;
	
	/**
	 * Users basket
	 *
	 * @var array
	 * @access protected
	 */
	public $_basket;
	
	/**
	 * Users basket items
	 *
	 * @var array
	 * @access protected
	 */
	public $_basketItems;
	
	/**
	 * Store categories.
	 * 
	 * @var array
	 * @access protected
	 */
	protected $_categories;
	
	/**
	 * Called before the controller action.
	 *
	 * @return void
	 * @access public
	 */
	function beforeFilter()	
	{
		if (!empty($this->params['prefix']))
		{
			($this->params['prefix'] == 'admin') ? $this->initAdmin() : $this->initAjax();
		}
		elseif($this->RequestHandler->isAjax())
		{
			$this->initFront();
			$this->layout = 'ajax';
		}
		else
		{
			$this->initFront();
		}
		
	}
	
	/**
	 * Called after the controller action is run, but before the view is rendered.
	 *
	 * @TODO only get recently viewed if necesary
	 * @TODO only get root featured products if necessary
	 * @return void
	 * @access public
	 */
	public function beforeRender()
	{
		if ($this->mode === 'front')
		{
			$this->set('breadcrumbs', $this->breadcrumbs);
			
			if (Configure::read('Catalog.recently_viewed_enabled'))
			{
				$this->set('recentlyViewed', $this->_getRecentlyViewed());
			}
			
			if (Configure::read('Catalog.featured_products_enabled'))
			{
				$this->set('rootFeauredProducts', $this->Category->Product->getFeaturedProducts());
			}
			
			if (Configure::read('Template.show_manufacturer_select'))
			{
				$this->set('manufacturerList', $this->Category->Product->Manufacturer->getUrlList());
			}

			$this->set('manufacturerNav', $this->Category->Product->Manufacturer->getUrlList('nav'));
			$this->set('manufacturerFooter', $this->Category->Product->Manufacturer->getUrlList('footer'));
		}

	}
	
	/**
	 * Sets up front end
	 *
	 * @TODO language_id
	 * @return void
	 * @access public
	 */
	public function initFront()
	{
		$this->mode = 'front';

		$this->helpers[] = 'Catalog';
		$this->helpers[] = 'AssetCompress.AssetCompress';
		
		$this->setActiveCurrency();
		
		if (Configure::read('Wishlist.enabled'))
		{
			$this->loadModel('Wishlist');			
		}

		$customer = $this->initAuth();
		
		if (Configure::read('Wishlist.enabled') && !empty($customer))
		{
			$this->_wishlist = $this->Wishlist->getCollection();
		}
		
		// $this->Session->__start();
		$this->Session->write('void', 'void');
		
		Configure::write('Runtime.active_currency', $this->activeCurrencyID);
		Configure::write('Runtime.mode', $this->mode);
		Configure::write('Runtime.session_id', $this->Session->id());
		
		$this->Category->bindName($this->Category, 1, false);
		$this->Category->Product->bindName($this->Category->Product, 1, false);
		$this->Category->Product->bindMeta($this->Category->Product, 1, false);
		$this->Category->Product->bindPrice($this->activeCurrencyID, false);
		$this->Category->Product->bindSingleQtyDiscount(false);
		
		$this->_getCacheSetCategories();
		
		$this->_basket = $this->Basket->getCollection();
		
		$this->set('basket', $this->_basket);
		$this->set('totalBasketItemQuantities', $this->Basket->BasketItem->getCollectionTotalQuantities());
		
		if (Configure::read('Template.products_in_minibasket'))
		{
			$this->_basketItems = $this->Basket->BasketItem->getCollectionItems();
			$this->set('basketItems', $this->_basketItems);
		}
		
		if (Configure::read('Template.show_latest_news'))
		{
			$this->loadModel('Article');
			$this->set('latestNews', $this->Article->find('all', array('limit' => 3)));
		}
		
	}
	
	/**
	 * Set up ajax request.
	 * 
	 * @return void
	 * @access public 
	 */
	public function initAjax()
	{
		Configure::write('debug', 0);

		$this->mode = 'ajax';

		$this->helpers[] = 'Catalog';
		$this->layout = 'ajax';		
		
		$this->setActiveCurrency();
		
		if (Configure::read('Wishlist.enabled'))
		{
			$this->loadModel('Wishlist');			
		}
		
		$this->Session->write('void', 'void');
		
		$customer = $this->initAuth();
		
		Configure::write('Runtime.active_currency', $this->activeCurrencyID);
		Configure::write('Runtime.mode', $this->mode);
		Configure::write('Runtime.session_id', $this->Session->id());
		
		$this->Category->bindName($this->Category, 1, false);
		$this->Category->Product->bindName($this->Category->Product, 1, false);
		$this->Category->Product->bindMeta($this->Category->Product, 1, false);
		$this->Category->Product->bindPrice($this->activeCurrencyID, false);
		
	}
	
	/**
	 * Sets up admin interface.
	 *
	 * @return void
	 * @access private
	 */
	protected function initAdmin()
	{
		$this->mode = 'admin';
		
		$this->helpers[] = 'Time';
		$this->layout = 'admin';
		
		$this->Auth->userModel = 'Customer';
		$this->Auth->allow('*');
		
		$this->Security->loginOptions = array(
			'type'  => 'basic',
			'realm' => Configure::read('Site.name') . ' Administration'
		);
		$this->Security->loginUsers = Configure::read('Admin.users');
		$this->Security->requireLogin();
		
		$this->Security->validatePost = false;
		
		Configure::write('Runtime.active_currency', 1);
		Configure::write('Runtime.mode', $this->mode);
		
		$languages = $this->Language->find('list');
		Configure::write('Runtime.languages', $languages);
		$this->set('languages', $languages);
		
	}
	
	/**
	 * Set up customer authentication.
	 * 
	 * @return mixed
	 * @access private
	 */
	private function initAuth()
	{
		$this->Auth->loginRedirect = array('controller' => 'customers', 'action' => 'index');
			
		if (($this->params['controller'] == 'customers') && ($this->params['action'] == 'login') && !empty($this->data['Customer']['to_checkout']))
		{
			$this->Auth->loginRedirect = array('controller' => 'checkout', 'action' => 'index');
		}
		
		$this->Auth->loginError = 'Your account could not be found. Please try again.';
		$this->Auth->authError = 'Please login to continue.';
		$this->Auth->userModel = 'Customer';
		$this->Auth->fields = array('username' => 'email', 'password' => 'password');
		
		$this->Auth->userScope = array(
			'Customer.guest' => 0,
		);
		
		$customer = $this->Auth->user();
		
		if (empty($customer))
		{
			return false;
		}
		
		Configure::write('Customer.id', $customer['Customer']['id']);
		Configure::write('Customer.group_id', $customer['Customer']['customer_group_id']);
		
		if (Configure::read('Catalog.use_tiered_customer_pricing'))
		{
			$group = ClassRegistry::init('CustomerGroup')->findById($customer['Customer']['customer_group_id']);
			Configure::write('Customer.group_discount_amount', $group['CustomerGroup']['discount_amount']);
		}
		
		$this->Basket->setCustomer($customer['Customer']);
		
		if (Configure::read('Wishlist.enabled'))
		{
			$this->Wishlist->setCustomer($customer['Customer']);
		}
		
		return $customer;
		
	}
	
	/**
	 * Get recently viewed products.
	 * 
	 * @return array mixed
	 * @access private
	 */
	private function _getRecentlyViewed()
	{
		if (!isset($this->Cookie))
		{
			return;
		}
		
		$recentlyViewedProducts = array();
		$recentlyViewedCookie = $this->Cookie->read('Catalog.recently_viewed');
		
		if (empty($recentlyViewedCookie))
		{
			return;
		}
		
		$recentArray = unserialize($recentlyViewedCookie);
		
		if (empty($recentArray))
		{
			return;
		}
		
		$this->Category->Product->unbindModel(array(
			'belongsTo' => array('Manufacturer'),
			'hasOne' => array('ProductDescription'),
			'hasMany' => array('RelatedProduct', 'CrossSell', 'ProductShippingCarrierService', 'ProductOption', 'ProductCategory')
		), true);
		
		$records = $this->Category->Product->find('all', array(
			'conditions' => array('Product.id' => $recentArray)
		));
		
		if (!empty($records))
		{
			$this->loadModel('ProductOptionStock');
			$records = $this->ProductOptionStock->addVarsToProducts($records, 'singleqty');
		}
		
		return $records;
		
	}
	
	/**
	 * Set last viewed page.
	 * 
	 * @return void
	 * @access protected
	 */
	protected function setLastPage()
	{
		$this->Session->delete('Site.last_page');
		
		$referrerControllers = array(
			'Home' => 'the homepage',
			'Basket' => 'your basket',
			'Wishlist' => 'your wishlist'
		);
		
		if (in_array($this->name, array_keys($referrerControllers)))
		{
			$this->Session->write('Site.last_page', array(
				$this->params['url']['url'] => $referrerControllers[$this->name] 
			));
			return;
		}
		
	}
	
	/**
	 * Get nested categories. Cache and set.
	 * 
	 * @return void
	 * @access private
	 */
	private function _getCacheSetCategories()
	{
		
                $this->_categories = Cache::read('categories');

		if ($this->_categories === false)
		{
			$this->Category->unbindModel(array('hasAndBelongsToMany' => array('Product')));
			$this->_categories = $this->Category->find('threaded', array('conditions' => array('Category.active' => 1)));
			Cache::write('categories', $this->_categories);
		}
		
		$this->set('categories', $this->_categories);
		
	}
	
	/**
	 * Set request active currency and persist in session
	 *
	 * @return void
	 * @access protected
	 */	
	protected function setActiveCurrency()
	{
		$currencies = $this->Currency->find('all');
		
		$this->activeCurrencyID = Configure::read('Currencies.main_currency_id');
		
		foreach ($currencies as $k => $currency)
		{
			if ($this->Session->read('currency') == $currency['Currency']['id'])
			{
				$this->activeCurrencyID = $currency['Currency']['id'];
			}
		}
		
		foreach ($currencies as $k => $currency)
		{
			if ($this->activeCurrencyID == $currency['Currency']['id'])
			{
				$this->activeCurrencySymbol = $currency['Currency']['symbol'];
				$this->activeCurrencyHTML = $currency['Currency']['html'];
			}
		}
		
		$this->set('currencies', $currencies);
		$this->set('activeCurrencyID', $this->activeCurrencyID);
		$this->set('activeCurrencySymbol', $this->activeCurrencySymbol);
		$this->set('activeCurrencyHTML', $this->activeCurrencyHTML);
		
	}	
	
	/**
	 * Adds breadcrumb to breadcrumb array
	 *
	 * @param string $url Url for breadcrumb item
	 * @param string $link Link text for breadcrumb item
	 * @return void
	 * @access public
	 */	
	public function addCrumb($url, $link)
	{
		$this->breadcrumbs[] = array('url' => $url, 'link' => $link);
	}
	
	/**
	 * Send HTTP no-cache headers.
	 * 
	 * @return void
	 * @access public
	 */
	public function sendNoCacheHeaders()
	{	
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
	}
	
	/**
	 * Determine if user is logged in as customer.
	 * 
	 * @return bool
	 * @access protected
	 */	
	protected function customerIsLoggedIn()
	{
		$user = $this->Auth->user();
		return !empty($user);
	}
	
	/**
	 * Init front end.
	 * 
	 * @return void
	 * @access public
	 */
	public function initDefaultEmailSettings()
	{
		$this->Email->reset();		
		$this->Email->from = Configure::read('Email.from_name') . '<' . Configure::read('Email.from_email') . '>';
		$this->Email->replyTo = Configure::read('Email.from_email');
		$this->Email->return = Configure::read('Email.from_email');
		$this->Email->layout = 'to_customer';
		$this->Email->sendAs = 'text';		
	}
	
	/**
	 * Shortcut for 404'ing if passed field is empty
	 * 
	 * @param mixed $var
	 * @return void
	 * @access protected
	 */
	protected function ifEmpty404($var)
	{
		if (empty($var))
		{
			$this->cakeError('error404');
		}
	}
	
	/**
	 * Shortcut for 404'ing if passed field is empty
	 * 
	 * @param mixed $field
	 * @return void
	 * @access protected
	 */
	protected function notEmptyOr404($field)
	{
		$this->ifEmpty404($field);
	}
	
	/**
	 * Hack required due to the way validationErrors is populated
	 * to determine precence of validation errors
	 * 
	 * @param object $model
	 * @return bool
	 * @access protected
	 */
	protected function hasValidationErrors($model)
	{
		if (!empty($model->validationErrors))
		{
			foreach ($model->validationErrors as $field => $errors)
			{
				if (ctype_lower(str_replace('_', '', $field)) || is_numeric($field))
				{
					return true;
				}
			}
		}
		
		return false;
		
	}

	
	protected function uploadLargeProductImage($source, $dest, $filename)
	{
		$imageSize = getimagesize($source);
		$width = $imageSize[0];
		$height = $imageSize[1];
		
		$maxWidth  = Configure::read('Images.product_max_large_width');
		$maxHeight = Configure::read('Images.product_max_large_height');
		
		$dest = WWW_ROOT . $dest . $filename;
		
		if (($width <= $maxWidth) && ($height <= $maxHeight))
		{
			copy($source, $dest);
			return $dest;
		}
		
		$masterDim = ($width > $maxWidth) ? 'width' : 'height';
		
		$this->ImageNew->resize($source, $dest, $maxWidth, $maxHeight, $masterDim);
		
		$resizedSize = getimagesize($dest);
		$resizedWidth = $resizedSize[0];
		$resizedHeight = $resizedSize[1];
		
		// If it's still too tall
		if (($masterDim == 'width') && ($resizedHeight > $maxHeight))
		{
			$this->ImageNew->resize($dest, $dest, $maxWidth, $maxHeight, 'height');
		}
		// If it's still too wide
		else if (($masterDim == 'height') && ($resizedWidth > $maxWidth))
		{
			$this->ImageNew->resize($dest, $dest, $maxWidth, $maxHeight, 'width');
		}

		/**
		 * Make sure large image is taller & wider than medium image.
		 * Otherwise jQuery zoom function gets upset
		 *
		 */
		$resizedSize = getimagesize($dest);
		$resizedWidth = $resizedSize[0];
		$resizedHeight = $resizedSize[1];

		$medHeight = Configure::read('Images.product_medium_height');
		$medWidth = Configure::read('Images.product_medium_width');

		if($resizedHeight < $medHeight){
			$this->ImageNew->expandToFit($dest, $resizedWidth, $medHeight);
		}

		$resizedSize = getimagesize($dest);
		$resizedWidth = $resizedSize[0];
		$resizedHeight = $resizedSize[1];

		if($resizedWidth < $medWidth){
			$this->ImageNew->expandToFit($dest, $medWidth, $resizedHeight);
		}

		/**
		 * Make image square
		 *
		 */
		$resizedSize = getimagesize($dest);
		$resizedWidth = $resizedSize[0];
		$resizedHeight = $resizedSize[1];

		$square = max($resizedWidth, $resizedHeight);
		$this->ImageNew->expandToFit($dest, $square, $square);

		return $dest;
		
	}


	/**
	 * Handles the PayPal IPN callback
	 *
	 */
	public function afterPaypalNotification($ipn_id){
		$this->requestAction('/checkout/paypal_callback/' . $ipn_id);
		return true;
	}

	/*public function processTransaction($ipn_id){
		$this->setAction('__processTransaction', $ipn_id);
	}*/
	
}

