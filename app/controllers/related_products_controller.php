<?php

/**
 * Related Products Controller
 * 
 */
class RelatedProductsController extends AppController
{
	/**
	 * Delete a related product link.
	 * 
	 * @param int $productID
	 * @return void
	 * @access public
	 */
	public function admin_delete($relatedProductID)
	{
		$this->RelatedProduct->id = $relatedProductID;
		$productID = $this->RelatedProduct->field('from_product_id');
		
		$this->RelatedProduct->delete($relatedProductID);
		
		$this->Session->setFlash('Related product association deleted.', 'default', array('class' => 'success'));
		$this->redirect('/admin/products/edit/' . $productID);
		
	}
	
	
	
	
}
