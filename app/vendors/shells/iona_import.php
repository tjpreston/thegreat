<?php

class IonaImportShell extends Shell {
	
	public $importFiles = array();

	public $uses = array('Product');

	public $validationErrors = array();

	public $currentRow = 0;

	public function __construct(&$dispatch){
		parent::__construct(&$dispatch);

		$this->importFiles['products'] = TMP . 'import' . DS . 'products.csv';

		$this->importFiles['related'] = TMP . 'import' . DS . 'product_related_bridge.csv';

		$this->importFiles['image_bridge'] = TMP . 'import' . DS . 'image_bridge.csv';

		$this->importFiles['attributes'] = TMP . 'import' . DS . 'attributes.csv';
		$this->importFiles['attribute_items'] = TMP . 'import' . DS . 'attribute_items.csv';

		$this->imageDataDir = DS . 'home' . DS . 'httpd' . DS . 'html' . DS . 'M' . DS . 'michelherbelin' . DS . 'htdocs' . DS . 'data' . DS . 'images' . DS;
	}

	public function main(){
		$this->products();
		$this->related();
		$this->images();
		$this->attributes();
	}

	// Import products
	public function products(){
		$logFile = TMP . 'validationErrors.txt';
		if(file_exists($logFile)){
			unlink($logFile);
		}

		$this->currentRow = 0;

		$fh = fopen($this->importFiles['products'], 'r');
		if(!$fh){
			$this->err('Could not open product import file'); exit;
		}

		$fields = fgetcsv($fh);

		$languageID = $currencyID = 1;

		while(($row = fgetcsv($fh)) !== false){
			$this->currentRow++;

			$row = array_combine($fields, $row);

			$validateFields = array(
				'code',
				'title',
				'desc_short',
				'desc_long',
				'price',
			);
			$error = false;
			foreach($validateFields as $field){
				if(empty($row[$field])){
					$this->validationErrors[] = array(
						'row' => $this->currentRow,
						'validationErrors' => 'Missing field: ' . $field,
					);
					$error = true;
				}
			}

			if($error){
				continue;
			}

			// Save Product
			$product = array('Product' => array(
				'id' => $row['product_id'],
				'sku' => $row['code'],
				'active' => $this->boolean($row['active']),
				'featured' => $this->boolean($row['most_popular']),
				'stock_base_qty' => $row['stock'],
				'attribute_set_id' => 1,
			));

			if(!$this->saveModel('Product', $product)){
				continue;
			}

			$productID = $this->Product->id;

			// Save ProductName
			$productName = array('ProductName' => array(
				'product_id' => $productID,
				'language_id' => $languageID,
				'name' => $row['title'],
				'sub_name' => '',
			));

			if(!$this->saveModel('ProductName', $productName)){
				continue;
			}

			// Save ProductDescription
			$productDescription = array('ProductDescription' => array(
				'product_id' => $productID,
				'language_id' => $languageID,
				'short_description' => trim(strip_tags($row['desc_short'])),
				'long_description' => '<p>' . nl2br(trim(strip_tags($row['desc_long']))) . '</p>',
				'spec_as_key_value' => 1,
				'specification' => $row['information'],
				'keywords' => $row['keywords'],
			));

			if(!$this->saveModel('ProductDescription', $productDescription)){
				continue;
			}

			// Save ProductMeta
			$productMeta = array('ProductMeta' => array(
				'product_id' => $productID,
				'language_id' => $languageID,
				'page_title' => $row['title'],
				'keywords' => '',
				'description' => '',
				'url' => Inflector::slug($row['url'], '-'),
			));

			if(!$this->saveModel('ProductMeta', $productMeta)){
				continue;
			}

			// Save ProductPrice
			$productPrice = array('ProductPrice' => array(
				'product_id' => $productID,
				'currency_id' => $currencyID,
				'base_price' => $row['price'],
				'base_rrp' => (!empty($row['price_rrp']) ? $row['price_rrp'] : null),
				'special_price' => (!empty($row['price_special']) ? $row['price_special'] : null),
			));

			if(!$this->saveModel('ProductPrice', $productPrice)){
				continue;
			}

		}

		fclose($fh);

		if(!empty($this->validationErrors)){
			$this->out('There were validation errors. See text file.');
			file_put_contents($logFile, print_r($this->validationErrors, true));
			$this->validationErrors = array();
		}

	}

	// Import related products
	public function related(){
		$logFile = TMP . 'relatedValidationErrors.txt';
		if(file_exists($logFile)){
			unlink($logFile);
		}

		$this->currentRow = 0;
		
		$fh = fopen($this->importFiles['related'], 'r');
		if(!$fh){
			$this->err('Could not open related import file'); exit;
		}

		$fields = fgetcsv($fh);

		$languageID = $currencyID = 1;

		while(($row = fgetcsv($fh)) !== false){
			$this->currentRow++;

			$row = array_combine($fields, $row);

			$productID = $this->Product->id;

			// Check that referenced product IDs exist before saving them (otherwise we get FK constraint errors)
			$this->Product->id = $row['product_id'];
			if(!$this->Product->exists()){
				continue;
			}

			$this->Product->id = $row['related_id'];
			if(!$this->Product->exists()){
				continue;
			}

			$this->Product->id = $productID;

			// Save RelatedProduct
			$relatedProduct = array('RelatedProduct' => array(
				'from_product_id' => $row['product_id'],
				'to_product_id' => $row['related_id'],
			));

			if(!$this->saveModel('RelatedProduct', $relatedProduct)){
				continue;
			}

		}

		fclose($fh);

		if(!empty($this->validationErrors)){
			$this->out('There were validation errors. See text file.');
			file_put_contents($logFile, print_r($this->validationErrors, true));
			$this->validationErrors = array();
		}

	}

	// Import product images
	public function images(){
		$logFile = TMP . 'imageValidationErrors.txt';
		if(file_exists($logFile)){
			unlink($logFile);
		}

		$this->currentRow = 0;
		
		$fh = fopen($this->importFiles['image_bridge'], 'r');
		if(!$fh){
			$this->err('Could not open image bridge import file'); exit;
		}

		/*$fh_br = fopen($this->importFiles['image_bridge'], 'r');
		if(!$fh_br){
			$this->err('Could not open image bridge import file'); exit;
		}*/

		App::import('Lib', 'EsperImage');

		$fields = fgetcsv($fh);

		$languageID = $currencyID = 1;

		while(($row = fgetcsv($fh)) !== false){
			$this->currentRow++;

			$row = array_combine($fields, $row);

			$this->Product->id = $row['data_id'];
			$record = $this->Product->read();

			if(empty($record)){
				$this->validationErrors[] = array(
					'row' => $this->currentRow,
					'validationErrors' => 'Product ID: ' . $row['data_id'] . ' does not exist',
				);
				continue;
			}

			$path = $this->imageDataDir . $row['image_id'] . DS . 'large.jpg';

			if(!file_exists($path)){
				$this->validationErrors[] = array(
					'row' => $this->currentRow,
					'validationErrors' => 'Could not find "large.jpg" image for image ID: ' . $row['image_id'],
				);
				continue;
			}

			$productImage = array('ProductImage' => array(
				'product_id' => $row['data_id'],
				'sort_order' => $row['sort'],
			));

			$this->saveModel('ProductImage', $productImage);

			$pathInfo = pathinfo($path);
			$filenameNoExt = Inflector::slug(strtolower($record['Product']['sku'])) . '-' . $this->ProductImage->id;
			$filename = $filenameNoExt . '.' .  $pathInfo['extension'];

			$options = array( 'saveFilename' => $filename );
			$esperImage = new EsperImage($path, $options);
			$esperImage->resizeAll();

			$this->ProductImage->save(array('ProductImage' => array(
				'filename'  => $filenameNoExt,
				'ext'	 	=> $pathInfo['extension']
			)));

			$this->ProductImage->id = null;

		}

		fclose($fh);

		if(!empty($this->validationErrors)){
			$this->out('There were validation errors. See text file.');
			file_put_contents($logFile, print_r($this->validationErrors, true));
			$this->validationErrors = array();
		}

	}

	public function attributes(){
		$logFile = TMP . 'attributeValidationErrors.txt';
		if(file_exists($logFile)){
			unlink($logFile);
		}

		$this->currentRow = 0;
		
		$fh = fopen($this->importFiles['attributes'], 'r');
		if(!$fh){
			$this->err('Could not open attributes import file'); exit;
		}

		$fields = fgetcsv($fh);

		$languageID = $currencyID = 1;

		while(($row = fgetcsv($fh)) !== false){
			$this->currentRow++;

			$row = array_combine($fields, $row);

			$attribute = array(
				'id' => $row['attribute_id'],
			);

			if(!$this->saveModel('Attribute', $attribute)){
				continue;
			}

			$name = array(
				'language_id' => $languageID,
				'attribute_id' => $this->Attribute->id,
				'name' => $row['name'],
				'display_name' => $row['name'],
				'url' => '',
			);

			if(!$this->saveModel('AttributeName', $name, array('validate' => false))){
				continue;
			}

		}

		fclose($fh);

		$fh = fopen($this->importFiles['attribute_items'], 'r');
		if(!$fh){
			$this->err('Could not open attribute_items import file'); exit;
		}

		$fields = fgetcsv($fh);

		$attributeValues = array();

		while(($row = fgetcsv($fh)) !== false){
			$this->currentRow++;

			$row = array_combine($fields, $row);

			$attrID = $row['attribute_id'];
			$value = trim(ucwords($row['value']));

			if(!isset($attributeValues[$attrID])){
				$attributeValues[$attrID] = array();
			}

			if(!isset($attributeValues[$attrID][$value])){
				$attributeValues[$attrID][$value] = array();
			}

			$attributeValues[$attrID][$value][] = $row['product_id'];
		}

		fclose($fh);

		foreach($attributeValues as $attrID => $attrValues){
			ksort($attrValues);
			
			$i = 0;
			foreach($attrValues as $value => $products){
				$attributeValue = array(
					'attribute_id' => $attrID,
					'sort' => $i,
				);

				if(!$this->saveModel('AttributeValue', $attributeValue)){
					continue;
				}

				$i++;

				$attributeValueName = array(
					'language_id' => $languageID,
					'attribute_value_id' => $this->AttributeValue->id,
					'name' => $value,
				);

				if(!$this->saveModel('AttributeValueName', $attributeValueName)){
					continue;
				}

				foreach($products as $productID){
					$this->Product->id = $productID;
					if(!$this->Product->exists()){
						$this->validationErrors[] = array(
							'row' => $this->currentRow,
							'model' => 'AttributeValuesProduct',
							'validationErrors' => 'Product ID: ' . $productID . ' does not exist. Cannot add it to attribute ID: ' . $this->AttributeValue->id,
						);

						continue;
					}

					$attributeProduct = array(
						'product_id' => $productID,
						'attribute_value_id' => $this->AttributeValue->id,
					);

					if(!$this->saveModel('AttributeValuesProduct', $attributeProduct)){
						continue;
					}
				}


			}
		}

		if(!empty($this->validationErrors)){
			$this->out('There were validation errors. See text file.');
			file_put_contents($logFile, print_r($this->validationErrors, true));
			$this->validationErrors = array();
		}
	}

	private function saveModel($modelName, $record, $saveOptions = array()){
		if(empty($this->{$modelName})){
			$this->{$modelName} = ClassRegistry::init($modelName);
		}

		$this->{$modelName}->create();
		if(!$this->{$modelName}->save($record, $saveOptions)){
			$this->validationErrors[] = array(
				'row' => $this->currentRow,
				'model' => $this->{$modelName}->name,
				'validationErrors' => $this->{$modelName}->validationErrors,
			);

			return false;
		} else {
			return true;
		}
	}

	private function boolean($input){
		if(!is_string($input)) return $input;

		if($input == 'yes'){
			return 1;
		} else {
			return 0;
		}
	}

	public function truncate(){
		$sql = "TRUNCATE product_categories;
		TRUNCATE product_cross_sells;
		TRUNCATE product_descriptions;
		TRUNCATE product_documents;
		TRUNCATE product_flags;
		TRUNCATE product_flags_products;
		TRUNCATE product_grouped_products;
		TRUNCATE product_images;
		TRUNCATE product_metas;
		TRUNCATE product_names;
		TRUNCATE product_option_names;
		TRUNCATE product_option_stock;
		TRUNCATE product_option_stock_discounts;
		TRUNCATE product_option_stock_images;
		TRUNCATE product_option_stock_prices;
		TRUNCATE product_option_values;
		TRUNCATE product_options;
		TRUNCATE product_price_discounts;
		TRUNCATE product_prices;
		TRUNCATE product_related_products;
		TRUNCATE products;";

		$sql = explode("\n", $sql);

		foreach($sql as $query){
			$this->Product->query($query);
		}


		$imageDirs = array(
			WWW_ROOT . Configure::read('Images.product_original_path'),
			WWW_ROOT . Configure::read('Images.product_large_path'),
			WWW_ROOT . Configure::read('Images.product_medium_path'),
			WWW_ROOT . Configure::read('Images.product_thumb_path'),
			WWW_ROOT . Configure::read('Images.product_tiny_path'),
			WWW_ROOT . Configure::read('Images.var_large_path'),
			WWW_ROOT . Configure::read('Images.var_medium_path'),
			WWW_ROOT . Configure::read('Images.var_thumb_path'),
			WWW_ROOT . Configure::read('Images.var_tiny_path'),
		);

		foreach($imageDirs as $dirPath){
			$dir = scandir($dirPath);
			foreach($dir as $file){
				if(in_array($file, array('.', '..'))){
					continue;
				}

				unlink($dirPath . $file);
			}
		}

	}

}