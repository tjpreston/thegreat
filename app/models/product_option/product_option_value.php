<?php

/**
 * Product Option Value
 * 
 */
class ProductOptionValue extends AppModel
{
	/**
	 * The column name(s) and direction(s) to order find results by default.
	 *
	 * @var string
	 * @access public
	 */
	public $order = 'ProductOptionValue.sort ASC';
	
	/**
	 * List of validation rules. 
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(
		'product_option_id' => array(
			'rule' => array('greaterThan', 'product_option_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Product Option ID missing'
		),
		'custom_option_value_id' => array(
			'rule' => array('greaterThan', 'custom_option_value_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Custom Option Value ID missing'
		)
	);
	
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
		$this->bind($model, 'ProductOptionValueName', $languageID, $reset);
	}
	
	/**
	 * Add a new product option value.
	 *
	 * @param int $productID
	 * @param array $data [$productOptionID => $newCustomOptionValueID]
	 * @return void
	 * @access public
	 */
	public function saveNewValues($productID, $data)
	{
		foreach ($data as $productOptionID => $newCustomOptionValueID)
		{
			$exists = $this->find('count', array('conditions' => array(
				'ProductOptionValue.product_option_id' => $productOptionID,
				'ProductOptionValue.custom_option_value_id' => $newCustomOptionValueID
			)));
			
			if (!empty($exists))
			{
				continue;
			}
			
			// Get last sort
			$lastSortValue = $this->field(
				'sort', array('ProductOptionValue.product_option_id' => $productOptionID), 'ProductOptionValue.sort DESC'
			);
			
			$lastSortValue = (is_int($lastSortValue)) ? $lastSortValue++ : 0;
			
			$this->create();
			$this->save(array('ProductOptionValue' => array(
				'product_option_id' => $productOptionID,
				'custom_option_value_id' => $newCustomOptionValueID,
				'sort' => $lastSortValue
			)));
			
			$newProductOptionValueID = $this->getInsertID();
			
			ClassRegistry::init('ProductOptionStock')->updateVariationStock($productID, $newProductOptionValueID);
			
		}
		
	}
	
	public function getValueIDs($productID)
	{
		$this->bindModel(array('belongsTo' => array('ProductOption')));
		
		$valueIDs = array();
		
		$options = $this->ProductOption->getOptions($productID, 'both', null, true);
		$options = $this->ProductOption->ProductOptionValue->addValuesToOptions($options, array(
			'get_prices' => true,
			'get_names' => true,
			'rekey' => true
		));
		
		$optionsCount = count($options);		
		if (empty($optionsCount))
		{
			return array();
		}
		
		$values = array();
		$valuesCount = array();

		foreach ($options as $k => $option)
		{
			if (empty($option['ProductOptionValue']))
			{			
				unset($options[$k]);
				continue;
			}
			
			$values[$k] = array();
			
			foreach ($option['ProductOptionValue'] as $value)
			{
				$vID = $value['ProductOptionValue']['id'];
				$vName = $value['CustomOptionValueName']['name'];
				$values[$k][] = array($vID, $vName);
			}
			
			$valuesCount[] = count($values[$k]);
			
		}
		
		$optionsCount = count($options);
		if (empty($optionsCount))
		{
			return array();
		}
		
		$totalValues = array_product($valuesCount) * $optionsCount;
		
		$inOption = 0;
		$inValue = array();
		
		foreach ($values as $k => $value)
		{
			$inValue[$k] = 0;
		}
		
		$optionKeys = array_keys($inValue);
		$lastKey = max($optionKeys);
		
		$fieldName = '';
		
		for ($i = 1; $i <= $totalValues; $i++)
		{
			$valueKey = $inValue[$inOption];
			$fieldName .= $values[$inOption][$valueKey][0] . '-';
			
			$inOption++;
			
			if ($inOption >= $optionsCount)
			{
				$inValue[$lastKey]++;			
				
				if ($inValue[$lastKey] == $valuesCount[$lastKey])
				{
					$inValue[$lastKey] = 0;
					
					if ($optionsCount > 1)
					{
						$vals = array_reverse(range(0, $lastKey - 1));
						
						foreach ($vals as $v)
						{
							$allZero = true;
							
							for ($l = $v; $l >= 0; $l--)
							{
								if ($inValue[$v + 1] != 0)
								{
									$allZero = false;
									break 2;
								}
							}
							
							if ($allZero)
							{
								$inValue[$v]++;
							}
							
							if ($inValue[$v] >= $valuesCount[$v])
							{
								$inValue[$v] = 0;
							}
							
						}
					}
				}
				
				$fieldName = substr($fieldName, 0, -1);
				
				$valueIDs[] = $fieldName;		
				
				$fieldName = '';
				
				$inOption = 0;
				
			}	
		
		}
		
		return $valueIDs;
	
	}
	
	
	
	
	
	/**
	 * Save new value option.
	 * 
	 * @param array $data
	 * @return void
	 * @access public
	 */
	/*
	public function saveNewValue($data)
	{
		$this->create();
		$result = $this->save(array('ProductOptionValue' => $data));
		
		if (!$result)
		{
			return false;
		}
		
	}
	*/
	
	/**
	 * Add option values to passed options array.
	 * 
	 * @param array $options
	 * @param array $args
	 * @access public
	 */
	public function addValuesToOptions($options, $args = array())
	{
		foreach ($options as $k => $v)
		{
			$options[$k]['ProductOptionValue'] = $this->getOptionValues(
				$v['ProductOption']['id'], $args
			);
		}
		
		return $options;
		
	}
	
	/**
	 * Get option values
	 * 
	 * @param int $productOptionID
	 * @param array $args
	 * @return array
	 * @access public
	 */
	public function getOptionValues($productOptionID, $args = array())
	{
		$valueID    = (!empty($args['value_id'])) ? $args['value_id'] : null;
		$currencyID = (!empty($args['currency_id'])) ? $args['currency_id'] : null;
		$languageID = (!empty($args['language_id'])) ? $args['language_id'] : Configure::read('Languages.main_lang_id');
		
		$getPrices  = (isset($args['get_prices'])) ? $args['get_prices'] : false;
		$getNames   = (isset($args['get_names'])) ? $args['get_names'] : false;
		$rekey      = (isset($args['rekey'])) ? $args['rekey'] : false;
		
		if (is_null($languageID))
		{
			$languageID = Configure::read('Languages.main_lang_id');
		}
		
		$findType = 'all';
		$fields = null;
		$joins = null;
		$conditions = array('ProductOptionValue.product_option_id' => $productOptionID);
		
		if (!empty($valueID) && is_numeric($valueID))
		{
			$findType = 'first';
		}
		
		if (!empty($valueID))
		{
			$conditions['ProductOptionValue.id'] = $valueID;
		}
		
		$fields = array('ProductOptionValue.*', 'CustomOptionValue.*');
		
		/*
		if ($getPrices)
		{
			$this->bindPrice($currencyID);
			if (!is_null($currencyID))
			{
				$fields[] = 'ProductOptionValuePrice.*';
			}
		}
		*/
		
		$joins = array(
			array(
				'table' => 'custom_option_values',
				'alias' => 'CustomOptionValue',
				'type'  => 'INNER',
				'conditions' => array('ProductOptionValue.custom_option_value_id = CustomOptionValue.id')
			)
		);
		
		if ($getNames)
		{
			$nameConditions = array('CustomOptionValueName.custom_option_value_id = CustomOptionValue.id');
			if (!is_null($languageID))
			{
				$nameConditions[] = 'CustomOptionValueName.language_id = ' . $languageID;
			}
			
			$joins[] = array(
				'table' => 'custom_option_value_names',
				'alias' => 'CustomOptionValueName',
				'type'  => 'INNER',
				'conditions' => $nameConditions
			);
			
			$fields[] = 'CustomOptionValueName.*';
		}
		
		$values = $this->find($findType, array(
			'fields' => $fields,
			'joins' => $joins,
			'conditions' => $conditions
		));
		
		if ($rekey)
		{
			foreach ($values as $k => $value)
			{
				if (isset($value['ProductOptionValuePrice']) && is_array($value['ProductOptionValuePrice']))
				{
					$values[$k]['ProductOptionValuePrice'] = array();
					foreach ($value['ProductOptionValuePrice'] as $price)
					{
						$values[$k]['ProductOptionValuePrice'][$price['currency_id']] = $price;
					}
				}
			}
		}
		
		return $values;
		
	}
	
}





