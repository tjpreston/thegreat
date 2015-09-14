<div id="admin-content">
		
	<div id="side-col">
		<?php echo $form->create('StockistCommission', array('url' => $this->here, 'id' => 'commission-filter', 'class' => 'filter-form')); ?>
		
		<h2>Filter Results</h2>
		
		<h3>Filter by Date</h3>
		<?php echo $this->Form->month('month', $month); ?>
		<?php echo $this->Form->year('year', 2011, date('Y'), $year); ?>
		
		<div class="submit">
			<?php echo $form->submit('Search', array('id' => 'filter-submit', 'div' => false)); ?>
			<?php // echo $form->submit('Reset', array('id' => 'reset-submit', 'div' => false, 'class' => 'reset')); ?>
			<a href="/admin/stockist_commissions">Reset</a>
		</div>
		
		<?php echo $form->end(); ?>
		
	</div>
	
		<div class="panes">
			
			<?php echo $session->flash(); ?>
			
			<div id="header">
				<h1>Stockist Commission Reports</h1>
			</div>
			
			<?php if (!empty($records)): ?>
				
				<table id="list-table" cellspacing="0">
					<thead>
						<tr> <!-- 760px -->
							<th style="width: 250px;">Stockist</th>
							<th style="width: 180px;">Affiliate Commission</th>
							<th style="width: 180px;">Sales Commission</th>
							<th style="width: 180px;">Total</th>
							<th style="width: 80px;">Orders</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($records as $k => $record): ?>
							<?php $commissionReport = '/admin/stockist_commissions/report/' . intval($record['Stockist']['id']); ?>
							<tr>
								<td><?php echo $record['Stockist']['name']; ?>
								<td>&pound;<?php echo number_format($record['commission']['referral'], 2); ?>
								<td>&pound;<?php echo number_format($record['commission']['basket'], 2); ?>
								<td>&pound;<?php echo number_format($record['commission']['total'], 2); ?>
								<td><?php echo $record['commission']['orders_count']; ?></td>
								<td class="actions">
									<!-- <a href="/admin/stockist_commissions/orders/<?php echo $year . '-' . $month; ?>/<?php echo intval($record['Stockist']['id']); ?>"><img src="/img/icons/report_go.png" alt="" /></a> -->
									<a href="/admin/orders?month=<?php echo $month; ?>&year=<?php echo $year; ?>&stockist=<?php echo $record['Stockist']['id']; ?>"><img src="/img/icons/report_go.png" alt="" /></a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>		
				</table>
				
			<?php else: ?>
						
				<p><?php echo (isset($records)) ? 'No commission reports found. Please refine your search.' : 'Please use the filter to the left to search for commission reports.'; ?></p>
			
			<?php endif; ?>
						
			
		</div>
	
</div>


