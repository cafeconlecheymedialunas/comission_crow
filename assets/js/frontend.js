jQuery(document).ready(function($) {
    var menu = $('#desktop-header');
    var sticky = menu.offset().top;

    // Función para manejar el sticky menu
    function stickyMenu() {
        if ($(window).scrollTop() > sticky) {
            menu.addClass('sticky');
        } else {
            menu.removeClass('sticky');
        }
    }

    // Llamar a stickyMenu cuando la página se carga
    stickyMenu();

    // Llamar a stickyMenu cuando se hace scroll
    $(window).on('scroll', function() {
        stickyMenu();
    });

    // Llamar a stickyMenu cuando se redimensiona la ventana
    $(window).on('resize', function() {
        stickyMenu();
    });

    // Llamar a stickyMenu cuando la ventana se carga por completo
    $(window).on('load', function() {
        stickyMenu();
    });
});
