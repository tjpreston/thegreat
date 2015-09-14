<?php

class StockistsController extends AppController
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
	
	public function index()
	{
		if(!empty($this->data['Stockist'])){
			$location = $this->data['Stockist']['location'];
			switch ($location) {
				case 1:
					if(!empty($this->data['Stockist']['postcode'])){
						$postcode = $this->data['Stockist']['postcode'];
						$stockists = $this->Stockist->getByPostcode($postcode);
					}
					break;

				case 2:
					if(!empty($this->data['Stockist']['county_2'])){
						$countyID = $this->data['Stockist']['county_2'];
						$stockists = $this->Stockist->getByCounty($countyID);
					}
					break;

				case 3:
					if(!empty($this->data['Stockist']['county_3'])){
						$countyID = $this->data['Stockist']['county_3'];
						$stockists = $this->Stockist->getByCounty($countyID);
					}
					break;
			}
		}

		if(!empty($stockists)){
			$this->set(compact('stockists'));
		}

		$counties_1 = $this->Stockist->StockistCounty->find('list', array(
			'conditions' => array('StockistCounty.location_id' => 2),
			'contain' => '',
		));

		$counties_2 = $this->Stockist->StockistCounty->find('list', array(
			'conditions' => array('StockistCounty.location_id' => 3),
			'contain' => '',
		));

		$this->set(compact('counties_1', 'counties_2'));
	}
	
	/**
	 * Process affiliate links to the site. Save affiliate ID to session & redirect to homepage.
	 *
	 */
	function affiliate_handler($id)
	{
		// First check if the stockist exists.
		$this->Stockist->recursive = -1;
		$exists = $this->Stockist->find('count', array('conditions' => array('Stockist.id' => $id)));
		if($exists){
			$this->Session->write('affiliate', $id);
		}
		$this->redirect('/');
		
	}
	
	/**
	 * Admin
	 * Display list of stockist.
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_index()
	{
		$this->paginate['limit'] = 1000;
		$this->set('records', $this->paginate());
	}
	
	/**
	 * Admin
	 * Edit a stockist record.
	 * 
	 * @param int $id [optional]
	 * @return void
	 * @access public
	 */
	public function admin_edit($id = null)
	{
		if (empty($this->data))
		{
			if (!empty($id) && is_numeric($id))
			{
				$record = $this->Stockist->find('first', array(
					'conditions' => array('Stockist.id' => $id),
					'recursive' => -1
				));
				$this->data = $record;
			}
		} else
		{
			if($this->Stockist->save($this->data)){
				$this->redirect(array('action' => 'admin_index'));
			}
		}
		
	}
	
	/**
	 * Admin
	 * Delete stockist
	 * 
	 * @param int $id
	 * @return void
	 * @access public
	 */
	public function admin_delete($id)
	{
		if ($this->Stockist->delete($id))
		{
			$this->Session->setFlash('Stockist deleted.', 'default', array('class' => 'success'));
			$this->redirect(array('action' => 'admin_index'));
		}
		
		$this->Session->setFlash('Stockist not deleted.', 'default', array('class' => 'failure'));
		$this->redirect(array('action' => 'admin_edit', $id));
		
	}
	
	/**
	 * Admin
	 * Refresh stockist latitude & longitude values.
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_refresh()
	{
		$this->Stockist->refreshLatLon();
		$this->redirect(array('action' => 'admin_index')); 
	}
	
}

?>