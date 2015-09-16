<?php

/**
 * Attribute Sets Controller
 * 
 */
class AttributeSetsController extends AppController
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
		$this->AttributeSet->bindName(0, false);
		
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
		$this->AttributeSet->bindName(null, false);
		
		if (!empty($id) && is_numeric($id))
		{	
			$record = $this->AttributeSet->find('first', array(
				'conditions' => array('AttributeSet.id' => $id)
			));
			
			$record['AttributeSetName'] = $this->AttributeSet->AttributeSetName->getNames('attribute_set_id', $id);
			
			if (empty($this->data))
			{
				$this->data = $record;
			}			
		}
		
		$this->set('attributes', $this->AttributeSet->Attribute->getList($this->AttributeSet->Attribute->getAttributes()));
		
	}
	
	/**
	 * Admin
	 * Save record (new or existing).
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_save()
	{
		$this->AttributeSet->bindName(null, false);
		
		if ($this->AttributeSet->saveAll($this->data, array('validate' => 'first')))
		{
			$id = (empty($this->data['AttributeSet']['id'])) ? $this->AttributeSet->getInsertID() : $this->data['AttributeSet']['id'];

			$this->Session->setFlash('Attribute Set saved.', 'default', array('class' => 'success'));
			$this->redirect('/admin/attribute_sets/edit/' . $id);	
		}
		
		$this->Session->setFlash('Attribute Set could not be saved. Please check the form for errors.', 'default', array('class' => 'failure'));
		return $this->setAction('admin_edit');	
		
	}
	
	/**
	 * Admin
	 * Delete manufacturer
	 * 
	 * @param int $id
	 * @return void
	 * @access public
	 */
	public function admin_delete($id)
	{
		if ($this->AttributeSet->delete($id))
		{
			$this->Session->setFlash('Attribute Set deleted.', 'default', array('class' => 'success'));
			$this->redirect('/admin/attribute_sets');
		}
		
		$this->Session->setFlash('Attribute Set not deleted.', 'default', array('class' => 'failure'));
		$this->redirect('/admin/attribute_sets/edit/' . $id);	
		
	}
	
}






