<?php

/**
 * Referral Controller
 * 
 */
class ReferralController extends AppController
{	
	/**
	 * Array containing the names of components this controller uses.
	 *
	 * @var array
	 * @access public
	 */
	public $components = array('Email');
	
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
	}
	
	/**
	 * Show referral page.
	 *
	 * @access public
	 * @return void
	 */
	public function index($product)
	{
		$this->Category->Product->bindMeta($this->Category->Product, 1, false);
		
		$productID = $this->Category->Product->ProductMeta->field('product_id', array(
			'ProductMeta.language_id' => 1,
			'ProductMeta.url' => $product
		));
		
		if (empty($productID))
		{
			$this->redirect('/');
		}
		
		$record = $this->Category->Product->find('first', array(
			'conditions' => array('Product.id' => $productID)
		));
		
		if (empty($record))
		{
			$this->redirect('/');
		}
		
		$this->addCrumb('#', $record['ProductName']['name']);
		$this->addCrumb('#', 'Email to a Friend');
		
		if (empty($this->data) && $this->customerIsLoggedIn())
		{
			$customer = $this->Auth->user();
			$this->data['Referral']['sender_name']  = $customer['Customer']['first_name'] . ' ' .$customer['Customer']['last_name'];
			$this->data['Referral']['sender_email'] = $customer['Customer']['email'];
		}
		
		$this->data['Referral']['product_id'] = $productID;
		
		$this->set('record', $record);
		$this->set('referer', $this->referer());
		
	}
	
	/**
	 * Send referral.
	 *
	 * @access public
	 * @return void
	 */
	public function send()
	{
		if (empty($this->data['Referral']['product_id']))
		{
			$this->redirect('/');
		}
		
		$this->Referral->set($this->data);
		
		$record = $this->Category->Product->find('first', array(
			'conditions' => array('Product.id' => $this->data['Referral']['product_id'])
		));
		
		if (!$this->Referral->validates())
		{
			$this->Session->setFlash('Please complete all the required fields', 'default', array('class' => 'failure'));		
			return $this->setAction('index', $record['ProductMeta']['url']);
		}
		
		$this->set('recipient', $this->data['Referral']['recipient_name']);
		$this->set('data', $this->data['Referral']);
		$this->set('record', $record);
		
		$this->initDefaultEmailSettings();
		$this->Email->to   	   = $this->data['Referral']['recipient_name'] . '<' . $this->data['Referral']['recipient_email'] . '>';
		$this->Email->subject  = Configure::read('Site.name') . ' - A Recommendation From ' . $this->data['Referral']['sender_name'];
		$this->Email->template = 'referrals/referral';
		$this->Email->send();
		
		$this->Session->setFlash('You successfully sent this product to ' . $this->data['Referral']['recipient_name'] . '. Thanks!', 'default', array('class' => 'success'));			
		$this->redirect('/referral/' . $record['ProductMeta']['url']);
		
	}
	
	
	
	
	
	
	
}
