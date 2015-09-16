<div id="leftcol">
	<?php echo $this->element('template/account_nav_panel'); ?>
	<?php echo $this->element('catalog/featured_and_recent'); ?>
</div>
<div id="content">

	<div class="header">
		<h1>My Account</h1>
		<p class="intro">Welcome back <span class="customer-name"><?php echo h($session->read('Auth.Customer.first_name')); ?></span>. Use your account to manage your orders and account information.</p>
	</div>
	<div class="content-pad">

		<div id="my-account-home-boxes">
			
			<div id="my-account-quick-order">
				<h2><a href="/quick_order">Quick Order Form</a></h2>
				<p>Know exactly what you're looking for? Quickly build your order here.</p>
			</div>

			<div id="my-account-order-history">
				<h2><a href="/orders">Order History</a></h2>
				<p>View your orders that you've placed with us.</p>
			</div>
			
			<div id="my-account-account-information" class="no-margin-right">
				<h2><a href="/customers/account_information">Account Information</a></h2>
				<p>Manage and edit your account information.</p>
			</div>
			
			<div id="my-account-address-book">
				<h2><a href="/customer_addresses">Address Book</a></h2>
				<p>Speed up the checkout process by saving your most frequently used addresses here.</p>
			</div>
			
			<div id="my-account-account-password" class="no-margin-right">
				<h2><a href="/customers/account_password">Account Password</a></h2>
				<p>Change your account password.</p>
			</div>
			
		</div>

	</div>

</div>
<div class="clear"></div>