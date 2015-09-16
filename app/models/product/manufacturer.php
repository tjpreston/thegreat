<?php

/**
 * Manufacturer Model
 * 
 */
class Manufacturer extends AppModel
{
	/**
	 * Model ordering
	 *
	 * @var array
	 * @access public
	 */
	public $order = array('Manufacturer.name ASC');

	/**
	 * Validation rules
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(
		'name' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Name cannot be empty'
		),
		'url' => array(
			'rule' => 'manufacturerUrlIsUnique',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'URL missing / un-unique / invalid'
		)
	);
	
	/**
	 * Called during save operations, before validation. Please note that custom
	 * validation rules can be defined in $validate.
	 *
	 * @return boolean True if validate operation should continue, false to abort
	 * @param $options array Options passed from model::save(), see $options of model::save().
	 * @access public
	 */
	public function beforeValidate()
	{
		if (empty($this->data['Manufacturer']['url']) && !empty($this->data['Manufacturer']['name']))
		{
			$url = $this->data['Manufacturer']['name'];
		}
		else
		{
			$url = $this->data['Manufacturer']['url'];
		}
		
		$this->data['Manufacturer']['url'] = Inflector::slug(strtolower($url), '-');

		if(empty($this->data['Manufacturer']['sort']) || !is_numeric($this->data['Manufacturer']['sort'])){
			$this->data['Manufacturer']['sort'] = 0;
		}

		return true;
		
	}
	
	/**
	 * Called after each find operation. Can be used to modify any results returned by find().
	 * Return value should be the (modified) results.
	 *
	 * @param mixed $results The results of the find operation
	 * @param boolean $primary Whether this model is being queried directly (vs. being queried as an association)
	 * @return mixed Result of the find operation
	 * @access public
	 */
	public function afterFind($results, $primary)
	{
		foreach ($results as $k => $result)
		{
			if (empty($result['Manufacturer']['id']))
			{
				continue;
			}
								
			if (!empty($result['Manufacturer']['img_ext']))
			{
				$file = $result['Manufacturer']['id'] . '.' . $result['Manufacturer']['img_ext'];
				$headerPath = Configure::read('Images.manufacturer_path') . $file;
				if (file_exists(WWW_ROOT . $headerPath))
				{
					$results[$k]['Manufacturer']['root_path'] = WWW_ROOT . $headerPath;
					$results[$k]['Manufacturer']['web_path'] = '/' . $headerPath;
				}
			}
			
			if (!empty($result['Manufacturer']['landing_img_ext']))
			{
				$file = $result['Manufacturer']['id'] . '.' . $result['Manufacturer']['landing_img_ext'];
				$landingPath = Configure::read('Images.manufacturer_landing_path') . $file;
				if (file_exists(WWW_ROOT . $landingPath))
				{
					$results[$k]['Manufacturer']['landing_root_path'] = WWW_ROOT . $landingPath;
					$results[$k]['Manufacturer']['landing_web_path'] = '/' . $landingPath;
				}
			}
			
		}
		
		return $results;
		
	}
	
	/**
	 * Check manufacturer url is unique to language.
	 * 
	 * @return bool
	 * @access public
	 */
	public function manufacturerUrlIsUnique()
	{
		$url = $this->data['Manufacturer']['url'];
		
		if (in_array($url, Configure::read('Catalog.reserved_url_words')))
		{
			return false;
		}
		
		$conditions = array('Manufacturer.url' => $url);
		
		if (!empty($this->data['Manufacturer']['id']))
		{
			$conditions['Manufacturer.id !='] = $this->data['Manufacturer']['id'];
		}
		
		$result = $this->find('first', array('conditions' => $conditions));
		
		if (!empty($result))
		{
			return false;
		}
		
		return true;
		
	}
	
	/**
	 * Get all featured manufacturers.
	 * 
	 * @return array
	 * @access public
	 */
	public function getFeatured()
	{
		$this->unbindProducts();
		return $this->find('all', array(
			'conditions' => array('Manufacturer.featured' => 1),
			'order' => array('Manufacturer.sort')
		));
	}

	/**
	 * Get all manufacturers.
	 * 
	 * @return array
	 * @access public
	 */
	public function getAll()
	{
		$this->unbindProducts();
		return $this->find('all', array(
			'order' => array('Manufacturer.name')
		));
	}
	
	/**
	 * Get list of manufacturers (url => name)
	 *
	 * @return array
	 * @access public
	 */
	public function getUrlList($type = null)
	{
		$conditions = array();
		if(!empty($type)){
			$conditions['in_' . $type] = '1';
		}

		$this->unbindProducts();
		return $this->find('list', array(
			'fields' => array('Manufacturer.url', 'Manufacturer.name'),
			'conditions' => $conditions
		));
	}

	/**
	 * Unbind products from categories.
	 *
	 * @return void
	 * @access public
	 */
	public function unbindProducts($reset = false)
	{
		$this->unbindModel(array('hasAndBelongsToMany' => array('Product')), $reset);
	}
	
}



