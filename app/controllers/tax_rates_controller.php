<?php

/**
 * Tax Rates Controller
 * 
 */
class TaxRatesController extends AppController
{
	/**
	 * Admin
	 * List tax rates. 
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
	 * Edit a tax rate record.
	 * 
	 * @param int $id [optional]
	 * @return void
	 * @access public
	 */
	public function admin_edit($id = null)
	{
		if (!empty($id) && is_numeric($id))
		{
			$record = $this->TaxRate->find('first', array(
				'conditions' => array('TaxRate.id' => $id)
			));
			
			if (empty($this->data))
			{
				$this->data = $record;
			}
		}
		
		$this->set('countries', $this->TaxRate->Country->find('list'));
		
	}

	/**
	 * Admin
	 * Save tax rate (new or existing)
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_save()
	{
		if ($this->TaxRate->save($this->data))
		{
			$id = (empty($this->data['TaxRate']['id'])) ? $this->TaxRate->getInsertID() : $this->data['TaxRate']['id'];

			$this->Session->setFlash('Tax Rate saved.', 'default', array('class' => 'success'));
			$this->redirect('/admin/tax_rates/edit/' . $id);	
		}
		else
		{
			$this->Session->setFlash('Tax Rate could not be saved. Please check the form for errors.', 'default', array('class' => 'failure'));
			return $this->setAction('admin_edit');	
		}
	}
	
	/**
	 * Admin
	 * Delete tax rate
	 * 
	 * @param int $id
	 * @return void
	 * @access public
	 */
	public function admin_delete($id)
	{
		if ($this->TaxRate->delete($id))
		{
			$this->Session->setFlash('Tax Rate deleted.', 'default', array('class' => 'success'));
			$this->redirect('/admin/tax_rates');
		}
		
		$this->Session->setFlash('Tax Rate not deleted.', 'default', array('class' => 'failure'));
		$this->redirect('/admin/tax_rates/edit/' . $id);	
		
	}
	
	
}
