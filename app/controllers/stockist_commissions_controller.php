<?php

class StockistCommissionsController extends AppController
{
	/**
	 * Called before the controller action.
	 *
	 * @return void
	 * @access public
	 */
	function beforeFilter() 
	{
		$this->Auth->allow('*');
		parent::beforeFilter();
		$this->Security->disabled = true;
		$this->Security->validatePost = false;
	}
	
	function admin_index($date = null)
	{
		if(empty($this->data['month']['month']) || empty($this->data['year']['year'])){
			$month = date('m');
			$year = date('Y');
		} else {
			$month = $this->data['month']['month'];
			$year = $this->data['year']['year'];
		}
		
		$records = $this->StockistCommission->commissionReport($month, $year);
		
		$this->set(compact('records', 'month', 'year'));
	}
	
	function admin_orders($date, $stockist_id)
	{
		if(!empty($this->data['month']['month']) && !empty($this->data['year']['year'])){
			$this->redirect('/admin/stockist_commissions/orders/' . $this->data['year']['year'] . '-' . $this->data['month']['month'] . '/' . $stockist_id);
		}
		
		$date_array = explode('-', $date);
		$year = $date_array[0];
		$month = $date_array[1];
		
		$start = $this->StockistCommission->firstOfMonth($month, $year);
		$end = $this->StockistCommission->lastOfMonth($month, $year);
		
		$records = $this->StockistCommission->find('all', array(
			'conditions' => array(
				'StockistCommission.stockist_id' => $stockist_id,
				'Order.created BETWEEN ? AND ?' => array($start, $end)
			),
			'contain' => array('StockistCommission', 'Order'),
			'fields' => array('StockistCommission.*', 'Order.*')
		));
		
		$stockists = $this->StockistCommission->Stockist->find('list');
		
		$stockist = $this->StockistCommission->Stockist->find('first', array(
			'conditions' => array('Stockist.id' => $stockist_id)
		));
		
		$this->set(compact('records', 'stockist', 'month', 'year', 'stockists', 'stockist_id'));
	}
	
}

?>