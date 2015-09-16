<div id="leftcol">
	<?php echo $this->element('template/account_nav_panel'); ?>
	<?php echo $this->element('catalog/featured_and_recent'); ?>
</div>
<div id="content">

	<div class="header">
		<h1>Order History</h1>
		<p class="intro">View your orders that you've placed with us.</p>
	</div>

	<div class="content-pad">

		<?php if (!empty($records)): ?>

		<table id="orders" class="order-table">
			
			<thead>
				<tr>
					<th>Date</th>
					<th>Order Ref.</th>

					<th>Items</th>
					<th>Order Total</th>
				</tr>
			</thead>
			
			<tbody>
			
			<?php foreach ($records as $k => $record): ?>
			
				<tr<?php if(($k % 2) === 1){ echo ' class="even"'; } ?>>
					<td><?php echo $time->format('j F Y', $record['Order']['created']); ?></td>
					<td><a href="/orders/view/<?php echo intval($record['Order']['id']); ?>">#<?php echo h($record['Order']['ref']); ?></a></td>
					<td><?php echo count($record['OrderItem']); ?></td>
					<td><?php echo $record['Currency']['html'] . number_format($record['Order']['grand_total'], 2); ?></td>
				</tr>
			
			</tbody>
			
			<?php endforeach; ?>
			
		<?php else: ?>

			<p>You don't have any orders at present.</p>

		<?php endif; ?>

		</table>

	</div>


</div>
<div class="clear"></div>