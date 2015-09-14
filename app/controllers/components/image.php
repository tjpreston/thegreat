<?php

/**
 * Image upload Component
 * 
 */
class ImageComponent extends Object
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
	
	
	public function init($parentModel, $imageModel)
	{
		$this->parentModel = $parentModel;
		$this->imageOf = strtolower($this->parentModel->name);
		$this->imageModel = $imageModel;
	}
	
	/**
	 * Upload an image.
	 * 
	 * @return void
	 * @access public
	 */
	public function upload($file, $recordID, $recordRef)
	{
		if ($file['error'] !== 0)
		{
			return false;
		}
		
		$this->imageModel->create();
		$insertResult = $this->imageModel->save(array($this->imageModel->name => array(
			$this->imageOf . '_id' => $recordID
		)));

		$imageID = $this->imageModel->getInsertID();
		
		if (empty($imageID))
		{
			return false;
		}
		
		$uploadedFileName = $file["name"];
		$pathInfo = pathinfo($uploadedFileName);
		$fileNameNoExt = Inflector::slug(strtolower($recordRef)) . '-' . $imageID;
		$fileName = $fileNameNoExt . '.' .  $pathInfo['extension'];
		
		$orginalPath = WWW_ROOT . 'img/' . Inflector::pluralize(strtolower($this->parentModel->name)) . '/original/' . $fileName;
		
		move_uploaded_file($file['tmp_name'], $orginalPath);
		
		$imageSize = getimagesize($orginalPath);
		$filesize = filesize($orginalPath);
		
		App::import('Vendor', 'CI_Image_lib', array('file' => 'Image_lib.php'));
		$largePath  = $this->resizeLargeImage($orginalPath, $fileName);
		
		$mediumPath = $this->resize('medium', $largePath, $fileName);
		$thumbPath  = $this->resize('thumb', $mediumPath, $fileName);
		$tinyPath   = $this->resize('tiny', $thumbPath, $fileName);
		
		$this->imageModel->save(array($this->imageModel->name => array(
			'mime_type' => $imageSize['mime'],
			'filename'  => $fileNameNoExt,
			'ext'	 	=> $pathInfo['extension']
		)));

	}
	
	public function resizeLargeImage($originalPath, $fileName)
	{
		$largePath = WWW_ROOT . 'img/' . Inflector::pluralize($this->imageOf) . '/large/' . $fileName;
		$imageSize = getimagesize($originalPath);
		
		
		if ($imageSize[0] <= Configure::read('Images.' . $this->imageOf . '_max_large_width') && ($imageSize[1] <= Configure::read('Images.' . $this->imageOf . '_max_large_height')))
		{
			copy($originalPath, $largePath);
			return $largePath;
		}
		
		$masterDim = ($imageSize[0] > Configure::read('Images.' . $this->imageOf . '_max_large_width')) ? 'width' : 'height';
		
		$resizeConfig = array(
			'image_library' 	=> 'GD2',
			'source_image'		=> $originalPath,
			'new_image'			=> $largePath,
			'width' 			=> Configure::read('Images.' . $this->imageOf . '_max_large_width'),
			'height'			=> Configure::read('Images.' . $this->imageOf . '_max_large_height'),
			'maintain_ratio'	=> true,
			'master_dim'		=> $masterDim,
			'dynamic_output'	=> false,
			'quality'			=> 90
		);
		
		$imageLib = new CI_Image_lib($resizeConfig);
		$imageLib->resize();
					
		$resizedSize = getimagesize($largePath);
			
		// If it's still too tall
		if (($masterDim == 'width') && ($resizedSize[1] > Configure::read('Images.' . $this->imageOf . '_max_large_height')))
		{
			$resizeConfig = array(
				'image_library' 	=> 'GD2',
				'source_image'		=> $largePath,
				'new_image'			=> $largePath,
				'width' 			=> Configure::read('Images.' . $this->imageOf . '_max_large_width'),
				'height'			=> Configure::read('Images.' . $this->imageOf . '_max_large_height'),
				'maintain_ratio'	=> true,
				'master_dim'		=> 'height',
				'dynamic_output'	=> false,
				'quality'			=> 90
			);
			
			$imageLib = new CI_Image_lib($resizeConfig);
			$imageLib->resize();
			
		}
		// If it's still too wide
		else if (($masterDim == 'height') && ($resizedSize[0] > Configure::read('Images.' . $this->imageOf . '_max_large_width')))
		{
			$resizeConfig = array(
				'image_library' 	=> 'GD2',
				'source_image'		=> $largePath,
				'new_image'			=> $largePath,
				'width' 			=> Configure::read('Images.' . $this->imageOf . '_max_large_width'),
				'height'			=> Configure::read('Images.' . $this->imageOf . '_max_large_height'),
				'maintain_ratio'	=> true,
				'master_dim'		=> 'width',
				'dynamic_output'	=> false,
				'quality'			=> 90
			);
			
			$imageLib = new CI_Image_lib($resizeConfig);
			$imageLib->resize();
			
		}

		return $largePath;
		
	}
	
	/**
	 * 
	 * 
	 * @param object $size
	 * @param object $source
	 * @param object $filename
	 * @param object $overwrite [optional]
	 * @return $dest
	 * @access public
	 */	
	public function resize($size, $source, $filename, $overwrite = true)
	{
		$dest = WWW_ROOT . 'img/' . Inflector::pluralize($this->imageOf) . '/' . $size . '/' . $filename;
		
		if (empty($overwrite) && file_exists($dest))
		{
			return $dest;
		}
		
		$thumbConfig = array(
			'image_library' 	=> 'GD2',
			'source_image'		=> $source,
			'new_image'			=> $dest,
			'width' 			=> Configure::read('Images.' . $this->imageOf . '_' . $size . '_width'),
			'height'			=> Configure::read('Images.' . $this->imageOf . '_' . $size . '_height'),
			'maintain_ratio'	=> true,
			'master_dim'		=> 'auto',
			'dynamic_output'	=> false,
			'quality'			=> 90
		);
		
		$imageLib = new CI_Image_lib($thumbConfig);
		$imageLib->resize();
		
		$dims = getimagesize($dest);
		
		if (($dims[0] < Configure::read('Images.' . $this->imageOf . '_' . $size . '_width')) || ($dims[1] < Configure::read('Images.' . $this->imageOf . '_' . $size . '_height')))
		{
			$image = imagecreatefromjpeg($dest);
			$frame = imagecreatetruecolor(
				Configure::read('Images.' . $this->imageOf . '_' . $size . '_width'),
				Configure::read('Images.' . $this->imageOf . '_' . $size . '_height')
			);
			$rgb = $this->_rgb2array('FFFFFF');
			$fillColor = imagecolorallocate($frame, $rgb[0], $rgb[1], $rgb[2]);
			imagefill($frame, 0, 0, $fillColor);
			
			// Too narrow
			if ($dims[0] < Configure::read('Images.' . $this->imageOf . '_' . $size . '_width'))
			{
				$tooNarrowByPixels = Configure::read('Images.' . $this->imageOf . '_' . $size . '_width') - $dims[0];
				$leftSidePadding = round($tooNarrowByPixels / 2);
				imagecopymerge($frame, $image, $leftSidePadding, 0, 0, 0, $dims[0], $dims[1], 100);
			}
			// Too short
			else
			{
				$tooShortByPixels = Configure::read('Images.' . $this->imageOf . '_' . $size . '_height') - $dims[1];
				$topSidePadding = round($tooShortByPixels / 2);
				imagecopymerge($frame, $image, 0, $topSidePadding, 0, 0, $dims[0], $dims[1], 100);
			}
			
			imagejpeg($frame, $dest);
			
		}
		
		return $dest;
		
	}
	
	
	
	
	
	/**
	 * Crop image
	 * 
	 * @param int $id
	 * @return bool
	 * @access public
	 */
	public function crop($id)
	{
		$image = $this->controller->ProductImage->findById($id);
		$ext = $image['ProductImage']['ext'];
		$fileName = $image['ProductImage']['filename'];
		
		$editedPath   = WWW_ROOT . 'img/products/edited/' . $fileName;
		$sourcePath = (file_exists($editedPath)) ? $editedPath : WWW_ROOT . 'img/products/original/' . $fileName;
		
		$resizeData = $this->controller->data['ProductImage'];
		
		/*
		@TODO
		if ($resizeData['w'] < 100)
		{
			$this->Session->setFlash('Please crop your photo to a larger size' , 'default', array('class' => 'bad'));
			return $this->redirect('/photos/crop/' . $imageID);
		}
		
		// if (($resizeData['w'] < (Configure::read('Photos.large_height') - (Configure::read('Photos.large_height') - 100))))
		if ($resizeData['h'] < 100)
		{
			$this->Session->setFlash('Please crop your photo to a larger size' , 'default', array('class' => 'bad'));
			return $this->redirect('/photos/crop/' . $imageID);
		}
		*/
		
		@unlink(WWW_ROOT . 'img/products/large/' . $fileName);
		@unlink(WWW_ROOT . 'img/products/medium/' . $fileName);
		@unlink(WWW_ROOT . 'img/products/thumb/' . $fileName);
		@unlink(WWW_ROOT . 'img/products/tiny/' . $fileName);
		
		$cropConfig = array(
			'image_library' 	=> 'GD2',
			'source_image'		=> $sourcePath,
			'new_image'			=> $editedPath,
			'quality'			=> 90,
			'width'				=> $resizeData['w'],
			'height'			=> $resizeData['h'],
			'x_axis'			=> $resizeData['x1'],
			'y_axis'			=> $resizeData['y1'],
			'maintain_ratio'	=> false
		);
		
		App::import('Vendor', 'CI_Image_lib', array('file' => 'Image_lib.php'));
		$imageLib = new CI_Image_lib($cropConfig);
		$imageLib->crop();
		
		$largePath  = $this->resizeLargeImage($editedPath, $fileName);
		$mediumPath = $this->resizeAndSaveMedium($largePath, $fileName);
		$thumbPath  = $this->resizeAndSaveThumb($mediumPath, $fileName);
		$tinyPath   = $this->resizeAndSaveTiny($thumbPath, $fileName);
		
		return true;
		
	}
	
	/**
	 * Rotate image.
	 * 
	 * @param int $id
	 * @return mixed
	 * @access public
	 */
	public function rotate($id)
	{
		$image = $this->controller->ProductImage->findById($id);
		$ext = $image['ProductImage']['ext'];
		$fileName = $image['ProductImage']['filename'];
		
		$editedPath   = WWW_ROOT . 'img/products/edited/' . $fileName;
		$sourcePath = (file_exists($editedPath)) ? $editedPath : WWW_ROOT . 'img/products/original/' . $fileName;
		
		$size = getimagesize($sourcePath);
		$types = array(1 => 'gif', 2 => 'jpeg', 3 => 'png');
		$imageType = $types[$size[2]];
		
		switch ($imageType)
		{
			case 'gif':	
				$img = imagecreatefromgif($sourcePath);
				break;
			case 'jpeg':
				$img = imagecreatefromjpeg($sourcePath);
				break;
			case 'png':
				$img = imagecreatefrompng($sourcePath);
				break;
			default:	
				return false;
				break;
		}
		
		$width = imagesx($img);
		$height = imagesy($img);
		$rotation = 90;
	  
		switch ($rotation)
		{
			case 90: $newimg = @imagecreatetruecolor($height, $width); break;
			case 180: $newimg = @imagecreatetruecolor($width, $height); break;
			case 270: $newimg = @imagecreatetruecolor($height, $width); break;
			case 0: return $img; break;
			case 360: return $img; break;
		}
	 	
		if ($newimg)
		{
			for($i = 0; $i < $width; $i++)
			{
				for ($j = 0; $j < $height; $j++)
				{
					$reference = imagecolorat($img, $i, $j);
					switch($rotation)
					{
						case 90: if (!@imagesetpixel($newimg, ($height - 1) - $j, $i, $reference)) { return false; } break;
						case 180: if (!@imagesetpixel($newimg, $width - $i, ($height - 1) - $j, $reference)) { return false; }break;
						case 270: if (!@imagesetpixel($newimg, $j, $width - $i, $reference)) {return false; } break;
					}
				}
			}
			
			switch ($imageType)
			{
				case 'gif':	
					imagegif($newimg, $editedPath);
					break;
				case 'jpeg':
					imagejpeg($newimg, $editedPath, 90);
					break;
				case 'png':
					imagepng($newimg, $editedPath, 9);
					break;
				default:	
					return false;
					break;
			}
			
		}
		
		App::import('Vendor', 'CI_Image_lib', array('file' => 'Image_lib.php'));
		
		$largePath  = $this->resizeLargeImage($editedPath, $fileName);
		$mediumPath = $this->resizeAndSaveMedium($largePath, $fileName);
		$thumbPath  = $this->resizeAndSaveThumb($mediumPath, $fileName);
		$tinyPath   = $this->resizeAndSaveTiny($thumbPath, $fileName);
		
		return false;
		
	}
	
	/**
	 * Restore original image
	 * 
	 * @param object $id
	 * @return mixed
	 * @access public
	 */
	public function restore($id)
	{
		$productImage = $this->controller->ProductImage->findById($id);
		
		$fileName = $productImage['ProductImage']['filename'];
		
		$originalPath = WWW_ROOT . 'img/products/original/' . $fileName;
		
		if (empty($originalPath))
		{
			return false;
		}

		@unlink(WWW_ROOT . 'img/products/edited/' . $fileName);		
		@unlink(WWW_ROOT . 'img/products/large/' . $fileName);
		@unlink(WWW_ROOT . 'img/products/medium/' . $fileName);
		@unlink(WWW_ROOT . 'img/products/thumb/' . $fileName);
		@unlink(WWW_ROOT . 'img/products/tiny/' . $fileName);		
				
		App::import('Vendor', 'CI_Image_lib', array('file' => 'Image_lib.php'));
		
		$largePath  = $this->resizeLargeImage($originalPath, $fileName);
		$mediumPath = $this->resizeAndSaveMedium($largePath, $fileName);
		$thumbPath  = $this->resizeAndSaveThumb($mediumPath, $fileName);
		$tinyPath   = $this->resizeAndSaveTiny($thumbPath, $fileName);
		
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

