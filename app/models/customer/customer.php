<?php

/**
 * Customer Model
 * 
 */
class Customer extends AppModel
{
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array(
		/*
		'CustomerDefaultBillingAddress' => array(
			'className' => 'CustomerAddress',
			'foreignKey' => 'default_billing_address_id'
		),
		'CustomerDefaultShippingAddress' => array(
			'className' => 'CustomerAddress',
			'foreignKey' => 'default_shipping_address_id'
		),
		*/
		'CustomerGroup'
	);
	
	
	/**
	 * Detailed list of hasMany associations.
	 *
	 * @var array
	 * @access public
	 */
	public $hasMany = array(
		'CustomerAddress',
		'Order'
	);

	public $order = array(
		'Customer.created' => 'DESC'
	);
	
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
		'email' => array(
			'valid' => array(
				'rule' => 'email', 
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Please enter your email address'
			)			
		),
		'phone' => array(
			'valid' => array(
				'rule' => 'notEmpty',
				'allowEmpty' => false,
				'message' => 'Please enter your telephone number'
			)			
		),
	);
	
	public function addUniqueRegisteredEmailValidation()
	{
		$this->validate['email']['unique'] = array(
			'rule' => 'isUniqueRegisteredUser',
			'message' => 'The email address you entered already exists in our database'
		);
	}

/**
 * Returns false if any fields passed match any (by default, all if $or = false) of their matching values.
 *
 * @param array $fields Field/value pairs to search (if no values specified, they are pulled from $this->data)
 * @param boolean $or If false, all fields specified must match in order for a false return value
 * @return boolean False if any records matching any fields are found
 * @access public
 */
	function isUniqueRegisteredUser($fields) {
		if (!is_array($fields)) {
			$fields = func_get_args();
		}

		foreach ($fields as $field => $value) {
			if (is_numeric($field)) {
				unset($fields[$field]);

				$field = $value;
				if (isset($this->data[$this->alias][$field])) {
					$value = $this->data[$this->alias][$field];
				} else {
					$value = null;
				}
			}

			if (strpos($field, '.') === false) {
				unset($fields[$field]);
				$fields[$this->alias . '.' . $field] = $value;
			}
		}

		if (!empty($this->id)) {
			$fields[$this->alias . '.' . $this->primaryKey . ' !='] =  $this->id;
		}

		$fields[$this->alias . '.' . 'guest'] = 0;

		return ($this->find('count', array('conditions' => $fields, 'recursive' => -1)) == 0);
	}
	
	/**
	 * Add password validation.
	 * 
	 * @return void
	 * @access public
	 */
	public function addPasswordValidation()
	{
		$this->validate['password_main'] = array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter a password'
		);
		$this->validate['password_confirm'] = array(
			'identicalFieldValues' => array(
				'rule' => array('validateIdenticalFieldValues', 'password_main'),
        		'message' => 'Please re-enter your password so that it matches'
			)
		);
	}
	
	/**
	 * Add phone validation.
	 * 
	 * @return void
	 * @access public
	 */
	public function addPhoneValidation()
	{
		$this->validate['phone'] = array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter your phone number'
		);
	}

	/**
	 * Add current password validation.
	 * 
	 * @return void
	 * @access public
	 */
	public function addCurrentPasswordValidation()
	{
		$this->validate['password_current'] = array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter your current password'
		);
	}
	
	
	/**
	 * Remove personal details validation.
	 * 
	 * @return void
	 * @access public
	 */
	public function removePersonalDetailsValidation()
	{
		unset($this->validate['first_name']);
		unset($this->validate['last_name']);
		unset($this->validate['email']);		
	}

	/**
	 * Get customer.
	 * 
	 * @param int $customerID
	 * @return array
	 * @access public
	 */
	public function getCustomer($customerID)
	{
		return $this->find('first', array(
			'conditions' => array('Customer.id' => $customerID),
			'recursive' => -1
		));
	}
	
}
 


