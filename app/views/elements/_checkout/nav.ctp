
<ol id="checkout-nav">
	
	<li class="first<?php echo ($step == 'details') ? ' active' : ''; ?>">Your Details</li>
	<li<?php echo ($step == 'confirmation') ? ' class="active"' : ''; ?>>Confirmation</li>
	<li<?php echo ($step == 'payment') ? ' class="active"' : ''; ?>>Payment</li>
	<li class="last<?php echo ($step == 'complete') ? ' active' : ''; ?>">Receipt</li>
	
</ol>

