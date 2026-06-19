<!-- resources/views/welcome.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="IFFS System Selection">
    <meta name="author" content="IDEAL SOFT">
    <title>System Selection - IFFS</title>
    <link rel="shortcut icon" type="image/x-icon" href="#">
    <link rel="stylesheet" href="assets/admin/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/admin/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/admin/css/all.min.css">
    <link rel="stylesheet" href="assets/admin/css/style.css">
    <style>
        body {
            background: #f7f7f7;
            font-family: 'Poppins', sans-serif;
        }
        .system-selection-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .system-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            overflow: hidden;
            margin-bottom: 30px;
            border: 1px solid #e5e5e5;
        }
        .system-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        .card-header-icon {
            padding: 40px 0 20px;
            text-align: center;
            font-size: 80px;
        }
        .iffs-header {
            background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
        }
        .tire-header {
            background: linear-gradient(135deg, #2ECC71 0%, #27AE60 100%);
        }
        .card-content {
            padding: 30px;
            text-align: center;
        }
        .system-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #2c3e50;
        }
        .system-description {
            color: #7f8c8d;
            line-height: 1.6;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .launch-btn {
            padding: 10px 30px;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s;
            border: none;
            color: white;
        }
        .launch-iffs {
            background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
        }
        .launch-tire {
            background: linear-gradient(135deg, #2ECC71 0%, #27AE60 100%);
        }
        .launch-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .user-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: white;
            padding: 10px 20px;
            border-radius: 50px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        .logout-link {
            position: absolute;
            top: 20px;
            left: 20px;
            background: #dc3545;
            color: white;
            padding: 8px 20px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            z-index: 1000;
        }
        .logout-link:hover {
            background: #c82333;
            color: white;
            transform: scale(1.05);
        }
        @media (max-width: 768px) {
            .system-card {
                margin: 15px;
            }
            .system-title {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <a href="{{ route('logout') }}" class="logout-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="GET" style="display: none;"></form>

    <div class="user-badge">
        <i class="fas fa-user-circle"></i> {{ Auth::user()->name ?? 'User' }}
    </div>

    <div class="system-selection-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 text-center mb-5">
                    <h1 style="color: #2c3e50; font-size: 42px; font-weight: 600;">Welcome to IFFS</h1>
                    <p style="color: #7f8c8d; font-size: 18px;">Choose your system to continue</p>
                </div>

                <div class="col-md-5">
                    <div class="system-card" onclick="window.location.href='{{ route('dashboard') }}'">
                        <div class="iffs-header card-header-icon">
                            <i class="fas fa-truck-moving" style="color: white; font-size: 80px;"></i>
                        </div>
                        <div class="card-content">
                            <h3 class="system-title">IFFS Management System</h3>
                            <p class="system-description">
                                Complete Inventory Management System with Purchase Orders, 
                                GRN, Stock Management, Supplier Management, User Management 
                                and Comprehensive Reporting
                            </p>
                            <button class="launch-btn launch-iffs">
                                Launch System <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="system-card" onclick="window.location.href='{{ route('tire.dashboard') }}'">
                        <div class="tire-header card-header-icon">
                            <i class="fas fa-car" style="color: white; font-size: 80px;"></i>
                        </div>
                        <div class="card-content">
                            <h3 class="system-title">Tire Lifecycle Management</h3>
                            <p class="system-description">
                                Specialized Tire Tracking System with Serial Number Management,
                                Vehicle Allocation, Refilling Module, Tire Passport History,
                                and Complete Scrap Management
                            </p>
                            <button class="launch-btn launch-tire">
                                Launch System <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/admin/js/jquery-3.6.0.min.js"></script>
    <script src="assets/admin/js/bootstrap.bundle.min.js"></script>
</body>
</html>