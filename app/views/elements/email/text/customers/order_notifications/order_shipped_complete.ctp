<?php if (!empty($comments)): ?>
----------------------------------------------------------------------
SHIPMENT COMMENTS:
<?php echo wordwrap($comments, 62, "\n", false); ?> 
----------------------------------------------------------------------<?php echo "\n"; ?> 
<?php endif; ?>
We are pleased to inform you the the following items of your order have shipped.

<?php foreach ($shipmentItems as $k => $item): ?>
<?php $id = $item['order_item_id']; ?>
<?php $orderItems[$id]['OrderItem']['this_shipment'] = $item['qty_shipped']; ?>
<?php echo $orderItems[$item['order_item_id']]['Product']['sku'] . ' - ' . $orderItems[$item['order_item_id']]['ProductName']['name']; ?> 
Qty Ordered: <?php echo $orderItems[$item['order_item_id']]['OrderItem']['qty']; ?>    Qty in this Shipment: <?php echo $item['qty_shipped']; ?> 

<?php endforeach; ?>


<?php if (!empty($tracking_ref)): ?>
Your shipment reference code is <?php echo $tracking_ref; ?>. 
<?php endif; ?> 
Your items were sent to:

   <?php echo $shipment['Shipment']['shipping_first_name'] . ' ' . $shipment['Shipment']['shipping_last_name']; ?> 
   <?php echo $shipment['Shipment']['shipping_address_1']; ?>, 
   <?php if (!empty($shipment['Shipment']['shipping_address_2'])): echo $shipment['Shipment']['shipping_address_2'] . ",\n"; endif; ?>
   <?php echo $shipment['Shipment']['shipping_town']; ?>, 
   <?php echo $shipment['Shipment']['shipping_county']; ?>, 
   <?php echo $shipment['Shipment']['shipping_postcode']; ?> 


This completes your order.