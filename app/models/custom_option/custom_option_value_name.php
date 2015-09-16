<?php

/**
 * Custom Option Value Name Model
 * 
 */
class CustomOptionValueName extends AppModel
{
	/**
	 * List of validation rules. 
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(
		'custom_option_value_id' => array(
			'rule' => array('greaterThan', 'custom_option_value_id', 0),
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
					'custom_option_value_id' => $valueID,
					'language_id' => $langID
				));
				
				if (!empty($id))
				{
					$this->id = $id;
					$this->saveField('name', $name, true);
				}
				else
				{
					$this->create();
					$this->save(array('CustomOptionValueName' => array(
						'custom_option_value_id' => $valueID,
						'language_id' => $langID,
						'name' => $name
					)));
				}
				
			}
		}
		
	}
	
}



