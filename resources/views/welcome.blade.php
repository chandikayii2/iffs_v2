<!-- resources/views/welcome.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="IFFS System Selection">
    <meta name="author" content="IDEAL SOFT">
    <title>System Selection - IFFS</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/admin/img/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/all.min.css') }}">
    <style>
        body {
            background: #f4f7f6;
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #333333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar-selection {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .user-badge {
            color: #4b5563;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logout-link {
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            color: #ef4444;
            padding: 6px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .logout-link:hover {
            background: #fee2e2;
            color: #dc2626;
            border-color: #fca5a5;
        }

        .main-container {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .logo-wrapper {
            margin-bottom: 20px;
            text-align: center;
        }
        .logo-img {
            max-height: 70px;
        }

        .welcome-title {
            font-size: 32px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
        }
        .welcome-subtitle {
            color: #6b7280;
            font-size: 16px;
            margin-bottom: 40px;
            text-align: center;
        }

        .system-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            transition: all 0.25s ease-in-out;
            cursor: pointer;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .system-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border-color: #cbd5e1;
        }

        .card-header-icon {
            padding: 35px 0 20px;
            text-align: center;
            background: #f8fafc;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .icon-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
        }

        .iffs-card .icon-circle {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }
        .tyre-card .icon-circle {
            background: rgba(249, 115, 22, 0.1);
            color: #f97316;
        }

        .card-content {
            padding: 25px 30px 30px;
            text-align: center;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .system-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 12px;
            color: #111827;
        }

        .system-description {
            color: #4b5563;
            line-height: 1.5;
            margin-bottom: 25px;
            font-size: 14px;
            flex-grow: 1;
        }

        .launch-btn {
            padding: 10px 24px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s ease;
            border: none;
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
        }

        .launch-iffs {
            background: #3b82f6;
        }
        .launch-iffs:hover {
            background: #2563eb;
        }
        
        .launch-tyre {
            background: #f97316;
        }
        .launch-tyre:hover {
            background: #ea580c;
        }

        .footer-credit {
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            padding: 15px 0;
            text-align: center;
            font-size: 13px;
            color: #9ca3af;
            width: 100%;
        }
        
        @media (max-width: 768px) {
            .navbar-selection {
                padding: 15px 20px;
            }
            .welcome-title {
                font-size: 26px;
            }
            .system-card {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="navbar-selection">
        <div class="user-badge">
            <i class="fas fa-user-circle text-secondary"></i> <strong>User:</strong> {{ Auth::user()->name ?? 'User' }}
        </div>
        
        <a href="{{ route('logout') }}" class="logout-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt me-1"></i> Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="GET" style="display: none;"></form>
    </div>

    <div class="main-container container">
        <div class="row justify-content-center text-center">
            <div class="col-12 logo-wrapper">
                <img src="{{ asset('assets/admin/img/ilogo.jpg') }}" class="logo-img" alt="IFFS Logo">
            </div>
            <div class="col-12">
                <h1 class="welcome-title">Welcome to IFFS Suite</h1>
                <p class="welcome-subtitle">Select a management module below to launch the system workspace.</p>
            </div>
        </div>

        <div class="row justify-content-center g-4" style="max-width: 900px; width: 100%;">
            <div class="col-md-6">
                <div class="system-card iffs-card" onclick="window.location.href='{{ route('dashboard') }}'">
                    <div class="card-header-icon">
                        <div class="icon-circle">
                            <i class="fas fa-truck-moving"></i>
                        </div>
                    </div>
                    <div class="card-content">
                        <h3 class="system-title">IFFS Management System</h3>
                        <p class="system-description">
                            Complete Factory Inventory Control with Purchase Orders, Goods Received Notes (GRN), Stock Trackers, Supplier Catalogues, and Audit Analytics.
                        </p>
                        <button class="launch-btn launch-iffs">
                            Launch Workspace <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="system-card tyre-card" onclick="window.location.href='{{ route('tyre.dashboard') }}'">
                    <div class="card-header-icon">
                        <div class="icon-circle">
                            <i class="fas fa-circle-notch"></i>
                        </div>
                    </div>
                    <div class="card-content">
                        <h3 class="system-title">Tyre Lifecycle Management</h3>
                        <p class="system-description">
                            Specialized Fleet Tyre Tracking with Serial Number Histories, Live Vehicle Allocations, Retreading/Refill Orders, Scrap Controls, and Tyre Passports.
                        </p>
                        <button class="launch-btn launch-tyre">
                            Launch Workspace <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-credit">
        &copy; {{ date('Y') }} IDEAL SOFT. All rights reserved.
    </div>

    <script src="{{ asset('assets/admin/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>