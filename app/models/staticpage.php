<?php

/**
 * Static Page Model
 * 
 */
class Staticpage extends AppModel
{
    /**
	 * Detailed list of hasMany associations.
	 *
	 * @var array
	 * @access public
	 */
	public $hasOne = array(
		'StaticpagesImage'
	);
    
}