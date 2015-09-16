<script type="text/javascript">
$(function() {
	$(".reset").click(function() {
		$(".filter-form").find("select").val(0);
		$(".filter-form").find("input:text").val("");
		$(".filter-form").submit();
	});
});
</script>

<div id="admin-content">
	
	<div id="side-col">
		
		<?php echo $form->create('Order', array('class' => 'filter-form', 'type' => 'get')); ?>
		
			<h2>Filter/Search</h2>
			
			<h3>Order Status</h3>			
			<?php echo $this->element('admin/orders/order_status_select', array(
				'selectName' => 'order_status_id',
				'orderStatusID' => $session->read('Order.order_status_id'),
				'allOption' => true
			)); ?>
			
			<h3>Order ID</h3>
			<?php echo $form->input('ref', array(
				'type' => 'text', 
				'label' => false,
				'value' => $session->read('Order.ref')
			)); ?>
			
			<h3>Customer Name</h3>
			<?php echo $form->input('customer_name', array(
				'label' => false,
				'value' => $session->read('Order.customer_name')
			)); ?>
			
			<h3>Order Date</h3>
			<?php echo $form->month('', $session->read('Order.month')); ?>
			<?php echo $form->year('', 2010, date('Y'), $session->read('Order.year')); ?>
			
			<div class="submit">
				<?php echo $form->submit('Search', array('id' => 'filter-submit', 'div' => false)); ?>
				<?php echo $form->submit('Reset', array('id' => 'reset-submit', 'div' => false, 'class' => 'reset', 'type' => 'reset')); ?>
			</div>
			
		<?php echo $form->end(); ?>
		
	</div>
	
	<?php echo $form->create('Order', array('action' => 'save')); ?>
	
		<div class="panes">
			
			<?php echo $session->flash(); ?>
			
			<div id="header">
				<h1>Order Management</h1>
			</div>
			
			<?php if (!empty($records)): ?>

				<?php
					/**
					 * Pagination helper won't allow us to tack query string paramaters on the end of URLs.
					 * We're using jQuery to do this for us.
					 */
					$pagination_append = '?';
					foreach($this->params['url'] as $k => $v){
						if($k != 'url') $pagination_append .= urlencode($k) . '=' . urlencode($v) . '&';
					}
				?>
				<div class="append" data-value="<?php echo $pagination_append; ?>"></div>
				<script type="text/javascript">
					$(document).ready(function(){
						var append = $('.append').data('value');
						$('#pagination a').each(function(){
							$(this).attr('href', $(this).attr('href') + append);
						});
					});
				</script>
				
				<table id="list-table" cellspacing="0">
					<thead>
						<tr>
							<th>&nbsp;</th>
							<th>ID</th>
							<th>Placed</th>
							<th>Customer</th>
							<th>Total</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($records as $k => $record): ?>
							<tr>
								<td style="text-align: center;"><img src="/img/icons/order-status-<?php echo intval($record['OrderStatus']['id']); ?>-small.png" /></td>
								<td><a href="/admin/orders/view/<?php echo intval($record['Order']['id']); ?>"><?php echo h($record['Order']['ref']); ?></a></td>
								<td><?php echo $time->format('d M Y H:i:s', $record['Order']['created']); ?></td>
								<td><?php echo h($record['Order']['customer_first_name']) . ' ' . h($record['Order']['customer_last_name']);; ?></td>
								<td>&pound; <?php echo number_format(floatval($record['Order']['grand_total']), 2); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>		
				</table>
				
				<?php if ($paginator->hasPage(2)): ?>
					<div id="pagination">
						<ul>
							<?php echo $paginator->prev('< Previous',  array('tag' => 'li'), null); ?>
							<?php echo $paginator->numbers(array('tag' => 'li',	'separator' => '')); ?>
							<?php echo $paginator->next('Next >', array('escape' => false, 'tag' => 'li'), null); ?>
						</ul>
					</div>
				<?php endif; ?>
				
			<?php else: ?>
				<p><?php echo (isset($records)) ? 'No orders found. Please refine your search.' : 'Please use the filter to the left to search for orders.'; ?></p>
			<?php endif; ?>
			
		</div>
		
	<?php echo $form->end(); ?>
	
</div>
			