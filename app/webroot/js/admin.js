
$(function()
{	
	jQuery('ul.sf-menu').superfish({
		autoArrows: false,
		animation: {opacity: 'show'},
		speed: 0
	});
	
	$("#modifier").change(function() {
		showDiscountModifierValueInputs();
	});
	
	showDiscountModifierValueInputs();
	
	function showDiscountModifierValueInputs()
	{
		if ($("#modifier").val() == "fixed") {
			$("#percentage-value").hide();
			$("#money-values").show();
		} 
		else {
			$("#money-values").hide();
			$("#percentage-value").show();
		}
	}
	
	$(".add-tier a").click(function() {
		$(this).parent().hide();
		$(this).parent().parent().children("div").show();
		return false;
	});
	$(".new-qty").focus(function() {
		$(this).val("").css("color", "#000");
	});
	$(".cancel-new-tier").click(function() {
		$(this).parent().hide();
		$(this).parent().parent().children("p").show();
		return false;
	});


	$(".attr-name input").click(function() {
		var valuesID = "#attr-values-" + $(this).attr("id").substring(5);
		if ($(valuesID).css("display") == "block")
		{
			$(valuesID).hide();
			$(valuesID + " input").attr("checked", false);
		}
		else
		{
			$(valuesID).show();
		}
	});
	

	
});
