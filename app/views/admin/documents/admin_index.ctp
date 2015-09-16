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
		<ul id="admin-links">
			<li><a href="/admin/documents/edit" class="icon-link add-link">Add New Document</a></li>
		</ul>
	</div>
	
	<?php echo $form->create('Document', array('action' => 'save')); ?>
	
		<div class="panes">
			
			<?php echo $session->flash(); ?>
			
			<div id="header">
				<h1>Documents</h1>
			</div>
			
			<?php if (!empty($records)): ?>
				
				<table id="list-table" cellspacing="0">
					<thead>
						<tr>
							<th style="width: 30px;"><?php echo $this->Paginator->sort('Type', 'ext'); ?></th>
							<th><?php echo $this->Paginator->sort('Name', 'name'); ?></th>
							<th style="width: 60px;">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($records as $k => $record): ?>
							<?php $editUrl = '/admin/documents/edit/' . intval($record['Document']['id']); ?>
							<tr>
								<td style="text-align: center;"><img src="/img/icons/file_extension_<?php echo h($record['Document']['ext']); ?>.png" /></td>
								<td><a href="<?php echo $editUrl; ?>"><?php echo h($record['Document']['name']); ?></a></td>
								<td class="actions">
									<a href="/admin/documents/delete/<?php echo intval($record['Document']['id']); ?>"><img src="/img/icons/delete.png" alt="" /></a>
									<a href="/admin/documents/edit/<?php echo intval($record['Document']['id']); ?>"><img src="/img/icons/page_edit.png" alt="" /></a>
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
						
				<p>No records found.</p>
			
			<?php endif; ?>
						
			
		</div>
		
	<?php echo $form->end(); ?>
	
</div>


