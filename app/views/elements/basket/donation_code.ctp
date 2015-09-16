<div id="gift-voucher">
	
	<h3>GOT A DONATION CODE?</h3>
	
	<?php if (!empty($basket['Basket']['basket_donation_id'])): ?>

		<div class="clear"></div>
		
		<?php if(!empty($basket['Basket']['basket_donation_id'])):?>
		<p><strong>The Home Market will make a donation on behalf of:  </strong></p>
		<p><?php echo h($basket['BasketDonation']['name']); ?> <a href="/basket/removedonationcode"> <img src="/img/icons/delete.png" alt="Remove gift voucher" title="Remove gift voucher" /> Remove Donation </a></p>
		
		<?php endif; ?>
		
	<?php else: ?>


		<?php echo $this->Form->input('Basket.basket_donation_id',array(
		'label' => false,
		'type' => 'text',
		'value' => 'Enter donation code' )); ?>
		<?php echo $this->Form->submit('Go', array(
			'div' =>false,
			'id' =>'donation-submit')); ?>
		
	<?php endif; ?>
	
</div>

