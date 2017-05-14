//
// jQuery events go here
//

// jQuery bootstrap initiations
$(document).on('mouseover', '[data-toggle=popover]', function() {
    if (!$(this).data('bs.popover')) {
        $(this).popover({
            placement: 'top',
            delay: {show: 700, hide: 100},
            trigger: 'hover' ,
            //container: 'body',
        });
        $(this).trigger('mouseover');
    }
});

$(document).on('focus', '.password-mask', function() {
    $(this).attr('type', 'text');
});

$(document).on('blur', '.password-mask', function() {
    $(this).attr('type', 'password');
});