<?php

/**
 * Product Option
 * 
 */
class ProductOption extends AppModel
{
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array('CustomOption');
	
	/**
	 * Detailed list of hasMany associations.
	 *
	 * @var array
	 * @access public
	 */
	public $hasMany = array('ProductOptionValue');
	
	/**
	 * The column name(s) and direction(s) to order find results by default.
	 *
	 * @var string
	 * @access public
	 */
	public $order = 'ProductOption.sort ASC';
	
	/**
	 * List of validation rules. 
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(
		'product_id' => array(
			'rule' => array('greaterThan', 'product_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Product ID missing'
		),
		'custom_option_id' => array(
			'rule' => array('greaterThan', 'custom_option_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Custom Option ID missing'
		)
	);
	
	/**
	 * Called after each successful save operation.
	 *
	 * @param boolean $created True if this save created a new record
	 * @access public
	 */
	public function afterSave($created)
	{
		if (!empty($this->data['ProductOption']['ProductOptionName']))
		{
			$this->ProductOptionName->saveAll($this->data['ProductOption']['ProductOptionName']);
		}
		
		/*
		if (!empty($this->data['ProductOption']['ProductOptionValue']))
		{
			$newValue = $this->data['ProductOption']['ProductOptionValue']['new'];
			unset($this->data['ProductOption']['ProductOptionValue']['new']);
			
			$this->ProductOptionValue->saveNewValue($newValue);
			$this->ProductOptionValue->saveAll($this->data['ProductOption']['ProductOptionValue']);			
		}
		*/
		
	}
	
	/**
	 * Bind product option name(s) to product
	 *
	 * @param object $model
	 * @param mixed $languageID
	 * @param bool $reset
	 * @return void
	 * @access public
	 */
	public function bindName($model, $languageID = null, $reset = false)
	{
		$this->bind($model, 'ProductOptionName', $languageID, $reset);
	}
	
	/**
	 * Save product option value sort order.
	 * 
	 * @param string $hash [optional]
	 * @return void
	 * @access public
	 */
	public function saveProductOptionValueSortOrders($hash = '')
	{
		if (empty($hash))
		{
			return false;
		}		
		
		$options = explode(';', $hash);		
		if (empty($options))
		{
			return false;	
		}
		
		foreach ($options as $k => $option)
		{					
			$values = explode('&', $option);
			if (empty($values))
			{
				continue;
			}
			
			foreach ($values as $sort => $ids)
			{
				$optionAndValue = explode('[]=', $ids);	
				if (empty($optionAndValue[1]))
				{
					continue;
				}
				
				$optionID = substr($optionAndValue[0], 5);
				$valueID  = $optionAndValue[1];
				
				$this->ProductOptionValue->updateAll(
					array('ProductOptionValue.sort' => $sort),
					array('ProductOptionValue.product_option_id' => $optionID, 'ProductOptionValue.id' => $valueID)
				);
			}
		}

	}
	
	/**
	 * Save new product option.
	 *
	 * @return bool
	 * @access public
	 */
	public function saveNewOption($data)
	{
		$exists = $this->find('count', array('conditions' => array(
			'ProductOption.product_id' => $data['product_id'],
			'ProductOption.custom_option_id' => $data['custom_option_id']
		)));
		
		if (!empty($exists))
		{
			return false;
		}
		
		// Check initial value
		$valueExists = $this->CustomOption->CustomOptionValue->find('count', array('conditions' => array(
			'CustomOptionValue.id' => $data['initial_custom_option_value_id'],
			'CustomOptionValue.custom_option_id' => $data['custom_option_id']
		)));
		
		if (empty($valueExists))
		{
			return false;
		}
	
		$names = array('ProductOptionName' => $data['ProductOptionName']);
		if (!$this->ProductOptionName->saveAll($names['ProductOptionName'], array('validate' => 'only')))
		{
			return false;
		}
		
		unset($data['ProductOptionName']);
		
		$this->create();
		$result = $this->save(array('ProductOption' => $data));

		if (!$result)
		{
			return false;
		}
		
		$optionID = $this->getInsertID();
		
		foreach ($names['ProductOptionName'] as $k => $name)
		{
			$names['ProductOptionName'][$k]['product_option_id'] = $optionID;
		}
		
		$this->ProductOptionName->addProductOptionIDValidation();
		
		$namesResult = $this->ProductOptionName->saveAll($names['ProductOptionName']);
		
		$this->ProductOptionValue->saveNewValues($data['product_id'], array($optionID => $data['initial_custom_option_value_id']));
		
		ClassRegistry::init('ProductOptionStock')->initOptionStock($data['product_id']);
		
		return true;
	
	}
	
	/**
	 * Get amount of options assigned to product.
	 * 
	 * @param int $productID
	 * @return int
	 * @access public
	 */
	public function getOptionCount($productID)
	{
		return $this->find('count', array(
			'conditions' => array('ProductOption.product_id' => $productID),
		));
	}
	
	/**
	 * Get product options.
	 * 
	 * @param int $productID
	 * @param string $getNames 'both', 'none', 'customonly', 'productonly'
	 * @param mixed $languageID
	 * @param bool $rekey
	 * @return array
	 * @access public
	 */
	public function getOptions($productID, $getNames = 'none', $languageID = null, $rekey = false)
	{
		$this->unbindModel(array('hasMany' => array('ProductOptionName')), false);
	
		$fields = array('ProductOption.*');
		$joins = array();
		
		if ($getNames == 'customonly' || $getNames == 'both')
		{
			$customOptionLanguageID = (is_null($languageID)) ? Configure::read('Languages.main_lang_id') : $languageID;
		
			$joins[] = array(
				'table' => 'custom_option_names',
				'alias' => 'CustomOptionName',
				'type'  => 'INNER',
				'conditions' => array(
					'CustomOptionName.custom_option_id = ProductOption.custom_option_id',
					'CustomOptionName.language_id' => $customOptionLanguageID
				)
			);
			
			$fields[] = 'CustomOptionName.*';
			
		}
		
		if ($getNames == 'productonly' || $getNames == 'both')
		{
			$this->bindName($this, $languageID, false);
			// $fields[] = 'ProductOptionName.*';
			
			/*
			if (!empty($languageID))
			{
				$fields[] = 'ProductOptionName.*';
			}
			*/
			
			/*
			$joins[] = array(
				'table' => 'product_option_names',
				'alias' => 'ProductOptionName',
				'type'  => 'INNER',
				'conditions' => array(
					'ProductOptionName.product_option_id = ProductOption.id',
					'ProductOptionName.language_id' => $languageID
				)
			);
			*/
			
		}
		
		$options = $this->find('all', array(
			'fields' => $fields,
			'joins' => $joins,
			'conditions' => array('ProductOption.product_id' => $productID),
		));
		
		if ($rekey)
		{
			foreach ($options as $k => $option)
			{	
				if (isset($options[$k]['ProductOptionName']))
				{
					$options[$k]['ProductOptionName'] = array();
					foreach ($option['ProductOptionName'] as $name)
					{
						$options[$k]['ProductOptionName'][$name['language_id']] = $name;
					}
				}
			}
		}
		
		return $options;
		
	}
	
	/**
	 * Check all posted options against required options.
	 * 
	 * @param mixed $productIDs
	 * @param array $postedOptions
	 * @return bool
	 * @access public
	 */
	public function allRequiredValuesPosted($productIDs, $postedOptions)
	{
		$ok = true;
		
		if (is_array($productIDs))
		{
			foreach ($productIDs as $id)
			{
				if (!$this->allRequiredValuesPostedForOneProduct($id, $postedOptions))
				{
					$ok = false;
				}
			}
		}
		else if (is_string($productIDs) || is_int($productIDs))
		{
			$ok = $this->allRequiredValuesPostedForOneProduct($productIDs, $postedOptions);
		}
		
		return $ok;
		
	}
	
	/**
	 * Check all posted options against required options.
	 * 
	 * @param int $productIDs
	 * @param array $postedOptions
	 * @return bool
	 * @access private
	 */
	private function allRequiredValuesPostedForOneProduct($productID, $postedOptions)
	{
		foreach ($this->getOptions($productID) as $k => $requiredOption)
		{
			if (!array_key_exists($requiredOption['ProductOption']['id'], $postedOptions))
			{
				return false;
			}
			
			$validValues = Set::extract('/ProductOptionValue/id', $requiredOption);
			
			$intersect = array_intersect($postedOptions, $validValues);
			
			if (empty($intersect))
			{
				return false;
			}
		}
		
		return true;
		
	}
	
}




