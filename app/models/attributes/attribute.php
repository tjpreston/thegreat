<?php

/**
 * Attribute Model
 * 
 */
class Attribute extends AppModel
{
	/**
	 * Detailed list of hasMany associations.
	 *
	 * @var array
	 * @access public
	 */
	public $hasMany = array('AttributeValue');
	
	/**
	 * Detailed list of hasAndBelongsToMany associations.
	 *
	 * @var array
	 * @access public
	 */
	public $hasAndBelongsToMany = array('AttributeSet' => array(
		'with' => 'AttributeSetsAttribute'
	));
	
	/**
	 * Called after each successful save operation.
	 *
	 * @param boolean $created True if this save created a new record
	 * @access public
	 */
	public function afterSave($created)
	{
		$this->AttributeValue->saveValues($this->data);	
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
		$this->bind($this, 'AttributeName', $languageID, $reset);
	}
	
	/**
	 * Get attributes. Optionally filter by ids.
	 *
	 * @param array $attributeIDs [optional]
	 * @return array
	 * @access public
	 */
	public function getAttributes($attributeIDs = array())
	{
		$this->bindName(Configure::read('Languages.main_lang_id'), false);
		$this->unbindModel(array('hasAndBelongsToMany' => array('AttributeSet')));
		
		$conditions = array();
		
		if (!empty($attributeIDs))
		{
			$conditions =  array('Attribute.id' => $attributeIDs);
		}
		
		return $this->find('all', array('conditions' => $conditions));
		
	}

	/**
	 * Get attribute by set.
	 *
	 * @param int $setID
	 * @return array
	 * @access public
	 */
	public function getAttributesBySet($setID)
	{
		$this->bindName(Configure::read('Languages.main_lang_id'), false);
		$this->unbindModel(array('hasAndBelongsToMany' => array('AttributeSet')));
		
		return $this->find('all', array(
			'joins' => array(
				array(
					'table' => 'attribute_sets_attributes',
					'alias' => 'AttributeSetsAttribute',
					'type' => 'INNER',
					'conditions'=> array('AttributeSetsAttribute.attribute_id = Attribute.id')
				)
			),
			'conditions' => array('AttributeSetsAttribute.attribute_set_id' => $setID)
		));
		
	}
	
	/**
	 * Add values to passed attributes.
	 * 
	 * @param array $records Attributes to add values to.
	 * @param array $limitValueIDs Limit attribute values to passed IDs.
	 * @return array
	 * @access public
	 */
	public function addValuesToAttributes($records, $limitValueIDs = array())
	{
		$this->AttributeValue->bindName(Configure::read('Languages.main_lang_id'), false);

		$attributes = array();
		
		// Re-index attributes by attribute ID
		foreach ($records as $k => $attribute)
		{
			$aID = $attribute['Attribute']['id'];
			$attributes[$aID] = $attribute;
			
			$conditions = array('AttributeValue.attribute_id' => $aID);
			
			if (!empty($limitValueIDs))
			{
				$conditions['AttributeValue.id'] = $limitValueIDs;
			}
			
			$values = array();
			$valuesTemp = $this->AttributeValue->find('all', array(
				'conditions' => $conditions
			));
			
			// Re-index attribute values by value ID
			foreach ($valuesTemp as $v)
			{
				$values[$v['AttributeValue']['id']] = $v;
			}
			
			$attributes[$aID]['AttributeValue'] = $values;
			
		}
		
		return $attributes;
		
	}

	/**
	 * Save a new custom option record.
	 * 
	 * @return mixed
	 * @access public 
	 */
	public function saveNewOption()
	{
		$data = $this->data;
		$names = $data['AttributeName'];
		
		$this->bindName($this, null, false);
		
		if (!$this->AttributeName->saveAll($names, array('validate' => 'only')))
		{			
			return false;
		}
		
		$this->create();
		$result = $this->save($data);

		if (!$result)
		{			
			return false;
		}
		
		$optionID = $this->getInsertID();
		
		foreach ($names as $k => $name)
		{
			$names[$k]['attribute_id'] = $optionID;
		}
		
		$this->AttributeName->addOptionIDValidation();
		$namesResult = $this->AttributeName->saveAll($names);

		return $optionID;
	
	}
	
	/**
	 * Make list of passed attributes.
	 * 
	 * @param array $attributes
	 * @return array
	 * @access public
	 */
	public function getList($attributes)
	{
		$list = array();
		
		foreach ($attributes as $attribute)
		{
			$list[$attribute['Attribute']['id']] = $attribute['AttributeName']['name'];
		}
		
		return $list;
		
	}
	
}


