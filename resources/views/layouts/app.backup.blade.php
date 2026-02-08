<!doctype html>
<html lang="en">

<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width,initial-scale=1'>
    <title>@yield('title','Bitbucket Sync')</title>
    <!-- Bootstrap 5 (AdminLTE v4 peer) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- AdminLTE v4 CSS (RC) -->
    <link href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-rc3/dist/css/adminlte.min.css" rel="stylesheet">
    <!-- Font Awesome (icons used by AdminLTE) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- Select2 (Bootstrap 5 theme) -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
</head>

<body class="layout-fixed sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                    </li>
                    <li class="nav-item d-none d-sm-inline-block">
                        <a href="/dashboard" class="nav-link">Bitbucket Sync</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item d-flex align-items-center">
                        <span class="me-3">{{ auth()->user()->email ?? 'Guest' }}</span>
                        <form method="post" action="/logout">@csrf<button class="btn btn-sm btn-outline-danger">Logout</button></form>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="/dashboard" class="brand-link text-decoration-none">
                <span class="brand-text fw-bold ms-2">Bitbucket Sync</span>
            </a>
            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                        <li class="nav-item"><a href="/dashboard" class="nav-link"><i class="nav-icon fas fa-gauge"></i><p class="ms-2">Dashboard</p></a></li>
                        <li class="nav-item"><a href="/merge" class="nav-link"><i class="nav-icon fas fa-code-merge"></i><p class="ms-2">Merge</p></a></li>
                        <li class="nav-item"><a href="/report" class="nav-link"><i class="nav-icon fas fa-list"></i><p class="ms-2">Logs</p></a></li>
                        @can('manage-users')
                        <li class="nav-item"><a href="/users" class="nav-link"><i class="nav-icon fas fa-users"></i><p class="ms-2">Manage Users</p></a></li>
                        @endcan
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">@yield('title','Bitbucket Sync')</h1>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </section>
        </div>

        <footer class="main-footer small">
            <div class="float-end d-none d-sm-inline">v1.0</div>
            <strong>&copy; {{ date('Y') }} Bitbucket Sync.</strong>
        </footer>
    </div>

    <!-- Required JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE v4 JS (RC) -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-rc3/dist/js/adminlte.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function() {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Select a branch...',
                allowClear: true
            });
        });
    </script>
</body>

</html>

