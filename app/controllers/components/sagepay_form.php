<?php

/**
 * Sagepay Form Callback Component
 * 
 */
class SagepayFormComponent extends Object
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
		if (empty($_REQUEST["crypt"]))
		{
			return false;
		}
		
		$crypt = $_REQUEST["crypt"];	
		
		$mode = Configure::read('SagepayForm.mode');
		$password = Configure::read('SagepayForm.passwords.' . $mode);
		
		$decoded = $this->_simpleXor($this->_base64Decode($crypt), $password);
		$this->_values = $this->_getToken($decoded);
		
		if (empty($this->_values['Status']))
		{
			return false;
		}
		
		return true;
		
	}
	
	/**
	 * Process callback.
	 * 
	 * @return void
	 * @access public 
	 */
	public function processCallback()
	{
		$status = $this->_values['Status'];
		
		$error = '';
		
		if (in_array($status, array('ABORT', 'MALFORMED', 'INVALID', 'ERROR')))
		{
			$error = $this->_values['StatusDetail'];
			$this->orderComplete = false;
			$this->transOK = false;
		}
		else if (in_array($status, array('NOTAUTHED', 'REJECTED')))
		{
			$error = $this->_values['StatusDetail'];
			$this->orderComplete = true;
			$this->transOK = false;
		}
		else if (in_array($status, array('OK', 'AUTHENTICATED')))
		{
			$this->orderComplete = true;
			$this->transOK = true;
		}
		
		$data = array('Order' => array(
			'ref'	=> $this->_values['VendorTxCode'],
			'error'	=> $error,
			'order_status_id' => ($this->transOK) ? Configure::read('OrderStatuses.processing') : Configure::read('OrderStatuses.failed')
		));
		
		// Update order record with result
		$this->_controller->Order->id = $this->_controller->orderID;
		$this->_controller->Order->save($data, array('validate' => false));
		
		// Write sagepay form order
		$this->_writeSagepayFormOrder();
		
	}
	
	/**
	 * Write Sagepay Form order record to db.
	 * 
	 * @return int
	 * @access private
	 */
	private function _writeSagepayFormOrder()
	{
		// Create sagepay form order data
		$sagepayFormOrder = array('SagepayFormOrder' => array(
			'order_id' 			=> $this->_controller->orderID,
			'status' 			=> $this->_values['Status'],
			'status_detail' 	=> $this->_values['StatusDetail'],
			'vendor_tx_code' 	=> $this->_values['VendorTxCode'],
			'vsp_tx_id' 		=> $this->_values['VPSTxId'],
			'amount' 			=> $this->_values['Amount'],
			'avs_cv2' 			=> $this->_values['AVSCV2'],
			'address_result' 	=> $this->_values['AddressResult'],
			'postcode_result' 	=> $this->_values['PostCodeResult'],
			'cv2_result' 		=> $this->_values['CV2Result'],
			'3d_secure_result' 	=> $this->_values['3DSecureStatus'],
			'card_type' 		=> $this->_values['CardType'],
			'last_4_digits' 	=> $this->_values['Last4Digits']
		));
		
		if ($this->_values['Status'] == 'OK')
		{
			$sagepayFormOrder['SagepayFormOrder']['tx_auth_no'] = $this->_values['TxAuthNo'];
		}
		
		if (!empty($this->_values['3DSecureStatus']) && ($this->_values['3DSecureStatus'] == 'OK'))
		{
			$sagepayFormOrder['SagepayFormOrder']['cavv'] = $this->_values['CAVV'];
		}
		
		// Create sagepay form order record
		$this->_controller->Order->SagepayFormOrder->create();
		$sagepayFormResult = $this->_controller->Order->SagepayFormOrder->save($sagepayFormOrder);
		
	}
	
	/**
	 * The getToken function.
	 * 
	 * NOTE: A function of convenience that extracts the value from the "name=value&name2=value2..." reply string
	 * Works even if one of the values is a URL containing the & or = signs.
	 */	
	private function _getToken($thisString)
	{
  		$tokens = array(
		    "Status", "StatusDetail", "VendorTxCode", "VPSTxId", "TxAuthNo", "Amount",
		    "AVSCV2", "AddressResult", "PostCodeResult", "CV2Result", "GiftAid", "3DSecureStatus", "CAVV", "AddressStatus",
			"CardType", "Last4Digits", "PayerStatus", "CardType"
		);

		$output = array();
		$resultArray = array();

		for ($i = count($tokens) - 1; $i >= 0; $i--)
		{
		    $start = strpos($thisString, $tokens[$i]);
			
		    if ($start !== false)
			{
		      	$resultArray[$i]->start = $start;
		      	$resultArray[$i]->token = $tokens[$i];
		    }
		}

		sort($resultArray);
		
	  	for ($i = 0; $i < count($resultArray); $i++)
		{
	    	$valueStart = $resultArray[$i]->start + strlen($resultArray[$i]->token) + 1;
			
	    	if ($i == (count($resultArray) - 1))
			{
	      		$output[$resultArray[$i]->token] = substr($thisString, $valueStart);
			}
			else
			{
	      		$valueLength = $resultArray[$i+1]->start - $resultArray[$i]->start - strlen($resultArray[$i]->token) - 2;
		  		$output[$resultArray[$i]->token] = substr($thisString, $valueStart, $valueLength);
	    	}			
		}

	  	return $output;
	  
	}
	
	/**
	 * The SimpleXor encryption algorithm
	 * 
	 * @param object $InString
	 * @param object $Key
	 * @return 
	 */
	private function _simpleXor($inString, $key)
	{
		$keyList = array();
		$output = "";

		for($i = 0; $i < strlen($key); $i++)
		{
			$keyList[$i] = ord(substr($key, $i, 1));
		}

		for($i = 0; $i < strlen($inString); $i++)
		{
			$output .= chr(ord(substr($inString, $i, 1)) ^ ($keyList[$i % strlen($key)]));
		}

		return $output;
		
	}
	
	/**
	 * Base 64 Encoding function
	 * 
	 */
	private function _base64Encode($plain) 
	{
		return base64_encode($plain);
	}

	/**
	 * Base 64 decoding function
	 * 
	 */
	private function _base64Decode($scrambled) 
	{  
		$scrambled = str_replace(" ", "+", $scrambled);
		return base64_decode($scrambled);
	}
	
	
}


