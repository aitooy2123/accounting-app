<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - Business Suite</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --biz-blue: #2563EB; /* ฟ้าหลัก */
            --biz-blue-dark: #1E40AF;
            --biz-bg: #F0F4F8;
            --text-main: #1E293B;
        }

        body {
            background-color: var(--biz-bg);
            font-family: 'Inter', sans-serif;
            color: var(--text-main);
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(37, 99, 235, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            border: none;
        }

        /* ฝั่งซ้าย: โทนสีฟ้า */
        .login-sidebar {
            background: linear-gradient(135deg, #3B82F6 0%, #1E40AF 100%);
            color: white;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        /* ตกแต่งพื้นหลังเล็กน้อย (วงกลมจางๆ) */
        .login-sidebar::before {
            content: "";
            position: absolute;
            top: -50px;
            left: -50px;
            width: 150px;
            height: 150px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }

        .login-sidebar h1 {
            font-weight: 700;
            font-size: 2.2rem;
            z-index: 1;
        }

        /* ฝั่งขวา: ฟอร์ม */
        .login-form-area {
            padding: 60px;
        }

        .form-control {
            padding: 12px 16px;
            border-radius: 10px;
            border: 1px solid #CBD5E1;
            background-color: #F8FAFC;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: var(--biz-blue);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            background-color: #fff;
        }

        .btn-blue {
            background-color: var(--biz-blue);
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .btn-blue:hover {
            background-color: var(--biz-blue-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
            color: white;
        }

        .input-group-text {
            background-color: #F8FAFC;
            border: 1px solid #CBD5E1;
            border-left: none;
            border-radius: 0 10px 10px 0;
            cursor: pointer;
            color: #64748B;
        }

        @media (max-width: 768px) {
            .login-sidebar { display: none; }
            .login-form-area { padding: 40px 24px; }
        }
    </style>
</head>
<body>

    <div class="login-container container">
        <div class="login-card">
            <div class="row g-0">
                <div class="col-md-5 login-sidebar">
                    <div class="mb-4">
                        <i class="fa-solid fa-chart-line fa-3x"></i>
                    </div>
                    <h1>Simplify Your Business.</h1>
                    <p class="mt-4 opacity-75">จัดการทุกระบบงานอย่างมืออาชีพด้วยแพลตฟอร์มที่ใช้งานง่ายที่สุด</p>

                    <div class="mt-auto pt-5 d-none d-md-block">
                        <p class="small opacity-50 mb-0">© 2026 BusinessSuite Inc.</p>
                    </div>
                </div>

                <div class="col-md-7 login-form-area">
                    <div class="mb-5">
                        <h2 class="fw-bold mb-1">เข้าสู่ระบบ</h2>
                        <p class="text-muted">ใส่ข้อมูลเพื่อเข้าใช้งานบัญชีของคุณ</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">อีเมล (EMAIL)</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}" required placeholder="example@business.com">
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between">
                                <label class="form-label small fw-bold text-muted">รหัสผ่าน (PASSWORD)</label>
                                <a href="{{ route('password.request') }}" class="small text-decoration-none fw-medium" style="color: var(--biz-blue);">ลืมรหัสผ่าน?</a>
                            </div>
                            <div class="input-group">
                                <input id="password" type="password" class="form-control border-end-0 @error('password') is-invalid @enderror"
                                       name="password" required placeholder="••••••••">
                                <span class="input-group-text" id="togglePassword">
                                    <i class="fa-regular fa-eye" id="eyeIcon"></i>
                                </span>
                            </div>
                        </div>

                        <div class="mb-4 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label small text-muted" for="remember">จดจำการใช้งานของฉัน</label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-blue">
                                ลงชื่อเข้าใช้งาน
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-5">
                        <p class="small text-muted">ยังไม่มีบัญชีใช้งาน? <a href="#" class="fw-bold text-decoration-none" style="color: var(--biz-blue);">ติดต่อผู้ดูแลระบบ</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');

        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
