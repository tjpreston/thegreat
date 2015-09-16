<?php



/**

 * Basket Discount Price Modifier Value Model

 * 

 */

class BasketDiscountPrice extends AppModel

{

	/**

	 * Detailed list of belongsTo associations.

	 *

	 * @var array

	 * @access public

	 */

	public $belongsTo = array('Currency', 'BasketDiscount');



	/**

	 * List of validation rules. 

	 *

	 * @var array

	 * @access public

	 */

	public $validate = array(

		'modifier_value' => array(

			'rule' => 'numeric',

			'required' => true,

			'allowEmpty' => false,

			'message' => 'Please enter a discount amount'

		)

	);

		

	

}

