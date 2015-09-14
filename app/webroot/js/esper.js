$(function() {

	/**
	 * Template
	 *
	 */
	$("#manufacturer-select").change(function() {
		window.location.href = "/brands/" + this.value;
	});
	
	
	/**
	 * Checkout
	 * 
	 */
	$("#new-billing a").click(function() {
		$("#new-billing a").hide();
		$("#choose-billing").hide(0, function() {
			$("#new-billing-form").show(0, function() {
				$("#cancel-new-billing").show();
			});
		});
		$("#choose-billing .select-address input").attr("checked", false);
		$("#new-billing-address").val(1);
		$("#billingFlashMessage").hide();
		return false;
	});
	$("#cancel-new-billing a").click(function() {
		$("#cancel-new-billing").hide();
		$("#new-billing").show();
		$("#new-billing-form").hide(0, function() {
			$("#choose-billing").show(0, function() {
				$("#new-billing a").show();
			});
		});
		$("#new-billing-address").val(0);
		return false;
	});
	

	var shippingAddressInput = $('#delivery-address-box');
	
	if ($("#deliver-to-billing:checked").length > 0) {
		shippingAddressInput.hide();
	}

	$("#deliver-to-billing").click(function() {
		if ($(this).is(":checked")) {
			shippingAddressInput.slideUp(200);
		}
		else {
			shippingAddressInput.slideDown(200);
		}
	});

	
	$("#new-shipping a").click(function() {
		$("#new-shipping a").hide();
		$("#choose-shipping").hide(0, function() {
			$("#new-shipping-form").show(0, function() {
				$("#cancel-new-shipping").show();
			});
		});
		$("#choose-shipping .select-address input").attr("checked", false);
		$("#new-shipping-address").val(1);
		return false;
	});
	$("#cancel-new-shipping a").click(function() {
		$("#cancel-new-shipping").hide();
		$("#new-shipping").show();
		$("#new-shipping-form").hide(0, function() {
			$("#choose-shipping").show(0, function() {
				$("#new-shipping a").show();
			});
		});
		$("#new-shipping-address").val(0);
		return false;
	});
	
	/**
	 * Mini Basket Status Update
	 * 
	 */
	var basketMsg = $(".bubble");
	if (basketMsg.length == 1) {
		basketMsg.hide();

		$('.close', basketMsg).click(function(e){
			e.preventDefault();
			basketMsg.fadeOut(250);
		});

		setTimeout(function() {
			basketMsg.fadeIn(250);
		}, 500);

		setTimeout(function() {
			basketMsg.fadeOut(250);
		}, 10500);
	}
	
	// $(".mini-basket-popup .continue").click(function() {
	// 	$(".mini-basket-popup").fadeOut();
	// 	return false;
	// });

	$('#mini-basket p').click(function(){
		if($(this).parent().hasClass('active')){
			$("#mini-basket-popup").slideUp(250, function(){
				$(this).parent().removeClass('active');
			});
		} else {
			$(this).parent().addClass('active');
			$("#mini-basket-popup").slideDown();
		}
	});
	
	
	/**
	 * Basket
	 * 
	 */

	hookUpdateBasket();

	
	
	
	$("#decrease-qty").click(function() {
		if ($(".qty-input").val() > 1) {
			$(".qty-input").val(parseInt($(".qty-input").val()) - 1);
		}
	});
	$("#increase-qty").click(function() {
		$(".qty-input").val(parseInt($(".qty-input").val()) + 1);
	});





	/**
	 * Product View
	 * 
	 */
	$(".decrease-qty").live("click", function() {
		if ($(".qty-input").val() > 1) {
			$(".qty-input").val(parseInt($(".qty-input").val()) - 1);
		}
	})
	$(".increase-qty").live("click", function() {
		$(".qty-input").val(parseInt($(".qty-input").val()) + 1);
	})

	$(".custom-options select").change(function() {

		$("#price-and-stock").load(
			"/ajax/catalog/get_price_and_stock/" + productID,
			$("#product-form").serialize()
		);
		
		if (typeof valuesStockIDs === "undefined") {
			return;
		}

		var values = "";		
		$(".custom-options select").each(function() {
			values += $(this).val() + "-";
		});
		values = values.substring(0, values.length - 1);

		var stockID = valuesStockIDs[values];

		if(stockID == undefined){
			return false;
		}
		
		$("#image-box").load('/ajax/catalog/get_var_images_box/' + stockID, function(){
			$('.cloud-zoom, .cloud-zoom-gallery').CloudZoom();
		});

	});


	$(".custom-options img").ajaxStart(function() {
		$(this).css("visibility", "visible");
	})
	$(".custom-options img").ajaxSuccess(function() {
		$(this).css("visibility", "hidden");
	})
	
	$("#add-to-basket-submit").click(function() {
		$(this).parents("form").attr("action", "/basket/add");
	});

	

	$(".attr-filter dt a").click(function() {
		
		var attrID = $(this).attr("id").substring(5);
		var valclass = ".attr-" + attrID + "-value";		

		if ($(valclass).css("display") == "block")
		{
			$(this).css("backgroundImage", "url(/img/icons/bullet_toggle_plus.png)");
			$(valclass).hide();
		}
		else
		{
			$(this).css("backgroundImage", "url(/img/icons/bullet_toggle_minus.png)");
			$(valclass).show();
		}

		return false;

	});

	/*$("#manu-filter dt a").click(function() {
		
		var dd = $(this).parent().parent().find("dd");
		
		if (dd.css("display") == "block")
		{
			$(this).css("backgroundImage", "url(/img/icons/bullet_toggle_plus.png)");
			dd.hide();
		}
		else
		{
			$(this).css("backgroundImage", "url(/img/icons/bullet_toggle_minus.png)");
			dd.show();
		}

		return false;
		
	});
	
	$("dl.closed dd").hide();*/

});


function updateBasket(){
	$('body').addClass('working');
	$.post($('#basket-form').attr('action'), $("#basket-form").serialize(), function(data, textStatus, jqXHR){
		$('#content').html(data);
		hookUpdateBasket();
		$('body').removeClass('working');
	});
	return false;
}

function updateBasketWithLink(anchor){
	$('body').addClass('working');
	$('#content').load($(anchor).attr('href'), function(){
		hookUpdateBasket();
		$('body').removeClass('working');
	});
}

function hookUpdateBasket(){
	//$('.send-button').click(function(e) {
	//	e.preventDefault();
	//	updateBasket();
	//});

	$('#basket-form').submit(function(e){
		e.preventDefault();
		updateBasket();
	});

	$("#shipping-location-select").change(function() {
		$("#basket-form").submit();
	});

	$("#del-services input").click(function() {
		$("#basket-form").submit();
	});

	$(".basket-qty a").click(function() {
		$("#basket-form").submit();
		return false;
	});

	$(".slidingDiv").hide();
	$(".show_hide").show();

	$('.show_hide').click(function(){
		$(".slidingDiv").slideToggle();
		return false;
	});

	$(".custom-text-input").hide();
	$('.show-hide-custom-text').click(function() {
         $(this).next().slideToggle();
    });

	$('.basket-remove a, .giftwrap-details a').click(function(e){
		e.preventDefault();
		updateBasketWithLink(this);
	});
}