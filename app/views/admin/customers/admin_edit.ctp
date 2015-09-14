<?php

$tabs = array(
	'personal' 	=> array(0, 'Personal &amp; Contact'),
	'addresses'	=> array(1, 'Customer Addresses')
);

$tabIndex = 0;
if (!empty($this->params['named']['tab']) && !empty($tabs[$this->params['named']['tab']]))
{
	$tabIndex = $tabs[$this->params['named']['tab']][0];
}
else if (!empty($initTab))
{
	$tabIndex = $tabs[$initTab][0];
}

?>

<script type="text/javascript">
$(function() {
	
	$("ul#product-nav").tabs("div.panes > div.pane", {
		initialIndex: <?php echo intval($tabIndex); ?> 
	});
	
	var api = $("ul#product-nav").data("tabs");
	
	$("#main-form").submit(function() {
		var pane = $(api.getCurrentPane())
		var id = pane.attr("id").substring(5);
		$("#last-pane").val(id);
		return true;
	});
	
});
</script>

<div id="admin-content">
	
	<div id="side-col">
		<p><a href="/admin/customers" class="icon-link back-link">Back to List</a></p>
		<ul id="product-nav">
			<?php $i = 0; ?>
			<?php foreach ($tabs as $tab => $tabData): ?>
				<?php $error = (!empty($errorsOnTabs) && in_array($tab, $errorsOnTabs)) ? ' class="tab-error"' : ''; ?>
				<li<?php echo $error; ?>><a href="#"><?php echo $tabData[1]; ?></a></li>
				<?php $i++; ?>
			<?php endforeach; ?>
		</ul>
	</div>
	
	<?php echo $form->create('Customer', array('action' => 'save', 'id' => 'main-form')); ?>
	
		<input type="hidden" name="last_pane" id="last-pane" value="personal" />
		
		<!--
		<input type="hidden" name="data[Customer][default_billing_address_id]" value="0" />
		<input type="hidden" name="data[Customer][default_shipping_address_id]" value="0" />
		-->
		
		<?php echo $form->input('Customer.id'); ?>
		
		<div class="panes">
			
			<?php echo $session->flash(); ?>
			
			<div id="header">
				<h1><?php echo h($this->data['Customer']['first_name']) . ' ' . h($this->data['Customer']['last_name']); ?></h1>
			</div>
			<?php if(!empty($this->data['Customer']['trade'])): ?>
				<div id="flashMessage" class="failure">
					<?php if ($this->data['Customer']['trade']): ?>
						<?php echo (empty($this->data['Customer']['approved'])) ? '<a href="/admin/customers/trade_approve/' . $this->data['Customer']['id']. '" >Activate trade account</a>' : '<a href="/admin/customers/trade_deactivate/' . $this->data['Customer']['id']. '" >Deactivate trade account</a>'; ?>



						
						<!-- echo $form->input('Customer.approved'); -->
					<?php endif; ?>
				</div>
			<?php endif;?>
			<div id="pane-personal" class="pane">
				
				<?php if (!empty($this->data['Customer']['pending'])): ?>
					<div class="info-message">
						This customer requires approval before they can log in.&nbsp;
						<a href="/admin/customers/approve/<?php echo $this->data['Customer']['id']; ?>">Approve</a> | <a href="/admin/customers/delete/<?php echo $this->data['Customer']['id']; ?>">Reject</a>
					</div>
				<?php endif; ?>
				
				<div class="fieldset-header"><span>General</span></div>
				<div class="fieldset-box">
					<fieldset>
						<?php //echo $form->input('Customer.customer_group_id', array('empty' => array(0 => 'No group'))); ?>
						<?php echo $form->input('Customer.allow_payment_by_account'); ?>
					</fieldset>	
				</div>
				
				<div class="fieldset-header"><span>Personal &amp; Contact Details</span></div>
				<div class="fieldset-box">
					<fieldset>
						<?php
						echo $form->input('Customer.first_name');
						echo $form->input('Customer.last_name');
						echo $form->input('Customer.email');
						echo $form->input('Customer.phone');
						?>
					</fieldset>	
				</div>
				
				<div class="fieldset-header"><span>Customer Password</span></div>
				<div class="fieldset-box">
					<fieldset>
						<?php
						echo $form->input('Customer.password_main', array(
							'label' => 'New Password',
							'type' => 'password',
							'value' => ''
						));
						echo $form->input('Customer.password_confirm', array(
							'label' => 'Confirm Password',
							'type' => 'password',
							'value' => ''
						));
						?>
					</fieldset>	
				</div>		

				<div class="fieldset-box">
					<?php echo $form->submit('Save', array('div' => 'submit single-line')); ?>
				</div>
					
			</div>
			
			
			<script type="text/javascript">
			$(function() {
				
				$("#add-address a").click(function() {
					$("#add-address").hide();
					$("#add-address-box").show();
					$("#add-address-box input, #add-address-box select").each(function() {
						this.disabled = false;
					});
					return false;
				});
				
				$(".add-address-cancel").click(function() {
					$("#add-address").show();
					$(this).parents(".address-box").hide();
					$(this).parents(".address-box").find("input, select").each(function() {
						this.disabled = true;
					});
					return false;
				});
				
				$("#add-address-box input, #add-address-box select").each(function() {
					this.disabled = true;
				});
				
				$(".delete-address-link").click(function() {
					return confirm("Are you sure wish to delete this address?");
				});
				
			});
			</script>
			
			<div id="pane-addresses" class="pane">
				
				<?php $lastK = 0; ?>
				
				<?php foreach ($this->data['CustomerAddress'] as $k => $address): ?>
				
					<?php $header = (isset($address['id'])) ? 'Customer Address #' . ($k + 1) : 'Add New Customer Address'; ?>
					
					<div class="address-box">
						<div class="fieldset-header">
							<span><?php echo $header; ?></span>

							<?php if (isset($address['id'])): ?>
								<a class="delete-address-link" href="/admin/customer_addresses/delete/<?php echo intval($address['id']); ?>">Delete this address</a>
							<?php else: ?>
								<a id="cancel-<?php echo $k; ?>" class="add-address-cancel" href="#">Cancel</a>
							<?php endif; ?>

						</div>
						<div class="fieldset-box">
							<fieldset>				
								<?php if (isset($address['id'])): ?>							
									<?php echo $form->input('CustomerAddress.' . $k . '.id'); ?>
								<?php endif; ?>
								
								<?php $id = (!empty($address['id'])) ? $address['id'] : 'new'; ?>
								<?php echo $this->element('admin/customers/address_inputs', array(
									'k' => $k, 
									'id' => $id,
									// 'defaultBillingAddress' => $this->data['Customer']['default_billing_address_id'],
									// 'defaultShippingAddress' => $this->data['Customer']['default_shipping_address_id']
								));
								?>					
							</fieldset>

						</div>
					</div>
					
					<?php $lastK = $k + 1; ?>
				
				<?php endforeach; ?>
				
				
				
				<?php $k = $lastK; ?>
				
				<p id="add-address"<?php echo (!empty($addingAddress)) ? ' style="display: none;"' : ''; ?>><img src="/img/icons/book_add.png" alt="" /><a href="#">Add New Customer Address</a></p>
				
				<div id="add-address-box" class="address-box" style="display: none;">
					<div class="fieldset-header">
						<span>Add New Customer Address</span>
						<a id="cancel-<?php echo $k; ?>" class="add-address-cancel" href="#">Cancel</a>
					</div>
					<div class="fieldset-box">
						<fieldset>
							<?php echo $this->element('admin/customers/address_inputs', array('k' => $lastK, 'id' => 'new')); ?>
						</fieldset>
					</div>
					<div class="fieldset-box">
						<?php echo $form->submit('Save', array('div' => 'submit single-line')); ?>
					</div>
				</div>
								
				
				
			</div>
			
			
			
		</div>
		
	<?php echo $form->end(); ?>
	
</div>


