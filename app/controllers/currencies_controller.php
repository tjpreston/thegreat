<?php

/**
 * Currencies Controller
 * 
 */
class CurrenciesController extends AppController
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
	}
	
	/**
	 * Set active currency and return to referring page
	 *
	 * @param int $currencyID Currency ID
	 * @return void
	 * @access public
	 */
	public function set_currency($currencyID = null)
	{	
		if (!empty($currencyID))
		{
			$currencies = $this->Currency->find('list');
			
			if (array_key_exists($currencyID, $currencies))
			{
				$this->activeCurrencyID = $currencyID;
				
				$this->Session->write('currency', $currencyID);
				Configure::write('Runtime.active_currency', $this->activeCurrencyID);
				
				// $this->Category->Product->unbindModel(array('hasOne' => array('ProductPrice')), false);				
				$this->Category->Product->bindPrice($this->activeCurrencyID, false);
				
				$this->Basket->saveTotals();
		
			}
		}
		
		$this->redirect($this->referer());
		
	}
	
	/**
	 * Show exchange rate entry screen
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_exchange_rates()
	{
		$currencies = $this->Currency->find('all');
		
		foreach ($currencies as $k => $currency)
		{
			if (Configure::read('Currencies.main_currency_id') == $currency['Currency']['id'])
			{
				$mainCurrency = $currency;
				unset($currencies[$k]);
			}
		}
		
		$this->set(compact('currencies', 'mainCurrency'));
		
	}
	
	/**
	 * Update exchange rates
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_save_exchange_rates()
	{
		if ($this->Currency->saveAll($this->data['Currency']))
		{
			$this->Session->setFlash('Exchange rates updated.', 'default', array('class' => 'success'));
			$this->redirect('/admin/currencies/exchange_rates');	
		}
		else
		{
			$this->Session->setFlash('Exchange rates could not be saved. Please check the form for errors.', 'default', array('class' => 'failure'));
			return $this->setAction('admin_exchange_rates');
		}
		
	}
	
	
}
