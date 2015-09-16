<div id="admin-content">
	
	<div id="side-col">
		<ul id="admin-links">
			<li><a href="/admin/basket_discounts/edit" class="icon-link add-link">Add New Basket Discount</a></li>
		</ul>
	</div>
				
	<?php echo $form->create('BasketDiscount', array('action' => 'save')); ?>

		<div class="panes">
			
			<?php echo $session->flash(); ?>
			
			<div id="header">
				<h1>Basket Discount Management</h1>
			</div>
			
			<?php if (!empty($records)): ?>
				
				<table id="list-table" cellspacing="0">
					<thead>
						<tr>
							<th><?php echo $this->Paginator->sort('Name', 'BasketDiscount.name'); ?></th>
							<th style="width: 60px;">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($records as $k => $record): ?>
							<?php $editUrl = '/admin/basket_discounts/edit/' . intval($record['BasketDiscount']['id']); ?>
							<tr>
								<td style="width: 140px;"><?php echo h($record['BasketDiscount']['name']); ?></td>
								<td class="actions">
									<a href="/admin/basket_discounts/delete/<?php echo intval($record['BasketDiscount']['id']); ?>"><img src="/img/icons/delete.png" alt="" /></a>									
									<a href="/admin/basket_discounts/edit/<?php echo intval($record['BasketDiscount']['id']); ?>"><img src="/img/icons/page_edit.png" alt="" /></a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				
				<?php echo $this->element('admin/pagination'); ?>
				
			<?php else: ?>
				<p>No records found.</p>
			<?php endif; ?>
			
		</div>
		
	<?php echo $form->end(); ?>
	
</div>
