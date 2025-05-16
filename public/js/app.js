$(document).ready(function() {
    // Enable Bootstrap tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Enable Bootstrap popovers
    $('[data-bs-toggle="popover"]').popover();

    // Add loading spinner to buttons when clicked
    $('.btn').on('click', function() {
        var $btn = $(this);
        if (!$btn.hasClass('disabled')) {
            var loadingText = '<i class="fas fa-spinner fa-spin"></i> ' + $btn.text();
            $btn.data('original-text', $btn.html()).html(loadingText);
        }
    });

    // Add custom validation styles
    $('.form-control').on('input', function() {
        $(this).removeClass('is-invalid');
    });

    // Add direction-aware dropdown menus
    if ($('html').attr('dir') === 'rtl') {
        $('.dropdown-menu').addClass('dropdown-menu-end');
    }
});

// Global AJAX setup
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
