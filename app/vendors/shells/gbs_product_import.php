<?php

class GbsProductImportShell extends Shell {

	public $uses = array(
		'Attribute',
		'AttributeName',
		'AttributeValue',
		'AttributeValueName',
		'AttributeValuesProduct',
		'AttributeSet',
		'AttributeSetName',
		'AttributeSetsAttribute',
		'Category',
		'CategoryName',
		'CategoryDescription',
		'CustomOption',
		'CustomOptionName',
		'CustomOptionValue',
		'CustomOptionValueName',
		'Language',
		'Manufacturer',
		'Product',
		'ProductCategory',
		'ProductDescription',
		'ProductMeta',
		'ProductName',
		'ProductOption',
		'ProductOptionName',
		'ProductOptionStock',
		'ProductOptionStockPrice',
		'ProductOptionValue',
		'ProductPrice',
	);

	public function main() {
		// keeps tabs on errors
		$this->erroredProducts = array();
		$this->erroredVarPrices = array();
		$erroredManufacturers = array();
		$this->erroredAttributes = array();
		$this->erroredCategories = array();
		$this->erroredCustomOptions = array();
		$this->erroredSKUs = array();
		
		// spreadsheet row count 
		$this->row = 0;

		// switch to make numerous imports easier, manu, cats and attr only need to go in once, then the prods.
		$runManufacturers = false;
		$runCategories = false;
		// also runs custom options (AKA vars)
		$runAttributes = false;
		$runProducts = true;

		// set up the basics
		$this->languageID = 1;
		$this->currencyID = 1;

		// add our saved prods to an arrays do we can check whats what
		$productCodesSaved = array();
		$handle = fopen(TMP . 'gbs_import.csv', "r");
		while (($data = fgetcsv($handle)) !== FALSE) {
			$this->row++;
			// set up easier var names, easier to read import back, allows flexibility if col order changes
			$this->sku = $data[0];
			$varSku = $data[1];
			$tillSku = $data[2];
			$this->name = $data[3];
			$longDescription = $data[4];
			$this->colourStyle = $data[5];
			$this->filterColour = $data[6];
			$this->dimensions = $data[7];
			$searchKeywords = $data[8];
			$costPrice = $data[9];
			$price = $data[10];
			$stock = $data[11];
			$vat = $data[12];
			$matches = $data[13];
			$inactive = $data[14];
			$weight = $data[15];
			$category = $data[16];
			$this->jewelleryAttributes = $data[17];
			$subCategory = $data[18];
			$category2 = $data[19];
			$subCategory2 = $data[20];
			$manufacturer = $data[21];
			$giftFinderSubCat = $data[22];

			$giftFinderCatName = 'Gift Finder';
			$giftFinderCat = 'giftFinderCatName';

			$this->CustOptionName = 'Colour';
			$this->StockQTY = 10;

			$this->attributes = array(
				1 => array(
					'attributeSetName' => 'Misc',
					'attributeName' => 'Colour',
					'column' => 'filterColour',
					'customOptionColumn' => 'colourStyle'
				),
				2 => array(
					'attributeSetName' => 'Jewellery',
					'attributeName' => 'Jewellery Type',
					'column' => 'jewelleryAttributes'
				)
			);
			$categoryColumns = array(
				'subCategory' => 'category',
				'subCategory2' => 'category2',
			);
			// Ignore column headings
			if ($this->row == 1) {
				continue;
			}

			if(empty($this->sku)) {
				$this->erroredProducts[$this->row] = 'No SKU Name:' . $this->name;
				continue;
			}

			if(empty($this->name)) {
				$this->erroredProducts[$this->row] = 'No Name SKU:' . $this->sku;
				continue;
			}
			if($runManufacturers) {
				// save manus
				$existingManufacturer = $this->Manufacturer->find('first', array(
					'conditions' => array('Manufacturer.name' => $manufacturer)
				));
				if(empty($existingManufacturer) && !empty($manufacturer)) {
					$saveManufacturer = array(
						'Manufacturer' => array(
							'name' => trim($manufacturer),
						)
					);
					$this->Manufacturer->create();
					if (!$this->Manufacturer->save($saveManufacturer))
					{
						pr($this->Manufacturer->validationErrors);
						$this->out('Error saving Manufacturer.' . $this->row);
						$erroredManufacturers[$this->row] = $manufacturer;
					}				
				}
			}
			if($runAttributes) {
				// save attrs
				foreach ($this->attributes as $key => $attributeArray) {
					$this->saveAttributes($attributeArray);
				}

			}
			if($runCategories) {
				// save categories
				Cache::delete('categories');
				foreach ($categoryColumns as $subCategoryColumn => $categoryColumn) {
					if(!empty(${$categoryColumn})) {
						$this->saveCategory(${$categoryColumn}, ${$subCategoryColumn});
					}
				}
				// run gift finder cats differently as they dont have the same cat > sub cat columns
				if(!empty($giftFinderSubCat)) {

					$existingGiftFinderCat = $this->CategoryName->find('first', array(
						'conditions' => array('CategoryName.name' => $giftFinderCatName)
					));
					if(empty($existingGiftFinderCat)) {
						$saveCategory = array(
						'Category' => array(
								'display_on_main_nav' => 0,
								'display_as_landing' => 1,
								'parent_id' => 0,
								'active' => 1,
							)
						);
						$this->Category->create();
						if (!$this->Category->save($saveCategory))
						{
							pr($this->Category->validationErrors);
							$this->out('Error saving Gift Finder Category.' . $this->row);
							$this->erroredCategories[$this->row] = $giftFinderSubCat;
						}				
						
						$this->categoryID = $this->Category->getInsertID();
						
						$saveCategoryName = array(
							'CategoryName' => array(
								'category_id' => $this->categoryID,
								'language_id' => $this->languageID,
								'name' => $giftFinderCatName
							)
						);
						$this->CategoryName->create();
						if (!$this->CategoryName->save($saveCategoryName))
						{
							pr($this->CategoryName->validationErrors);
							$this->out('Error saving Gift Finder Category Name.' . $this->row);
							$this->erroredCategories[$this->row] = $giftFinderSubCat;
						}	
						$parentID = $this->categoryID;	
						$saveCategoryDescription = array(
							'CategoryDescription' => array(
								'category_id' => $this->categoryID,
								'language_id' => $this->languageID,
							)
						);
						$this->CategoryDescription->create();
						if (!$this->CategoryDescription->save($saveCategoryDescription))
						{
							pr($this->CategoryDescription->validationErrors);
							$this->out('Error saving Category Description.' . $this->row);
							$this->erroredCategories[$this->row] = $category;
						}						
					} else {
						$parentID = $existingGiftFinderCat['CategoryName']['category_id'];
					}
					$existingGiftFinderSubCat = $this->CategoryName->find('first', array(
						'conditions' => array(
							'CategoryName.name' => $giftFinderSubCat,
							'Category.parent_id' => $parentID
						)
					));
					if(empty($existingGiftFinderSubCat)) {
						$saveCategory = array(
						'Category' => array(
								'parent_id' => $parentID,
								'active' => 1,
							)
						);
						$this->Category->create();
						if (!$this->Category->save($saveCategory))
						{
							pr($this->Category->validationErrors);
							$this->out('Error saving Gift Finder Category.' . $this->row);
							$this->erroredCategories[$this->row] = $giftFinderSubCat;
						}				
						
						$this->categoryID = $this->Category->getInsertID();

						$saveSubCategoryName = array(
							'CategoryName' => array(
								'category_id' => $this->categoryID,
								'language_id' => $this->languageID,
								'name' => trim($giftFinderSubCat)
							)
						);
						$this->CategoryName->create();
						if (!$this->CategoryName->save($saveSubCategoryName))
						{
							pr($this->CategoryName->validationErrors);
							$this->out('Error saving Gift Finder Category Name.' . $this->row);
							$this->erroredCategories[$this->row] = $giftFinderSubCat;
						}	
						$saveCategoryDescription = array(
							'CategoryDescription' => array(
								'category_id' => $this->categoryID,
								'language_id' => $this->languageID,
							)
						);
						$this->CategoryDescription->create();
						if (!$this->CategoryDescription->save($saveCategoryDescription))
						{
							pr($this->CategoryDescription->validationErrors);
							$this->out('Error saving Category Description.' . $this->row);
							$this->erroredCategories[$this->row] = $category;
						}									
					}
				}
			}
			$this->productID = NULL;
			if ($runProducts) {
				if (!array_key_exists($this->sku, $productCodesSaved)) {
					// save Product
					$productManufacturer = '';
					$productManufacturer = $this->Manufacturer->find('first', array(
						'conditions' => array('Manufacturer.name' => $manufacturer),
						'fields' => array('Manufacturer.id'),
					));
					foreach ($this->attributes as $attributeOption) {
						if(!empty($this->{$attributeOption['column']})) {
							$attributeNameForProduct = $this->AttributeName->find('first', array(
								'conditions' => array('AttributeName.name' => $attributeOption['attributeName'])
							));
							$attributeSetName = $this->AttributeSetsAttribute->find('first', array(
								'conditions' => array('AttributeSetsAttribute.attribute_id' => $attributeNameForProduct['Attribute']['id'])
							));
							$attributeSetID = $attributeSetName['AttributeSet']['id'];

						}
					}
					$saveProduct = array('Product' => array(
						'type' => 'simple',
						'sku' => $this->sku,
						'till_sku' => $tillSku,
						'manufacturer_id' => !empty($productManufacturer) ? $productManufacturer['Manufacturer']['id'] : 0,
						'weight' => $weight / 1000,
						'active' => empty($inactive) ? 1 : 0,
						'visibility' => 'catalogsearch',
						'deliverable' => 1,
						'stock_base_qty' => empty($stock) ? $this->StockQTY : $stock,
						'in_stock' => 1,
						'stock_in_stock' => 1,
						'attribute_set_id' => !empty($attributeSetID) ? $attributeSetID : 0,
						'courier_shipping_only' => !empty($matches) ? 1 : 0
					));
					$this->Product->create();
					
					if (!$this->Product->save($saveProduct)) {
						pr($this->Product->validationErrors);
						$this->out('Error saving Product.' . $this->row);
						$this->erroredProducts[$this->row] = 'Error saving Product. ' . $this->name;
						continue;
					}
					$this->productID = $this->Product->getInsertID();
					$productCodesSaved[$this->sku] = $this->productID;

					if(!is_null($this->productID)) {

						// save ProductName
						$saveProductName = array('ProductName' => array(
							'product_id' => $this->productID,
							'language_id' => $this->languageID,
							'name' => $this->name,
						));
						$this->ProductName->create();

						if (!$this->ProductName->save($saveProductName)) {
							pr($this->ProductName->validationErrors);
							$this->out('Error saving Product Name.' . $this->row);
							$this->erroredProducts[$this->row] = 'Error saving Product Name. ' . $this->name;
						}

						// save ProductDescription
						$saveProductDescription = array('ProductDescription' => array(
							'product_id' => $this->productID,
							'language_id' => $this->languageID,
							'long_description' => $this->formatDescription($longDescription, $vat),
							'keywords' => $searchKeywords,
						));
						$this->ProductDescription->create();
						
						if (!$this->ProductDescription->save($saveProductDescription)) {
							pr($this->ProductDescription->validationErrors);
							$this->out('Error saving Product Description.' . $this->row);
							$this->erroredProducts[$this->row] = 'Error saving Product Description. ' . $this->name;
						}

						// save ProductPrice
						// $basePrice used to check if var price changes later on
						$basePrice = $price;
						$saveProductPrice = array('ProductPrice' => array(
							'product_id' => $this->productID,
							'currency_id' => $this->currencyID,
							'base_price' => $price,
							'on_special' => 0,
							'active_price' => $price,
							'cost_price' => $costPrice,
						));
						$this->ProductPrice->create();
						if (!$this->ProductPrice->save($saveProductPrice)) {
							pr($this->ProductPrice->validationErrors);
							$this->out('Error saving Product Price.' . $this->row);
							$this->erroredProducts[$this->row] = 'Error saving Product Price. ' . $this->name;
						}
						// save ProductCategory
						$categoryColumns['giftFinderSubCat'] = 'giftFinderCatName';
						foreach ($categoryColumns as $subCat => $cat) {
							$productCategory = $this->CategoryName->find('first', array(
								'conditions' => array(
									'CategoryName.name' => ${$cat},
									'Category.parent_id' => 0
								)
							));
							if(!empty($productCategory)) {
								// dont assign products to the gift finder cat as the page will crash due to too many prods
								// just assign to the sub cat
								if(${$cat} != 'Gift Finder'){

									$saveProductCategory = array('ProductCategory' => array(
										'product_id' => $this->productID,
										'category_id' => $productCategory['Category']['id'],
										'primary' => $cat == 'category' ? 1 : 0,
									));
									$this->ProductCategory->create();
									if (!$this->ProductCategory->save($saveProductCategory)) {
										pr($this->ProductCategory->validationErrors);
										// debug($productCategory);
										$this->out('Error saving Product Category. cat: ' . ${$cat} . '.row: ' . $this->row);
										$this->erroredProducts[$this->row] = 'Error saving Product Category. ' . $this->name;
									}
								}
								if(!empty(${$subCat}))
								$productSubCategory = $this->CategoryName->find('first', array(
									'conditions' => array(
										'CategoryName.name' => ${$subCat},
										'Category.parent_id' => $productCategory['Category']['id']
									)
								));
								if(!empty($productSubCategory)) {
									$saveProductSubCategory = array('ProductCategory' => array(
										'product_id' => $this->productID,
										'category_id' => $productSubCategory['Category']['id'],
										'primary' => 0,
									));
									$this->ProductCategory->create();
									if (!$this->ProductCategory->save($saveProductSubCategory)) {
										pr($this->ProductCategory->validationErrors);
										// debug($productCategory);
										$this->out('Error saving Product Category. cat: ' . ${$subCat} . '.row: ' . $this->row);
										$this->erroredProducts[$this->row] = 'Error saving Product Category. ' . $this->name;
									}

								}
							}
							// die;
						}

						// save ProductMeta
						$saveProductMeta = array('ProductMeta' => array(
							'product_id' => $this->productID,
							'language_id' => $this->languageID,
							'url' => Inflector::slug($this->name),
						));
						$this->ProductMeta->create();

						if (!$this->ProductMeta->save($saveProductMeta)) {
							pr($this->ProductMeta->validationErrors);
							$this->out('Error saving Product Meta.' . $this->row);
							$this->erroredProducts[$this->row] = 'Error saving Product Meta. ' . $this->name;
						}
					}
				} else {
					$this->productID = $productCodesSaved[$this->sku];
				}
				// Now we have the product Id we can add the variations - if needed
				if(!empty($varSku) && !empty($this->colourStyle)) {
					if($this->sku . $varSku != $tillSku) {
						$this->erroredSKUs[$this->row] = 'TillSKU and Variation Sku inconsistent. SKU:  ' . $this->sku . ' VarSKU: ' . $varSku . ' TillSKU: ' . $tillSku;
					}
					// save ProductOption
					
					// save ProductOptionName
					$existingProductOptionName = $this->ProductOptionName->find('first', array(
						'conditions' => array('ProductOptionName.name' => $this->CustOptionName)
					));
					$customOptionName = $this->CustomOptionName->find('first', array(
						'conditions' => array('CustomOptionName.name' => $this->CustOptionName)
					));
					$existingProductOption = $this->ProductOption->find('first', array(
						'conditions' => array(
							'ProductOption.product_id' => $this->productID,
							'ProductOption.custom_option_id' => $customOptionName['CustomOptionName']['custom_option_id']
						)
					));
					if(empty($existingProductOption)) {
						$saveProductOptions = array('ProductOption' => array(
							'product_id' => $this->productID,
							'custom_option_id' => $customOptionName['CustomOptionName']['custom_option_id'],
						));
						$this->ProductOption->create();

						if (!$this->ProductOption->save($saveProductOptions)) {
							pr($this->ProductOption->validationErrors);
							$this->out('Error saving Product Option.' . $this->row);
							$this->erroredProducts[$this->row] = 'Error saving Product Option. ' . $this->name;
						}
						$this->productOptionID = $this->ProductOption->getInsertID();

						$saveProductOptionNames = array('ProductOptionName' => array(
							'product_option_id' => $this->productOptionID,
							'language_id' => $this->languageID,
							'name' => $this->CustOptionName,
						));

						$this->ProductOptionName->create();

						if (!$this->ProductOptionName->save($saveProductOptionNames, false)) {
							pr($this->ProductOptionName->validationErrors);
							$this->out('Error saving Product Option Name.' . $this->row);
							$this->erroredProducts[$this->row] = 'Error saving Product Option Name. ' . $this->name;
						}
						$this->productOptionNameID = $this->ProductOptionName->getInsertID();
					} else {
						$this->productOptionID = $existingProductOption['ProductOption']['id'];
					}

					// save ProductOptionValue


					$customValueName = $this->CustomOptionValueName->find('first', array(
						'conditions' => array('CustomOptionValueName.name' => trim($this->colourStyle))
					));
					$existingProductOptionValue = $this->ProductOptionValue->find('first', array(
						'conditions' => array(
							'ProductOptionValue.product_option_id' => $this->productOptionID,
							'custom_option_value_id' => $customValueName['CustomOptionValueName']['custom_option_value_id']
						)
					));
					if(empty($existingProductOptionValue)) {
						$saveProductOptionValues = array('ProductOptionValue' => array(
							'product_option_id' => $this->productOptionID,
							'custom_option_value_id' => $customValueName['CustomOptionValueName']['custom_option_value_id'],
						));
						$this->ProductOptionValue->create();

						if (!$this->ProductOptionValue->save($saveProductOptionValues, false)) {
							pr($this->ProductOptionValue->validationErrors);
							$this->out('Error saving Product Option Value.' . $this->row);
							$this->erroredProducts[$this->row] = 'Error saving Product Option Value. ' . $this->name;
						}
						$this->productOptionValueID = $this->ProductOptionValue->getInsertID();
					} else {
						$this->productOptionValueID = $existingProductOptionValue['ProductOptionValue']['id'];
					}
					$existingProductOptionStock = $this->ProductOptionStock->find('first', array(
						'conditions' => array(
							'product_id' => $this->productID,
							'value_ids' => $this->productOptionValueID,
						)
					));
					if(empty($existingProductOptionStock)) {

						// save ProductOptionStock
						$saveProductOptionStocks = array('ProductOptionStock' => array(
							'product_id' => $this->productID,
							'value_ids' => $this->productOptionValueID,
							'option_ids' =>  $this->productOptionID,
							'name' => trim($this->colourStyle),
							'available' => empty($inactive) ? 1 : 0,
							'sku' => $this->sku . $varSku,
							'stock_in_stock' => 1,
							'stock_base_qty' => $this->StockQTY,
							'stock_allow_backorders' => 0,
							'stock_subtract_qty' => 0,
							'modifier' => 'fixed',
						));
						$this->ProductOptionStock->create();

						if (!$this->ProductOptionStock->save($saveProductOptionStocks, false)) {
							pr($this->ProductOptionStock->validationErrors);
							$this->out('Error saving Product Option Stock.' . $this->row);
							$this->erroredProducts[$this->row] = 'Error saving Product Option Stock. ' . $this->name;
						}
						$this->productOptionStockID = $this->ProductOptionStock->getInsertID();

						$product = $this->Product->find('first', array(
							'conditions' => array(
								'Product.id' => $this->productID,
								'Product.default_product_option_stock_id' => 0
							)
						));
						if(!empty($product)) {
							$this->Product->savefield('default_product_option_stock_id', $this->productOptionStockID);
						}
						
						// save ProductOptionStockPrice
						// product_option_stock prices
						if($basePrice != $price) {
							$this->erroredVarPrices[$this->row] = 'FYI Price change. BasePrice: ' . $basePrice . ' Price: ' . $price;
						}
						$saveProductOptionStockPrices = array('ProductOptionStockPrice' => array(
							'product_option_stock_id' => $this->productOptionStockID,
							'currency_id' => $this->currencyID,
							'modifier_value' => ($basePrice != $price) ? $price : '',
						));
						$this->ProductOptionStockPrice->create();
						if (!$this->ProductOptionStockPrice->save($saveProductOptionStockPrices, false)) {
							pr($this->ProductOptionStockPrice->validationErrors);
							$this->out('Error saving Product Option Stock Price.' . $this->row);
							$this->erroredProducts[$this->row] = 'Error saving Product Option Stock Price. ' . $this->name;
						}
						$this->ProductOptionStockPriceID = $this->ProductOptionStockPrice->getInsertID();

					}
					// set default_product_option_stock_id to product
					// if($this->row == 20) {
					// 	debug($this->erroredSKUs);
					// 	die;
					// }
				}
				// save AttributeValuesProduct
				// outside of the products saved loop as each attr is on different line.
				$this->saveAttributeValuesToProducts();
			}



			// debug($productCodesSaved);
			// if($this->row == 5) {
			// 	file_put_contents(TMP . 'erroredProducts.txt', 'Products not imported: ' . print_r($this->erroredProducts, true));
			// 	file_put_contents(TMP . 'erroredVarPrices.txt', 'Products not imported: ' . print_r($this->erroredVarPrices, true));
				// die;
			// }
		}
		debug($this->erroredSKUs);
		debug($this->erroredProducts);
		debug($this->erroredVarPrices);
		file_put_contents(TMP . 'erroredManufacturers.txt', 'Manufacturers not imported: ' . print_r($erroredManufacturers, true));
		file_put_contents(TMP . 'erroredAttributes.txt', 'Attributes not imported: ' . print_r($this->erroredAttributes, true));
		file_put_contents(TMP . 'erroredCustomOptions.txt', 'Custom Options not imported: ' . print_r($this->erroredCustomOptions, true));
		file_put_contents(TMP . 'erroredCategories.txt', 'Categories not imported: ' . print_r($this->erroredCategories, true));
		file_put_contents(TMP . 'erroredProducts.txt', 'Products not imported: ' . print_r($this->erroredProducts, true));
		file_put_contents(TMP . 'erroredVarPrices.txt', 'Products not imported: ' . print_r($this->erroredVarPrices, true));
		file_put_contents(TMP . 'erroredSKUs.txt', 'Inconsistent SKUs - imported but flagged: ' . print_r($this->erroredSKUs, true));
	}

	public function saveCategory($category, $subCategory) {
		$this->existingCategory = $this->CategoryName->find('first', array(
			'conditions' => array('CategoryName.name' => trim($category))
		));
		if(!empty($category)) {

			if(empty($this->existingCategory)) {
				$saveCategory = array(
					'Category' => array(
						'parent_id' => 0,
						'active' => 1,
					)
				);
				$this->Category->create();
				if (!$this->Category->save($saveCategory))
				{
					pr($this->Category->validationErrors);
					$this->out('Error saving Category.' . $this->row);
					$this->erroredCategories[$this->row] = $category;
				}				
				
				$this->categoryID = $this->Category->getInsertID();

				$saveCategoryName = array(
					'CategoryName' => array(
						'category_id' => $this->categoryID,
						'language_id' => $this->languageID,
						'name' => trim($category)
					)
				);
				$this->CategoryName->create();
				if (!$this->CategoryName->save($saveCategoryName))
				{
					pr($this->CategoryName->validationErrors);
					$this->out('Error saving Category Name.' . $this->row);
					$this->erroredCategories[$this->row] = $category;
				}				
				$saveCategoryDescription = array(
					'CategoryDescription' => array(
						'category_id' => $this->categoryID,
						'language_id' => $this->languageID,
					)
				);
				$this->CategoryDescription->create();
				if (!$this->CategoryDescription->save($saveCategoryDescription))
				{
					pr($this->CategoryDescription->validationErrors);
					$this->out('Error saving Category Description.' . $this->row);
					$this->erroredCategories[$this->row] = $category;
				}				
			} else {
				$this->categoryID = $this->existingCategory['CategoryName']['category_id'];
			}

			// save subcategories
			$existingSubCategory = $this->CategoryName->find('first', array(
				'conditions' => array(
					'CategoryName.name' => trim($subCategory),
					'parent_id' => $this->categoryID,
				)
			));
			if(empty($existingSubCategory) && !empty($subCategory)) {
				$saveSubCategory = array(
					'Category' => array(
						'parent_id' => $this->categoryID,
						'active' => 1,
					)
				);
				$this->Category->create();
				if (!$this->Category->save($saveSubCategory))
				{
					pr($this->Category->validationErrors);
					$this->out('Error saving Sub Category.' . $this->row);
					$this->erroredCategories[$this->row] = $category;
				}				
				
				$this->subCategoryID = $this->Category->getInsertID();

				$saveSubCategoryName = array(
					'CategoryName' => array(
						'category_id' => $this->subCategoryID,
						'language_id' => $this->languageID,
						'name' => trim($subCategory)
					)
				);
				$this->CategoryName->create();
				if (!$this->CategoryName->save($saveSubCategoryName))
				{
					pr($this->CategoryName->validationErrors);
					$this->out('Error saving Sub Category Name.' . $this->row);
					$this->erroredCategories[$this->row] = $subCategory;
					// $this->Category->delete($this->subCategoryID);
				}
				$saveSubCategoryDescription = array(
					'CategoryDescription' => array(
						'category_id' => $this->subCategoryID,
						'language_id' => $this->languageID,
					)
				);
				$this->CategoryDescription->create();
				if (!$this->CategoryDescription->save($saveSubCategoryDescription))
				{
					pr($this->CategoryDescription->validationErrors);
					$this->out('Error saving Category Description.' . $this->row);
					$this->erroredCategories[$this->row] = $category;
				}				
			}	
		}
		$this->categoryID = NULL;	
		$this->subCategoryID = NULL;	
	}

	public function saveAttributes($attributeArray) {
		$existingAttributeSet = $this->AttributeSetName->find('first', array(
			'conditions' => array('AttributeSetName.name' => $attributeArray['attributeSetName'])
		));
		if(empty($existingAttributeSet)) {
			$this->AttributeSet->create();
			$this->AttributeSet->savefield('created', date('Y-m-d h:i:s'));
			$this->attributeSetID = $this->AttributeSet->getInsertID();

			$saveAttributeSetNames = array(
				'AttributeSetName' => array(
					'language_id' => $this->languageID,
					'attribute_set_id' => $this->attributeSetID,
					'name' => $attributeArray['attributeSetName'],
				)
			);
			$this->AttributeSetName->create();
			if (!$this->AttributeSetName->save($saveAttributeSetNames))
			{
				pr($this->AttributeSetName->validationErrors);
				$this->out('Error saving AttributeSetName.' . $this->row);
				$this->erroredAttributes[$this->row] = $this->name;
			}
		}
		// save attrs
		$existingAttributeName = $this->AttributeName->find('first', array(
			'conditions' => array('AttributeName.name' => $attributeArray['attributeName'])
		));
		if(empty($existingAttributeName)) {
			// save Attribute
			$this->Attribute->create();
			$this->Attribute->savefield('created', date('Y-m-d h:i:s'));
			$this->attributeID = $this->Attribute->getInsertID();
			
			// save AttributeName
			$saveAttributeNames = array(
				'AttributeName' => array(
					'language_id' => $this->languageID,
					'attribute_id' => $this->attributeID,
					'name' => $attributeArray['attributeName'],
					'display_name' => $attributeArray['attributeName'],
				)
			);
			$this->AttributeName->create();
			// turn off attrName name validation 
			if (!$this->AttributeName->save($saveAttributeNames, false)) {
				pr($this->AttributeName->validationErrors);
				$this->out('Error saving AttributeName.' . $this->row);
				$this->erroredAttributes[$this->row] = 'Error saving AttributeName. ' . $this->name;
			}
			$saveAttributeSetsAttributes = array(
				'AttributeSetsAttribute' => array(
					'attribute_set_id' => $this->attributeSetID,
					'attribute_id' => $this->attributeID,
				)
			);
			$this->AttributeSetsAttribute->create();
			if (!$this->AttributeSetsAttribute->save($saveAttributeSetsAttributes)) {
				pr($this->AttributeSetsAttribute->validationErrors);
				$this->out('Error saving Attribute Set Attribute.' . $this->row);
				$this->erroredAttributes[$this->row] = 'Error saving Attribute Set Attribute. ' . $this->name;
			}
		}
		if(!empty($this->{$attributeArray['column']})) {
			$existingAttributeValue = $this->AttributeValueName->find('first', array(
				'conditions' => array('AttributeValueName.name' => trim($this->{$attributeArray['column']}))
			));
			if(empty($existingAttributeValue)) {
				// save AttributeValue
				$attributeID = $this->AttributeName->find('first', array(
					'conditions' => array('AttributeName.name' => $attributeArray['attributeName'])
				));
				$this->AttributeValue->create();
				$this->AttributeValue->savefield('attribute_id', $attributeID['Attribute']['id']);
				$this->attributeValueID = $this->AttributeValue->getInsertID();

				// save AttributeValueName
				$saveAttributeValueNames = array(
					'AttributeValueName' => array(
						'language_id' => $this->languageID,
						'attribute_value_id' => $this->attributeValueID,
						'name' => trim($this->{$attributeArray['column']}),
					)
				);
				$this->AttributeValueName->create();
				if (!$this->AttributeValueName->save($saveAttributeValueNames)) {
					pr($this->AttributeValueName->validationErrors);
					$this->out('Error saving Attribute Value Name.' . $this->row);
					$this->erroredAttributes[$this->row] = 'Error saving Attribute Value Name. ' . $this->name;
				}
			}
		}
		$this->saveCustomOptions($attributeArray);
	}
	public function saveCustomOptions($attributeArray) {
		if($attributeArray['attributeName'] == $this->CustOptionName) {
			$existingCustomOption = $this->CustomOptionName->find('first', array(
				'conditions' => array('CustomOptionName.name' =>trim($this->CustOptionName))
			));
			if(empty($existingCustomOption)) {
				$this->CustomOption->create();
				$this->CustomOption->savefield('created', date('Y-m-d h:i:s'));
				$this->customOptionID = $this->CustomOption->getInsertID();

				$saveCustomOptionNames = array(
					'CustomOptionName' => array(
						'language_id' => $this->languageID,
						'custom_option_id' => $this->customOptionID,
						'name' => trim($this->CustOptionName),
					)
				);
				$this->CustomOptionName->create();
				if (!$this->CustomOptionName->save($saveCustomOptionNames, false))
				{
					pr($this->CustomOptionName->validationErrors);
					$this->out('Error saving Custom Option Name.' . $this->row);
					$this->erroredCustomOptions[$this->row] = $this->name;
				}
				$this->customOptionNameID = $this->CustomOptionName->getInsertID();
			} else {
				$this->customOptionNameID = $existingCustomOption['CustomOptionName']['id'];
			}

			if(!empty($this->customOptionID) && !empty($this->{$attributeArray['customOptionColumn']})) {
				$customValueName = $this->CustomOptionValueName->find('first', array(
					'conditions' => array('CustomOptionValueName.name' => trim($this->{$attributeArray['customOptionColumn']}))
				));
				if(empty($customValueName)) {
					$saveCustomOptionValues = array(
						'CustomOptionValue' => array(
							'custom_option_id' => $this->customOptionID,
						)
					);
					$this->CustomOptionValue->create();
					if (!$this->CustomOptionValue->save($saveCustomOptionValues))
					{
						pr($this->CustomOptionValue->validationErrors);
						$this->out('Error saving Custom Option Value.' . $this->row);
						$this->erroredCustomOptions[$this->row] = $this->name;
					}
					$this->customOptionValueID = $this->CustomOptionValue->getInsertID();
					if(!empty($this->customOptionValueID)) {
						
						$saveCustomOptionValueNames = array(
							'CustomOptionValueName' => array(
								'custom_option_value_id' => $this->customOptionValueID,
								'language_id' => $this->languageID,
								'name' => trim($this->{$attributeArray['customOptionColumn']}),
							)
						);
						$this->CustomOptionValueName->create();
						if (!$this->CustomOptionValueName->save($saveCustomOptionValueNames))
						{
							pr($this->CustomOptionValueName->validationErrors);
							$this->out('Error saving Custom Option Value Name.' . $this->row);
							$this->erroredCustomOptions[$this->row] = $this->name;
						}
					}
				}
			}
		}

	}
	public function saveAttributeValuesToProducts() {
		$productID = $this->Product->find('first', array(
			'conditions' => array('Product.sku' => $this->sku)
		));
		foreach ($this->attributes as $attributeOption) {
			if(!empty($this->{$attributeOption['column']})) {

				$attributeValueID = $this->AttributeValueName->find('first', array(
					'conditions' => array('AttributeValueName.name' => $this->{$attributeOption['column']})
				));
				if(!empty($attributeValueID)) {
					$saveAttributeValuesProduct = array('AttributeValuesProduct' => array(
						'product_id' => $productID['Product']['id'],
						'attribute_value_id' => $attributeValueID['AttributeValueName']['attribute_value_id'],
					));
					$this->AttributeValuesProduct->create();
					if (!$this->AttributeValuesProduct->save($saveAttributeValuesProduct)) {
						pr($this->AttributeValuesProduct->validationErrors);
						$this->out('Error saving Product Attributes.' . $this->row);
						$this->erroredProducts[$this->row] = 'Error saving Product Attributes. ' . $this->name;
					}
				}
			}
			
		}
	}

	public function formatDescription($longDescription, $vat) {
		$longDescription = '<p>' . trim($longDescription) . '</p>';
		$vatMessage = array(
			's' => 'Inclusive of VAT at 20%',
			'zero' => 'VAT exampt'
		);
		// if empty set to standard
		$vat = empty($vat) ? 's' : $vat;
		// tidy up inconsistencies
		$vat = ($vat == 'Z') ? 'zero' : $vat;
		$longDescription .= '<p>' . $vatMessage[strtolower($vat)] . '</p>' ;
		$longDescription .= '<p>Dimensions  H x W x D (mm):' . $this->dimensions . '</p>';
		return $longDescription;
	}

}