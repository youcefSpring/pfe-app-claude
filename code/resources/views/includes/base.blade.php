<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>

    <!-- Google Font: Source Sans Pro -->
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">



    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte.min2167.css?v=3.2.0') }}">

    <link rel="stylesheet" href="{{ asset('assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">

    @if (app()->getLocale() === 'ar')
        <link rel="stylesheet" href="{{ asset('assets/dist/css/custom.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte-rtl.css') }}">
@endif


@yield('css')

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="{{ asset('assets/dist/img/AdminLTELogo.png') }}" alt="AdminLTELogo" height="60" width="60">

    </div>

    <!-- Navbar -->
    @include('admin.includes.navbar')
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    @include('admin.includes.sidebar')

    <!-- Content Wrapper. Contains page content -->
    @include('admin.includes.content')
    <!-- /.content-wrapper -->


    @include('admin.includes.footer')

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('assets/plugins/jquery-ui/jquery-ui.min.js') }}"></script>

<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>

<!-- Bootstrap 4 -->
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<!-- ChartJS -->
<script src="{{ asset('assets/plugins/chart.js/Chart.min.js') }}"></script>

<!-- Sparkline -->
<script src="{{ asset('assets/plugins/sparklines/sparkline.js') }}"></script>

<!-- JQVMap -->
<script src="{{ asset('assets/plugins/jqvmap/jquery.vmap.min.js') }}"></script>
<script src="{{ asset('assets/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>

<!-- jQuery Knob Chart -->
<script src="{{ asset('assets/plugins/jquery-knob/jquery.knob.min.js') }}"></script>

<!-- daterangepicker -->
<script src="{{ asset('assets/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>

<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ asset('assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>

<!-- Summernote -->
<script src="{{ asset('assets/plugins/summernote/summernote-bs4.min.js') }}"></script>

<!-- overlayScrollbars -->
<script src="{{ asset('assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>

<!-- AdminLTE App -->
<script src="{{ asset('assets/dist/js/adminlte2167.js?v=3.2.0') }}"></script>


<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{ asset('assets/dist/js/pages/dashboard.js') }}"></script>

@yield('script')

</body>

</html>
