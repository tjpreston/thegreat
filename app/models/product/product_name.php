<?php

/**
 * Product Name
 * 
 */
class ProductName extends AppModel
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
		'name' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Product name missing'
		)
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
		return $results;

		$search = array(chr(174), chr(176));
		$replace = array('&reg;', '&deg;');

		if (!empty($results))
		{
			foreach ($results as $k => $result)
			{
				$name = str_replace($search, $replace, $result['ProductName']['name']);
				$results[$k]['ProductName']['name'] = $name;
			}
		}
		
		return $results;

	}

}
