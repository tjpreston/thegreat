<?php

class PricesShell extends Shell
{
	public $uses = array('Product');
	
	public function main()
	{		
		$this->Product->bindPrice($this->Product, null, false);
		$this->Product->bindOptions($this->Product);
		
		// Update main prices
		$this->Product->ProductPrice->updateActivePrices();
		
		// Update option prices
		//$this->Product->ProductOption->ProductOptionValue->bindPrice($this->Product->ProductOption->ProductOptionValue, null, false);
		//$this->Product->ProductOption->ProductOptionValue->ProductOptionValuePrice->updateActivePrices();
		
		// Update lowest / highest price
		$this->Product->ProductPrice->updateLowestHightestPrices();
		
	}
	
	
}
