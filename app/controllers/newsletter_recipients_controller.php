<?php

/**
 * NewsletterRecipient Controller
 *
 */
class NewsletterRecipientsController extends AppController
{
 	public $helpers = array('Html', 'Form');
    public $components = array('Email');


	public function beforeFilter()
	{
		$this->Auth->allow('*');
		parent::beforeFilter();
	}
	


	public function add()
	{
		
		if(!empty($this->data))
		{
			$this->NewsletterRecipient->set($this->data);
			if ($this->NewsletterRecipient->validates())
            {	
            	$recipient_email = $this->data['NewsletterRecipient']['email'];
				$this->NewsletterRecipient->savefield('email', $recipient_email);

				$this->Email->from = Configure::read('Email.from_name');
	            $this->Email->to = Configure::read('Email.from_email');
	            $this->Email->subject = 'New user for your newletter';
	            $this->Email->sendAs = 'text';
	            $this->Email->send($this->data['NewsletterRecipient']['email']);

                $this->Session->setFlash('Your newsletter request was sent. Thank you.', 'default', array('class' => 'success'), 'newsletter');
                $this->redirect($this->referer()); 
            }
            else{
                $this->Session->setFlash('Your newsletter request was not sent. Please enter a valid email address and try again.', 'default', array('class' => 'failure'), 'newsletter');
              
                $this->redirect($this->referer()); 
            }
		}
	}
	public function admin_index()
	{
		$path = $this->NewsletterRecipient->generateCSV();
		$filename = 'newsletter_customers_download.csv'; 
		$this->layout = 'download_csv';
		$this->set(compact('path', 'filename'));
	}
}