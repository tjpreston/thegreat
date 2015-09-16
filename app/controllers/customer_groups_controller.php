<?php

/**
 * Customer Groups Controller
 *
 */
class CustomerGroupsController extends AppController
{
	/**
	 * Admin
	 * List records. 
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_index()
	{
		$this->set('records', $this->paginate());		
	}
	
	/**
	 * Admin
	 * View record.
	 * 
	 * @param int $id
	 * @return void
	 * @access public
	 */
	public function admin_edit($id = null)
	{		
		if (empty($this->data))
		{
			$this->data = $this->CustomerGroup->findById($id);
		}
	}

	public function admin_delete($id = null){
		$this->CustomerGroup->delete($id);
		$this->redirect($this->referer());
	}
	
	/**
	 * Admin
	 * Save customer record.
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_save()
	{
		if ($this->CustomerGroup->saveAll($this->data))
		{
			$id = (empty($this->data['CustomerGroup']['id'])) ? $this->CustomerGroup->getInsertID() : $this->data['CustomerGroup']['id'];
			
			$this->Session->setFlash('Record saved.', 'default', array('class' => 'success'));
			$this->redirect('/admin/customer_groups/edit/' . $id);
		}	
		
		$this->Session->setFlash('Record could not be saved. Please check the form for errors.', 'default', array('class' => 'failure'));
		$this->setAction('admin_edit', $id);
		
	}

}





