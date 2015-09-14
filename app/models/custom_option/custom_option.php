<?php

/**
 * Custom Option Model
 * 
 */
class CustomOption extends AppModel
{
	/**
	 * Detailed list of hasMany associations.
	 *
	 * @var array
	 * @access public
	 */
	public $hasMany = array('CustomOptionValue');
	
	/**
	 * Called after each successful save operation.
	 *
	 * @param boolean $created True if this save created a new record
	 * @access public
	 */
	public function afterSave($created)
	{
		$this->CustomOptionValue->saveValues($this->data);	
	}
	
	/**
	 * Bind custom option name(s) to custom option
	 *
	 * @param object $model
	 * @param mixed $languageID
	 * @param bool $reset
	 * @return void
	 * @access public
	 */
	public function bindName($model, $languageID = null, $reset = false)
	{
		$this->bind($model, 'CustomOptionName', $languageID, $reset);
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
		$names = $data['CustomOptionName'];
		
		$this->bindName($this, null, false);
		
		if (!$this->CustomOptionName->saveAll($names, array('validate' => 'only')))
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
			$names[$k]['custom_option_id'] = $optionID;
		}
		
		$this->CustomOptionName->addOptionIDValidation();
		$namesResult = $this->CustomOptionName->saveAll($names);

		return $optionID;
	
	}
	
	/**
	 * Get list of custom options.
	 * 
	 * @param int $langID [optional]
	 * @return array $list
	 * @access public
	 */
	public function getList($langID = null)
	{
		$list = array();
		
		if (empty($langID))
		{
			$langID = Configure::read('Languages.main_lang_id');
		}
		
		$this->bindName($this, $langID);
		$this->unbindModel(array('hasMany' => array('CustomOptionValue')));
		
		foreach ($this->find('all') as $k => $record)
		{
			$id = $record['CustomOption']['id'];
			$name = $record['CustomOptionName']['name'];
			
			$list[$id] = $name;
		}
		
		return $list;
		
	}
	
	
}

