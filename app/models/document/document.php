<?php

/**
 * Document Model
 *
 */
class Document extends AppModel
{
	/**
	 * List of validation rules. 
	 *
	 * @var array
	 * @access public
	 */
	public $validate = array(
		'name' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter the document name'
		)
	);
	
	public function beforeSave()
	{
		if (empty($this->data['Document']['id']) && !empty($this->data['Document']['file']['error']) && !is_uploaded_file($this->data['Document']['file']['tmp_name']))
		{
			$this->invalidate('file', 'You must upload a file');
			return false;
		}

		if (empty($this->data['Document']['file']['error']))
		{
			$checkPath = WWW_ROOT . Configure::read('Documents.path') . $this->data['Document']['file']['name'];
			if (file_exists($checkPath))
			{
				$this->invalidate('file', 'File already exists. Please rename the file before uploading.');
				return false;
			}
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
			if (empty($result['Document']['id']))
			{
				continue;
			}
								
			if (!empty($result['Document']['ext']))
			{
				$path = Configure::read('Documents.path') . $result['Document']['filename'];
				
				if (file_exists(WWW_ROOT . $path))
				{
					$results[$k]['Document']['root_path'] = WWW_ROOT . $path;
					$results[$k]['Document']['web_path'] = '/' . $path;
				}
			}
			
		}
		
		return $results;
		
	}

}

