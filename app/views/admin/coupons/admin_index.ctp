<div id="admin-content">
	
	<div id="side-col">
		<ul id="admin-links">
			<li><a href="/admin/coupons/edit" class="icon-link add-link">Add New Coupon</a></li>
		</ul>
	</div>
	
	<?php echo $form->create('Coupon', array('action' => 'save')); ?>
	
		<div class="panes">
			
			<?php echo $session->flash(); ?>
			
			<div id="header">
				<h1>Coupon Management</h1>
			</div>
			
			<?php if (!empty($records)): ?>
				
				<table id="list-table" cellspacing="0">
					<thead>
						<tr>
							<th><?php echo $this->Paginator->sort('Coupon Code', 'Coupon.code'); ?></th>
							<th style="width: 60px;">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($records as $k => $record): ?>
							<?php $editUrl = '/admin/coupons/edit/' . intval($record['Coupon']['id']); ?>
							<tr>
								<td style="width: 140px;"><?php echo h($record['Coupon']['code']); ?></td>
								<td class="actions">
									<a href="/admin/coupons/delete/<?php echo intval($record['Coupon']['id']); ?>"><img src="/img/icons/delete.png" alt="" /></a>
									<a href="/admin/coupons/edit/<?php echo intval($record['Coupon']['id']); ?>"><img src="/img/icons/page_edit.png" alt="" /></a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				
				<?php echo $this->element('admin/pagination'); ?>
				
			<?php else: ?>
				<p>No coupons found.</p>
			<?php endif; ?>
			
		</div>
	
	<?php echo $form->end(); ?>
	
</div>
