<?php

/**
 * Manufacturers Controller
 * 
 */
class ManufacturersController extends AppController
{

	/**
	 * Admin
	 * Display list of manufacturers.
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_index()
	{
		$this->paginate['limit'] = 1000;
		$this->set('records', $this->paginate());
	}
	
	/**
	 * Admin
	 * Edit a manufacturer record.
	 * 
	 * @param int $id [optional]
	 * @return void
	 * @access public
	 */
	public function admin_edit($id = null)
	{
		if (!empty($id) && is_numeric($id))
		{
			$record = $this->Manufacturer->find('first', array(
				'conditions' => array('Manufacturer.id' => $id),
				'recursive' => -1
			));
			
			if (empty($this->data))
			{
				$this->data = $record;
			}
		}
	}

	/**
	 * Admin
	 * Save manufacturer (new or existing)
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_save()
	{
		if ($this->Manufacturer->save($this->data))
		{
			$id = (empty($this->data['Manufacturer']['id'])) ? $this->Manufacturer->getInsertID() : $this->data['Manufacturer']['id'];
			
			if($this->_upload_image($id) == true){
				$this->Session->setFlash('Manufacturer saved.', 'default', array('class' => 'success'));
				$this->redirect('/admin/manufacturers/edit/' . $id);	
			} else {
				$this->setAction('admin_edit');
			}
			//$this->upload_landing_image($id);
			
		}
		else
		{
			$this->Session->setFlash('Manufacturer could not be saved. Please check the form for errors.', 'default', array('class' => 'failure'));
			return $this->setAction('admin_edit');	
		}
	}
	
	/**
	 * Admin
	 * Delete manufacturer
	 * 
	 * @param int $id
	 * @return void
	 * @access public
	 */
	public function admin_delete($id)
	{
		$record = $this->Manufacturer->findById($id);
		@unlink($record['Manufacturer']['header_root_path']);
		
		if ($this->Manufacturer->delete($id))
		{
			$this->Session->setFlash('Manufacturer deleted.', 'default', array('class' => 'success'));
			$this->redirect('/admin/manufacturers');
		}
		
		$this->Session->setFlash('Manufacturer not deleted.', 'default', array('class' => 'failure'));
		$this->redirect('/admin/manufacturers/edit/' . $id);	
		
	}

	/**
	 * Admin
	 * Delete manufacturer image.
	 * 
	 * @param int $id Man ID
	 * @return void
	 * @access public
	 */
	public function admin_delete_image($id, $type)
	{
		$record = $this->Manufacturer->findById($id);
		
		@unlink($record['Manufacturer']['root_path']);
		
		$this->Manufacturer->id = $id;
		$this->Manufacturer->saveField('img_ext', '', false);
		
		$this->Session->setFlash('Image deleted.', 'default', array('class' => 'success'));		
		$this->redirect('/admin/manufacturers/edit/' . $id);
		
	}
	
	public function admin_delete_landing_image($id, $type)
	{
		$record = $this->Manufacturer->findById($id);
		$ext = $record['Manufacturer']['landing_img_ext'];
		
		$path = WWW_ROOT . Configure::read('Images.manufacturer_landing_path') . $id . '.' . $ext;
		
		@unlink($path);
		
		$this->Manufacturer->id = $id;
		$this->Manufacturer->saveField('landing_img_ext', '', false);
		
		$this->Session->setFlash('Image deleted.', 'default', array('class' => 'success'));		
		$this->redirect('/admin/manufacturers/edit/' . $id);
		
	}
	
	
	
	private function upload_landing_image($id)
	{
		$tempFile = $this->data['Manufacturer']['landing_image']['tmp_name'];
		
		if (!is_uploaded_file($tempFile))
		{
			return false;
		}
		
		$info = pathinfo($this->data['Manufacturer']['landing_image']['name']);
		$ext = $info['extension'];
		
		$saveToPath = WWW_ROOT . Configure::read('Images.manufacturer_landing_path') . $id . '.' . $ext;
		
		move_uploaded_file($tempFile, $saveToPath);
		
		$this->Manufacturer->id = $id;
		$this->Manufacturer->save(array('Manufacturer' => array(
			'landing_img_ext' => $ext,
		)), false);
		
		return true;
	
	}
	
	/**
	 * Upload category image
	 * 
	 * @TODO: delete cached copies
	 * 
	 * @param int $id Category ID
	 * @param string $type Header/List
	 * @return void
	 * @access public
	 */
	private function _upload_image($id)
	{		
		$tempFile = $this->data['Manufacturer']['image']['tmp_name'];
		
		if (!is_uploaded_file($tempFile))
		{
			return true;
		}
		
		$size = getimagesize($tempFile);
		$width = $resizeWidth = $size[0];
		$height = $resizeHeight = $size[1];
		
		$info = pathinfo($this->data['Manufacturer']['image']['name']);
		$ext = $info['extension'];
		
		$saveToPath = WWW_ROOT . Configure::read('Images.manufacturer_path') . $id . '.' . $ext;
		
		if ($width > Configure::read('Images.manufacturer_width'))
		{
			$resizeWidth = Configure::read('Images.manufacturer_width');
		}
		
		if ($height > Configure::read('Images.manufacturer_height'))
		{
			$resizeHeight = Configure::read('Images.manufacturer_height');
		}
		
		$resizeConfig = array(
			'image_library' 	=> 'GD2',
			'source_image'		=> $tempFile,
			'new_image'			=> $saveToPath,
			'width' 			=> $resizeWidth,
			'height'			=> $resizeHeight,
			'maintain_ratio'	=> true,
			'master_dim'		=> Configure::read('Images.manufacturer_master_dim'),
			'quality'			=> 90
		);
		
		App::import('Vendor', 'CI_Image_lib', array('file' => 'Image_lib.php'));
		$imageLib = new CI_Image_lib($resizeConfig);
		
		if (!$imageLib->resize())
		{
			die($imageLib->display_errors('<br />', ''));
		}

		/* Expand image to fit */
		$path = $saveToPath;
		$maxWidth = Configure::read('Images.manufacturer_width');
		$maxHeight = Configure::read('Images.manufacturer_height');

		$imgFunctions = array(
			'jpg' => array('imagecreatefrom' => 'imagecreatefromjpeg', 'image' => 'imagejpeg'),
			'png' => array('imagecreatefrom' => 'imagecreatefrompng', 'image' => 'imagepng'),
			'gif' => array('imagecreatefrom' => 'imagecreatefromgif', 'image' => 'imagegif'),
		);

		$dims = getimagesize($path);
		
		$isTooNarrow = $dims[0] < $maxWidth;
		$isTooShort  = $dims[1] < $maxHeight;
		
		if ($isTooNarrow || $isTooShort)
		{
			//$image = imagecreatefromjpeg($path);]
			
			if ($isTooNarrow)
			{
				$image = $imgFunctions[strtolower($ext)]['imagecreatefrom']($path);

				$frame = imagecreatetruecolor($maxWidth, $maxHeight);
				$fillColor = imagecolorallocate($frame, 255, 255, 255);
				imagefill($frame, 0, 0, $fillColor);
				
				$tooNarrowByPixels = $maxWidth - $dims[0];
				$leftSidePadding = round($tooNarrowByPixels / 2);
				imagecopymerge($frame, $image, $leftSidePadding, 0, 0, 0, $dims[0], $dims[1], 100);

				$imgFunctions[strtolower($ext)]['image']($frame, $saveToPath);
			}
			
			if($isTooShort)
			{
				$image = $imgFunctions[strtolower($ext)]['imagecreatefrom']($path);

				$frame = imagecreatetruecolor($maxWidth, $maxHeight);
				$fillColor = imagecolorallocate($frame, 255, 255, 255);
				imagefill($frame, 0, 0, $fillColor);

				$tooShortByPixels = $maxHeight - $dims[1];
				$topSidePadding = round($tooShortByPixels / 2);
				imagecopymerge($frame, $image, 0, $topSidePadding, 0, 0, $dims[0], $dims[1], 100);

				$imgFunctions[strtolower($ext)]['image']($frame, $saveToPath);
			}
			
			
		}

		
		$filesize = filesize($saveToPath);

		$this->Manufacturer->id = $id;
		$this->Manufacturer->save(array('Manufacturer' => array(
			'img_ext' => $ext,
		)), false);
		
		return true;
		
	}
	
	
	
}


