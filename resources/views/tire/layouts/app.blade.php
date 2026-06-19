<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="Tire Lifecycle Management System">
    <meta name="author" content="IDEAL SOFT">
    <title>Tire Management System - IFFS</title>
    <link rel="shortcut icon" type="image/x-icon" href="#">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap-datetimepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    
    <style>
        /* Preloader Styles */
        #global-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .whirly-loader {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 5px solid #e5e5e5;
            border-top-color: #2ECC71;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Fix for toggle button alignment */
        .header-left.active {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        #toggle_btn {
            cursor: pointer;
            font-size: 20px;
            color: #2c3e50;
            transition: all 0.3s;
        }
        #toggle_btn:hover {
            color: #2ECC71;
        }
        .main-wrapper {
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        
        /* Sidebar toggle fix - working across all pages */
        .sidebar {
            transition: all 0.3s ease;
            width: 250px;
        }
        body.mini-sidebar .sidebar {
            width: 80px;
        }
        body.mini-sidebar .sidebar .sidebar-menu ul li a span {
            display: none;
        }
        body.mini-sidebar .sidebar .sidebar-menu ul li a i {
            margin-right: 0;
        }
        body.mini-sidebar .sidebar .sidebar-menu ul li.menu-title {
            display: none;
        }
        body.mini-sidebar .sidebar .sidebar-menu ul li {
            text-align: center;
        }
        .sidebar .sidebar-menu  ul  li.submenu ul li a {
        background: #fafbfe !important;
        }
.sidebar-menu ul li.submenu ul li.active a {
    background: rgba(46, 204, 113, 0.2);
    color: #ff9f43 !important;
}
        body.mini-sidebar .sidebar .sidebar-menu ul li a i {
            margin: 0;
        }
        body.mini-sidebar .page-wrapper {
            margin-left: 80px;
        }
        .page-wrapper {
            margin-left: 250px;
            transition: all 0.3s ease;
        }
        
        /* Mobile responsive */
        @media (max-width: 768px) {
            body.mini-sidebar .page-wrapper {
                margin-left: 0;
            }
            .page-wrapper {
                margin-left: 0;
            }
        }
        @media (min-width: 991.98px) {
            .mini-sidebar .header-left #toggle_btn {
                opacity: 10 !important;
            }
        }
        
        /* Dashboard card styling */
        .dash-count {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s;
            cursor: pointer;
        }
        .dash-count:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .dash-counts h4 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        .dash-counts h5 {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 0;
        }
        .dash-imgs i {
            font-size: 40px;
            opacity: 0.3;
        }
        
        /* Table styling */
        .table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #e5e5e5;
            padding: 12px;
        }
        .table td {
            padding: 12px;
            vertical-align: middle;
        }
        
        /* Badge styling */
        .badge-soft-success {
            background: rgba(46, 204, 113, 0.15);
            color: #27AE60;
            padding: 5px 12px;
            border-radius: 50px;
            font-weight: 500;
        }
        .badge-soft-primary {
            background: rgba(52, 152, 219, 0.15);
            color: #2980B9;
            padding: 5px 12px;
            border-radius: 50px;
            font-weight: 500;
        }
        .badge-soft-warning {
            background: rgba(243, 156, 18, 0.15);
            color: #F39C12;
            padding: 5px 12px;
            border-radius: 50px;
            font-weight: 500;
        }
        .badge-soft-danger {
            background: rgba(231, 76, 60, 0.15);
            color: #E74C3C;
            padding: 5px 12px;
            border-radius: 50px;
            font-weight: 500;
        }
        .badge-soft-dark {
            background: rgba(52, 73, 94, 0.15);
            color: #2C3E50;
            padding: 5px 12px;
            border-radius: 50px;
            font-weight: 500;
        }
        .badge-soft-info {
            background: rgba(155, 89, 182, 0.15);
            color: #9B59B6;
            padding: 5px 12px;
            border-radius: 50px;
            font-weight: 500;
        }
        
        /* Action buttons - icon only */
        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        .action-btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.3s;
            background: transparent;
            border: none;
            cursor: pointer;
        }
        .action-btn:hover {
            transform: scale(1.1);
        }
        .action-btn i {
            font-size: 16px;
        }
        .action-btn-view {
            color: #3498DB;
        }
        .action-btn-view:hover {
            background: rgba(52, 152, 219, 0.1);
            color: #2980B9;
        }
        .action-btn-edit {
            color: #F39C12;
        }
        .action-btn-edit:hover {
            background: rgba(243, 156, 18, 0.1);
            color: #E67E22;
        }
        .action-btn-delete {
            color: #E74C3C;
        }
        .action-btn-delete:hover {
            background: rgba(231, 76, 60, 0.1);
            color: #C0392B;
        }
        .action-btn-info {
            color: #9B59B6;
        }
        .action-btn-info:hover {
            background: rgba(155, 89, 182, 0.1);
            color: #8E44AD;
        }
        .action-btn-download {
            color: #E74C3C;
        }
        .action-btn-download:hover {
            background: rgba(231, 76, 60, 0.1);
            color: #C0392B;
        }
        
        /* Card styling */
        .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 20px;
        }
        .card-header {
            background: white;
            border-bottom: 1px solid #e5e5e5;
            padding: 15px 20px;
        }
        .card-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 0;
            color: #2c3e50;
        }
        .card-text {
            color: #7f8c8d;
            font-size: 13px;
            margin-top: 5px;
        }
        
        /* Button styling */
        .btn-added {
            background: linear-gradient(135deg, #2ECC71 0%, #27AE60 100%);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .btn-added:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3);
            color: white;
        }
        
        /* Dashboard card colors */
        .dash-count.das1 { background: linear-gradient(135deg, #3498DB, #2980B9); color: white; }
        .dash-count.das2 { background: linear-gradient(135deg, #F39C12, #E67E22); color: white; }
        .dash-count.das3 { background: linear-gradient(135deg, #9B59B6, #8E44AD); color: white; }
        .dash-count.das4 { background: linear-gradient(135deg, #E74C3C, #C0392B); color: white; }
        .dash-count.das5 { background: linear-gradient(135deg, #1ABC9C, #16A085); color: white; }
        .dash-count.das6 { background: linear-gradient(135deg, #E67E22, #D35400); color: white; }
        .dash-count.das7 { background: linear-gradient(135deg, #2ECC71, #27AE60); color: white; }
        
        .dash-count.das1 .dash-counts h4,
        .dash-count.das1 .dash-counts h5,
        .dash-count.das2 .dash-counts h4,
        .dash-count.das2 .dash-counts h5,
        .dash-count.das3 .dash-counts h4,
        .dash-count.das3 .dash-counts h5,
        .dash-count.das4 .dash-counts h4,
        .dash-count.das4 .dash-counts h5,
        .dash-count.das5 .dash-counts h4,
        .dash-count.das5 .dash-counts h5,
        .dash-count.das6 .dash-counts h4,
        .dash-count.das6 .dash-counts h5,
        .dash-count.das7 .dash-counts h4,
        .dash-count.das7 .dash-counts h5 {
            color: white;
        }
        
        .dash-count.das1 .dash-imgs i,
        .dash-count.das2 .dash-imgs i,
        .dash-count.das3 .dash-imgs i,
        .dash-count.das4 .dash-imgs i,
        .dash-count.das5 .dash-imgs i,
        .dash-count.das6 .dash-imgs i,
        .dash-count.das7 .dash-imgs i {
            color: white;
            opacity: 0.5;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .page-wrapper {
                margin-left: 0;
            }
            .dash-counts h4 {
                font-size: 20px;
            }
            .action-buttons {
                flex-direction: column;
                gap: 8px;
            }
        }

        /* Submenu dropdown styling like IFFS */
        .sidebar-menu ul li.submenu ul {
            display: none;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 0 0 8px 8px;
        }
        .sidebar-menu ul li.submenu.active ul {
            display: block;
        }
        .sidebar-menu ul li.submenu ul li a {
            padding-left: 50px !important;
            font-size: 13px;
        }
        .sidebar-menu ul li.submenu ul li a i {
            margin-right: 10px;
            font-size: 14px;
        }
        .sidebar-menu ul li.submenu ul li.active a {
            background: rgba(46, 204, 113, 0.2);
            color: #2ECC71;
        }
        .menu-arrow {
            float: right;
            transition: transform 0.3s;
        }
        .active > a > .menu-arrow {
            transform: rotate(90deg);
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Global Loader -->
    <div id="global-loader">
        <div class="whirly-loader"></div>
    </div>

    <div class="main-wrapper">
        <div class="header">
            <div class="header-left active">
                <a href="{{ route('tire.dashboard') }}" class="logo">
                    <img src="{{ asset('assets/admin/img/ilogo.jpg') }}" alt="Logo" style="height: 40px; width:50px;">
                </a>
                <a id="toggle_btn" href="javascript:void(0);">
                    <i class="fas fa-bars"></i>
                </a>
            </div>
            <a id="mobile_btn" class="mobile_btn" href="#sidebar">
                <span class="bar-icon">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </a>

            <ul class="nav user-menu">
                <li class="nav-item dropdown has-arrow main-drop">
                    <a href="javascript:void(0);" class="dropdown-toggle nav-link userset" data-bs-toggle="dropdown">
                        <span class="user-img"><i class="fas fa-user-circle fa-2x"></i></span>
                    </a>
                    <div class="dropdown-menu menu-drop-user">
                        <div class="profilename">
                            <hr class="m-0">
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user me-2"></i>
                                @if (Auth::check())
                                    {{ Auth::user()->name }}
                                @endif
                            </a>
                            <a class="dropdown-item" href="{{ route('welcome') }}">
                                <i class="fas fa-exchange-alt me-2"></i> Switch to IFFS
                            </a>
                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <!-- Sidebar with proper submenu dropdown -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="menu-title">Tire Management</li>
                        
                        <li class="{{ request()->routeIs('tire.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('tire.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
                            </a>
                        </li>
                        
                        <!-- Tire Inventory Dropdown -->
                        <li class="submenu {{ request()->routeIs('tire.inventory.*') || request()->routeIs('tire.issue.*') ? 'active' : '' }}">
                            <a href="javascript:void(0);">
                                <i class="fas fa-warehouse"></i> <span>Tire Inventory</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li class="{{ request()->routeIs('tire.inventory.index') || request()->routeIs('tire.inventory.edit') || request()->routeIs('tire.inventory.show') ? 'active' : '' }}">
                                    <a href="{{ route('tire.inventory.index') }}">
                                       Tire Inventory
                                    </a>
                                </li>
                                <li class="{{ request()->routeIs('tire.inventory.create') || request()->routeIs('tire.inventory.create') || request()->routeIs('tire.inventory.edit') || request()->routeIs('tire.inventory.show') ? 'active' : '' }}">
                                    <a href="{{ route('tire.inventory.create') }}">
                                        Add Tire
                                    </a>
                                </li>
                                <li class="{{ request()->routeIs('tire.issue.create') ? 'active' : '' }}">
                                    <a href="{{ route('tire.issue.create') }}">
                                        Issue Tire
                                    </a>
                                </li>
                                <li class="{{ request()->routeIs('tire.issue.index') ? 'active' : '' }}">
                                    <a href="{{ route('tire.issue.index') }}">
                                        Issue Tire List
                                    </a>
                                </li>
                            </ul>
                        </li>
                        
                        <li class="{{ request()->routeIs('tire.vehicles.*') ? 'active' : '' }}">
                            <a href="{{ route('tire.vehicles.index') }}">
                                <i class="fas fa-truck"></i> <span>Vehicles</span>
                            </a>
                        </li>
                        
                        <li class="{{ request()->routeIs('tire.refilling.index') ? 'active' : '' }}">
                            <a href="{{ route('tire.refilling.index') }}">
                                <i class="fas fa-sync-alt"></i> <span>Refilling Orders</span>
                            </a>
                        </li>
                        
                        <li class="{{ request()->routeIs('tire.refilling.vendors.manage') ? 'active' : '' }}">
                            <a href="{{ route('tire.refilling.vendors.manage') }}">
                                <i class="fas fa-building"></i> <span>Manage Vendors</span>
                            </a>
                        </li>
                        
                        <li class="{{ request()->routeIs('tire.scrap.*') ? 'active' : '' }}">
                            <a href="{{ route('tire.scrap.index') }}">
                                <i class="fas fa-trash-alt"></i> <span>Scrap Management</span>
                            </a>
                        </li>
                        
                        <li class="menu-title">System</li>
                        <li>
                            <a href="{{ route('welcome') }}">
                                <i class="fas fa-exchange-alt"></i> <span>Switch to IFFS</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="page-wrapper">
            <div class="content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ $errors->first() }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="GET" style="display: none;"></form>

    <!-- Scripts -->
    <script src="{{ asset('assets/admin/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/feather.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/script.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Hide preloader after page loads
            setTimeout(function() {
                $('#global-loader').fadeOut('slow');
            }, 500);
            
            // Auto-hide alerts after 3 seconds
            setTimeout(function() {
                $(".alert").fadeOut('slow');
            }, 3000);
            
            // Initialize feather icons
            feather.replace();
            
            // Toggle sidebar functionality
            var body = $('body');
            
            // Check localStorage for sidebar state
            var sidebarState = localStorage.getItem('sidebar_mini');
            if (sidebarState === 'true') {
                body.addClass('mini-sidebar');
            }
            
            // Toggle button click handler
            $('#toggle_btn').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                if (body.hasClass('mini-sidebar')) {
                    body.removeClass('mini-sidebar');
                    localStorage.setItem('sidebar_mini', 'false');
                } else {
                    body.addClass('mini-sidebar');
                    localStorage.setItem('sidebar_mini', 'true');
                }
            });
            
            // Mobile menu toggle
            $('#mobile_btn').on('click', function() {
                body.toggleClass('mini-sidebar');
            });
            
            // Handle window resize
            $(window).on('resize', function() {
                if ($(window).width() <= 768) {
                    body.addClass('mini-sidebar');
                } else {
                    var storedState = localStorage.getItem('sidebar_mini');
                    if (storedState === 'true') {
                        body.addClass('mini-sidebar');
                    } else {
                        body.removeClass('mini-sidebar');
                    }
                }
            });
            
            // Trigger resize on load
            $(window).trigger('resize');
            
            // Submenu toggle - like IFFS style
            $('.submenu > a').on('click', function(e) {
                e.preventDefault();
                
                var parentLi = $(this).parent();
                
                // Close other submenus
                $('.submenu').not(parentLi).removeClass('active');
                $('.submenu').not(parentLi).find('ul').slideUp(200);
                
                // Toggle current submenu
                parentLi.toggleClass('active');
                parentLi.find('ul').slideToggle(200);
            });
            
            // Keep submenu open if active page is inside
            $('.submenu').each(function() {
                if ($(this).find('li.active').length) {
                    $(this).addClass('active');
                    $(this).find('ul').show();
                }
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>