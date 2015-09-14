<?php

/**
 * Customers Controller
 * 
 */
class CustomersController extends AppController
{
	/**
	 * Array containing the names of components this controller uses.
	 *
	 * @var array
	 * @access public
	 */
	public $components = array('Email');
	
	/**
	 * An array containing the names of helpers this controller uses. 
	 *
	 * @var mixed A single name as a string or a list of names as an array.
	 * @access public
	 */
	public $helpers = array('Time');
	
	/**
	 * Called before the controller action.
	 *
	 * @return void
	 * @access public
	 */
	function beforeFilter() 
	{
		$this->Auth->allow('register', 'success', 'forgotten_password', 'reset_password');
		$this->Auth->autoRedirect = false;

		parent::beforeFilter();
	}
		
	/**
	 * Show my account homepage.
	 * 
	 * @return void
	 * @access public
	 */
	public function index()
	{
		$this->addCrumb('/customers', 'My Account');
	}
	
	/**
	 * Show my account info page.
	 * 
	 * @return void
	 * @access public
	 */
	public function account_information()
	{
		$this->addCrumb('/customers', 'My Account');
		$this->addCrumb('/customers/account_info', 'Account Information');
		
		if (empty($this->data))
		{
			$this->data = $this->Customer->find('first', array(
				'conditions' => array('Customer.id' => $this->Auth->user('id'))
			));
		}

		$referer = $this->referer();
		if(strstr($referer, '/checkout')){
			$this->Session->write('Customer.redirect_to', $referer);
		}
	}
	
	/**
	 * Show my account info page.
	 * 
	 * @return void
	 * @access public
	 */
	public function save_account_information()
	{
		if (empty($this->data['Customer']))
		{
			$this->redirect('/account_information');
		}
		
		$this->data['Customer']['id'] = $this->Auth->user('id');
		
		$this->Customer->addUniqueRegisteredEmailValidation();
		$result = $this->Customer->save($this->data, true, array('first_name', 'last_name', 'email', 'phone', 'mobile', 'company_name'));
		
		if (!$result)
		{
			$this->Session->setFlash('Could not save your account information. Please check the form for errors.', 'default', array('class' => 'failure'));
			return $this->setAction('account_information');
		}
		
		$this->Session->setFlash('Your account information has been saved.', 'default', array('class' => 'success'));

		$redirectTo = $this->Session->read('Customer.redirect_to');
		if(!empty($redirectTo))
		{
			$this->Session->delete('Customer.redirect_to');
			$this->redirect($redirectTo);
		}
		else
		{
			$this->redirect('/customers/account_information');
		}
		
	}
	
	/**
	 * Show my account info page.
	 * 
	 * @return void
	 * @access public
	 */
	public function account_password()
	{
		$this->addCrumb('/customers', 'My Account');
		$this->addCrumb('/customers/account_password', 'Account Password');
		
		$this->Customer->addPasswordValidation();
		$this->Customer->addCurrentPasswordValidation();
		
		$this->data = $this->Customer->find('first', array(
			'conditions' => array('Customer.id' => $this->Auth->user('id'))
		));
		
	}
	
	/**
	 * Save new password.
	 * 
	 * @return void
	 * @access public
	 */
	public function save_account_password()
	{
		if (empty($this->data['Customer']))
		{
			$this->redirect('/account_password');
		}
		
		$this->data['Customer']['id'] = $this->Auth->user('id');
		
		$this->Customer->addPasswordValidation();
		$this->Customer->addCurrentPasswordValidation();
		
		$this->Customer->set($this->data);
		$validates = $this->Customer->validates(array('fieldList' => array(
			'password_current', 'password_main', 'password_confirm'
		)));
		
		$error = 'Could not save your password. Please check the form for errors.';
		
		if (!$validates)
		{
			$this->Session->setFlash($error, 'default', array('class' => 'failure'));
			return $this->setAction('account_password');
		}
		
		$result = $this->Customer->find('first', array('conditions' => array(
			'Customer.id' => $this->Auth->user('id'),
			'Customer.password' => $this->Auth->password($this->data['Customer']['password_current'])
		)));
		
		if (empty($result))
		{
			$this->Customer->invalidate('password_current', 'The current password you entered is incorrect');
			$this->Session->setFlash($error, 'default', array('class' => 'failure'));
			return $this->setAction('account_password');
		}
		
		$result = $this->Customer->saveField('password', $this->Auth->password($this->data['Customer']['password_main']));
		
		if (empty($result))
		{
			$this->Session->setFlash($error, 'default', array('class' => 'failure'));
			return $this->setAction('account_password');
		}
		
		$this->Session->setFlash('Your password has been saved.', 'default', array('class' => 'success'));
		$this->redirect('/customers/account_password');
		
	}
	
	/**
	 * Show the customer login page.
	 *
	 * @return void
	 * @access public
	 */
	public function login()
	{
		if ($this->customerIsLoggedIn())
		{
			$this->Basket->getCollection();

			$customer = $this->Auth->user();
			$this->Basket->saveField('customer_id', $customer['Customer']['id']);

			Configure::write('Customer.id', $customer['Customer']['id']);

			$this->Basket->bindFullDetails();
			$this->Basket->saveTotals();

			$this->redirect($this->Auth->redirect());
		}
		
		if ((isset($this->params['url']['ref']) && ($this->params['url']['ref'] == 'checkout')) || !empty($this->data['Customer']['to_checkout']))
		{
			$this->set('fromCheckout', true);
		}

		
		
		$this->addCrumb('/customers/login', 'Log In');
	}
	
	/**
	 * Handle a logout request
	 *
	 * @return void
	 * @access public
	 */
	public function logout()
	{
		Configure::write('Customer.id', 0);
		Configure::write('Customer.group_id', 0);

		$this->Basket->bindFullDetails();
		$this->Basket->saveTotals();

		$this->Session->setFlash('Successfully logged out', 'default', array('class' => 'success'));
		$this->redirect($this->Auth->logout());
	}
	
	/**
	 * Show the customer registration page.
	 *
	 * @return void
	 * @access public
	 */
	public function register()
	{
		$this->Customer->addPasswordValidation();
		
		if (!empty($this->data))
		{	
			$this->Customer->set($this->data);
			
			if ($this->Customer->validates())
			{
				$password = $this->data['Customer']['password_main'];
				$this->data['Customer']['password'] = $this->data['Customer']['password_main'];
				$this->data = $this->Auth->hashPasswords($this->data);
				
				if (Configure::read('Customers.require_approval_to_login'))
				{
					$this->data['Customer']['pending'] = 1;
				}
				
				if ($this->Customer->save($this->data))
				{
					if (Configure::read('Customers.login_after_register'))
					{
						$this->Session->setFlash('Thank you for registering.', 'default', array('class' => 'success'));
						$this->Auth->login($this->data);
						$this->redirect('/customers');
					}
					
					$this->redirect('/customers/success');
					
				}
			}
			
			$this->Session->setFlash('There were errors. Please check the form below.', 'default', array('class' => 'failure'));			
		}
		
		$this->addCrumb('/customers/register', 'Register');
		
	}
	
	/**
	 * Show registration success page.
	 *
	 * @return void
	 * @access public
	 */
	public function success()
	{
		$this->addCrumb('/customers/success', 'Thank You For Registering');
	}
	
	
	/**
	 * Display forgotten password screen
	 * 
	 * @return void
	 * @access public
	 */
	public function forgotten_password()
	{
		$this->addCrumb('/customers/forgotten_password', 'Forgotten Your Password?');
		
		if (empty($this->data))
		{
			return $this->render('password/forgotten');
		}

		if (empty($this->data['Customer']['email']))
		{	
			$this->Session->setFlash('Please enter your email address and press Continue.', 'default', array('class' => 'failure'));
			return $this->render('password/forgotten');
		}
		
		$customer = $this->Customer->find('first', array('conditions' => array(
			'email' => $this->data['Customer']['email'],
			'password !=' => '',
			'guest' => '0',
		)));
		
		if (empty($customer))
		{
			$this->Session->setFlash('Your email address could not be found. Did you type it correctly?', 'default', array('class' => 'failure'));
			return $this->render('password/forgotten');
		}
		
		$this->loadModel('ForgottenPassword');
		$this->ForgottenPassword->deleteAllByMember($customer['Customer']['id']);
		$hash = $this->ForgottenPassword->createRecord($customer['Customer']['id']);

		$this->initDefaultEmailSettings();
		
		$this->Email->to   		= $customer['Customer']['first_name'] . '<' . $customer['Customer']['email'] . '>';
		$this->Email->subject 	= Configure::read('Site.name') . ' - Reset Your Password';
		$this->Email->template 	= 'customers/forgotten_password';
		
		$this->set('recipient', $customer['Customer']['first_name']);
		$this->set('hash', $hash);
		
		$this->Email->send();
		
		$this->data = array();
		
		$this->Session->setFlash('Great! We\'ve sent you an email which will allow you to reset your password.', 'default', array('class' => 'success'));
		$this->render('password/forgotten');
	
	}
		
	/**
	 * Display new password screen.
	 * 
	 * @param string $hash
	 * @return void
	 * @access public
	 */
	public function reset_password($hash = null)
	{
		$this->addCrumb('/customers/forgotten_password', 'Forgotten Your Password?');
		
		$this->loadModel('ForgottenPassword');
		
		$this->Customer->removePersonalDetailsValidation();
		$this->Customer->addPasswordValidation();
		
		if (!empty($this->data['Customer']['hash']))
		{
			$record = $this->ForgottenPassword->findByHash($this->data['Customer']['hash']);
			$this->ifEmpty404($record);
									
			$data['Customer'] = array(
				'password_main' 	=> $this->data['Customer']['password_main'],
				'password_confirm' 	=> $this->data['Customer']['password_confirm'],
				'password' 			=> $this->Auth->password($this->data['Customer']['password_main'])
			);
			
			$this->Customer->id = $record['Customer']['id'];
			
			if ($this->Customer->save($data))
			{
				$this->ForgottenPassword->deleteAll(array('ForgottenPassword.hash' => $this->data['Customer']['hash']));
				return $this->render('password/reset_done');
			}
			
			$this->set('hash', $hash);
			return $this->render('password/reset');	
	
		}
		
		$this->ifEmpty404($hash);
		
		$record = $this->ForgottenPassword->findByHash($hash);
		
		$this->ifEmpty404($record);
		
		$this->set('hash', $hash);		
		$this->render('password/reset');
		
	}
	
	/**
	 * Admin
	 * List customers. 
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_index()
	{
		$conditions = array();

		if (!empty($this->params['url']['name']))
		{
			$conditions[] = array('OR' => array(
				'Customer.first_name LIKE' => '%' . $this->params['url']['name'] . '%',
				'Customer.last_name LIKE' => '%' . $this->params['url']['name'] . '%'
			));	
		}
		
		if (!empty($this->params['url']['email']))
		{
			$conditions['Customer.email LIKE'] = '%' . $this->params['url']['email'] . '%';
		}
		
		$this->paginate['limit'] = 20;
		$this->paginate['conditions'] = $conditions;
		
		if (Configure::read('Customers.require_approval_to_login'))
		{
			$this->paginate['order'] = array('Customer.pending DESC', 'Customer.last_name');
		}
		
		$this->set('records', $this->paginate());
		
	}
	
	/**
	 * Admin
	 * View existing customer for editing.
	 * 
	 * @param int $id
	 * @return void
	 * @access public
	 */
	public function admin_edit($id)
	{		
		if (empty($this->data))
		{
			$this->data = $this->Customer->findById($id);
		}
		
		if ($this->Session->check('Admin.' . $this->params['controller'] . '.last_tab'))
		{
			$this->set('initTab', $this->Session->read('Admin.' . $this->params['controller'] . '.last_tab'));
			$this->Session->delete('Admin.' . $this->params['controller'] . '.last_tab');
		}
		
		$this->set('customerGroups', $this->Customer->CustomerGroup->find('list'));
		$this->set('countries', $this->Customer->CustomerAddress->Country->find('list'));
		
	}
	
	/**
	 * Admin
	 * Save customer record.
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_save()
	{
		$id = $this->data['Customer']['id'];
		
		if (!empty($this->data['CustomerAddress']))
		{
			if (!isset($this->data['CustomerAddress'][count($this->data['CustomerAddress'])]['id']))
			{
				$this->set('addingAddress', true);
			}
		}
		
		if (isset($this->params['form']['last_pane']))
		{
			$this->Session->write('Admin.' . $this->params['controller'] . '.last_tab', $this->params['form']['last_pane']);
		}
		
		$this->Customer->set($this->data);
		
		if (!empty($this->data['Customer']['password_main']))
		{
			if (!empty($this->data['Customer']['password_main']))
			{
				$this->data['Customer']['password'] = $this->Auth->password($this->data['Customer']['password_main']);
			}
			
			$this->Customer->addPasswordValidation();
		}
		
		
		if ($this->Customer->saveAll($this->data))
		{
			if (empty($this->data['Customer']['id']))
			{				
				$id = $this->Customer->getInsertID();
			}
			
			$this->Session->setFlash('Customer record saved.', 'default', array('class' => 'success'));
			$this->redirect('/admin/customers/edit/' . $id);
		}	
		else
		{
			$errorsOnTabs = array();
			if ($this->hasValidationErrors($this->Customer))
			{
				$errorsOnTabs[] = 'personal';
			}			
			if ($this->hasValidationErrors($this->Customer->CustomerAddress))
			{
				$errorsOnTabs[] = 'addresses';
			}			
			$this->set('errorsOnTabs', $errorsOnTabs);
		}
		
		$this->Session->setFlash('Customer could not be saved. Please check the form for errors.', 'default', array('class' => 'failure'));
		$this->setAction('admin_edit', $id);
		
	}
	
	/**
	 * Admin
	 * Delete customer record.
	 * 
	 * @param int $id
	 * @return void
	 * @access public
	 */
	public function admin_delete($id)
	{
		$this->Customer->delete($id);
		
		$this->Session->setFlash('Customer deleted', 'default', array('class' => 'success'));
		$this->redirect('/admin/customers');
	
	}
	
	/**
	 * Admin
	 * Approve customer record.
	 * 
	 * @param int $id
	 * @return void
	 * @access public
	 */
	public function admin_approve($id)
	{
		$customer = $this->Customer->find('first', array('conditions' => array(
			'Customer.id' => $id,
			'Customer.pending' => 1
		)));
	
		if (empty($customer))
		{
			$this->redirect('/admin/customers');
		}
		
		$this->Customer->id = $id;
		$this->Customer->saveField('pending', 0);
		
		$this->initDefaultEmailSettings();
		$this->Email->to   	   = $customer['Customer']['first_name'] . '<' . $customer['Customer']['email'] . '>';
		$this->Email->subject  = Configure::read('Site.name') . ' - Account Activated';
		$this->Email->template = 'customers/account_approved';
		$this->set('recipient', $customer['Customer']['first_name']);
		$this->Email->send();
		
		$this->Session->setFlash('Customer approved', 'default', array('class' => 'success'));
		$this->redirect('/admin/customers/edit/' . $id);		
	
	}

	/**
	 * Admin
	 * Approve trade record.
	 * 
	 * @param int $id
	 * @return void
	 * @access public
	 */
	public function admin_trade_approve($id)
	{
		$customer = $this->Customer->find('first', array('conditions' => array(
			'Customer.id' => $id,
			'Customer.approved' => 0
		)));
	
		if (empty($customer))
		{
			$this->redirect('/admin/customers');
		}
		
		$this->Customer->id = $id;
		$this->Customer->saveField('approved', 1);
		
		$this->initDefaultEmailSettings();
		$this->Email->to   	   = $customer['Customer']['first_name'] . '<' . $customer['Customer']['email'] . '>';
		$this->Email->subject  = Configure::read('Site.name') . ' - Trade Account Activated';
		$this->Email->template = 'customers/account_approved';
		$this->set('recipient', $customer['Customer']['first_name']);
		$this->Email->send();
		
		$this->Session->setFlash('Trade Account activated', 'default', array('class' => 'success'));
		$this->redirect('/admin/customers/edit/' . $id);		
	
	}

	/**
	 * Admin
	 * Deactivate trade record.
	 * 
	 * @param int $id
	 * @return void
	 * @access public
	 */
	public function admin_trade_deactivate($id)
	{
		$customer = $this->Customer->find('first', array('conditions' => array(
			'Customer.id' => $id,
			'Customer.approved' => 1
		)));
	
		if (empty($customer))
		{
			$this->redirect('/admin/customers');
		}
		
		$this->Customer->id = $id;
		$this->Customer->saveField('approved', 0);
		
		$this->initDefaultEmailSettings();
		$this->Email->to   	   = $customer['Customer']['first_name'] . '<' . $customer['Customer']['email'] . '>';
		$this->Email->subject  = Configure::read('Site.name') . ' - Trade Account Deactivated';
		$this->Email->template = 'customers/account_deactivated';
		$this->set('recipient', $customer['Customer']['first_name']);
		$this->Email->send();
		
		$this->Session->setFlash('Trade Account deactivated', 'default', array('class' => 'success'));
		$this->redirect('/admin/customers/edit/' . $id);		
	
	}
	
	
}



