<?php

/**
 * Product Cross Sells Controller
 * 
 */
class CrossSellsController extends AppController
{
	/**
	 * Admin
	 * Delete cross sell
	 * 
	 * @param int $id
	 * @return void
	 * @access public
	 */
	public function admin_delete($id)
	{
		$this->Session->write('Admin.products.last_tab', 'cross');
		
		$this->CrossSell->id = $id;
		$productID = $this->CrossSell->field('from_product_id');
		
		if ($this->CrossSell->delete($id))
		{
			$this->Session->setFlash('Cross-sell link deleted.', 'default', array('class' => 'success'));
		}
		else
		{
			$this->Session->setFlash('Cross-sell not deleted.', 'default', array('class' => 'failure'));
		}
		
		$this->redirect('/admin/products/edit/' . $productID);	
		
	}
	
	
}


