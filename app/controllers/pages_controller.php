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
	 * @var array
	 * @access public
	 */
	public $uses = array();
	
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
		
		$pages = Configure::read('Static.pages');
		
		$this->addCrumb('/pages/' . $page, $pages[$page]);
		$title_for_layout = $pages[$page];
		
		$this->set(compact('page', 'subpage', 'title_for_layout'));

		$this->set('isAjax', $this->RequestHandler->isAjax());

		//if($this->RequestHandler->isAjax()){
		//	$this->render('/elements/pages/' . $page);
		//} else {
			$this->render('template');
		//}
		
	}
	
}



