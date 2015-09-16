<?php

/**
 * Attribute Value Model
 * 
 */
class AttributeValue extends AppModel
{
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array('Attribute');

	/**
	 * The column name(s) and direction(s) to order find results by default.
	 *
	 * @var string
	 * @access public
	 */
	public $order = array('AttributeValue.sort ASC');
	
	/**
	 * Bind attribute value name(s) to attribute value
	 *
	 * @param object $model
	 * @param mixed $languageID
	 * @param bool $reset
	 * @return void
	 * @access public
	 */
	public function bindName($languageID = null, $reset = false)
	{
		$this->bind($this, 'AttributeValueName', $languageID, $reset);
	}
	
	/**
	 * Get values.
	 * 
	 * @param int $optionID
	 * @param bool $reKey [optional]
	 * @return array
	 * @access public
	 */
	public function getValues($optionID, $reKey = false)
	{
		$this->bindName(null, false);
		
		$records = $this->find('all', array('conditions' => array(
			'AttributeValue.attribute_id' => $optionID
		)));
		
		if ($reKey)
		{
			$this->_reKey($records);
		}
		
		return $records;
		
	}
	
	/**
	 * Save custom option values.
	 * 
	 * @param array $data
	 * @return void
	 * @access public
	 */
	public function saveValues($data)
	{
		if (!empty($data['Attribute']['value_names_json']))
		{
			$valueNames = json_decode($data['Attribute']['value_names_json']);
			$newValueID = $this->saveNewValue($valueNames, $data['Attribute']['id']);
			
			$this->AttributeValueName->saveNames($valueNames);
			
			$this->saveSorts($data, $newValueID);
			$this->deleteValues($data);
			
		}
	}
	
	/**
	 * Delete values.
	 * 
	 * @param array $data
	 * @return void
	 * @access public
	 */
	public function deleteValues($data)
	{
		if (empty($data['ValueDelete']))
		{
			return;
		}
		
		foreach ($data['ValueDelete'] as $valueID => $delete)
		{
			if (!empty($delete))
			{
				$this->delete($valueID);
			}
		}
	}
	
	/**
	 * Save new custom option value.
	 * 
	 * @param array $valueNames
	 * @param int $optionID
	 * @return int
	 * @access private
	 */
	private function saveNewValue($valueNames, $optionID)
	{
		$newValueID = 0;
		$newValues = array();
		$languages = Configure::read('Runtime.languages');
		
		foreach ($languages as $languageID => $language)
		{
			if (!empty($valueNames->new->{$languageID}))
			{
				$newValues[$languageID] = $valueNames->new->{$languageID};
			}
		}
		
		if (count($languages) == count($newValues))
		{
			$this->create();
			$this->save(array('AttributeValue' => array(
				'attribute_id' => $optionID
			)));
			
			$newValueID = $this->getInsertID();
			
			foreach ($languages as $languageID => $language)
			{
				$this->AttributeValueName->create();
				$this->AttributeValueName->save(array('AttributeValueName' => array(
					'attribute_value_id' => $newValueID,
					'language_id' => $languageID,
					'name' => $newValues[$languageID]
				)));
			}
		}
		
		return $newValueID;
		
	}
	
	/**
	 * Sort value sort order.
	 * 
	 * @param array $data
	 * @return void
	 * @access public
	 */
	private function saveSorts($data, $newValueID)
	{
		if (empty($data['Attribute']['sort_hash']))
		{
			return;
		}
		
		$nodes = explode(';', $data['Attribute']['sort_hash']);
		if (empty($nodes))
		{
			return;
		}
		
		foreach ($nodes as $node)
		{
			$values = explode('&', $node);
			
			if (empty($values))
			{
				continue;
			}
				
			foreach ($values as $sort => $value)
			{
				$optionAndValue = explode('[]=', $value);
				$optionID = substr($optionAndValue[0], 5);
				$valueID = $optionAndValue[1];
				
				if ($valueID == 'new' && !empty($newValueID))
				{
					$valueID = $newValueID;	
				}
				
				$this->Attribute->AttributeValue->updateAll(
					array('AttributeValue.sort' => $sort),
					array('AttributeValue.attribute_id' => $optionID, 'AttributeValue.id' => $valueID)
				);
			}
		}		
	}
	
	/**
	 * Re-key values.
	 * 
	 * @param array $records
	 * @return array
	 * @access private
	 */
	private function _reKey(&$records)
	{
		foreach ($records as $k => $record)
		{
			$names = $record['AttributeValueName'];
			unset($records[$k]['AttributeValueName']);
						
			foreach ($names as $name)
			{
				$languageID = $name['language_id'];
				$records[$k]['AttributeValueName'][$languageID] = $name;
			}
		}
		
		return $records;		
	}
	
	
}




