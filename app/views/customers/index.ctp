<div class="grid_24">

	<div class="grid_18 prefix_6 alpha omega account-header">
		<h1><span class="face1">My</span> <span class="face2">Account</span></h1>
	</div>

	<?php echo $this->element('customers/my_account_nav', array('step' => 'home')); ?>

	<div class="grid_18 omega">
		<p class="intro">Welcome back <span class="second-color"><?php echo h($session->read('Auth.Customer.first_name')); ?></span>. Use your account to view and track your recent and previous purchases and update your account information.</p>
		<div id="my-account-home-boxes" class="grid_18 alpha omega">
			<div id="my-account-order-history" class="grid_9 alpha">
				<h2 class="border-top-bottom"><a href="/orders"><span class="face1">Order</span> <span class="face2">History</span></a></h2>
				<p>View the online orders that you’ve placed with us.</p>
			</div>
			<div id="my-account-account-information" class="grid_9 omega">
				<h2 class="border-top-bottom"><a href="/customers/account_information"><span class="face1">My</span> <span class="face2">Details</span></a></h2>
				<p>Manage and edit your account information.</p>
			</div>
			<div id="my-account-address-book" class="grid_9 alpha">
				<h2 class="border-top-bottom"><a href="/customer_addresses"><span class="face1">Address</span> <span class="face2">Book</span></a></h2>
				<p>Speed up the checkout process by saving your most frequently used addresses here.</p>
			</div>
			<div id="my-account-account-password" class="grid_9 omega">
				<h2 class="border-top-bottom"><a href="/customers/account_password"><span class="face1">My</span> <span class="face2">Password</span></a></h2>
				<p>Change your account password.</p>
			</div>
		</div>
	</div>

</div>
