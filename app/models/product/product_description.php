<?php

/**
 * Product Description Model
 * 
 */
class ProductDescription extends AppModel
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
		/*
		'short_description' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter a short description'
		),
		'long_description' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter a long description'
		)
		*/
	);
	
	/**
	 * Called after each find operation. Can be used to modify any results returned by find().
	 * Return value should be the (modified) results.
	 *
	 * @param mixed $results The results of the find operation
	 * @param boolean $primary Whether this model is being queried directly (vs. being queried as an association)
	 * @return mixed Result of the find operation
	 * @access public
	 */
	public function afterFind($results, $primary = false)
	{
		foreach ($results as $k => $record)
		{
			if (!empty($record['ProductDescription']['specification']) && !empty($record['ProductDescription']['spec_as_key_value']))
			{
				$spec = trim($record['ProductDescription']['specification']);
				$lines = explode("\n", $spec);
				$out = array();
				
				foreach ($lines as $l => $line)
				{
					$temp = explode(':', $line);
					if (!empty($temp[0]) && !empty($temp[1]))
					{
						$out[$l] = array(trim($temp[0]), trim($temp[1]));
					} else {
						$out[$l] = array('', trim($temp[0]));
					}
				}
							
				// If all lines of key-value, otherwise display as is.
				if (count($lines) === count($out))
				{
					$results[$k]['ProductDescription']['specification_array'] = $out;
				}	
				
			}
		}
		
		return $results;
		
	}
	
	/**
	 * Called before each save operation, after validation. Return a non-true result
	 * to halt the save.
	 *
	 * @return boolean True if the operation should continue, false if it should abort
	 * @access public
	 */
	public function beforeSave($options = array()) 
	{
		/*
		if (!empty($this->data['ProductDescription']['specification']))
		{
			$spec = trim($this->data['ProductDescription']['specification']);
			$lines = explode("\n", $spec);
			
			foreach ($lines as $k => $line)
			{
				$temp = explode(':', $line);
				if (!empty($temp[0]))
				{
					$lines[$k] = array(
						trim($temp[0]),
						trim($temp[1])
					);
				}
			}
			
			$this->data['ProductDescription']['specification'] = json_encode($lines);
			
		}
		*/
		
		return true;
		
	}
	
	/**
	 * Get product descriptions
	 * 
	 * @param int $productID
	 * @return array $descriptions
	 * @access public
	 */
	public function getDescriptions($productID)
	{
		$records = $this->find('all', array(
			'conditions' => array('ProductDescription.product_id' => $productID),
			'recursive' => -1
		));
		
		$descriptions = array();
		
		foreach ($records as $k => $record)
		{
			$languageID = $record['ProductDescription']['language_id'];
			$descriptions[$languageID] = $record['ProductDescription'];
		}
		
		return $descriptions;
		
	}
	
}



