<?php

/**
 * Attributes Controller
 * 
 */
class AttributesController extends AppController
{
	/**
	 * Admin
	 * Display list of records.
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_index()
	{
		$this->Attribute->bindName(0, false);
		$this->paginate['limit'] = 1000;
		$this->set('records', $this->paginate());
	}
	
	/**
	 * Admin
	 * Edit a record.
	 * 
	 * @param int $id [optional]
	 * @return void
	 * @access public
	 */
	public function admin_edit($id = null)
	{
		$this->set('json', '{}');
		
		$this->Attribute->bindName(null, true);
		
		if (!empty($id))
		{
			$record = $this->Attribute->find('first', array(
				'conditions' => array('Attribute.id' => $id),
				'recursive' => -1
			));
			$record['AttributeName'] = $this->Attribute->AttributeName->getNames('attribute_id', $id);
			$record['AttributeValue'] = $this->Attribute->AttributeValue->getValues($id, true);
			
			$this->data = $record;
			
			$this->set('record', $record);
			$this->set('json', json_encode($this->_getJsonArray($record)));
		}
		
	}
	
	/**
	 * Admin
	 * Create a new record.
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_create()
	{
		$this->Attribute->set($this->data);
		$newID = $this->Attribute->saveNewOption();
		
		if (!empty($newID))
		{
			$this->Session->setFlash('Attribute created.', 'default', array('class' => 'success'));
			$this->redirect('/admin/attributes/edit/' . $newID);
		}
	
		$this->Session->setFlash('Attribute could not be created. Please check the form for errors.', 'default', array('class' => 'failure'));
		return $this->setAction('admin_edit');
		
	}
	
	/**
	 * Admin
	 * Save record.
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_save()
	{
		$id = $this->data['Attribute']['id'];
		
		$this->Attribute->bindName(null, false);
		$this->Attribute->AttributeValue->bindName(null, false);
		
		if ($this->Attribute->AttributeName->saveAll($this->data['AttributeName']))
		{
			$this->Attribute->AttributeValue->saveValues($this->data);
			
			$this->Session->setFlash('Attribute saved.', 'default', array('class' => 'success'));
			$this->redirect('/admin/attributes/edit/' . $id);
		}
		
		$this->Session->setFlash('Attribute could not be saved. Please check the form for errors.', 'default', array('class' => 'failure'));
		return $this->setAction('admin_edit', $id);
		
	}

	/**
	 * Admin
	 * Delete attribute.
	 * 
	 * @param int $id
	 * @return void
	 * @access public
	 */
	public function admin_delete($id)
	{
		if ($this->Attribute->delete($id))
		{
			$this->Session->setFlash('Attribute deleted.', 'default', array('class' => 'success'));
		}
		else
		{
			$this->Session->setFlash('Cannot delete a attribute while it is in use.', 'default', array('class' => 'failure'));
		}
		
		$this->redirect('/admin/attributes');
		
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
			
		foreach ($record['AttributeValue'] as $value)
		{
			$valueID = $value['AttributeValue']['id'];				
			$names[$valueID] = array();
			
			foreach (Configure::read('Runtime.languages') as $langID => $langName)
			{	
				$names[$valueID][$langID] = (!empty($value['AttributeValueName'][$langID]['name'])) ? $value['AttributeValueName'][$langID]['name'] : '';							
			}
		}
		
		foreach (Configure::read('Runtime.languages') as $langID => $langName)
		{	
			$names['new'][$langID] = '';
		}
			
		return $names;
		
	}
	
	
}




