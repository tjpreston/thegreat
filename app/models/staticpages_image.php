<?php

/**
 * Static Page Images Model
 * 
 */
class StaticpagesImage extends AppModel
{
    /**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array('Staticpage');
        
        /**
	 * Get static pages images
	 * 
	 * @param int $staticpageID
	 * @return array $images
	 * @access public
	 */
	public function getImages($staticpageID)
	{
		$records = $this->find('all', array(
			'conditions' => array('StaticpagesImage.staticpage_id' => $staticpageID),
			'recursive' => -1
		));
		
		$images = array();
		
		foreach ($records as $k => $record)
		{
			$imageID = $record['StaticpagesImage']['id'];
			$images[$imageID] = $record['StaticpagesImage'];
		}
		
		return $images;
		
	}
        
    
}