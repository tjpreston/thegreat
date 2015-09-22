

<div id="admin-content">
		
	<div id="side-col">
		
		<ul id="admin-links">
			<a href="/admin/products/new" class="icon-link add-link">Add New Product</a></p>
		</ul>
		
	<!--	<?php echo $this->element('admin/products/filter'); ?> -->
		
	</div>
	
	<?php echo $form->create('Product', array('action' => 'save')); ?>
	
		<div class="panes">
			
			<?php echo $session->flash(); ?>
			
			<div id="header">
				<h1>Product Management</h1>
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
							<th class="first-th"><?php echo $this->Paginator->sort('Active', 'active'); ?></th>
							<th><?php echo $this->Paginator->sort('Type', 'type'); ?></th>
							<th><?php echo $this->Paginator->sort('SKU', 'sku'); ?></th>
							<th><?php echo $this->Paginator->sort('Product', 'ProductName.name'); ?></th>
							<th style="border-right: 0;">Category</th>
							<th class="last-th" style="width: 60px;">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($records as $k => $record): ?>
							<?php $editUrl = '/admin/products/edit/' . intval($record['Product']['id']); ?>
							<tr>
								<td><?php
									if ($record['Product']['active']) {
										$title = 'Active';
										$url = 'icons/order-status-4-small.png';
									} else {
										$title = 'Inactive';
										$url = 'icons/order-status-1-small.png';
									}

									echo $this->Html->image($url, array('title' => $title));
								?></td>
								<td><?php echo h(ucwords($record['Product']['type'])); ?></td>
								<td><a href="<?php echo $editUrl; ?>"><?php echo h($record['Product']['sku']); ?></a></td>	
								<td><?php echo htmlentities($record['ProductName']['name']); ?></td>
								<td style="border-right: 0;">
									<?php echo !empty($record['CategoryName']['name']) ? h($record['CategoryName']['name']) : 'N/A'; ?>
								</td>
								<td class="actions">
									<a href="/admin/products/delete/<?php echo intval($record['Product']['id']); ?>" title="Delete product"><img src="/img/icons/delete.png" alt="" /></a>
									<?php /*<a href="/admin/products/duplicate/<?php echo intval($record['Product']['id']); ?>" title="Duplicate product"><img src="/img/icons/page_copy.png" alt="" /></a> */?>
									<a href="/admin/products/edit/<?php echo intval($record['Product']['id']); ?>" title="Edit product"><img src="/img/icons/page_edit.png" alt="" /></a>
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

