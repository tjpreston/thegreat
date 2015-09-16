<?php

/**
 * Newsletter Model
 * 
 */
class Newsletter extends AppModel
{
	
	/**
	 * List of validation rules. 
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(
		'email' => array(
			'valid' => array(
				'rule' => 'email', 
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Please enter a valid email address'
			),
			//'isUnique' => array(
			//	'rule' => 'isUnique',
			//	'required' => true,
			//	'allowEmpty' => false,
			//	'message' => 'It looks like you\'ve already signed up'
			//)
		)
	);

	public $useTable = false;

	/**
	 * Don't use a database table
	 */
	//public $useTable = false;

	function subscribe($data){
    	$this->set($data);
    	if($this->validates()){
    		App::import('Vendor', 'MCAPI', array('file' => 'MCAPI.class.php'));
			$api_key = Configure::read('MailChimp.api_key');
			$list_id = Configure::read('MailChimp.list_id');
			$api = new MCAPI($api_key);

			$email = $data[$this->name]['email'];

			//$merge_vars = array('FNAME' => $first_name, 'LNAME' => $last_name);

			if($api->listSubscribe($list_id, $email, array())){
				return true;
			} else {
				$this->log('MailChimp error: ' . $api->errorMessage, 'error');
				return $api->errorMessage;
			}
    	}
    	return false;
    }


    public function subscribeCampaignMonitor($email){
    	$data = array($this->name => array(
    		'email' => $email,
    	));

    	$this->set($data);
    	if($this->validates()){
    		App::import('Vendor', 'CsrestSubscribers', array('file' => 'campaignmonitor/csrest_subscribers.php'));

    		$apiKey = Configure::read('Newsletter.CampaignMonitor.api_key');
			$list = Configure::read('Newsletter.CampaignMonitor.list_id');

			// Subscribe email address to mailing list
			$wrap = new CS_REST_Subscribers($list, $apiKey);

			$result = $wrap->add(array(
				'EmailAddress' => $email,
				'Resubscribe' => true
			));

			return $result->was_successful();
    	}

    	return false;
    }

    
	
}
