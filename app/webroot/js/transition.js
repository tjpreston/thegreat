(function($) {

   $.fn.transictionto = function(options) {
      var settings = $.extend({
   }, options || {});
   //wrap into div if no div is present.
   $(this).each(function() {
      if ($(this).parent('div').size() == 0) {
         $(this).wrap('<div></div>')
      }
      //now swap with background trick
      $(this)
      .parent()
         .css('background-image', 'url(' + settings.destinationImage + ')')
         .css('background-repeat', 'no-repeat')
      .end()
      .fadeOut(1000, function() {
         this.src = settings.destinationImage;
         $(this).show();
      });
   });
};
})(jQuery);