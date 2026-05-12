<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="POS - Bootstrap Admin Template">
    <meta name="keywords"
        content="admin, estimates, bootstrap, business, corporate, creative, invoice, html5, responsive, Projects">
    <meta name="author" content="Dreamguys - Bootstrap Admin Template">
    <meta name="robots" content="noindex, nofollow">
    <title>IFFS Login</title>
    <link rel="shortcut icon" type="image/x-icon" href="#">
    {{-- <link rel="stylesheet" href="assets/admin/css/bootstrap.min.css"> --}}
    {{-- <link rel="stylesheet" href="assets/admin/css/fontawesome.min.css"> --}}
    {{-- <link rel="stylesheet" href="assets/admin/css/all.min.css"> --}}
    <link rel="stylesheet" href="assets/admin/css/style.css">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
</head>

<body class="account-page">
    <div class="main-wrapper">
        <div class="account-content">
            <div class="login-wrapper">
                <div class="login-content">
                    <div class="login-userset" style="margin-top: 80px; margin-bottom: 60px;">
                        <div class="login-logo logo-normal">
                            <img src="assets/admin/img/ilogo.jpg" alt="img">
                        </div>

                        <div>

                            @if ($errors->has('message'))
                                <div class="alert alert-danger" id="alert">
                                    <button type="button" class="close" data-dismiss="alert">x</button>
                                    {{ $errors->first('message') }}
                                </div>
                            @endif
                        </div>
                        <a href="" class="login-logo logo-white">
                            <img src="" alt="">
                        </a>
                        <form action="{{ url('login-check') }}" method="POST">
                            @csrf
                            @method('POST')
                            <div class="login-userheading">
                                <h3>Sign In</h3>
                                <h4>Please login to your account</h4>
                            </div>
                            <div class="form-login">
                                <label>Email</label>
                                <div class="form-addons">
                                    <input type="email" name="email" placeholder="Enter your email">
                                    <img src="assets/admin/img/icons/mail.svg" alt="img">
                                </div>
                            </div>
                            <div class="form-login">
                                <label>Password</label>
                                <div class="pass-group">
                                    <input type="password" name="password" class="pass-input"
                                        placeholder="Enter your password">
                                    <span class="fas toggle-password fa-eye-slash"></span>
                                </div>
                            </div>
                            <div class="form-login">
                                <button class="btn btn-login" type="submit">Sign In</button>
                            </div>
                        </form>

                    </div>
                    <span>Inter Freight Forwarding Service © 2024 Solution By <b>IDEAL SOFT.</b></span>

                </div>
                <div class="login-img">
                    <img src="assets/admin/img/login.jpg" alt="img">
                </div>
            </div>
        </div>
    </div>


    <!-- Include jQuery library -->
    <script src="assets/admin/js/jquery-3.6.0.min.js"></script>
    <script src="assets/admin/js/feather.min.js"></script>
    <script src="assets/admin/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script> <!-- Include SweetAlert library -->


    <script>
        $(document).ready(function() {
            // Add an event listener for your form
            $('form').on('submit', function(e) {
                var form = this; // Store a reference to the form

                e.preventDefault(); // Prevent the form from submitting initially

                var email = $('input[name="email"]').val();
                var password = $('input[name="password"]').val();

                if (email === '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Email is required!',
                        showConfirmButton: false, // Disable the "OK" button
                        timer: 1000, // Auto-close after 1000ms (1 second)
                    });
                } else if (password === '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Password is required!',
                        showConfirmButton: false, // Disable the "OK" button
                        timer: 1000, // Auto-close after 1000ms (1 second)
                    });
                } else {
                    // Continue with form submission
                    $(this).off('submit'); // Remove the event listener to allow the form to submit
                    this.submit();
                }
            });
        });
    </script>


    <script src="assets/admin/js/script.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            setTimeout(function() {
                $("div.alert").remove();
            }, 1500);
        });
    </script>


</body>

</html>
