<?php if(Configure::read('GoogleAnalytics.enabled')): ?>
<!-- Begin Google Analytics -->
<script type="text/javascript">

	var _gaq = _gaq || [];
    _gaq.push(['_setAccount', '<?php echo Configure::read('GoogleAnalytics.account'); ?>']);
    _gaq.push(['_setDomainName', 'thegreatbritishshop.co.uk']);
    _gaq.push(['_trackPageview']);
  
	<?php
	  if(!empty($analyticsCode)) {
	    echo $analyticsCode;
	  }
	?>

    (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
 	})();

</script>
<!-- End Google Analytics -->
<?php endif; ?>