<?php

/**
 * Application Model
 * 
 */
class AppModel extends Model
{
	/**
	 * Site mode
	 *
	 * @var string
	 * @access protected
	 */
	protected $mode;
	
	/**
	 * Logged in user customer record
	 *
	 * @var array
	 * @access protected
	 */
	protected $customer = array();

	public $actsAs = array('Containable');
	
	/**
	 * Global validation method.
	 * Checks value from passed specified by passed key is greater than passed param.
	 *
	 * @param array $data
	 * @param string $key
	 * @param int $param
	 * @return bool
	 * @access protected
	 */
	protected function greaterThan($data, $key, $param) 
	{
		if (is_numeric($data[$key]) && ($data[$key] > $param))
		{
			return true;
		}
		
		return false;
	}
	
	/**
	 * Global validation method
	 * Checks 2 incoming fields are identical (eg email addresses, passwords)
	 *
	 * @return bool
	 * @access protected
	 */
	protected function validateIdenticalFieldValues($field = array(), $compare_field = null) 
    {
        foreach ($field as $key => $value)
		{
            $v1 = $value;
            $v2 = $this->data[$this->name][$compare_field];
			
            if ($v1 !== $v2)
			{
				return false;
			}
			continue;
		}
		
        return true;
    }
	
	/**
	 * Perform an on-the-fly model association bind
	 *
	 * @param array $model Model to bind on
	 * @param string $modelToBind Model to bind
	 * @param int $languageID Language ID
	 * @param bool $reset Reset association
	 * @return bool
	 * @access protected
	 */
	protected function bind($model, $modelToBind, $languageID, $reset)
	{
		if (is_null($languageID))
		{
			$bind = array('hasMany' => array());
			$bind['hasMany'][$modelToBind] = array(
				'className' => $modelToBind,
				'dependent' => true
			);
		}
		else
		{
			if (empty($languageID))
			{
				$languageID = Configure::read('Languages.main_lang_id');
			}
			
			$bind = array('hasOne' => array());
			$bind['hasOne'][$modelToBind] = array(
				'className' => $modelToBind,
				'conditions' => array($modelToBind . '.language_id' => $languageID)
			);
		}
		
		$model->bindModel($bind, $reset);
		
	}
	
	/**
	 * Bind prices(s)
	 *
	 * @param mixed $currencyID
	 * @param bool $reset
	 * @return object
	 * @access public
	 */
	public function bindPrice($currencyID = null, $reset = false)
	{
		$modelToBindName = $this->name . 'Price';
		
		if (is_null($currencyID))
		{
			$bind = array('hasMany' => array());
			$bind['hasMany'][$modelToBindName] = array(
				'className' => $modelToBindName
			);
		}
		else
		{
			if (empty($currencyID))
			{
				$currencyID = Configure::read('Currencies.main_currency_id');
			}
			
			$bind = array('hasOne' => array());
			$bind['hasOne'][$modelToBindName] = array(
				'className' => $modelToBindName,
				'conditions' => array($modelToBindName . '.currency_id' => $currencyID)
			);
		}
		
		$this->bindModel($bind, $reset);
		
		return $this;
		
	}
	
	/**
	 * Get model names
	 * 
	 * @param int $recordID
	 * @return array $output
	 * @access public
	 */
	public function getNames($fk, $recordID)
	{
		$records = $this->find('all', array(
			'conditions' => array($this->name . '.' . $fk => $recordID),
			'recursive' => -1
		));
		
		$output = array();
		
		foreach ($records as $k => $record)
		{
			$languageID = $record[$this->name]['language_id'];
			$output[$languageID] = $record[$this->name];
		}
		
		return $output;
		
	}
	
	/**
	 * Get collection.
	 *
	 * @return array
	 * @access public
	 */
	public function getCollection()
	{
		return $this->find('first', array(
			'conditions' => array($this->name . '.id' => $this->getCollectionID())
		));
	}
	
	/**
	 * Get clean array of posted product options.
	 * 
	 * @param array $data
	 * @return array $dataOptions
	 * @access public
	 */
	public function getPostedProductOptions($data)
	{
		$dataOptions = array();
	
		if (empty($data['ProductOption']))
		{
			return $dataOptions;
		}
		
		foreach ($data['ProductOption'] as $k => $v)
		{
			if (strpos($k, '-') === false)
			{
				continue;
			}
			
			$temp = explode('-', $k);
			$optionID = intval($temp[1]);
			$valueID  = intval($v);
			
			if (empty($optionID) || empty($valueID))
			{
				continue;
			}
			
			$dataOptions[$optionID] = $valueID;
			
		}
		
		return $dataOptions;
		
	}
	
	private function getCollectionVars()
	{
		$vars = array();
		
		$vars['modelName'] = $this->name;
		$vars['tableName'] = $this->table;
				
		if ($vars['modelName'] == 'BasketItem') 
		{
			$vars['collection'] = $this->Basket;
			$vars['fieldName'] = 'basket_id';
		 	$vars['joinFieldName'] = 'basket_item_id';
			// $vars['optionModelName'] = 'BasketItemOptionValue';
			// $vars['optionModel'] = $this->BasketItemOptionValue;
		}
		else
		{
			$vars['collection'] = $this->Wishlist;
			$vars['fieldName'] = 'wishlist_id';
		 	$vars['joinFieldName'] = 'wishlist_item_id';
			// $vars['optionModelName'] = 'WishlistItemOptionValue';
			// $vars['optionModel'] = $this->WishlistItemOptionValue;
		}
		
		return $vars;
		
	}
	
	/**
	 * Set session ID.
	 * 
	 * @param string $sessionID
	 * @return void
	 * @access public
	 */
	public function setSessionID($sessionID)
	{
		$this->sessionID = $sessionID;
	}
	
	/**
	 * Set customer.
	 * 
	 * @param array $customer
	 * @return void
	 * @access public
	 */
	public function setCustomer($customer)
	{
		$this->customer = $customer;
	}
	
	/**
	 * Remove product ID validation.
	 * 
	 * @return void
	 * @access public
	 */
	public function removeProductIDvalidation()
	{
		unset($this->validate['product_id']);
	}

	/**
	 * Save default sort order per category.
	 * 
	 * @return void
	 * @access protected
	 */
	protected function saveProductCategorySortOrders()
	{
		if (empty($this->data['ProductCategorySort']['hash']))
		{
			return;
		}
		
		$cats = explode(';', $this->data['ProductCategorySort']['hash']);
		if (empty($cats))
		{
			return;
		}		
		
		foreach ($cats as $k => $cat)
		{					
			$prods = explode('&', $cat);
			if (!empty($prods))
			{
				foreach ($prods as $sort => $ids)
				{
					$catAndProd = explode('[]=', $ids);
					$catID = substr($catAndProd[0], 5);
					$prodID = $catAndProd[1];
					
					$this->ProductCategory->updateAll(
						array('ProductCategory.sort' => $sort),
						array('ProductCategory.category_id' => $catID, 'ProductCategory.product_id' => $prodID)
					);
				}
			}
		}

	}	
	
}



