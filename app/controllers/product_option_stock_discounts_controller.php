<?php

/**
 * Product Option Stock Discounts Controller
 *
 */
class ProductOptionStockDiscountsController extends AppController
{
	/**
	 * Admin
	 * Edit a record.
	 * 
	 * @param int $stockID 
	 * @return void
	 * @access public
	 */
	public function admin_edit($stockID)
	{
		$this->layout = 'admin_popup';
		
		$stock = $this->ProductOptionStockDiscount->ProductOptionStock->findById($stockID);
		$productID = $stock['ProductOptionStock']['product_id'];
		
		$ids = ClassRegistry::init('ProductOptionValue')->find('list', array(
			'fields' => array('id', 'custom_option_value_id'),
			'conditions' => array('id' => explode('-', $stock['ProductOptionStock']['value_ids']))
		));
		
		$CustomOptionValue = ClassRegistry::init('CustomOptionValue');		
		$CustomOptionValue->bindName($CustomOptionValue, 0, false);
		
		$name = '';
		
		foreach ($ids as $id)
		{
			$value = $CustomOptionValue->find('first', array(
				'conditions' => array('CustomOptionValue.id' => $id)
			));
			
			$name .= $value['CustomOptionValueName']['name'] . ' ';
		}
	
		$this->ProductOptionStockDiscount->CustomerGroup->bindModel(array('hasMany' => array(
			'ProductOptionStockDiscount' => array(
				'conditions' => array('ProductOptionStockDiscount.product_option_stock_id' => $stockID),
				'order' => array('ProductOptionStockDiscount.min_qty ASC')
			)
		)));
		
		$groups = $this->ProductOptionStockDiscount->CustomerGroup->find('all');
		
		$this->set(compact('stockID', 'productID', 'groups', 'name'));		
		
	}
	
	/**
	 * Admin
	 * Save a record.
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_save()
	{
		$url = '/admin/product_option_stock_discounts/edit/' . $this->data['ProductOptionStockDiscount']['id'];
		
		$this->ProductOptionStockDiscount->pruneData($this->data);
		
		if (!empty($this->data['ProductOptionStockDiscount']))
		{
			if ($this->ProductOptionStockDiscount->saveAll($this->data['ProductOptionStockDiscount'], array('validate' => false)))
			{
				$this->Session->setFlash('Variation discount data saved.', 'default', array('class' => 'success'));
				$this->redirect($url);
			}
		}
		
		$this->Session->setFlash('Variation discount data not saved.', 'default', array('class' => 'failure'));
		$this->redirect($url);
	
	}
	
	/**
	 * Admin
	 * Delete a record.
	 * 
	 * @param int $tierID
	 * @return void
	 * @access public
	 */
	public function admin_delete_customer_tier($tierID)
	{
		$record = $this->ProductOptionStockDiscount->find('first', array(
			'conditions' => array('ProductOptionStockDiscount.id' => $tierID)
		));
		
		if (empty($record))
		{
			exit;
		}
		
		$optionID = $record['ProductOptionStockDiscount']['product_option_stock_id'];
		
		$result = $this->ProductOptionStockDiscount->delete($tierID);
		
		if ($result)
		{
			$this->Session->setFlash('Discount tier deleted.', 'default', array('class' => 'success'));
			$this->redirect('/admin/product_option_stock_discounts/edit/' . $optionID);
		}
		
		$this->Session->setFlash('Discount tier not deleted.', 'default', array('class' => 'failure'));
		$this->redirect('/admin/product_option_stock_discounts/edit/' . $optionID);
	
	}
	
	
}





