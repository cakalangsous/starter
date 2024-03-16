<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - Starter</title>

    <link rel="shortcut icon" href="{{ asset("assets/images/logo/favicon.svg") }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset("assets/images/logo/favicon.png") }}" type="image/png">
    <link rel="stylesheet" href="{{ asset("assets/css/shared/iconly.css") }}">
    <link rel="stylesheet" href="{{ asset("assets/css/pages/fontawesome.css") }}">
    <link rel="stylesheet" href="{{ asset("assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css") }}">
    <link rel="stylesheet" href="{{ asset("assets/css/main/table-datatable.css") }}">
    <link rel="stylesheet" href="{{ asset("assets/extensions/sweetalert2/sweetalert2.min.css") }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    @yield("style")
    <link rel="stylesheet" href="{{ asset("assets/css/main/app.css") }}">
    <link rel="stylesheet" href="{{ asset("assets/css/main/app-dark.css") }}">
    <link rel="stylesheet" href="{{ asset("assets/css/main/custom.css") }}">
    
    
</head>

<body>
    <script src="{{ asset('assets/js/initTheme.js') }}"></script>
    <div id="app">
        <div id="main" class="d-flex flex-column min-vh-100">
            @include("admin.partials.sidebar")
            @yield("content")
    
            <footer class="mt-auto">
                <div class="footer clearfix mb-0 text-muted">
                    <div class="float-start">
                        <p>2023 &copy; <a href="https://github.com/zuramai/mazer" target="_blank">Mazer</a></p>
                    </div>
                    <div class="float-end">
                        <p>Crafted with <span class="text-danger"><i class="bi bi-heart"></i></span> by <a href="https://saugi.me">Saugi</a></p>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script>
        let base_url = '{{ url("/") }}';
        let token = '{{ csrf_token() }}';
    </script>
    <script src="{{ asset("assets/extensions/jquery/jquery.min.js") }}"></script>
    <script src="{{ asset("assets/js/bootstrap.js") }}"></script>
    <script src="{{ asset("assets/extensions/sweetalert2/sweetalert2.min.js") }}"></script>
    <script src="{{ asset("assets/js/components/dark.js") }}"></script>
    <script src="{{ asset("assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js") }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="{{ asset("assets/js/app.js") }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        }, false);
        </script>
    @yield("script")

    <script>
        $(document).ready(function () {
            @if (session("success"))
                Toastify({
                    text: "{{ session('message') }}",
                    duration: 5000,
                    newWindow: true,
                    close: true,
                    gravity: "top",
                    position: "right",
                    stopOnFocus: true,
                    className: "bg-success",
                    style: {
                        background: "unset",
                    },
                }).showToast();
            @endif
        });
    </script>
    <script src="{{ asset("assets/js/custom.js") }}"></script>

</body>

</html>