<?php

/**
 * Custom Option Name Model
 * 
 */
class CustomOptionName extends AppModel
{
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array('CustomOption');
	
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
			'message' => 'Language ID missing'
		),
		'name' => array(
			'rule' => array('checkAllNamesSupplied'),
			'message' => 'Custom Option name(s) missing'
		),
	);
	
	/**
	 * Add option ID validation.
	 * 
	 * @return void
	 * @access public
	 */
	public function addOptionIDValidation()
	{
		$this->validate['custom_option_id'] = array(
			'rule' => array('greaterThan', 'custom_option_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Option ID missing'
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
			if (empty($this->data['CustomOptionName']['name']))
			{
				return false;
			}
		}
		
		return true;
		
	}
	
}


