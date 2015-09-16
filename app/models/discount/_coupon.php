<?php

/**
 * Coupon Model
 *
 */
class Coupon extends AppModel
{
	/**
	 * List of validation rules. 
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(
		'code' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter a valid coupon code'
		)
	);
	
	/**
	 * Called before each find operation. Return false if you want to halt the find
	 * call, otherwise return the (modified) query data.
	 *
	 * @param array $queryData Data used to execute this query, i.e. conditions, order, etc.
	 * @return mixed true if the operation should continue, false if it should abort; or, modified $queryData to continue with new $queryData
	 * @access public
	 */
	public function beforeFind($queryData) 
	{
		if (Configure::read('Runtime.mode') == 'front')
		{
			$queryData['conditions']['Coupon.active'] = 1;
		}
		
		return $queryData;
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
		if (!empty($this->data['BasketDiscount']['infinite_uses']))
		{
			$this->data['BasketDiscount']['use_limit'] = null;
		}
		
		foreach (array('from', 'to') as $dir)
		{
			if (!empty($this->data['Coupon']['active_from']))
			{
				$temp = explode('/', $this->data['Coupon']['active_' . $dir]);
				$this->data['Coupon']['active_' . $dir] = $temp[2] . '-' . $temp[1] . '-' . $temp[0];
			}
		}
		
		$today = date('Y-m-d');
		
		$from = $this->data['Coupon']['active_from'];		
		if (!empty($from) && ($from > $today))
		{
			$this->data['Coupon']['active'] = 0;
		}
		
		$to = $this->data['Coupon']['active_to'];
		if (!empty($to) && ($to < $today))
		{
			$this->data['Coupon']['active'] = 0;
		}
		
		return true;
		
	}
	
	/**
	 * Make list of passed coupons.
	 * 
	 * @param array $records
	 * @return array
	 * @access public
	 */
	public function getList()
	{
		$list = $this->find('list', array(
			'fields' => array('Coupon.id', 'Coupon.code')
		));
		
		return $list;
		
	}
	
}





 