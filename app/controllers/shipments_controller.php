<?php

/**
 * Shipments Controller
 * 
 */
class ShipmentsController extends AppController
{
	/**
	 * Array containing the names of components this controller uses.
	 *
	 * @var array
	 * @access public
	 */
	public $components = array('Email');
	
	/**
	 * Admin
	 * Save shipment.
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_save()
	{
		if (empty($this->data['Shipment']['order_id']))
		{
			$this->cakeError('error404');
		}
		
		$orderID = $this->data['Shipment']['order_id'];
		$order = $this->Shipment->Order->findById($orderID);
		$data = $this->_getShipmentData($order);
		
		$items = array();
		
		if (!empty($this->data['Shipment']['ShipmentItem']))
		{
			foreach ($this->data['Shipment']['ShipmentItem'] as $k => $item)
			{
				if (!empty($item['ship']) && !empty($item['qty_shipped']))
				{
					$items[] = $item;
				}
			}
		}
		
		if (empty($items))
		{
			$this->Session->setFlash('No items selected for the shipment', 'default', array('class' => 'failure'));
			$this->redirect('/admin/orders/view/' . $orderID);
		}
		
		if (!$this->Shipment->save($data))
		{
			$this->Session->setFlash('Shipment could not be saved2', 'default', array('class' => 'failure'));
			
			$this->redirect('/admin/orders/view/' . $orderID);
		}
		
		$shipmentID = $this->Shipment->getInsertID();
		
		foreach ($items as $k => $item)
		{
			$items[$k]['shipment_id'] = $shipmentID;
		}
		
		$itemsData = array('ShipmentItem' => $items);
		
		if (!$this->Shipment->ShipmentItem->saveAll($itemsData['ShipmentItem']))
		{
			$this->Shipment->delete($shipmentID);
			
			$this->Session->setFlash('Shipment could not be saved', 'default', array('class' => 'failure'));
			$this->redirect('/admin/orders/view/' . $orderID);
		}

		$orderShipped = $this->Shipment->Order->hasShipped($orderID);
		$this->Shipment->Order->updateShippedStatus($orderID, $orderShipped);		
		
		$shipment = $this->Shipment->findById($shipmentID);
		
		if (!empty($this->data['Shipment']['send_notification']))
		{
			$this->_sendNotification($order, $shipment, $items);
		}
		
		$this->Session->setFlash('Shipment saved', 'default', array('class' => 'success'));
		$this->redirect('/admin/orders/view/' . $orderID);
		
	}
	
	/**
	 * Admin.
	 * Show print-ready HTML shipment note.
	 *
	 * @param int $id
	 * @return void
	 * @access public
	 */
	public function admin_note($id)
	{
		$this->layout = 'admin_print';
		$record = $this->Shipment->getShipment($id);
		$this->set('record', $record);
	}
	
	private function _getShipmentData($order)
	{
		$data = array('Shipment' => array(
			'order_id' 						=> $order['Order']['id'],
			'shipping_carrier_service_name' => $order['Order']['shipping_carrier_service_name'],
			'total_weight' 					=> 0,
			'tracking_ref' 					=> $this->data['Shipment']['tracking_ref'],
			'shipping_first_name' 			=> $order['Order']['shipping_first_name'],
			'shipping_last_name' 			=> $order['Order']['shipping_last_name'],
			'shipping_address_1' 			=> $order['Order']['shipping_address_1'],
			'shipping_address_2' 			=> $order['Order']['shipping_address_2'],
			'shipping_town' 				=> $order['Order']['shipping_town'],
			'shipping_country_id' 			=> $order['Order']['shipping_country_id'],
			'shipping_county' 				=> $order['Order']['shipping_county'],
			'shipping_postcode' 			=> $order['Order']['shipping_postcode']
		));
		
		return $data;
		
	}
	
	private function _sendNotification($order, $shipment, $items)
	{
		$this->initDefaultEmailSettings();
		$this->Email->to = $order['Customer']['first_name'] . '<' . $order['Customer']['email'] . '>';
		
		if ($shipment['Order']['shipped'])
		{
			$this->Email->subject = Configure::read('Site.name') . ' - Order Complete';
			$this->Email->template = 'customers/order_notifications/order_shipped_complete';
		}
		else
		{
			$this->Email->subject = Configure::read('Site.name') . ' - Order Shipment Notification';
			$this->Email->template = 'customers/order_notifications/order_shipped';
		}
		
		if (!empty($this->data['Shipment']['comments']) && !empty($this->data['Shipment']['append_comments']))
		{
			$this->set('comments', $this->data['Shipment']['comments']);
		}
		
		if (!empty($this->data['Shipment']['tracking_ref']))
		{
			$this->set('tracking_ref', $this->data['Shipment']['tracking_ref']);
		}



		
		$this->set('shipmentItems', $items);
		$this->set('orderItems', $this->Shipment->Order->getItemsIndexedById($order['Order']['id']));
		$this->set('recipient', $order['Customer']['first_name']);
		$this->set('order', $order);
		$this->set('shipment', $shipment);
		
		//debug($this->Shipment->Order->getItemsIndexedById($order['Order']['id']));
		$this->Email->send();
		
	}
	
		
}

