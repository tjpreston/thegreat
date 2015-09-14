<?php

/**
 * Attribute Sets Attribute Join Model
 * 
 */
class AttributeSetsAttribute extends AppModel
{
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array('AttributeSet', 'Attribute');
	
}
