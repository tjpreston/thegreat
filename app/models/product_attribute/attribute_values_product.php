<?php

/**
 * Attribute Value to Product Join Model
 * 
 */
class AttributeValuesProduct extends AppModel
{
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
		'attribute_value_id' => array(
			'rule' => array('greaterThan', 'attribute_value_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Attribute Value ID missing'
		)
	);
	
	/**
	 * Save attribute values for product.
	 * 
	 * @param int $productID
	 * @param array $values
	 * @return bool
	 * @access public
	 */
	public function saveValues($productID, $values) 
	{
		$this->deleteAll(array('AttributeValuesProduct.product_id' => $productID));
		
		foreach ($values as $v)
		{
			if (empty($v['AttributeValue']) || !is_array($v['AttributeValue']))
			{
				continue;
			}
			
			foreach ($v['AttributeValue'] as $id)
			{
				$this->create();
				$this->save(array('AttributeValuesProduct' => array(
					'product_id' => $productID,
					'attribute_value_id' => $id
				)));
			}
			
		}
		
		return true;
		
	}

	
	
	
	
}
