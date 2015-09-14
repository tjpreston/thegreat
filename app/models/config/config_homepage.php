<?php

/**
 * Homepage Config
 * 
 */
class ConfigHomepage extends AppModel
{
	/**
	 * List of validation rules. 
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(
		'id' => array(
			'rule' => array('greaterThan', 'id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Config ID missing'
		),
		'config_id' => array(
			'rule' => array('greaterThan', 'config_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Config ID missing'
		),
		'language_id' => array(
			'rule' => array('greaterThan', 'language_id', 0),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Language ID missing'
		)
	);
	
	/**
	 * Get homepage config
	 * 
	 * @TODO abstract this
	 * @return array $config
	 * @access public
	 */
	public function getConfig()
	{
		$records = $this->find('all', array(
			'conditions' => array('ConfigHomepage.config_id' => Configure::read('Site.use_config')),
			'recursive' => -1
		));
		
		$config = array();
		
		foreach ($records as $k => $record)
		{
			$languageID = $record['ConfigHomepage']['language_id'];
			$config[$languageID] = $record['ConfigHomepage'];
		}
		
		return $config;
		
	}
	
	
	
}
