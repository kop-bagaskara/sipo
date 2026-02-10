<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SIPO - Login</title>
    <link rel="shortcut icon" href="<?php echo e(asset('sipo_krisan/public/assets/images/ficon.png')); ?>">

    <link href="<?php echo e(asset('sipo_krisan/public/new/assets/css/bootstrap.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('sipo_krisan/public/new/assets/css/icons.min.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('sipo_krisan/public/new/assets/css/theme.min.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 1000px;
            width: 100%;
            animation: slideInUp 0.6s ease-out;
        }

        @keyframes  slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-section {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            padding: 60px 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .logo-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes  rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .logo-section img {
            position: relative;
            z-index: 2;
            max-width: 220px;
            height: auto;
            filter: drop-shadow(0 6px 12px rgba(0,0,0,0.15));
            transition: transform 0.3s ease;
        }

        .logo-section img:hover {
            transform: scale(1.05);
        }

        .logo-content {
            position: relative;
            z-index: 2;
            color: white;
        }

        .welcome-title {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .welcome-subtitle {
            font-size: 1.1rem;
            margin-bottom: 25px;
            opacity: 0.9;
            line-height: 1.5;
        }

        .company-info {
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .company-name {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .company-desc {
            font-size: 0.9rem;
            opacity: 0.8;
            line-height: 1.4;
        }

        .features-list {
            list-style: none;
            padding: 0;
            margin: 25px 0;
            text-align: left;
        }

        .features-list li {
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .features-list li i {
            margin-right: 10px;
            color: #ffd700;
            font-size: 1.1rem;
        }

        .version-info {
            position: absolute;
            bottom: 20px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 0.8rem;
            opacity: 0.7;
            z-index: 2;
        }

        .form-section {
            padding: 50px 40px;
            background: white;
        }

        .system-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .system-title h1 {
            color: #2c3e50;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }

        .signin-section {
            margin-bottom: 30px;
        }

        .signin-section h2 {
            color: #495057;
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .signin-section p {
            color: #6c757d;
            font-size: 0.95rem;
            margin-bottom: 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            color: #495057;
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
            font-size: 0.95rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
            color: #495057;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
            background: white;
            outline: none;
        }

        .form-control::placeholder {
            color: #adb5bd;
        }

        .btn-login {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border: none;
            border-radius: 12px;
            padding: 14px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.4);
            background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.6s;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .footer-section {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        .footer-section p {
            color: #6c757d;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .footer-section a {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .footer-section a:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.9rem;
            margin-top: 5px;
            display: flex;
            align-items: center;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 8px 12px;
        }

        .error-message i {
            margin-right: 8px;
        }

        @media (max-width: 768px) {
            .login-card {
                margin: 10px;
                border-radius: 15px;
            }

            .form-section {
                padding: 30px 20px;
            }

            .logo-section {
                padding: 40px 20px;
            }

            .system-title h1 {
                font-size: 1.5rem;
            }

            .signin-section h2 {
                font-size: 1.2rem;
            }
        }

        /* Loading animation */
        .loading {
            display: none;
        }

        .btn-login.loading .loading {
            display: inline-block;
            animation: spin 1s linear infinite;
        }

        @keyframes  spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Input focus effects */
        .form-control:hover {
            border-color: #ced4da;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .login-wrapper {
                padding: 10px;
            }

            .form-section {
                padding: 25px 15px;
            }

            .logo-section {
                padding: 30px 15px;
            }

            .logo-section img {
                max-width: 180px;
            }
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="row g-0">
                <div class="col-lg-5">
                    <div class="logo-section h-100">
                        <img src="<?php echo e(asset('sipo_krisan/public/assets/images/logo-kop-2.png')); ?>" alt="SIPP Logo">
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="form-section">
                        <div class="system-title">
                            <h1>SISTEM INFORMASI PENJADWALAN & OPERASIONAL</h1>
                        </div>

                        

                        <form method="POST" action="<?php echo e(route('login')); ?>" id="loginForm">
                            <?php echo csrf_field(); ?>

                            <div class="form-group">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user me-2"></i>&nbsp;USERNAME
                                </label>
                                <input type="text"
                                       id="username"
                                       class="form-control <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       name="username"
                                       placeholder="Masukkan Username"
                                       value="<?php echo e(old('username')); ?>"
                                       required
                                       autofocus>
                                <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <?php echo e($message); ?>

                                    </div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="form-group">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>&nbsp;PASSWORD
                                </label>
                                <input type="password"
                                       id="password"
                                       class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       name="password"
                                       required
                                       autocomplete="current-password"
                                       placeholder="Masukkan Password" />
                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <?php echo e($message); ?>

                                    </div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <button type="submit" class="btn btn-login" id="loginBtn">
                                <span class="loading">
                                    <i class="fas fa-spinner"></i>
                                </span>
                                <span class="btn-text">
                                    <i class="fas fa-sign-in-alt me-2"></i> Log in
                                </span>
                            </button>
                        </form>

                        <div class="footer-section">
                            <p><i class="fas fa-building me-2"></i> PT. KRISANTHIUM OFFSET PRINTING</p>
                            <a href="#" target="_blank">
                                <i class="fas fa-book me-2"></i> User Manual SiPO
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery  -->
    <script src="<?php echo e(asset('sipo_krisan/public/new/assets/js/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(asset('sipo_krisan/public/new/assets/js/bootstrap.bundle.min.js')); ?>"></script>
    <script src="<?php echo e(asset('sipo_krisan/public/new/assets/js/metismenu.min.js')); ?>"></script>
    <script src="<?php echo e(asset('sipo_krisan/public/new/assets/js/waves.js')); ?>"></script>
    <script src="<?php echo e(asset('sipo_krisan/public/new/assets/js/simplebar.min.js')); ?>"></script>

    <!-- App js -->
    <script src="<?php echo e(asset('sipo_krisan/public/new/assets/js/theme.js')); ?>"></script>

    <script>
        $(document).ready(function() {
            // Loading animation for login button
            $('#loginForm').on('submit', function() {
                $('#loginBtn').addClass('loading');
                $('.btn-text').text('Memproses...');
            });

            // Auto-focus username field
            $('#username').focus();

            // Smooth hover effects
            $('.form-control').on('focus', function() {
                $(this).parent().find('i').css('color', '#007bff');
            });

            $('.form-control').on('blur', function() {
                if (!$(this).val()) {
                    $(this).parent().find('i').css('color', '#adb5bd');
                }
            });
        });
    </script>
</body>

</html>
<?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/auth/login.blade.php ENDPATH**/ ?>