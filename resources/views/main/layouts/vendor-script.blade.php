<!-- jQuery (load once) -->
<script src="{{ asset('sipo_krisan/public/news/plugins/jquery/jquery.min.js') }}"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('sipo_krisan/public/news/plugins/select2/dist/js/select2.full.min.js') }}" type="text/javascript"></script>

<!-- Toastr for notifications -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<!-- CSRF Token Setup for AJAX -->
<script>
// Setup CSRF token for all AJAX requests
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Ensure jQuery is available globally
window.$ = window.jQuery = jQuery;

// Prevent Bootstrap 5 conflicts with Bootstrap 4
if (typeof bootstrap !== 'undefined') {
    // If Bootstrap 5 is loaded, store it separately
    window.bootstrap5 = bootstrap;
    // Remove global bootstrap to prevent conflicts
    delete window.bootstrap;
}

// Toastr configuration
toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};
</script>

@yield('scripts')
