<?php

/**
 * Attribute Name Model
 * 
 */
class AttributeName extends AppModel
{
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array('Attribute');
	
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
		$this->validate['attribute_id'] = array(
			'rule' => array('greaterThan', 'attribute_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Attribute ID missing'
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
			if (empty($this->data['AttributeName']['name']))
			{
				return false;
			}
		}
		
		return true;
		
	}
	
}




