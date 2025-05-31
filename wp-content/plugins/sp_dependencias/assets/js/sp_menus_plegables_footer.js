/* MENUS FOOTER */

jQuery(document).ready(function($) {
    $(".menu_trigger_movil").on("click", function(){
        if ($(window).width() < 768) {
            var $currentNav = $(this).parent().find(".menu_plegable_movil");
            $(".menu_plegable_movil").not($currentNav).slideUp(300);
            $(".menu_trigger_movil").not(this).removeClass("trigger_activo");
            $currentNav.slideToggle(300);
            $(this).toggleClass("trigger_activo");
        }
    });

    $(".menu_plegable_movil").css("display", "none");
});