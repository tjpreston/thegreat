<div id="admin-content">
	
	<div id="side-col">
		&nbsp;
	</div>
	
	<?php echo $form->create('Currency', array('action' => 'save_exchange_rates', 'id' => 'product-form')); ?>
	
		<div class="panes">
			
			<?php echo $session->flash(); ?>
			
			<div id="header">
				<h1>Currency Exchange Rates</h1>
			</div>
			
			<div id="pane-general" class="pane">
				<div id="exchange-rates" class="contains-table">
					<table>
						<thead>
							<tr>
								<th><?php echo h($mainCurrency['Currency']['code']); ?> (Base)</th>
								<?php foreach ($currencies as $k => $currency): ?>
									<?php $class = ($k == count($currencies)) ? ' class="last-col"' : ''; ?>
									<th<?php echo $class; ?>><?php echo h($currency['Currency']['code']); ?></th>
								<?php endforeach; ?>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<?php 
									echo $form->text('null.null', array(
										'value' => '1.00000',
										'class' => 'smaller',
										'readonly' => 'readonly'
									)); 
									?>
								</td>
								<?php foreach ($currencies as $k => $currency): ?>
									<?php $class = ($k == count($currencies)) ? ' class="last-col"' : ''; ?>
									<td<?php echo $class; ?>>
									<?php 
									echo $form->hidden('Currency.' . $k . '.id', array(
										'value' => $currency['Currency']['id']
									));
									echo $form->input('Currency.' . $k . '.exchange_rate', array(
										'value' => number_format($currency['Currency']['exchange_rate'], 5),
										'class' => 'smaller',
										'label' => false
									));
									?>
								</td>
								<?php endforeach; ?>
							</tr>
						</tbody>
					</table>
				</div>
				<?php echo $form->submit('Save'); ?>
			</div>
			
		</div>
		
	<?php echo $form->end(); ?>
	
</div>