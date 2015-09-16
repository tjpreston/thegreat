<?php

/**
 * WorldpayFormComponent Callback Component
 * 
 */
class WorldpayFormComponent extends Object
{
	/**
	 * OrderID.
	 * 
	 * @var int
	 * @access public
	 */
	public $orderID;
	
	/**
	 * Order ref.
	 *
	 * @var string
	 * @access public
	 */
	public $orderRef;
	
	/**
	 * Order was completed.
	 *
	 * @var bool
	 * @access public
	 */
	public $orderComplete;
	
	/**
	 * Transaction was successful.
	 *
	 * @var bool
	 * @access public
	 */
	public $transOK;	
		
	/**
	 * Error string
	 *
	 * @var array
	 * @access public
	 */
	public $error;
	
	/**
	 * Checkout controller reference.
	 *
	 * @var object
	 * @access private
	 */
	private $_controller;
	
	/**
	 * Callback values.
	 * 
	 * @var array
	 * @access private
	 */
	private $_values = array();
	
	/**
	 * Component startup routine.
	 * 
	 * @param object $controller
	 * @return void
	 * @access public
	 */
	public function startup(&$controller)
	{
		$this->_controller = $controller;
	}
	
	/**
	 * Check callback request is OK and init callback vars
	 * 
	 * @access public
	 * @return bool 
	 */
	public function initCallback()
	{
		if (empty($_POST['transId']))
		{
			return false;
		}
	
		$this->_values = $_POST;
		
		return true;
		
	}
	
	public function getBasketId()
	{
		return $this->_values['cartId'];
	}
	
	/**
	 * Process callback.
	 * 
	 * @return void
	 * @access public 
	 */
	public function processCallback()
	{	
		$status = $this->_values['transStatus'];
		
		$error = '';
		
		if ($status == 'Y')
		{
			$this->orderComplete = true;
			$this->transOK = true;		
		}
		else
		{
			$error = 'Error';
			$this->orderComplete = false;
			$this->transOK = false;		
		}
		
		$data = array('Order' => array(
			'ref'	=> $this->_values['transId'],
			'error'	=> $error,
			'order_status_id' => ($this->transOK) ? Configure::read('OrderStatuses.processing') : Configure::read('OrderStatuses.failed')
		));
		
		// Update order record with result
		$this->_controller->Order->id = $this->_controller->orderID;
		$this->_controller->Order->save($data, array('validate' => false));
		
		// Write sagepay form order
		$this->_writeWorldpayFormOrder();
		
	}
	
	/**
	 * Write Sagepay Form order record to db.
	 * 
	 * @return int
	 * @access private
	 */
	private function _writeWorldpayFormOrder()
	{
		//mail('andrew@popcornwebdesign.co.uk', 'wp', 'init');
		
		// Create sagepay form order data
		$order = array('WorldpayFormOrder' => array(
			'order_id' 		=> $this->_controller->orderID,
			'cartId' 		=> $this->_values['cartId'],
			'amount' 		=> $this->_values['amount'],
			'authMode' 		=> $this->_values['authMode'],
			'testMode' 		=> $this->_values['testMode'],
			'transId' 		=> $this->_values['transId'],
			'transStatus' 	=> $this->_values['transStatus'],
			'transTime' 	=> $this->_values['transTime'],
			'authAmount' 	=> $this->_values['authAmount'],
			'cardType' 		=> $this->_values['cardType'],
			'countryMatch' 	=> $this->_values['countryMatch'],
			'AVS' 			=> $this->_values['AVS'],
			'ipAddress' 	=> $this->_values['ipAddress']
		));
		
		//mail('andrew@popcornwebdesign.co.uk', 'wp', var_export($order, true));
		
		// Create sagepay form order record
		$this->_controller->Order->WorldpayFormOrder->create();
		$worldpayFormResult = $this->_controller->Order->WorldpayFormOrder->save($order);
		
		//mail('andrew@popcornwebdesign.co.uk', 'wp', var_export($this->_controller->Order->WorldpayFormOrder->validationErrors, true));
		
	}
	
	
}


