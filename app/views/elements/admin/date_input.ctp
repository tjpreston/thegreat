
<?php $value = (!empty($value) && ($value != '0000-00-00')) ? $this->Time->format('d/m/Y', $value) : ''; ?>


	<label for="-input"><?php echo ucwords($dir); ?></label>
	<input name="data<?php echo $input; ?>"  id="<?php echo $field; ?>-input" class="date-input" value="<?php echo $value; ?>" />

					
<?php // echo $this->Form->hidden($input, array('id' => $field)); ?>

<script>
$(function() {
	$("#<?php echo $field; ?>-input").datepicker({
		dateFormat: "dd/mm/yy"
	});
});
</script>

