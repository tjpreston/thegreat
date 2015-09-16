<?php

class FormsController extends AppController {

	public $components = array('Email');

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
		$this->Security->validatePost = false;
	}

	public function index($formType){
		$this->Form->addValidation($formType);
		$error = false;

		if(!empty($this->data)){
			$this->Form->set($this->data);
			if($this->Form->validates()){
				if(!empty($this->data['Form']['password_main']))
				{
					$this->data['Form']['password'] = $this->data['Form']['password_main'];
					$this->data['Form']['password'] = $this->Auth->password($this->data['Form']['password']);
					$this->Form->set($this->data);
				}
				$this->Form->saveForm($formType);
				$this->sendEmail($formType);
				$this->render($formType . '_submitted');
			} else {
				$this->Session->setFlash('The form could not be submitted. Please check below for errors.', 'default', array('class' => 'failure'));
				$error = true;
			}
		}

		if(empty($this->data) || $error){
			$this->render($formType . '_index');
		}
	}

	/**
	 * Send email to website owner.
	 * 
	 * @return void
	 * @access private
	 */
	private function sendEmail($formType){
		$to = Configure::read('Email.Forms.' . $formType . '.to');
		if (empty($to)) {
			$to = Configure::read('Email.Forms.default.to');
		}


		$this->Email->reset();
		$this->Email->from = Configure::read('Email.from_name') . ' <' . Configure::read('Email.from_email') . '>';
		$this->Email->replyTo = Configure::read('Email.from_email');
		$this->Email->return = Configure::read('Email.from_email');
		$this->Email->layout = 'to_vendor';
		$this->Email->sendAs = 'text';
		$this->Email->to = $to;

		$this->set('data', $this->data['Form']);

		$this->Email->subject = 'Catalogue Request';
		$this->Email->template = 'forms/' . $formType;

		$this->Email->send();

	}

}