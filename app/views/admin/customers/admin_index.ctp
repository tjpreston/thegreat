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
		
		<?php echo $form->create('Customer', array('class' => 'filter-form', 'type' => 'get')); ?>
		
			<h2>Filter/Search</h2>

			<h3>Search by Name</h3>
			<?php echo $form->input('name', array('label' => false)); ?>

			<h3>Search by Email Address</h3>
			<?php echo $form->input('email', array('label' => false)); ?>
			
			<div class="submit">
				<?php echo $form->submit('Reset', array('id' => 'reset-submit', 'div' => false, 'class' => 'reset')); ?>
				<?php echo $form->submit('Search', array('id' => 'filter-submit', 'div' => false)); ?>
			</div>
	
		<?php echo $form->end(); ?>
		
	</div>
	
	<?php echo $form->create('Customer', array('action' => 'save')); ?>
	
		<div class="panes">
			
			<?php echo $session->flash(); ?>
			
			<div id="header">
				<h1>Customer Management</h1>
			</div>
			
			<?php if (!empty($records)): ?>
				
				<table id="list-table" cellspacing="0">
					<thead>
						<tr>
							
							<?php if (Configure::read('Customers.require_approval_to_login')): ?>
								<th style="width: 26px;"><?php echo $this->Paginator->sort('', 'pending'); ?></th>							
							<?php endif; ?>
							
							<th><?php echo $this->Paginator->sort('Name', 'first_name'); ?></th>
							<th><?php echo $this->Paginator->sort('Email Address', 'email'); ?></th>
							<th style="width: 70px;"><?php echo $this->Paginator->sort('Type', 'guest'); ?></th>

						</tr>
					</thead>
					<tbody>
						<?php foreach ($records as $k => $record): ?>
							<?php $editUrl = '/admin/customers/edit/' . intval($record['Customer']['id']); ?>
							<tr>
								
								<?php if (Configure::read('Customers.require_approval_to_login')): ?>
									<td style="text-align: center;">
										<?php echo (!empty($record['Customer']['pending'])) ? '<img src="/img/icons/exclamation.png" alt="Account requires approval" title="Account requires approval" />' : '<img src="/img/icons/accept.png" alt="" />'; ?>
									</td>
								<?php endif; ?>
								
								<td><a href="<?php echo $editUrl; ?>"><?php echo h($record['Customer']['first_name']) . ' ' . h($record['Customer']['last_name']);; ?></a></td>
								<td><a href="mailto:<?php echo h($record['Customer']['email']); ?>"></a><?php echo h($record['Customer']['email']); ?></td>
								<td>
									<?php 
									if(!$record['Customer']['trade'])
									{
										echo (!empty($record['Customer']['guest'])) ? 'Guest' : 'Member';
									}else
									{
										echo 'Trade';
									} 
									?>
									<?php if($record['Customer']['trade']):?>
										
										<?php echo (empty($record['Customer']['approved'])) ? '<img src="/img/icons/exclamation.png" alt="Account requires approval" title="Account requires approval" class="right" />' : '<img src="/img/icons/accept.png" alt="" class="right" />'; ?>
									
									<?php endif;?>
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
						
				<p><?php echo (isset($records)) ? 'No customers found. Please refine your search.' : 'Please use the filter to the left to search for customers.'; ?></p>
			
			<?php endif; ?>
						
			
		</div>
		
	<?php echo $form->end(); ?>
	
</div>


