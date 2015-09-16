<div id="admin-content">
	
	<div id="side-col">
		<ul id="admin-links">
			<li><a href="/admin/tax_rates/edit" class="icon-link add-link">Add New Tax Rate</a></li>
		</ul>
	</div>
				
	<?php echo $form->create('TaxRate', array('action' => 'save')); ?>

		<div class="panes">
			
			<?php echo $session->flash(); ?>
			
			<div id="header">
				<h1>Tax Rate Management</h1>
			</div>
			
			<?php if (!empty($records)): ?>
				
				<table id="list-table" cellspacing="0">
					<thead>
						<tr>
							<th><?php echo $this->Paginator->sort('Country', 'Country.name'); ?></th>
							<th><?php echo $this->Paginator->sort('Identifer', 'TaxRate.name'); ?></th>
							<th style="width: 60px;">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($records as $k => $record): ?>
							<?php $editUrl = '/admin/tax_rates/edit/' . intval($record['TaxRate']['id']); ?>
							<tr>
								<td style="width: 140px;"><?php echo h($record['Country']['name']); ?></td>
								<td><a href="<?php echo $editUrl; ?>"><?php echo h($record['TaxRate']['name']); ?></a></td>	
								<td class="actions">
									<a href="/admin/tax_rates/delete/<?php echo intval($record['TaxRate']['id']); ?>"><img src="/img/icons/delete.png" alt="" /></a>									
									<a href="/admin/tax_rates/edit/<?php echo intval($record['TaxRate']['id']); ?>"><img src="/img/icons/page_edit.png" alt="" /></a>
								</td>
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
				<p><?php echo (isset($records)) ? 'No products found. Please refine your search.' : 'Please use the filter to the left to search for products.'; ?></p>
			<?php endif; ?>
			
		</div>
		
	<?php echo $form->end(); ?>
	
</div>

