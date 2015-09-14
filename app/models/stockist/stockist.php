<?php

class Stockist extends AppModel
{
	public $order = array('name');

	public $hasMany = array('StockistCommission');

	public $hasAndBelongsToMany = array('StockistCounty');
	
	public function postcode2LatLon($postcode, $forceLookup = false)
	{ $forceLookup = true;
		if(Configure::read('debug') < 1 || $forceLookup){
			$client = new SoapClient("http://www.postcoderwebsoap.co.uk/websoap/websoap.php?wsdl");
			$result = $client->getGrids($postcode, 'MichelHerbelinStockist', 'm1ch3l', 'h3rb3l1n');
			
			if(empty($result->latitude_etrs89) || empty($result->longitude_etrs89)){
				return false;
			}
			
			$lat = $result->latitude_etrs89;
			$lon = $result->longitude_etrs89;
		} else {
			$lat = 51.866722;
			$lon = 0.278353;
		}
		
		$latlon = array('lat' => $lat, 'lon' => $lon);
		
		return $latlon;
	}
	
	public function getByPostcode($postcode, $limit = null)
	{
		$latlon = $this->postcode2LatLon($postcode);
		if(!$latlon){
			return false;
		}
		
		$this->virtualFields = array(
			'distance' => "((ACOS(SIN(" . $latlon['lat'] . " * PI() / 180) * SIN(" . $this->name . ".latitude * PI() / 180) + COS(" . $latlon['lat'] . " * PI() / 180) * COS(" . $this->name . ".latitude * PI() / 180) * COS((" . $latlon['lon'] . " - " . $this->name . ".longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515)"
		);
		
		$conditions = array();
		if(!empty($limit) && is_numeric($limit)){
			$conditions['distance <='] = $limit;
		}

		$conditions['latitude <>'] = null;
		$conditions['longitude <>'] = null;
		
		$return = $this->find('all', array(
			'conditions' => $conditions,
			'limit' => 5,
			'order' => 'distance ASC',
			'contain' => '',
		));
		
		return $return;
	}

	public function getByCounty($county_id){
		$dbo = $this->StockistCounty->getDataSource();
		$subQuery = $dbo->buildStatement(array(
			'fields' => array('`' . $this->hasAndBelongsToMany['StockistCounty']['with'] . '`.`stockist_id`'),
			'table' => $this->hasAndBelongsToMany['StockistCounty']['joinTable'],
			'alias' => $this->hasAndBelongsToMany['StockistCounty']['with'],
			'limit' => null,
			'offset' => null,
			'joins' => array(),
			'conditions' => array('`' . $this->hasAndBelongsToMany['StockistCounty']['with'] . '`.`stockist_county_id`' => $county_id),
			'order' => null,
			'group' => null,
		), $this->StockistCounty);

		$subQuery = ' `Stockist`.`id` IN (' . $subQuery . ') ';
		$subQueryExpr = $dbo->expression($subQuery);

		$records = $this->find('all', array(
			'conditions' => array($subQueryExpr),
			'contain' => array('StockistCounty'),
		));

		return $records;
	}
	
	public function beforeSave($options){
		if(!empty($this->data[$this->name]['postcode'])){
			$latlon = $this->postcode2LatLon($this->data[$this->name]['postcode'], true);
			$this->data[$this->name]['latitude'] = $latlon['lat'];
			$this->data[$this->name]['longitude'] = $latlon['lon'];
		}
		
		return true;
	}
	
	// Refresh lat & lon for *all* stockists in database.
	function refreshLatLon(){
		$records = $this->find('all');
		foreach($records as $r){
			$this->id = $r['Stockist']['id'];
			$postcode = $r['Stockist']['postcode'];
			$latlon = $this->postcode2LatLon($postcode);
			$save = array('Stockist' => array(
				'latitude' => $latlon['lat'],
				'longitude' => $latlon['lon']
			));
			$this->save($save);
		}
	}
	
}


?>