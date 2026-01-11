<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Apollo Diagnostic Center</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f0f4f8; /* Soft light background */
        }
        .login-card {
            border-radius: 15px;
            padding: 2rem;
            background: #ffffff;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .login-card:hover {
            transform: translateY(-5px);
        }
        .logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }
        .center-name {
            font-weight: 700;
            font-size: 12px;
            margin-top: 10px;
            color: #0d6efd;
        }
        .form-label {
            font-weight: 500;
        }
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0b5ed7;
        }
    </style>
</head>
<body>

<div class="container min-vh-100 d-flex align-items-center justify-content-center">
    <div class="row w-100 justify-content-center">
        <div class="col-12 col-sm-8 col-md-6 col-lg-4">
            <div class="login-card">

                @php
                    $logoPath = public_path('storage/' . $center->logo_image);
                    $logoBase64 = '';
                    if (file_exists($logoPath)) {
                        $type = pathinfo($logoPath, PATHINFO_EXTENSION);
                        $data = file_get_contents($logoPath);
                        $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    }
                @endphp

                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo" style="max-height: 80px; width: auto;">
                @endif

                <!-- Center Name -->
                <div class="center-name mb-4">
                    {{$center->name_en}}<br>
                    {{$center->name_bn}} <br>

                </div>

                <!-- Error Messages -->
                @if($errors->any())
                    <div class="alert alert-danger text-start">
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('admin.login.post') }}">
                    @csrf
                    <div class="mb-3 text-start">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                    </div>

                    <div class="mb-3 text-start">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>

                    <!-- Optional Footer Links -->
                    {{-- <div class="text-muted small">
                        Forgot your password? <a href="#" class="text-decoration-none">Reset here</a>
                    </div> --}}
                </form>

            </div>
        </div>
    </div>
</div>

</body>
</html>
