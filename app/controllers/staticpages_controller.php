<?php

/**
 * StaticPageController
 * 
 */
class StaticpagesController extends AppController {

    /**
     * An array containing the class names of models this controller uses.
     *
     * @var array
     * @access public
     */
    // public $uses = array('StaticPage');

    public $helpers = array('Form');
    public function admin_index() {
        //  'fields' => array('Model.field1',
        $pagedata = $this->Staticpage->find('all', array('fields' => array('Staticpage.id', 'Staticpage.name')));
        $this->set('pagedata', $pagedata);


        // $pagetitle = array();
        // Haven't got time to do this properly
        //  foreach ($allpagedata as $value)
        //  {
        //    $pagetitle['id'] = $value['Staticpage']['id'];
        //    $pagetitle[] = $value['Staticpage']['name'];
        //  }
    }

    /**
     * Admin
     * View existing product for editing.
     * 
     * @param  $id
     * @return void
     * @access public
     */
    public function admin_edit($id = null) {
        xdebug_break();
        if (is_null($id)) {
            $this->redirect('/admin/staticpages');
        }
       // if (empty($this->data)) {
//        $this->request->data = $this->Staticpage->find('first', array('conditions' => array('Staticpage.id' => $id)));
        $tmp = $this->Staticpage->findById($id);
        $this->data = $tmp;
       // } else {
            // Save logic goes here
        //}
        $pagedata = $this->Staticpage->find('first', array('conditions' => array('Staticpage.id' => $id), 'fields' => array('Staticpage.id', 'Staticpage.name')));
        $pagetitle = $pagedata['Staticpage']['name'];
        $this->set('pagetitle', $pagetitle);
    }

}
