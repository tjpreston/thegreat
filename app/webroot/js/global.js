$(document).ready(function() {
		
	// External links in new window
	$('a[rel=external]').click(function(){
		url = $(this).attr('href');
		window.open(url);
		return false;
	});

	$('#sitenav > ul').superfish({
		autoArrows: false
	});

	// Form helper doesn't know about "email" input type yet, so this is a simple workaround.
	$('#CustomerEmail').prop('type', 'email');

	$('img.retina').retina('@2x');

});