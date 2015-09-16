<?php

/**
 * Coupons Controller
 *
 */
class CouponsController extends AppController
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
	 * Edit an existing record or display form for adding a new record.
	 * 
	 * @param int $id [optional]
	 * @return void
	 * @access public
	 */
	public function admin_edit($id = null)
	{
		if (!empty($id) && is_numeric($id))
		{
			$record = $this->Coupon->find('first', array(
				'conditions' => array('Coupon.id' => $id)
			));
			
			if (empty($this->data))
			{
				$this->data = $record;
			}

		}
	}
	
	/**
	 * Admin
	 * Save record (existing or new).
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_save()
	{
		if ($this->Coupon->saveAll($this->data))
		{
			$id = (empty($this->data['Coupon']['id'])) ? $this->Coupon->getInsertID() : $this->data['Coupon']['id'];

			$this->Session->setFlash('Record saved.', 'default', array('class' => 'success'));
			$this->redirect('/admin/coupons/edit/' . $id);	
		}
		
		$this->Session->setFlash('Record could not be saved. Please check the form for errors.', 'default', array('class' => 'failure'));
		return $this->setAction('admin_edit');	

	}
		
	/**
	 * Admin
	 * Delete a record.
	 * 
	 * @param int $id
	 * @return void
	 * @access public
	 */
	public function admin_delete($id)
	{
		if ($this->Coupon->delete($id))
		{
			$this->Session->setFlash('Record deleted.', 'default', array('class' => 'success'));
			$this->redirect('/admin/coupons');
		}
		
		$this->Session->setFlash('Record not deleted.', 'default', array('class' => 'failure'));
		$this->redirect('/admin/coupons/edit/' . $id);	
		
	}
	
}



