<div class="grid_24">

	<div class="grid_18 prefix_6 alpha omega account-header">
		<h1><span class="face1">Order</span> <span class="face2">History</span></h1>
	</div>

	<?php echo $this->element('customers/my_account_nav', array('step' => 'history')); ?>

	<div class="grid_18 omega">

		<p class="intro">You can view and track your recent and previous purchases below.</p>

		<?php if (!empty($records)): ?>

		<table cellspacing="0" class="basket-table">
			
			<thead>
				<tr>
					<th>Order Date</th>
					<th>Order Ref.</th>

					<th>Items</th>
					<th>Order Total</th>
				</tr>
			</thead>
			
			<tbody>
			
			<?php foreach ($records as $k => $record): ?>
			
				<tr>
					<td style="width: 140px;"><?php echo $time->format('j F Y', $record['Order']['created']); ?></td>
					<td style="width: 240px;"><a class="blue"href="/orders/view/<?php echo intval($record['Order']['id']); ?>"><?php echo h($record['Order']['ref']); ?></a></td>

					<td style="width: 100px;"><?php echo count($record['OrderItem']); ?></td>
					<td style="width: 140px;"><?php echo $record['Currency']['html'] . ' ' . number_format($record['Order']['grand_total'], 2); ?></td>
				</tr>
			
			</tbody>
			
			<?php endforeach; ?>
			
		</table>

		<?php else: ?>

			<p class="georgia">You don't have any orders at present.</p>

		<?php endif; ?>

	</div>

</div>