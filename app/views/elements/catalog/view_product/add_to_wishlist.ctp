<script type="text/javascript">
$(function() {
	$("#add-to-wishlist-p").html('<input id="add-to-wishlist" type="image" name="add-to-wishlist" src="/img/bn-add-to-wishlist.png" />');
	$("#add-to-wishlist").click(function() {
		$(this).parents("form").attr("action", "/wishlist/add");
	});
})
</script>

<p id="add-to-wishlist-p"></p>