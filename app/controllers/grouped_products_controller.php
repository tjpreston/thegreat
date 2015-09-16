<?php

/**
 * Grouped Products Controller
 * 
 */
class GroupedProductsController extends AppController
{
	/**
	 * Delete a grouped product link.
	 * 
	 * @param int $productID
	 * @return void
	 * @access public
	 */
	public function admin_delete($id)
	{
		$this->GroupedProduct->id = $id;
		$productID = $this->GroupedProduct->field('from_product_id');
		
		$this->GroupedProduct->delete($id);
		
		$this->Session->setFlash('Grouped product association deleted.', 'default', array('class' => 'success'));
		$this->redirect('/admin/products/edit/' . $productID);
		
	}
	
	
}
