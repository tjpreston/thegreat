<?php

/**
 * Customer Address
 * 
 */
class CustomerAddress extends AppModel
{
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array('Customer', 'Country');
	
	/**
	 * List of validation rules.
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(
		'first_name' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter your first name'
		),
		'last_name' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter your last name'
		),
		'address_1' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter the first line of your address'
		),
		'town' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter your town'
		),
		'postcode' => array(
			'rule' =>  'notEmpty',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter your postcode'
		),
		'country_id' => array(
			'rule' => array('greaterThan', 'country_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter your country'
		),
		'customer_id' => array(
			'rule' => array('greaterThan', 'customer_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Customer ID missing'
		)
	);
	
	/**
	 * Remove customer ID validation.
	 *
	 * @return void
	 * @access public
	 */
	public function removeCustomerIDValidation()
	{
		unset($this->validate['customer_id']);
	}

	/**
	 * Add customer ID validation.
	 *
	 * @return void
	 * @access public
	 */
	public function addCustomerIDValidation()
	{
		$this->validate['customer_id'] = array(
			'rule' => array('greaterThan', 'customer_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Customer ID missing'
		);
	}

	/**
	 * Called during save operations, before validation. Please note that custom
	 * validation rules can be defined in $validate.
	 *
	 * @return boolean True if validate operation should continue, false to abort
	 * @param $options array Options passed from model::save(), see $options of model::save().
	 * @access public
	 */
	public function beforeValidate($options) 
	{
		$allOrdersToCountry = Configure::read('Orders.all_orders_to_country');
		
		if (!empty($allOrdersToCountry))
		{
			if (!empty($this->data['BillingAddress']))
			{
				$this->data['BillingAddress']['country_id'] = $allOrdersToCountry;
			}
			if (!empty($this->data['ShippingAddress']))
			{
				$this->data['ShippingAddress']['country_id'] = $allOrdersToCountry;
			}
		}
		
		return true;
		
	}
	
	/**
	 * Called after each successful save operation.
	 *
	 * @param boolean $created True if this save created a new record
	 * @access public
	 */
	public function afterSave($created) 
	{
		if (!empty($this->data['CustomerAddress']))
		{
			$ca = $this->data['CustomerAddress'];
		
			$id = (!empty($ca['id'])) ? $ca['id'] : $this->getInsertID();
			
			$this->Customer->id = $ca['customer_id'];
			
			if (!empty($ca['default_billing_address']))
			{
				$this->Customer->saveField('default_billing_address_id', $id);
			}
			
			if (!empty($ca['default_shipping_address']))
			{
				$this->Customer->saveField('default_shipping_address_id', $id);
			}
		}
		
	}
	
	/**
	 * Check address is in use by a basket.
	 * 
	 * @param int $id
	 * @return bool
	 * @access public
	 */
	public function inUseByBasket($id)
	{
		$Basket = ClassRegistry::init('Basket');
		
		$inUseAsBilling = $Basket->find('first', array(			
			'conditions' => array('BasketBillingAddress.customer_billing_address_id' => $id),
			'recursive' => -1,
		));
		
		$inUseAsShipping = $Basket->find('first', array(			
			'conditions' => array('BasketShippingAddress.customer_shipping_address_id' => $id),
			'recursive' => -1,
		));
		
		if (!empty($inUseAsBilling) || !empty($inUseAsShipping))
		{
			return true;
		}
		
		return false;
		
	}
	
	public function getList($customerID)
	{
		$records = $this->find('all', array(
			'conditions' => array('CustomerAddress.customer_id' => $customerID),
			'recursive' => -1
		));
		
		$list = array();
		
		foreach ($records as $k => $record)
		{
			$key = $record['CustomerAddress']['id'];
			$value = $record['CustomerAddress']['first_name'] . ' ' . $record['CustomerAddress']['last_name'] . ', ' . $record['CustomerAddress']['address_1'];
			$list[$key] = $value;
		}
		
		return $list;
		
	}

	/**
	 * Get saved addresses.
	 * 
	 * @param int $customerID
	 * @return array
	 * @access public
	 */
	public function getSavedAddresses($customerID)
	{
		$records = $this->find('all', array('conditions' => array(
			'CustomerAddress.customer_id' => $customerID,
			// 'CustomerAddress.first_use' => 0
		)));
		
		return $records;
		
	}
	
}



