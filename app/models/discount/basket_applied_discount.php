<?php

/**
 * Basket Discount <-> Basket Bridge Model
 *
 */
class BasketAppliedDiscount extends AppModel
{
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array('Basket', 'BasketDiscount');

	/**
	 * Get discounts applied to basket.
	 * 
	 * @param int $basketID
	 * @return array
	 * @access public
	 */
	public function getDiscounts($basketID)
	{
		$this->BasketDiscount->bindPrice(0);

		$records = $this->BasketDiscount->find('all', array(
			'joins' => array(
				array(
					'table' => 'basket_applied_discounts',
		            'alias' => 'BasketAppliedDiscount',
		            'type' => 'INNER',
		            'conditions'=> array('BasketAppliedDiscount.basket_discount_id = BasketDiscount.id')
				)
			),
			'conditions' => array('BasketAppliedDiscount.basket_id' => $basketID),
			'limit' => 1
		));
		
		return $records;
		
	}
	
	/**
	 * Basket Discount <-> Basket Bridge Model.
	 *
	 * @param int $basketID
	 * @param array $discounts
	 * @return void
	 * @access public
	 */
	public function saveDiscounts($basketID, $discounts)
	{
		foreach ($discounts as $discount)
		{
			$discountID = $discount['BasketDiscount']['id'];
			
			$this->create();
			$this->save(array('BasketAppliedDiscount' => array(
				'basket_id' => $basketID,
				'basket_discount_id' => $discountID
			)));
			
			if (!empty($discount['BasketDiscount']['stop']))
			{
				break;
			}

		}
	}
	
	
}

