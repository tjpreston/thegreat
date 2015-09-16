<script type="text/javascript">
$(function() {

	$("#add-to-basket-submit").click(function() {
		$(this).parents("form").attr("action", "/basket/add");
	});

	// $("#decrease-qty").click(function() {
	// 	if ($(".qty-input").val() > 1) {
	// 		$(".qty-input").val(parseInt($(".qty-input").val()) - 1);
	// 	}
	// })
	// $("#increase-qty").click(function() {
	// 	$(".qty-input").val(parseInt($(".qty-input").val()) + 1);
	// })
	
})
</script>

<div id="qty-box">
	<?php echo $form->label('Basket.0.qty', 'Qty', array(
		'class' => 'orange'
	)); ?>
	<!-- <img class="decrease-qty" src="/img/icons/bn-minus.png" alt="" /> -->
	<?php echo $form->text('Basket.0.qty', array(
		'class' => 'qty-input',
		'div' => false,
		'value' => 1
	)); ?>
	<!-- <img class="increase-qty" src="/img/icons/bn-plus.png" alt="" /> -->
</div>


