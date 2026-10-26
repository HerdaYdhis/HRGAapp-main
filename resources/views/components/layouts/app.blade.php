<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <x-layouts.title title="{{ $title }}" />
    {{--    <title>{{ $title }} - {{config('app.name')}}</title> --}}

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="{{ url('assets/css/main/app.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/main/app-dark.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/shared/iconly.css') }}">
    <link rel="shortcut icon" href="{{ url('assets/images/logo/favicon.svg') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ url('assets/images/logo/favicon.png') }}" type="image/png">

    @livewireStyles

    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Prevent CSS transition conflict with JS slideToggle animations */
        .sidebar-wrapper .menu .submenu {
            transition: none !important;
        }
    </style>
</head>

<body>
    <div id="app">
        <div id="sidebar" class="active">
            <x-layouts.sidebar />
        </div>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>
            {{ $slot }}
            <footer>
                <div class="footer clearfix text-muted mb-0">
                    <div class="float-start">
                        <p>{{ date('Y') }} &copy; {{ config('app.name') }}</p>
                    </div>
                    <div class="float-end">
                        {{-- <p>Crafted with <span class="text-danger"><i class="bi bi-heart"></i></span> by <a
                                href="{{ route('dashboard') }}">IT Dept</a></p> --}}
                        <p><span class="text-danger"><i class="bi bi-question-circle-fill"></i></span> Need Help,
                            Contact me<a href="mailto:itsupport@saf-indo.com"> IT Support</a></p>

                    </div>
                    <p class="alert alert-warning" wire:offline>
                        Whoops, your device has lost connection. The web page you are viewing is offline.
                    </p>
                </div>
            </footer>
        </div>
    </div>



    <script src="{{ url('assets/js/bootstrap.js') }}"></script>
    <script src="{{ url('assets/js/app.js') }}"></script>
    <script src="{{ url('assets/js/pages/sweetalert2.js') }}"></script>

    <script type="text/javascript" src="//unpkg.com/alpinejs" defer></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="{{ url('assets/js/simple.money.format.js') }}"></script>
    <script src="https://cdn.tiny.cloud/1/s76ennbpmpr15q3rzfdmhu1efu6ds8k00zukbfck1203ua49/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>
    <script type="text/javascript" src="{{ url('assets/extensions/apexcharts/apexcharts.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('assets/js/pages/dashboard.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


    @livewireScripts
    @stack('script')
    <script>
        window.addEventListener('modal-hide', event => {
            $('#' + event.detail.modal).modal('hide');
        })
        window.addEventListener('modal-show', event => {
            $('#' + event.detail.modal).modal('show');

            // tinymce.init({
            //     selector: '#description',
            //     plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            //     toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
            // });

        })
        @if (session()->has('success'))
            Swal.fire({
                icon: 'success',
                title: 'BERHASIL!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 3000
            })
        @elseif (session()->has('error'))
            Swal.fire({
                icon: 'error',
                title: 'GAGAL!',
                text: '{{ session('error') }}',
                showConfirmButton: false,
                timer: 3000
            })
        @endif

        // Fix Sidebar Submenu Toggle & Accordion
        $(document).ready(function() {
            // Strip broken event listeners from Mazer's vendor app.js
            document.querySelectorAll('.sidebar-item.has-sub > .sidebar-link').forEach(function(link) {
                var clone = link.cloneNode(true);
                link.parentNode.replaceChild(clone, link);
            });

            // Rebind clean slide toggle and accordion behavior
            $(document).on('click', '.sidebar-item.has-sub > .sidebar-link', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var $parent = $(this).closest('.sidebar-item.has-sub');
                var $submenu = $parent.find('> .submenu');
                var isAlreadyOpen = $submenu.is(':visible') && ($submenu.hasClass('active') || $parent.hasClass('active'));

                if (isAlreadyOpen) {
                    // Close this submenu
                    $parent.removeClass('active');
                    $submenu.removeClass('active').slideUp(200, function() {
                        $(this).css('display', 'none');
                    });
                } else {
                    // Accordion: close other open submenus
                    $parent.siblings('.sidebar-item.has-sub').each(function() {
                        var $otherParent = $(this);
                        var $otherSubmenu = $otherParent.find('> .submenu');
                        $otherParent.removeClass('active');
                        $otherSubmenu.removeClass('active').slideUp(150, function() {
                            $(this).css('display', 'none');
                        });
                    });

                    // Open this submenu
                    $parent.addClass('active');
                    $submenu.addClass('active').slideDown(200, function() {
                        $(this).css('display', 'block');
                    });
                }
            });

            // Close sidebar on mobile screens (< 1200px) when clicking any menu or submenu link
            $(document).on('click', '.sidebar-menu .submenu-link, .sidebar-menu .sidebar-item:not(.has-sub) > .sidebar-link', function() {
                if (window.innerWidth < 1200) {
                    $('#sidebar').removeClass('active');
                }
            });
        });
    </script>
</body>

</html>
