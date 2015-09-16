
$(function() {

	var i = 0;
	var products = {};
	var newProduct = null;

	var prodID = 0;
	var optionID = 0;

	// Init

	$("#prod-code").keyup(function(e) {

		prodID = 0;
		
		var code = (e.keyCode) ? e.keyCode : e.which;
		if (code == 13) { return false; }
		
		$.getJSON("/ajax/quick_order/find_product/" + $(this).val(), function(data, text, xhr) {			
			
			$("#prod-preview").text("");
			
			if (!data) {
				$("#result, #add-to-basket").hide();
				return;
			}

			prodID = data.id;
			if (data.optionid !== undefined)
			{
				optionID = data.optionid;
			}
			
			$("#result, #add-to-basket").show();
			$("#prod-preview").text(data.name);

			if (data.img)
			{
				$("#prod-preview").prepend('<img src="' + data.img + '" />');
			}
			
		});
		
		return false;

	});
	
	$("#save").click(function() {
		saveProduct();
		return false;
	});
	
	$("#prod-code, #prod-qty").keydown(function(e) {
	
		var code = (e.keyCode) ? e.keyCode : e.which;
		if ((code == 13) && (newProduct !== null)) 
		{
			$(this).blur();
			saveProduct();
			return false;
		}

	});

	$("#prods .delete").live("click", function() {	
		
		var key = $(this).attr("id").substring(6);
		delete products[key];
		
		$(this).parents("tr").remove();
		
		showHideSubmit();

		i--;

		return false;

	});

	$(".quick-qty-update").live("click", function() {

		var trtag = $(this).parent().parent();		
		var ids = trtag.attr("id").split("-");
		var qty = trtag.find(".quick-qty").val();

		prodID = ids[1];
		optionID = 0;

		if (ids[2])
		{
			optionID = ids[2];
		}
		
		var prodKey = prodID + '-' + optionID;
		
		var url = "/ajax/quick_order/get_product/" + prodID + "/" + optionID + "/" + qty;

		$.getJSON(url, function(data, text, xhr) {
			
			if (!data) { return; }

			var price = Number(data.price);

			trtag.find('#prod-' + prodKey + '-price').text(price.toFixed(2));

			var total = price * Number(qty);
			trtag.find('#prod-' + prodKey + '-total').text(total.toFixed(2));

		});

	});


	function saveProduct() 
	{
		var prodKey = prodID + '-' + optionID;

		for (key in products)
		{
			if (prodKey === key)
			{
				return false;
			}
		}

		var qty = $("#prod-qty").val();
		qty = (isNaN(qty) || (qty < 1)) ? 1 : qty;
		
		var url = "/ajax/quick_order/get_product/" + prodID + "/" + optionID + "/" + qty;

		$.getJSON(url, function(data, text, xhr) {

			if (!data) { return; }

			var total = Number(data.price) * Number(qty);

			var html = '<tr id="prod-' + prodKey + '">';

			if (optionID)
			{
				html += '<input type="hidden" name="data[Basket][' + i + '][product_option_stock_id]" value="' + optionID + '" />';
			}
			
			html += '<td>';
			if (data.img)
			{
				html += '<img class="prod-img" src="' + data.img + '" />';
			}
			html += data.name;
			html += '</td>';

			html += '<td>&pound;<span id="prod-' + prodKey + '-price">' + data.price + '</span></td>';

			html += '<td>';
			html += '<input type="hidden" name="data[Basket][' + i + '][product_id]" value="' + prodID + '" />';
			html += '<input class="quick-qty" type="text" name="data[Basket][' + i + '][qty]" value="' + qty + '" />'
			html += '<img src="/img/icons/arrow_refresh.png" alt="Update" class="quick-qty-update" />';
			html += '</td>';

			html += '<td>&pound;<span id="prod-' + prodKey + '-total">' + total.toFixed(2) + '</span></td>';

			html += '<td><img id="delete' + prodKey + '" class="delete" src="/img/icons/delete2.png" /></td>';

			html += '</tr>';

			$("#prods").append(html);

			i++;

			products[prodKey] = data;

			resetState();
			showHideSubmit();

		});

		return false;
	
	}
	
	function resetState()
	{	
		$("#prod-code").val("").focus();

		prodID = 0;
		optionID = 0;

		$("#prod-preview").text("");
		$("#result, #add-to-basket").hide();
	}
	
	function showHideSubmit() 
	{
		var count = $('#prods > tr').length;
		var display = (count > 0) ? 'block' : 'none';
		$("#quick-buy").css("display", display);
	}

});


