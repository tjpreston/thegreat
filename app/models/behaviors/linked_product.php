<?php

/**
 * Linked Product Behavior
 * 
 */
class LinkedProductBehavior extends ModelBehavior
{	
	/**
	 * Model Instance.
	 * 
	 * @var object
	 * @access public
	 */
	public $model = null;

	/**
	 * Setup behaviour.
	 * 
	 * @param object $Model
	 * @param array $settings
	 * @return void
	 * @access public
	 */
	public function setup(&$Model, $settings)
	{
		$this->model = $Model;
		$this->model->order = array($this->model->alias . '.sort ASC');
	}
	
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
		if (empty($this->data[$this->model->alias]['to_product_id']) || empty($this->data[$this->model->alias]['from_product_id']))
		{
			unset($this->data[$this->model->alias]);
		}
		
		return true;
	
	}
	
	/**
	 * Called during save operations, before validation. Please note that custom
	 * validation rules can be defined in $validate.
	 *
	 * @return boolean True if validate operation should continue, false to abort
	 * @param $options array Options passed from model::save(), see $options of model::save().
	 * @access public
	 */
	public function addValidation()
	{
		$this->model->validate = array(
			'from_product_id' => array(
				'rule' => array('greaterThan', 'from_product_id', 0),
				'required' => true,
				'allowEmpty' => false,
				'message' => 'From Product ID missing'
			),
			'to_product_id' => array(
				'present' => array(
					'rule' => array('greaterThan', 'to_product_id', 0),
					'required' => true,
					'allowEmpty' => false,
					'message' => 'To Product ID missing'
				),
				'unique' => array(
					'rule' => array('isUniqueRecord'),
					'message' => 'Product already cross selled'
				)
			)
		);
	}
	
	/**
	 * Validation method for checking record doesn't already exist.
	 * 
	 * @return bool
	 * @access public 
	 */
	public function isUniqueRecord()
	{
		$conditions = array(
			$this->model->alias . '.from_product_id' => $this->model->data[$this->model->alias]['from_product_id'],
			$this->model->alias . '.to_product_id' => $this->model->data[$this->model->alias]['to_product_id']
		);
		
		if (!empty($this->model->data[$this->model->alias]['id']))
		{
			$conditions[$this->model->alias . '.id !='] = $this->model->data[$this->model->alias]['id'];
		}
		
		$record = $this->model->find('first', array('conditions' => $conditions));
		
		return empty($record);
		
	}
	
	/**
	 * Get products.
	 * 
	 * @TODO currency
	 * @TODO language
	 * @return array
	 * @access public
	 */
	public function getProducts(&$Model, $productID, $extra = array())
	{
		$Model->Product->unbindModel(array(
			'hasMany' => array('ProductName', 'ProductDescription', 'ProductMeta', 'ProductPrice', 'RelatedProduct'),
			'hasAndBelongsToMany' => array('Document', 'ProductFlag')
		), false);
		
		$conditions = array($Model->alias . '.from_product_id' => $productID);
		
		if (!empty($extra['conditions']))
		{
			$conditions = array_merge($conditions, $extra['conditions']);
		}
		
		$limit = (!empty($extra['limit'])) ? $extra['limit'] : null;
		
		$Model->Product->bindName($Model->Product, 1, true);
		$Model->Product->bindDescription($Model->Product, 1, true);
		$Model->Product->bindPrice(1, true);
		$Model->Product->bindMeta($Model->Product, 1, true);
		$Model->Product->bindOptionStock(true);
		$Model->Product->bindSingleQtyDiscount(true);
		
		$join = array(
			'table' => $Model->table,
			'alias' => $Model->name,
			'type'  => 'INNER',
			'conditions' => array($Model->alias . '.to_product_id = Product.id')
		);
		
		$records = $Model->Product->find('all', array(
			'fields' => array(
				$Model->alias . '.*', 
				'Product.*', 'ProductName.*', 'ProductMeta.*', 'ProductPrice.*', 
				'Manufacturer.*',
				'SingleQtyProductPriceDiscount.discount_amount'
			),
			'joins' => array($join),
			'conditions' => $conditions,
			'order' => array($Model->alias . '.sort ASC', 'ProductName.name ASC'),
			'limit' => $limit
		));
		
		if (!empty($records))
		{
			$records = ClassRegistry::init('ProductOptionStock')->addVarsToProducts($records, 'singleqty');
		}
		
		return $records;		
		
	}
	
	/**
	 * 
	 * 
	 * @param object $id
	 * @return 
	 */
	public function getProductList(&$Model, $id)
	{
		return $Model->find('list', array(
			'fields' => array($Model->alias . '.id', $Model->alias . '.to_product_id'),
			'conditions' => array($Model->alias . '.from_product_id' => $id)
		));
	}
	
	
	
}
