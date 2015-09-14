<?php

/**
 * Documents Controller
 * 
 */
class DocumentsController extends AppController
{
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
	 * Edit a document record.
	 * 
	 * @param int $id [optional]
	 * @return void
	 * @access public
	 */
	public function admin_edit($id = null)
	{
		if (!empty($id) && is_numeric($id))
		{
			$record = $this->Document->find('first', array(
				'conditions' => array('Document.id' => $id),
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
	 * Save document (new or existing).
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_save()
	{
		if ($this->Document->save($this->data))
		{
			$id = (empty($this->data['Document']['id'])) ? $this->Document->getInsertID() : $this->data['Document']['id'];
			
			$this->upload_file($id);

			$this->Session->setFlash('Document saved.', 'default', array('class' => 'success'));
			$this->redirect('/admin/documents/edit/' . $id);	
		}
		else
		{
			$this->Session->setFlash('Document could not be saved. Please check the form for errors.', 'default', array('class' => 'failure'));
			return $this->setAction('admin_edit');	
		}
	}

	/**
	 * Admin
	 * Delete document.
	 * 
	 * @param int $id
	 * @return void
	 * @access public
	 */
	public function admin_delete($id)
	{
		if ($this->Document->delete($id))
		{
			$this->Session->setFlash('Document deleted.', 'default', array('class' => 'success'));
			$this->redirect('/admin/documents');
		}
		
		$this->Session->setFlash('Document not deleted.', 'default', array('class' => 'failure'));
		$this->redirect('/admin/documents/edit/' . $id);
		
	}
	
	/**
	 * Upload file.
	 * 
	 * @param int $id
	 * @return void
	 * @access public
	 */
	private function upload_file($id)
	{
		if (!empty($this->data['Document']['file']['error']))
		{
			return false;
		}

		$tempfile = $this->data['Document']['file']['tmp_name'];
		$filename = $this->data['Document']['file']['name'];
		$filesize = $this->data['Document']['file']['size'];
		$filetype = $this->data['Document']['file']['type'];
		
		$info = pathinfo($filename);
		$ext = $info['extension'];

		$path = WWW_ROOT . Configure::read('Documents.path') . $filename;

		move_uploaded_file($tempfile, $path);
		
		$this->Document->id = $id;
		$this->Document->save(array('Document' => array(
			'filename' => $filename,
			'filesize' => $filesize,
			'filetype' => $filetype,
			'ext' => $ext,
		)), array('validate' => false, 'callbacks' => false));

		return true;

	}


}


