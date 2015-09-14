<?php

/**
 * Product Option Name Model
 * 
 */
class ProductOptionName extends AppModel
{
	/**
	 * List of validation rules. 
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(		
		'language_id' => array(
			'rule' => array('greaterThan', 'language_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Language missing'
		),
		'name' => array(
			'rule' => array('checkAllNamesSupplied'),
			'message' => 'Product Option name(s) missing'
		),
	);
	
	/**
	 * Add product ID validation.
	 * 
	 * @return void
	 * @access public
	 */
	public function addProductOptionIDValidation()
	{
		$this->validate['product_option_id'] = array(
			'rule' => array('greaterThan', 'product_option_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Product Option ID missing'
		);
	}
	
	/**
	 * Validation method for checking name passed for all languages.
	 * 
	 * @return bool
	 * @access public
	 */
	public function checkAllNamesSupplied()
	{
		foreach (Configure::read('Runtime.languages') as $languageID => $language)
		{
			if (empty($this->data['ProductOptionName']['name']))
			{
				return false;
			}
		}
		
		return true;
		
	}

}



