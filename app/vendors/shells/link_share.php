<?php

/**
 * This shell will generate a file containing all products,
 * and then upload it to LinkShare's FTP account.
 *
 * Ported accross from old Michel Herbelin mason site in 2013.
 *
 * @author Ollie <ollie@popcornwebdesign.co.uk>
 */
class LinkShareShell extends Shell {
	/**
	 * String containing which config key to use
	 *
	 * @var string
	 * @access public
	 */
	public $location = null;

	/**
	 * LinkShare Merchant ID
	 * Pulled in from Configure class in class constructor
	 *
	 * @var string
	 * @access public
	 */
	public $merchantId = null;

	/**
	 * Company name
	 *
	 * @var string
	 * @access public
	 */
	public $companyName = 'Michel Herbelin';

	/**
	 * Base for product URLs
	 *
	 * @var string
	 * @access public
	 */
	public $baseUrl = null;

	/**
	 * FTP details, keyed by current location
	 * Leave blank for no FTP
	 *
	 * @var array
	 * @access public
	 */
	public $ftpConf = array(
		'mother' => array(
			'hostname'  => '',
			'username'  => '',
			'password'  => '',
			'directory' => '',
		),
		'popdev' => array(
			'hostname'  => '',
			'username'  => '',
			'password'  => '',
			'directory' => '',
		),
		'live' => array(
			'hostname'  => 'mftp.linksynergy.com',
			'username'  => 'herbMIC',
			'password'  => 'f97zJhiB',
			'directory' => '/',
		),
	);

	/**
	 * Path to products file
	 *
	 * @var string
	 * @access public
	 */
	public $filePath = null;

	public $uses = array('Product', 'Category');

	public function __construct(&$dispatch){
		parent::__construct($dispatch);

		$this->filePath = TMP . 'linkshareProducts.txt';

		$this->merchantId = Configure::read('LinkShare.merchant_id');

		// Determine where we are
		if(function_exists('gethostname') && gethostname() == 'mother'){
			$this->location = 'mother';
		} else if(env('USER') == 'popdev') {
			$this->location = 'popdev';
		} else {
			$this->location = 'live';
		}

		// Set the correct base URL
		switch ($this->location) {
			case 'mother':
				$this->baseUrl = 'http://www.michelherbelin.devel/';
				break;

			case 'popdev':
				$this->baseUrl = 'http://michelherbelin.popcorndev.co.uk/';
				break;
			
			default:
				$this->baseUrl = 'http://www.michelherbelin.co.uk/';
				break;
		}
	}

	public function main(){
		$this->generate();
		$this->upload();
	}

	/**
	 * Generate the products file.
	 *
	 * @return void
	 * @access public
	 */
	public function generate(){
		$this->Product->bindName($this->Product, 1, false);
		$this->Product->bindMeta($this->Product, 1, false);
		$this->Product->bindDescription($this->Product, 1, false);
		$this->Product->bindPrice(1, false);
		
		$this->Category->bindName($this->Category, 1, false);
		$this->Category->unbindModel(array('hasAndBelongsToMany' => array('Product')), false);
		
		$records = $this->Product->find('all', array('conditions' => array(
			'Product.active' => 1,
		)));

		$fh = fopen($this->filePath, 'w');

		// Output the header row
		fwrite($fh, 'HDR|' . $this->merchantId . '|' . $this->companyName . '|' . date('Y-m-d/H:i:s') . PHP_EOL);

		foreach($records as $record){
			// Primary & Secondary category
			$primaryCatId = null;
			foreach($record['ProductCategory'] as $cat){
				if($cat['primary']){
					$primaryCatId = $cat['category_id'];
				}
			}

			$primaryCategoryName = '';
			$secondaryCategoryName = '';
			if(!empty($primaryCatId)){
				$primaryCategory = $this->Category->find('first', array(
					'conditions' => array('Category.id' => $primaryCatId),
				));

				$secondaryCategory = $this->Category->getparentnode($primaryCategory['Category']['id']);

				$primaryCategoryName = $primaryCategory['CategoryName']['name'];

				if(!empty($secondaryCategory)){
					$secondaryCategory = $this->Category->find('first', array(
						'conditions' => array('Category.id' => $secondaryCategory['Category']['id']),
					));

					$secondaryCategoryName = $secondaryCategory['CategoryName']['name'];
				}
			}

			// Product Image
			$productImageUrl = '';
			if(!empty($record['ProductImage'][0]['large_web_path'])){
				$productImageUrl = $this->baseUrl . substr($record['ProductImage'][0]['large_web_path'], 1);
			}

			// Pricing
			if($record['ProductPrice']['on_special']){
				$price = $record['ProductPrice']['base_price'];
				$specialPrice = $record['ProductPrice']['active_price'];
			} else {
				$price = $record['ProductPrice']['active_price'];
				$specialPrice = null;
			}

			$fields = array(
				'product_id' => $record['Product']['id'],
				'product_name' => $record['ProductName']['name'],
				'sku' => $record['Product']['sku'],
				'primary_category' => (!empty($primaryCategoryName) ? $primaryCategoryName : ''),
				'secondary_category' => (!empty($secondaryCategoryName) ? $secondaryCategoryName : ''),
				'product_url' => $this->baseUrl . $record['ProductMeta']['url'],
				'image_url' => $productImageUrl,
				'buy_url' => null,
				'short_product_description' => $record['ProductDescription']['short_description'],
				'long_product_description' => $record['ProductDescription']['long_description'],
				'discount' => null,
				'discount_type' => null,
				'sale_price' => $specialPrice,
				'retail_price' => $price,
				'begin_date' => null,
				'end_date' => null,
				'brand' => null,
				'shipping' => null,
				'is_deleted' => 'N',
				'keywords' => null,
				'is_all' => 'Y',
				'manufacturer_part_no' => null,
				'manufacturer_name' => null,
				'shipping_info' => null,
				'availability' => null,
				'universal_product_code' => null,
				'class_id' => null,
				'is_product_link' => 'Y',
				'is_storefront' => 'Y',
				'is_merchandiser' => 'Y',
				'currency' => 'GBP',
				'M1' => null
			);

			fwrite($fh, implode('|', $fields) . PHP_EOL);
		}

		$num_rows = count($records);

		fwrite($fh, 'TRL|' . $num_rows);

		fclose($fh);

		$this->out('Created products file: ' . $this->filePath);
	}

	/**
	 * Upload the products file via FTP.
	 *
	 * @return void
	 * @access public
	 */
	public function upload(){
		$config = $this->ftpConf[$this->location];

		// If we don't need to FTP, quit.
		if(empty($config) || empty($config['hostname'])){
			$this->out("We don't have FTP details for location '{$this->location}', so I'm quitting.");
			return false;
		}

		if(!function_exists('ftp_connect')){
			$this->out("ERROR: The ftp_connect() function is not installed on location '{$this->location}', so I'm quitting.");
			return false;
		}

		$ftp = ftp_connect($config['hostname']);
		ftp_login($ftp, $config['username'], $config['password']);

		$ftp_path = $config['directory'] . $this->merchantId . '_nmerchandis' . date('Ymd') . '.txt';
		$local_file = $this->filePath;

		$upload = ftp_put($ftp, $ftp_path, $local_file, FTP_ASCII);

		ftp_close($ftp);

		$this->out('Uploaded products file via FTP.');
	}
}