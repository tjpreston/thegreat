<?php

/**
 * Custom Options Controller
 * 
 */
class CustomOptionsController extends AppController
{
	/**
	 * Holds pagination defaults for controller actions.
	 *
	 * @var array
	 * @access public
	 */
	public $paginate = array(
		'fields' => array('CustomOption.*', 'CustomOptionName.*'),
		'order' => array('CustomOptionName.name ASC'),
		'limit' => 20
	);
	
	/**
	 * Admin
	 * List custom product options. 
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_index()
	{
		$this->CustomOption->bindName($this->CustomOption, 0, false);
		$this->set('records', $this->paginate());
	}
	
	/**
	 * Admin
	 * Edit custom option.
	 * 
	 * @param int $id [optional]
	 * @return void
	 * @access public
	 */
	public function admin_edit($id = null)
	{
		$this->set('json', '{}');
		
		$this->CustomOption->bindName($this->CustomOption, null, true);
		
		if (!empty($id))
		{
			$record = $this->CustomOption->find('first', array(
				'conditions' => array('CustomOption.id' => $id),
				'recursive' => -1
			));
			$record['CustomOptionName'] = $this->CustomOption->CustomOptionName->getNames('custom_option_id', $id);
			$record['CustomOptionValue'] = $this->CustomOption->CustomOptionValue->getValues($id, true);
			
			$this->data = $record;
			
			$this->set('record', $record);
			$this->set('json', json_encode($this->_getJsonArray($record)));
		}
		
	}
	
	/**
	 * Admin
	 * Create a new custom option.
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_create()
	{
		$this->CustomOption->set($this->data);
		$newOptionID = $this->CustomOption->saveNewOption();
		
		if (!empty($newOptionID))
		{
			$this->Session->setFlash('Custom Option created.', 'default', array('class' => 'success'));
			$this->redirect('/admin/custom_options/edit/' . $newOptionID);
		}
	
		$this->Session->setFlash('Custom Option could not be created. Please check the form for errors.', 'default', array('class' => 'failure'));
		return $this->setAction('admin_edit');
		
	}
	
	/**
	 * Admin
	 * Save custom option.
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_save()
	{
		$id = $this->data['CustomOption']['id'];
		
		$this->CustomOption->bindName($this->CustomOption, null, false);
		$this->CustomOption->CustomOptionValue->bindName($this->CustomOption->CustomOptionValue, null, false);
		
		if ($this->CustomOption->CustomOptionName->saveAll($this->data['CustomOptionName']))
		{
			$this->CustomOption->CustomOptionValue->saveValues($this->data);
			
			$this->Session->setFlash('Custom Option saved.', 'default', array('class' => 'success'));
			$this->redirect('/admin/custom_options/edit/' . $id);
		}
		
		$this->Session->setFlash('Custom Option could not be saved. Please check the form for errors.', 'default', array('class' => 'failure'));
		return $this->setAction('admin_edit', $id);
		
	}
	
	/**
	 * Admin
	 * Delete custom option.
	 * 
	 * @param int $id
	 * @return void
	 * @access public
	 */
	public function admin_delete($id)
	{
		if ($this->CustomOption->delete($id))
		{
			$this->Session->setFlash('Custom Option deleted.', 'default', array('class' => 'success'));
		}
		else
		{
			$this->Session->setFlash('Cannot delete a custom option while it is in use.', 'default', array('class' => 'failure'));
		}
		
		$this->redirect('/admin/custom_options');
		
	}
	
	/**
	 * Build array of values for encoding to JSON.
	 * 
	 * @param array $record
	 * @return array
	 * @access private
	 */
	private function _getJsonArray($record)
	{
		$names = array();
			
		foreach ($record['CustomOptionValue'] as $value)
		{
			$valueID = $value['CustomOptionValue']['id'];				
			$names[$valueID] = array();
			
			foreach (Configure::read('Runtime.languages') as $langID => $langName)
			{	
				$names[$valueID][$langID] = (!empty($value['CustomOptionValueName'][$langID]['name'])) ? $value['CustomOptionValueName'][$langID]['name'] : '';							
			}
		}
		
		foreach (Configure::read('Runtime.languages') as $langID => $langName)
		{	
			$names['new'][$langID] = '';
		}
			
		return $names;
		
	}
	
}


