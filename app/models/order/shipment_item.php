<?php

/**
 * Shipment Item
 * 
 * @TODO shipment item validation
 * 
 */
class ShipmentItem extends AppModel
{	
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array(
		'Shipment', 'OrderItem'
	);
	
	/**
	 * List of validation rules. 
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(
		'shipment_id' => array(
			'rule' => array('greaterThan', 'shipment_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Shipment ID missing'
		),
		'order_item_id' => array(
			'rule' => array('greaterThan', 'order_item_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Order Item ID missing'
		),
		'qty_shipped' => array(
			'rule' => array('greaterThan', 'qty_shipped', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Qty to ship is empty'
		)
	);
	
	/**
	 * Called after each successful save operation.
	 *
	 * @param boolean $created True if this save created a new record
	 * @access public
	 */
	public function afterSave($created) 
	{
		/*
		if (!empty($this->data['ShipmentItem']['order_item_id']))
		{
			$orderID = $this->data['ShipmentItem']['order_item_id'];
			
			$records = $this->find('list', array(
				'fields' => array('ShipmentItem.id', 'ShipmentItem.qty_shipped'),
				'conditions' => array('ShipmentItem.order_item_id' => $orderID)
			));
			
			$this->Shipment->Order->OrderItem->id = $orderID;
			$this->Shipment->Order->OrderItem->saveField('shipped', array_sum($records), false);
			
			$this->Shipment->Order->updateShippedField();
			
		}
		*/
	}
	
	/**
	 * Get qty of shipped order item.
	 *
	 * @param int $orderItemID
	 * @return int
	 * @access public
	 */
	public function getShippedQty($orderItemID)
	{
		$records = $this->find('list', array(
			'fields' => array('ShipmentItem.id', 'ShipmentItem.qty_shipped'),
			'conditions' => array('ShipmentItem.order_item_id' => $orderItemID),
			'recursive' => -1
		));
		
		return array_sum($records);
		
	}
	
	

}


