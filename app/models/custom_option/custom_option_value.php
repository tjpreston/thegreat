<?php

/**
 * Custom Option Value Model
 * 
 */
class CustomOptionValue extends AppModel
{
	/**
	 * The column name(s) and direction(s) to order find results by default.
	 *
	 * @var string
	 * @access public
	 */
	public $order = 'CustomOptionValue.sort';
	
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array('CustomOption');

	/**
	 * Bind custom option value name(s) to custom option value
	 *
	 * @param object $model
	 * @param mixed $languageID
	 * @param bool $reset
	 * @return void
	 * @access public
	 */
	public function bindName($model, $languageID = null, $reset = false)
	{
		$this->bind($model, 'CustomOptionValueName', $languageID, $reset);
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
		$this->bindName($this, null, false);
		
		$records = $this->find('all', array('conditions' => array(
			'CustomOptionValue.custom_option_id' => $optionID
		)));
		
		if ($reKey)
		{
			$this->_reKey($records);
		}
		
		return $records;
		
	}
	
	/**
	 * Get list of all values of all options
	 * 
	 * @param array $getOptions [optional]
	 * @return array $list
	 * @access public 
	 */
	public function getValuesList($getOptions = array())
	{
		$list = array();
		$conditions = array();
				
		if (!empty($getOptions))
		{
			$conditions = array('CustomOption.id' => $getOptions);
		}
		
		$options = $this->CustomOption->find('list', array('conditions' => $conditions));
		
		$this->bindName($this, 0, false);
		
		foreach ($options as $optionID)
		{
			$list[$optionID] = array();
			
			$values = $this->find('all', array('conditions' => array(
				'CustomOptionValue.custom_option_id' => $optionID
			)));
			
			foreach ($values as $k => $value)
			{
				$valueID = $value['CustomOptionValue']['id'];
				$valueName = $value['CustomOptionValueName']['name'];
				$list[$optionID][$valueID] = $valueName;
			}
			
		}
		
		return $list;
		
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
		if (!empty($data['CustomOption']['value_names_json']))
		{
			$valueNames = json_decode($data['CustomOption']['value_names_json']);
			$newValueID = $this->saveNewValue($valueNames, $data['CustomOption']['id']);
			
			$this->CustomOptionValueName->saveNames($valueNames);
			
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
			$this->save(array('CustomOptionValue' => array(
				'custom_option_id' => $optionID
			)));
			
			$newValueID = $this->getInsertID();

			foreach ($languages as $languageID => $language)
			{
				$this->CustomOptionValueName->create();
				$this->CustomOptionValueName->save(array('CustomOptionValueName' => array(
					'custom_option_value_id' => $newValueID,
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
		if (empty($data['CustomOption']['sort_hash']))
		{
			return;
		}
		
		$nodes = explode(';', $data['CustomOption']['sort_hash']);
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
				
				$this->CustomOption->CustomOptionValue->updateAll(
					array('CustomOptionValue.sort' => $sort),
					array('CustomOptionValue.custom_option_id' => $optionID, 'CustomOptionValue.id' => $valueID)
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
			$names = $record['CustomOptionValueName'];
			unset($records[$k]['CustomOptionValueName']);
						
			foreach ($names as $name)
			{
				$languageID = $name['language_id'];
				$records[$k]['CustomOptionValueName'][$languageID] = $name;
			}
		}
		
		return $records;		
	}
	
}

