<!DOCTYPE html>
<html lang="en">
<!-- Mirrored from dreamspos.dreamguystech.com/html/template/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 19 Feb 2023 09:04:46 GMT -->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="POS - Bootstrap Admin Template">
    <meta name="keywords"
        content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern,  html5, responsive">
    <meta name="author" content="Dreamguys - Bootstrap Admin Template">
    <meta name="robots" content="noindex, nofollow">
    <title>IFFS Dashboard</title>
    <link rel="shortcut icon" type="image/x-icon"
        href="https://dreamspos.dreamguystech.com/html/template/assets/img/favicon.png">
    <link rel="stylesheet" href="/assets/admin/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/admin/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="/assets/admin/css/animate.css">
    <link rel="stylesheet" href="/assets/admin/css/select2.min.css">
    <link rel="stylesheet" href="/assets/admin/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="/assets/admin/css/fontawesome.min.css">
    {{-- <link rel="stylesheet" href="/assets/admin/css/all.min.css"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/assets/admin/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css"
        integrity="sha384-b6lVK+yci+bfDmaY1u0zE8YYJt0TZxLEAFyYSLHId4xoVvsrQu3INevFKo+Xir8e" crossorigin="anonymous">


    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        #productTableBody td:nth-child(6) {
            text-align: right;
        }

        #productTableBody td:last-child {
            text-align: center;
        }

        .badge-warning {
            background-color: #dc3545;
            /* red */
        }

        .badge-success {
            background-color: #28a745;
            /* green */
        }
    </style>
</head>

<body>
    <div id="global-loader">
        <div class="whirly-loader"> </div>
    </div>

    <div class="main-wrapper">
        <div class="header">
            <div class="header-left active">
                {{-- <a href="{{ url('/admindashboard') }}" class="logo logo-normal"><img src="assets/admin/img/ilogo.jpg"
                        alt=""></a> --}}
                <a id="toggle_btn" href="javascript:void(0);"></a>
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
                        <span class="user-img"><i class="me-2" data-feather="user"></i>
                            <span class="status online"></span></span>
                    </a>
                    <div class="dropdown-menu menu-drop-user">
                        <div class="profilename">

                            <hr class="m-0">
                            <a class="dropdown-item" href=""> <i class="me-2" data-feather="user"></i>
                                @if (Auth::check())
                                    {{ Auth::user()->name }}
                                @endif
                            </a>
                            <a class="dropdown-item" href="{{ url('logout') }}"><i class="me-2"
                                    data-feather="settings"></i>Logout</a>
                            <a class="dropdown-item" href="{{ route('welcome') }}">
                                <i class="fas fa-exchange-alt"></i> Switch to IFFS
                            </a>
                        </div>
                    </div>
                </li>
            </ul>

        </div>

        <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>

                        <li class="active">
                            <a href="{{ url('admin/dashboard') }}"><img src="/assets/admin/img/icons/dashboard.svg"
                                    alt="img"><span> Dashboard</span> </a>
                        </li>


                        @if (isset($getLoginUserPermission))

                            @foreach ($getLoginUserPermission as $check)
                                @if ($check->slug === 'purchase_order')
                                    <li class="submenu">
                                        <a href="javascript:void(0);"><svg xmlns="http://www.w3.org/2000/svg"
                                                width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" class="feather feather-shopping-bag">
                                                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                                <line x1="3" y1="6" x2="21" y2="6">
                                                </line>
                                                <path d="M16 10a4 4 0 0 1-8 0"></path>
                                            </svg><span> Purchase Order</span> <span class="menu-arrow"></span></a>
                                        <ul>
                                            @foreach ($getLoginUserPermission as $subCheck)
                                                @if ($subCheck->slug === 'add_purchase_order')
                                                    <li><a href="{{ route('create-purchase-order-view') }}">Add Purchase
                                                            Order</a>
                                                    </li>
                                                @elseif ($subCheck->slug === 'purchase_order_list')
                                                    <li><a href="{{ route('purchase-orders') }}">Purchase Order
                                                            List</a></li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </li>
                                @elseif ($check->slug === 'grn')
                                    <li class="submenu">
                                        <a href="javascript:void(0);"><svg xmlns="http://www.w3.org/2000/svg"
                                                width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" class="feather feather-copy">
                                                <rect x="9" y="9" width="13" height="13" rx="2"
                                                    ry="2">
                                                </rect>
                                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1">
                                                </path>
                                            </svg><span> Grn</span> <span class="menu-arrow"></span></a>
                                        <ul>
                                            @foreach ($getLoginUserPermission as $subCheck)
                                                @if ($subCheck->slug === 'grn_list')
                                                    <!-- Display "GRN List" if user has permission -->
                                                    <li><a href="{{ route('get-all-grns') }}">GRN List</a></li>
                                                @elseif ($subCheck->slug === 'add_grn')
                                                    <!-- Display "Add GRN" if user has permission -->
                                                    <li><a href="{{ route('create-grn-view') }}">Add GRN</a></li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </li>
                                @elseif ($check->slug === 'issue_note')
                                    <li class="submenu">
                                        <a href="javascript:void(0);"><svg xmlns="http://www.w3.org/2000/svg"
                                                width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" class="feather feather-shopping-cart">
                                                <circle cx="9" cy="21" r="1"></circle>
                                                <circle cx="20" cy="21" r="1"></circle>
                                                <path
                                                    d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6">
                                                </path>
                                            </svg><span> Issue Note</span> <span class="menu-arrow"></span></a>
                                        <ul>
                                            @foreach ($getLoginUserPermission as $subCheck)
                                                @if ($subCheck->slug === 'issue_note_list')
                                                    <!-- Display "Issue Note List" if user has permission -->
                                                    <li><a href="{{ route('get-all-issue-note') }}">Issue Note List</a>
                                                    </li>
                                                @elseif ($subCheck->slug === 'add_issue_note')
                                                    <!-- Display "Add Issue Note" if user has permission -->
                                                    <li><a href="{{ route('create-issue-note-view') }}">Add Issue
                                                            Note</a></li>
                                                @endif
                                            @endforeach

                                        </ul>
                                    </li>
                                @elseif ($check->slug === 'stock')
                                    <li class="">
                                        <a href="{{ route('stock-get-all') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-hard-drive">
                                                <line x1="22" y1="12" x2="2" y2="12">
                                                </line>
                                                <path
                                                    d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z">
                                                </path>
                                                <line x1="6" y1="16" x2="6.01" y2="16">
                                                </line>
                                                <line x1="10" y1="16" x2="10.01" y2="16">
                                                </line>
                                            </svg>
                                            <span>Stock</span>
                                        </a>
                                    </li>
                                @elseif ($check->slug === 'products')
                                <li class="submenu">
                <a href="javascript:void(0);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-box">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                    <span>Products</span>
                    <span class="menu-arrow"></span>
                </a>
                <ul>
                    <li><a href="{{ route('product-get-all') }}">Product List</a></li>
                    <li><a href="{{ route('reports.product.form') }}">Product Reports</a></li>
                </ul>
            </li>
                                @elseif ($check->slug === 'supplier')
                                    <li class="">
                                        <a href="{{ route('supplier-get-all') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-users">
                                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="9" cy="7" r="4"></circle>
                                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                            </svg>
                                            <span>Supplier</span>
                                        </a>
                                    </li>
                                @elseif ($check->slug === 'users')
                                    <li class="submenu">
                                        <a href="javascript:void(0);"><svg xmlns="http://www.w3.org/2000/svg"
                                                width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" class="feather feather-user-check">
                                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="8.5" cy="7" r="4"></circle>
                                                <polyline points="17 11 19 13 23 9"></polyline>
                                            </svg><span>Users</span> <span class="menu-arrow"></span></a>
                                        <ul>
                                            <li><a href="{{ route('user-get-all') }}">Users List</a></li>
                                            <li><a href="{{ route('role-all-get-all') }}">Users Roles</a></li>
                                            <li><a href="{{ route('get-all-user-permissions') }}">Users
                                                    Permissions</a>
                                            </li>
                                            <li><a href="{{ route('get-all-user-role-permissions') }}">User Role
                                                    Permission</a>
                                            </li>

                                        </ul>
                                    </li>
                                @endif
                            @endforeach
                        @endif

                    </ul>
                </div>
            </div>
        </div>


        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
        {{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script> --}}
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.full.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>

        <script src="/assets/admin/js/jquery-3.6.0.min.js"></script>
        <script src="/assets/admin/js/feather.min.js"></script>
        <script src="/assets/admin/js/jquery.slimscroll.min.js"></script>
        <script src="/assets/admin/js/jquery.dataTables.min.js"></script>
        <script src="/assets/admin/js/dataTables.bootstrap4.min.js"></script>
        <script src="/assets/admin/js/bootstrap.bundle.min.js"></script>
        <script src="/assets/admin/js/apexcharts.min.js"></script>
        <script src="/assets/admin/js/chart-data.js"></script>
        <script src="/assets/admin/js/script.js"></script>
        <script src="{{ url('assets/admin/js/select2.min.js') }}"></script>
        <script src="/assets/admin/js/moment.min.js"></script>
        <script src="/assets/admin/js/bootstrap-datetimepicker.min.js"></script>
        <script src="/assets/admin/js/sweetalert2.all.min.js"></script>
        <script src="/assets/admin/js/sweetalerts.min.js"></script>

</body>
<!-- Mirrored from dreamspos.dreamguystech.com/html/template/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 19 Feb 2023 09:04:46 GMT -->


</html>
