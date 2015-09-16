<div id="admin-content">
	
	<div id="side-col">
		
		<ul id="admin-links">
			<li><a href="/admin/custom_options/edit" class="icon-link add-link">Add New Custom Option</a></li>
		</ul>
		
	</div>	
			
	<?php echo $form->create('CustomOption', array('action' => 'save')); ?>

		<div class="panes">
			
			<?php echo $session->flash(); ?>
			
			<div id="header">
				<h1>Custom Options Management</h1>
			</div>
			
			<?php if (!empty($records)): ?>
				
				<table id="list-table" cellspacing="0">
					<thead>
						<tr>
							<th><?php echo $this->Paginator->sort('Option', 'CustomOptionName.name'); ?></th>
							<th style="width: 60px;">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($records as $k => $record): ?>
							<?php $editUrl = '/admin/custom_options/edit/' . intval($record['CustomOption']['id']); ?>
							<tr>
								<td><a href="<?php echo $editUrl; ?>"><?php echo h($record['CustomOptionName']['name']); ?></a></td>						
								<td class="actions">
									<a href="/admin/custom_options/delete/<?php echo intval($record['CustomOption']['id']); ?>"><img src="/img/icons/delete.png" alt="" /></a>									
									<a href="/admin/custom_options/edit/<?php echo intval($record['CustomOption']['id']); ?>"><img src="/img/icons/page_edit.png" alt="" /></a>
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
				<p>No custom options found.</p>
			<?php endif; ?>
			
		</div>
		
	<?php echo $form->end(); ?>
	
</div>



