<?php

/**
 * Attribute Value Name Model
 * 
 */
class AttributeValueName extends AppModel
{
	/**
	 * List of validation rules. 
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(
		'attribute_value_id' => array(
			'rule' => array('greaterThan', 'attribute_value_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Value ID missing'
		),
		'language_id' => array(
			'rule' => array('greaterThan', 'language_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Language ID missing'
		),
		'name' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Name missing'
		)
	);
	
	/**
	 * Called during save operations, before validation. Please note that custom
	 * validation rules can be defined in $validate.
	 *
	 * @return boolean True if validate operation should continue, false to abort
	 * @param $options array Options passed from model::save(), see $options of model::save().
	 * @access public
	 */
	public function beforeValidate($options = array()) 
	{
		$this->data['AttributeValueName']['url'] = Inflector::slug(strtolower($this->data['AttributeValueName']['name']), '-');
		
		return true;
	}
		
	/**
	 * Save custom option value names.
	 * 
	 * @param array $data
	 * @return void
	 * @access public
	 */	
	public function saveNames($data)
	{
		if (empty($data))
		{
			return false;	
		}
		
		foreach ($data as $valueID => $value)
		{
			foreach ($value as $langID => $name)
			{
				$id = $this->field('id', array(
					'attribute_value_id' => $valueID,
					'language_id' => $langID
				));
				
				if (!empty($id))
				{
					$this->id = $id;
					$this->save(array('AttributeValueName' => array(
						'attribute_value_id' => $valueID,
						'language_id' => $langID,
						'name' => $name
					)));
				}
				else
				{
					$this->create();
					$this->save(array('AttributeValueName' => array(
						'attribute_value_id' => $valueID,
						'language_id' => $langID,
						'name' => $name
					)));
				}
				
			}
		}
		
	}	
	
	
}


