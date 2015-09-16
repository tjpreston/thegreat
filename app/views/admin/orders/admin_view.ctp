<?php

echo $this->Html->css(array('admin_print.css'), null, array('inline' => false, 'media' => 'print'));

$tabs = array(
	'info' 		=> array(0, 'Information'),
	'shipments' => array(1, 'Shipments'),
	// 'log' 		=> array(2, 'Notes / Log'),
	// 'cust'		=> array(3, 'Previous Orders')
);

$tabIndex = 0;
if (!empty($this->params['named']['tab']) && !empty($tabs[$this->params['named']['tab']]))
{
	$tabIndex = $tabs[$this->params['named']['tab']][0];
}

?>

<script type="text/javascript">

$(function() {
	$("ul#product-nav").tabs("div.panes > div.pane", {
		initialIndex: <?php echo intval($tabIndex); ?> 
	});	
	$("#order-form").submit(function() {
		var pane = $(api.getCurrentPane())
		var id = pane.attr("id").substring(5);
		$("#last-pane").val(id);
		return true;
	});	
});

</script>

<div id="admin-content">
	
	<div id="side-col">
		<?php

		$returnUrl = $this->Session->read('Order.last_index_url');
		if(empty($returnUrl)) $returnUrl = '/admin/orders';

		?>
		<p><a href="<?php echo $returnUrl; ?>" class="icon-link back-link">Back to List</a></p>
		<ul id="product-nav">
			<?php $i = 0; ?>
			<?php foreach ($tabs as $tab => $tabData): ?>
				<?php $error = (!empty($errorsOnTabs) && in_array($tab, $errorsOnTabs)) ? ' class="tab-error"' : ''; ?>
				<li<?php echo $error; ?>><a href="#"><?php echo $tabData[1]; ?></a></li>
				<?php $i++; ?>
			<?php endforeach; ?>
		</ul>
	</div>

	<div class="panes" style="position: relative;">
		
		<?php echo $session->flash(); ?>			
		
		<div id="header" class="order-header">
			<h1>
				Order #<?php echo h($record['Order']['ref']); ?> | 
				<?php echo $time->format('d M Y H:i:s', $record['Order']['created']); ?>
			</h1>	
		</div>
					
		<div id="pane-information" class="pane">
		
			<div class="half-width">
				
				<div class="half-box odd">
					<div class="fieldset-header"><span>Order Summary</span></div>
					<div class="fieldset-box" style="height: 90px;">
						<dl>
							<dt style="padding-top: 7px;">Order Status</dt>
							<dd>
								<?php echo $form->create('Order', array('id' => 'order-status-form', 'action' => 'save')); ?>
									<?php echo $form->input('id'); ?>
									<?php echo $this->element('admin/orders/order_status_select', array(
										'selectName' => 'data[Order][order_status_id]',
										'orderStatusID' => $this->data['Order']['order_status_id'],
									)); ?>
									<?php if ($this->data['Order']['order_status_id'] != Configure::read('OrderStatuses.failed')): ?>
										<?php echo $form->submit('/img/icons/disk.png', array('div' => false, 'id' => 'oss')); ?>
									<?php endif; ?>
								<?php echo $form->end(); ?>
							</dd>
							<dt>Processed By</dt>
							<dd><?php echo (!empty($this->data['Order']['processor'])) ? $this->data['Order']['processor'] : 'On Account'; ?></dd>
						</dl>
					</div>
				</div>
				
				<div class="half-box">
					<div class="fieldset-header"><span>Customer Details</span></div>
					<div class="fieldset-box" style="height: 90px;">
						<dl>
							<dt>Name</dt>
							<dd>
								<?php
								if (!empty($record['Customer']['id'])) echo '<a href="/admin/customers/edit/' .intval($record['Customer']['id']) . '">';
								echo h($record['Order']['customer_first_name']) . ' ' . h($record['Order']['customer_last_name']);
								if (!empty($record['Customer']['id'])) echo '</a>';
								?>
							</dd>
							<dt>Type</dt>
							<dd>
								<?php echo (!empty($record['Customer']['guest'])) ? 'Guest' : 'Member'; ?>
							</dd>
							<dt>Email</dt>
							<dd>
								<?php $email = (!empty($record['Customer']['id'])) ? h($record['Customer']['email']) : h($record['Order']['customer_email']); ?>
								<a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a>
							</dd>
							<dt>Source IP</dt>
							<dd><?php echo h($record['Order']['placed_from_ip']); ?></dd>
						</dl>
					</div>
				</div>
				
				<div class="half-box odd">
					<div class="fieldset-header"><span>Billing Address</span></div>
					<div class="fieldset-box">
						<p class="order-address">
							<?php
							echo h($record['Order']['customer_first_name']) . ' ' . h($record['Order']['customer_last_name']) . ',<br />';
							echo h($record['Order']['billing_address_1']) . ',<br />';
							if (!empty($record['Order']['billing_address_2']))
							{
								echo h($record['Order']['billing_address_2']) . ',<br />';
							}
							echo h($record['Order']['billing_town']) . ',<br />';
							if (!empty($record['Order']['billing_county']))
							{
								echo h($record['Order']['billing_county']) . ', ';
							}
							echo h($record['Order']['billing_postcode']) . '<br />';
							echo h($record['BillingCountry']['name']);
							?>
						</p>
					</div>
				</div>
				
				<div class="half-box">
					<div class="fieldset-header"><span>Shipping Address</span></div>
					<div class="fieldset-box">
						<p class="order-address">
							<?php
							echo h($record['Order']['shipping_first_name']) . ' ' . h($record['Order']['shipping_last_name']) . ',<br />';
							echo h($record['Order']['shipping_address_1']) . ',<br />';
							if (!empty($record['Order']['shipping_address_2']))
							{
								echo h($record['Order']['shipping_address_2']) . ',<br />';
							}
							echo h($record['Order']['shipping_town']) . ',<br />';
							if (!empty($record['Order']['shipping_county']))
							{
								echo h($record['Order']['shipping_county']) . ', ';
							}
							echo h($record['Order']['shipping_postcode']) . '<br />';
							echo h($record['ShippingCountry']['name']);
							?>
						</p>
					</div>
				</div>
				
			</div>
		
			
			<div>
				<div class="fieldset-header"><span>Order Breakdown</span></div>
				<div class="fieldset-box contains-table">
					<table>
						<thead>
							<tr>
								<th style="width: 100px;">SKU</th>
								<th style="width: 340px;">Product</th>
								<th>Price</th>
								<th>Qty</th>
								<th>Total</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($items as $k => $item): ?>

								<?php $class = (!empty($item['OrderItem']['giftwrap_product_name'])) ? ' class="item-with-gwp"' : ''; ?>
								
								<tr<?php echo $class; ?>>
									<td><?php echo h($item['OrderItem']['product_sku']); ?></td>
									<td><?php echo h($item['OrderItem']['product_name']); ?></td>
									<td>&pound; <?php echo number_format($item['OrderItem']['price'], 2); ?></td>
									<td><?php echo intval($item['OrderItem']['qty']); ?></td>
									<td>&pound; <?php echo number_format(($item['OrderItem']['price'] * $item['OrderItem']['qty']), 2); ?></td>
								</tr>

								<?php if (!empty($item['OrderItem']['giftwrap_product_name'])): ?>

									<tr>
										<td>&nbsp;</td>
										<td>
											└ <img src="/img/icons/present.png" style="vertical-align:bottom" /> Gift wrapping<br />
												<?php if (!empty($item['OrderItem']['custom_text'])): ?>
													Gift Card Message: <?php echo h($item['OrderItem']['custom_text']); ?>
												<?php endif; ?>

										</td>
										<td>&pound; <?php echo number_format(($item['OrderItem']['giftwrap_price'] / $item['OrderItem']['qty']), 2); ?></td>
										<td>&nbsp;</td>
										<td>&pound; <?php echo number_format($item['OrderItem']['giftwrap_price'], 2); ?></td>
									</tr>

								<?php endif; ?>


							<?php endforeach; ?>
						</tbody>
						<tfoot>
							<tr class="totals">
								<td colspan="4" style="text-align: right;">Subtotal</td>
								<td>&pound; <?php echo number_format($record['Order']['subtotal'], 2); ?></td>
							</tr>
							<?php if(!empty($record['Order']['coupon_code'])): ?>
							<tr class="voucher">
								<td colspan="4" style="text-align: right;"><strong>Voucher Code</strong> "<?php echo h($record['Order']['coupon_code']); ?>"</td>
								<td>- &pound; <?php echo number_format($record['Order']['discount_total'], 2); ?></td>
							</tr>
							<?php endif; ?>
							<tr>
								<td colspan="4" style="text-align: right;">
                                	Delivery 
                                   <?php echo (!empty($record['Order']['free_shipping'])) ? 'Free Shipping' : $record['Order']['shipping_carrier_service_name']; ?>
                                </td>
								<td>&pound; <?php echo number_format($record['Order']['shipping_cost'], 2); ?></td>
							</tr>
							<!-- <tr class="totals">
								<td colspan="4" style="text-align: right;">Tax</td>
								<td>&pound; <?php echo number_format($record['Order']['subtotal_tax'] + $record['Order']['shipping_tax'], 2); ?></td>
							</tr> -->
							<?php if($record['Order']['tax_rate'] < Configure::read('Tax.rate')): ?>
							<tr class="totals">
								<td colspan="4" style="text-align: right;">Tax Reduction</td>
								<?php
								$taxRate = Configure::read('Tax.rate');
								$taxRate = (100 + $taxRate) / 100;

								$vatReduction = ($record['Order']['grand_total'] * $taxRate) - $record['Order']['grand_total'];
								?>
								<td>-&pound;<?php echo number_format($vatReduction, 2); ?></td>
							</tr>
							<?php endif; ?>
							<tr class="totals" style="font-size: 120%;">
								<td colspan="4" style="text-align: right;">Grand Total</td>
								<td>&pound; <?php echo number_format($record['Order']['grand_total'], 2); ?></td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
			
			<?php if (!empty($record['Order']['processor']) && $this->data['Order']['processor'] == Configure::read('Payments.processor')): ?>
				
				<?php
				$paymentElement = Inflector::underscore(Configure::read('Payments.processor'));
				echo $this->element('admin/payments/' . $paymentElement);
				?>
			
			<?php endif; ?>
		
		</div>
		
		

		<div id="pane-shipments" class="pane">
			
			<script type="text/javascript">
			$(function() {
				$("#new-shipment-link a").click(function() {
					$("#new-shipment-link").hide();
					$("#new-shipment").show();
				});
				$(".shipment-icon").click(function() {
					var id = $(this).parent().parent().attr("id").substring(14);
					$("#shipment-" + id).show();
					$(this).parent().parent().hide();
					return false;
				});
			});
			</script>
			
			<?php if (!empty($shipments)): ?>
			
				<div id="existing-shipments">
			
					<?php foreach ($shipments as $k => $shipment): ?>
					
						<div id="show-shipment-<?php echo intval($shipment['Shipment']['id']); ?>" class="fieldset-box">
							<p><a href="#" class="icon-link shipment-icon">Show Shipment #<?php echo $k + 1; ?></a></p>
						</div>
						
						<div id="shipment-<?php echo intval($shipment['Shipment']['id']); ?>" style="display: none;">
							<div class="fieldset-header">
								<span>Shipment #<?php echo $k + 1; ?></span>
								<a href="/admin/shipments/note/<?php echo intval($shipment['Shipment']['id']); ?>" class="icon-link lorry-go-link">Delivery Note</a>
							</div>
							<div class="fieldset-box" style="margin-bottom: 0; border-bottom: 1px solid #ccc;">
								<dl>
									<dt>Shipped</dt>
									<dd><?php echo $time->format('M Y', $shipment['Shipment']['created']); ?></dd>
								</dl>
								<dl>
									<dt>Carrier / Service</dt>
									<dd><?php echo h($shipment['Shipment']['shipping_carrier_service_name']); ?></dd>
								</dl>
								<dl>
									<dt>Tracking Reference</dt>
									<dd><a href="#"><?php echo h($shipment['Shipment']['tracking_ref']); ?></a></dd>
								</dl>
							</div>
							<div class="fieldset-box contains-table">
								<table>
									<thead>
										<tr>
											<th style="width: 100px;">SKU</th>
											<th style="width: 340px;">Product</th>
											<th style="text-align: center; width: 60px;">Qty Shipped</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($shipment['ShipmentItem'] as $k => $item): ?>
											<tr>
												<td><?php echo h($item['OrderItem']['product_sku']); ?></td>
												<td><?php echo h($item['OrderItem']['product_name']); ?></td>
												<td style="text-align: center;"><?php echo intval($item['qty_shipped']); ?></td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						</div>
						
					<?php endforeach; ?>
					
				</div>
			
			<?php endif; ?>
											
			
			<hr />

											
			<?php if (empty($hasShipped)): ?>
				
				<div id="new-shipment-link" class="fieldset-box">
					<p><a href="#" class="icon-link new-shipment-icon">New Shipment</a></p>
				</div>
				
				<?php echo $form->create('Shipment', array('url' => '/admin/shipments/save')); ?>
								
					<div id="new-shipment" style="display: none;">
					
						<script type="text/javascript">
						$(function() {
							$("#service-override").click(function() {
								if (this.checked == true) {
									$("#service-id").attr("disabled", "");
								}
								else {
									$("#service-id").attr("disabled", "disabled");
									$("#service-id option").attr("selected", "");
									var selectedID = $("#service-default");
									$("#service-id option").each(function() {
										if ($(this).val() == selectedID) {
											$(this).attr("selected", "selected");
										}
									});
								}
							});
						});
						</script>
					
						<div class="fieldset-header"><span>New Shipment Details</span></div>
						<div class="fieldset-box">
							<fieldset>
								<?php
								echo $form->hidden('Shipment.order_id', array(
									'value' => $this->data['Order']['id']
								));
								/*
								echo $form->hidden('Shipment.carrier_shipping_service_id', array(
									'id' => 'service-default',
									'value' => $this->data['Order']['shipping_carrier_service_id']
								));
								echo $form->input('Shipment.carrier_shipping_service_id', array(
									'id' => 'service-id',
									'selected' => $this->data['Order']['shipping_carrier_service_id'],
									'disabled' => 'disabled',
									'after' => '<input type="checkbox" id="service-override" name="override" value="null" style="width: 13px; margin-left: 20px; vertical-align: top; margin-right: 6px;" /><label for="#" style="vertical-align: top; float: none; width: auto;">Override</labe>'
								));
								*/
								echo $form->input('Shipment.tracking_ref', array(
									'style' => 'width: 100px;'
								));
								?>
							</fieldset>		
						</div>
						
						<script type="text/javascript">
						$(function() {
							
							$(".ship-item").click(function() {
								var checkboxID = $(this).attr("id");
								var itemID = checkboxID.substring(10);
								if (this.checked == true) {
									$("#qty-input-" + itemID).attr("disabled", "");
									$("#qty-input-" + itemID).val(getQtyToShip(itemID));									
								}
								else {
									$("#qty-input-" + itemID).attr("disabled", "disabled");
									$("#qty-input-" + itemID).val("");
								}								
							});
							
							$(".decrease-qty").click(function() {
								var buttonID = $(this).attr("id");
								var itemID = buttonID.substring(13);
								var qtyBox = $("#qty-input-" + itemID);
								if (qtyBox.val() > 1) {
									qtyBox.val(parseInt(qtyBox.val()) - 1);
								}
							});
							
							$(".increase-qty").click(function() {
								var buttonID = $(this).attr("id");
								var itemID = buttonID.substring(13);
								var qtyBox = $("#qty-input-" + itemID);
								if (qtyBox.val() < getQtyToShip(itemID)) {
									qtyBox.val(parseInt(qtyBox.val()) + 1);
								}
							});
							
							var startingValue = "";
							
							$(".qty-input").keydown(function() {
								startingValue = $(this).val();
							});
							
							$(".qty-input").change(function() {
								var qtyBox = $(this);
								var qtyBoxID = qtyBox.attr("id");
								var itemID = qtyBoxID.substring(10);
								if ((qtyBox.val() < 1) || (qtyBox.val() > getQtyToShip(itemID))) {
									qtyBox.val(startingValue)
									return false;
								}
							});
							
							function getQtyToShip(itemID) {
								var qtyOrdered = parseInt($("#item-" + itemID + "-ordered").val());
								var qtyShipped = parseInt($("#item-" + itemID + "-shipped").val());
								return qtyOrdered - qtyShipped;
							}
							
						});
						</script>
						
						<div class="fieldset-header"><span>New Shipment Contents</span></div>
						<div class="fieldset-box contains-table">
							<table>
								<thead>
									<tr>
										<th>Ship</th>
										<th style="width: 100px;">SKU</th>
										<th style="width: 340px;">Product</th>
										<th style="text-align: center;">Ordered</th>
										<th style="text-align: center;">Shipped</th>
										<th class="qty-to-ship">Qty to Ship</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($items as $k => $item): ?>
									
										<?php $completelyShipped = ($item['OrderItem']['qty'] == $item['OrderItem']['qty_shipped']); ?>
										
										<tr>
											<td style="text-align: center;">
												
												<?php
												
												echo $form->hidden('Shipment.ShipmentItem.' . $k . '.order_item_id', array(
													'value' => $item['OrderItem']['id']
												));
												echo $form->hidden('Shipment.ShipmentItem.' . $k . '.ordered', array(
													'id' => 'item-' . intval($item['OrderItem']['id']) . '-ordered',
													'value' => $item['OrderItem']['qty'],
												));
												echo $form->hidden('Shipment.ShipmentItem.' . $k . '.shipped', array(
													'id' => 'item-' . intval($item['OrderItem']['id']) . '-shipped',
													'value' => $item['OrderItem']['qty_shipped'],
												));
												
												if (!$completelyShipped)
												{
													echo $form->checkbox('Shipment.ShipmentItem.' . $k . '.ship', array(
														'id' => 'ship-item-' . intval($item['OrderItem']['id']),
														'class' => 'ship-item',
														'value' => 1,
														'checked' => false
													));
												}
												else
												{
													echo '-';
												}
												
												?>
												
											</td>
											
											<td><?php echo h($item['OrderItem']['product_sku']); ?></td>
											<td><?php echo h($item['OrderItem']['product_name']); ?></td>
											<td style="text-align: center;"><?php echo intval($item['OrderItem']['qty']); ?></td>
											<td style="text-align: center;"><?php echo intval($item['OrderItem']['qty_shipped']); ?></td>
											<td class="qty-to-ship">
												
												<?php if (!$completelyShipped): ?>
												
													<img id="decrease-qty-<?php echo intval($item['OrderItem']['id']); ?>" class="decrease-qty" src="/img/icons/bn-minus-trans.png" />
													<?php echo $form->input('Shipment.ShipmentItem.' . $k . '.qty_shipped', array(
														'id' => 'qty-input-' . intval($item['OrderItem']['id']),
														'label' => false,
														'class' => 'qty-input',
														'div' => false,
														'disabled' => 'disabled',
														'value' => ''
													)); ?>
													<img id="increase-qty-<?php echo intval($item['OrderItem']['id']); ?>" class="increase-qty" src="/img/icons/bn-plus-trans.png" />
	
												<?php else: ?>
													
													-
												
												<?php endif; ?>
	
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
						
						<div class="half-width">
							
							<script type="text/javascript">
							$(function() {
								$("#switch-address-link").click(function() {
									$("#change-shipment-del-address").hide();
									$("#switch-del-address-box").show();
									return false;
								});
								$("#cancel-address-switch").click(function() {
									$("#switch-del-address-box").hide();
									$("#change-shipment-del-address").show();								
									return false;
								});
								$("#switch-del-address-box select").change(function() {
									
								});
							});
							</script>
							
							<div class="half-box odd">
								<div class="fieldset-header"><span>Delivery Address</span></div>
								<div class="fieldset-box" style="height: 128px;">
									<p class="order-address" style="margin-bottom: 16px;">
										<?php
										echo h($this->data['Order']['shipping_first_name']) . ' ' . h($this->data['Order']['shipping_last_name']) . ',<br />';
										echo h($this->data['Order']['shipping_address_1']) . ',<br />';
										if (!empty($this->data['Order']['shipping_address_2']))
										{
											echo h($this->data['Order']['shipping_address_2']) . ',<br />';
										}
										echo h($this->data['Order']['shipping_town']) . ',<br />';
										echo h($this->data['Order']['shipping_county']) . ', ';
										echo h($this->data['Order']['shipping_postcode']) . '<br />';
										echo h($this->data['ShippingCountry']['name']);
										?>
									</p>
									<!--
									<p id="change-shipment-del-address">
										<a id="switch-address-link" class="icon-link address-link" href="#">Switch Address</a>
										<a class="icon-link new-address-link" href="/admin/customers/edit/<?php echo intval($this->data['Order']['customer_id']); ?>">Modify / New Address</a>
									</p>
									<p id="switch-del-address-box" style="display: none;">
										<?php echo $form->hidden('order_delivery_address_id', array(
											'id' => 'order-delivery-addressid',
											'value' => $this->data['Order']['delivery_address_id']
										)); ?>
										<?php echo $form->select(
											'delivery_address', 
											$customerAddressList, 
											$this->data['Order']['delivery_address_id'],
											array('id' => 'asff', 'empty' => false)
										); ?>
										<a id="cancel-address-switch" href="#"><img src="/img/icons/cancel.png" /></a>
									</p>
									-->
								</div>
							</div>
							
							<div class="half-box">
								<div class="fieldset-header"><span>Notification &amp; Comments</span></div>
								<div class="fieldset-box" style="height: 128px;">
									<div>
										<input type="checkbox" name="data[Shipment][send_notification]" value="1" checked="checked" style="vertical-align: bottom; margin-left: 1px; margin-right: 4px;" />
										<label for="#">Send notification to customer</label>
									</div>
									<?php echo $form->textarea('comments', array(
										'style' => 'width: 286px; height: 70px; padding: 4px; margin: 10px 0;',								
										
									)); ?>			
									<div>
										<input type="checkbox" name="data[Shipment][append_comments]" value="1" style="vertical-align: bottom; margin-left: 1px; margin-right: 4px;" />
										<label for="#">Append comments to notification</label>
									</div>
									<?php ?>
								</div>
							</div>
							
						</div>
				
						<div class="fieldset-box">
							<?php echo $form->submit('Ship', array('div' => 'submit single-line')); ?>
						</div>
						
					</div>
				
				<?php else: ?>
				
					<p class="icon-link tick-link">Order shipped.</p>
				
				<?php endif; ?>
				
				<?php if(empty($hasShipped)): ?>
					<p class="icon-link warn-link">There are<?php if(!empty($shipments)) echo ' still'; ?> items awaiting shipment.</p>
				<?php endif; ?>
				
			<?php echo $form->end(); ?>
			
		</div>
		
		
		<div id="pane-log" class="pane">
			
			<?php if (!empty($logItems)): ?>
			
				<div class="fieldset-header">Order Notes</div>
				<div class="fieldset-box contains-table">
					
					<table id="log-table">
						<tbody>
							<?php foreach ($logItems as $k => $item): ?>
								<tr>
									<td style="width: 150px;">
										<p>
											<?php if (!empty($item['OrderNote']['icon'])): ?>
												<?php
												$paths = Configure::read('OrderLogIcons');
												$path = '/img/icons/' . $paths[$item['OrderNote']['icon']] . '.png';
												?>
												<img src="<?php echo $path; ?>" />
											<?php endif; ?>						
											<strong><?php echo $time->format('j M Y', $item['OrderNote']['created']); ?></strong> <?php echo $time->format('H:i:s', $item['OrderNote']['created']); ?>
										</p>
									</td>
									<td>
										<p class="log-item-name">
											<?php echo h($item['OrderNote']['name']); ?>
										</p>
										<?php if (!empty($item['OrderNote']['content'])): ?>
											<p><?php echo h($item['OrderNote']['content']); ?></p>
										<?php endif; ?>
										<?php if (!empty($item['OrderNote']['customer_notified'])): ?>
											<p class="log-item-emailed"><img src="/img/icons/email.png" alt="Customer notified" title="Customer notified" /> Customer notified</p>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
								
				</div>
				
			<?php endif; ?>
			
			<script type="text/javascript">
			$(function() {
				$("#new-note-link-box a").click(function() {
					$("#new-note-link-box").hide();
					$("#new-note-form").show();
					return false;
				});
			});
			</script>
			
			<!-- <p id="new-note-link-box"><a href="#" class="icon-link note-icon">Add Note</a></p> -->
			
			<?php echo $form->create('OrderNote', array('id' => 'new-note-form', 'action' => 'save')); ?>
			
				<?php echo $form->hidden('OrderNote.order_id', array('value' => $record['Order']['id'])); ?>
			
				<div class="fieldset-header">New Note</div>
				<div class="fieldset-box">
					<fieldset>
						<div style="float: left; width: 460px;">
							<?php echo $form->input('OrderNote.content', array('label' => false, 'div' => false)); ?>
						</div>
						<div style="float: right; width: 170px; margin-top: 36px;">
							<input type="checkbox" name="data[OrderNote][customer_notified]" value="1" checked="checked" style="vertical-align: bottom; margin-left: 1px; margin-right: 4px;" />
							<label for="#">Send note to customer</label>
							<?php echo $form->submit('Save', array('id' => 'note-button')); ?>
						</div>				
					</fieldset>
				</div>
			
			<?php echo $form->end(); ?>
			
		</div>
		
		<div class="pane">
			
			<p>Not yet implemented.</p>
			
		</div>
		
		
	</div>
		
	
	
</div>


