<?php

/**
 * Customer Addresses Controller
 * 
 */
class CustomerAddressesController extends AppController
{
	/**
	 * Show my address book.
	 * 
	 * @return void
	 * @access public
	 */
	public function index()
	{
		$this->addCrumb('/customers', 'My Account');
		$this->addCrumb('/customer_addresses', 'Address Book');
		
		$customer = $this->CustomerAddress->Customer->find('first', array(
			'conditions' => array('Customer.id' => $this->Auth->user('id')),
			'recursive' => -1
		));
		
		$records = $this->CustomerAddress->find('all', array('conditions' => array(
			'CustomerAddress.customer_id' => $this->Auth->user('id'),
			'CustomerAddress.first_use' => 0
		)));
		
		$this->set(compact('customer', 'records'));
		
	}
	
	/**
	 * Show my address book.
	 * 
	 * @return void
	 * @access public
	 */
	public function view($id = null)
	{
		$this->set('countries', $this->CustomerAddress->Country->find('list'));
		
		$this->addCrumb('/customers', 'My Account');
		$this->addCrumb('/customer_addresses', 'Address Book');
				
		if (!empty($id) && is_numeric($id))
		{
			if (empty($this->data))
			{
				$this->data = $this->CustomerAddress->find('first', array('conditions' => array(
					'CustomerAddress.id' => $id,
					'Customer.id' => $this->Auth->user('id')
				)));
			}
			
			$this->set('customer', $this->CustomerAddress->Customer->find('first', array(
				'conditions' => array('Customer.id' => $this->Auth->user('id')),
				'recursive' => -1
			)));
			
			$this->addCrumb('/customer_addresses/view/' . $id, 'Edit Address');
			
		}
		else
		{
			$this->addCrumb('/customer_addresses/view', 'Add Address');
		}
				
	}
	
	/**
	 * Save address.
	 * 
	 * @return void
	 * @access public
	 */
	public function save()
	{
		if (empty($this->data['CustomerAddress']))
		{
			$this->redirect('/customer_addresses');
		}
		
		$this->data['CustomerAddress']['customer_id'] = $this->Auth->user('id');
		
		if ($this->CustomerAddress->save($this->data))
		{
			$this->Session->setFlash('Your address has been saved.', 'default', array('class' => 'success'));
			$this->redirect('/customer_addresses');
		}
		
		$this->Session->setFlash('Could not save your address. Please check the form for errors.', 'default', array('class' => 'failure'));
		$this->setAction('view');
		
	}
	
	/**
	 * Delete address.
	 * 
	 * @param int $id
	 * @return void
	 * @access public
	 */
	public function delete($id)
	{
		$preCount = $this->CustomerAddress->find('count', array('conditions' => array(
			'CustomerAddress.customer_id' => $this->Auth->user('id')
		)));
		
		$this->CustomerAddress->deleteAll(array(
			'CustomerAddress.customer_id' => $this->Auth->user('id'),
			'CustomerAddress.id' => $id
		));
		
		$postCount = $this->CustomerAddress->find('count', array('conditions' => array(
			'CustomerAddress.customer_id' => $this->Auth->user('id')
		)));
		
		if ($postCount < $preCount)
		{
			$this->Session->setFlash('Your address was deleted.', 'default', array('class' => 'success'));
		}
		else
		{
			$this->Session->setFlash('Could not delete your address.', 'default', array('class' => 'failure'));
		}
		
		$this->redirect('/customer_addresses');
		
	}
		
	/**
	 * Admin.
	 * Delete customer address.
	 * 
	 * @param int $id
	 * @return void
	 * @access public
	 */
	public function admin_delete($id)
	{
		$this->CustomerAddress->id = $id;
		$customerID = $this->CustomerAddress->field('customer_id');
		
		if ($this->CustomerAddress->inUseByBasket($id))
		{
			$this->Session->setFlash('Address is currently in use in customer\'s basket. Cannot delete.', 'default', array('class' => 'failure'));
			$this->redirect('/admin/customers/edit/' . $customerID);
		}
		
		if ($this->CustomerAddress->delete($id))
		{
			$this->Session->setFlash('Address deleted.', 'default', array('class' => 'success'));
		}
		else
		{
			$this->Session->setFlash('Could not delete address.', 'default', array('class' => 'failure'));
		}
		
		$this->redirect('/admin/customers/edit/' . $customerID);
		
	}	
	
}


