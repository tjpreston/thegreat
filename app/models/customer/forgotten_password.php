<?php

/**
 * Forgotten Password
 * 
 */
class ForgottenPassword extends AppModel
{
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array('Customer');
	
	/**
	 * Delete forgotten password records by Member ID.
	 * 
	 * @param int $customerID
	 * @return void
	 * @access public
	 */
	public function deleteAllByMember($customerID)
	{
		$this->deleteAll(array(
			'ForgottenPassword.customer_id' => $customerID
		));	
	}
		
	/**
	 * Create a forgotten password and return hash.
	 * 
	 * @param int $memberID
	 * @return string $hash
	 * @access public
	 */
	public function createRecord($customerID)
	{
		$hash = md5(uniqid(mt_rand(), true));
			
		$this->create();
		$this->save(array('ForgottenPassword' => array(
			'customer_id' => $customerID,
			'hash' => $hash
		)));
		
		return $hash;
		
	}	
	
}



