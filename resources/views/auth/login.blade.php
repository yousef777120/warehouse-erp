<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل الدخول — {{ config('app.name', 'نظام إدارة المخازن') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Cairo', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            background: #ffffff;
            overflow-x: hidden;
        }

        /* ==========================================
           الجانب الأيمن - نموذج تسجيل الدخول
           ========================================== */
        .login-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            background: #ffffff;
        }

        .login-container {
            width: 100%;
            max-width: 480px;
        }

        .login-header {
            margin-bottom: 3rem;
            text-align: center;
        }

        .login-header h1 {
            font-size: 2.2rem;
            font-weight: 900;
            color: #1a1a2e;
            margin-bottom: 0.75rem;
        }

        .login-header p {
            color: #666;
            font-size: 1rem;
        }

        /* حقول الإدخال */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
        }

        .form-label i {
            margin-left: 0.5rem;
            color: #667eea;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper input {
            width: 100%;
            padding: 1rem 3rem 1rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s;
            background: #f8f9fa;
            font-family: 'Cairo', sans-serif;
        }

        .input-wrapper input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .input-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 1.2rem;
            transition: all 0.3s;
        }

        .input-wrapper input:focus ~ .input-icon {
            color: #667eea;
        }

        .toggle-password {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #999;
            cursor: pointer;
            font-size: 1.2rem;
            transition: all 0.3s;
            padding: 0;
        }

        .toggle-password:hover {
            color: #667eea;
        }

        /* خيارات إضافية */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        .remember-me input {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #667eea;
        }

        .remember-me label {
            margin: 0;
            cursor: pointer;
            color: #666;
            font-size: 0.9rem;
        }

        .forgot-password {
            color: #667eea;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 700;
            transition: all 0.3s;
        }

        .forgot-password:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        /* زر تسجيل الدخول */
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            font-family: 'Cairo', sans-serif;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login i {
            margin-left: 0.5rem;
        }

        /* رسائل التنبيه */
        .alert {
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border: none;
            animation: shake 0.5s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .alert-danger {
            background: #fee;
            color: #c33;
            border-right: 4px solid #c33;
        }

        .alert-success {
            background: #efe;
            color: #3c3;
            border-right: 4px solid #3c3;
        }

        /* ==========================================
           الجانب الأيسر - المعلومات والتصميم
           ========================================== */
        .info-side {
            flex: 1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            color: white;
            overflow: hidden;
        }

        .info-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255,255,255,0.1) 0%, transparent 50%);
            animation: shimmer 8s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }

        /* جسيمات متحركة */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            animation: float-particle 15s infinite;
        }

        .particle:nth-child(1) { width: 10px; height: 10px; left: 10%; animation-delay: 0s; animation-duration: 12s; }
        .particle:nth-child(2) { width: 15px; height: 15px; left: 20%; animation-delay: 2s; animation-duration: 14s; }
        .particle:nth-child(3) { width: 8px; height: 8px; left: 30%; animation-delay: 4s; animation-duration: 10s; }
        .particle:nth-child(4) { width: 12px; height: 12px; left: 40%; animation-delay: 1s; animation-duration: 16s; }
        .particle:nth-child(5) { width: 20px; height: 20px; left: 50%; animation-delay: 3s; animation-duration: 18s; }
        .particle:nth-child(6) { width: 10px; height: 10px; left: 60%; animation-delay: 5s; animation-duration: 13s; }
        .particle:nth-child(7) { width: 15px; height: 15px; left: 70%; animation-delay: 2s; animation-duration: 15s; }
        .particle:nth-child(8) { width: 8px; height: 8px; left: 80%; animation-delay: 4s; animation-duration: 11s; }
        .particle:nth-child(9) { width: 12px; height: 12px; left: 90%; animation-delay: 1s; animation-duration: 17s; }
        .particle:nth-child(10) { width: 18px; height: 18px; left: 15%; animation-delay: 3s; animation-duration: 19s; }

        @keyframes float-particle {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% {
                transform: translateY(-100vh) rotate(720deg);
                opacity: 0;
            }
        }

        .info-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 500px;
        }

        .info-logo {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            font-size: 3.5rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .info-content h1 {
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 1rem;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .info-content p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 3rem;
            line-height: 1.8;
        }

        /* إحصائيات */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .stat-item {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 1.5rem;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s;
        }

        .stat-item:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.15);
        }

        .stat-item i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            display: block;
        }

        .stat-item .number {
            font-size: 2rem;
            font-weight: 900;
            display: block;
        }

        .stat-item .label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* تجاوب */
        @media (max-width: 992px) {
            .info-side {
                display: none;
            }
            
            .login-side {
                flex: 1;
            }
        }

        @media (max-width: 480px) {
            .login-side {
                padding: 1.5rem;
            }
            
            .login-header h1 {
                font-size: 1.8rem;
            }
        }

        /* تأثير التحميل */
        .btn-login.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .btn-login.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            right: calc(50% - 10px);
            top: calc(50% - 10px);
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

    <!-- الجانب الأيمن: نموذج تسجيل الدخول -->
    <div class="login-side">
        <div class="login-container">
            <div class="login-header">
                <h1>مرحباً بعودتك 👋</h1>
                <p>سجل دخولك للوصول إلى لوحة التحكم</p>
            </div>

            @if(session('status'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope"></i> البريد الإلكتروني
                    </label>
                    <div class="input-wrapper">
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            placeholder="example@domain.com"
                            required 
                            autofocus
                            autocomplete="email"
                        >
                        <i class="bi bi-envelope-fill input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock"></i> كلمة المرور
                    </label>
                    <div class="input-wrapper">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        >
                        <i class="bi bi-lock-fill input-icon"></i>
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">تذكرني</label>
                    </div>
                    
                    @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-password">
                            نسيت كلمة المرور؟
                        </a>
                    @endif
                </div>

                <button type="submit" class="btn-login" id="loginBtn">
                    <i class="bi bi-box-arrow-in-left"></i>
                    تسجيل الدخول
                </button>
            </form>
        </div>
    </div>

    <!-- الجانب الأيسر: المعلومات والتصميم -->
    <div class="info-side">
        <div class="particles">
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
        </div>

        <div class="info-content">
            <div class="info-logo">
                <i class="bi bi-box-seam-fill"></i>
            </div>
            
            <h1>نظام إدارة المخازن</h1>
            <p>
                نظام متكامل لإدارة المخازن والعمليات اللوجستية
                <br>
                يتبع أحدث معايير الأمان والكفاءة
            </p>

            <div class="stats-grid">
                <div class="stat-item">
                    <i class="bi bi-building"></i>
                    <span class="number">4+</span>
                    <span class="label">مستودعات</span>
                </div>
                <div class="stat-item">
                    <i class="bi bi-box-seam"></i>
                    <span class="number">1000+</span>
                    <span class="label">صنف</span>
                </div>
                <div class="stat-item">
                    <i class="bi bi-shield-check"></i>
                    <span class="number">99.9%</span>
                    <span class="label">أمان</span>
                </div>
                <div class="stat-item">
                    <i class="bi bi-arrow-left-right"></i>
                    <span class="number">500+</span>
                    <span class="label">تحويل شهري</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        }

        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');
            btn.innerHTML = '<i class="bi bi-hourglass-split"></i> جاري تسجيل الدخول...';
        });

        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
        });

        window.addEventListener('load', function() {
            document.getElementById('email').focus();
        });
    </script>
</body>
</html>