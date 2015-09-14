<?php

/**
 * Order Notes Controller
 * 
 */
class OrderNotesController extends AppController
{
	/**
	 * Array containing the names of components this controller uses.
	 *
	 * @var array
	 * @access public
	 */
	public $components = array('Email');
	
	/**
	 * Admin
	 * Save (and send to customer) a note.
	 * 
	 * @return void
	 * @access public
	 */
	public function admin_save()
	{
		if (empty($this->data['OrderNote']['order_id']))
		{
			$this->redirect('/admin/orders');
		}
		
		if (empty($this->data['OrderNote']['content']))
		{
			$this->redirect('/admin/orders/edit/' . $this->data['OrderNote']['order_id']);
		}
		
		$this->data['OrderNote']['icon'] = 3;
		$this->data['OrderNote']['name'] = 'Note';
		
		if ($this->OrderNote->save($this->data))
		{
			if (!empty($this->data['OrderNote']['customer_notified']))
			{
				$order = $this->OrderNote->Order->findById($this->data['OrderNote']['order_id']);
				
				$this->initDefaultEmailSettings();
				
				$this->Email->to = $order['Customer']['first_name'] . '<' . $order['Customer']['email'] . '>';
				$this->Email->subject = Configure::read('Site.name') . ' - Order Note';
				$this->Email->template = 'customers/order_notifications/order_note';
				
				$this->set('recipient', $order['Customer']['first_name']);
				$this->set('note', $this->data['OrderNote']['content']);
				
				$this->Email->send();
				
				$sentNote = true;		
				
			}
			
			$message  = 'Note saved';
			$message .= (!empty($sentNote)) ? ' and saved.' : '.';
			
			$this->Session->setFlash($message, 'default', array('class' => 'success'));
			$this->redirect('/admin/orders/edit/' . $this->data['OrderNote']['order_id']);
			
		}
		
		$this->Session->setFlash('There were errors. Please check the form below.', 'default', array('class' => 'failure'));
		$this->redirect('/admin/orders/edit/' . $this->data['OrderNote']['order_id']);
		
	}
	
	
	
}
