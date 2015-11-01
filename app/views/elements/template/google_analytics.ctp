<?php

if(Configure::read('GoogleAnalytics.enabled')): ?>
<!-- Begin Google Analytics -->
<script>

    
    
    (function (i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r;
        i[r] = i[r] || function () {
            (i[r].q = i[r].q || []).push(arguments)
        }, i[r].l = 1 * new Date();
        a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
        a.async = 1;
        a.src = g;
        m.parentNode.insertBefore(a, m)
    })
    (window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

    ga('create', '<?php echo Configure::read('GoogleAnalytics.account'); ?>', 'auto');
//    ga('require', 'linkid');
    ga('send', 'pageview');

</script>
<!-- End Google Analytics -->
<?php endif; ?>


<!--<script type="text/javascript">

    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', '<?php echo Configure::read('GoogleAnalytics.account'); ?>']);
    _gaq.push(['_setDomainName', 'thegreatbritishshop.co.uk']);
    _gaq.push(['_trackPageview']);

        <?php
          if(!empty($analyticsCode)) {
            echo $analyticsCode;
          }
        ?>

    (function () {
        var ga = document.createElement('script');
        ga.type = 'text/javascript';
        ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(ga, s);
    })();

</script>-->