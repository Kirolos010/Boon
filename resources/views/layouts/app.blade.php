<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title') - نظام إدارة المبيعات</title>

    <!-- Bootstrap 5.3 RTL -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts - Arabic -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --coffee-dark: #2C1810;
            --coffee-medium: #6F4E37;
            --coffee-light: #8B7355;
            --cream-light: #F5E6D3;
            --cream-medium: #E8D7C3;
            --cream-dark: #D4A574;
            --accent-gold: #D4AF37;
            --text-light: #F5E6D3;
            --text-dark: #2C1810;
        }

        * {
            font-family: 'Cairo', sans-serif;
        }

        body {
            background-color: var(--cream-light);
            color: var(--text-dark);
        }

        /* Main Layout */
        .main-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: linear-gradient(to bottom, var(--coffee-dark), var(--coffee-medium));
            color: white;
            position: fixed;
            height: 100vh;
            right: 0;
            top: 0;
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.3);
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--cream-dark);
            border-radius: 5px;
        }

        .sidebar-header {
            padding: 25px 15px;
            border-bottom: 2px solid var(--accent-gold);
            text-align: center;
        }

        .sidebar-logo {
            font-size: 28px;
            font-weight: 700;
            color: var(--accent-gold);
            margin-bottom: 5px;
        }

        .sidebar-tagline {
            font-size: 12px;
            color: var(--cream-light);
            opacity: 0.8;
        }

        .sidebar-menu {
            padding: 20px 0;
            list-style: none;
            margin: 0;
        }

        .sidebar-menu li {
            margin: 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: var(--cream-light);
            text-decoration: none;
            transition: all 0.3s ease;
            border-right: 3px solid transparent;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: rgba(212, 175, 55, 0.2);
            color: var(--accent-gold);
            border-right-color: var(--accent-gold);
        }

        .sidebar-menu i {
            width: 20px;
            margin-left: 12px;
            text-align: center;
        }

        .sidebar-menu .submenu {
            list-style: none;
            padding-right: 40px;
            display: none;
            transition: all 0.3s ease;
            opacity: 0;
            max-height: 0;
            overflow: hidden;
        }

        .sidebar-menu .submenu.show {
            display: block;
            opacity: 1;
            max-height: 500px;
        }

        .sidebar-menu .menu-toggle {
            cursor: pointer;
            user-select: none;
        }

        .sidebar-menu .menu-toggle:hover {
            background-color: rgba(212, 175, 55, 0.1);
        }

        .sidebar-menu .submenu a {
            padding: 8px 20px;
            font-size: 13px;
            opacity: 0.9;
        }

        /* Content Area */
        .content-area {
            margin-right: 260px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* Navbar */
        .navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--coffee-dark);
            font-weight: 600;
        }

        .navbar-brand i {
            font-size: 24px;
            color: var(--accent-gold);
        }

        .navbar-end {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 15px;
            background-color: var(--cream-medium);
            border-radius: 20px;
            cursor: pointer;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: var(--coffee-medium);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        /* Main Container */
        .main-content {
            flex: 1;
            padding: 0 30px 30px;
        }

        /* Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--cream-medium);
        }

        .page-title {
            font-size: 32px;
            font-weight: 700;
            color: var(--coffee-dark);
            margin: 0;
        }

        .page-title-subtitle {
            font-size: 14px;
            color: var(--coffee-light);
            opacity: 0.8;
            margin: 0;
        }

        .page-actions {
            display: flex;
            gap: 10px;
        }

        /* Breadcrumb */
        .breadcrumb {
            background-color: transparent;
            padding: 10px 0;
            margin-bottom: 20px;
        }

        .breadcrumb-item {
            color: var(--coffee-light);
        }

        .breadcrumb-item.active {
            color: var(--coffee-dark);
            font-weight: 600;
        }

        .breadcrumb-item a {
            color: var(--coffee-medium);
            text-decoration: none;
        }

        .breadcrumb-item a:hover {
            color: var(--accent-gold);
        }

        /* Cards */
        .card {
            background: white;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .card-header {
            background: linear-gradient(to right, var(--coffee-dark), var(--coffee-medium));
            color: white;
            border-radius: 12px 12px 0 0;
            padding: 20px;
            font-weight: 600;
            border: none;
        }

        .card-body {
            padding: 25px;
        }

        .card-footer {
            background-color: var(--cream-light);
            border-top: 1px solid var(--cream-medium);
            border-radius: 0 0 12px 12px;
            padding: 15px 25px;
        }

        /* Stat Cards */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            border-right: 4px solid var(--coffee-medium);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .stat-icon {
            font-size: 40px;
            color: var(--accent-gold);
            margin-bottom: 15px;
        }

        .stat-label {
            font-size: 13px;
            color: var(--coffee-light);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--coffee-dark);
        }

        .stat-change {
            font-size: 12px;
            margin-top: 10px;
        }

        .stat-change.positive {
            color: #28a745;
        }

        .stat-change.negative {
            color: #dc3545;
        }

        /* Tables */
        .table {
            color: var(--coffee-dark);
        }

        .table thead th {
            background-color: var(--coffee-dark);
            color: white;
            font-weight: 600;
            border: none;
            padding: 15px;
            text-align: right;
        }

        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid var(--cream-medium);
        }

        .table tbody tr:hover {
            background-color: var(--cream-light);
        }

        /* Forms */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--coffee-dark);
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid var(--cream-medium);
            padding: 10px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--accent-gold);
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
        }

        .form-select {
            border-radius: 8px;
            border: 1px solid var(--cream-medium);
        }

        .form-select:focus {
            border-color: var(--accent-gold);
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
        }

        /* Buttons */
        .btn {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-primary {
            background-color: var(--coffee-dark);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--coffee-medium);
            color: white;
        }

        .btn-success {
            background-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .btn-warning {
            background-color: #ffc107;
            color: var(--text-dark);
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-outline-secondary {
            border: 1px solid var(--cream-medium);
            color: var(--text-dark);
        }

        .btn-outline-secondary:hover {
            background-color: var(--cream-medium);
            border-color: var(--cream-medium);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        /* Form Actions */
        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--cream-medium);
        }

        .form-actions button,
        .form-actions a {
            flex: 0 1 auto;
            min-width: 120px;
        }

        .btn-secondary {
            background-color: var(--cream-medium);
            color: var(--text-dark);
            border: 1px solid var(--cream-medium);
        }

        .btn-secondary:hover {
            background-color: var(--cream-dark);
            color: var(--text-dark);
            border-color: var(--cream-dark);
        }

        /* Badge */
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
        }

        .badge-warning {
            background-color: #ffc107;
            color: var(--text-dark);
        }

        .badge-danger {
            background-color: #dc3545;
            color: white;
        }

        .badge-info {
            background-color: #17a2b8;
            color: white;
        }

        /* Alert */
        .alert {
            border: none;
            border-radius: 8px;
            padding: 15px 20px;
        }

        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .alert-warning {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
        }

        .alert-info {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }

        /* Modal */
        .modal-content {
            border-radius: 12px;
            border: none;
        }

        .modal-header {
            background: linear-gradient(to right, var(--coffee-dark), var(--coffee-medium));
            color: white;
            border: none;
        }

        .modal-title {
            font-weight: 600;
        }

        .btn-close {
            filter: brightness(0) invert(1);
        }

        /* Pagination */
        .pagination {
            margin-top: 20px;
        }

        .page-link {
            color: var(--coffee-medium);
            border: 1px solid var(--cream-medium);
            border-radius: 5px;
            margin: 0 2px;
        }

        .page-link:hover {
            background-color: var(--cream-medium);
            border-color: var(--coffee-medium);
            color: var(--coffee-dark);
        }

        .page-link.active {
            background-color: var(--coffee-dark);
            border-color: var(--coffee-dark);
        }

        /* Search & Filter */
        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-box input {
            flex: 1;
            max-width: 300px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                transition: width 0.3s ease;
            }

            .sidebar.show {
                width: 260px;
            }

            .content-area {
                margin-right: 0;
            }

            .main-content {
                padding: 0 15px 15px;
            }

            .page-title {
                font-size: 24px;
            }

            .page-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .page-actions {
                width: 100%;
                flex-wrap: wrap;
            }

            .stat-card {
                margin-bottom: 15px;
            }

            .navbar {
                padding: 15px;
            }
        }
    </style>

    @yield('styles')
</head>

<body>
    <div class="main-wrapper">
        <!-- Sidebar -->
        @include('components.sidebar')

        <!-- Content Area -->
        <div class="content-area">
            <!-- Navbar -->
            @include('components.navbar')

            <!-- Main Content -->
            <div class="main-content">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Sidebar toggle - فقط للـ menu-toggle items
        document.querySelectorAll('.sidebar-menu .menu-toggle').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const submenu = this.nextElementSibling;
                if (submenu && submenu.classList.contains('submenu')) {
                    // إغلاق جميع الـ submenus الأخرى
                    document.querySelectorAll('.sidebar-menu > li > .submenu').forEach(menu => {
                        if (menu !== submenu) {
                            menu.classList.remove('show');
                        }
                    });
                    submenu.classList.toggle('show');
                }
            });
        });

        // Alert auto-hide
        document.querySelectorAll('.alert').forEach(alert => {
            setTimeout(() => {
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        });
    </script>

    @yield('scripts')
</body>

</html>
