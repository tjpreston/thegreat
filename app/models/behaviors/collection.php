<?php

/**
 * Collection Behavior
 * 
 */
class CollectionBehavior extends ModelBehavior
{
	/**
	 * Model Instance.
	 * 
	 * @var object
	 * @access public
	 */
	public $model = null;
	
	/**
	 * Setup behaviour.
	 * 
	 * @param object $Model
	 * @param array $settings
	 * @return void
	 * @access public
	 */
	public function setup(&$Model, $settings)
	{
		$this->model = $Model;		
	}
		
}


