<?php

/**
 * Attribute Set Model
 * 
 */
class AttributeSet extends AppModel
{
	/**
	 * Detailed list of hasAndBelongsToMany associations.
	 *
	 * @var array
	 * @access public
	 */
	public $hasAndBelongsToMany = array('Attribute' => array(
		'with' => 'AttributeSetsAttribute'
	));
	
	/**
	 * Called during save operations, before validation. Please note that custom
	 * validation rules can be defined in $validate.
	 *
	 * @return boolean True if validate operation should continue, false to abort
	 * @param $options array Options passed from model::save(), see $options of model::save().
	 * @access public
	 */
	public function beforeValidate()
	{
		if (!isset($this->data['Attribute']['Attribute']) || empty($this->data['Attribute']['Attribute']))
		{
			$this->invalidate('non_existent_field');
			$this->Attribute->invalidate('Attribute', 'Please select at least one attribute');
		}

		$this->data['AttributeSet']['attribute_ids_concat'] = implode(',', $this->data['Attribute']['Attribute']);
		
		return true;
		
	}
	
	/**
	 * Called after every deletion operation.
	 *
	 * @access public
	 */
	public function afterDelete() 
	{
		ClassRegistry::init('Product')->updateAll(
			array('Product.attribute_set_id' => 0),
			array('Product.attribute_set_id' => $this->id)
		);
	}
	
	/**
	 * Bind attribute name(s) to attribute
	 *
	 * @param object $model
	 * @param mixed $languageID
	 * @param bool $reset
	 * @return void
	 * @access public
	 */
	public function bindName($languageID = null, $reset = false)
	{
		$this->bind($this, 'AttributeSetName', $languageID, $reset);
	}
	
	/**
	 * Get list of attribute sets.
	 * 
	 * @return array
	 * @access public
	 */
	public function getAttributeSetList()
	{
		$records = $this->find('all', array(
			'fields' => array('AttributeSet.id', 'AttributeSetName.name')
		));
		
		$list = array();
		
		foreach ($records as $record)
		{
			$list[$record['AttributeSet']['id']] = $record['AttributeSetName']['name'];
		}
		
		return $list;
		
	}
	
	/**
	 * Get all attribute IDs in a given attribute set.
	 * 
	 * @param int $setID
	 * @return array
	 * @access public
	 */
	public function getAttributesIDsInSet($setID)
	{
		$this->Attribute->bindModel(array('hasOne' => array('AttributeSetsAttribute')));
		
		$ids = $this->Attribute->AttributeSetsAttribute->find('list', array(
			'fields' => array('AttributeSetsAttribute.id', 'AttributeSetsAttribute.attribute_id'),
			'conditions' => array('AttributeSetsAttribute.attribute_set_id' => $setID),
			'recursive' => -1
		));
		
		return $ids;
		
	}
	
	
}




