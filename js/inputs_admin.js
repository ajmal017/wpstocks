jQuery(document).ready(function($) {
    $.mobilepop_cpt = {

        init : function() {

            if (jQuery().miniColors) {
              $('.colors').miniColors();
            }

            if (jQuery().checkbox) {
              $('.checkbox-toggle').checkbox({ theme: 'switch'});
            }

            if (jQuery().numeric) {
              $('.num-slider').numeric();
            }

            if (jQuery().radio) {
              $('.radio').radio();
            }

            $(".button_theme_radio a").click(function(e) {
                e.preventDefault();

                $(".button_theme_radio a").removeClass("selected");

                $(this).addClass("selected");
            });
            
            $(".navigation_theme_radio a").click(function(e) {
                e.preventDefault();

                $(".navigation_theme_radio a").removeClass("selected");

                $(this).addClass("selected");
            });


            return false;
        }
    }; 

    $.mobilepop_cpt.init();
});