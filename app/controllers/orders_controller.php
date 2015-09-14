<?php

/**
 * Orders Controller
 * 
 */
class OrdersController extends AppController
{
	/**
	 * Array containing the names of components this controller uses.
	 *
	 * @var array
	 * @access public
	 */
	public $components = array('Email');
	
	/**
	 * An array containing the names of helpers this controller uses. 
	 *
	 * @var mixed A single name as a string or a list of names as an array.
	 * @access public
	 */
	public $helpers = array('Time');
	
	/**
	 * Holds pagination defaults for controller actions.
	 *
	 * @var array
	 * @access public
	 */
	public $paginate = array('limit' => 20);
	
	/**
	 * Show my account order history page. 
	 * 
	 * @return void
	 * @access public
	 */
	public function index()
	{
		$this->addCrumb('/customers', 'My Account');
		$this->addCrumb('/orders', 'Order History');
		
		$records = $this->Order->find('all', array('conditions' => array(
			'Customer.id' => $this->Auth->user('id')
		)));
		
		$this->set('records', $records);
		
	}
	
	/**
	 * View order.
	 * 
	 * @return void
	 * @access public
	 */
	public function view($id, $itemMode = 'items')
	{
		$this->addCrumb('/customers', 'My Account');
		$this->addCrumb('/orders', 'Order History');
		$this->addCrumb('/orders/views/' . $id, 'View Order');
		
		$this->Order->unbindModel(array('hasMany' => array('Shipment')));
		$record = $this->Order->find('first', array(
			'conditions' => array(
				'Order.customer_id' => $this->Auth->user('id'),
				'Order.id' => $id
			)
		));
		
		if (empty($record))
		{
			$this->cakeError('error404');
		}
		
		if ($itemMode == 'shipments')
		{
			$this->Order->Shipment->unbindModel(array('belongsTo' => array('Order')));
			$this->Order->Shipment->Behaviors->attach('Containable');
			
			$shipments = $this->Order->Shipment->find('all', array(
				'contain' => array('ShipmentItem' => 'OrderItem'),
				'conditions' => array('Shipment.order_id' => $id)
			));
			
			$this->set('shipments', $shipments);
			
		}

		

		$this->set(compact('record', 'itemMode'));
	}
	
	/**
	 * Admin
	 * List orders. 
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_index()
	{
		$this->paginate['conditions'] = $this->_admin_filter();
		$this->set('records', $this->paginate());		
		$this->set('orderStatuses', $this->Order->OrderStatus->find('list'));

		$this->Session->write('Order.last_index_url', env('REQUEST_URI'));
	}
	
	/**
	 * Admin.
	 * View an order record.
	 * 
	 * @param int $id
	 * @return void
	 */
	public function admin_view($id)
	{	
		$this->Order->unbindModel(array('hasMany' => array('OrderItem')));
		$this->Category->Product->bindName($this->Category->Product, 1, false);
		
		$record = $this->Order->getOrder($id);
		$items = $this->Order->getItems($id);

		$this->data = $record;
		
		if ($this->Session->check('Admin.' . $this->params['controller'] . '.last_tab'))
		{
			$this->set('initTab', $this->Session->read('Admin.' . $this->params['controller'] . '.last_tab'));
			$this->Session->delete('Admin.' . $this->params['controller'] . '.last_tab');
		}
		
		$ShippingCarrierServiceModel = ClassRegistry::init('ShippingCarrierService');
		
		$services = $ShippingCarrierServiceModel->getAvailableServices(
			$record['Order']['shipping_country_id'],
			$record['Order']['subtotal']
		);
		
		$carrierShippingServices = $ShippingCarrierServiceModel->getList($services);
		$orderStatuses = $this->Order->OrderStatus->find('list');
		$shipments = $this->Order->Shipment->getShipments($record['Order']['id']);
		$customerAddressList = $this->Order->Customer->CustomerAddress->getList($this->data['Order']['customer_id']);
		$logItems = $this->Order->OrderNote->findAllByOrderId($id);
		$hasShipped = $this->Order->hasShipped($id);
		

		$this->set(compact(
			'record', 'items', 'carrierShippingServices', 'shipments', 
			'customerAddressList', 'logItems', 'hasShipped', 'orderStatuses'
		));
		
	}
	
	/**
	 * Admin.
	 * Save an order record.
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_save()
	{
		$id = $this->data['Order']['id'];
		
		/*
		if (isset($this->params['form']['last_pane']))
		{
			$this->Session->write('Admin.' . $this->params['controller'] . '.last_tab', $this->params['form']['last_pane']);
		}
		*/
		
		$saveOK = !$this->Order->save($this->data, false, array('order_status_id'));
		
		if ($saveOK)
		{
			$this->Session->setFlash('There were errors. Please check the form below.', 'default', array('class' => 'failure'));	
			return $this->setAction('admin_view', $id);
		}
		
		$this->Session->setFlash('Order saved.', 'default', array('class' => 'success'));
		$this->redirect('/admin/orders/view/' . $id);	
		
	}
	
	/**
	 * Admin.
	 * Show print-ready HTML shipment note.
	 *
	 * @param int $id
	 * @return void
	 * @access public
	 */
	public function admin_shipment_note($id)
	{
		$this->layout = 'admin_print';
		
		$record = $this->Order->getOrder($id);
		$items = $this->Order->getItems($id);
		
		$this->set(compact('record', 'items'));
		
	}
	
	/**
	 * Admin.
	 * Parse params for list filter and save to/delete from session.
	 * 
	 * @return array $conditions
	 * @access public
	 */
	private function _admin_filter()
	{
		$conditions = array();
		
		if (isset($this->params['url']['order_status_id']))
		{			
			if (!empty($this->params['url']['order_status_id']))
			{
				$this->Session->write('Order.order_status_id', $this->params['url']['order_status_id']);
				$conditions['Order.order_status_id'] = $this->params['url']['order_status_id'];
			}
			else
			{
				$this->Session->delete('Order.order_status_id');
			}
		}
		
		if (isset($this->params['url']['ref']))
		{	
			if (!empty($this->params['url']['ref']))
			{
				$this->Session->write('Order.ref', $this->params['url']['ref']);
				$conditions['Order.ref'] = $this->params['url']['ref'];
			}
			else
			{
				$this->Session->delete('Order.ref');
			}
		}
		
		if (isset($this->params['url']['customer_name']))
		{
			if (!empty($this->params['url']['customer_name']))
			{
				$this->Session->write('Order.customer_name', $this->params['url']['customer_name']);
				$conditions[] = array('OR' => array(
					'Order.customer_first_name LIKE' => '%' . $this->params['url']['customer_name'] . '%',
					'Order.customer_last_name LIKE'  => '%' . $this->params['url']['customer_name'] . '%',
					'Customer.first_name LIKE' => '%' . $this->params['url']['customer_name'] . '%',
					'Customer.last_name LIKE'  => '%' . $this->params['url']['customer_name'] . '%'
				));
			}
			else
			{
				$this->Session->delete('Order.customer_name');
			}
		}
		
		if (isset($this->params['url']['year']))
		{
			if (!empty($this->params['url']['year']))
			{
				$this->Session->write('Order.year', $this->params['url']['year']);
				
				$year  = $this->params['url']['year'];
				$month = (!empty($this->params['url']['month'])) ? $this->params['url']['month'] : date('m');
				
				$conditions[] = array('AND' => array(
					'Order.created >=' => $year . '-' . $month . '-01',
					'Order.created <=' => $year . '-' . $month . '-31'
				));
			}
			else
			{
				$this->Session->delete('Order.year');
			}			
		}
		
		if (isset($this->params['url']['month']))
		{
			if (!empty($this->params['url']['month']))
			{
				$this->Session->write('Order.month', $this->params['url']['month']);
				
				$month = $this->params['url']['month'];
				$year  = (!empty($this->params['url']['year'])) ? $this->params['url']['year'] : date('Y');	
				
				$conditions[] = array('AND' => array(
					'Order.created >=' => $year . '-' . $month . '-01',
					'Order.created <=' => $year . '-' . $month . '-31'
				));
			}
			else
			{
				$this->Session->delete('Order.month');
			}			
		}
		
		return $conditions;
		
	}
	
	
}

