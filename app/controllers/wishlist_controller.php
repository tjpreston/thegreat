<?php

/**
 * Wishlist Controller
 * 
 */
class WishlistController extends AppController
{
	/**
	 * Array containing the names of components this controller uses.
	 *
	 * @var array
	 * @access public
	 */
	public $components = array('Security', 'Email');
	
	/**
	 * Show wishlist
	 *
	 * @access public
	 * @return void
	 */
	public function index($hash = null)
	{
		$this->_wishlistItems = $this->Wishlist->WishlistItem->getCollectionItems();
		$this->set('wishlistItems', $this->_wishlistItems);
		
		$this->addCrumb('/wishlist', 'Wishlist');
		
		$this->setLastPage();
		
	}
	
	/**
	 * Add item to wishlist.
	 *
	 * @return void
	 * @access public
	 */
	public function add()
	{
		if (empty($this->data['Basket']))
		{
			$this->redirect('/wishlist');
		}
		
		$result = $this->Wishlist->WishlistItem->addItemsToCollection($this->data['Basket']);
		
		if (is_bool($result) && ($result === true))
		{
			$this->Session->setFlash('Item added to your wishlist', 'default', array('class' => 'success'));
		}
		else if (is_array($result))
		{
			$this->Session->setFlash($result['message'], 'default', array('class' => 'failure'));
			$this->redirect($result['returnTo']);
		}
		else if (is_string($result))
		{
			$this->Session->setFlash(implode('<br />', $result), 'default', array('class' => 'failure'));
		}

		$this->redirect('/wishlist');
		
	}
	
	/**
	 * Remove item.
	 *
	 * @return void
	 * @access public
	 */
	public function remove($id)
	{
		$itemCountBeforeDelete = $this->Wishlist->WishlistItem->getCollectionItemCount();
		$result = $this->Wishlist->WishlistItem->removeItemFromCollection($id);
		$itemCountAfterDelete = $this->Wishlist->WishlistItem->getCollectionItemCount();
		
		if ($itemCountAfterDelete < $itemCountBeforeDelete)
		{
			$this->Session->setFlash('Item removed from your wishlist', 'default', array('class' => 'success'));
		}

		$this->redirect('/wishlist');
	
	}
	
	/**
	 * Update quantities.
	 *
	 * @return void
	 * @access public
	 */
	public function update()
	{	
		$preUpdate = $this->Wishlist->WishlistItem->getCollectionTotalQuantities();
		$this->Wishlist->WishlistItem->updateCollectionItemQuantities($this->data['WishlistItem']);
		$postUpdate = $this->Wishlist->WishlistItem->getCollectionTotalQuantities();
		
		if ($preUpdate <> $postUpdate)
		{
			$this->Session->setFlash('Wishlist quantities updated', 'default', array('class' => 'success'));
		}
		
		$this->redirect('/wishlist');
		
	}
	
	/**
	 * Add item from wishlist to basket.
	 *
	 * @param mixed $id
	 * @return void
	 * @access public
	 */
	public function add_to_basket($id)
	{
		if ($id == 'all')
		{
			$id = $this->Wishlist->WishlistItem->find('list', array(
				'conditions' => array('WishlistItem.wishlist_id' => $this->Wishlist->getCollectionID())
			));
		}
		
		$newBasketItem = $this->Wishlist->WishlistItem->getBasketItemArray($id);
		
		if (empty($newBasketItem))
		{
			$this->redirect('/wishlist');
		}
		
		$result = $this->Basket->BasketItem->addOneItemToCollection($newBasketItem);
		
		if ($result === true)
		{
			$this->Session->setFlash('The item was added to your basket', 'default', array('class' => 'success'));
			$this->redirect('/basket');
		}
		else if (is_array($result))
		{
			$this->Session->setFlash($result['message'], 'default', array('class' => 'failure'));
			$this->redirect($result['returnTo']);
		}
		else if (is_string($result))
		{
			$this->Session->setFlash($result, 'default', array('class' => 'failure'));
		}
		else
		{
			$this->Session->setFlash('Item could not be added your basket', 'default', array('class' => 'failure'));
		}
		
		$this->redirect('/wishlist');
		
	}
	
	
	
	/**
	 * Send wishlist to a friend
	 *
	 * @return void
	 * @access public
	 */
	public function send()
	{	
		if (empty($this->data['WishlistRecipient']))
		{
			$this->redirect('/wishlist');
		}
		
		$customer = $this->Auth->user();
		
		$this->data['WishlistRecipient']['wishlist_id'] = $this->_wishlist['Wishlist']['id'];
		
		$this->Wishlist->WishlistRecipient->set($this->data);
		if (!$this->Wishlist->WishlistRecipient->validates())
		{
			$this->Session->setFlash('Please complete all the required fields', 'default', array('class' => 'failure'));		
			return $this->setAction('index');
		}
		
		$this->set('wishlistItems', $this->Wishlist->WishlistItem->getCollectionItems());		
		$this->set('recipient', $this->data['WishlistRecipient']['name']);
		$this->set('message', $this->data['WishlistRecipient']['message']);
		
		$this->initDefaultEmailSettings();
		$this->Email->to   	   = $this->data['WishlistRecipient']['name'] . '<' . $this->data['WishlistRecipient']['email'] . '>';
		$this->Email->subject  = Configure::read('Site.name') . ' - A Wishlist from ' . $customer['Customer']['first_name'];
		$this->Email->template = 'wishlist/send';
		$this->Email->send();
		
		if (!empty($this->data['WishlistRecipient']['to_customer']))
		{
			$this->initDefaultEmailSettings();
			$this->Email->to   	   = $this->data['WishlistRecipient']['name'] . '<' . $customer['Customer']['email'] . '>';
			$this->Email->subject  = Configure::read('Site.name') . ' - A Wishlist from ' . $customer['Customer']['first_name'];
			$this->Email->template = 'wishlist/send';
			$this->Email->send();
		}
		
		$this->Wishlist->WishlistRecipient->save($this->data);
		
		$msg = 'Your wishlist was successfully sent to ' . $this->data['WishlistRecipient']['name'] . ' (' . $this->data['WishlistRecipient']['email'] . ')';
		$this->Session->setFlash($msg, 'default', array('class' => 'success'));
		$this->redirect('/wishlist');
		
	}
	
	
}
