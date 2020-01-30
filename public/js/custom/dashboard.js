$(document).ready(function() {
    $('.btn-notification-type').click(function(event) {
        $('.li-notification-info').hide();
        $('.notification-' + $(this).data('type')).show();
    });

    $('#btn-notification-type-reset').click(function(event) {
        $('.li-notification-info').show();
    });

});