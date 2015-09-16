<?php 
	$last_order_time = Configure::read('NextDayDelivery.last_order_time');
	$deliveryAfterWeekend = Configure::read('NextDayDelivery.deliveryAfterWeekend');
?>

<?php

	/* 
	*
	* General Times and Dates 
	*
	*/

	//the time now
	$nowTime = date('H:i:s');

	//todays date YYYY:MM:DD
	$todayDate = date("Y-m-d");
	
	//Seconds
	$date = strtotime($deliveryAfterWeekend);
	$tomorrowDate = strtotime('tomorrow');
	$today = strtotime('today 00:00:00');

	$date = strtotime('next '.$deliveryAfterWeekend.' ');
	$afterWeekendDate = date("jS F", $date);

	
	//Day of the Week
	$todayDay = date('l');
	$tomorrowDay = date('l', $tomorrowDate);

	//The Weekend
	$Weekend = array("Saturday", "Sunday");

	/* 
	*
	* If statements for delivery dates
	*
	*/
	
	//if the time is after the 'last delivery' time
	if ($nowTime > $last_order_time):
		
		//Its Sat or Sun
		if (in_array($todayDay, $Weekend)):

			$delivery_date = strtotime($deliveryAfterWeekend.'00:00:00');
			$delivery = $deliveryAfterWeekend;
			$getIt = $deliveryAfterWeekend;
			$orderBy = $deliveryAfterWeekend;
			$time = strtotime($deliveryAfterWeekend.$last_order_time);

		//Its Mon-Fri
		else:

			$delivery_date = strtotime('tomorrow 00:00:00');
			$delivery = 'same day';
			$getIt = 'Tomorrow';
			$orderBy =  ' tomorrow';
			$time = strtotime('tomorrow '.$last_order_time);
			
		endif; 
	
	//if the time is before the 'last delivery' time	
	else:
		
		//Its Sat or Sun
		if (in_array($todayDay, $Weekend)):

			$delivery_date = strtotime($deliveryAfterWeekend.'00:00:00');
			$delivery = $deliveryAfterWeekend;
			$getIt = $deliveryAfterWeekend;
			$orderBy = $deliveryAfterWeekend;
			$time = strtotime($deliveryAfterWeekend.$last_order_time);

		//Its Mon-Fri
		else:

			$delivery_date = strtotime('today 00:00:00');
			$delivery = 'same day';
			$getIt = 'Today';
			$orderBy = '';
			$time = strtotime('today '.$last_order_time);

		endif; 

	endif; 

	/* 
	*
	* Workings for the countdown timer 
	*
	*/

	$now = time('now');
	//difference in seconds between the time at the 'last order time' and the time now
	$difference = ($time-$now);

	//convert the difference in secounds into hours and minutes
	$hours = (($difference / 60)/60);
	$minutes = round(($hours - intval($hours)) * 60);
	$hours = intval($hours); 
	$days = round((($difference / 60)/60)/24);
?>

<p><strong>Next available delivery date:</strong><br />
	<span class="header-green arial-black">
	<?php if ($getIt == $deliveryAfterWeekend):?>
		<br /> <?php echo $afterWeekendDate; ?><br />
	<?php else: ?>
		Get it <?php echo $getIt; ?><br />
	<?php endif; ?>
	</span>
</p>

<p class="delivery">
	Order in the next 
	<strong>
		<?php 

			if ($hours >23):
				echo $days. ' days ';
			elseif ($hours == 0):
				echo ''.$minutes.' minutes'  ;
			else:
				echo $hours.' hours '.$minutes.' minutes';
			endif; 
		?>
	</strong> 
	(by <?php echo date('ga', strtotime($last_order_time)); ?><?php echo $orderBy; ?>) <br />
	to be eligible for <?php echo $delivery; ?> delivery*
</p>




<p class="terms"><a href="/pages/terms#delivery">* Delivery terms apply</a></p>













































