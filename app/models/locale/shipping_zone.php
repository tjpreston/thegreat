<?php

/**
 * Delivery Country
 * 
 */
class ShippingZone extends AppModel
{
	/**
	 * Detailed list of hasAndBelongsToMany associations.
	 *
	 * @var array
	 * @access public
	 */
	public $hasAndBelongsToMany = array('Country' => array(
		'joinTable' => 'shipping_zone_countries'
	));
	
	/**
	 * The column name(s) and direction(s) to order find results by default.
	 *
	 * @var string
	 * @access public
	 */
	public $order = 'ShippingZone.sort ASC, ShippingZone.name ASC';

	/**
	 * Get list of all delivery countries / zones.
	 *
	 * @return array
	 * @access public
	 */
	public function getList()
	{		
		return $this->find('list');
	}
		
}



