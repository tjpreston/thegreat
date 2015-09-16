<?php

/**
 * Product Option Stock Image Controller
 *
 */
class ProductOptionStockImagesController extends AppController
{
	/**
	 * Admin
	 * Delete image.
	 * 
	 * @param int $id Product Image ID
	 * @return void
	 * @access public
	 */
	public function admin_delete($id)
	{
		$this->ProductOptionStockImage->bindModel(array('belongsTo' => array('ProductOptionStock')));

		$record = $this->ProductOptionStockImage->find('first', array('conditions' => array(
			'ProductOptionStockImage.id' => $id
		)));

		if (empty($record))
		{
			$this->redirect('/admin/products');
		}

		$product = $this->ProductOptionStockImage->ProductOptionStock->Product->find('first', array('conditions' => array(
			'Product.id' => $record['ProductOptionStock']['product_id']
		)));
		
		$this->ProductOptionStockImage->delete($id);
		
		$this->Session->setFlash('Image deleted.', 'default', array('class' => 'success'));
		$this->redirect('/admin/products/edit/' . $product['Product']['id']  . '/tab:variations');
		
	}

	





}


