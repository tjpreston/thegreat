<?php

/**
 * Image upload Component
 * 
 */
class ImageNewComponent extends Object
{
	/**
	 * Reference to calling controller.
	 * 
	 * @var object
	 * @access private
	 */
	private $controller;
	
	/**
	 * Reference to image model.
	 * 
	 * @var object
	 * @access private
	 */
	private $imageModel;
	
	/**
	 * Called after controller's beforeFilter.
	 * 
	 * @param object &$controller
	 * @return void
	 * @access public
	 */
	public function startup(&$controller)
	{
		$this->controller = $controller;		
	}

	public function resize($source, $dest, $width, $height, $masterDim = 'auto')
	{
		$config = array(
			'source_image'		=> $source,
			'new_image'			=> $dest,
			'width' 			=> $width,
			'height'			=> $height,
			'maintain_ratio'	=> true,
			'master_dim'		=> $masterDim,
			'dynamic_output'	=> false,
			'quality'			=> Configure::read('Images.upload_quality'),
			'image_library' 	=> 'GD2'
		);

		$imageLib = new CI_Image_lib($config);
		$imageLib->resize();

		return $dest;
		
	}

	public function expandToFit($path, $width, $height)
	{
		$dims = getimagesize($path);
		
		$isTooNarrow = $dims[0] < $width;
		$isTooShort  = $dims[1] < $height;
		
		if ($isTooNarrow || $isTooShort)
		{
			$image = imagecreatefromjpeg($path);
			$frame = imagecreatetruecolor($width, $height);
			$rgb = $this->_rgb2array('FFFFFF');
			$fillColor = imagecolorallocate($frame, $rgb[0], $rgb[1], $rgb[2]);
			imagefill($frame, 0, 0, $fillColor);
			
			if ($isTooNarrow)
			{
				$tooNarrowByPixels = $width - $dims[0];
				$leftSidePadding = round($tooNarrowByPixels / 2);
				imagecopymerge($frame, $image, $leftSidePadding, 0, 0, 0, $dims[0], $dims[1], 100);
			}
			else
			{
				$tooShortByPixels = $height - $dims[1];
				$topSidePadding = round($tooShortByPixels / 2);
				imagecopymerge($frame, $image, 0, $topSidePadding, 0, 0, $dims[0], $dims[1], 100);
			}
			
			imagejpeg($frame, $path, Configure::read('Images.upload_quality'));
			
		}
		
	}
	
	/**
	 * Convert RGB value to array
	 * 
	 * @param string $rgb
	 * @return array
	 * @access private
	 */
	private function _rgb2array($rgb) 
	{
	    return array(
	        base_convert(substr($rgb, 0, 2), 16, 10),
	        base_convert(substr($rgb, 2, 2), 16, 10),
	        base_convert(substr($rgb, 4, 2), 16, 10),
	    );
	}

}


