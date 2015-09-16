<?php

/**
 * Basket Discounts Controller
 * 
 */
class BasketDiscountsController extends AppController
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
			$this->BasketDiscount->bindPrice(null, false);

			$record = $this->BasketDiscount->find('first', array(
				'conditions' => array('BasketDiscount.id' => $id)
			));

			$values = $record['BasketDiscountPrice'];
			$indexed = array();
			
			foreach ($values as $k => $value)
			{
				$cID = $value['currency_id'];
				$indexed[$cID] = $value;
			}
			
			$record['BasketDiscountPrice'] = $indexed;
			
			if (empty($this->data))
			{
				$this->data = $record;
			}
		}
		
		$this->set('currencies', $this->Currency->find('all'));

		// Get categories
		$this->BasketDiscount->Product->ProductCategory->Category->unbindProducts();
		$this->BasketDiscount->Product->ProductCategory->Category->bindName($this->BasketDiscount->Product->ProductCategory->Category, 1);
		$this->BasketDiscount->Product->ProductCategory->Category->recursive = 1;
		$categories = $this->BasketDiscount->Product->ProductCategory->Category->find('threaded');

		$this->set('categories', $categories);

		if(!empty($record)){
			$checkedCategories = Set::extract('Category.{n}.id', $record);
			$this->set('checkedCategories', $checkedCategories);
		}

		// Get products
		$this->BasketDiscount->Product->bindName($this->BasketDiscount->Product, 1);
		$allProductsList = $this->BasketDiscount->Product->find('all', array(
			'fields' => array('Product.id', 'Product.sku', 'ProductName.name'),
			'order' => array('Product.sku')
		));
		
		$productList = array();	
		foreach ($allProductsList as $product)
		{
			$productList[$product['Product']['id']] = $product['Product']['sku'] . ' ' . $product['ProductName']['name'];
		}

		$this->set('productList', $productList);
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

		$this->BasketDiscount->bindPrice(null, false);

		// Massage HABTM fields
		foreach($this->data['Category']['Category'] as $k => $v){
			if($v == 0){
				unset($this->data['Category']['Category'][$k]);
			}
		}

		switch ($this->data['BasketDiscount']['applies_to']) {
			case 'products':
				$this->data['Category']['Category'] = array();
				break;

			case 'categories':
				$this->data['Product']['Product'] = array();
				break;
			
			default:
				# code...
				break;
		}

		foreach($this->data['BasketDiscountPrice'] as $k => $v){
			if(empty($v['modifier_value'])){
				$this->data['BasketDiscountPrice'][$k]['modifier_value'] = '0.00';
			}
		}

		if ($this->BasketDiscount->saveAll($this->data))
		{
			$id = (empty($this->data['BasketDiscount']['id'])) ? $this->BasketDiscount->getInsertID() : $this->data['BasketDiscount']['id'];

			$this->Session->setFlash('Record saved.', 'default', array('class' => 'success'));
			$this->redirect('/admin/basket_discounts/edit/' . $id);
		}
		
		$this->Session->setFlash('Record could not be saved. Please check the form for errors.', 'default', array('class' => 'failure'));
		// debug($this->BasketDiscount->validationErrors);
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
		if ($this->BasketDiscount->delete($id))
		{
			$this->Session->setFlash('Record deleted.', 'default', array('class' => 'success'));
		}
		else
		{
			$this->Session->setFlash('Record not deleted.', 'default', array('class' => 'failure'));
		}
		
		$this->redirect('/admin/basket_discounts');
		
	}
	
}



