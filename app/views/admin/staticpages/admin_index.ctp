

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
				<h1>Static Page Management</h1>
			</div>
                    <div id="staticpages">
                        <?php 
                        foreach ($pagedata as $value)
        {
                        echo '<p>' . '<a href="/admin/staticpages/edit/' . $value['Staticpage']['id'] . '">' . 
                                $value['Staticpage']['name'] . '</a>' . '</p>'; 
        }
                        ?>
                    </div>
				


