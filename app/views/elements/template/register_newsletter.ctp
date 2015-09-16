<div id="home-right">
	
	<?php if ($session->check('Auth.Customer.id')): ?>
		
		<div id="why-register" class="sidebox">
			<h2>Quick Order Catalogue</h2>
			<p><img src="/img/app/template/know-what-you-want.png" alt="Know what you want?" /></p>
			<p>With the Hytek Quick Order Form ordering is simple. If you know the stock code and how many of the item you want then it's perfect. Just add them to the corresponding fields and add to basket.</p>
			<a href="/quick_order"><img src="/img/app/template/bn-quick-order.png" alt="Quick Order" /></a>
		</div>
		
	<?php else: ?>
		
		<div id="why-register" class="sidebox">
			<h2>Account Registration</h2>
			<p><img src="/img/app/template/why-register.png" alt="Why Register?" /></p>
			<p>Login is for previously approved trade credit accounts only, giving you access to a wider range of information and allowing you to order from us online.</p>
			<p>If you don't have an account then just <a href="/pages/contact">contact us</a> with your requirements.</p>
			<a href="/customers/register"><img src="/img/app/bn-register-now.png" alt="Register Now" /></a>
		</div>

		<div id="newsletter">
			<h2>Newsletter</h2>
			<?php echo $this->Form->create('Newsletter', array('url' => '/newsletter/signupviaemail', 'class' => 'panel')); ?>
				<?php echo $this->Form->input('email', array(
					'label' => 'Sign up for offers &amp; promotions',
					'id' => 'newsletter-email',
					'value' => 'Email Address',
				)); ?>
				<?php echo $this->Form->submit('/img/app/bn-signup.png', array('id' => 'go-signup')); ?>
			<?php echo $this->Form->end(); ?>
		</div>
		
	<?php endif; ?>
	
</div>

