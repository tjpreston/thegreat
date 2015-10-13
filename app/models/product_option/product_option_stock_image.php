<?php

/**
 * Product Option Stock Image Model
 *
 */
class ProductOptionStockImage extends AppModel
{
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
		$types = array('large','medium', 'small', 'thumb', 'tiny');
                xdebug_break();
		foreach ($results as $k => $result)
		{
			if (empty($result['ProductOptionStockImage']['id']) || empty($result['ProductOptionStockImage']['ext']))
			{
				continue;
			}
			
			$filename = $result['ProductOptionStockImage']['filename'] . '.' . $result['ProductOptionStockImage']['ext'];
			
			foreach ($types as $type)
			{
				$path = Configure::read('Images.var_' . $type . '_path') . $filename;
				
				if (file_exists(WWW_ROOT . $path))
				{
					$results[$k]['ProductOptionStockImage'][$type . '_root_path'] = WWW_ROOT . $path;
					$results[$k]['ProductOptionStockImage'][$type . '_web_path'] = '/' . $path;
				}
				else
				{
					$results[$k]['ProductOptionStockImage'][$type . '_root_path'] = WWW_ROOT . 'img/vars/no-' . $type . '.png';
					$results[$k]['ProductOptionStockImage'][$type . '_web_path'] = '/img/vars/no-' . $type . '.png';
				}
			}
			
		}
		
		return $results;
		
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
		$types = array('large','medium', 'small', 'thumb', 'tiny');

		$record = $this->find('first', array('conditions' => array(
			'ProductOptionStockImage.id' => $this->id
		)));
		
		foreach ($types as $type)
		{
			if(empty($record[$this->alias][$type . '_root_path'])){
				continue;
			}

			if(stripos($record[$this->alias][$type . '_root_path'], 'no-' . $type) !== FALSE){
				continue;
			}
			
			if (file_exists($record[$this->alias][$type . '_root_path'])){
				@unlink($record[$this->alias][$type . '_root_path']);
			}
		}

		return true;

	}

}



