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
			<li><a href="/admin/stockists/edit" class="icon-link add-link">Add New Stockist</a></li>
		</ul>
	</div>
	
	<?php echo $form->create('Customer', array('action' => 'save')); ?>
	
		<div class="panes">
			
			<?php echo $session->flash(); ?>
			
			<div id="header">
				<h1>Stockist Management</h1>
			</div>
			
			<?php if (!empty($records)): ?>
				
				<table id="list-table" cellspacing="0">
					<thead>
						<tr> <!-- 760px -->
							<th style="width: 300px;"><?php echo $this->Paginator->sort('Name', 'name'); ?></th>
							<th style="width: 150px;"><?php echo $this->Paginator->sort('Town', 'town'); ?></th>
							<th style="width: 200px;"><?php echo $this->Paginator->sort('County', 'county'); ?></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($records as $k => $record): ?>
							<?php $editUrl = '/admin/stockists/edit/' . intval($record['Stockist']['id']); ?>
							<tr>
								<td><?php echo $record['Stockist']['name']; ?>
								<td><?php echo $record['Stockist']['town']; ?>
								<td><?php echo $record['Stockist']['county']; ?>
								<td class="actions">
									<a href="/admin/stockists/delete/<?php echo intval($record['Stockist']['id']); ?>"><img src="/img/icons/delete.png" alt="" /></a>
									<a href="/admin/stockists/edit/<?php echo intval($record['Stockist']['id']); ?>"><img src="/img/icons/page_edit.png" alt="" /></a>
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
						
				<p><?php echo (isset($records)) ? 'No stockists found. Please refine your search.' : 'Please use the filter to the left to search for stockists.'; ?></p>
			
			<?php endif; ?>
						
			
		</div>
		
	<?php echo $form->end(); ?>
	
</div>


