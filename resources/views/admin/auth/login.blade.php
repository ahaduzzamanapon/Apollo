<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - {{ \App\Models\Setting::get('app_name', 'System') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            overflow: hidden;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        .login-container {
            height: 100vh;
        }
        .login-image-side {
            background: linear-gradient(135deg, #0d6efd 0%, #0099ff 100%);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            padding: 40px;
        }
        .login-image-side::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('https://source.unsplash.com/random/1920x1080/?medical,hospital') no-repeat center center/cover;
            opacity: 0.1;
        }
        .center-info {
            position: relative;
            z-index: 2;
            text-align: center;
        }
        .login-form-side {
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
        }
        .logo-img {
            height: 200px;
            width: auto;
            margin-bottom: 20px;
            object-fit: contain;
        }
        .form-control {
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }
        .form-control:focus {
            background-color: white;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
            border-color: #0d6efd;
        }
        .btn-login {
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
            background: linear-gradient(to right, #0d6efd, #0099ff);
            border: none;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
        }

        /* Mobile specific adjustments */
        @media (max-width: 768px) {
            .login-image-side {
                display: none;
            }
            body {
                background: white;
            }
        }
    </style>
</head>
<body>

@php
    $center = \App\Models\CenterDetails::first();
    $logoBase64 = null;
    if ($center && $center->logo_image) {
        $logoPath = public_path('storage/' . $center->logo_image);
        if (file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $data = file_get_contents($logoPath);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
    }
@endphp

<div class="container-fluid p-0">
    <div class="row g-0 login-container">
        <!-- Left Side: Center Info (Hidden on Mobile) -->
        <div class="col-md-6 col-lg-7 login-image-side">
            <div class="center-info">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo" class="logo-img bg-white  p-2 shadow mb-4">
                @endif
                <h1 class="fw-bold mb-2" style="white-space:nowrap">{{ $center->name_en ?? config('app.name') }}</h1>
                <h4 class="text-white-50" style="white-space:nowrap;font-weight: bold;color:white !important">{{ $center->name_bn ?? '' }}</h4>
                <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                    <i class="bi bi-geo-alt-fill"></i>
                    <span>{{ $center->address ?? '' }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-telephone-fill"></i>
                    <span>{{ $center->phone ?? '' }}</span>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="col-md-6 col-lg-5 login-form-side">
            <div class="login-card">
                <div class="text-center mb-5 d-md-none">
                    <!-- Mobile Logo View -->
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Logo" class="logo-img mb-3" style>
                    @endif
                    <h4 class="fw-bold">{{ $center->name_en ?? config('app.name') }}</h4>
                </div>

                <div class="mb-5">
                    <h3 class="fw-bold">Welcome Back!</h3>
                    <p class="text-muted">Please sign in to continue accessing the panel.</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm mb-4">
                        <i class="bi bi-exclamation-circle-fill me-2"></i> {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.post') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold text-uppercase">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control border-start-0 ps-0" placeholder="name@example.com" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold text-uppercase">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" class="form-control border-start-0 ps-0" placeholder="Enter your password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-login w-100 mb-4">
                        Sign In <i class="bi bi-arrow-right ms-2"></i>
                    </button>

                    <div class="text-center text-muted small">
                        &copy; {{ date('Y') }} {{ $center->name_en }}. All rights reserved.
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
