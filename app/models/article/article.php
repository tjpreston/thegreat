<?php

/**
 * Article Model
 *
 */
class Article extends AppModel
{
	/**
	 * Model ordering
	 *
	 * @var array
	 * @access public
	 */
	public $order = array('Article.published DESC');
	
	/**
	 * List of validation rules. 
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(
		'slug' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Slug name missing'
		),
		'published' => array(
			'rule' => 'date',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Published date missing'
		),
		'name' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Article name missing'
		),
		'blurb' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Blurb content missing'
		),
		'content' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Article content missing'
		)
	);
	
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
			if (!empty($result['Article']['id']) && !empty($result['Article']['ext1']))
			{
				$path = Configure::read('News.image_1_path') . $result['Article']['filename1'] . '.' . $result['Article']['ext1'];
				
				if (file_exists(WWW_ROOT . $path))
				{
					$results[$k]['Article']['root_path'] = WWW_ROOT . $path;
					$results[$k]['Article']['web_path'] = '/' . $path;
				}
			}
		}
		
		return $results;
		
	}
	
	/**
	 * Called during save operations, before validation. Please note that custom
	 * validation rules can be defined in $validate.
	 *
	 * @return boolean True if validate operation should continue, false to abort
	 * @param $options array Options passed from model::save(), see $options of model::save().
	 * @access public
	 */
	public function beforeValidate($options = array()) 
	{
		if (!empty($this->data['Article']['name']))
		{
			$this->data['Article']['slug'] = Inflector::slug(strtolower($this->data['Article']['name']), '-');
		}
		
		return true;
		
	}
	
	/**
	 * Called after every deletion operation.
	 *
	 * @param boolean $cascade If true records that depend on this record will also be deleted
	 * @return boolean True if the operation should continue, false if it should abort
	 * @access public
	 */
	public function beforeDelete($cascade = true) 
	{
		$record = $this->findById($this->id);		
		$path = WWW_ROOT . Configure::read('News.image_1_path') . $record['Article']['filename1'] . '.' . $record['Article']['ext1'];
		@unlink($path);		
	
		return true;
	}
	
}


