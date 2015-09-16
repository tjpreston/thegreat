<?php

/**
 * Category Description Model
 * 
 */
class CategoryDescription extends AppModel
{
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array('Category', 'Language');
	
	/**
	 * Get category descriptions
	 * 
	 * @TODO abstract this
	 * @param int $categoryID
	 * @return array $descriptions
	 * @access public
	 */
	public function getDescriptions($categoryID)
	{
		$records = $this->find('all', array(
			'conditions' => array('CategoryDescription.category_id' => $categoryID),
			'recursive' => -1
		));
		
		$descriptions = array();
		
		foreach ($records as $k => $record)
		{
			$languageID = $record['CategoryDescription']['language_id'];
			$descriptions[$languageID] = $record['CategoryDescription'];
		}
		
		return $descriptions;
		
	}
	
}


