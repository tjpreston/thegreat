<?php

/**
 * Categories Controller
 * 
 */
class CategoriesController extends AppController
{	
	/**
	 * Array containing the names of components this controller uses.
	 *
	 * @var array
	 * @access public
	 */
	public $components = array('ImageNew');

	/**
	 * Holds pagination defaults for controller actions.
	 *
	 * @var array
	 * @access public
	 */
	public $paginate = array('limit' => 20);
	
	/**
	 * Get category thumbnail.
	 * 
	 * 
	 * @param int $id Category ID
	 * @param string $type Header/List
	 * @param string $page Page
	 * @return void
	 * @access public
	 */
	public function get_thumb($id, $type, $admin = false)
	{
		$record = $this->Category->findById($id);
		
		if (!empty($record['Category'][$type . '_web_path']))
		{
			$width = Configure::read('Images.category_' . $type . '_width');
			$height = Configure::read('Images.category_' . $type . '_height');
			
			if (!empty($admin))
			{
				$width = Configure::read('Images.admin_category_thumb_width');
				$height = Configure::read('Images.admin_category_thumb_height');		
			}
			
			$resizeConfig = array(
				'image_library' 	=> 'GD2',
				'source_image'		=> $record['Category'][$type . '_root_path'],
				'width' 			=> $width,
				'height'			=> $height,
				'maintain_ratio'	=> true,
				'master_dim'		=> Configure::read('Images.category_' . $type . '_master_dim'),
				'dynamic_output'	=> true,
				'quality'			=> Configure::read('Images.upload_quality')
			);
			
			App::import('Vendor', 'CI_Image_lib', array('file' => 'Image_lib.php'));
			$imageLib = new CI_Image_lib($resizeConfig);
			$imageLib->resize();
			
		}
		
		exit;
		
	}

	/**
	 * Admin
	 * Display list of categories.
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_index($id = null)
	{	
		$this->Category->bindName($this->Category, null, true);
		$this->Category->bindDescription($this->Category, null, true);
		$this->Category->bindFeaturedProducts($this->Category, false);
		
		if (Configure::read('Catalog.show_manu_landing_page'))
		{
			$this->Category->bindFeaturedManufacturers(false);
		}
		
		$this->Category->unbindProducts();
		
		$record = array();
		$descriptions = array();
		
		if (!empty($id) && is_numeric($id))
		{
			$record = $this->Category->find('first', array(
				'conditions' => array('Category.id' => $id),
				'recursive' => -1
			));

			if (empty($record))
			{
				$this->redirect('/admin/categories');
			}
			
			$record['CategoryName'] = $this->Category->CategoryName->getNames($id);
			$record['CategoryDescription'] = $this->Category->CategoryDescription->getDescriptions($id);
			
			$record['CategoryFeaturedProduct'] = $this->Category->CategoryFeaturedProduct->find('list', array(
				'fields' => array('CategoryFeaturedProduct.product_id', 'CategoryFeaturedProduct.product_id'),
				'conditions' => array('CategoryFeaturedProduct.category_id' => $id)
			));

			if (Configure::read('Catalog.show_manu_landing_page'))
			{
				$this->Category->CategoryFeaturedManufacturer->bindModel(array('belongsTo' => array('Manufacturer')));
				$record['CategoryFeaturedManufacturer'] = $this->Category->CategoryFeaturedManufacturer->find('all', array(
					'conditions' => array('CategoryFeaturedManufacturer.category_id' => $id),
					'order' => array('CategoryFeaturedManufacturer.sort')
				));

				$manufacturers = $this->Category->CategoryFeaturedManufacturer->Manufacturer->find('list', array(
					'fields' => array('id', 'name')
				));

				$this->set('manufacturers', $manufacturers);

			}
			
			$categoryProducts = array();
			$availableProducts = array();
			
			$categoryProducts = $this->Category->Product->ProductCategory->getProductsInCategory($id);
			$categoryProductsList = Set::extract('{n}.Product.id', $categoryProducts);
			$conditions = array('NOT' => array('Product.id' => $categoryProductsList));
			$availableProducts = $this->Category->Product->getList($conditions);
			
			$this->set(compact('record', 'categoryProducts', 'availableProducts'));

			if (empty($this->data))
			{
				$this->data = $record;
			}
			
		}
		else if (!empty($id) && ($id == 'new'))
		{
			$this->set('newRecord', true);
		}
		
		$this->Category->bindName($this->Category, 0, false);
		$treeList = $this->Category->generatetreelist(null, '{n}.Category.id', '{n}.CategoryName.name', ' - ', 1);
		$this->set('treeList', $treeList);
		
		$this->Category->recursive = 1;
		$this->set('categories', $this->Category->find('threaded'));
		$this->set('languages', $this->Category->CategoryName->Language->find('list'));
		
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
		$this->Category->bindName($this->Category, null, false);
		$this->Category->bindDescription($this->Category, null, false);

		if (Configure::read('Catalog.show_manu_landing_page'))
		{
			$this->Category->bindFeaturedManufacturers(false);
		}
		if ($this->Category->saveAll($this->data))
		{
			$id = (empty($this->data['Category']['id'])) ? $this->Category->getInsertID() : $this->data['Category']['id'];
			
			$this->uploadImage($id, 'header');
			$this->uploadImage($id, 'list');
			
			$this->Session->setFlash('Category saved.', 'default', array('class' => 'success'));
			$this->redirect('/admin/categories/index/' . $id);
		}
		else
		{
			$id = (!empty($this->data['Category']['id'])) ? $this->data['Category']['id'] : 'new';
			$this->Session->setFlash('Category could not be saved. Please check the form for errors.', 'default', array('class' => 'failure'));
			return $this->setAction('admin_index', $id);			
		}
		
	}
	
	/**
	 * Admin
	 * Delete category.
	 * 
	 * @param int $id Category ID
	 * @return void
	 * @access public
	 */
	public function admin_delete($id)
	{
		$this->Category->bindName($this->Category, null, false);
		$this->Category->bindDescription($this->Category, null, false);
		
		// Check if used as main category
		/*
		$mainCatProducts = $this->Category->Product->findByMainCategoryId($id);
		if (!empty($mainCatProducts))
		{
			$this->Session->setFlash('Category assigned to product(s). Cannot delete.', 'default', array('class' => 'failure'));
			$this->redirect('/admin/categories/index/' . $id);
		}
		*/
		
		$record = $this->Category->findById($id);		
		@unlink($record['Category']['header_root_path']);
		@unlink($record['Category']['list_root_path']);
		
		$this->Category->delete($id);
		
		$this->Session->setFlash('Category deleted.', 'default', array('class' => 'success'));
		
		$this->redirect('/admin/categories');
		
	}

	public function admin_edit_image($type, $id)
	{
		$this->sendNoCacheHeaders();
		$this->layout = 'admin_popup';

		$this->set('type', $type);
		$this->set('record', $this->Category->findById($id));

	}
	
	/**
	 * Admin
	 * Delete category image.
	 * 
	 * @param int $id Category ID
	 * @return void
	 * @access public
	 */
	public function admin_delete_image($id, $type)
	{
		$validTypes = array('header', 'list');
		
		if (!in_array($type, $validTypes))
		{
			$this->redirect('/admin/categories/index/' . $id);
		}
		
		$record = $this->Category->findById($id);
		
		@unlink($record['Category'][$type . '_root_path']);
		
		$this->Category->id = $id;
		$this->Category->saveField('img_' . $type . '_ext', '', false);
		
		$this->Session->setFlash('Category ' . $type . ' image deleted.', 'default', array('class' => 'success'));		
		$this->redirect('/admin/categories/index/' . $id);
		
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
		$id = $this->data['Category']['id'];
		$type = $this->data['Category']['type'];
		
		$record = $this->Category->findById($id);

		$filename = $id . '.' . $record['Category']['img_' . $type . '_ext'];
		
		$originalPath = WWW_ROOT . 'img/categories/' . $type . '/original/' . $filename;
		$editedPath = WWW_ROOT . 'img/categories/' . $type . '/edited/' . $filename;
		
		$sourcePath = (file_exists($editedPath)) ? $editedPath : $originalPath;

		$resizeData = $this->data['Category'];

		@unlink($saveToPath);

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

		
		$saveToPath = WWW_ROOT . 'img/categories/' . $type . '/' . $filename;

		$width = Configure::read('Images.category_' . $type . '_width');
		$height = Configure::read('Images.category_' . $type . '_height');
		
		$masterDim = Configure::read('Images.category_' . $type . '_master_dim');
		$this->ImageNew->resize($editedPath, $saveToPath, $width, $height, $masterDim);
		
		$resizedSize = getimagesize($saveToPath);
		$resizedWidth = $resizedSize[0];
		$resizedHeight = $resizedSize[1];
		
		// If it's still too tall
		if (($masterDim == 'width') && ($resizedHeight > $height))
		{
			$this->ImageNew->resize($saveToPath, $saveToPath, $width, $height, 'height');
		}
		// If it's still too wide
		else if (($masterDim == 'height') && ($resizedWidth > $width))
		{
			$this->ImageNew->resize($saveToPath, $saveToPath, $width, $height, 'width');
		}

		$this->ImageNew->expandToFit($saveToPath, $width, $height);
		
		
		$this->Session->setFlash('Image cropped.', 'default', array('class' => 'success'));
		$this->redirect('/admin/categories/edit_image/' . $type . '/' . $id);
			
	}

	/**
	 * Admin
	 * Restore original image.
	 * 
	 * @param int $id Product Image ID
	 * @return void
	 * @access public
	 */
	public function admin_restore_image($type, $id)
	{
		$record = $this->Category->findById($id);
		
		$ext = $record['Category']['img_' . $type . '_ext'];
		$filename = $record['Category']['id'] . '.' . $ext;
		
		$pathToOriginal = WWW_ROOT . 'img/categories/' . $type . '/original/' . $filename;
		
		if (empty($pathToOriginal))
		{
			return false;
		}
		
		@unlink(WWW_ROOT . 'img/categories/' . $type . '/edited/' . $filename);
		@unlink(WWW_ROOT . 'img/categories/' . $type . '/' . $filename);
		
		App::import('Vendor', 'CI_Image_lib', array('file' => 'Image_lib.php'));


		$saveToPath = WWW_ROOT . Configure::read('Images.category_' . $type . '_path') . $filename;

		$width = Configure::read('Images.category_' . $type . '_width');
		$height = Configure::read('Images.category_' . $type . '_height');

		$masterDim = Configure::read('Images.category_' . $type . '_master_dim');
		$this->ImageNew->resize($pathToOriginal, $saveToPath, $width, $height, $masterDim);
		
		$resizedSize = getimagesize($saveToPath);
		$resizedWidth = $resizedSize[0];
		$resizedHeight = $resizedSize[1];

		// If it's still too tall
		if (($masterDim == 'width') && ($resizedHeight > $height))
		{
			$this->ImageNew->resize($saveToPath, $saveToPath, $width, $height, 'height');
		}
		// If it's still too wide
		else if (($masterDim == 'height') && ($resizedWidth > $width))
		{
			$this->ImageNew->resize($saveToPath, $saveToPath, $width, $height, 'width');
		}

		$this->ImageNew->expandToFit($saveToPath, $width, $height);


		$this->Category->id = $id;
		$this->Category->save(array('Category' => array(
			'img_' . $type . '_ext' => $ext,
		)), false);

		$this->Session->setFlash('Image resorted to original.', 'default', array('class' => 'success'));
		$this->redirect('/admin/categories/edit_image/' . $type . '/' . $id);

	}

	/**
	 * Upload image.
	 *
	 * @param int $id
	 * @param string $type
	 * @return mixed
	 * @acces private
	 */
	private function uploadImage($id, $type)
	{
		$tempFile = $this->data['Category'][$type . '_image']['tmp_name'];
		
		if (!is_uploaded_file($tempFile))
		{
			return false;
		}
		
		$info = pathinfo($this->data['Category'][$type . '_image']['name']);
		$ext = $info['extension'];
		
		// $pathToOriginal = WWW_ROOT . 'img/categories/' . $type . '/original/' . $id . '.' . $ext;
		// $editedPath = WWW_ROOT . 'img/categories/' . $type . '/edited/' . $id . '.' . $ext;
		
		// @unlink($editedPath);
		
		// move_uploaded_file($tempFile, $pathToOriginal);
		
		App::import('Vendor', 'CI_Image_lib', array('file' => 'Image_lib.php'));
		
		$saveToPath = WWW_ROOT . Configure::read('Images.category_' . $type . '_path') . $id . '.' . $ext;
		
		$width = Configure::read('Images.category_' . $type . '_width');
		$height = Configure::read('Images.category_' . $type . '_height');
		
		$masterDim = Configure::read('Images.category_' . $type . '_master_dim');
		$this->ImageNew->resize($tempFile, $saveToPath, $width, $height, $masterDim);
		
		$resizedSize = getimagesize($saveToPath);
		$resizedWidth = $resizedSize[0];
		$resizedHeight = $resizedSize[1];
		
		// If it's still too tall
		if (($masterDim == 'width') && ($resizedHeight > $height))
		{
			$this->ImageNew->resize($saveToPath, $saveToPath, $width, $height, 'height');
		}
		// If it's still too wide
		else if (($masterDim == 'height') && ($resizedWidth > $width))
		{
			$this->ImageNew->resize($saveToPath, $saveToPath, $width, $height, 'width');
		}

		$this->ImageNew->expandToFit($saveToPath, $width, $height);


		$this->Category->id = $id;
		$this->Category->save(array('Category' => array(
			'img_' . $type . '_ext' => $ext,
		)), false);

	}

	
}


