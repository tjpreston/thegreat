//##############################
// jQuery Custom Radio-buttons and Checkbox; basically it's styling/theming for Checkbox and Radiobutton elements in forms
// By Dharmavirsinh Jhala - dharmavir@gmail.com
// Date of Release: 13th March 10
// Version: 0.8
/*
 USAGE:
	$(document).ready(function(){
		$(":radio").behaveLikeCheckbox();
	}
*/

jQuery.fn.extend({
dgStyle: function()
{
	$.each($(this), function() {
	
		var elm	=	$(this).children().get(0);

		elmType = $(elm).attr("type");
		$(this).data('type',elmType);
		$(this).data('checked',$(elm).attr("checked"));
		$(this).dgClear();
	});
	
	$(this).mouseup(function() { $(this).dgHandle(); });	
},
dgClear: function()
{
	if($(this).data("checked") == true)
	{
		$(this).addClass("gwp-selected");
	}
	else
	{
		$(this).removeClass("gwp-selected");
	}	
},
dgHandle: function()
{
	var elm	=	$(this).children().get(0);
	
	if ($(this).data("checked") != true)
	{
		$(elm).dgCheck(this);
	}
	
	$.each($("input[name='"+$(elm).attr("name")+"']"),function()
	{
		if (elm!=this)
		{
			$(this).dgUncheck(-1);
		}
	});

},
dgCheck: function(div)
{
	$(this).attr("checked",true);
	$(div).data('checked',true).addClass("gwp-selected");
},
dgUncheck: function(div)
{
	$(this).attr("checked",false);
	if(div != -1)
	{
		$(div).data('checked',false).removeClass("gwp-selected");
	}
	else
	{
		$(this).parent().data("checked",false).removeClass("gwp-selected");
	}
}
});