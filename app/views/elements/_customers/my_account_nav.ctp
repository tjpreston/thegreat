<div class="panel">
  <div class="panel-content">
    <ul id="customer-nav-panel" class="filter-style">
      <li<?php echo ($step == 'home') ? ' class="active"' : ''; ?>><a href="/customers">Home</a></li>
	  <li<?php echo ($step == 'history') ? ' class="active"' : ''; ?>><a href="/orders">Order History</a></li>
	  <li<?php echo ($step == 'information') ? ' class="active"' : ''; ?>><a href="/customers/account_information">Account Information</a></li>
	  <li<?php echo ($step == 'address') ? ' class="active"' : ''; ?>><a href="/customer_addresses">Address Book</a></li>
	  <li<?php echo ($step == 'password') ? ' class="active"' : ''; ?>><a href="/customers/account_password">Account Password</a></li>
	<div style="clear:both;"></div>

	</ul>
  </div>
<div class="clear-both"></div>
</div>