<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="png" href="{{ asset('images/logo.svg') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fjalla+One&family=Poppins:ital,wght@1,800&family=Roboto:wght@900&family=Ubuntu:wght@700&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            position: relative;
            background-image: url('{{ asset('images/bg1.jpg') }}');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        .container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-box {
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            padding: 20px;
            width: 90%;
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
        }

        .login-box img {
            max-width: 100px;
            height: auto;
            transform: rotateY(15deg);
            transition: transform 0.3s ease;
        }

        .login-box img:hover {
            transform: rotateY(0deg);
        }

        i {
            font-size: 25px;
            padding: 10px;
        }

        h3 {
            font-family: 'Poppins', sans-serif;
            font-size: 24px;
        }

        label {
            font-size: 18px;
            font-family: 'Fjalla One', sans-serif;
        }

        input {
            font-family: 'Roboto', sans-serif;
            font-size: 16px;
        }

        .btn-primary {
            font-size: 18px;
        }

        #login {
            font-family: 'Ubuntu', sans-serif;
        }

        @media (max-width: 768px) {
            h3 {
                font-size: 20px;
            }

            label {
                font-size: 16px;
            }

            input {
                font-size: 14px;
            }

            .btn-primary {
                font-size: 16px;
            }
        }

        footer {
            padding: 10px;
            border-top: 1px solid #ccc;
        }

        footer p {
            margin: 0;
            font-size: 14px;
            color: #ffffff
        }

        .shake {
            animation: shake 0.4s cubic-bezier(0.36, 0.07, 0.19, 0.97);
            transform-origin: center;
        }


        .float-end {
            float: right;
        }


        @keyframes beat {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            10%,
            30%,
            50%,
            70%,
            90% {
                transform: translateX(-5px);
            }

            20%,
            40%,
            60%,
            80% {
                transform: translateX(5px);
            }
        }

        @keyframes rotateContinuously {
            0% {
                transform: rotateY(0deg);
            }

            100% {
                transform: rotateY(360deg);
            }


        }

        .rotate-3d {
            max-width: 100px;
            height: auto;
            animation: rotateContinuously 5s linear infinite;
        }

        .icon-beat {
            animation: beat 2s ease infinite;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <title>Login - Taka Dashboards</title>
    <link type="text/css" rel="stylesheet" href="{{ asset('css/app.css') }}">

</head>
<?php
$rand = rand(999, 100);
?>

<body>
    <div class="container">
        <div class="login-box w-60 text-center">
            <img src="{{ asset('images/logopanjang.svg') }}" alt="Logo Perusahaan" class="mb-3 ">
            <h3 class="mb-4">ACCOUNT LOGIN</h3>

            <form action="{{ route('login-proses') }}" method="POST">
                @csrf
                <div class="mb-3 text-start">
                    <label for="username" class="form-label"><i
                            class="fas fa-user icon-beat @error('username') shake @enderror"></i> Username</label>
                    <input type="username" value="{{ old('username') }}" name="username" class="form-control"
                        placeholder="Masukkan Username Anda" required>
                    @error('username')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3 text-start">
                    <label for="password" class="form-label"><i
                            class="fas fa-lock icon-beat @error('password') shake @enderror"></i> Password</label>
                    <div class="password-input">
                        <input type="password" id="password" name="password" class="form-control"
                            placeholder="Masukkan Password Anda" required>
                    </div>
                    @error('password')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div id="login" class="mb-1 d-grid gap-3 col-6 mx-auto">
                    <button name="submit" type="submit" class="btn btn-primary">LOGIN</button>
                </div>
            </form>
        </div>
        <footer class="text-center">
            <p>&copy; 2025 TAKA TEXTILES. Hak Cipta Dilindungi.</p>
        </footer>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('lte/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('lte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('lte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Toastr -->
    <script src="{{ asset('lte/plugins/toastr/toastr.min.js') }}"></script>

    <script>
        $(function() {
            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000
            });

            @if (session('error'))
                Toast.fire({
                    icon: 'error',
                    title: '{{ session('error') }}'
                });
            @endif

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    text: '{{ session('success') }}'
                });
            @endif
        });
    </script>
</body>

</html>
