<?php

/**
 * Order Note
 * 
 */
class OrderNote extends AppModel
{
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array('Order');
	
	/**
	 * The column name(s) and direction(s) to order find results by default.
	 *
	 * @var string
	 * @access public
	 */
	public $order = 'OrderNote.created DESC';
	
	/**
	 * List of validation rules. 
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(
		'order_id' => array(
			'rule' => array('greaterThan', 'order_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Order ID missing'
		),
		'name' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Note name missing'
		)
	);
	
	public function addContentValidation()
	{
		$this->validate['content'] = array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Note content missing'
		);
	}
	
}

