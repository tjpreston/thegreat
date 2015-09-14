<?php

/**
 * News Controller
 *
 */
class NewsController extends AppController
{
	/**
	 * An array containing the class names of models this controller uses.
	 *
	 * @var array
	 * @access public
	 */
	public $uses = array('Article');
	
	/**
	 * Array containing the names of components this controller uses.
	 *
	 * @var array
	 * @access public
	 */
	public $components = array('ImageNew', 'RequestHandler');
	
	/**
	 * An array containing the names of helpers this controller uses. 
	 *
	 * @var mixed A single name as a string or a list of names as an array.
	 * @access public
	 */
	public $helpers = array('Time', 'Text', 'Rss');

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
	 * List articles.
	 * 
	 * @return void
	 * @access public
	 */
	public function index()
	{
		$records = $this->Article->find('all');
		
		$this->addCrumb('/news', 'News');
		$this->set('records', $records);
	
	}

	public function newsfeed()
	{
		Configure::write('debug', 0);
		$posts = $this->Article->find('all', array('limit' => 20, 'order' => 'Article.created DESC'));
		return $this->set(compact('posts'));
	}
	
	
	/**
	 * View article.
	 * 
	 * @param string $slug
	 * @return void
	 * @access public
	 */
	public function view($slug)
	{
		$record = $this->Article->findBySlug($slug);
		
		if (empty($record))
		{
			$this->redirect('/news');
		}
		
		$this->addCrumb('/news', 'News');
		$this->addCrumb('/news/' . $record['Article']['slug'], $record['Article']['name']);
		
		$this->set('record', $record);
		
	}
	
	/**
	 * Admin
	 * Display list of records.
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
	 * Edit a Article record.
	 * 
	 * @param int $id [optional]
	 * @return void
	 * @access public
	 */
	public function admin_edit($id = null)
	{
		if (!empty($id) && is_numeric($id))
		{
			$record = $this->Article->find('first', array(
				'conditions' => array('Article.id' => $id),
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
	 * Save Article (new or existing).
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_save()
	{
		if ($this->Article->save($this->data))
		{
			$id = (empty($this->data['Article']['id'])) ? $this->Article->getInsertID() : $this->data['Article']['id'];
			
			$this->upload_file($id, $this->data['Article']['name']);

			$this->Session->setFlash('Article saved.', 'default', array('class' => 'success'));
			$this->redirect('/admin/news/edit/' . $id);	
		}
		else
		{
			$this->Session->setFlash('Article could not be saved. Please check the form for errors.', 'default', array('class' => 'failure'));
			return $this->setAction('admin_edit');	
		}
	}

	/**
	 * Admin
	 * Delete Article.
	 * 
	 * @param int $id
	 * @return void
	 * @access public
	 */
	public function admin_delete($id)
	{
		if ($this->Article->delete($id))
		{
			$this->Session->setFlash('Article deleted.', 'default', array('class' => 'success'));
			$this->redirect('/admin/news');
		}
		
		$this->Session->setFlash('Article not deleted.', 'default', array('class' => 'failure'));
		$this->redirect('/admin/news/edit/' . $id);
		
	}
	
	/**
	 * Upload file.
	 * 
	 * @param int $id
	 * @return void
	 * @access public
	 */
	private function upload_file($id, $name)
	{
		$file = $this->data['Article']['file'];
		
		if (!empty($file['error']) || !is_uploaded_file($file['tmp_name']))
		{
			return false;
		}
		
		$pathInfo = pathinfo($file["name"]);
		$filenameNoExt = Inflector::slug(strtolower($name));
		$filename = $filenameNoExt . '.' .  $pathInfo['extension'];
		
		$source = $file['tmp_name'];
		$dest = WWW_ROOT . Configure::read('News.image_1_path') . $filename;
		
		App::import('Vendor', 'CI_Image_lib', array('file' => 'Image_lib.php'));
		
		$maxWidth  = Configure::read('News.image_1_width');
		$maxHeight = Configure::read('News.image_1_height');		
		
		$this->ImageNew->resize($source, $dest, $maxWidth, $maxHeight, 'width');
		
		$this->Article->id = $id;
		$this->Article->save(array('Article' => array(
			'filename1'  => $filenameNoExt,
			'ext1'	 	=> $pathInfo['extension']
		)), false);
		
		return true;

	}
	
	public function admin_delete_image($id)
	{
		$record = $this->Article->findById($id);
		
		$path = WWW_ROOT . Configure::read('News.image_1_path') . $record['Article']['filename1'] . '.' . $record['Article']['ext1'];
		
		@unlink($path);
		
		$this->Article->id = $id;
		$this->Article->save(array('Article' => array(
			'filename1' => '',
			'ext1' => ''
		)), false);
		
		$this->Session->setFlash('Article image deleted.', 'default', array('class' => 'success'));
		$this->redirect('/admin/news/edit/' . $id);

	}


}

