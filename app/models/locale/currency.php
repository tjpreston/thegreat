<?php

/**
 * Currency Model
 * 
 */
class Currency extends AppModel
{
	/**
	 * List of validation rules. 
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(
		'exchange_rate' => array(
			'rule' => array('greaterThan', 'exchange_rate', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Exchange rate missing'
		),
	);
	
	/**
	 * Called before each save operation, after validation. Return a non-true result
	 * to halt the save.
	 *
	 * @return boolean True if the operation should continue, false if it should abort
	 * @access public
	 */
	public function beforeSave($options = array()) 
	{
		if (!empty($this->data['Currency']['exchange_rate']))
		{
			$this->data['Currency']['exchange_rate_from'] = Configure::read('Currencies.main_currency_id');
		}
		
		return true;
		
	}
	
}





