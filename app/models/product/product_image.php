<?php

/**
 * Product Image Model
 * 
 */
class ProductImage extends AppModel
{
	/**
	 * The column name(s) and direction(s) to order find results by default.
	 *
	 * @var string
	 * @access public
	 */
	public $order = 'ProductImage.sort_order ASC';

	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array('Product');


	/**
	 * When set to true, we won't get placeholder images
	 * when the file doesn't exists in the afterFind
	 *
	 * @var boolean
	 * @access public
	 */
	public $disablePlaceholders = false;
	
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
			if (empty($result['ProductImage']['id']) || empty($result['ProductImage']['ext']))
			{
				continue;
			}
			
			$filename = $result['ProductImage']['filename'] . '.' . $result['ProductImage']['ext'];
			
			$types = array(
				'original' => false,
				'edited'   => false,
				'large'    => true,
				'medium'   => true,
				'thumb'    => true,
				'tiny'     => true
			);
			
			foreach ($types as $type => $substitutePlaceholder)
			{
				$path = Configure::read('Images.product_' . $type . '_path') . $filename;
								
				if (file_exists(WWW_ROOT . $path))
				{
					$results[$k]['ProductImage'][$type . '_root_path'] = WWW_ROOT . $path;
					$results[$k]['ProductImage'][$type . '_web_path'] = '/' . $path;
				}
				else if ($substitutePlaceholder && $this->disablePlaceholders == false)
				{
					$results[$k]['ProductImage'][$type . '_root_path'] = WWW_ROOT . Configure::read('Images.placeholder_' . $type . '_path');
					$results[$k]['ProductImage'][$type . '_web_path'] = Configure::read('Images.placeholder_' . $type . '_path');
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
	public function beforeDelete($cascade)
	{
		$this->disablePlaceholders = true;
		$record = $this->findById($this->id);

		$sizes = array(
			'original',
			'large',
			'medium',
			'thumb',
			'tiny',
			'edited', // don't think this actually exists..
		);

		foreach($sizes as $size){
			if(!empty($record['ProductImage'][$size . '_root_path'])){
				@unlink($record['ProductImage'][$size . '_root_path']);
			}
		}
		
		return true;
		
	}
			
	/**
	 * Get product images
	 * 
	 * @param int $productID
	 * @return array $images
	 * @access public
	 */
	public function getImages($productID)
	{
		$records = $this->find('all', array(
			'conditions' => array('ProductImage.product_id' => $productID),
			'recursive' => -1
		));
		
		$images = array();
		
		foreach ($records as $k => $record)
		{
			$imageID = $record['ProductImage']['id'];
			$images[$imageID] = $record['ProductImage'];
		}
		
		return $images;
		
	}
	
	
	
	
	
	
	
	
	
}
