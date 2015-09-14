<?php

/**
 * Contact Controller
 *
 */
class ContactController extends AppController
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
	 * @access public
	 */
	public function beforeFilter()
	{
		$this->Auth->allow('*');		
		parent::beforeFilter();
	}
	
	/**
	 * Display form
	 *
	 * @return void
	 * @access public
	 */
	public function index() {}

	/**
	 * Send email
	 *
	 * @return void
	 * @access public
	 */
	public function send()
	{
		if (!empty($this->data))
		{	
			$this->Contact->set($this->data);
			
			if ($this->Contact->validates())
			{
				$this->set('data', $this->data['Contact']);
				$this->set('recipient', $this->data['Contact']['name']);

				$this->initDefaultEmailSettings();
				$this->Email->subject = Configure::read('Site.name') . ' - Customer Enquiry';
				$this->Email->layout = 'to_vendor';
				$this->Email->template = 'enquiry';
				$this->Email->to = Configure::read('Email.from_name') . '<' . Configure::read('Email.from_email') . '>';
				$this->Email->send();
				
				$this->initDefaultEmailSettings();
				$this->Email->subject = Configure::read('Site.name') . ' - Your Enquiry';
				$this->Email->template = 'customers/enquiry_thankyou';
				$this->Email->to = $this->data['Contact']['name'] . '<' . $this->data['Contact']['email'] . '>';
				$this->Email->send();

				$this->Session->setFlash('Thank you for your enquiry. We will reply ASAP.', 'default', array('class' => 'success'));

			}
			else
			{
				$this->Session->setFlash('Please enter all required information', 'default', array('class' => 'failure'));
				return $this->setAction('index');
			}
		}

		$this->redirect('/contact');

	}


}


