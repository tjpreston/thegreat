<?php

class GbsShippingImportShell extends Shell {

	// this can be reused - it can also be run in part of full as each stage chacks for existing
	// This script does not run for UK or the Hermes courier
	// it is only for the royal mail international post - service ids are hardcoded.
	// if the weight ranges or prices ever change 
	// you should be able to use this script to add the new prices (delete all rows excluding UK and the 3 Hermes islands first)
	//  :)

	public $uses = array(
		'Country',
		'ShippingZone',
		'ShippingZoneCountry',
		'ShippingCarrierServiceCountry',
		'ShippingCarrierServiceWeightRange',
		'ShippingCarrierServiceWeightRangeCountryPrice',
	);

	public function main() {
		// zones according to royal mail
		$countries = array(
			'europe' => array(
				'countries' => array(
					'ALBANIA',
					'ANDORRA',
					'ARMENIA',
					'AUSTRIA',
					'AZERBAIJAN',
					'BELARUS',
					'BELGIUM',
					'BOSNIA AND HERZEGOVINA',
					'BULGARIA',
					'CROATIA',
					'CYPRUS',
					'CZECH REPUBLIC',
					'DENMARK',
					'ESTONIA',
					'FAROE ISLANDS',
					'FINLAND',
					'FRANCE',
					'GEORGIA',
					'GERMANY',
					'GIBRALTAR',
					'GREECE',
					'GREENLAND',
					'HUNGARY',
					'ICELAND',
					'ITALY',
					'KAZAKHSTAN',
					'KYRGYZSTAN',
					'LATVIA',
					'LIECHTENSTEIN',
					'LITHUANIA',
					'LUXEMBOURG',
					'MACEDONIA',
					'MALTA',
					'MOLDOVA',
					'MONACO',
					'NETHERLANDS',
					'NORWAY',
					'POLAND',
					'PORTUGAL',
					'ROMANIA',
					'RUSSIAN FEDERATION',
					'SAN MARINO',
					'SERBIA AND MONTENEGRO',
					'SLOVAKIA',
					'SLOVENIA',
					'SPAIN',
					'SWEDEN',
					'SWITZERLAND',
					'TAJIKISTAN',
					'TURKEY',
					'TURKMENISTAN',
					'UKRAINE',
					'UZBEKISTAN',
				),
				'weight_ranges' => array(
					0 => array(
						'weight_from' => 0,
						'weight_to' => 100,
						'price' => 2.90
					),
					1 => array(
						'weight_from' => 100.001,
						'weight_to' => 250,
						'price' => 3.35
					),
					2 => array(
						'weight_from' => 250.001,
						'weight_to' => 500,
						'price' => 4.70
					),
					3 => array(
						'weight_from' => 500.001,
						'weight_to' => 750,
						'price' => 6.05
					),
					4 => array(
						'weight_from' => 750.001,
						'weight_to' => 1000,
						'price' => 7.40
					),
					5 => array(
						'weight_from' => 1000.001,
						'weight_to' => 1250,
						'price' => 8.75
					),
					6 => array(
						'weight_from' => 1250.001,
						'weight_to' => 1500,
						'price' => 10.10
					),
					7 => array(
						'weight_from' => 1500.001,
						'weight_to' => 1750,
						'price' => 11.45
					),
					6 => array(
						'weight_from' => 1750.001,
						'weight_to' => 2000,
						'price' => 12.80
					),
					7 => array(
						'weight_from' => 2250.001,
						'weight_to' => 2500,
						'price' => 14.15
					),
					8 => array(
						'weight_from' => 2500.001,
						'weight_to' => 2750,
						'price' => 15.50
					),
					9 => array(
						'weight_from' => 2750.001,
						'weight_to' => 3000,
						'price' => 16.85
					),
					10 => array(
						'weight_from' => 3000.001,
						'weight_to' => 3250,
						'price' => 18.20
					),
					11 => array(
						'weight_from' => 3250.001,
						'weight_to' => 3500,
						'price' => 19.55
					),
					12 => array(
						'weight_from' => 3500.001,
						'weight_to' => 3750,
						'price' => 20.90
					),
					13 => array(
						'weight_from' => 3750.001,
						'weight_to' => 4000,
						'price' => 22.25
					),
					14 => array(
						'weight_from' => 4000.001,
						'weight_to' => 4250,
						'price' => 23.60
					),
					15 => array(
						'weight_from' => 4250.001,
						'weight_to' => 4500,
						'price' => 24.95
					),
					16 => array(
						'weight_from' => 4500.001,
						'weight_to' => 4750,
						'price' => 26.30
					),
					15 => array(
						'weight_from' => 4750.001,
						'weight_to' => 5000,
						'price' => 27.65
					),
				)
			),
			'worldZone2' => array(
				'countries' => array(
					'AUSTRALIA',
					'BRITISH INDIAN OCEAN TERRITORY',
					'CHRISTMAS ISLAND',
					'COCOS (KEELING) ISLANDS',
					'COOK ISLANDS',
					'FIJI',
					'RENCH POLYNESIA',
					'FRENCH SOUTHERN TERRITORIES',
					'KIRIBATI',
					'MACAO',
					'NAURU',
					'NEW CALEDONIA',
					'NEW ZEALAND',
					'NIUE',
					'NORFOLK ISLAND',
					'PAPUA NEW GUINEA',
					'PITCAIRN',
					'SOLOMON ISLANDS',
					'TOKELAU',
					'TONGA',
					'TUVALU',
				),
				'weight_ranges' => array(
					0 => array(
						'weight_from' => 0,
						'weight_to' => 100,
						'price' => 3.65
					),
					1 => array(
						'weight_from' => 100.001,
						'weight_to' => 250,
						'price' => 4.60
					),
					2 => array(
						'weight_from' => 250.001,
						'weight_to' => 500,
						'price' => 7.25
					),
					3 => array(
						'weight_from' => 500.001,
						'weight_to' => 750,
						'price' => 9.90
					),
					4 => array(
						'weight_from' => 750.001,
						'weight_to' => 1000,
						'price' => 12.55
					),
					5 => array(
						'weight_from' => 1000.001,
						'weight_to' => 1250,
						'price' => 15.20
					),
					6 => array(
						'weight_from' => 1250.001,
						'weight_to' => 1500,
						'price' => 17.85
					),
					7 => array(
						'weight_from' => 1500.001,
						'weight_to' => 1750,
						'price' => 20.50
					),
					6 => array(
						'weight_from' => 1750.001,
						'weight_to' => 2000,
						'price' => 23.15
					),
					7 => array(
						'weight_from' => 2250.001,
						'weight_to' => 2500,
						'price' => 25.80
					),
					8 => array(
						'weight_from' => 2500.001,
						'weight_to' => 2750,
						'price' => 28.45
					),
					9 => array(
						'weight_from' => 2750.001,
						'weight_to' => 3000,
						'price' => 31.10
					),
					10 => array(
						'weight_from' => 3000.001,
						'weight_to' => 3250,
						'price' => 33.75
					),
					11 => array(
						'weight_from' => 3250.001,
						'weight_to' => 3500,
						'price' => 36.40
					),
					12 => array(
						'weight_from' => 3500.001,
						'weight_to' => 3750,
						'price' => 39.05
					),
					13 => array(
						'weight_from' => 3750.001,
						'weight_to' => 4000,
						'price' => 41.70
					),
					14 => array(
						'weight_from' => 4000.001,
						'weight_to' => 4250,
						'price' => 44.35
					),
					15 => array(
						'weight_from' => 4250.001,
						'weight_to' => 4500,
						'price' => 47.00
					),
					16 => array(
						'weight_from' => 4500.001,
						'weight_to' => 4750,
						'price' => 49.65
					),
					15 => array(
						'weight_from' => 4750.001,
						'weight_to' => 5000,
						'price' => 52.30
					),
				)
			),
			'worldZone1' => array(
				'countries' => array(
					'AFGHANISTAN',
					'ALGERIA',
					'AMERICAN SAMOA',
					'ANGOLA',
					'ANGUILLA',
					'ANTARCTICA',
					'ANTIGUA AND BARBUDA',
					'ARGENTINA',
					'ARUBA',
					'BAHAMAS',
					'BAHRAIN',
					'BANGLADESH',
					'BARBADOS',
					'BELIZE',
					'BENIN',
					'BERMUDA',
					'BHUTAN',
					'BOLIVIA',
					'BOTSWANA',
					'BOUVET ISLAND',
					'BRAZIL',
					'BRUNEI DARUSSALAM',
					'BURKINA FASO',
					'BURUNDI',
					'CAMBODIA',
					'CAMEROON',
					'CANADA',
					'CAPE VERDE',
					'CAYMAN ISLANDS',
					'CENTRAL AFRICAN REPUBLIC',
					'CHAD',
					'CHILE',
					'CHINA',
					'COLOMBIA',
					'COMOROS',
					'CONGO',
					'CONGO',
					'COSTA RICA',
					'COTE D\'IVOIRE',
					'CUBA',
					'DJIBOUTI',
					'DOMINICA',
					'DOMINICAN REPUBLIC',
					'ECUADOR',
					'EGYPT',
					'EL SALVADOR',
					'EQUATORIAL GUINEA',
					'ERITREA',
					'ETHIOPIA',
					'FALKLAND ISLANDS (MALVINAS)',
					'FRENCH GUIANA',
					'GABON',
					'GAMBIA',
					'GHANA',
					'GRENADA',
					'GUADELOUPE',
					'GUAM',
					'GUATEMALA',
					'GUINEA',
					'GUINEA-BISSAU',
					'GUYANA',
					'HAITI',
					'HEARD ISLAND AND MCDONALD ISLANDS',
					'HOLY SEE (VATICAN CITY STATE)',
					'HONDURAS',
					'HONG KONG',
					'INDIA',
					'INDONESIA',
					'IRAN',
					'IRAQ',
					'IRELAND',
					'ISRAEL',
					'JAMAICA',
					'JAPAN',
					'JORDAN',
					'KENYA',
					'KOREA',
					'KOREA',
					'KUWAIT',
					'LAO PEOPLE\'S DEMOCRATIC REPUBLIC',
					'LEBANON',
					'LESOTHO',
					'LIBERIA',
					'LIBYAN ARAB JAMAHIRIYA',
					'MADAGASCAR',
					'MALAWI',
					'MALAYSIA',
					'MALDIVES',
					'MALI',
					'MARSHALL ISLANDS',
					'MARTINIQUE',
					'MAURITANIA',
					'MAURITIUS',
					'MAYOTTE',
					'MEXICO',
					'MICRONESIA, FEDERATED STATES OF',
					'MONGOLIA',
					'MONTSERRAT',
					'MOROCCO',
					'MOZAMBIQUE',
					'MYANMAR',
					'NAMIBIA',
					'NEPAL',
					'NETHERLANDS ANTILLES',
					'NICARAGUA',
					'NIGER',
					'NIGERIA',
					'NORTHERN MARIANA ISLANDS',
					'OMAN',
					'PAKISTAN',
					'PALAU',
					'PALESTINIAN TERRITORY, OCCUPIED',
					'PANAMA',
					'PARAGUAY',
					'PERU',
					'PHILIPPINES',
					'PUERTO RICO',
					'QATAR',
					'REUNION',
					'RWANDA',
					'SAINT HELENA',
					'SAINT KITTS AND NEVIS',
					'SAINT LUCIA',
					'SAINT PIERRE AND MIQUELON',
					'SAINT VINCENT AND THE GRENADINES',
					'SAMOA',
					'SAO TOME AND PRINCIPE',
					'SAUDI ARABIA',
					'SENEGAL',
					'SEYCHELLES',
					'SIERRA LEONE',
					'SINGAPORE',
					'SOMALIA',
					'SOUTH AFRICA',
					'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS',
					'SRI LANKA',
					'SUDAN',
					'SURINAME',
					'SVALBARD AND JAN MAYEN',
					'SWAZILAND',
					'SYRIAN ARAB REPUBLIC',
					'TAIWAN, PROVINCE OF CHINA',
					'TANZANIA, UNITED REPUBLIC OF',
					'THAILAND',
					'TIMOR-LESTE',
					'TOGO',
					'TRINIDAD AND TOBAGO',
					'TUNISIA',
					'TURKS AND CAICOS ISLANDS',
					'UGANDA',
					'UNITED ARAB EMIRATES',
					'UNITED STATES',
					'UNITED STATES MINOR OUTLYING ISLANDS',
					'URUGUAY',
					'VANUATU',
					'VENEZUELA',
					'VIET NAM',
					'VIRGIN ISLANDS, BRITISH',
					'VIRGIN ISLANDS, U.S.',
					'WALLIS AND FUTUNA',
					'WESTERN SAHARA',
					'YEMEN',
					'ZAMBIA',
					'ZIMBABWE',
				),
				'weight_ranges' => array(
					0 => array(
						'weight_from' => 0,
						'weight_to' => 100,
						'price' => 3.45
					),
					1 => array(
						'weight_from' => 100.001,
						'weight_to' => 250,
						'price' => 4.40
					),
					2 => array(
						'weight_from' => 250.001,
						'weight_to' => 500,
						'price' => 6.85
					),
					3 => array(
						'weight_from' => 500.001,
						'weight_to' => 750,
						'price' => 9.30
					),
					4 => array(
						'weight_from' => 750.001,
						'weight_to' => 1000,
						'price' => 11.75
					),
					5 => array(
						'weight_from' => 1000.001,
						'weight_to' => 1250,
						'price' => 14.20
					),
					6 => array(
						'weight_from' => 1250.001,
						'weight_to' => 1500,
						'price' => 16.65
					),
					7 => array(
						'weight_from' => 1500.001,
						'weight_to' => 1750,
						'price' => 19.10
					),
					6 => array(
						'weight_from' => 1750.001,
						'weight_to' => 2000,
						'price' => 21.55
					),
					7 => array(
						'weight_from' => 2250.001,
						'weight_to' => 2500,
						'price' => 24.00
					),
					8 => array(
						'weight_from' => 2500.001,
						'weight_to' => 2750,
						'price' => 26.45
					),
					9 => array(
						'weight_from' => 2750.001,
						'weight_to' => 3000,
						'price' => 28.90
					),
					10 => array(
						'weight_from' => 3000.001,
						'weight_to' => 3250,
						'price' => 31.35
					),
					11 => array(
						'weight_from' => 3250.001,
						'weight_to' => 3500,
						'price' => 33.80
					),
					12 => array(
						'weight_from' => 3500.001,
						'weight_to' => 3750,
						'price' => 36.25
					),
					13 => array(
						'weight_from' => 3750.001,
						'weight_to' => 4000,
						'price' => 38.70
					),
					14 => array(
						'weight_from' => 4000.001,
						'weight_to' => 4250,
						'price' => 41.15
					),
					15 => array(
						'weight_from' => 4250.001,
						'weight_to' => 4500,
						'price' => 43.60
					),
					16 => array(
						'weight_from' => 4500.001,
						'weight_to' => 4750,
						'price' => 46.05
					),
					15 => array(
						'weight_from' => 4750.001,
						'weight_to' => 5000,
						'price' => 48.50
					),
				)
			)
		);

		foreach ($countries as $zone => $values) {
			foreach ($values['countries'] as $country) {
				// Add the shipping xones to the table
				$country = ucwords(strtolower($country));
				$existingShippingZone = $this->ShippingZone->find('first', array(
					'conditions' => array('ShippingZone.name' => $country)
				));
				if(empty($existingShippingZone)) {
					$saveShippingZone = array(
						'ShippingZone' => array(
							'name' => $country,
						)
					);
					$this->ShippingZone->create();
					if (!$this->ShippingZone->save($saveShippingZone))
					{
						pr($this->ShippingZone->validationErrors);
						$this->out('Error saving ShippingZone.' . $country);
					}	
					$this->ShippingZoneID = $this->ShippingZone->getInsertID();	
				} else {
					$this->ShippingZoneID = $existingShippingZone['ShippingZone']['id'];
				}
				// get the country id for the relevant shipping zone
				$country = $this->Country->find('first', array(
					'conditions' => array('Country.name' => strtoupper($country))
				));
				if(!empty($country)) {
					$countryID = $country['Country']['id'];
					$existingShippingZoneCountry = $this->ShippingZoneCountry->find('first', array(
						'conditions' => array(
							'ShippingZoneCountry.shipping_zone_id' => $this->ShippingZoneID,
							'ShippingZoneCountry.country_id' => $countryID,
						)
					));
					if(empty($existingShippingZoneCountry)) {
						$saveShippingZoneCountry = array(
							'ShippingZoneCountry' => array(
								'shipping_zone_id' => $this->ShippingZoneID,
								'country_id' => $countryID,
							)
						);
						// assign shipping zones to countries
						$this->ShippingZoneCountry->create();
						if (!$this->ShippingZoneCountry->save($saveShippingZoneCountry))
						{
							pr($this->ShippingZoneCountry->validationErrors);
							$this->out('Error saving ShippingZoneCountry.' . $country);
						}	
						$this->ShippingZoneCountryID = $this->ShippingZoneCountry->getInsertID();
					}
				}
				// add shipping carriers for each shipping zone
				// in this case its royal mail internation shipping - hardcoded at 3
				$existingShippingCarrierServiceCountry = $this->ShippingCarrierServiceCountry->find('first', array(
					'conditions' => array(
						'ShippingCarrierServiceCountry.shipping_carrier_service_id' => 3,
						'ShippingCarrierServiceCountry.shipping_zone_id' => $this->ShippingZoneID,
					)
				));
				if(empty($existingShippingCarrierServiceCountry)) {
					$saveShippingCarrierServiceCountry = array(
						'ShippingCarrierServiceCountry' => array(
							'shipping_carrier_service_id' => 3,
							'shipping_zone_id' => $this->ShippingZoneID,
						)
					);
					$this->ShippingCarrierServiceCountry->create();
					if (!$this->ShippingCarrierServiceCountry->save($saveShippingCarrierServiceCountry))
					{
						pr($this->ShippingCarrierServiceCountry->validationErrors);
						$this->out('Error saving ShippingCarrierServiceCountry.' . $country);
					}	
					$this->ShippingCarrierServiceCountryID = $this->ShippingCarrierServiceCountry->getInsertID();
				}
				// add all the different weight ranges for the shipping carrier 
				// royal mail internation shipping - hardcoded at 3
				if(!empty($values['weight_ranges'])) {

					foreach ($values['weight_ranges'] as $weightRange) {
						$existingShippingCarrierServiceWeightRange = $this->ShippingCarrierServiceWeightRange->find('first', array(
							'conditions' => array(
								'ShippingCarrierServiceWeightRange.shipping_carrier_service_id' => 3,
								'ShippingCarrierServiceWeightRange.from' => $weightRange['weight_from'],
								'ShippingCarrierServiceWeightRange.to' => $weightRange['weight_to'],
							)
						));
						if(empty($existingShippingCarrierServiceWeightRange)) {
							$saveShippingCarrierServiceWeightRange = array(
								'ShippingCarrierServiceWeightRange' => array(
									'shipping_carrier_service_id' => 3,
									'from' => $weightRange['weight_from'],
									'to' => $weightRange['weight_to'],
								)
							);
							$this->ShippingCarrierServiceWeightRange->create();
							if (!$this->ShippingCarrierServiceWeightRange->save($saveShippingCarrierServiceWeightRange))
							{
								pr($this->ShippingCarrierServiceWeightRange->validationErrors);
								$this->out('Error saving ShippingCarrierServiceWeightRange.' . $country);
							}	
							$this->ShippingCarrierServiceWeightRangeID = $this->ShippingCarrierServiceWeightRange->getInsertID();
						} else {
							$this->ShippingCarrierServiceWeightRangeID = $existingShippingCarrierServiceWeightRange['ShippingCarrierServiceWeightRange']['id'];
						}
						// Now we have the ranges in we can assign a price to each
						$existingShippingCarrierServiceWeightRangeCountryPrice = $this->ShippingCarrierServiceWeightRangeCountryPrice->find('first', array(
							'conditions' => array(
								'ShippingCarrierServiceWeightRangeCountryPrice.shipping_zone_id' => $this->ShippingZoneID,
								'ShippingCarrierServiceWeightRangeCountryPrice.shipping_carrier_service_weight_range_id' => $this->ShippingCarrierServiceWeightRangeID,
								'ShippingCarrierServiceWeightRangeCountryPrice.price' => $weightRange['price'],
							)
						));
						if(empty($existingShippingCarrierServiceWeightRangeCountryPrice)) {
							$saveShippingCarrierServiceWeightRangeCountryPrice = array(
								'ShippingCarrierServiceWeightRangeCountryPrice' => array(
									'shipping_zone_id' => $this->ShippingZoneID,
									'shipping_carrier_service_weight_range_id' => $this->ShippingCarrierServiceWeightRangeID,
									'price' => $weightRange['price'],
									'currency_id' => 1,
								)
							);
							$this->ShippingCarrierServiceWeightRangeCountryPrice->create();
							if (!$this->ShippingCarrierServiceWeightRangeCountryPrice->save($saveShippingCarrierServiceWeightRangeCountryPrice))
							{
								pr($this->ShippingCarrierServiceWeightRangeCountryPrice->validationErrors);
								$this->out('Error saving ShippingCarrierServiceWeightRangeCountryPrice.' . $country);
							}	
							$this->ShippingCarrierServiceWeightRangeCountryPriceID = $this->ShippingCarrierServiceWeightRangeCountryPrice->getInsertID();
						} else {
							$this->ShippingCarrierServiceWeightRangeCountryPriceID = $existingShippingCarrierServiceWeightRangeCountryPrice['ShippingCarrierServiceWeightRangeCountryPrice']['id'];
						}
					}
				}
			}
		}
	}
}
// simples!