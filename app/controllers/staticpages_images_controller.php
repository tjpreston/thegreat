<?php

/**
 * StaticPagesImagesController
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
        // xdebug_break
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
       
        if (!empty($this->data)) {
            if ($this->StaticpagesImage->save($this->data)) {
                $this->Session->setFlash('Your data has been saved.');
                // $this->redirect(array('action' => 'index'));
            }
        }
    }

}


