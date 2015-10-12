<?php

/**
 * StaticpagesController
 * 
 */
class StaticpagesController extends AppController {

    /**
     * An array containing the class names of models this controller uses.
     *
     * @var array
     * @access public
     */
    public $uses = array('Staticpage','StaticpagesImage');

    public $helpers = array('Form', 'Session');

    public function admin_index() {
        //  'fields' => array('Model.field1',
        
        $pagedata = $this->Staticpage->find('all', array('fields' => array('Staticpage.id', 'Staticpage.name'), 'recursive' => -1));
        $this->set('pagedata', $pagedata);


        // $pagetitle = array();
        // Haven't got time to do this properly so leads to the view having to
        // index the array $value['Staticpage']['name'] instead of the more 
        // sensible $value['name'] etc.
        //  foreach ($allpagedata as $value)
        //  {
        //    $pagetitle['id'] = $value['Staticpage']['id'];
        //    $pagetitle[] = $value['Staticpage']['name'];
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
        
        $record = $this->Staticpage->findById($id);
        
        
        
        
        
        if (empty($this->data)) {
//        $this->request->data = $this->Staticpage->find('first', array('conditions' => array('Staticpage.id' => $id)));
        //    $this->data = $this->Staticpage->findById($id);
            $this->data = $record;
            $images = $this->Staticpage->StaticpagesImage->getImages($id);
            $this->set('images',$images);
            
        } else {
            if ($this->Staticpage->save($this->data)) {
                $this->Session->setFlash('Your data has been saved.', 'default', array('class' => 'success'));
                $this->redirect(array('action' => 'index'));
            }
        }
        
        $pagedata = $this->Staticpage->find('first', array('conditions' => array('Staticpage.id' => $id), 'fields' => array('Staticpage.id', 'Staticpage.name')));
        $pagetitle = $pagedata['Staticpage']['name'];
        $this->set('pagetitle', $pagetitle);
    }

    public function admin_save() {
        xdebug_break();
        if (!empty($this->data)) {
            if ($this->Staticpage->save($this->data)) {
                $this->Session->setFlash('Your data has been saved.');
                $this->redirect(array('action' => 'index'));
            }
        }
    }

}
