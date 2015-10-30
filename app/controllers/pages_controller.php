<?php

/**
 * Static content controller
 *
 */
class PagesController extends AppController
{
	/**
	 * An array containing the class names of models this controller uses.
	 *
         * In this case we use the Staticpage model as we need to get the
         * homepage description and images from the staticpages table. - TJP
         * 
	 * @var array
	 * @access public
	 */
	public $uses = array('Staticpage');
	
	/**
	 * Called before the controller action.
	 *
	 * @return void
	 * @access public
	 */
	function beforeFilter() 
	{
		$this->Auth->allow('*');
		parent::beforeFilter();
		$this->Security->disabled = true;
	}
	
	/**
	 * Displays a view
	 *
	 * @param mixed What page to display
	 * @access public
	 */
	function display()
	{
		$path = func_get_args();
		$count = count($path);
		if (!$count)
		{
			$this->redirect('/');
		}
		
		$page = $subpage = null;
		if (!empty($path[0]))
		{
			$page = $path[0];
		}
		if (!empty($path[1]))
		{
			$subpage = $path[1];
		}
		// This is popcorn stuff slight clash of naming but
                // whatcha gonna do? - TJP 7/10/15 
		$pages = Configure::read('Static.pages');
		
		$this->addCrumb('/pages/' . $page, $pages[$page]);
		xdebug_break();
                $title_for_layout = $pages[$page];
		
		$this->set(compact('page', 'subpage', 'title_for_layout'));

		$this->set('isAjax', $this->RequestHandler->isAjax());
               
                // Yes this is a hack but I don't care - TJP 7/10/15
                if(!strcasecmp($title_for_layout,'shop'))
                {
                        $title_for_layout = 'Our shop';
                }
                $this->set('pagedata', $this->Staticpage->find('first', 
                array('conditions' => array('Staticpage.name' => $title_for_layout))));
		//if($this->RequestHandler->isAjax()){
		//	$this->render('/elements/pages/' . $page);
		//} else {
                xdebug_break();
			$this->render('template');
		//}
		
	}
	
}



