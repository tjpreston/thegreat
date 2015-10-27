<?php

/**
 * StaticpagesImagesController
 * 
 */
class StaticpagesImagesController extends AppController
{

    public $helpers = array('Form', 'Session');

    public function admin_index()
    {
       $pagedata = $this->StaticpagesImage->find('all', array('fields' => array('StaticpagesImage.id', 'StaticpagesImage.name')));
        $this->set('pagedata', $pagedata);
    }

    /**
     * Admin
     * Edit existing product for editing.
     * 
     * @param  $id
     * @return void
     * @access public
     */
    public function admin_edit($id = null)
    {
        if (is_null($id))
        {
            $this->redirect('/admin/staticpages');
        }

        if (empty($this->data))
        {
            $this->data = $this->StaticpagesImage->findById($id);
        }
        else
        {

            if ($this->StaticpagesImage->save($this->data))
            {
                $this->Session->setFlash('Your data has been saved.');
                $this->redirect(array('action' => 'index'));
            }
        }
        $pagedata = $this->StaticpagesImage->find('first', array('conditions' => array('StaticpagesImage.id' => $id), 'fields' => array('StaticpagesImage.id', 'StaticpagesImage.name')));
        $pagetitle = $pagedata['StaticpagesImage']['name'];
        $this->set('pagetitle', $pagetitle);
    }

    public function admin_save()
    {
        if (!empty($this->data))
        {
            $this->uploadAndReplaceImage($this->data['StaticpagesImage']['currentFilename'], $this->data['StaticpagesImage']['filename']);
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

        $pathToOriginal = WWW_ROOT . $originalFileLocation; // tack on webroot
        move_uploaded_file($file['tmp_name'], $pathToOriginal);

        return true;
    }

}
