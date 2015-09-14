<?php

class EsperImage {
	// Holds the PHPThumb object
	private $pt = null;

	// Names of different sizes of images (must be corresponding keys in config.php!)
	private $sizes = array('tiny', 'thumb', 'medium', 'large');

	// Holds dimensions for each size
	private $dimensions = array();

	// Holds image paths for each size
	private $paths = array();

	// Holds options
	private $options = array();

	// Holds options for PHPThumb
	private $thumbOptions = array();

	// Holds original image path
	private $fileName = null;

	public function __construct($fileName, $options = array()){
		$thumbOptions = array();
		if(!empty($options['jpegQuality'])){
			$thumbOptions['jpegQuality'] = $options['jpegQuality'];
		} else {
			$thumbOptions['jpegQuality'] = Configure::read('Images.upload_quality');
		}

		$thumbOptions['resizeUp'] = true;

		if(empty($options['type'])){
			$options['type'] = 'product';
		}

		$this->options = $options;
		$this->thumbOptions = $thumbOptions;
		$this->fileName = $fileName;

		App::import('Vendor', 'PhpThumbFactory', array('file' => 'PHPThumb/ThumbLib.inc.php'));

		$this->configureSizes();
	}

	private function create(){
		$this->pt = PhpThumbFactory::create($this->fileName, $this->thumbOptions);
	}

	private function configureSizes(){
		$dimensionTypes = array('width', 'height');

		$dimensions = array();
		$paths = array();
		foreach($this->sizes as $size){
			// Get dimensions
			foreach($dimensionTypes as $dimensionType){
				$sizeKey = $size;
				if($sizeKey == 'large'){
					$sizeKey = 'max_large';
				}

				$configKeyStart = 'product';

				if($this->options['type'] == 'var' && $size !== 'large'){
					$configKeyStart = 'var';
				}

				$configKey = 'Images.' . $configKeyStart . '_' . $sizeKey . '_' . $dimensionType;
				$dimensions[$size][$dimensionType] = Configure::read($configKey);
			}

			// Get paths
			$configKeyStart = 'product';

			if($this->options['type'] == 'var'){
				$configKeyStart = 'var';
			}

			$configKey = 'Images.' . $configKeyStart . '_' . $size . '_' . 'path';
			$paths[$size] = Configure::read($configKey);
		}

		$this->dimensions = $dimensions;
		$this->paths = $paths;
	}

	public function resize($size){
		$this->create();

		$dimensions = $this->dimensions[$size];
		$path = $this->paths[$size];

		$padColour = Configure::read('Images.pad_colour');
		if(empty($padColour)) $padColour = array(255, 255, 255);

		$saveTo = WWW_ROOT . $path . $this->options['saveFilename'];

		$this->pt->resize($dimensions['width'], $dimensions['height']);
		$this->pt->pad($dimensions['width'], $dimensions['height'], $padColour);
		$this->pt->save($saveTo);
	}

	public function resizeAll(){
		foreach($this->sizes as $size){
			$this->resize($size);
		}
	}

}