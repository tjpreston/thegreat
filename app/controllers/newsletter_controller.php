<?php

/**
 * Newsletter Controller
 *
 */
class NewsletterController extends AppController
{
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

		$this->Security->validatePost = false;
	}

	function signup_mailchimp($anchor = 'newsletter') 
	{
		if(!empty($this->data))
		{
			$subscribe = $this->Newsletter->subscribe($this->data);
			if($subscribe === true){
				$this->Session->setFlash('Almost there! Please check your inbox to confirm your subscription. Thank you.', 'default', array('class' => 'success'), 'newsletter');
			} else {
				$this->Session->setFlash('Something went wrong and you weren\'t subscribed to the newsletter. Sorry.', 'default', array('class' => 'failure'), 'newsletter');
			}
			$this->redirect($this->referer(array('controller' => 'home', 'action' => 'index')) . '#' . $anchor);
		}
	}

	/**
	 * Send email to admin informing of new newsletter subscriber.
	 *
	 * @access public
	 * @return void
	 */
	public function signup($anchor = 'keep-in-touch')
	{
		$this->Newsletter->set( $this->data );
		if(!$this->Newsletter->validates())
		{
			$this->Session->setFlash('The email address you submitted was invalid. Please try again.', 'newsletter', array('class' => 'failure'), 'newsletter');
			$this->redirect($this->referer(array('controller' => 'home', 'action' => 'index')) . '#' . $anchor);
			exit;
		}
		
		$this->set('email', $this->data['Newsletter']['email']);

		App::import('Vendor', 'CsrestSubscribers', array('file' => 'campaignmonitor/csrest_subscribers.php'));

		$apiKey = Configure::read('Newsletter.api_key');
		$list = Configure::read('Newsletter.list_id');

		// Subscribe email address to mailing list
		$wrap = new CS_REST_Subscribers($list, $apiKey);

		$result = $wrap->add(array(
			'EmailAddress' => $this->data['Newsletter']['email'],
			'Resubscribe' => true
		));

		if($result->was_successful()) {
			$this->Session->setFlash('Please check your inbox to confirm your subscription. Thank you.', 'newsletter', array('class' => 'success'), 'newsletter');
		} else {
			debug($result->response);
			$this->Session->setFlash($result->http_status_code . ': Something went wrong and you weren\'t subscribed to the newsletter. Sorry.', 'default', array('class' => 'failure'), 'newsletter');
		}
		
		$this->redirect($this->referer(array('controller' => 'home', 'action' => 'index')) . '#' . $anchor);
		
	}

	public function add($anchor = 'footer')
    {
		if (!empty($this->data))
        {
           
            $this->Newsletter->set($this->data);
          
            if ($this->Newsletter->save())
            {
                $this->Session->setFlash('Your newsletter request was sent. Thank you.', 'default', array('class' => 'news-success'), 'newsletter');
	           
	            $this->Email->from = Configure::read('Email.from_name');
	            $this->Email->to = Configure::read('Email.from_email');
	            $this->Email->subject = 'New user for newletter';
	            $this->Email->sendAs = 'text';

	            
	            $this->Email->send($this->data['Newsletter']['email']);
                $this->redirect($this->referer(array('controller' => 'home', 'action' => 'index')) . '#' . $anchor);

            }
            else{
            	$message = $this->Newsletter->validationErrors['email'];
                $this->Session->setFlash('Your newsletter request was not sent.<br/>' . $message, 'default', array('class' => 'news-failure'), 'newsletter');
                $this->redirect($this->referer(array('controller' => 'home', 'action' => 'index')) . '#' . $anchor);

            }
                
			      
        }
    }

}


