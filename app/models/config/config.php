<?php

/**
 * Config Model
 * 
 */
class Config extends AppModel
{		
	/**
	 * Get runtime config.
	 *
	 * @return array
	 * @access public
	 */
	public function getConfig()
	{
		$config = $this->find('first', array(
			'conditions' => array('Config.id' => Configure::read('Site.use_config'))
		));
		
		return $config;
		
	}
	
	/**
	 * Bind homepage config.
	 *
	 * @param object $model
	 * @param mixed $languageID
	 * @param bool $reset
	 * @return void
	 * @access public
	 */
	public function bindHomepage($model, $languageID = null, $reset = false)
	{
		$this->bind($model, 'ConfigHomepage', $languageID, $reset);
	}
	
}
