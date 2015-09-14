<?php

class ImagesShell extends Shell
{
	public $uses = array('ProductImage');
	
	public function main()
	{
		App::import('Component', 'Image');
		$this->Image =& new ImageComponent();
		
		App::import('Vendor', 'CI_Image_lib', array('file' => 'Image_lib.php'));

		$images = $this->ProductImage->find('all');
		
		foreach ($images as $image)
		{
			$thumbPath  = WWW_ROOT . 'img/products/thumb/' . $image['ProductImage']['filename'];
			$tinyPath   = $this->Image->resizeAndSaveTiny($thumbPath, $image['ProductImage']['filename'], false);
		}
		
		
	}
	
	
}


