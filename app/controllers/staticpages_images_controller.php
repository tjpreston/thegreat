<?php

/**
 * StaticpagesImagesController
 * 
 */
class StaticpagesImagesController extends AppController {

    /**
     * An array containing the class names of models this controller uses.
     *
     * @var array
     * @access public
     */
    // public $uses = array('StaticPagesImage');

    public $helpers = array('Form', 'Session');

    public function admin_index() {
        //  'fields' => array('Model.field1',
        
        $pagedata = $this->StaticpagesImage->find('all', array('fields' => array('StaticpagesImage.id', 'StaticpagesImage.name')));
        $this->set('pagedata', $pagedata);


        // $pagetitle = array();
        // Haven't got time to do this properly so leads to the view having to
        // index the array $value['StaticpagesImage']['name'] instead of the more 
        // sensible $value['name'] etc.
        //  foreach ($allpagedata as $value)
        //  {
        //    $pagetitle['id'] = $value['StaticpagesImage']['id'];
        //    $pagetitle[] = $value['StaticpagesImage']['name'];
        //  }
    }

    /**
     * Admin
     * Edit existing product for editing.
     * 
     * @param  $id
     * @return void
     * @access public
     */
    public function admin_edit($id = null) {
        if (is_null($id)) {
            $this->redirect('/admin/staticpages');
        }
        if (empty($this->data)) {
//        $this->request->data = $this->StaticpagesImage->find('first', array('conditions' => array('StaticpagesImage.id' => $id)));
            $this->data = $this->StaticpagesImage->findById($id);
        } else {
            if ($this->StaticpagesImage->save($this->data)) {
                $this->Session->setFlash('Your data has been saved.');
                $this->redirect(array('action' => 'index'));
            }
        }
        $pagedata = $this->StaticpagesImage->find('first', array('conditions' => array('StaticpagesImage.id' => $id), 'fields' => array('StaticpagesImage.id', 'StaticpagesImage.name')));
        $pagetitle = $pagedata['StaticpagesImage']['name'];
        $this->set('pagetitle', $pagetitle);
    }

    public function admin_save() {
       // xdebug_break();
        if (!empty($this->data)) {
//            if ($this->StaticpagesImage->save($this->data)) {
//                $this->Session->setFlash('Your data has been saved.');
//                // $this->redirect(array('action' => 'index'));
//            }
       
            $this->uploadAndReplaceImage($this->data['currentFilename'], $this->data['StaticpagesImage']['filename']);
          //  $this->uploadVarImages();

            $this->Session->setFlash('Image saved.', 'default', array('class' => 'success'));
            $this->redirect('/admin/staticpages');
            
        }
    }
/**
	 * Upload static pages image. Copy and pasted from products_controller
         * then edited to work with staticpages - TJP
	 * 
	 * @param int $staticPageImageID
	 * @param array $file
	 * @param string $sku
	 * @return bool
	 * @access private
	 */
	private function uploadAndReplaceImage($originalFileLocation, $file)
	{
		if (empty($file) || !empty($file['error']) || !is_uploaded_file($file['tmp_name']))
		{
			return false;
		}

		
		
//		$pathInfo = pathinfo($file["name"]);
//		$filenameNoExt = Inflector::slug(strtolower($sku)) . '-' . $imageID;
//		$filename = $filenameNoExt . '.' .  $pathInfo['extension'];

		$pathToOriginal = WWW_ROOT . $originalFileLocation; // tack on webroot
		
		move_uploaded_file($file['tmp_name'], $pathToOriginal);
		
		//App::import('Vendor', 'CI_Image_lib', array('file' => 'Image_lib.php'));
		
		// Upload large image
		/*$largePath = $this->uploadLargeProductImage($pathToOriginal, Configure::read('Images.product_large_path'), $filename);
		
		$out = $largePath;
		foreach (array('medium', 'thumb', 'tiny') as $size)
		{
			$dest = WWW_ROOT . 'img/products/' . $size . '/' . $filename;
			$width = Configure::read('Images.product_' . $size . '_width');
			$height = Configure::read('Images.product_' . $size . '_height');

			$out = $this->ImageNew->resize($pathToOriginal, $dest, $width, $height, 'auto');
			$this->ImageNew->expandToFit($out, $width, $height);
		}*/

//		App::import('Lib', 'EsperImage');
//		$options = array('saveFilename' => $filename);
//		$esperImage = new EsperImage($pathToOriginal, $options);
//		$esperImage->resizeAll();
//		
//		$this->Product->ProductImage->id = $imageID;
//		$this->Product->ProductImage->save(array('ProductImage' => array(
//			'filename'  => $filenameNoExt,
//			'ext'	 	=> $pathInfo['extension']
//		)));

		return true;
		
	}
}


