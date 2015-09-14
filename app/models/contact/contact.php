<?php

/**
 * Contact Model
 *
 */
class Contact extends AppModel
{
	public $useTable = false;

	/**
	 * List of validation rules. 
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(
		'name' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter your first name'
		),
		'email' => array(
			'rule' => 'email', 
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter your email address'
		),
		'enquiry' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter your enquiry'
		),
	);

}

