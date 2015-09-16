<select id="orderStatusSelect" name="<?php echo $selectName; ?>" class="order-status-<?php echo intval($orderStatusID); ?>">
	
	<?php if (!empty($allOption)): ?>
		<option value="0" class="order-status-0">All</option>
	<?php endif; ?>
	
	<?php foreach ($orderStatuses as $k => $v): ?>	
		<?php $selected = ($orderStatusID == $k) ? ' selected="selected"' : ''; ?>	
		<option value="<?php echo intval($k); ?>" class="order-status-<?php echo intval($k); ?>"<?php echo $selected; ?>><?php echo h($v); ?></option>
	<?php endforeach; ?>
	
</select>

<script type="text/javascript">
$(function() {
	$("#orderStatusSelect").change(function() {
		$(this).attr("class", "order-status-" + this.value);
	});
});
</script>