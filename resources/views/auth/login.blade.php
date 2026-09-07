<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل الدخول — {{ config('app.name', 'نظام إدارة المخازن') }}</title>

    <!-- Bootstrap 5 RTL & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Cairo Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #0d6efd;
            --sidebar-bg: #1e293b;
        }
        * { font-family: 'Cairo', sans-serif; }
        
        body {
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: #fff;
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
        }

        .login-header {
            background: var(--sidebar-bg);
            color: #fff;
            padding: 2rem 1.5rem;
            text-align: center;
        }

        .login-header .logo-icon {
            width: 60px;
            height: 60px;
            background: var(--primary-color);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.75rem;
            margin: 0 auto 1rem;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }

        .login-header h4 {
            margin: 0;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .login-header p {
            margin: 0.5rem 0 0;
            color: #94a3b8;
            font-size: 0.85rem;
        }

        .login-body {
            padding: 2rem 1.5rem;
        }

        .form-floating > .form-control {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            padding-right: 2.5rem; /* مساحة للأيقونة */
        }

        .form-floating > .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }

        .input-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            z-index: 5;
        }

        .btn-login {
            background: var(--primary-color);
            border: none;
            border-radius: 10px;
            padding: 0.75rem;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.2s;
        }

        .btn-login:hover {
            background: #0b5ed7;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .alert {
            border-radius: 10px;
            border: none;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <!-- رأس بطاقة الدخول -->
        <div class="login-header">
            <div class="logo-icon">
                <i class="bi bi-box-seam-fill"></i>
            </div>
            <h4>نظام إدارة المخازن</h4>
            <p>سجل دخولك للوصول إلى لوحة التحكم</p>
        </div>

        <!-- جسم بطاقة الدخول -->
        <div class="login-body">
            
            <!-- عرض رسائل النجاح (مثل إعادة تعيين كلمة المرور) -->
            @if (session('status'))
                <div class="alert alert-success mb-3">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('status') }}
                </div>
            @endif

            <!-- عرض أخطاء التحقق -->
            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- نموذج تسجيل الدخول -->
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- حقل البريد الإلكتروني -->
                <div class="form-floating mb-3 position-relative">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           placeholder="name@example.com" 
                           required 
                           autofocus>
                    <label for="email">البريد الإلكتروني</label>
                </div>

                <!-- حقل كلمة المرور -->
                <div class="form-floating mb-3 position-relative">
                    <i class="bi bi-lock input-icon"></i>
                    <input type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           id="password" 
                           name="password" 
                           placeholder="كلمة المرور" 
                           required>
                    <label for="password">كلمة المرور</label>
                </div>

                <!-- تذكرني -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label small text-muted" for="remember">
                            تذكرني
                        </label>
                    </div>
                    
                    @if (Route::has('password.request'))
                        <a class="small text-decoration-none" href="{{ route('password.request') }}">
                            نسيت كلمة المرور؟
                        </a>
                    @endif
                </div>

                <!-- زر الدخول -->
                <button type="submit" class="btn btn-primary btn-login w-100 text-white">
                    <i class="bi bi-box-arrow-in-left me-2"></i>تسجيل الدخول
                </button>

            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>