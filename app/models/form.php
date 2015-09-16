<?php

class Form extends AppModel {
	public $useTable = false;

	public $validate = array();

	public function addValidation($formType)
	{
		$method = lcfirst(Inflector::camelize($formType)) . 'Validation';
		if(method_exists($this, $method)){
			return $this->{$method}();
		}
	}

	public function saveForm($formType)
	{
		$method = lcfirst(Inflector::camelize($formType)) . 'SaveForm';
		if(method_exists($this, $method)){
			return $this->{$method}();
		}
	}

	public function catalogueValidation(){
		$this->validate = array(
			'title' => array(
				'rule' => 'notEmpty',
				'message' => 'Please select your title',
			),
			'first_name' => 'notEmpty',
			'surname' => 'notEmpty',
			'email' => array(
				'rule' => 'email',
				'message' => 'Please enter your email address',
			),
			'telephone' => 'notEmpty',
			'address_line_1' => 'notEmpty',
			'town' => 'notEmpty',
			'county' => 'notEmpty',
			'postcode' => 'notEmpty',
		);
	}

	public function tradeRegistrationValidation(){
		$this->setSource('customers');

		$this->validate = array(
			'company_name' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Please enter your company name'

			),
			'first_name' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Please enter your first name'

			),
			'last_name' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Please enter your last name'

			),
			'email' => array(
				'validEmail' => array(
					'rule' => 'email',
					'required' => true,
					'allowEmpty' => false,
					'message' => 'Please enter your email address'
				),
			),
			'phone' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Please enter a phone number'
			)
		);

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
		$this->validate['email']['unique'] = array(
			'rule' => 'isUnique',
			'message' => 'The email address you entered already exists in our database'
		);
	}

	public function tradeRegistrationSaveForm()
	{
		$this->data[$this->alias]['approved'] = 0;
		$this->data[$this->alias]['trade'] = 1;
		$this->data[$this->alias]['allow_payment_by_account'] = 1;
		return $this->save($this->data);
	}
}