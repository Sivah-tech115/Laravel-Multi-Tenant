<!DOCTYPE html>
<html lang="en">

<head>

    <title>Sign In</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="E-commerce platform login page for product sales" />
    <meta name="keywords" content="e-commerce, login, products, online store, customer login">
    <meta name="author" content="Your Company" />

    <!-- Favicon icon -->
    <link rel="icon" href="{{ asset('assets/images/favicon.png') }}" type="image/png">
    <!-- FontAwesome icon -->
    <link rel="stylesheet" href="assets/fonts/fontawesome/css/fontawesome-all.min.css">
    <!-- Animation CSS -->
    <link rel="stylesheet" href="assets/plugins/animation/css/animate.min.css">
    <!-- Vendor CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        .auth-wrapper.login {
            background: linear-gradient(90deg, rgba(0, 123, 255, 0.8), rgba(0, 0, 0, 0.5)), url('assets/newimg/bg-sign.jpg') no-repeat center/cover;
            background-size: cover;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .auth-content {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            max-width: 1200px;
        }

        .card {
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            background-color: white;
            width: 100%;
            max-width: 500px;
        }

        .login_with_logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .login_with_logo img {
            max-width: 150px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group input {
            border-radius: 5px;
            padding: 12px;
            border: 1px solid #ddd;
            width: 100%;
            font-size: 16px;
        }

        .btn-primary {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .text-muted {
            text-align: center;
            font-size: 14px;
        }

        .text-muted a {
            color: #007bff;
            text-decoration: none;
        }

        .text-muted a:hover {
            text-decoration: underline;
        }

        .image-section {
            display: none;
            flex: 1;
            justify-content: center;
            align-items: center;
        }

        .image-section img {
            width: 100%;
            max-width: 450px;
            border-radius: 10px;
        }

        @media (min-width: 768px) {
            .auth-content {
                flex-direction: row;
            }

            .login-form-section {
                flex: 1;
            }

            .image-section {
                display: flex;
            }
        }
    </style>

</head>

<body>

    <!-- [ signin-img-tabs ] start -->
    <div class="auth-wrapper login">
        <div class="auth-content">
                   <!-- Image Section -->
                   <div class="image-section">
                <img src="assets/images/istockphoto-1428709516-612x612.jpg" alt="Product Image">
            </div>
            <div class="card shadow login-form-section">
                <div class="card-body">
                    <!-- Logo and Heading -->
                    <div class="login_with_logo">
                    <img src="assets/images/compraspesa.webp" alt="Logo" class="img-fluid">
                        <h2 class="mt-4">Welcome Back to Our Store!</h2>
                    </div>

                    <!-- Error or Success Message -->
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if (Session::has('message'))
                        <p class="alert alert-info">{{ Session::get('message') }}</p>
                    @endif
                    @if (Session::has('success'))
                        <p class="alert alert-info">{{ Session::get('success') }}</p>
                    @endif

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Enter your email">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Enter your password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group text-start mb-3">
                            <div class="checkbox checkbox-primary d-inline">
                                <input type="checkbox" name="remember" id="checkbox-fill-a1" {{ old('remember') ? 'checked' : '' }}>
                            </div>
                            <label for="checkbox-fill-a1">Remember Me</label>
                        </div>

                        <button class="btn btn-primary mb-4">Login</button>

                        <div class="text-muted">
                            <p>Don't have an account? <a href="{{ url('/register') }}">Create one now</a></p>
                            <!-- <p><a href="{{ route('password.request') }}">Forgot your password?</a></p> -->
                        </div>
                    </form>
                </div>
            </div>

     
        </div>
    </div>
    <!-- [ signin-img-tabs ] end -->

    <!-- Required JS -->
    <script src="assets/js/vendor-all.min.js"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>

</body>

</html>
