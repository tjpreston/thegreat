<?php

/**
 * Attribute Set Name Model
 * 
 */
class AttributeSetName extends AppModel
{
	/**
	 * List of validation rules. 
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(
		'name' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Attribute Set name missing'
		),
		'language_id' => array(
			'rule' => array('greaterThan', 'language_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Language ID missing'
		),
		
	);
	
}



