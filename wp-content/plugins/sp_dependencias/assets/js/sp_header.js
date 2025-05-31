jQuery(document).ready(function ($) {

    function check_header() {
        const scrollPosition = $(window).scrollTop();
        const viewportWidth = $(window).width();
      
        if (viewportWidth <= 767) {
          // Siempre sticky en pantallas pequeñas
            if(!$("header").hasClass('header_degradado')) $("header").addClass('header_degradado');
        } else {
            // Sticky solo al hacer scroll en pantallas grandes
            if (scrollPosition > 48) {
                $("header").addClass('header_degradado');
            } else {
                $("header").removeClass('header_degradado');
            }
        }
    }
          
    $(window).on('scroll resize', check_header);
    check_header();

});