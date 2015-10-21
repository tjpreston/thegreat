<div class="top-nav clearfix">
	
        <?php
        // Uncomment to echo execution time at top of page
        
        if(isset($phpExecutionTime1, $phpExecutionTime2))
        {
           
        // To benchmark add commented code below
        //$this->set('phpExecutionTime1',  microtime(true));
        // do stuff
        //$this->set('phpExecutionTime2',  microtime(true));
        
        echo "PHP execution time: " . round(($phpExecutionTime2-$phpExecutionTime1)*1000,0) . " ms" . "<br>";
        echo "<br>";         
         
        }
         
         
        ?>
    
        <nav id="account-nav">
		<ul>
		<?php if ($this->Session->read('Auth.Customer.id')): ?>
			<li><a href="/customers">My Account</a></li>
			<li><a href="/customers/logout">Logout</a></li>
		<?php else: ?>
			<li><a href="/customers/login">Register</a></li>
			<li><a href="/customers/login">Login</a></li>
		<?php endif; ?>
		</ul>
	</nav>
	<div id="mini-basket">
		<?php echo $this->Session->flash('collection'); ?>
		<?php if(intval($totalBasketItemQuantities) > 0): ?>
			<a href="/basket">
				Your Basket <?php echo intval($totalBasketItemQuantities); ?> Item<?php echo ($totalBasketItemQuantities !== 1) ? 's' : ''; ?> <?php echo $activeCurrencyHTML; ?><?php echo number_format(floatval($basket['Basket']['last_calculated_grand_total']), 2); ?>
			</a>
		<?php else: ?>
			Your Basket is Empty
		<?php endif; ?>
	</div>
</div>