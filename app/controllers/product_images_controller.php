<?php

/**
 * Product Images Controller
 * 
 */
class ProductImagesController extends AppController
{
	/**
	 * Array containing the names of components this controller uses.
	 *
	 * @var array
	 * @access public
	 */
	public $components = array('ImageNew');
	
	/**
	 * Admin
	 * Show image edit popup.
	 * 
	 * @param int $id
	 * @return void
	 * @access public
	 */
	public function admin_edit($id)
	{
		$this->sendNoCacheHeaders();
		$this->layout = 'admin_popup';

		$this->set('record', $this->ProductImage->findById($id));
		
	}
	
	/**
	 * Admin
	 * Crop image.
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_crop()
	{
		$id = $this->data['ProductImage']['id'];
		
		$record = $this->ProductImage->findById($id);

		$filename = $record['ProductImage']['filename'] . '.' . $record['ProductImage']['ext'];
		
		$originalPath = WWW_ROOT . 'img/products/original/' . $filename;
		$editedPath = WWW_ROOT . 'img/products/edited/' . $filename;

		$sourcePath = (file_exists($editedPath)) ? $editedPath : $originalPath;

		
		$resizeData = $this->data['ProductImage'];

		@unlink(WWW_ROOT . 'img/products/large/' . $filename);
		@unlink(WWW_ROOT . 'img/products/medium/' . $filename);
		@unlink(WWW_ROOT . 'img/products/thumb/' . $filename);
		@unlink(WWW_ROOT . 'img/products/tiny/' . $filename);

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

		// Upload large image
		$largePath = $this->uploadLargeProductImage($editedPath, $filename);
		
		$out = $largePath;
		foreach (array('medium', 'thumb', 'tiny') as $size)
		{
			$dest = WWW_ROOT . 'img/products/' . $size . '/' . $filename;
			$width = Configure::read('Images.product_' . $size . '_width');
			$height = Configure::read('Images.product_' . $size . '_height');

			$out = $this->ImageNew->resize($out, $dest, $width, $height, 'auto');
			$this->ImageNew->expandToFit($out, $width, $height);
		}
		
		$this->Session->setFlash('Image cropped.', 'default', array('class' => 'success'));
		$this->redirect('/admin/product_images/edit/' . $id);
			
	}
	
	/**
	 * Admin
	 * Rotate image.
	 * 
	 * @param int $id Product Image ID
	 * @return void
	 * @access public
	 */
	public function admin_rotate($id)
	{
		$ext = $this->ProductImage->field('ext', array('id' => $id));
		$this->Image->rotate($id, $ext);
		
		$this->redirect('/admin/product_images/edit/' . $id);
		
	}
	
	/**
	 * Admin
	 * Restore original image.
	 * 
	 * @param int $id Product Image ID
	 * @return void
	 * @access public
	 */
	public function admin_restore($id)
	{
		$productImage = $this->ProductImage->findById($id);
		
		$filename = $productImage['ProductImage']['filename'] . '.' . $productImage['ProductImage']['ext'];		
		$originalPath = WWW_ROOT . 'img/products/original/' . $filename;
		
		if (empty($originalPath))
		{
			return false;
		}
		
		@unlink(WWW_ROOT . 'img/products/edited/' . $filename);		
		@unlink(WWW_ROOT . 'img/products/large/' . $filename);
		@unlink(WWW_ROOT . 'img/products/medium/' . $filename);
		@unlink(WWW_ROOT . 'img/products/thumb/' . $filename);
		@unlink(WWW_ROOT . 'img/products/tiny/' . $filename);		
		
		App::import('Vendor', 'CI_Image_lib', array('file' => 'Image_lib.php'));
		
		
		// Upload large image
		$largePath = $this->uploadLargeProductImage($originalPath, $filename);
		
		$out = $largePath;
		foreach (array('medium', 'thumb', 'tiny') as $size)
		{
			$dest = WWW_ROOT . 'img/products/' . $size . '/' . $filename;
			$width = Configure::read('Images.product_' . $size . '_width');
			$height = Configure::read('Images.product_' . $size . '_height');

			$out = $this->ImageNew->resize($out, $dest, $width, $height, 'auto');
			$this->ImageNew->expandToFit($out, $width, $height);
		}
		
		$this->Session->setFlash('Image resorted to original.', 'default', array('class' => 'success'));
		$this->redirect('/admin/product_images/edit/' . $id);
		
	}
	
	/**
	 * Admin
	 * Delete image.
	 * 
	 * @param int $id Product Image ID
	 * @return void
	 * @access public
	 */
	public function admin_delete($id)
	{
		$productID = $this->ProductImage->field('product_id', array(
			'ProductImage.id' => $id
		));
		
		$this->ProductImage->delete($id);
		
		$this->Session->setFlash('Image deleted.', 'default', array('class' => 'success'));
		$this->redirect('/admin/products/edit/' . $productID  . '/tab:images');
		
	}






	/*
	public function get_image($output, $id)
	{
		$outputs = array('thumb', 'view', 'zoom');
		if (!in_array($output, $outputs))
		{
			// @TODO
		}
		
		$record = $this->ProductImage->findById($id);
		
		if (empty($record['ProductImage']['use_root_path']))
		{
			// @TODO
		}
		
		$file = $record['ProductImage']['use_root_path'];
		
		$resizeConfig = array(
			'image_library' 	=> 'GD2',
			'source_image'		=> $file,
			'width' 			=> Configure::read('Images.product_' . $output . '_width'),
			'height'			=> Configure::read('Images.product_' . $output . '_height'),
			'maintain_ratio'	=> true,
			'master_dim'		=> 'width',
			'dynamic_output'	=> true,
			'quality'			=> 90
		);
		
		App::import('Vendor', 'CI_Image_lib', array('file' => 'Image_lib.php'));
		$imageLib = new CI_Image_lib($resizeConfig);
		$imageLib->resize();
		
		exit;
		
	}
	*/

	/**
	 * Admin
	 * Generate thumbnail of image and send as response.
	 * 
	 * @param int $productImageID
	 * @return void
	 * @access public
	 */
	/*
	public function admin_get_thumb($id)
	{
		$record = $this->ProductImage->findById($id);
		
		if (!empty($record['ProductImage']['use_root_path']))
		{
			$file = $record['ProductImage']['use_root_path'];
			
			$resizeConfig = array(
				'image_library' 	=> 'GD2',
				'source_image'		=> $file,
				'width' 			=> Configure::read('Images.admin_product_thumb_width'),
				'height'			=> Configure::read('Images.admin_product_thumb_height'),
				'maintain_ratio'	=> true,
				'master_dim'		=> 'width',
				'dynamic_output'	=> true,
				'quality'			=> 90
			);
			
			App::import('Vendor', 'CI_Image_lib', array('file' => 'Image_lib.php'));
			$imageLib = new CI_Image_lib($resizeConfig);
			$imageLib->resize();
			
		}
		
		exit;
		
	}
	*/
	
}


