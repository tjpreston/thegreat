<?php

class StockistCommission extends AppModel
{
	/**
	 * Detailed list of belongsTo associations.
	 *
	 * @var array
	 * @access public
	 */
	public $belongsTo = array(
		'Stockist',
		'Order'
	);
	
	var $commissionTypes = array(
		'basket',
		'referral'
	);
	
	function afterFind($results, $primary)
	{
		if($primary)
		{
			$count = count($results);
			for($i = 0; $i < $count; $i++)
			{
				/*if(is_array($results[$i]['orders']))
				{
					$orderscount = count($results[$i]['orders']);
					for($j = 0; $j < $orderscount; $j++){
						$orderTotal = $results[$i]['orders'][$j]['Order']['grand_total'];
						$taxReduction = $results[$i]['orders'][$j]['Order']['tax_reduction'];
						$commissionType = $results[$i]['orders'][$j]['StockistCommission']['type'];
						$results[$i]['orders'][$j]['StockistCommission']['pay'] = $this->calculatePay($orderTotal, $taxReduction, $commissionType);
					}
				}
				else
				{*/
					$orderTotal = $results[$i]['Order']['grand_total'];
					$taxRate = $results[$i]['Order']['tax_rate'];
					$commissionType = $results[$i]['StockistCommission']['type'];
					$results[$i]['StockistCommission']['pay'] = $this->calculatePay($orderTotal, $taxRate, $commissionType);
				//}
			}
		}
		
		return $results;
	}
	
	function calculatePay($orderTotal, $taxRate, $commissionType)
	{
		// Remove VAT from order total before calculating commission
		$orderTotal = $orderTotal / ( ( 100 + $taxRate ) / 100 );
		
		switch($commissionType)
		{
			case 'basket':
				$rate = Configure::read('StockistCommission.Basket.rate');
				$commission = $orderTotal * $rate;
				break;
			case 'referral':
				$tradePriceRate = Configure::read('StockistCommission.Affiliate.trade_price_rate');
				$adminFeeRate = Configure::read('StockistCommission.Affiliate.admin_fee_rate');
				$tradePrice = $orderTotal * $tradePriceRate;
				$adminFee = $orderTotal * $adminFeeRate;
				$commission = ($orderTotal - $tradePrice) - $adminFee;
				break;
		}
		
		return number_format($commission, 2);
	}
	
	function lastOfMonth($month, $year, $returnSeconds = false)
	{
		$date = strtotime('-1 second',strtotime('+1 month',strtotime($month.'/01/'.$year.' 00:00:00')));
		if($returnSeconds) return $date;
		return date("Y-m-d H:i:s", $date);
	}
	
	function firstOfMonth($month, $year, $returnSeconds = false)
	{
		$date = strtotime($month.'/01/'.$year.' 00:00:00');
		if($returnSeconds) return $date;
		return date("Y-m-d", $date);
	}
	
	function commissionReport($month, $year)
	{
		$stockists = $this->Stockist->find('all', array(
			'contain' => ''
		));
		
		$start = $this->firstOfMonth($month, $year);
		$end = $this->lastOfMonth($month, $year);
		
		$return = array();
		
		foreach($stockists as $s){
			$id = $s['Stockist']['id'];
			$commission = $this->calculateCommissionForMonth($id, $month, $year);
			if(!empty($commission)) $return[] = array_merge($s, array('commission' => $commission));
		}
		
		return $return;
	}
	
	function calculateCommissionForMonth($stockist_id, $month, $year){
		$start = $this->firstOfMonth($month, $year);
		$end = $this->lastOfMonth($month, $year);
		$count = 0;
		
		foreach($this->commissionTypes as $type){
			$commission[$type] = 0;
			
			$orders = $this->find('all', array(
				'conditions' => array(
					'Order.created BETWEEN ? AND ?' => array($start, $end),
					'StockistCommission.stockist_id' => $stockist_id,
					'StockistCommission.type' => $type
				),
				'contain' => array('Order')
			));
			
			foreach($orders as $order){
				$commission[$type] = $commission[$type] + $order['StockistCommission']['pay'];
			}
			
			$count = $count + count($orders);
		}
		
		$commission['total'] = $commission[$this->commissionTypes[0]] + $commission[$this->commissionTypes[1]];
		$commission['orders_count'] = $count;
		
		if($commission[$this->commissionTypes[0]] > 0 || $commission[$this->commissionTypes[1]] > 0){
			return $commission;
		} else {
			return false;
		}
	}
	
}


?>