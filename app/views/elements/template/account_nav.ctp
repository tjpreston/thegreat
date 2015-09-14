<p class="account-nav">
	<?php if($this->Session->read('Auth.Customer.id')): ?>
		<a href="/customers">My Account</a> | 
		<a href="/customers/logout">Logout</a>
	<?php else: ?>
		<a href="/customers/login">Trade Login</a>
	<?php endif; ?>
</p>