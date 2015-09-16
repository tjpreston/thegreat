<?php

/**
 * Config Controller
 * 
 */
class ConfigController extends AppController
{
	/**
	 * Admin
	 * Show config page
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_index()
	{
		$this->data = $this->Config->find('first', array(
			'recursive' => -1
		));
		
		$this->Config->bindHomepage($this->Config, null, false);
		$this->data['ConfigHomepage'] = $this->Config->ConfigHomepage->getConfig();
		
		$languages = $this->Language->find('list');		
		$this->set(compact('languages'));
		
	}
	
	/**
	 * Admin
	 * Save config.
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_save()
	{
		$this->Config->bindHomepage($this->Config, null, false);
		
		$this->data['Config']['id'] = Configure::read('Site.use_config');

		if ($this->Config->saveAll($this->data))
		{
			$this->Session->setFlash('Site configuration saved.', 'default', array('class' => 'success'));
			$this->redirect('/admin/config');
		}
		else
		{
			$this->Session->setFlash('Site configuration could not be saved. Please check the form for errors.', 'default', array('class' => 'failure'));
			return $this->setAction('admin_index');
		}
		
	}
	
	
}
