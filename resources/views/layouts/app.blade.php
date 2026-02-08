<!doctype html>
<html lang="en">

<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width,initial-scale=1'>
    <title>@yield('title','Bitbucket Sync')</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-rc3/dist/css/adminlte.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    @stack('styles')
</head>

<body class="sidebar-expand-lg sidebar-open bg-body-tertiary" data-bs-theme="dark">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <!--begin::Header-->
        <!-- <nav class="app-header navbar navbar-expand bg-body"> -->
        <nav class="app-header navbar navbar-expand bg-body" data-bs-theme="dark">
            <!--begin::Container-->
            <div class="container-fluid">
                <!--begin::Start Navbar Links-->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="bi bi-list"></i>
                        </a>
                    </li>
                    <!--<li class="nav-item d-none d-md-block">
                        <a href="#" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item d-none d-md-block">
                        <a href="#" class="nav-link">Contact</a>
                    </li>-->
                </ul>
                <!--end::Start Navbar Links-->

                <!--begin::End Navbar Links-->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item d-flex align-items-center">
                        <span class="me-3">{{ auth()->user()->email ?? 'Guest' }}</span>
                        <form method="post" action="/logout">@csrf<button class="btn btn-sm btn-outline-light">Logout</button></form>
                    </li>
                </ul>
                <!--end::End Navbar Links-->
            </div>
            <!--end::Container-->
        </nav>
        <!--end::Header-->
        <!--begin::Sidebar-->
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <!--begin::Sidebar Brand-->
            <div class="sidebar-brand">
                <!--begin::Brand Link-->
                <a href="../index.html" class="brand-link">
                    <!--begin::Brand Image-->
                    <!--<img
                        src="../assets/img/AdminLTELogo.png"
                        alt="AdminLTE Logo"
                        class="brand-image opacity-75 shadow" />-->
                    <!--end::Brand Image-->
                    <!--begin::Brand Text-->
                    <span class="brand-text fw-light">CodeMerger</span>
                    <!--end::Brand Text-->
                </a>
                <!--end::Brand Link-->
            </div>
            <!--end::Sidebar Brand-->
            <!--begin::Sidebar Wrapper-->
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <!--begin::Sidebar Menu-->
                    <ul
                        class="nav sidebar-menu flex-column"
                        data-lte-toggle="treeview"
                        role="navigation"
                        aria-label="Main navigation"
                        data-accordion="false"
                        id="navigation">
                        <li class="nav-item"><a href="/dashboard" class="nav-link"><i class="nav-icon fas fa-gauge"></i>
                                <p class="ms-2">Dashboard</p>
                            </a></li>
                        <li class="nav-item"><a href="/merge" class="nav-link"><i class="nav-icon fas fa-code-merge"></i>
                                <p class="ms-2">Merge</p>
                            </a></li>
                        <li class="nav-item"><a href="/report" class="nav-link"><i class="nav-icon fas fa-list"></i>
                                <p class="ms-2">Logs</p>
                            </a></li>
                        @can('manage-users')
                        <li class="nav-item"><a href="/users" class="nav-link"><i class="nav-icon fas fa-users"></i>
                                <p class="ms-2">Manage Users</p>
                            </a></li>
                        @endcan
                    </ul>
                    <!--end::Sidebar Menu-->
                </nav>
            </div>
            <!--end::Sidebar Wrapper-->
        </aside>
        <!--end::Sidebar-->
        <!--begin::App Main-->
        <main class="app-main">
            <!--begin::App Content Header-->
            <div class="app-content-header">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!--begin::Row-->
                    <div class="row">
                        @yield('content-title')
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::Container-->
            </div>
            <!--end::App Content Header-->
            <!--begin::App Content-->
            <div class="app-content">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!--begin::Row-->
                    <div class="row">
                        @yield('content')
                    </div>
                    <!--end::Row-->
                </div>
            </div>
            <!--end::App Content-->
        </main>
        <!--end::App Main-->
        <!--begin::Footer-->
        <footer class="app-footer">
            <!--begin::To the end-->
            <div class="float-end d-none d-sm-inline">Anything you want</div>
            <!--end::To the end-->
            <!--begin::Copyright-->
            <strong>
                Copyright &copy; {{ date('Y') }};
                <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.
            </strong>
            All rights reserved.
            <!--end::Copyright-->
        </footer>
        <!--end::Footer-->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-rc3/dist/js/adminlte.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>
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
    <div class="modal fade" id="progressModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Processing...</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <pre id="progressOutput" class="mb-0" style="max-height:50vh;overflow:auto"></pre>
                </div>
                <div class="modal-footer">
                    <a id="progressReportLink" href="#" class="btn btn-primary d-none">View Report</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        new DataTable('#datatables', {
            order: [[7, 'desc']]
        });

        document.addEventListener('click', function(e) {
            const btn = e.target.closest('form.ajax-merge button[name="direction"]');
            if (!btn) return;
            e.preventDefault();
            const form = btn.form;
            const formData = new FormData(form);
            formData.set('direction', btn.value);
            const modalEl = document.getElementById('progressModal');
            const out = document.getElementById('progressOutput');
            const reportLink = document.getElementById('progressReportLink');
            out.textContent = 'Starting...';
            reportLink.classList.add('d-none');
            const bsModal = new bootstrap.Modal(modalEl);
            bsModal.show();
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': form.querySelector('input[name=_token]').value
                },
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                out.textContent = (data.output || '').trim() || ('Status: ' + data.status);
                const footer = modalEl.querySelector('.modal-footer');
                // remove any previous confirm button
                const oldBtn = footer.querySelector('.js-confirm-commit');
                if (oldBtn) oldBtn.remove();
                if (data.status === 'preview') {
                    const confirmBtn = document.createElement('button');
                    confirmBtn.type = 'button';
                    confirmBtn.className = 'btn btn-primary js-confirm-commit';
                    confirmBtn.textContent = 'Confirm & Commit';
                    confirmBtn.onclick = () => {
                        formData.set('confirm', '1');
                        out.textContent = 'Committing...';
                        confirmBtn.remove();
                        fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': form.querySelector('input[name=_token]').value
                            },
                            body: formData
                        })
                            .then(r => r.json())
                            .then(data2 => {
                                out.textContent = (data2.output || '').trim() || ('Status: ' + data2.status);
                                if (data2.report_url) {
                                    reportLink.href = data2.report_url;
                                    reportLink.classList.remove('d-none');
                                }
                                confirmBtn.remove();
                            })
                            .catch(err => {
                                out.textContent = 'Error: ' + err;
                                confirmBtn.remove();
                            });
                    };
                    footer.prepend(confirmBtn);
                } else {
                    if (data.report_url) {
                        reportLink.href = data.report_url;
                        reportLink.classList.remove('d-none');
                    }
                }
            })
            .catch(err => {
                out.textContent = 'Error: ' + err;
            });
        });

        // When modal is hidden, clean up confirm button and reset output
        document.getElementById('progressModal').addEventListener('hidden.bs.modal', function () {
            const footer = this.querySelector('.modal-footer');
            const confirmBtn = footer.querySelector('.js-confirm-commit');
            if (confirmBtn) confirmBtn.remove(); // remove confirm button
            const out = this.querySelector('#progressOutput');
            if (out) out.textContent = ''; // optional: clear text
        });

        document.getElementById('progressModal').addEventListener('hidden.bs.modal', function () {
            const footer = this.querySelector('.modal-footer');
            footer.querySelectorAll('.js-confirm-commit').forEach(btn => btn.remove());
            const out = this.querySelector('#progressOutput');
            if (out) out.textContent = '';
            const reportLink = this.querySelector('#progressReportLink');
            if (reportLink) reportLink.classList.add('d-none');
        });
    </script>
    @stack('scripts')
</body>

</html>