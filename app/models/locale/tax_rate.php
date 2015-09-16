<?php

/**
 * Tax Rate Model
 * 
 */
class TaxRate extends AppModel
{	
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array('Country');
	
	/**
	 * The column name(s) and direction(s) to order find results by default.
	 *
	 * @var string
	 * @access public
	 */
	public $order = array(
		'Country.name ASC', 'TaxRate.name ASC'
	);
	
	/**
	 * List of validation rules. 
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(
		'country_id' => array(
			'rule' => array('greaterThan', 'country_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please select a country'
		),
		'name' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter an identifier'
		)
	);

	/**
	 * Get tax rate based on country ID.
	 * 
	 * @param int $countryID
	 * @return float
	 * @access public
	 */
	public function getTaxRate($countryID)
	{
		$record = $this->find('first', array('conditions' => array(
			'TaxRate.country_id' => $countryID
		)));
		
		if (empty($record))
		{
			return 0.00;
		}
		
		return $record['TaxRate']['rate'];
		
	}
	
	/**
	 * Get tax based on tax inclusive amount.
	 * 
	 * @param float $amount
	 * @param float $rate
	 * @return float
	 * @access static
	 */
	public static function getTaxFromInclusiveAmount($amount, $rate)
	{
		$exVat = round(((100 / (100 + $rate)) * $amount), 2);
		return ($amount - $exVat);
	}
	
	/**
	 * Get tax based on tax exclusive amount.
	 * 
	 * @param float $amount
	 * @param float $rate
	 * @return float
	 * @access static
	 */	
	public static function getTaxFromExclusiveAmount($amount, $rate)
	{		
		return (($rate / 100) * $amount);
	}	
	
}

