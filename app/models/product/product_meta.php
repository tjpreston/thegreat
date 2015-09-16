<?php

/**
 * Product Meta
 * 
 */
class ProductMeta extends AppModel
{
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array('Product', 'Language');
	
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
		'language_id' => array(
			'rule' => array('greaterThan', 'language_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Language ID missing'
		),
		'url' => array(
			'rule' => 'productUrlIsUnique',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'URL missing / un-unique / invalid'
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
	public function beforeValidate()
	{
		if (empty($this->data['ProductMeta']['url']))
		{
			if (empty($this->data['ProductMeta']['product_name']))
			{
				return false;
			}

			$url = $this->data['ProductMeta']['product_name'];
			
			if (Configure::read('Catalog.prefix_product_name_with_sku') && !empty($this->data['ProductMeta']['product_sku']))
			{
				$url = $this->data['ProductMeta']['product_sku'] . '-' . $url;
			}
		}
		else
		{
			$url = $this->data['ProductMeta']['url'];
		}
		
		$this->data['ProductMeta']['url'] = Inflector::slug(strtolower($url), '-');

		/* Check that URL is unique. If not, ammend it. */
		if(!$this->productUrlIsUnique()){
			$this->data['ProductMeta']['url'] .= '-' . $this->data['ProductMeta']['product_id'];
		}

		return true;
		
	}
	
	/**
	 * Check product url is unique to language.
	 * 
	 * @return bool
	 * @access public
	 */
	public function productUrlIsUnique()
	{
		$url = $this->data['ProductMeta']['url'];
		
		if (in_array($url, Configure::read('Catalog.reserved_url_words')))
		{
			return false;
		}
		
		$conditions = array(
			'ProductMeta.language_id' => $this->data['ProductMeta']['language_id'],
			'ProductMeta.url' => $url
		);
		
		if (!empty($this->data['ProductMeta']['id']))
		{
			$conditions['ProductMeta.id !='] = $this->data['ProductMeta']['id'];
		}
		
		$result = $this->find('first', array('conditions' => $conditions));
		
		if (!empty($result))
		{
			return false;
		}
		
		return true;
		
	}
	
	/**
	 * Get product meta
	 * 
	 * @param int $productID
	 * @return array $metas
	 * @access public
	 */
	public function getMetas($productID)
	{
		$records = $this->find('all', array(
			'conditions' => array('ProductMeta.product_id' => $productID),
			'recursive' => -1
		));
		
		$metas = array();
		
		foreach ($records as $k => $record)
		{
			$languageID = $record['ProductMeta']['language_id'];
			$metas[$languageID] = $record['ProductMeta'];
		}
		
		return $metas;
		
	}
	
		
}



